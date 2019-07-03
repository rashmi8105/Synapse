<?php
use Codeception\Util\Stub;

use Synapse\UploadBundle\Job\CreateCourse;

class CreateCourseJobTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\UploadBundle\Job\CreateCourse
     */
    private $courseUploadService;

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
        $this->courseUploadService = $this->container->get('course_upload_service');
        $this->personService = $this->container->get('person_service');
        $this->organizationService = $this->container->get('org_service');
        $this->resque = $this->container->get('bcc_resque.resque');
    }

    public function testCreateJob()
    {
        $creates = [
            [
                'YearID' => '201415',
                'TermID' => '1',
                'UniqueCourseSectionID' => '402712252',
                'SubjectCode' => 'ANAT',
                'Coursenumber' => '101',
                'SectionNumber' => '1',
                'Coursename' => 'ANAT',
                'CreditHours' => '3',
                'CollegeCode' => 'CAS',
                'DeptCode' => 'MAS',
                'Days/Times' => 'MTWRF 9:00-9:50',
                'Location' => 'HALL'
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;

        $job = new CreateCourse();
        $job->args = array(
            'creates' => $creates,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadId,
            'orgId' => $orgId
        );

        $jobs = $this->resque->enqueue($job, true);

        \PHPUnit_Framework_Assert::assertInstanceOf('Resque_Job_Status', $jobs);
    }

    public function testRunJob()
    {
        $jobNumber = uniqid();
        $creates = [
            [
                'YearID' => '201415',
                'TermID' => '1',
                'UniqueCourseSectionID' => '402712252',
                'SubjectCode' => 'ANAT',
                'Coursenumber' => '101',
                'SectionNumber' => '1',
                'Coursename' => 'ANAT',
                'CreditHours' => '3',
                'CollegeCode' => 'CAS',
                'DeptCode' => 'MAS',
                'Days/Times' => 'MTWRF 9:00-9:50',
                'Location' => 'HALL'
            ]
        ];
        $uploadId = 1;
        $orgId = 1;

        $errors = $this->runTestJob($jobNumber, $creates, $uploadId, $orgId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(1, count($errors));
    }

    private function runTestJob($jobNumber, $creates, $uploadId, $orgId)
    {
        $job = new CreateCourse();
        $job->setKernelOptions([
            'kernel.root_dir' => 'app'
        ]);
        $errors = $job->run([
            'creates' => $creates,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadId,
            'orgId' => $orgId
        ]);

        return $errors;
    }

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {}
}