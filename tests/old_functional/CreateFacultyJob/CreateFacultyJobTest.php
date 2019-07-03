<?php
use Codeception\Util\Stub;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\UploadBundle\Job\CreateFaculty;
use Synapse\CoreBundle\Entity\MetadataMaster;

class CreateFacultyJobTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\UploadBundle\Job\CreateFaculty
     */
    private $facultyUploadService;

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
        $this->facultyUploadService = $this->container->get('faculty_upload_service');
        $this->personService = $this->container->get('person_service');
        $this->organizationService = $this->container->get('org_service');
        $this->entityService = $this->container->get('entity_service');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->metadataRepository = $this->container->get('repository_resolver')->getRepository('SynapseCoreBundle:MetadataMaster');
    }

    public function testCreateJob()
    {
        $creates = [
            [
                'ExternalId' => 'CreateFacultyJobTest',
                'Firstname' => 'Test',
                'Lastname' => 'User',
                'PrimaryEmail' => 'facultyjobtest@mnv-tech.com'
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;

        $job = new CreateFaculty();
        $job->args = array(
            'creates'    => $creates,
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
                'ExternalId' => 'CreateFacultyJobTest',
                'Firstname' => 'Test',
                'Lastname' => 'User',
                'PrimaryEmail' => 'facultyjobtest'. $jobNumber .'@mnv-tech.com'
            ]
        ];
        $uploadId = 1;
        $orgId = 1;

        $errors = $this->runTestJob($jobNumber, $creates, $uploadId, $orgId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(0, count($errors));
    }

    public function testRunJobMissingPrimaryEmail()
    {
        $creates = [
            [
                'ExternalId' => 'CreateFacultyJobTest',
                'Firstname' => 'Test',
                'Lastname' => 'User',
                'PrimaryEmail' => ''
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;

        $errors = $this->runTestJob($jobNumber, $creates, $uploadId, $orgId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(1, count($errors));
    }

    public function testRunJobInvalidPrimaryEmail()
    {
        $jobNumber = uniqid();
        $creates = [
            [
                'ExternalId' => 'CreateFacultyJobTest',
                'Firstname' => 'Test',
                'Lastname' => 'User',
                'PrimaryEmail' => ''
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
        $job = new CreateFaculty;
        $job->setKernelOptions([
            'kernel.root_dir' => 'app'
        ]);
        $errors = $job->run([
            'creates'    => $creates,
            'jobNumber'  => $jobNumber,
            'uploadId'   => $uploadId,
            'orgId'      => $orgId
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