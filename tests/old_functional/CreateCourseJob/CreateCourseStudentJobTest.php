<?php
use Codeception\Util\Stub;

use Synapse\UploadBundle\Job\AddCourseStudent;

class CreateCourseStudentJobTest extends \Codeception\TestCase\Test
{

    /**
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\UploadBundle\Job\AddCourseStudent
     */
    private $courseStudentUploadService;

    /**
     *
     * @var \Synapse\CoreBundle\Service\PersonService
     */
    private $personService;

    /**
     *
     * @var \Synapse\CoreBundle\Service\OrganizationService
     */
    private $organizationService;

    private $testPerson;

    private $resque;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->courseStudentUploadService = $this->container->get('course_student_upload_service');
        $this->personService = $this->container->get('person_service');
        $this->organizationService = $this->container->get('org_service');
        $this->resque = $this->container->get('bcc_resque.resque');
    }

    public function testCreateJob()
    {
        $creates = [
            [
                'StudentID' => '1',
                'UniqueCourseSectionID' => '402712252'
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;
        $courseId = 1;

        $job = new AddCourseStudent();
        $job->args = array(
            'creates' => $creates,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadId,
            'orgId' => $orgId,
            'courseId' => $courseId
        );

        $jobs = $this->resque->enqueue($job, true);

        \PHPUnit_Framework_Assert::assertInstanceOf('Resque_Job_Status', $jobs);
    }

    public function testRunJob()
    {
        $jobNumber = uniqid();
        $creates = [
            [
                strtolower('StudentID') => '1',
                strtolower('UniqueCourseSectionID') => '402712252'
            ]
        ];
        $uploadId = 1;
        $orgId = 1;
        $courseId = 1;

        $errors = $this->runTestJob($jobNumber, $creates, $uploadId, $orgId, $courseId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(1, count($errors));
    }

    private function runTestJob($jobNumber, $creates, $uploadId, $orgId, $courseId)
    {
        $job = new AddCourseStudent();
        $job->setKernelOptions([
            'kernel.root_dir' => 'app'
        ]);
        $errors = $job->run([
            'creates' => $creates,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadId,
            'orgId' => $orgId,
            'courseId' => $courseId
        ]);

        return $errors;
    }

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {}
}