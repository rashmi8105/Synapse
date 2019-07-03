<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Entity\ActivityCategory;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\TeamMembers;
use Synapse\CoreBundle\Entity\Teams;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\ContactsRepository;
use Synapse\CoreBundle\Repository\ContactsTeamsRepository;
use Synapse\CoreBundle\Repository\ContactTypesLangRepository;
use Synapse\CoreBundle\Repository\ContactTypesRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ContactsDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Entity\TeamMembersDto;
use Synapse\RestBundle\Entity\TeamsDto;
use Synapse\RestBundle\Exception\ValidationException;


class TeamsServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $teamsArray = [
        [
            'team_id' => 1,
            'team_name' => 'Test',
            'team_description' => 'Test Team',
            'modified_at' => '2018-01-03 10:00:00',
            'team_no_leaders' => 1,
            'team_no_members' => 0,
            'role' => 'Faculty'
        ]
    ];

    private $teamDetail = [
        'team_id' => 1,
        'team_name' => 'Test',
        'modified_at' => '2018-01-03 10:00:00'
    ];

    private $teamMembersArray = [
        [
        'person' => 1,
        'first_name' => 'David',
        'last_name' => 'Warner',
        'is_leader' => 0,
        'primaryEmail' => 'David.Warner@mailinator.com'
        ]
    ];

    public function testCreateNewTeam()
    {
        $this->specify("Test create new team", function ($teamsDtoArray, $expectedResult, $errors) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock LoggerHelperService
            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);

            // Mock OrganizationService
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);
            $mockOrganization = new Organization();
            if (($teamsDtoArray['organization_id'] == -1) || ($teamsDtoArray['organization_id'] == null)) {
                $mockOrganizationRepository->method('find')->willThrowException(new SynapseValidationException($expectedResult));
            } else {
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            }

            // Mock Validator Service
            $entityValidationService = $this->getMock('entityValidationService', ['validateDoctrineEntity']);
            if ($errors == 'team') {
                $entityValidationService->method('validateDoctrineEntity')->willThrowException(new DataProcessingExceptionHandler($expectedResult));
            }

            // Mock TeamsRepository
            $mockTeamsRepository = $this->getMock('TeamsRepository', ['createNewTeam', 'flush']);
            $mockTeams = $this->getMock('Synapse\CoreBundle\Entity\Teams', ['getId', 'setOrganization']);
            $mockTeams->method('setOrganization')->willReturn($mockOrganization);
            $mockTeamsRepository->method('createNewTeam')->willReturn($mockTeams);

            // Mock PersonService
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockPerson = $this->getMock('Synapse\CoreBundle\Entity\Person', ['getId']);
            $mockPerson->method('getId')->willReturn(3);

            if ($errors = 'person') {
                $mockPersonRepository->method('find')->willThrowException(new SynapseValidationException($expectedResult));
            } else {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            }

            // Mock TeamMembersRepository
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['createTeamMembers']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [EntityValidationService::SERVICE_KEY, $entityValidationService]
                ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamsRepository::REPOSITORY_KEY, $mockTeamsRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [TeamMembersRepository::REPOSITORY_KEY, $mockTeamMembersRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $teamsDto = $this->createTeamsDto($teamsDtoArray);
                $result = $teamsService->createNewTeam($teamsDto);
                $this->assertEquals($expectedResult, $result);
            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Creating new team
                [
                    [
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['id' => 1, 'is_leader' => true]]
                    ],
                    $this->getTeamsResponse([
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                    ]),
                    ''
                ],
                // Test02 - If team name will be duplicate will throw SynapseValidationException
                [
                    [
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['id' => 1, 'is_leader' => true]]
                    ],
                    'Team was invalid or a duplicate',
                    'team',
                ],
                // Test03 - Invalid organization id will throw SynapseValidationException
                [
                    [
                        'organization_id' => -1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['id' => 1, 'is_leader' => true]]
                    ],
                    'Organization not found!',
                    ''
                ],
                // Test04 - Organization id as null will throw SynapseValidationException
                [
                    [
                        'organization_id' => null,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['id' => 1, 'is_leader' => true]]
                    ],
                    'Organization not found!',
                    ''
                ],
                // Test05 - bad Person id as null will throw SynapseValidationException
                [
                    [
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['id' => 1, 'is_leader' => true]]
                    ],
                    'Faculty not found!',
                    'person'
                ],
            ]
        ]);
    }

    private function createTeamsDto($teamsDtoArray){

        $teamsDto = new TeamsDto();
        $teamsDto->setOrganization($teamsDtoArray['organization_id']);
        $teamsDto->setTeamName($teamsDtoArray['team_name']);
        $teamsDto->setTeamDescription($teamsDtoArray['team_description']);
        $teamsDto->setStaff($teamsDtoArray['team_staff']);
        return $teamsDto;
    }


    private function getTeamsResponse($teamsDtoArray, $isUpdate = false)
    {
        $teamsArray = [];
        $teams = new Teams();
        $organization = new Organization();
        $teams->setOrganization($organization);
        $teams->setTeamName($teamsDtoArray['team_name']);
        $teams->setTeamDescription($teamsDtoArray['team_description']);
        if(!$isUpdate) {
            $teamsArray['team'] = $teams;
            $teamsArray['staff'] = [0 => ''];
        }else{
            $teamsArray['staff'] = [
                0 => 1
            ];
            $teamsArray[0] = [
                'team' => $teams
            ];
        }
        return $teamsArray;
    }

    private function arrayOfErrorObjects($errorArray)
    {
        $returnArray = [];
        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject = $this->getMock('ErrorObject', ['getPropertyPath', 'getMessage']);
            $mockErrorObject->method('getPropertyPath')->willReturn($errorKey);
            $mockErrorObject->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject;
        }
        return $returnArray;
    }

    public function testGetTeams()
    {
        $this->specify("Test get teams", function ($organizationId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock TeamsRepository
            $mockTeamsRepository = $this->getMock('TeamsRepository', ['getTeams']);
            if ($organizationId == -1 || $organizationId == null) {
                $mockTeamsRepository->method('getTeams')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockTeamsRepository->method('getTeams')->willReturn($this->teamsArray);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamsRepository::REPOSITORY_KEY,$mockTeamsRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->getTeams($organizationId);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing valid organization id will return teams array
                [
                    1,
                    $this->teamsArray
                ],
                // Test02 - Passing invalid organization id will throw an exception
                [
                    -1,
                   'Team Not Found.'
                ],
                // Test03 - Passing organization id as null will throw an exception
                [
                    null,
                    'Team Not Found.'
                ]
            ]
        ]);
    }

    public function testGetTeamLeadersTeams()
    {
        $this->specify("Test get team leaders teams", function ($organizationId, $facultyId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock TeamsRepository
            $mockTeamsRepository = $this->getMock('TeamsRepository', ['getfacultyTeams']);
            if (($organizationId == -1 || $organizationId == null) || ($facultyId == -1 || $facultyId == null)) {
                $mockTeamsRepository->method('getfacultyTeams')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockTeamsRepository->method('getfacultyTeams')->willReturn($this->teamsArray);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamsRepository::REPOSITORY_KEY,$mockTeamsRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->getTeamLeadersTeams($organizationId, $facultyId);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing valid organization id and valid faculty id will return teams array
                [
                    1,
                    1,
                    $this->teamsArray
                ],
               // Test02 - Passing invalid organization id and valid faculty id will throw an exception
                [
                    -1,
                     1,
                    'Team Not Found.'
                ],
                // Test03 - Passing valid organization id and invalid faculty id will throw an exception
                [
                     1,
                     -1,
                    'Team Not Found.'
                ],
                // Test04 - Passing both organization id and faculty id as invalid will throw an exception
                [
                    -1,
                    -1,
                    'Team Not Found.'
                ],
                // Test05 - Passing both organization id and faculty id as null will throw an exception
                [
                    null,
                    null,
                    'Team Not Found.'
                ]
            ]
        ]);
    }

    public function testDeleteTeam()
    {
        $this->specify("Test delete team", function ($teamId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock TeamMembersRepository
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['findBy']);
            $mockTeamMembers = $this->getMock('TeamMembers', ['getId']);
            $mockTeamMembersRepository->method('findBy')->willReturn($mockTeamMembers);

            // Mock TeamsRepository
            $mockTeamsRepository = $this->getMock('TeamsRepository', ['find', 'deleteTeam', 'flush']);
            $teamsArray = [
                'team_name' => 'Test',
                'team_description' => 'Team description',
            ];
            if ($teamId == -1 || $teamId == null) {
                $mockTeamsRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            }
            $mockTeamsRepository->method('find')->willReturn($this->getTeamsResponse($teamsArray));


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamMembersRepository::REPOSITORY_KEY,$mockTeamMembersRepository],
                    [TeamsRepository::REPOSITORY_KEY,$mockTeamsRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->deleteTeam($teamId);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing valid team id will delete the team and return teams instance
                [
                    1,
                    $this->getTeamsResponse(['team_name' => 'Test', 'team_description' => 'Team description'])
                ],
                // Test02 - Passing invalid team id will throw an exception
                [
                    -1,
                    'Team Not Found.'
                ],
                // Test03 - Passing team id as null will throw an exception
                [
                    null,
                    'Team Not Found.'
                ],
            ]
        ]);
    }

    public function testGetTeamMembers()
    {
        $this->specify("Test get team members", function ($teamId, $organizationId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock TeamsRepository
            $mockTeamsRepository = $this->getMock('TeamsRepository', ['findOneBy', 'getTeamMembers','getTeamDetails']);
            if (($organizationId == -1 || $organizationId == null) || ($teamId == -1 || $teamId == null)) {
                $mockTeamsRepository->method('findOneBy')->willThrowException(new SynapseValidationException($expectedResult));
            } else {
                $mockTeam = $this->getMock('Teams', ['getId']);
                $mockTeam->method('getId')->willReturn($teamId);
                $mockTeamsRepository->method('findOneBy')->willReturn($mockTeam);
            }
            $mockTeamsRepository->method('getTeamMembers')->willReturn($this->teamMembersArray);
            $mockTeamsRepository->method('getTeamDetails')->willReturn($this->teamDetail);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamsRepository::REPOSITORY_KEY,$mockTeamsRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->getTeamMembers($teamId, $organizationId);
                $this->assertEquals($expectedResult, $result);
            } catch (SynapseValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing valid team id and valid organization id will return team members array
                [
                    1,
                    1,
                    [
                        'team_id' => 1,
                        'team_name' => 'Test',
                        'modified_at' => '2018-01-03 10:00:00',
                        'staff' =>
                            [
                                [
                                    'person' => 1,
                                    'first_name' => 'David',
                                    'last_name' => 'Warner',
                                    'is_leader' => 0,
                                    'primaryEmail' => 'David.Warner@mailinator.com'
                                ]
                            ]
                    ],
                ],
                // Test02 - Passing invalid team id and valid organization id will throw an exception
                [
                    -1,
                    1,
                    'Team Not Found.'
                ],
                // Test03 - Passing valid team id and invalid organization id will throw an exception
                [
                    1,
                    -1,
                    'Team Not Found.'
                ],
                // Test04 - Passing both team id and organization id as invalid will throw an exception
                [
                    -1,
                    -1,
                    'Team Not Found.'
                ],
                // Test05 - Passing both team id and organization id as null will throw an exception
                [
                    null,
                    null,
                    'Team Not Found.'
                ]
            ]
        ]);
    }

    private function getTeamMembersResponse($teamsMembersDtoArray)
    {
        $teamsArray = [];
        $teamMembers = new TeamMembers();
        $organization = new Organization();
        $teamMembers->setOrganization($organization);
        $person = new Person();
        $person->setFirstname($teamsMembersDtoArray[0]['first_name']);
        $person->setLastname($teamsMembersDtoArray[0]['last_name']);
        $teamMembers->setPerson($person);
        $teamMembers->setIsTeamLeader(1);
        $teamsArray['team'] = $teamMembers;
        $teamsArray['staff'] = [0 => ''];
        return $teamsArray;
    }

    public function testFindTeam()
    {
        $this->specify("Test find team", function ($teamId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock TeamMembersRepository
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['findBy']);
            $mockTeamMembers = $this->getMock('TeamMembers', ['getId']);
            $mockTeamMembersRepository->method('findBy')->willReturn($mockTeamMembers);

            // Mock TeamsRepository
            $mockTeamsRepository = $this->getMock('TeamsRepository', ['find', 'deleteTeam', 'flush']);
            $teamsArray = [
                'team_name' => 'Test',
                'team_description' => 'Team description',
            ];
            if ($teamId == -1 || $teamId == null) {
                $mockTeamsRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            }
            $mockTeamsRepository->method('find')->willReturn($this->getTeamsResponse($teamsArray));


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamMembersRepository::REPOSITORY_KEY,$mockTeamMembersRepository],
                    [TeamsRepository::REPOSITORY_KEY,$mockTeamsRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->findTeam($teamId);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing valid team id will delete the team and return teams instance
                [
                    1,
                    $this->getTeamsResponse(['team_name' => 'Test', 'team_description' => 'Team description'])
                ],
                // Test02 - Passing invalid team id will throw an exception
                [
                    -1,
                    'Team Not Found.'
                ],
                // Test03 - Passing team id as null will throw an exception
                [
                    null,
                    'Team Not Found.'
                ],
            ]
        ]);
    }

    public function testGetOrganizationTeamByUserId()
    {
        $this->specify("Test get organization team by user id", function ($organizationId, $personId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockPerson = $this->getMock('Person',['getId']);

            if ($personId == -1 || $personId == null) {
                $mockPersonRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            }

            // Mock OrganizationRepository
            $mockOrganizationRepository = $this->getMock('OrganizationRepository',['find']);
            $mockOrganization = $this->getMock('Organization',['getId']);

            if ($organizationId == -1 || $organizationId == null) {
                $mockOrganizationRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
            }

            // Mock OrgPersonFacultyRepository
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository',['findBy']);
            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', ['getId']);

            if (($organizationId == -1 || $organizationId == null) ||($personId == -1 || $personId == null)) {
                $mockOrgPersonFacultyRepository->method('findBy')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockOrgPersonFacultyRepository->method('findBy')->willReturn($mockOrgPersonFaculty);
            }

            // Mock TeamMembersRepository
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getOrganizationTeamByUserId']);
            $mockTeamMembersRepository->method('getOrganizationTeamByUserId')->willReturn($this->teamsArray);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY,$mockPersonRepository],
                    [OrganizationRepository::REPOSITORY_KEY,$mockOrganizationRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY,$mockOrgPersonFacultyRepository],
                    [TeamMembersRepository::REPOSITORY_KEY,$mockTeamMembersRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->getOrganizationTeamByUserId($organizationId, $personId);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing valid organization id and person id will return organization team details array
                [
                    1,
                    1,
                    $this->teamsArray
                ],
                // Test02 - Passing invalid organization id and valid person id will throw an exception
                [
                    -1,
                    1,
                    'Organization Not Found.'
                ],
                // Test03 - Passing valid organization id and invalid person id will throw an exception
                [
                    1,
                    -1,
                    'Person Not Found in this Organization.'
                ],
                // Test04 - Passing both organization id and person id as invalid will throw an exception
                [
                    -1,
                    -1,
                    'Organization Not Found.'
                ],
                // Test05 - Passing both organization id and person id as null will throw an exception
                [
                    null,
                    null,
                    'Organization Not Found.'
                ]
            ]
        ]);
    }

    public function testGetMyTeams()
    {
        $this->specify("Test get my teams", function ($loggedInUserId, $organizationId,  $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock TeamMembersRepository
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getMyTeams']);

            // Mock PersonService
            $mockPersonService = $this->getMock('PersonService',['findPerson']);

            $mockPerson = $this->getMock('Person',['getOrganization']);
            $mockOrganization = $this->getMock('Organization',['getId']);
            $mockOrganization->method('getId')->willReturn($organizationId);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);

            if ($loggedInUserId == -1 || $loggedInUserId == null){
                $mockPersonService->method('findPerson')->willThrowException(new ValidationException([], $expectedResult));
            }else {
                $mockPersonService->method('findPerson')->willReturn($mockPerson);
            }

            if ($loggedInUserId && is_null($organizationId)) {
                $mockTeamMembersRepository->method('getMyTeams')->willReturn([]);
            } else {
                $mockTeamMembersRepository->method('getMyTeams')->willReturn($this->teamsArray);
            }

            $mockContainer->method('get')
                ->willReturnMap([
                    [PersonService::SERVICE_KEY,$mockPersonService],
                ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamMembersRepository::REPOSITORY_KEY,$mockTeamMembersRepository],
                ]);

            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->getMyTeams($loggedInUserId);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing invalid logged in user id and valid organization id will throw an exception
                [
                    -1,
                    1,
                    'Person Not Found.'
                ],
                // Test02 - Passing logged in user id as null and valid organization id will throw an exception
                [
                    null,
                    1,
                    'Person Not Found.'
                ],
                // Test03 - Passing valid logged in user id and valid organization id will return my teams details for the person
                [
                    1,
                    1,
                    $this->getTeamsDtoResponse(1, $this->teamsArray)
                ],
                // Test02 - Passing valid logged in user id and organization id as null will return empty result array
                [
                    1,
                    null,
                    $this->getTeamsDtoResponse(1, [])
                ],
            ]
        ]);
    }

    private function getTeamsDtoResponse($personId, $teamsArray)
    {
        $teamsDto = new TeamsDto();
        if (!empty($teamsArray)) {
            $teamsDto->setPersonId($personId);
            $teamIdsDto = new TeamIdsDto();
            $teamIdsDto->setId($teamsArray[0]['team_id']);
            $teamIdsDto->setTeamName($teamsArray[0]['team_name']);
            $teamsDto->setTeamIds([$teamIdsDto]);
        }
        return $teamsDto;
    }

    public function testGetTeamMembersByPerson()
    {
        $this->specify("Test get my teams", function ($loggedInUserId, $teamId, $organizationId,  $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock PersonService
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockPerson = $this->getMock('Person', ['getOrganization']);
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn(1);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);

            // Mock TeamMembersRepository
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getTeamMembersByPerson']);

            if ($loggedInUserId == -1 || $loggedInUserId == null) {
                $mockPersonService->method('findPerson')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockPersonService->method('findPerson')->willReturn($mockPerson);
            }

            if ($loggedInUserId && is_null($organizationId)) {
                $mockTeamMembersRepository->method('getTeamMembersByPerson')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockTeamMembersRepository->method('getTeamMembersByPerson')->willReturn($this->teamMembersArray);
            }

            $mockContainer->method('get')
                ->willReturnMap([
                    [PersonService::SERVICE_KEY, $mockPersonService],
                ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamMembersRepository::REPOSITORY_KEY,$mockTeamMembersRepository],
                ]);

            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $teamsService->getTeamMembersByPerson($loggedInUserId, $teamId);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing invalid logged in user id and valid team id, organization id will throw an exception
                [
                    -1,
                    1,
                    2,
                    'Person Not Found.'
                ],
                 // Test02 - Passing valid logged in user id, team id and organization id as null will throw an exception
                [
                    1,
                    1,
                    null,
                    'Team Not Found.'
                ],
                // Test03 - Passing valid logged in user id, team id and organization id  will return team members details
                [
                    1,
                    1,
                    2,
                    $this->getTeamMembersDto($this->teamMembersArray, 1, 1)
                ],
                // Test04 - Passing logged in user id as null and valid team id, organization id will throw an exception
                [
                    null,
                    1,
                    2,
                    'Person Not Found.'
                ],
                // Test05 - Passing every value as null will throw an exception
                [
                    null,
                    null,
                    null,
                    'Person Not Found.'
                ]
            ]
        ]);
    }

    private function getTeamMembersDto($teamMembers, $loggedInUserId, $teamId)
    {
        $teamMembersArray = [];
        $teamsDto = new TeamsDto();
        $teamsDto->setPersonId($loggedInUserId);
        $teamsDto->setTeamId($teamId);
        $teamMembersDto = new TeamMembersDto();
        $teamMembersDto->setId($teamMembers[0]['person']);
        $teamMembersDto->setFirstName($teamMembers[0]['first_name']);
        $teamMembersDto->setLastName($teamMembers[0]['last_name']);
        $teamMembersDto->setTeamMemberEmailId($teamMembers[0]['primaryEmail']);
        $teamMembersArray[] = $teamMembersDto;
        $teamsDto->setTeamMembers($teamMembersArray);
        return $teamsDto;
    }

    public function testUpdateTeams()
    {
        $this->specify("Test update teams", function ($teamsDtoArray, $teamId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock LoggerHelperService
            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);

            // Mock OrganizationService
            $mockOrganizationService = $this->getMock('OrganizationService', ['find']);
            $mockOrganization = new Organization();
            if (($teamsDtoArray['organization_id'] == -1) || ($teamsDtoArray['organization_id'] == null)) {
                $mockOrganizationService->method('find')->willThrowException(new ValidationException([], $expectedResult));
            } else {
                $mockOrganizationService->method('find')->willReturn($mockOrganization);
            }

            // Mock Validator Service
            $mockValidatorService = $this->getMock('Validator', ['validate']);
            if (!empty($teamErrors)) {
                $errors = $this->arrayOfErrorObjects($teamErrors);
                $mockValidatorService->method('validate')->willReturn($errors);
            }

            // Mock TeamsRepository
            $mockTeamsRepository = $this->getMock('TeamsRepository', ['flush', 'find', 'updateTeams']);
            $mockTeams = $this->getTeamsResponse($teamsDtoArray);
            $mockTeamsRepository->method('createNewTeam')->willReturn($mockTeams);
            if($teamId == -1 || $teamId == null){
                $mockTeamsRepository->method('find')->willThrowException(new ValidationException([], $expectedResult));
            }else {
                $mockTeamsRepository->method('find')->willReturn($mockTeams['team']);
            }
            // Mock PersonService
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockPerson = $this->getMock('Synapse\CoreBundle\Entity\Person', ['getId']);
            $mockPerson->method('getId')->willReturn(3);
            $mockPersonService->method('findPerson')->willReturn($mockPerson);

            // Mock TeamMembersRepository
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getTeamById', 'find', 'updateTeamMembers', 'deleteMember']);
            $mockTeamMembersRepository->method('getTeamById')->willReturn([['id' => 1]]);
            $mockTeamMembers = $this->getMock('TeamMembers', ['getId', 'setPerson', 'setIsTeamLeader']);
            $mockPerson = $this->getMock('Person', ['getId']);
            $mockPerson->method('getId')->willReturn(201);
            $mockTeamMembers->method('setPerson')->willReturn($mockPerson);
            $mockTeamMembers->method('getId')->willReturn(123);
            $mockTeamMembersRepository->method('find')->willReturn($mockTeamMembers);

            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService],
                    [SynapseConstant::VALIDATOR, $mockValidatorService],
                    [PersonService::SERVICE_KEY, $mockPersonService],
                ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [TeamsRepository::REPOSITORY_KEY, $mockTeamsRepository],
                    [TeamMembersRepository::REPOSITORY_KEY, $mockTeamMembersRepository],
                ]);
            try {
                $teamsService = new TeamsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $teamsDto = $this->createTeamsDto($teamsDtoArray);
                $result = $teamsService->updateTeams($teamsDto);
                $this->assertEquals($expectedResult, $result);
            } catch (ValidationException $e) {
                if (!empty($teamErrors)) {
                    $this->assertEquals($e->getMessage(), $teamErrors['team_name']);
                } else {
                    $this->assertEquals($e->getMessage(), $expectedResult);
                }
            }
        }, [
            'examples' => [

                // Test01 - Passing organization id as null will throw ValidationException
                [
                    [
                        'organization_id' => null,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['person_id' => 1, 'is_leader' => true]]
                    ],
                    1,
                    'Organization not found!',
                ],
                // Test02 - Passing invalid team id will throw exception
                [
                    [
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['person_id' => 1, 'is_leader' => true]]
                    ],
                    -1,
                    'Team not found!',
                ],
                // Test03 - Updating team with action = delete and is_leader = true
                [
                    [
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['person_id' => 1, 'is_leader' => true, 'action' => 'delete']]
                    ],
                    1,
                    $this->getTeamsResponse([
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                    ], true),
                ],
                // Test04 - Updating team with action = delete and is_leader = false
                [
                    [
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                        'team_staff' => [['person_id' => 1, 'is_leader' => false, 'action' => 'delete']]
                    ],
                    1,
                    $this->getTeamsResponse([
                        'organization_id' => 1,
                        'team_name' => "Test",
                        'team_description' => "Test team",
                    ], true),
                ],
            ]
        ]);
    }
}