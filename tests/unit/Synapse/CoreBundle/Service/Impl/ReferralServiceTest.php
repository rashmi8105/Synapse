<?php

namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\DateTime;
use Synapse\CoreBundle\Entity\ActivityCategory;
use Synapse\CoreBundle\Entity\ActivityCategoryLang;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ReferralHistory;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Entity\ReferralsInterestedParties;

use Synapse\CoreBundle\Entity\Teams;

use Synapse\CoreBundle\Entity\ReferralsTeams;


use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;

use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;

use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;

use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\ReferralHistoryRepository;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Repository\ReferralRoutingRulesRepository;
use Synapse\CoreBundle\Repository\ReferralsInterestedPartiesRepository;
use Synapse\CoreBundle\Repository\ReferralsTeamsRepository;

use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\AssignToResponseDto;
use Synapse\RestBundle\Entity\ReferralsDTO;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Entity\TeamsDto;


class ReferralServiceTest extends Unit
{
    use Specify;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;


    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }


    /**
     * @expectedException \Synapse\CoreBundle\Exception\AccessDeniedException
     * @expectedExceptionMessage John Doe does not have access to the student.
     */
    public function testValidateFacultyNoAccessToStudent()
    {
        $this->specify("Staff do not has Access to student", function ($studentId, $referralDto, $studentAccess) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'error'
            ));

            $mockPersonRepository = $this->getMock('PersonRepository', array('find'));

            $mockPersonObject = $this->getMock('person', array('getFirstname', 'getLastName'));

            // Mocking manager service will be used in constroctor
            $managerService = $this->getMock('Manager', array(
                'checkAccessToStudent'
            ));
            $managerService->method('checkAccessToStudent')
                ->willReturn($studentAccess);

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    ['SynapseCoreBundle:Person', $mockPersonRepository]
                ]
            );

            $mockPersonRepository->expects($this->any())->method('find')->with($this->equalTo(3))->willReturn($mockPersonObject);
            $mockPersonObject->expects($this->any())->method('getFirstname')->willReturn('John');
            $mockPersonObject->expects($this->any())->method('getLastname')->willReturn('Doe');

            $mockContainer->method('get')->willReturnMap([['tinyrbac.manager', $managerService]]);
            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $referralService->validateFacultyAccessToStudent($studentId, $referralDto);

        }, [
            'examples' => [
                [
                    1,
                    $this->getReferralDto(),
                    false
                ]
            ]
        ]);
    }


    public function testValidateFacultyAccessToStudent()
    {
        $this->specify("Test to check staff has access to student access", function ($studentId, $referralDto, $studentAccess) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'error'
            ));

            // Mocking manager service will be used in constroctor
            $managerService = $this->getMock('Manager', array(
                'checkAccessToStudent'
            ));
            $managerService->method('checkAccessToStudent')
                ->willReturn($studentAccess);

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockContainer->method('get')->willReturnMap([['tinyrbac.manager', $managerService]]);
            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $studentStatus = $referralService->validateFacultyAccessToStudent($studentId, $referralDto);

            //method will return null if staff has access to student
            $this->assertEquals(NULL, $studentStatus);
        }, [
            'examples' => [
                [
                    1,
                    $this->getReferralDto(),
                    true
                ]
            ]
        ]);
    }


    private function getReferralDto()
    {
        $referral = new ReferralsDTO();
        $referral->setPersonStaffId(2);
        $referral->setAssignedToUserId(3);
        $referral->setInterestedParties(array(
            [
                'id' => 4
            ]
        ));

        return $referral;
    }


    public function testChangeReferralStatus()
    {
        $this->specify("Test to change referral status", function ($referralId, $loggedInPersonId, $creatorId, $assigneeId, $currentStatus, $newStatus, $expectedExceptionClass, $expectedExceptionMessage, $successfulCommunication, $isThereInterestedParties, $isReasonRouted, $coordinatorAccess) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mocking ReferralRepository
            $referralRepository = $this->getMock('ReferralRepository', ['find', 'flush']);

            //Creating Real Referral for a holder
            $referral = new Referrals();

            //Creating Persons to Populate Referral
            $creatorPerson = new Person();
            $creatorPerson->setId($creatorId);
            $assigneePerson = new Person();
            $assigneePerson->setId($assigneeId);

            if ($loggedInPersonId) {
                $closerOrUpdater = new Person();
                $closerOrUpdater->setId($loggedInPersonId);
            } else {
                $closerOrUpdater = null;
            }

            $organization = new Organization();
            $organization->setCampusId(5);

            //Filling Referral with Persons and Information
            $referral->setPersonFaculty($creatorPerson);
            $referral->setPersonAssignedTo($assigneePerson);
            $referral->setModifiedBy($loggedInPersonId);
            $referral->setStatus($currentStatus);
            $referral->setOrganization($organization);
            $referral->setIsReasonRouted($isReasonRouted);

            if ($referralId == -1) {
                $referralRepository->method('find')->willReturn(null);
            } else {
                $referralRepository->method('find')->willReturn($referral);

            }


            $mockRbacManager = $this->getMock('Manager', array('hasCoordinatorAccess'));
            $mockRbacManager->method('hasCoordinatorAccess')->willReturn($coordinatorAccess);


            // Mock organizationService
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrganizationDetailsLang']);

            //Mock OrganizationLang
            $mockOrganizationLang = $this->getMock('OrganizationLang', ['getId', 'getLang']);
            $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLang);

            //Mock activityCategoryRepository
            $mockActivityCategoryRepository = $this->getMock('ActivityCategoryRepository', ['find']);
            $mockActivityCategory = $this->getMock('ActivityCategory', ['getId']);
            $mockActivityCategoryRepository->method('find')->willReturn($mockActivityCategory);

            //Mock activityCategoryLangRepository
            $mockActivityCategoryLangRepository = $this->getMock('ActivityCategoryLangRepository', ['findOneBy']);
            $mockActivityCategoryLang = $this->getMock('ActivityCategoryLang', ['getId', 'getDescription']);
            $mockActivityCategoryLangRepository->method('findOneBy')->willReturn($mockActivityCategoryLang);

            //Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockPersonRepository->method('find')->willReturn($closerOrUpdater);

            $mockSecurityContext = $this->getMock('SecurityContext', array('getToken'));
            $mockToken = $this->getMock('Token', array('getUser'));
            $mockUser = $this->getMock('Person', array('getId'));
            $mockUser->method('getId')->willReturn($loggedInPersonId);
            $mockToken->method('getUser')->willReturn($mockUser);
            $mockSecurityContext->method('getToken')->willReturn($mockToken);

            $mockReferralHistoryRepository = $this->getMock('ReferralHistoryRepository', ['persist']);

            $tokenValues = [
                '$$creator_first_name$$' => 'Aniyah',
                '$$creator_last_name$$' => 'Cole',
                '$$creator_email_address$$' => 'scot@mailinator.com',
                '$$creator_title$$' => 'ASSOCIATE DEAN OF STUDENTS',
                '$$current_assignee_first_name$$' => 'Aniyah',
                '$$current_assignee_last_name$$' => 'Cole',
                '$$current_assignee_email_address$$' => 'scot@mailinator.com',
                '$$current_assignee_title$$' => 'ASSOCIATE DEAN OF STUDENTS',
                '$$student_first_name$$' => 'Alanna',
                '$$student_last_name$$' => 'Abbott',
                '$$student_email_address$$' => 'nits@mailinator.com',
                '$$student_title$$' => '',
                '$$coordinator_first_name$$' => 'Lyric',
                '$$coordinator_last_name$$' => 'Weaver',
                '$$coordinator_email_address$$' => 'nitesh63@mailinator.com',
                '$$coordinator_title$$' => 'SENIOR RESEARCH ANALYST, DEPARTMENT OF RESIDE',
                '$$date_of_creation$$' => '04/27/2017 10:55AM CDT',
                '$$Skyfactor_Mapworks_logo$$' => 'http://synapsetesting0062-integration.skyfactor.com/images/Skyfactor-Mapworks-login.png',
                '$$staff_referralpage$$' => 'http://synapsetesting0062-integration.skyfactor.com/#/dashboard/',
                'interested_parties' => []
            ];

            if ($isThereInterestedParties) {
                $interestedPartyArray = [];
                $interestedPartyArray['$$interested_party_first_name$$'] = 'Lyric';
                $interestedPartyArray['$$interested_party_last_name$$'] = 'Weaver';
                $interestedPartyArray['$$interested_party_email_address$$'] = 'nitesh631@mailinator.com';
                $interestedPartyArray['$$interested_party_id$$'] = 1;
                $tokenValues['interested_parties'][] = $interestedPartyArray;
            }

            // Mock MapworksActionService
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['getTokenVariablesFromPerson', 'sendCommunicationBasedOnMapworksAction']);
            $mockMapworksActionService->method('getTokenVariablesFromPerson')->willReturn($tokenValues);


            // Mock ReferralsInterestedPartiesRepository
            $mockReferralsInterestedPartiesRepository = $this->getMock('ReferralsInterestedPartiesRepository', ['findBy']);
            $mockReferralsInterestedPartiesRepository->method('findBy')->willReturn($this->getInterestedParties());

            // Mock PersonService
            $mockPersonService = $this->getMock('PersonService', ['getFirstPrimaryCoordinatorPerson']);
            $mockPersonService->method('getFirstPrimaryCoordinatorPerson')->willReturn($closerOrUpdater);

            // Mock dateUtilityService
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getFormattedDateTimeForOrganization']);
            $mockDateUtilityService->method('getFormattedDateTimeForOrganization')->willReturn(date('Y-m-d'));

            // Mock ebiConfigService
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl', 'generateCompleteUrl']);
            $mockEbiConfigService->method('getSystemUrl')->willReturn('https://mapworks.skyfactor.com');
            $mockEbiConfigService->method('generateCompleteUrl')->willReturn('https://mapworks.skyfactor.com/#/dashboard/');

            //Determine the number of communications that should be allowed and test for it
            if ($assigneeId == $loggedInPersonId && $creatorId == $loggedInPersonId && !$isThereInterestedParties) {
                $mockMapworksActionService->expects($this->once())->method('sendCommunicationBasedOnMapworksAction')->willReturn(true);
            } elseif (in_array($loggedInPersonId, [$creatorId, $assigneeId]) && !$isThereInterestedParties) {
                $mockMapworksActionService->expects($this->atMost(2))->method('sendCommunicationBasedOnMapworksAction')->willReturn(true);
            } elseif ($successfulCommunication) {
                $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn(true);
            } else {
                $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn(false);
            }


            // Scaffolding for Repository
            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [ReferralRepository::REPOSITORY_KEY, $referralRepository],
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                    [ActivityCategoryLangRepository::REPOSITORY_KEY, $mockActivityCategoryLangRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [ReferralHistoryRepository::REPOSITORY_KEY, $mockReferralHistoryRepository],
                    [ReferralsInterestedPartiesRepository::REPOSITORY_KEY, $mockReferralsInterestedPartiesRepository]
                ]);

            // Scaffolding for service
            $mockContainer->method('get')->willReturnMap(
                [
                    [Manager::SERVICE_KEY, $mockRbacManager],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService],
                    [SynapseConstant::SECURITY_CONTEXT_CLASS_KEY, $mockSecurityContext],
                    [MapworksActionService::SERVICE_KEY, $mockMapworksActionService],
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService]
                ]);

            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $referral = $referralService->changeReferralStatus($this->getReferralDtoForChangeStatus($newStatus), $loggedInPersonId, $referralId);
                $this->assertInstanceOf('Synapse\CoreBundle\Entity\Referrals', $referral);
                $this->assertEquals($newStatus, $referral->getStatus());
            } catch (SynapseException $exception) {

                verify($exception)->isInstanceOf($expectedExceptionClass);
                verify($exception->getMessage())->equals($expectedExceptionMessage);
            }

        },
            ['examples' => [
                // change referral status to open (Coordinator Access)
                [95, 2833, 2811, 2810, 'C', 'O', '', '', true, true, false, true],
                // change referral status to closed (Coordinator Access)
                [95, 2833, 2811, 2810, 'O', 'C', '', '', true, true, false, true],
                // change referral status back to open (Coordinator Access)
                [95, 2833, 2811, 2810, 'C', 'O', '', '', true, true, false, true],
                //Invalid Referral Id (Coordinator Access)
                [-1, 2833, 2811, 2810, 'C', 'O', '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Referral not found', true, true, false, true],
                //Communication Failed (Coordinator Access)
                [95, 2833, 2811, 2810, 'C', 'O', '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Expected Communication using Email or Notifications failed', false, true, false, true],
                //Bypass for Closer/Opener overriding Assignee Communications with no interested parties (Coordinator Access)
                [95, 2833, 2833, 2811, 'C', 'O', '', '', true, false, false, true],
                //Bypass for Closer/Opener overriding Creator Communications with no interested parties (Coordinator Access)
                [95, 2833, 2811, 2833, 'C', 'O', '', '', true, false, false, true],
                //Bypass for Closer/Opener overriding Both Creator/Assignee Communications with no interested parties (Coordinator Access)
                [95, 2833, 2833, 2833, 'C', 'O', '', '', true, false, false, true],
                //Invalid Person (Coordinator Access)
                [95, null, 2833, 2833, 'C', 'O', '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Referral Updater not found', true, false, false, true],
                //Referral Status Matches, INVALID REQUEST (Coordinator Access)
                [95, 2833, 2811, 2810, 'C', 'C', '', '', true, true, false, true],
                //Not Creator or Assignee or Coordinator: Does Not have Access
                [95, 2833, 2809, 2810, 'C', 'O', '\Synapse\CoreBundle\Exception\SynapseValidationException', 'Access denied. You do not have permission to close the referral.', true, true, false, false],
                // change referral status to open (Only Faculty Access), I am Creator
                [95, 2833, 2833, 2, 'C', 'O', '', '', true, true, false, false],
                // change referral status to open (Only Faculty Access), I am Assignee
                [95, 2833, 2, 2833, 'C', 'O', '', '', true, true, false, false]
            ]]);
    }


    private function getReferralDtoForChangeStatus($status)
    {
        $referral = new ReferralsDTO();
        $referral->setStatus($status);
        return $referral;
    }


    private function getReferralData($loggedIdPersonId, $personAssignedToId)
    {
        $referral = new Referrals();
        $referral->setStatus('O');
        $referral->setPersonIdFaculty($this->getPersonInstance($loggedIdPersonId));
        $referral->setPersonAssignedTo($this->getPersonInstance($personAssignedToId));
        $referral->setPersonStudent($this->getPersonInstance(6));
        $referral->setActivityCategory($this->getActivityCategoryInstance());
        $referral->setOrganization($this->getOrganizationInstance());
        return $referral;
    }


    private function getPersonInstance($id)
    {
        $person = new Person();
        $person->setId($id);
        $person->addContact($this->getContactInfoInstance());
        return $person;
    }


    private function getActivityCategoryInstance()
    {
        $activityCategory = new ActivityCategory();
        $activityCategory->setId(19);
        return $activityCategory;
    }


    private function getOrganizationInstance()
    {
        $organization = new Organization();
        $organization->setCampusId(2);
        return $organization;
    }


    private function getActivityCategoryLangInstance()
    {
        $activityCategoryLang = new ActivityCategoryLang();
        $activityCategoryLang->setActivityCategoryId(19);
        $activityCategoryLang->setDescription("Class Attendance Concern");
        return $activityCategoryLang;
    }


    private function getContactInfoInstance()
    {
        $contactInfo = new ContactInfo();
        return $contactInfo;
    }


    public function testGetReferralCampusConnections()
    {
        $this->specify("Test to get referral campus connections", function ($organizationId, $facultyId, $studentId) {

            // Declare mock services.
            $mockRbacManager = $this->getMock("Manager", ["assertPermissionToEngageWithStudents"]);

            // Declare mock repositories
            $mockFeatureMasterLangRepository = $this->getMock('FeatureMasterLangRepository', ['findOneBy']);
            $mockOrgFeaturesRepository = $this->getMock('OrgFeaturesRepository', ['isFeatureEnabledForOrganization']);
            $mockOrgGroupFacultyRepository = $this->getMock('OrgGroupFacultyRepository', ['getPermissionsByFacultyStudent']);
            $mockOrgPermissionsetFeaturesRepository = $this->getMock('OrgPermissionsetFeaturesRepository', ['getFeaturePermissions']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            $mockReferralRepository = $this->getMock('ReferralRepository', ['getPossibleReferralAssigneesByStudent']);

            // Create objects and mock objects
            $orgPersonStudent = new OrgPersonStudent();
            $primaryConnection = new Person();
            $orgPersonStudent->setPersonIdPrimaryConnect($primaryConnection);

            $mockFeatureMasterLangObject = $this->getMock('FeatureMasterLang', ['getId']);

            $this->mockContainer->method('get')->willReturnMap([['tinyrbac.manager', $mockRbacManager]]);

            // Mock method calls
            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($orgPersonStudent);

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    ["SynapseCoreBundle:FeatureMasterLang", $mockFeatureMasterLangRepository],
                    ["SynapseCoreBundle:OrgFeatures", $mockOrgFeaturesRepository],
                    ["SynapseCoreBundle:OrgGroupFaculty", $mockOrgGroupFacultyRepository],
                    ["SynapseCoreBundle:OrgPermissionsetFeatures", $mockOrgPermissionsetFeaturesRepository],
                    ["SynapseCoreBundle:OrgPersonStudent", $mockOrgPersonStudentRepository],
                    ["SynapseCoreBundle:Referrals", $mockReferralRepository],
                ]
            );

            $mockOrgFeaturesRepository->method('isFeatureEnabledForOrganization')->willReturn(1);
            $mockReferralRepository->method('getPossibleReferralAssigneesByStudent')->willReturn($this->getCampusConnections());

            $mockOrgGroupFacultyRepository->method('getPermissionsByFacultyStudent')->willReturn([['org_permissionset_id' => 364]]);
            $mockFeatureMasterLangRepository->method('findOneBy')->willReturn($mockFeatureMasterLangObject);
            $mockFeatureMasterLangObject->method('getId')->willReturn(1);
            $mockOrgPermissionsetFeaturesRepository->method('getFeaturePermissions')->willReturn(['private_create' => 1, 'public_create' => 1, 'teams_create' => 1]);

            // Call the function to be tested and verify results.
            $referralService = new ReferralService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $referralCampusConnections = $referralService->getReferralCampusConnections($organizationId, $facultyId, $studentId);

            $this->assertInternalType("array", $referralCampusConnections);
            foreach ($referralCampusConnections['campus_connections'] as $campusConnection) {

                if ($campusConnection['person_id'] == 4725738) {
                    $this->assertEquals($campusConnection['person_id'], 4725738);
                    $this->assertEquals($campusConnection['title'], 'Full-Time Staff');
                    $this->assertEquals($campusConnection['first_name'], 'John');
                    $this->assertEquals($campusConnection['last_name'], 'Smith');
                    $this->assertEquals($campusConnection['user_key'], 'CC-4725738');
                } else if ($campusConnection['person_id'] == 4725685) {
                    $this->assertEquals($campusConnection['person_id'], 4725685);
                    $this->assertEquals($campusConnection['title'], 'Full-Time Faculty');
                    $this->assertEquals($campusConnection['first_name'], 'John');
                    $this->assertEquals($campusConnection['last_name'], 'Doe');
                    $this->assertEquals($campusConnection['user_key'], 'CC-4725685');
                }
            }

        }, ['examples' => [[2, 6, 95], [2, 11, 95]]]);
    }


    private function getCampusConnections()
    {
        $campusConnections = [
            ['person_id' => 4725738, 'title' => "Full-Time Staff", 'first_name' => 'John', 'last_name' => 'Smith', 'user_key' => 'CC-4725738', 'is_invisible' => 0],
            ['person_id' => 4725685, 'title' => "Full-Time Faculty", 'first_name' => 'John', 'last_name' => 'Doe', 'user_key' => 'CC-4725685', 'is_invisible' => 0]
        ];
        return $campusConnections;
    }


    public function testMapReferralToTokenVariables()
    {
        $this->specify("Test to check staff has access to student access", function ($organizationId, $referralDto, $recipientType, $expectedResults) {
            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockInterestedPartiesRepository = $this->getMock('interestedPartiesRepository', ['findBy']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ReferralsInterestedPartiesRepository::REPOSITORY_KEY, $mockInterestedPartiesRepository]
                ]);


            $mockInterestedPartiesRepository->method('findBy')->willReturn($this->getInterestedParties());

            $mockMapworksActionService = $this->getMock('MapworksActionService', ['getTokenVariablesFromPerson']);
            $mockPersonService = $this->getMock('PersonService', ['getFirstPrimaryCoordinatorPerson']);
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['getFormattedDateTimeForOrganization']);
            $mockEbiConfigService = $this->getMock('ebiConfigService', ['getSystemUrl', 'generateCompleteUrl']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [MapworksActionService::SERVICE_KEY, $mockMapworksActionService],
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService]
                ]);
            if ($organizationId) {
                $tokenValues = ['$$' . $recipientType . '_first_name$$' => $recipientType . '_first_name',
                    '$$' . $recipientType . '_last_name$$' => $recipientType . '_last_name',
                    '$$' . $recipientType . '_email_address$$' => $recipientType . '_email_address',
                    '$$' . $recipientType . '_title$$' => $recipientType . '_title'
                ];
            } else {
                $tokenValues = [];
            }

            $mockMapworksActionService->method('getTokenVariablesFromPerson')->willReturn($tokenValues);

            $mockPersonService->method('getFirstPrimaryCoordinatorPerson')->willReturn($this->getPersonObject('coordinator'));
            $mockDateUtilityService->method('getFormattedDateTimeForOrganization')->willReturn('05/08/2017 10:50am UTC');
            $mockEbiConfigService->method('getSystemUrl')->willReturn('test-system-url');
            $mockEbiConfigService->method('generateCompleteUrl')->willReturn('test-dashboard-url');

            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $tokenValuesResult = $referralService->mapReferralToTokenVariables($organizationId, $referralDto);
            $this->assertEquals($tokenValuesResult, $expectedResults);
        }, [
            'examples' => [
                //Map token variable for faculty
                [
                    1,
                    $this->getReferral(),
                    'faculty',
                    [
                        '$$faculty_first_name$$' => 'faculty_first_name',
                        '$$faculty_last_name$$' => 'faculty_last_name',
                        '$$faculty_email_address$$' => 'faculty_email_address',
                        '$$faculty_title$$' => 'faculty_title',
                        'interested_parties' => [
                            [
                                '$$faculty_first_name$$' => 'faculty_first_name',
                                '$$faculty_last_name$$' => 'faculty_last_name',
                                '$$faculty_email_address$$' => 'faculty_email_address',
                                '$$faculty_title$$' => 'faculty_title',
                                '$$interested_party_id$$' => 2
                            ]
                        ],
                        '$$date_of_creation$$' => '05/08/2017 10:50am UTC',
                        '$$Skyfactor_Mapworks_logo$$' => 'test-system-urlimages/Skyfactor-Mapworks-login.png',
                        '$$staff_referralpage$$' => 'test-dashboard-url'
                    ]
                ],
                //Map token variable for student
                [
                    1,
                    $this->getReferral(),
                    'student',
                    [
                        '$$student_first_name$$' => 'student_first_name',
                        '$$student_last_name$$' => 'student_last_name',
                        '$$student_email_address$$' => 'student_email_address',
                        '$$student_title$$' => 'student_title',
                        'interested_parties' => [
                            [
                                '$$student_first_name$$' => 'student_first_name',
                                '$$student_last_name$$' => 'student_last_name',
                                '$$student_email_address$$' => 'student_email_address',
                                '$$student_title$$' => 'student_title',
                                '$$interested_party_id$$' => 2
                            ]
                        ],
                        '$$date_of_creation$$' => '05/08/2017 10:50am UTC',
                        '$$Skyfactor_Mapworks_logo$$' => 'test-system-urlimages/Skyfactor-Mapworks-login.png',
                        '$$staff_referralpage$$' => 'test-dashboard-url'
                    ]
                ],
                //if no token values for student
                [
                    null,
                    $this->getReferral(),
                    'student',
                    [
                        'interested_parties' => [
                            [
                                '$$interested_party_id$$' => 2
                            ]
                        ],
                        '$$date_of_creation$$' => '05/08/2017 10:50am UTC',
                        '$$Skyfactor_Mapworks_logo$$' => 'test-system-urlimages/Skyfactor-Mapworks-login.png',
                        '$$staff_referralpage$$' => 'test-dashboard-url'
                    ]
                ]
            ]
        ]);
    }


    private function getInterestedParties()
    {
        $interestedPartyObject = new ReferralsInterestedParties();
        $interestedPartyObject->setPerson($this->getPersonObject('faculty'));

        return [$interestedPartyObject];
    }


    private function getReferral()
    {
        $referral = new Referrals();
        $referral->setPersonFaculty($this->getPersonObject('faculty'));
        $referral->setPersonAssignedTo($this->getPersonObject('faculty'));
        $referral->setPersonStudent($this->getPersonObject('student'));
        $dateTime = new \DateTime('now');
        $referral->setCreatedAt($dateTime);
        return $referral;
    }


    private function getPersonObject($recipientType, $personId = '')
    {
        $person = new Person();
        if (!empty($personId)) {
            $person->setId($personId);
        } else {
            $person->setId(2);
        }
        $person->setFirstname($recipientType . '_first_name');
        $person->setLastname($recipientType . '_last_name');
        $person->setUsername($recipientType . '_email_address');
        $person->setTitle($recipientType . '_title');

        return $person;
    }


    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testCreateReferralHistoryRecord()
    {
        $this->specify("Test to create Referral History Record", function ($loggedInUserId, $referral, $action, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockSecurityContext = $this->getMock('SecurityContext', array('getToken'));
            $mockToken = $this->getMock('Token', array('getUser'));
            $mockUser = $this->getMock('Synapse\CoreBundle\Entity\Person', array('getId'));
            $mockUser->method('getId')->willReturn($loggedInUserId);
            $mockToken->method('getUser')->willReturn($mockUser);
            $mockSecurityContext->method('getToken')->willReturn($mockToken);

            $mockReferralHistoryRepository = $this->getMock('ReferralHistoryRepository', array('persist'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        \Synapse\CoreBundle\Repository\ReferralHistoryRepository::REPOSITORY_KEY,
                        $mockReferralHistoryRepository
                    ]
                ]);
            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        SynapseConstant::SECURITY_CONTEXT_CLASS_KEY, $mockSecurityContext
                    ]
                ]);

            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $loggedInPerson = $this->getPersonObject('faculty', $loggedInUserId);
            $results = $referralService->createReferralHistoryRecord($referral, $action, $loggedInPerson);
            $this->assertEquals($results->getCreatedBy()->getId(), $expectedResult['created_by']);
            $this->assertEquals($results->getAction(), $expectedResult['action']);
            $this->assertEquals($results->getPersonAssignedTo()->getId(), $expectedResult['assigned_to']);
            $this->assertEquals($results->getStatus(), $expectedResult['status']);
            $this->assertEquals($results->getUserKey(), $expectedResult['user_key']);


        },
            [
                'examples' => [
                    [// referral history for opening referral
                        45454563,
                        $this->getReferralObject(45454563, 5048693, 757568568, 57757567, 'O', 'Test1', 'PCC-90888'),
                        'open',
                        [
                            'created_by' => 45454563,
                            'assigned_to' => 57757567,
                            'action' => 'open',
                            'status' => 'O',
                            'user_key' => 'PCC-90888'
                        ]

                    ],
                    [// referral history for creating referral
                        56466645,
                        $this->getReferralObject(56466645, 5048693, 757568568, 57757567, 'C', 'Test2', 'PCC-88877'),
                        'create',
                        [
                            'created_by' => 56466645,
                            'assigned_to' => 57757567,
                            'action' => 'create',
                            'status' => 'C',
                            'user_key' => 'PCC-88877'
                        ]
                    ],
                    [//referral history for assigning referral
                        123123111,
                        $this->getReferralObject(123123111, 5048693, 757568568, 57757567, 'A', 'Test3', 'PCC-63639'),
                        'assign',
                        [
                            'created_by' => 123123111,
                            'assigned_to' => 57757567,
                            'action' => 'assign',
                            'status' => 'A',
                            'user_key' => 'PCC-63639'
                        ]
                    ],
                    [//referral history for null referral
                        5544123,
                        null,
                        'assign',
                        []
                    ]

                ]
            ]
        );
    }


    private function getReferralObject($personCreatedById, $personFacultyId, $personStudentId, $personAssignedToId, $referralStatus, $organizationSubDomain, $userKey)
    {

        $referral = new Referrals();
        $referral->setAccessPrivate(false);
        $referral->setAccessPublic(true);

        $personCreatedBy = new Person();
        $personCreatedBy->setId($personCreatedById);

        $referral->setCreatedBy($personCreatedBy);
        $referral->setAccessTeam(false);
        $referral->setIsHighPriority(false);
        $referral->setIsDiscussed(false);
        $referral->setIsLeaving(false);

        $personFaculty = new Person();
        $personFaculty->setId($personFacultyId);

        $referral->setPersonFaculty($personFaculty);
        $referral->setNotifyStudent(null);

        $personStudent = new Person();
        $personStudent->setId($personStudentId);

        $referral->setPersonIdStudent($personStudent);

        $personAssignedTo = new Person();
        $personAssignedTo->setId($personAssignedToId);

        $referral->setPersonAssignedTo($personAssignedTo);
        $referral->setStatus($referralStatus);

        $organization = new Organization();
        $organization->setSubdomain($organizationSubDomain);


        $referral->setOrganization($organization);
        $referral->setReferrerPermission(false);
        $referral->setUserKey($userKey);

        return $referral;
    }


    public function testSendCreateReferralCommunication()
    {
        $this->specify("Test email, notification for create referral", function ($studentNotification, $assignTo, $tokenValues, $isReasonRouted, $isNotificationSent, $notificationReason) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['sendCommunicationBasedOnMapworksAction']);

            $mockReferralRoutingRulesObject = $this->getMock('ReferralRoutingRules', ['getIsPrimaryCoordinator', 'getIsPrimaryCampusConnection']);
            $mockReferralRoutingRulesObject->method('getIsPrimaryCoordinator')->willReturn(1);
            $mockReferralRoutingRulesObject->method('getIsPrimaryCampusConnection')->willReturn(0);

            $mockPersonService = $this->getMock('PersonService', ['getFirstPrimaryCoordinatorPersonId']);
            $mockPersonService->method('getFirstPrimaryCoordinatorPersonId')->willReturn(1);


            $mockReferralRoutingRepository = $this->getMock('ReferralRoutingRulesRepository', array('find', 'findOneBy'));
            $mockReferralRoutingRepository->method('findOneBy')->willReturn($mockReferralRoutingRulesObject);

            $mockRepositoryResolver->method('getRepository')->willReturn($mockReferralRoutingRepository);

            $referral = $this->createReferral($studentNotification, $isReasonRouted);
            if (!$isNotificationSent) {
                $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn(false);
            } else {
                $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn(true);
            }
            $activityCategory = $this->getActivityCategoryInstance();
            $mockContainer->method('get')
                ->willReturnMap([
                    [MapworksActionService::SERVICE_KEY, $mockMapworksActionService],
                    [PersonService::SERVICE_KEY, $mockPersonService]
                ]);
            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $notificationSent = $referralService->sendCreateReferralCommunication($referral, $activityCategory, $assignTo, $tokenValues, false, $notificationReason);
            if ($isNotificationSent) {
                $this->assertEquals($notificationSent, $isNotificationSent); //returns true when notification sent
            } else {
                $this->assertEquals(is_array($notificationSent), true); // if the notifications are not sent this would be an array of errors
            }
        }, [
            'examples' => [

                // Notify student
                [1, 1233, [
                    '$$creator_first_name$$' => 'Admin',
                    '$$creator_last_name$$' => 'Skyfactor',
                    '$$creator_email_address$$' => 'admin@skyfactor.com',
                    '$$creator_title$$' => 'Faculty',
                    '$$coordinator_first_name$$' => 'Coordinator',
                    '$$coordinator_last_name$$' => 'Name',
                ], false, true, 'Academic performance concern'],

                // Student notification is false
                [0, 1233, [
                    '$$creator_first_name$$' => 'Admin',
                    '$$creator_last_name$$' => 'Skyfactor',
                    '$$creator_email_address$$' => 'admin@skyfactor.com',
                    '$$creator_title$$' => 'Faculty',
                    '$$coordinator_first_name$$' => 'Coordinator',
                    '$$coordinator_last_name$$' => 'Name',
                ], false, true, 'Academic Skills'],

                // Send Notification to interested parties.
                [1, 222,
                    [
                        '$$creator_first_name$$' => 'Admin',
                        '$$creator_last_name$$' => 'Skyfactor',
                        '$$creator_email_address$$' => 'admin@skyfactor.com',
                        '$$creator_title$$' => 'Faculty',
                        '$$coordinator_first_name$$' => 'Coordinator',
                        '$$coordinator_last_name$$' => 'Name',
                        'interested_parties' => [
                            [
                                '$$interested_party_first_name$$' => 'John',
                                '$$interested_party_last_name$$' => 'Jose',
                                '$$interested_party_email_address$$' => 'johnjose@mailinator.com',
                                '$$interested_party_title$$' => 'faculty',
                                '$$interested_party_id$$' => 23456
                            ]
                        ]
                    ],
                    false, true, 'Academic performance concern'
                ],
                // Reason routed referrals , notifications sent
                [1, 0,
                    [
                        '$$creator_first_name$$' => 'Admin',
                        '$$creator_last_name$$' => 'Skyfactor',
                        '$$creator_email_address$$' => 'admin@skyfactor.com',
                        '$$creator_title$$' => 'Faculty',
                        '$$coordinator_first_name$$' => 'Coordinator',
                        '$$coordinator_last_name$$' => 'Name',
                        'interested_parties' => [
                            [
                                '$$interested_party_first_name$$' => 'John',
                                '$$interested_party_last_name$$' => 'Jose',
                                '$$interested_party_email_address$$' => 'johnjose@mailinator.com',
                                '$$interested_party_title$$' => 'faculty',
                                '$$interested_party_id$$' => 23456
                            ]
                        ]
                    ],
                    true, true, 'Registration Concern'
                ],
                // notifications will not be sent here
                [1, 222,
                    [
                        'interested_parties' => [
                            [
                                '$$interested_party_first_name$$' => 'John',
                                '$$interested_party_last_name$$' => 'Jose',
                                '$$interested_party_email_address$$' => 'johnjose@mailinator.com',
                                '$$interested_party_title$$' => 'faculty',
                                '$$interested_party_id$$' => 23456
                            ]
                        ]
                    ],
                    true, false, 'Registration Concern'
                ]

            ]
        ]);
    }


    private function createReferral($isNotify, $isReasonRouted)
    {
        $referral = new Referrals();
        $referral->setPersonFaculty($this->getPersonObject('faculty'));
        $referral->setPersonAssignedTo($this->getPersonObject('faculty'));
        $referral->setPersonIdStudent($this->getPersonObject('student'));
        $referral->setIsReasonRouted($isReasonRouted);
        $dateTime = new \DateTime('now');
        $referral->setCreatedAt($dateTime);
        $referral->setNotifyStudent($isNotify);
        $organization = new Organization();
        $organization->setCampusId(213);
        $referral->setOrganization($organization);
        return $referral;
    }


    public function testSendBulkCreateReferralCommunication()
    {
        $this->specify("Test to send email, notification for bulk referral creation.", function ($tokenValues, $creatorArrayWithReferralCount, $assigneeArrayWithReferralCount, $interestedPartyArrayWithReferralCount, $interestedPartyArray, $sendCommunication) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $organizationId = 213;
            $mockReferralRepository = $this->getMock('ReferralRepository', ['findOneById']);

            $referralEntity = new Referrals();
            $referralHistoryEntity = new ReferralHistory();
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ReferralRepository::REPOSITORY_KEY, $mockReferralRepository]
                ]);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['sendCommunicationBasedOnMapworksAction']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [MapworksActionService::SERVICE_KEY, $mockMapworksActionService]
                ]);
            $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn($sendCommunication);
            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $notificationSent = $referralService->sendBulkCreateReferralCommunication($organizationId, $tokenValues, $creatorArrayWithReferralCount, $assigneeArrayWithReferralCount, $interestedPartyArrayWithReferralCount, $referralEntity, $referralHistoryEntity, $interestedPartyArray);

            if ($sendCommunication) {
                $this->assertEquals($notificationSent, $sendCommunication);
            } else {
                $this->assertEquals(is_array($notificationSent), true);
            }
        }, [
            'examples' => [
                // send email notification to assignee, creator and interested parties.
                [
                    [
                        '$$creator_first_name$$' => 'Admin',
                        '$$creator_last_name$$' => 'Skyfactor',
                        '$$creator_email_address$$' => 'admin@skyfactor.com',
                        '$$creator_title$$' => 'Faculty',
                        '$$coordinator_first_name$$' => 'Coordinator',
                        '$$coordinator_last_name$$' => 'Name',
                        'interested_parties' => [
                            [
                                '$$interested_party_first_name$$' => 'John',
                                '$$interested_party_last_name$$' => 'Jose',
                                '$$interested_party_email_address$$' => 'johnjose@mailinator.com',
                                '$$interested_party_title$$' => 'faculty',
                                '$$interested_party_id$$' => 23456
                            ]
                        ]
                    ],
                    [
                        23478 => 2,
                        65778 => 5
                    ],
                    [
                        7676767 => 5
                    ],
                    [
                        23456 => 3
                    ],
                    // this would be the list of interested parties, with interested party id as key. we need this while sending email to each of the interested party,
                    [
                        23456 => [
                            '$$interested_party_first_name$$' => 'John',
                            '$$interested_party_last_name$$' => 'Jose',
                            '$$interested_party_email_address$$' => 'johnjose@mailinator.com',
                            '$$interested_party_title$$' => 'faculty',
                            '$$interested_party_id$$' => 23456
                        ]
                    ],

                    true
                ],
                // send email notification to assignee and creator not interested parties.
                [
                    [
                        '$$creator_first_name$$' => 'Admin',
                        '$$creator_last_name$$' => 'Skyfactor',
                        '$$creator_email_address$$' => 'admin@skyfactor.com',
                        '$$creator_title$$' => 'Faculty',
                        '$$coordinator_first_name$$' => 'Coordinator',
                        '$$coordinator_last_name$$' => 'Name'
                    ],
                    // Creator array
                    [
                        23478 => 2,
                        65778 => 5
                    ],
                    // Assignee array
                    [
                        7676767 => 5
                    ],
                    [], // no interested parties,
                    [],
                    true
                ],
                // Send email, notification to only creator
                [
                    [
                        '$$creator_first_name$$' => 'Admin',
                        '$$creator_last_name$$' => 'Skyfactor',
                        '$$creator_email_address$$' => 'admin@skyfactor.com',
                        '$$creator_title$$' => 'Faculty',
                        '$$coordinator_first_name$$' => 'Coordinator',
                        '$$coordinator_last_name$$' => 'Name'
                    ],
                    // Creator array
                    [
                        23478 => 2,
                        65778 => 5
                    ],
                    [],
                    [],
                    [],
                    true
                ],
                // Error in email notification, so this will return SynapseValidationException
                [
                    [
                        '$$creator_first_name$$' => 'Admin',
                        '$$creator_last_name$$' => 'Skyfactor',
                        '$$creator_email_address$$' => 'admin@skyfactor.com',
                        '$$creator_title$$' => 'Faculty',
                        '$$coordinator_first_name$$' => 'Coordinator',
                        '$$coordinator_last_name$$' => 'Name'
                    ],
                    // Creator array
                    [
                        23478 => 2,
                        65778 => 5
                    ],
                    [],
                    [],
                    [],
                    false
                ],
            ]
        ]);
    }


    public function testSendCreateReferralCommunicationToStudent()
    {
        $this->specify("Test to send email, notification for bulk referral creation.", function ($referral, $tokenValues, $notificationReason, $sendCommunication) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $organizationId = 213;
            $mockReferralRepository = $this->getMock('ReferralRepository', ['findOneById']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ReferralRepository::REPOSITORY_KEY, $mockReferralRepository]
                ]);
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['sendCommunicationBasedOnMapworksAction']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [MapworksActionService::SERVICE_KEY, $mockMapworksActionService]
                ]);
            $referralHistoryEntity = new ReferralHistory();
            $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn($sendCommunication);
            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $notificationSent = $referralService->sendCreateReferralCommunicationToStudent($referral, $tokenValues, $notificationReason, $referralHistoryEntity);

            if ($sendCommunication) {
                $this->assertEquals($notificationSent, $sendCommunication); //return boolean true if notification is success
            } else {
                $this->assertEquals(is_array($notificationSent), true); // if sending notification fails it would be an array of errors
            }
        }, [
            'examples' => [

                // sends out notification
                [$this->createReferral(1, false), [
                    '$$student_first_name$$' => 'Admin',
                    '$$student_last_name$$' => 'Skyfactor',
                    '$$student_email_address$$' => 'admin@skyfactor.com',
                ], 'Academic performance concern', true],

                //fails to send notification
                [$this->createReferral(1, false), [
                    '$$student_first_name$$' => 'Admin',
                    '$$student_last_name$$' => 'Skyfactor',
                    '$$student_email_address$$' => 'admin@skyfactor.com',
                ], 'Academic performance concern', false]
            ]
        ]);
    }


    public function testSendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate()
    {
        $this->specify("Test to sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate", function ($studentId, $organizationId, $openReferrals, $currentAssignee, $creator, $interestedParty, $expectedResult) {
            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockReferralRepository = $this->getMock('ReferralRepository', ['findBy']);
            $mockReferralRepository->method('findBy')->willReturn($openReferrals);
            $mockReferralHistoryRepository = $this->getMock('ReferralHistoryRepository', ['persist']);
            $mockPersonObject = new Person();
            $mockPersonObject->setFirstname("Test");
            $mockPersonObject->setLastname("User");
            $mockPersonObject->setUsername("testuser@mailinator.com");
            $mockPersonObject->setTitle("Mr.");

            $mockReferralsInterestedPartiesRepository = $this->getMock('ReferralsInterestedPartiesRepository', ['findBy']);
            $mockReferralsInterestedPartiesRepository->method('findBy')->willReturn($this->getInterestedParties());


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ReferralRepository::REPOSITORY_KEY, $mockReferralRepository],
                    [ReferralsInterestedPartiesRepository::REPOSITORY_KEY, $mockReferralsInterestedPartiesRepository],
                    [ReferralHistoryRepository::REPOSITORY_KEY, $mockReferralHistoryRepository]
                ]);

            $mockMapworksActionService = $this->getMock('MapworksActionService', ['getTokenVariablesFromPerson', 'sendCommunicationBasedOnMapworksAction']);
            $mockPersonService = $this->getMock('PersonService', ['getAllPrimaryCoordinators', 'getFirstPrimaryCoordinatorPerson']);
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getFormattedDateTimeForOrganization']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl', 'generateCompleteUrl']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['find']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [MapworksActionService::SERVICE_KEY, $mockMapworksActionService],
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService]
                ]);
            if ($organizationId) {
                $tokenValues = [
                    '$$creator_first_name$$' => 'Aniyah',
                    '$$creator_last_name$$' => 'Cole',
                    '$$creator_email_address$$' => 'scot@mailinator.com',
                    '$$creator_title$$' => 'ASSOCIATE DEAN OF STUDENTS',
                    '$$current_assignee_first_name$$' => 'Aniyah',
                    '$$current_assignee_last_name$$' => 'Cole',
                    '$$current_assignee_email_address$$' => 'scot@mailinator.com',
                    '$$current_assignee_title$$' => 'ASSOCIATE DEAN OF STUDENTS',
                    '$$student_first_name$$' => 'Alanna',
                    '$$student_last_name$$' => 'Abbott',
                    '$$student_email_address$$' => 'nits@mailinator.com',
                    '$$student_title$$' => '',
                    '$$coordinator_first_name$$' => 'Lyric',
                    '$$coordinator_last_name$$' => 'Weaver',
                    '$$coordinator_email_address$$' => 'nitesh63@mailinator.com',
                    '$$coordinator_title$$' => 'SENIOR RESEARCH ANALYST, DEPARTMENT OF RESIDE',
                    '$$date_of_creation$$' => '04/27/2017 10:55AM CDT',
                    '$$Skyfactor_Mapworks_logo$$' => 'http://synapsetesting0062-integration.skyfactor.com/images/Skyfactor-Mapworks-login.png',
                    '$$staff_referralpage$$' => 'http://synapsetesting0062-integration.skyfactor.com/#/dashboard/',
                    'interested_parties' => []
                ];

                if ($interestedParty) {
                    $interestedPartyArray = [];
                    $interestedPartyArray['$$interested_party_first_name$$'] = 'Lyric';
                    $interestedPartyArray['$$interested_party_last_name$$'] = 'Weaver';
                    $interestedPartyArray['$$interested_party_email_address$$'] = 'nitesh631@mailinator.com';
                    $interestedPartyArray['$$interested_party_id$$'] = 1;
                    $tokenValues['interested_parties'][] = $interestedPartyArray;
                }
            } else {
                $tokenValues = [];
            }

            $sendCommunicationAction = false;
            if ($currentAssignee && $creator && $interestedParty) {
                $sendCommunicationAction = true;
            }

            $mockMapworksActionService->method('getTokenVariablesFromPerson')->willReturn($tokenValues);
            $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn($sendCommunicationAction);
            $mockPersonService->method('getFirstPrimaryCoordinatorPerson')->willReturn($mockPersonObject);

            $referralService = new ReferralService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $sentStatusResult = $referralService->sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate($studentId, $organizationId, 'student_made_participant');

            $this->assertInternalType("boolean", $sentStatusResult);
            $this->assertEquals($sentStatusResult, $expectedResult);

        }, [
            'examples' => [
                // test condition when no open referrals for the student.. Expected result "true"
                [
                    4901835,
                    203,
                    [],
                    false,
                    false,
                    false,
                    true
                ],
                // test condition when student has open referrals with current_assignee is "true" then Expected result "false"
                [
                    4778376,
                    203,
                    $this->getOpenReferrals(4778376),
                    true,
                    false,
                    false,
                    false
                ],
                //test condition when student has open referrals with creator is "true" then Expected result "false"
                [
                    4879376,
                    203,
                    $this->getOpenReferrals(4879376),
                    false,
                    true,
                    false,
                    false
                ],
                //test condition when student has open referrals with interested parties is "true" then Expected result "false"
                [
                    4879376,
                    203,
                    $this->getOpenReferrals(4879376),
                    false,
                    false,
                    true,
                    false
                ],
                //test condition when student has open referrals with current_assignee, creator and interested parties is "true" then Expected result "true"
                [
                    4879376,
                    203,
                    $this->getOpenReferrals(4879376),
                    true,
                    true,
                    true,
                    true
                ]

            ]
        ]);
    }


    private function getOpenReferrals($studentId)
    {
        $referral = new Referrals();
        $referral->setPersonFaculty($this->getPersonObject('faculty'));
        $referral->setPersonAssignedTo($this->getPersonObject('faculty'));
        $referral->setPersonStudent($this->getPersonObject('student', $studentId));
        $referral->setStatus('O');
        $dateTime = new \DateTime('now');
        $referral->setCreatedAt($dateTime);
        return [$referral];
    }


    public function getReferralMock($privateAccess, $publicAccess, $teamAccess)
    {

        $mockOrganization = $this->getMock(get_class(new Organization()), ['getId']);
        $mockOrganization->method('getId')->willReturn(1);

        $mockActivityCategory = $this->getMock(get_class(new ActivityCategory()), ['getId']);
        $mockActivityCategory->method('getId')->willReturn(1);

        $mockReferral = $this->getMock(get_class(new Referrals()), ['getId']);
        $mockReferral->method('getId')->willReturn(1);
        $mockReferral->setPersonFaculty($this->getPersonMock(1, "faculty", "name"));
        $mockReferral->setPersonStudent($this->getPersonMock(2, "student", "name"));
        $mockReferral->setPersonAssignedTo($this->getPersonMock(3, "assigned", "name"));
        $mockReferral->setOrganization($mockOrganization);
        $mockReferral->setActivityCategory($mockActivityCategory);
        $mockReferral->setAccessPrivate($privateAccess);
        $mockReferral->setAccessPublic($publicAccess);
        $mockReferral->setAccessTeam($teamAccess);
        return $mockReferral;

    }

    public function getPersonMock($personId, $firstName = null, $lastName = null)
    {

        $mockPerson = $this->getMock(get_class(new Person()), ['getId']);
        $mockPerson->method('getId')->willReturn($personId);
        $mockPerson->setFirstname($firstName);
        $mockPerson->setLastname($lastName);
        return $mockPerson;
    }


    public function testGetReferrals()
    {
        $this->specify("Test Get Referral", function ($referralId, $isValidReferral, $permission, $hasAssetAccess, $validAssignedTo, $studentActiveStatus, $hasInterestedParty, $privateAccess, $publicAccess, $teamAccess, $expectedResult) {

            $mockRbacManager = $this->getMock('Manager', ['assertPermissionToEngageWithStudents', 'hasAssetAccess']);

            if ($permission) {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willReturn($permission);
            } else {
                $mockRbacManager->method('assertPermissionToEngageWithStudents')->willThrowException(
                    new AccessDeniedException("No Permissions")
                );
            };
            $mockRbacManager->method('hasAssetAccess')->willReturn($hasAssetAccess);

            $mockLang = $this->getMock('Lang', ['getId']);
            $mockLang->method('getId')->willReturn(1);
            $mockOrgLangObject = $this->getMock('orglang', ['getLang']);
            $mockOrgLangObject->method('getLang')->willReturn($mockLang);
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrganizationDetailsLang']);
            $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrgLangObject);

            $mockUserManagementService = $this->getMock('userManagementService', ['isStudentActive']);
            $mockUserManagementService->method('isStudentActive')->willReturn($studentActiveStatus);

            $mockPersonService = $this->getMock('PersonService', ['getFirstPrimaryCoordinatorPerson']);

            $mockPersonPrimaryCoordinator = $this->getPersonMock(5, 'primary', 'coordinator');
            $mockPersonService->method('getFirstPrimaryCoordinatorPerson')->willReturn($mockPersonPrimaryCoordinator);

            $mockReferralRepository = $this->getMock("ReferralRepository", ['find']);
            if ($isValidReferral) {
                $mockReferralRepository->method('find')->willReturn($this->getReferralMock($privateAccess, $publicAccess, $teamAccess));
            } else {
                $mockReferralRepository->method('find')->willThrowException(
                    new SynapseValidationException('ReferralId is Not valid')
                );
            }

            $mockLangEntity = $this->getMock('OrgLang', ['getDescription']);
            $mockLangEntity->method('getDescription')->willReturn('category');
            $mockActivityCategoryLangRepository = $this->getMock("ActivityCategoryLangRepository", ['findOneBy']);
            $mockActivityCategoryLangRepository->method('findOneBy')->willReturn($mockLangEntity);

            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneBy']);
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy']);
            $mockInterestedPartiesRepositories = $this->getMock('InterestedPartiesRepositories', ['findBy']);

            if ($hasInterestedParty) {
                $mockInterstedParty = $this->getMock(get_class(new ReferralsInterestedParties()), ['getPerson']);
                $mockInterstedParty->method('getPerson')->willReturn($this->getPersonMock(6, "interested", "party"));
                $mockInterestedPartiesRepositories->method('findBy')->willReturn([$mockInterstedParty]);
                $mockPersonRepository->method('findOneBy')->willReturn($this->getPersonMock(6, "interested", "party"));
            }

            if ($validAssignedTo) {
                $mockPersonRepository->method('findOneBy')->willReturn(1);
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn(1);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn(null);
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn(null);
            }

            $mockReferralTeamsRepositories = $this->getMock("ReferralsTeamsRepository", ['findBy']);
            $mockReferralTeamEntity = $this->getMock('ReferralTeam', ['getTeams']);

            $mockTeamEntity = $this->getMock(get_class(new Teams()), ['getId', 'getTeamName']);
            $mockTeamEntity->method('getId')->willReturn(1);
            $mockTeamEntity->method('getTeamName')->willReturn("Team Name");

            $mockReferralTeamEntity->method('getTeams')->willReturn($mockTeamEntity);
            $mockTeamRepository = $this->getMock('TeamRepository', ['findOneBy']);
            $mockTeamRepository->method('findOneBy')->willReturn($mockTeamEntity);

            if ($teamAccess) {
                $mockReferralTeamsRepositories->method('findBy')->willReturn([$mockReferralTeamEntity]);
            } else {
                $mockReferralTeamsRepositories->method('findBy')->willReturn([]);
            }
            $mockReferralTeamsRepositories->method('findBy')->willReturn(1);
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    ReferralRepository::REPOSITORY_KEY,
                    $mockReferralRepository
                ],
                [
                    ActivityCategoryLangRepository::REPOSITORY_KEY,
                    $mockActivityCategoryLangRepository
                ],
                [
                    PersonRepository::REPOSITORY_KEY,
                    $mockPersonRepository
                ],
                [
                    ReferralsInterestedPartiesRepository::REPOSITORY_KEY,
                    $mockInterestedPartiesRepositories
                ],
                [
                    OrgPersonFacultyRepository::REPOSITORY_KEY,
                    $mockOrgPersonFacultyRepository
                ],
                [
                    ReferralsTeamsRepository::REPOSITORY_KEY,
                    $mockReferralTeamsRepositories
                ],
                [
                    TeamsRepository::REPOSITORY_KEY,
                    $mockTeamRepository
                ]
            ]);

            $this->mockContainer->method('get')->willReturnMap([
                [
                    SynapseConstant::TINYRBAC_MANAGER,
                    $mockRbacManager
                ],
                [
                    OrganizationService::SERVICE_KEY,
                    $mockOrganizationService
                ],
                [
                    UserManagementService::SERVICE_KEY,
                    $mockUserManagementService
                ],
                [
                    PersonService::SERVICE_KEY,
                    $mockPersonService
                ]
            ]);
            $referralService = new ReferralService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            try {
                $result = $referralService->getReferral($referralId);

                $this->assertInstanceOf('Synapse\RestBundle\Entity\ReferralsDTO', $result);

                $this->assertEquals($result->getOrganizationId(), $expectedResult->getOrganizationId());
                $this->assertEquals($result->getReferralId(), $expectedResult->getReferralId());

                $this->assertEquals($result->getAssignedTo()->getUserId(), $result->getAssignedTo()->getUserId());
                $this->assertEquals($result->getAssignedTo()->getFirstName(), $result->getAssignedTo()->getFirstName());
                $this->assertEquals($result->getAssignedTo()->getLastName(), $result->getAssignedTo()->getLastName());
                $this->assertEquals($result->getStudentStatus(), $studentActiveStatus);
                $this->assertEquals($result->getInterestedParties(), $result->getInterestedParties());

                $sharingOptions = $result->getShareOptions()[0];
                $expectedSharingOptons = $expectedResult->getShareOptions()[0];
                $this->assertInstanceOf("Synapse\RestBundle\Entity\ShareOptionsDto", $sharingOptions);
                $this->assertEquals($sharingOptions->getPrivateShare(), $expectedSharingOptons->getPrivateShare());
                $this->assertEquals($sharingOptions->getPublicShare(), $expectedSharingOptons->getPublicShare());
                $this->assertEquals($sharingOptions->getTeamsShare(), $expectedSharingOptons->getTeamsShare());
                if ($teamAccess) {

                    $teams = $sharingOptions->getTeamIds()[0];
                    $expectedTeams = $expectedSharingOptons->getTeamIds()[0];
                    $this->assertEquals($teams->getId(), $expectedTeams->getId());
                    $this->assertEquals($teams->getTeamName(), $expectedTeams->getTeamName());
                }

            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }
        }, [
            'examples' => [

                // When referralId is valid with student status as false , private sharing
                [
                    1, true, true, true, true, false, false, true, false, false, $this->getReferralResponseDto(true, false, false, true, false, false)
                ],
                // When referralId is valid with student status as false , public sharing
                [
                    1, true, true, true, true, false, false, false, true, false, $this->getReferralResponseDto(true, false, false, false, true, false)
                ],
                // When referralId is valid with student status as false , Team sharing
                [
                    1, true, true, true, true, false, false, false, false, true, $this->getReferralResponseDto(true, false, false, false, false, true)
                ],
                // When referralId is valid with student status as false
                [
                    1, true, true, true, true, false, false, true, false, false, $this->getReferralResponseDto(true, false, false, true, false, false)
                ],
                // When referralId is valid without interestedParties
                [
                    1, true, true, true, true, true, false, true, false, false, $this->getReferralResponseDto(true, true, false, true, false, false)
                ],
                // When referralId is valid with interestedParties
                [
                    1, true, true, true, true, true, true, true, false, false, $this->getReferralResponseDto(true, true, true, true, false, false)
                ],
                // When referralId is valid but no but assigned to person is invalid, gets assigned to primary coordinator
                [
                    1, true, true, true, false, true, false, true, false, false, $this->getReferralResponseDto(false, true, false, true, false, false)
                ],
                // When referralId is valid but no asset access
                [
                    1, true, true, false, true, true, true, true, false, false, "referral"
                ],
                // When referralId is valid but no permissions available
                [
                    1, true, false, false, true, true, true, true, false, false, "No Permissions"
                ],
                // Invalid Referral Id
                [
                    1, false, false, false, true, true, true, true, false, false, "ReferralId is Not valid"
                ]
            ]
        ]);
    }


    private function getReferralResponseDto($isValidAssignTo, $studentStatus, $hasInterestedParty, $privateAccess, $publicAccess, $teamAccess)
    {

        $referralDto = new ReferralsDTO();

        $referralDto->setOrganizationId(1);
        $referralDto->setReferralId(1);

        if ($isValidAssignTo) {

            $assignTo = new AssignToResponseDto();
            $assignTo->setUserId(3);
            $assignTo->setFirstName('assigned');
            $assignTo->setLastName('name');
            $referralDto->setAssignedTo($assignTo);
        } else {

            $assignTo = new AssignToResponseDto();
            $assignTo->setUserId(5);
            $assignTo->setFirstName('primary');
            $assignTo->setLastName('coordinator');
            $referralDto->setAssignedTo($assignTo);

        }

        $referralDto->setStudentStatus($studentStatus);
        if ($hasInterestedParty) {

            $interestedPartyArray = [
                'id' => 6,
                'first_name' => "interested",
                'last_name' => "party",
            ];
            $referralDto->setInterestedParties([$interestedPartyArray]);
        }
        $sharingOptions = new ShareOptionsDto();
        $sharingOptions->setPrivateShare($privateAccess);
        $sharingOptions->setPublicShare($publicAccess);
        if ($teamAccess) {
            $sharingOptions->setTeamsShare($teamAccess);
            $teamDto = new TeamIdsDto();
            $teamDto->setId(1);
            $teamDto->setTeamName("Team Name");
            $sharingOptions->setTeamIds([$teamDto]);
        }
        $referralDto->setShareOptions([$sharingOptions]);

        return $referralDto;
    }


    public function testDetermineMapworksActionFromEditedReferral()
    {
        $this->specify("Test to determineMapworksActionFromEditedReferral", function ($assignTo, $assignedId, $areInterestedPartiesAdded, $areInterestedPartiesRemoved, $referralDtoDetailsArray, $teamId, $expectedResult) {

            // Mock Referrals
            $mockReferrals = $this->getMock('Referrals', ['getId', 'getNote']);
            $mockReferrals->method('getNote')->willReturn("This is a test comment");

            // Mock ReferralsTeamsRepository
            $mockReferralsTeamsRepository = $this->getMock('ReferralsTeamsRepository', ['findBy']);

            $referralsTeamsObject = new ReferralsTeams();

            $teamsObject = new Teams();
            $teamsObject->setId($teamId);

            $referralsTeamsObject->setTeams($teamsObject);
            $mockReferralsTeamsRepository->method('findBy')->willReturn([$referralsTeamsObject]);

            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ReferralsTeamsRepository::REPOSITORY_KEY, $mockReferralsTeamsRepository]
                ]);

            $referralService = new ReferralService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $mapworksAction = $referralService->determineMapworksActionFromEditedReferral($this->getReferralDtoDetails($referralDtoDetailsArray), $this->getReferralDetails(), $assignTo, $assignedId, $areInterestedPartiesAdded, $areInterestedPartiesRemoved);
            if ($expectedResult != NULL) {
                $this->assertInternalType("string", $mapworksAction);
            }
            $this->assertEquals($mapworksAction, $expectedResult);
        },
            [
                'examples' => [

                    // Test0 - If person assignee is not changed but referral team has been changed , action will be 'update_content'.
                    [123, 123, false, false,
                        [
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 112,
                        'update_content'
                    ],

                    // Test1 - If person assignee is not changed but referral comment has been changed , action will be 'update_content'.
                    [123, 123, false, false,
                        [
                            'comment' => 'This comment has been updated',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 111,
                        'update_content'
                    ],

                    // Test2 - If person assignee is not changed but referral interested party is added, action will be 'add_interested_party'.
                    [123, 123, true, false,
                        [
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 111,
                        'add_interested_party'
                    ],

                    // Test3 - If person assignee is not changed but referral interested party is removed, action will be 'remove_interested_party'.
                    [123, 123, false, true,
                        [
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 111,
                        'remove_interested_party'
                    ],

                    // Test4 - If person assignee is not changed and nothing has been modified in referral , action will be 'null'.
                    [123, 123, false, false,
                        [
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 111,
                        NULL
                    ],

                    // Test5 - If person assignee is changed, action will be 'reassign'.
                    [123, 124, false, false,
                        [
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 111,
                        'reassign'
                    ],
                    // Test6 - Passing invalid person assign to, action will be 'reassign'.
                    [-1, 124, false, false,
                        [
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 111,
                        'reassign'
                    ],
                    // Test7 - Passing invalid person assigned id, action will be 'reassign'.
                    [1, -1, false, false,
                        [
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        'teamId' => 111,
                        'reassign'
                    ],
                ]
            ]);
    }

    private function getReferralDtoDetails($referralDtoDetailsArray)
    {
        $referralsDto = new ReferralsDTO();
        $referralsDto->setComment($referralDtoDetailsArray['comment']);
        $referralsDto->setIssueDiscussedWithStudent($referralDtoDetailsArray['issue_discussed_with_student']);
        $referralsDto->setHighPriorityConcern($referralDtoDetailsArray['high_priority_concern']);
        $referralsDto->setIssueRevealedToStudent($referralDtoDetailsArray['issue_revealed_to_student']);
        $referralsDto->setStudentIndicatedToLeave($referralDtoDetailsArray['student_indicated_to_leave']);
        $referralsDto->setNotifyStudent($referralDtoDetailsArray['notify_student']);
        $referralsDto->setShareOptions([$this->getShareOptionsDto($referralDtoDetailsArray['share_options'])]);
        if (isset($referralDtoDetailsArray['interested_parties'])) {
            $referralsDto->setInterestedParties($referralDtoDetailsArray['interested_parties']);
        }
        if (isset($referralDtoDetailsArray['assigned_to_user_id'])) {
            $referralsDto->setAssignedToUserId($referralDtoDetailsArray['assigned_to_user_id']);
        }
        if (isset($referralDtoDetailsArray['referral_id'])) {
            $referralsDto->setReferralId($referralDtoDetailsArray['referral_id']);
        }
        return $referralsDto;
    }

    private function getShareOptionsDto($shareOptionsArray)
    {
        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPrivateShare($shareOptionsArray['private_share']);
        $shareOptionsDto->setPublicShare($shareOptionsArray['public_share']);
        $shareOptionsDto->setTeamsShare($shareOptionsArray['teams_share']);
        $teamIdsDto = new TeamIdsDto();
        $teamIdsDto->setId(111);
        $teamIdsDto->setIsTeamSelected(true);
        $teamIdsDto->setTeamName("Test team");
        $shareOptionsDto->setTeamIds([$teamIdsDto]);
        return $shareOptionsDto;
    }

    private function getReferralDetails()
    {

        $referrals = new Referrals();
        $referrals->setNote("This is test");
        $referrals->setIsDiscussed(true);
        $referrals->setIsHighPriority(true);
        $referrals->setReferrerPermission(true);
        $referrals->setIsLeaving(true);
        $referrals->setNotifyStudent(false);
        $referrals->setAccessPublic(true);
        $referrals->setAccessPrivate(false);
        $referrals->setAccessTeam(false);
        return $referrals;
    }

    public function testEditReferral()
    {
        $this->specify("Test to determineMapworksActionFromEditedReferral", function ($referralDtoDetailsArray, $loggedInPersonId, $studentAccessPermission, $exceptionMessage, $expectedResult) {

            // Mock SecurityContext
            $mockSecurityContext = $this->getMock('SecurityContext', array('getToken'));
            $mockToken = $this->getMock('Token', array('getUser'));
            $mockUser = $this->getMock('Person', array('getId'));
            $mockUser->method('getId')->willReturn($loggedInPersonId);
            $mockToken->method('getUser')->willReturn($mockUser);
            $mockSecurityContext->method('getToken')->willReturn($mockToken);

            // Mock Person
            $mockPerson = $this->getPersonObject('coordinator');

            // Mock activityCategory
            $mockActivityCategory = $this->getMock('ActivityCategory', ['id', 'getId']);
            $mockActivityCategory->method('getId')->willReturn(19);

            // Mock ActivityCategoryRepository
            $mockActivityCategoryRepository = $this->getMock('ActivityCategoryRepository', ['find']);
            $mockActivityCategoryRepository->method('find')->willReturn($mockActivityCategory);

            // Mock Organization
            $mockOrganization = $this->getMock('Organization', ['id', 'getId']);
            $mockOrganization->method('getId')->willReturn(203);

            // Mock Referral
            $mockReferral = $this->getReferralInstance($mockPerson);

            // Mock ReferralRepository
            $mockReferralRepository = $this->getMock('ReferralRepository', ['find', 'flush']);
            if ($referralDtoDetailsArray['referral_id'] != -1) {
                $mockReferralRepository->method('find')->willReturn($mockReferral);
            } else {
                $mockReferralRepository->method('find')->willReturn(null);
            }

            $currentDateTime = new \DateTime();
            // Mock Manager
            $mockRbacManager = $this->getMock("Manager", ['assertPermissionToEngageWithStudents', 'hasStudentAccess', 'checkAccessToStudent']);
            $mockRbacManager->method('hasStudentAccess')->willReturn($studentAccessPermission);
            $mockRbacManager->method('checkAccessToStudent')->willReturn($studentAccessPermission);

            // Mock ReferralsInterestedParties
            $mockReferralsInterestedParties = $this->getMock('ReferralsInterestedParties', ['id', 'setPerson', 'getPerson']);
            $mockReferralsInterestedParties->method('setPerson')->willReturn($mockPerson);
            $mockReferralsInterestedParties->method('getPerson')->willReturn($mockPerson);

            // Mock ReferralsInterestedPartiesRepository
            $mockReferralsInterestedPartiesRepository = $this->getMock('ReferralsInterestedPartiesRepository', ['findBy', 'findOneBy', 'createReferralsInterestedParties', 'flush', 'removeReferralsInterestedParties']);
            $mockReferralsInterestedPartiesRepository->method('findBy')->willReturn([$mockReferralsInterestedParties]);
            $mockReferralsInterestedPartiesRepository->method('findOneBy')->willReturn($mockReferralsInterestedParties);

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockPersonRepository->method('find')->willReturn($mockPerson);

            // Mock ReferralRoutingRules
            $mockReferralRoutingRules = $this->getMock('ReferralRoutingRules', ['id', 'getIsPrimaryCoordinator', 'getIsPrimaryCampusConnection', 'getPerson']);
            $mockReferralRoutingRules->method('getPerson')->willReturn($mockPerson);

            // Mock ReferralRoutingRulesRepository
            $mockReferralRoutingRulesRepository = $this->getMock('ReferralRoutingRulesRepository', ['findOneBy']);
            $mockReferralRoutingRulesRepository->method('findOneBy')->willReturn($mockReferralRoutingRules);

            // Mock OrgPermissionsetService
            $mockOrgPermissionSetService = $this->getMock('OrgPermissionsetService', ['getStudentFeature']);
            $mockOrgPermissionSetService->method('getStudentFeature')->willReturn([]);

            // Mock PersonService
            $mockPersonService = $this->getMock('PersonService', ['getFirstPrimaryCoordinatorPersonId', 'getFirstPrimaryCoordinatorPerson']);
            $mockPersonService->method('getFirstPrimaryCoordinatorPersonId')->willReturn(123);
            $mockPersonService->method('getFirstPrimaryCoordinatorPerson')->willReturn($mockPerson);

            // Mock MapworksActionService
            $mockMapworksActionService = $this->getMock('MapworksActionService', ['getTokenVariablesFromPerson', 'sendCommunicationBasedOnMapworksAction']);
            $mockMapworksActionService->method('getTokenVariablesFromPerson')->willReturn(
                [
                    '$$Skyfactor_Mapworks_logo$$' => 'http://www.mapworks-integration.skyfactor.com/images/Skyfactor-Mapworks-login.png',
                    '$$date_of_creation$$' => $currentDateTime,
                    '$$staff_referralpage$$' => 'http://www.mapworks-integration.skyfactor.com/staff-referral-page',
                    'interested_parties' => [['$$interested_party_id$$' => 123]],
                ]
            );
            $mockMapworksActionService->method('sendCommunicationBasedOnMapworksAction')->willReturn(true);

            // Mock DateUtilityService
            $mockDateUtilityService = $this->getMock('DateUtilityService', ['getFormattedDateTimeForOrganization']);
            $mockDateUtilityService->method('getFormattedDateTimeForOrganization')->willReturn('20/06/2017');

            // Mock EbiConfigService
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl', 'generateCompleteUrl']);
            $mockEbiConfigService->method('getSystemUrl')->willReturn('mapworks-integration.skyfactor.com');
            $mockEbiConfigService->method('generateCompleteUrl')->willReturn('http://www.mapworks-integration.skyfactor.com');

            // Mock OrgPersonStudentRepository
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockPerson);

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['getUserCoordinatorRole']);
            $mockOrganizationRoleRepository->method('getUserCoordinatorRole')->willReturn(true);

            // Mock Teams
            $mockTeams = $this->getMock('Teams', ['getId']);
            $mockTeams->method('getId')->willReturn(111);

            // Mock ReferralsTeams
            $mockReferralsTeams = $this->getMock('ReferralsTeams', ['getId', 'getTeams']);
            $mockReferralsTeams->method('getTeams')->willReturn($mockTeams);

            // Mock ReferralsTeamsRepository
            $mockReferralsTeamsRepository = $this->getMock('ReferralsTeamsRepository', ['findBy', 'removeReferralsTeam']);
            $mockReferralsTeamsRepository->method('findBy')->willReturn([$mockReferralsTeams]);

            // Mock ReferralHistoryRepository
            $mockReferralHistoryRepository = $this->getMock('ReferralHistoryRepository', ['persist']);

            // Mock LanguageMaster
            $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);

            // Mock OrganizationLang
            $mockOrganizationLang = $this->getMock('OrganizationLang', ['getId', 'getLang']);
            $mockOrganizationLang->method('getLang')->willReturn($mockLanguageMaster);

            // Mock OrganizationService
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrganizationDetailsLang']);
            $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLang);

            // Mock ActivityCategoryLang
            $mockActivityCategoryLang = $this->getMock('ActivityCategoryLang', ['getId', 'getDescription']);

            //Mock ActivityCategoryLangRepository
            $mockActivityCategoryLangRepository = $this->getMock('ActivityCategoryLangRepository', ['findOneBy']);
            $mockActivityCategoryLangRepository->method('findOneBy')->willReturn($mockActivityCategoryLang);


            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ReferralsTeamsRepository::REPOSITORY_KEY, $mockReferralsTeamsRepository],
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                    [ReferralRepository::REPOSITORY_KEY, $mockReferralRepository],
                    [ReferralsInterestedPartiesRepository::REPOSITORY_KEY, $mockReferralsInterestedPartiesRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [ReferralRoutingRulesRepository::REPOSITORY_KEY, $mockReferralRoutingRulesRepository],
                    [OrgPersonStudentRepository::REPOSITORY_KEY, $mockOrgPersonStudentRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [ReferralHistoryRepository::REPOSITORY_KEY, $mockReferralHistoryRepository],
                    [ActivityCategoryLangRepository::REPOSITORY_KEY, $mockActivityCategoryLangRepository],
                ]);

            $this->mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::SECURITY_CONTEXT_CLASS_KEY, $mockSecurityContext],
                    [SynapseConstant::TINYRBAC_MANAGER, $mockRbacManager],
                    [OrgPermissionsetService::SERVICE_KEY, $mockOrgPermissionSetService],
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [MapworksActionService::SERVICE_KEY, $mockMapworksActionService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService],
                ]);

            try {
                $referralService = new ReferralService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
                $result = $referralService->editReferral($this->getReferralDtoDetails($referralDtoDetailsArray), $loggedInPersonId);
                $this->assertInstanceOf('Synapse\CoreBundle\Entity\Referrals', $result);
                $this->assertEquals($expectedResult, $result);
            } catch (SynapseException $e) {
                $this->assertEquals($exceptionMessage, $e->getMessage());

                // Adding assertion for SynapseValidationException
                if ($referralDtoDetailsArray['referral_id'] == -1) {
                    $this->assertEquals('Synapse\CoreBundle\Exception\SynapseValidationException', get_class($e));
                }

                // Adding assertion for AccessDeniedException
                if (!$studentAccessPermission) {
                    $this->assertEquals('Synapse\CoreBundle\Exception\AccessDeniedException', get_class($e));
                }

            }

        },
            ['examples' =>
                [
                    // Invalid referral id , throw SynapseValidationException
                    [
                        [
                            'referral_id' => -1,
                            'assigned_to_user_id' => 123,
                            'interested_parties' => [],
                            'comment' => 'Comment has been updated!',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        203,
                        true,
                        'Referral not found',
                        ''
                    ],

                    // Updated an assigned_to_user_id while editing the referral
                    [
                        [
                            'referral_id' => 1,
                            'assigned_to_user_id' => 124,
                            'interested_parties' => [],
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        203,
                        true,
                        '',
                        $this->getReferralResponse(
                            [
                                'referral_id' => 1,
                                'assigned_to_user_id' => 124,
                                'interested_parties' => [],
                                'comment' => 'This is test',
                                'issue_discussed_with_student' => true,
                                'high_priority_concern' => true,
                                'issue_revealed_to_student' => true,
                                'student_indicated_to_leave' => true,
                                'notify_student' => false,
                                'share_options' => [
                                    'private_share' => false,
                                    'public_share' => true,
                                    'teams_share' => false
                                ]
                            ], 'coordinator')
                    ],
                    // If coordinator has not permission to access the students , AccessDeniedException will be thrown while editing the referral
                    [
                        [
                            'referral_id' => 1,
                            'assigned_to_user_id' => 123,
                            'interested_parties' => [],
                            'comment' => 'This is test',
                            'issue_discussed_with_student' => true,
                            'high_priority_concern' => true,
                            'issue_revealed_to_student' => true,
                            'student_indicated_to_leave' => true,
                            'notify_student' => false,
                            'share_options' => [
                                'private_share' => false,
                                'public_share' => true,
                                'teams_share' => false
                            ]
                        ],
                        203,
                        false,
                        "Access denied to editing student's referral",
                        ''
                    ],
                ]
            ]);
    }

    private function getReferralInstance($personObject)
    {

        $referral = new Referrals();
        $organization = new Organization();
        $activityCategory = new ActivityCategory();
        $referral->setOrganization($organization);
        $referral->setPersonStudent($personObject);
        $referral->setPersonFaculty($personObject);
        $referral->setActivityCategory($activityCategory);
        return $referral;
    }

    private function getReferralResponse($referralDtoDetailsArray, $userType)
    {

        $referral = new Referrals();
        $referral->setNote($referralDtoDetailsArray['comment']);
        $referral->setIsDiscussed($referralDtoDetailsArray['issue_discussed_with_student']);
        $referral->setIsHighPriority($referralDtoDetailsArray['high_priority_concern']);
        $referral->setReferrerPermission($referralDtoDetailsArray['issue_revealed_to_student']);
        $referral->setIsLeaving($referralDtoDetailsArray['student_indicated_to_leave']);
        $referral->setNotifyStudent($referralDtoDetailsArray['notify_student']);
        $referral->setAccessPrivate($referralDtoDetailsArray['share_options']['private_share']);
        $referral->setAccessPublic($referralDtoDetailsArray['share_options']['public_share']);
        $referral->setAccessTeam($referralDtoDetailsArray['share_options']['teams_share']);
        $referral->setStatus('');
        $person = new Person();
        $person->setId(2);
        $person->setUsername($userType . '_email_address');
        $person->setFirstname($userType . '_first_name');
        $person->setLastname($userType . '_last_name');
        $person->setTitle($userType . '_title');
        $referral->setPersonFaculty($person);
        $referral->setPersonStudent($person);
        $organization = new Organization();
        $referral->setOrganization($organization);
        if (isset($referralDtoDetailsArray['assigned_to_user_id'])) {
            $referral->setPersonAssignedTo($person);
        }
        $referral->setActivityCategory(new ActivityCategory());
        $referral->setIsReasonRouted(0);
        $referral->setModifiedBy($person);
        return $referral;
    }
}