<?php
use Codeception\Util\Stub;

use Synapse\UploadBundle\Job\AddCourseFaculty;

class CreateCourseFacultyJobTest extends \Codeception\TestCase\Test
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
    private $courseFacultyUploadService;

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
        $this->courseFacultyUploadService = $this->container->get('course_faculty_upload_service');
        $this->personService = $this->container->get('person_service');
        $this->organizationService = $this->container->get('org_service');
        $this->resque = $this->container->get('bcc_resque.resque');
    }

    public function testCreateJob()
    {
        $creates = [
            [
                'UniqueCourseSectionID' => '402712252',
                'FacultyID' => '1',
                'PermissionSet' => 1
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;
        $courseId = 1;

        $job = new AddCourseFaculty();
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
                strtolower('UniqueCourseSectionID') => '402712252',
                strtolower('FacultyID') => '1',
                'PermissionSet' => 1
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
        $job = new AddCourseFaculty();
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