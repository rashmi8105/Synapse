<?php
use Codeception\Util\Stub;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;

class StudentUploadServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\UploadBundle\Service\Impl\StudentUploadService
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

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->studentUploadService = $this->container
            ->get('student_upload_service');
        $this->personService = $this->container
            ->get('person_service');
        $this->organizationService = $this->container
            ->get('org_service');
        $this->entityService = $this->container
            ->get('entity_service');

        $person = new Person;
        $contact = new ContactInfo;
        $organization = $this->organizationService->find(1);
        $studentEntity = $this->entityService->findOneByName('Student');

        $person->setExternalId('TestUpdateUser');
        $person->setFirstname('Test');
        $person->setLastname('User');
        $person->setOrganization($organization);
        $person->addEntity($studentEntity);
        $contact->setPrimaryEmail('testupdateuser@mnv-tech.com');

        $this->testPerson = $this->personService->createPersonRaw($person, $contact);
    }

    public function testLoadFile()
    {
        $filePath = 'tests/_data/student_upload_file.csv';
        $orgId = 1;
        $fileData = $this->studentUploadService->load($filePath, $orgId);
        \PHPUnit_Framework_Assert::assertInternalType('array', $fileData);
        $this->assertEquals(40, $fileData['totalRows']);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFilePath()
    {
        $filePath = 'tests/_data/missing.csv';
        $orgId = '1';
        $fileData = $this->studentUploadService->load($filePath, $orgId);
    }

    public function testProcessFile()
    {
        $this->loadTestFile();
        $uploadId = 1;
        $processData = $this->studentUploadService->process($uploadId);
        \PHPUnit_Framework_Assert::assertInternalType('array', $processData);
        $this->assertEquals(3, count($processData['jobs']));
    }

    public function testGenerateErrorCSV()
    {
        $this->loadTestFile();
        $errors = [
            1 => [
                [
                    'name' => 'Test Field',
                    'errors' => [
                        'Test Error'
                    ]
                ]
            ]
        ];
        $this->studentUploadService->generateErrorCSV($errors);
    }

    public function testGenerateDumpCSV()
    {
        $orgId = 1;
        $this->studentUploadService->generateDumpCSV($orgId);
    }

    private function loadTestFile()
    {
        $filePath = 'tests/_data/student_upload_file.csv';
        $orgId = 1;
        $fileData = $this->studentUploadService->load($filePath, $orgId);
    }


    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}