<?php
use Codeception\Util\Stub;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\UploadBundle\Job\UpdateStudent;
use Synapse\CoreBundle\Entity\MetadataMaster;

class UpdateStudentJobTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\UploadBundle\Job\UpdateStudent
     */
    private $studentUploadService;

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
        $this->studentUploadService = $this->container->get('student_upload_service');
        $this->personService = $this->container->get('person_service');
        $this->organizationService = $this->container->get('org_service');
        $this->entityService = $this->container->get('entity_service');
        $this->resque = $this->container->get('bcc_resque.resque');
        $this->metadataRepository = $this->container->get('repository_resolver')->getRepository('SynapseCoreBundle:MetadataMaster');

    }

    public function testUpdateJob()
    {
        $updates = [
            [
                'CreateStudentJobTest',
                [
                    'ExternalId' => 'CreateStudentJobTest',
                    'Firstname' => 'Test',
                    'Lastname' => 'User',
                    'PrimaryEmail' => 'studentjobtest@mnv-tech.com'

                ]
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;

        $job = new UpdateStudent();
        $job->args = array(
            'updates'    => $updates,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadId,
            'orgId' => $orgId
        );

        $jobs = $this->resque->enqueue($job, true);

        \PHPUnit_Framework_Assert::assertInstanceOf('Resque_Job_Status', $jobs);
    }

    public function testRunJob()
    {
        $this->markTestSkipped("Errored");
        $this->createTestUser();
        $jobNumber = uniqid();
        $updates = [
            [
                'CreateStudentJobTest',
                [
                    'ExternalId' => 'CreateStudentJobTest',
                    'Firstname' => 'Test',
                    'Lastname' => 'User',
                    'PrimaryEmail' => 'studentjobtest'. $jobNumber .'@mnv-tech.com'
                ]
            ]
        ];
        $uploadId = 1;
        $orgId = 1;

        $errors = $this->runTestJob($jobNumber, $updates, $uploadId, $orgId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(0, count($errors));
    }

    public function testRunJobMissingData()
    {
        $updates = [
            [
                'CreateStudentJobTest',
                [
                    'ExternalId' => 'CreateStudentJobTest'
                ]
            ]
        ];
        $jobNumber = uniqid();
        $uploadId = 1;
        $orgId = 1;

        $errors = $this->runTestJob($jobNumber, $updates, $uploadId, $orgId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(1, count($errors));
    }

    public function testRunJobInvalidPrimaryEmail()
    {
        $jobNumber = uniqid();
        $updates = [
            [
                'CreateStudentJobTest',
                [
                    'ExternalId' => 'CreateStudentJobTest',
                    'Firstname' => 'Test',
                    'Lastname' => 'User',
                    'PrimaryEmail' => 'studentjobtest'. $jobNumber .'mnv-tech.com'
                ]
            ]
        ];
        $uploadId = 1;
        $orgId = 1;

        $errors = $this->runTestJob($jobNumber, $updates, $uploadId, $orgId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(1, count($errors));
    }

    public function testRunJobMissingExternalId()
    {
        $jobNumber = uniqid();
        $updates = [
            [
                'CreateStudentJobTest',
                [
                    'ExternalId' => ''
                ]
            ]
        ];
        $uploadId = 1;
        $orgId = 1;

        $errors = $this->runTestJob($jobNumber, $updates, $uploadId, $orgId);

        \PHPUnit_Framework_Assert::assertInternalType('array', $errors);
        $this->assertEquals(1, count($errors));
    }

    private function runTestJob($jobNumber, $updates, $uploadId, $orgId)
    {
        $job = new UpdateStudent;
        $job->setKernelOptions([
            'kernel.root_dir' => 'app'
        ]);
        $errors = $job->run([
            'updates'    => $updates,
            'jobNumber'  => $jobNumber,
            'uploadId'   => $uploadId,
            'orgId'      => $orgId,
			'headerRow' => ''
        ]);

        return $errors;
    }

    private function createTestUser()
    {
        $person = new Person;
        $contact = new ContactInfo;
        $organization = $this->organizationService->find(1);
        $studentEntity = $this->entityService->findOneByName('Student');

        $person->setExternalId('CreateStudentJobTest');
        $person->setFirstname('Test');
        $person->setLastname('User');
        $person->setOrganization($organization);
        $person->addEntity($studentEntity);
        $contact->setPrimaryEmail('testupdateuser@mnv-tech.com');

        $this->testPerson = $this->personService->createPersonRaw($person, $contact);
        $this->personService->flush();
        $this->personService->clear();
    }

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}
