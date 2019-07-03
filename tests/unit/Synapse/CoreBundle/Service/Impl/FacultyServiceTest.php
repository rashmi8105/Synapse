<?php
namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\PersonBundle\DTO\PersonDTO;

class FacultyServiceTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    private $personDataForCreate = [
        "1" => [
            'external_id' => 'X10001',
            'mapworks_internal_id' => 1,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => "http://googole.com",
            'is_student' => 0,
            'is_faculty' => 1,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1
        ],
        "2" => [
            'external_id' => 'X10001',
            'mapworks_internal_id' => 1,
            'auth_username' => 'auth',
            'firstname' => 'Jasmine',
            'lastname' => 'Russell',
            'primary_email' => 'Jasmine.Russell@ns2016.mapworks.com',
            'photo_url' => "http://googole.com",
            'is_student' => 0,
            'is_faculty' => 0,
            'primary_connection_person_id' => 4567,
            'risk_group_id' => 1
        ],
    ];

    public function testDetermineFacultyUpdateType()
    {
        $this->specify("Test determine faculty update type", function ($personDTO, $personId, $organizationId, $actionType, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $personObject = $this->getPersonInstance($personId);
            $organization = $this->getOrganizationInstance($organizationId);
            $loggedInUser = $this->getPersonInstance(2);

            // Mock Repositories
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy', 'persist']);

            // Mock Services
            $mockPersonService = $this->getMock('PersonService', ['generateAuthKey']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        PersonService::SERVICE_KEY,
                        $mockPersonService
                    ]
                ]);

            if ($actionType == 'remove_faculty') {
                $mockPerson = $this->getMock('Person', ['getId']);
                $mockPerson->method('getId')->willReturn($personId);

                $mockOrganization = $this->getMock('Organization', ['getId']);
                $mockOrganization->method('getId')->willReturn($organizationId);

                $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', ['getId', 'getOrganization', 'getPerson', 'setDeletedAt', 'setDeletedBy']);
                $mockOrgPersonFaculty->method('getId')->willReturn(1);
                $mockOrgPersonFaculty->method('getOrganization')->willReturn($mockOrganization);
                $mockOrgPersonFaculty->method('getPerson')->willReturn($mockPerson);
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            } else {
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn(null);

                $mockPersonService->method('generateAuthKey')->willReturn('test');
            }

            $facultyService = new FacultyService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $actualResult = $facultyService->determineFacultyUpdateType($personObject, $personDTO, $organization, $loggedInUser);

            $this->assertEquals($actualResult, $expectedResult);

        }, [
                'examples' => [
                    // Test0: Create faculty, returns true
                    [
                        $this->getPersonDTO($this->personDataForCreate[1]),
                        95,
                        203,
                        'create_faculty',
                        true
                    ],
                    // Test2: Remove faculty, returns true
                    [
                        $this->getPersonDTO($this->personDataForCreate[2]),
                        95,
                        203,
                        'remove_faculty',
                        true
                    ],
                ]
            ]
        );
    }

    private function getPersonInstance($externalId = '123')
    {
        $person = new Person();
        $person->setExternalId($externalId);
        return $person;
    }


    private function getOrganizationInstance($campusId = 1)
    {
        $organization = new Organization();
        $organization->setExternalId('ABC123');
        $organization->setCampusId($campusId);
        return $organization;
    }

    private function getPersonDTO($personData)
    {
        $personDTO = new PersonDTO();

        $personDTO->setAuthUsername($personData['auth_username']);
        $personDTO->setPrimaryEmail($personData['primary_email']);
        $personDTO->setExternalId($personData['external_id']);
        $personDTO->setMapworksInternalId($personData['mapworks_internal_id']);
        $personDTO->setFirstname($personData['firstname']);
        $personDTO->setLastname($personData['lastname']);
        if (isset($personData['photo_url'])) {
            $personDTO->setPhotoLink($personData['photo_url']);
        }
        if (isset($personData['primary_connection_person_id'])) {
            $personDTO->setPrimaryCampusConnectionId($personData['primary_connection_person_id']);
        }
        $personDTO->setIsStudent($personData['is_student']);
        $personDTO->setIsFaculty($personData['is_faculty']);
        $personDTO->setTitle("Mr.");
        if (isset($personData['risk_group_id'])) {
            $personDTO->setRiskGroupId($personData['risk_group_id']);
        }
        $personDTO->setFieldsToClear([]);

        return $personDTO;
    }

    public function testIsPersonAFaculty()
    {
        $this->specify("Test is person a faculty", function ($facultyId, $errorType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock Repository
            $mockOrgPersonFacultyRepository = $this->getMock('orgPersonFacultyRepository', ['findOneBy']);
            if ($errorType == 'valid') {
                $mockPerson = $this->getPersonInstance('1');
            } else {
                $mockPerson = null;
            }
            $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockPerson);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                ]);

            $facultyService = new FacultyService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $facultyService->isPersonAFaculty($facultyId);

            $this->assertEquals($results, $expectedResult);

        }, [
            'examples' => [
                // Test0: Case when person is not a faculty
                [
                    1,
                    "invalid",
                    false
                ],
                // Test1: Case when person is a faculty
                [
                    1,
                    "valid",
                    true
                ]
            ]
        ]);
    }

    public function testIsFacultyActive()
    {
        $this->specify("Test is person a faculty", function ($facultyId, $status, $isValidFaculty, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));


            $mockFacultyPersonObject = $this->getMock('OrgPersonFaculty', ['getStatus']);
            $mockFacultyPersonObject->method('getStatus')->willReturn($status);

            // Mock Repository
            $mockOrgPersonFacultyRepository = $this->getMock('orgPersonFacultyRepository', ['findOneBy']);
            if ($isValidFaculty) {
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockFacultyPersonObject);
            } else {
                $mockOrgPersonFacultyRepository->method('findOneBy')->willThrowException(new SynapseValidationException('1 is not a valid faculty'));
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                ]);

            $facultyService = new FacultyService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $results = $facultyService->isFacultyActive($facultyId);
                $this->assertEquals($results, $expectedResult);
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->assertEquals($errorMessage, $expectedResult);
            }
        }, [
            'examples' => [
                // Test0: Case when person is not a faculty
                [
                    1,
                    0,
                    true,
                    false
                ],
                // Test1: Case when person is a faculty
                [
                    1,
                    1,
                    true,
                    true
                ],
                // Test2: When the person id is  not a valid faculty
                [
                    1,
                    1,
                    false,
                    "1 is not a valid faculty"
                ]
            ]
        ]);
    }
}