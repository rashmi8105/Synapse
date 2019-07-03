<?php
use Codeception\Util\Stub;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;

class FacultyUploadServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\UploadBundle\Service\Impl\FacultyUploadService
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

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->facultyUploadService = $this->container
            ->get('faculty_upload_service');
        $this->personService = $this->container
            ->get('person_service');
        $this->organizationService = $this->container
            ->get('org_service');
        $this->entityService = $this->container
            ->get('entity_service');

        $person = new Person;
        $contact = new ContactInfo;
        $organization = $this->organizationService->find(1);
        $facultyEntity = $this->entityService->findOneByName('Faculty');

        $person->setExternalId('TestUpdateUser');
        $person->setFirstname('Test');
        $person->setLastname('User');
        $person->setOrganization($organization);
        $person->addEntity($facultyEntity);
        $contact->setPrimaryEmail('testupdateuser@mnv-tech.com');

        $this->testPerson = $this->personService->createPersonRaw($person, $contact);
    }

    public function testLoadFile()
    {
        $filePath = 'tests/_data/faculty_upload_file.csv';
        $orgId = 1;
        $fileData = $this->facultyUploadService->load($filePath, $orgId);
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
        $fileData = $this->facultyUploadService->load($filePath, $orgId);
    }

    public function testProcessFile()
    {
        $this->loadTestFile();
        $uploadId = 1;
        $processData = $this->facultyUploadService->process($uploadId);
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
        $this->facultyUploadService->generateErrorCSV($errors);
    }

    public function testGenerateDumpCSV()
    {
        $this->markTestSkipped("Errored");
        $orgId = 1;
        $this->facultyUploadService->generateDumpCSV($orgId);
    }

    private function loadTestFile()
    {
        $filePath = 'tests/_data/faculty_upload_file.csv';
        $orgId = 1;
        $fileData = $this->facultyUploadService->load($filePath, $orgId);
    }


    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}
