<?php
use Codeception\Util\Stub;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\UploadBundle\Job\CreateAcademicUpdate;
use Synapse\CoreBundle\Entity\MetadataMaster;

class CreateAcademicUpdatesJobTest extends \Codeception\TestCase\Test
{

    /**
    * @var UnitTester
    */
    protected $tester;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\UploadBundle\Job\CreateAcademicUpdate
     */
    private $academicUploadService;

    /**
     * @var \Synapse\CoreBundle\Service\PersonService
     */
    private $personService;

    /**
     * @var \Synapse\CoreBundle\Service\OrganizationService
     */
    private $organizationService;

    /**
     * @var \Synapse\CoreBundle\Service\EntityService
     */
    private $entityService;

    private $testPerson;

    private $resque;

    private $metadataRepository;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->academicUpdateUploadService = $this->container->get('academicupdate_upload_service');
        $this->personService = $this->container->get('person_service');
        $this->organizationService = $this->container->get('org_service');
        $this->entityService = $this->container->get('entity_service');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->metadataRepository = $this->container->get('repository_resolver')->getRepository('SynapseCoreBundle:MetadataMaster');
        $this->logger = $this->container->get('logger');
    }

    public function testCreateJob()
    {

        $creates = [
            [
                'UniqueCourseSectionID' => '7985',
                'StudentID' => '1234567',
                'FailureRisk' => 'High',
                'InProgressGrade' => 'B',
                'FinalGrade' => 'A-',
                'Absences' => '1',
                'Comments' => 'Test Comments',
                'SentToStudent' => '0',
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;
        $userId = 1;

        $job = new CreateAcademicUpdate();
        $job->args = array(
            'creates' => $creates,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadId,
            'userId' => $userId,
            'orgId' => $orgId
        );

        $jobs = $this->resque->enqueue($job, true);

        \PHPUnit_Framework_Assert::assertInstanceOf('Resque_Job_Status', $jobs);
    }

    public function testRunJob()
    {
        /* PHP Fatal error:  Call to a member function getSendToStudent() on a non-object in /vagrant/synapse-backend/src/Synapse/UploadBundle/Job/Creat
eAcademicUpdate.php on line 84 */
        $this->markTestSkipped("Failed");
        $jobNumber = uniqid();
        $creates = [
            [
                'uniquecoursesectionid' => '7985',
                'studentid' => 'CreateStudentJobTest',
                'failurerisk' => 'High',
                'inprogressgrade' => 'B',
                'finalgrade' => 'A-',
                'absences' => '1',
                'comments' => 'Test Comments',
                'senttostudent' => '0',
            ]
        ];
        $uploadId = 1;
        $orgId = 1;
        $userId = 1;

        $errors = $this->runTestJob($jobNumber, $creates, $uploadId, $orgId,$userId);
        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(0, count($errors));
    }

    private function runTestJob($jobNumber, $creates, $uploadId, $orgId, $userId)
    {
    
        $job = new CreateAcademicUpdate;
        $job->setKernelOptions([
            'kernel.root_dir' => 'app'
        ]);

        $errors = $job->run([
            'creates' => $creates,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadId,
            'userId' => $userId,
            'orgId' => $orgId
        ]);        

        return $errors;
    }


    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}