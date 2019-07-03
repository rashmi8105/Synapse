<?php
use Synapse\RestBundle\Entity\TeamMembersActivitiesDto;
use Synapse\RestBundle\Entity\TeamsDto;
use Synapse\RestBundle\Entity\TeamActivityCountDto;
use Synapse\CoreBundle\Service\Impl\TeamActivityService;

class TeamsActivityServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testGetMyTeamsActivitiesCount()
    {
        $this->specify("Get My Team Activities Detail", function ($teamIds, $teamNamesById, $datePeriod, $startDate, $endDate, $teamActivitiesCountLogin, $teamActivitiesCountInteraction, $teamActivitiesCountReferrals) {

            //Data
            $personId = 1;
            $organizationId = 1;
            $dateArray = [];
            $dateArray['from_date'] = $startDate;
            $dateArray['to_date'] = $endDate;
            $orgAcademicYear = [];
            $orgAcademicYear[0]['startDate'] = $startDate;
            $orgAcademicYear[0]['endDate'] = $endDate;
            $pageNumber = null;
            $recordsPerPage = null;
            $sortBy = null;

            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            //Mock Repositories/Services
            $mockOrgPermissionSetFeaturesRepository = $this->getMock('orgPermissionSetFeaturesRepository', ['getFeaturePermissions']);
            $mockActivityService = $this->getMock('activityService', ['verifyAccessToActivity', 'getActivityFeatureName', 'getActivityType']);
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['buildDateTimeRangeByTimePeriodAndDateTimeObject', 'getTimezoneAdjustedCurrentDateTimeForOrganization', 'getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString']);
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getMyTeams', 'getActivityDetailsOfMyTeam', 'getActivityCountsOfMyTeamByActivityType']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicDetails']);
            $mockFeatureMasterLangRepository = $this->getMock('featureMasterLangRepository', ['findOneBy']);
            $mockStudentService = $this->getMock('studentService', ['isStudentActive']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrgTimeZone']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);

            //Mock Objects
            $mockPersonObject = $this->getMock('loggedPerson', ['getOrganization']);
            $mockOrganizationObject = $this->getMock('personOrganization', ['getId']);
            $mockDateTimeObject = new \DateTime('now');

            //Retrieve Services and Repositories
            $mockContainer->method('get')->willReturnMap([
                ["org_service", $mockOrganizationService],
                ["person_service", $mockPersonService],
                ['date_utility_service', $mockDateUtilityService],
                ['activity_service', $mockActivityService],
                ['student_service', $mockStudentService]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ["SynapseCoreBundle:TeamMembers", $mockTeamMembersRepository],
                ["SynapseCoreBundle:OrgPersonStudent", $mockOrgPersonStudentRepository],
                ["SynapseAcademicBundle:OrgAcademicYear", $mockOrgAcademicYearRepository],
                ['SynapseCoreBundle:FeatureMasterLang', $mockFeatureMasterLangRepository],
                ['SynapseCoreBundle:OrgPermissionsetFeatures', $mockOrgPermissionSetFeaturesRepository]
            ]);

            $mockPersonService->method('findPerson')->willReturn($mockPersonObject);
            $mockPersonObject->method('getOrganization')->willReturn($mockOrganizationObject);
            $mockOrganizationObject->method('getId')->willReturn($organizationId);

            $mockDateUtilityService->method('getTimezoneAdjustedCurrentDateTimeForOrganization')->willReturn($mockDateTimeObject);
            $mockDateUtilityService->method('buildDateTimeRangeByTimePeriodAndDateTimeObject')->willReturn($dateArray);
            $mockDateUtilityService->expects($this->at(0))->method('getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString')->willReturn($startDate);
            $mockDateUtilityService->expects($this->at(1))->method('getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString')->willReturn($endDate);


            $mockOrgAcademicYearRepository->method('getCurrentAcademicDetails')->willReturn(null);

            $mockTeamMembersRepository->expects($this->at(0))->method('getActivityCountsOfMyTeamByActivityType')->with($this->equalTo('open-referral'))->willReturn($teamActivitiesCountReferrals);
            $mockTeamMembersRepository->expects($this->at(1))->method('getActivityCountsOfMyTeamByActivityType')->with($this->equalTo('interaction'))->willReturn($teamActivitiesCountInteraction);
            $mockTeamMembersRepository->expects($this->at(2))->method('getActivityCountsOfMyTeamByActivityType')->with($this->equalTo('login'))->willReturn($teamActivitiesCountLogin);


            $teamsDto = new TeamsDto();
            $teamsDto->setPersonId($personId);
            $recentActivitiesDtoArray = [];
            foreach ($teamIds as $teamId) {
                $recentActivities = new TeamActivityCountDto();
                $recentActivities->setTeamId($teamId);
                $recentActivities->setTeamName($teamNamesById[$teamId]);
                $recentActivities->setTeamOpenReferrals($teamActivitiesCountReferrals[0]['team_activities_count']);
                $recentActivities->setTeamActivities($teamActivitiesCountInteraction[0]['team_activities_count']);
                $recentActivities->setTeamLogins($teamActivitiesCountLogin[0]['team_activities_count']);
                $recentActivitiesDtoArray[] = $recentActivities;

            }

            $teamsDto->setRecentActivities($recentActivitiesDtoArray);

            $teamActivityService = new TeamActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $teamActivityService->getActivityCountsOfMyTeam($organizationId, $personId, $datePeriod);

            $this->assertEquals($teamsDto, $result);

        }, ['examples' => [
            //Single Team
            [
                [1], [1 => 'Joe'], 'today', '2016-01-01 00:00:00', '2016-01-01 00:00:00',
                [0 => [
                    'team_id' => 1,
                    'team_name' => 'Joe',
                    'team_activities_count' => 10,
                    'activity' => 'login'
                ]],
                [0 => [
                    'team_id' => 1,
                    'team_name' => 'Joe',
                    'team_activities_count' => 10,
                    'activity' => 'interaction'
                ]],
                [0 => [
                    'team_id' => 1,
                    'team_name' => 'Joe',
                    'team_activities_count' => 10,
                    'activity' => 'open-referral'
                ]]
            ],
            //Double Team
            [
                [1, 2], [1 => 'Joe', 2 => 'Ted'], 'today', '2016-01-01 00:00:00', '2016-01-01 00:00:00',
                [
                    0 => [
                        'team_id' => 1,
                        'team_name' => 'Joe',
                        'team_activities_count' => 5,
                        'activity' => 'login'
                    ],
                    1 => [
                        'team_id' => 2,
                        'team_name' => 'Ted',
                        'team_activities_count' => 5,
                        'activity' => 'login'
                    ]
                ],
                [0 => [
                    'team_id' => 1,
                    'team_name' => 'Joe',
                    'team_activities_count' => 5,
                    'activity' => 'interaction'
                ],
                    1 => [
                        'team_id' => 2,
                        'team_name' => 'Ted',
                        'team_activities_count' => 5,
                        'activity' => 'interaction'
                    ]
                ],
                [0 => [
                    'team_id' => 1,
                    'team_name' => 'Joe',
                    'team_activities_count' => 5,
                    'activity' => 'open-referral'
                ], 1 => [
                    'team_id' => 2,
                    'team_name' => 'Ted',
                    'team_activities_count' => 5,
                    'activity' => 'open-referral'
                ]]
            ]
        ]]);
    }


    public function testGetActivityDetailsOfMyTeam()
    {
        $this->specify("Get My Team Activities Detail", function ($activityType, $timePeriod, $startDate, $endDate, $accessToActivity, $activityCodes, $teamActivities, $teamActivitiesCount, $expectedResult) {

            //Data
            $personId = 1;
            $teamId = 1;
            $teamMemberIdsString = '1, 2';
            $organizationId = 1;
            $personTeams = [0 => ['team_id' => 1]];
            $dateArray = [];
            $dateArray['from_date'] = $startDate;
            $dateArray['to_date'] = $endDate;
            $orgAcademicYear = [];
            $orgAcademicYear[0]['startDate'] = $startDate;
            $orgAcademicYear[0]['endDate'] = $endDate;
            $pageNumber = null;
            $recordsPerPage = null;
            $sortBy = null;

            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            //Mock Repositories/Services
            $mockOrgPermissionSetFeaturesRepository = $this->getMock('orgPermissionSetFeaturesRepository', ['getFeaturePermissions']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrgTimeZone']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockActivityService = $this->getMock('activityService', ['verifyAccessToActivity', 'getActivityFeatureName', 'getActivityType', 'getActivityId']);
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['buildDateTimeRangeByTimePeriodAndDateTimeObject', 'getTimezoneAdjustedCurrentDateTimeForOrganization', 'getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString', 'convertToUtcDatetime']);
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getMyTeams', 'getActivityDetailsOfMyTeam', 'getActivityCountsOfMyTeamByActivityType']);
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            $mockOrgAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', ['getCurrentAcademicDetails']);
            $mockFeatureMasterLangRepository = $this->getMock('featureMasterLangRepository', ['findOneBy']);
            $mockStudentService = $this->getMock('studentService', ['isStudentActive']);

            //Mock Objects
            $mockPersonObject = $this->getMock('loggedPerson', ['getOrganization']);
            $mockOrganizationObject = $this->getMock('personOrganization', ['getId']);
            $mockFeatureObject = $this->getMock('feature', ['getId']);
            $mockStudentObject = $this->getMock('studentObject', ['getStatus']);

            //Retrieve Services and Repositories
            $mockContainer->method('get')->willReturnMap([
                ["org_service", $mockOrganizationService],
                ["person_service", $mockPersonService],
                ['date_utility_service', $mockDateUtilityService],
                ['activity_service', $mockActivityService],
                ['student_service', $mockStudentService]
            ]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ["SynapseCoreBundle:TeamMembers", $mockTeamMembersRepository],
                ["SynapseCoreBundle:OrgPersonStudent", $mockOrgPersonStudentRepository],
                ["SynapseAcademicBundle:OrgAcademicYear", $mockOrgAcademicYearRepository],
                ['SynapseCoreBundle:FeatureMasterLang', $mockFeatureMasterLangRepository],
                ['SynapseCoreBundle:OrgPermissionsetFeatures', $mockOrgPermissionSetFeaturesRepository]
            ]);

            $mockPersonService->method('findPerson')->willReturn($mockPersonObject);
            $mockPersonObject->method('getOrganization')->willReturn($mockOrganizationObject);
            $mockOrganizationObject->method('getId')->willReturn($organizationId);
            $dateTimeObject = new \DateTime('now');

            $mockDateUtilityService->method('getTimezoneAdjustedCurrentDateTimeForOrganization')->willReturn($dateTimeObject);


            $mockTeamMembersRepository->method('getMyTeams')->willReturn($personTeams);

            if ($timePeriod === 'custom') {
                $mockDateUtilityService->expects($this->at(0))->method('convertToUtcDatetime')->willReturn($startDate);
                $mockDateUtilityService->expects($this->at(1))->method('convertToUtcDatetime')->willReturn($endDate);
            } else {
                $mockDateUtilityService->method('buildDateTimeRangeByTimePeriodAndDateTimeObject')->willReturn($dateArray);
                $mockDateUtilityService->expects($this->at(0))->method('getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString')->willReturn($startDate);
                $mockDateUtilityService->expects($this->at(1))->method('getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString')->willReturn($endDate);
            }


            $mockOrgAcademicYearRepository->method('getCurrentAcademicDetails')->willReturn(null);

            $mockTeamMembersRepository->method('getActivityDetailsOfMyTeam')->willReturn($teamActivities);
            $mockTeamMembersRepository->method('getActivityCountsOfMyTeamByActivityType')->willReturn($teamActivitiesCount);

            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockStudentObject);

            if ($activityType == 'login') {
                $mockStudentObject->method('getStatus')->willReturn(null);
            } else {
                $mockStudentObject->method('getStatus')->willReturn(1);
            }


            $mockActivityService->method('getActivityFeatureName')->willReturn(['Referral']);

            $mockFeatureMasterLangRepository->method('findOneBy')->willReturn($mockFeatureObject);
            $mockFeatureObject->method('getId')->willReturn('');
            $mockOrgPermissionSetFeaturesRepository->method('getFeaturePermissions')->willReturn('');

            $mockActivityService->method('verifyAccessToActivity')->willReturn($accessToActivity);

            $mockActivityService->method('getActivityId')->willReturn(1);

            $mockActivityService->method('getActivityType')->willReturn($activityType);

            $mockLogger->method('info')->willReturn('');

            $teamsDto = new TeamsDto();
            $teamsDto->setPersonId($expectedResult['personId']);
            $teamsDto->setTeamId($expectedResult['teamId']);
            $teamsDto->setTeamMemberIds($expectedResult['teamMemberIdsString']);

            $activityTypeFormatted = strtolower($activityType);
            if ($activityTypeFormatted == "open-referral") {
                $activityTypeFormatted = "Open Referral";
            }

            $teamsDto->setActivityType($activityTypeFormatted);
            $teamsDto->setFilter($expectedResult['datePeriod']);
            $teamsDto->setTotalRecords($expectedResult['totalCount']);
            $teamsDto->setTotalPages($expectedResult['totalPageCount']);
            $teamsDto->setRecordsPerPage($expectedResult['recordsPerPage']);
            $teamsDto->setCurrentPage($expectedResult['pageNumber']);

            $teamMembersActivitiesDto = new TeamMembersActivitiesDto();
            $teamMembersActivitiesDto->setDate($expectedResult['date']);
            $teamMembersActivitiesDto->setTeamMemberId($expectedResult['team_member_id']);
            $teamMembersActivitiesDto->setTeamMemberFirstName($expectedResult['team_member_firstname']);
            $teamMembersActivitiesDto->setTeamMemberLastName($expectedResult['team_member_lastname']);
            $teamMembersActivitiesDto->setTeamMemberEmailId($expectedResult['primary_email']);
            $teamMembersActivitiesDto->setTeamMemberExternalId($expectedResult['team_member_external_id']);
            if (!in_array('L', $activityCodes)) {
                $teamMembersActivitiesDto->setStudentId($expectedResult['student_id']);
                $teamMembersActivitiesDto->setStudentExternalId($expectedResult['student_external_id']);
                $teamMembersActivitiesDto->setStudentEmail($expectedResult['student_email']);
                $teamMembersActivitiesDto->setStudentFirstName($expectedResult['student_firstname']);
                $teamMembersActivitiesDto->setStudentLastName($expectedResult['student_lastname']);
                $teamMembersActivitiesDto->setStudentStatus(1);
            } else {
                $teamMembersActivitiesDto->setStudentFirstName('');
                $teamMembersActivitiesDto->setStudentLastName('');
            }
            $teamMembersActivitiesDto->setReasonText($expectedResult['reason_text']);
            $teamMembersActivitiesDto->setActivityId($expectedResult['activity_id']);
            $teamMembersActivitiesDto->setActivityType($expectedResult['activity_type']);

            $teamMemberActivitiesDtoArray = [$teamMembersActivitiesDto];

            $teamsDto->setTeamMembersActivities($teamMemberActivitiesDtoArray);

            $teamActivityService = new TeamActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $teamActivityService->getActivityDetailsOfMyTeam($organizationId, $personId, $teamId, $teamMemberIdsString, $activityType, $timePeriod, $startDate, $endDate, $pageNumber, $recordsPerPage, $sortBy);

            $this->assertEquals($teamsDto, $result);

        }, ['examples' => [
            //Open Referrals
            ['open-referral', 'today', '2016-01-01 00:00:00', '2016-01-01 00:00:00', true, ['R'],
                [0 => [
                    'status' => 'O',
                    'referrals_id' => 1,
                    'org_permissionset_ids' => '',
                    'activity_date' => '2016-01-01 00:00:00',
                    'team_member_id' => 2,
                    'team_member_firstname' => 'John',
                    'team_member_lastname' => 'Smith',
                    'primary_email' => 'sample@mailinator.com',
                    'team_member_external_id' => '34',
                    'activity_code' => 'R',
                    'student_id' => 3,
                    'student_external_id' => '10',
                    'student_email' => 'studetn@mailinator.com',
                    'student_status' => 1,
                    'student_firstname' => 'Donna',
                    'student_lastname' => 'Marsh',
                    'reason_text' => 'reason',
                    'activity_type' => 'open-referral'

                ]],
                [0 => [
                    'team_activities_count' => 10
                ]],
                [
                    'personId' => 1,
                    'teamId' => 1,
                    'teamMemberIdsString' => '1, 2',
                    'activity_type' => 'open referral',
                    'datePeriod' => 'today',
                    'totalCount' => 10,
                    'totalPageCount' => 1,
                    'recordsPerPage' => 25,
                    'pageNumber' => 1,
                    'date' => '2016-01-01T00:00:00+0000',
                    'team_member_id' => 2,
                    'team_member_firstname' => 'John',
                    'team_member_lastname' => 'Smith',
                    'primary_email' => 'sample@mailinator.com',
                    'team_member_external_id' => '34',
                    'student_id' => 3,
                    'student_external_id' => '10',
                    'student_email' => 'studetn@mailinator.com',
                    'student_status' => 1,
                    'student_firstname' => 'Donna',
                    'student_lastname' => 'Marsh',
                    'reason_text' => 'reason',
                    'activity_id' => 1
                ]
            ],
            //Interaction
            ['interaction', 'today', '2016-01-01 00:00:00', '2016-01-01 00:00:00', true, ['N', 'A', 'R', 'C'],
                [0 => [
                    'status' => null,
                    'note_id' => 1,
                    'org_permissionset_ids' => '',
                    'activity_date' => '2016-01-01 00:00:00',
                    'team_member_id' => 2,
                    'team_member_firstname' => 'John',
                    'team_member_lastname' => 'Smith',
                    'primary_email' => 'sample@mailinator.com',
                    'team_member_external_id' => '34',
                    'activity_code' => 'N',
                    'student_id' => 3,
                    'student_external_id' => '10',
                    'student_email' => 'studetn@mailinator.com',
                    'student_firstname' => 'Donna',
                    'student_lastname' => 'Marsh',
                    'student_status' => 1,
                    'reason_text' => 'reason',
                    'activity_type' => 'open-referral'

                ]],
                [0 => [
                    'team_activities_count' => 10
                ]],
                [
                    'personId' => 1,
                    'teamId' => 1,
                    'teamMemberIdsString' => '1, 2',
                    'activity_type' => 'note',
                    'datePeriod' => 'today',
                    'totalCount' => 10,
                    'totalPageCount' => 1,
                    'recordsPerPage' => 25,
                    'pageNumber' => 1,
                    'date' => '2016-01-01T00:00:00+0000',
                    'team_member_id' => 2,
                    'team_member_firstname' => 'John',
                    'team_member_lastname' => 'Smith',
                    'primary_email' => 'sample@mailinator.com',
                    'team_member_external_id' => '34',
                    'student_id' => 3,
                    'student_external_id' => '10',
                    'student_email' => 'studetn@mailinator.com',
                    'student_firstname' => 'Donna',
                    'student_lastname' => 'Marsh',
                    'student_status' => 1,
                    'reason_text' => 'reason',
                    'activity_id' => 1
                ]
            ],
            //Login
            ['login', 'today', '2016-01-01 00:00:00', '2016-01-01 00:00:00', true, ['L'],
                [0 => [
                    'status' => null,
                    'org_permissionset_ids' => '',
                    'activity_date' => '2016-01-01 00:00:00',
                    'team_member_id' => 2,
                    'team_member_firstname' => 'John',
                    'team_member_lastname' => 'Smith',
                    'primary_email' => 'sample@mailinator.com',
                    'team_member_external_id' => '34',
                    'activity_code' => 'L',
                    'student_id' => null,
                    'student_external_id' => null,
                    'student_email' => null,
                    'student_firstname' => '',
                    'student_lastname' => '',
                    'student_status' => null,
                    'reason_text' => null,
                    'activity_type' => 'login'

                ]],
                [0 => [
                    'team_activities_count' => 10
                ]],
                [
                    'personId' => 1,
                    'teamId' => 1,
                    'teamMemberIdsString' => '1, 2',
                    'activity_type' => 'login',
                    'datePeriod' => 'today',
                    'totalCount' => 10,
                    'totalPageCount' => 1,
                    'recordsPerPage' => 25,
                    'pageNumber' => 1,
                    'date' => '2016-01-01T00:00:00+0000',
                    'team_member_id' => 2,
                    'team_member_firstname' => 'John',
                    'team_member_lastname' => 'Smith',
                    'primary_email' => 'sample@mailinator.com',
                    'team_member_external_id' => '34',
                    'student_firstname' => '',
                    'student_lastname' => '',
                    'student_id' => null,
                    'student_external_id' => null,
                    'student_email' => null,
                    'student_status' => null,
                    'reason_text' => null,
                    'activity_id' => 1
                ]
            ]


        ]]);
    }


    public function testMyTeamActivitiesValidation()
    {
        $this->specify("Get My Team Activities Validation", function ($expectException, $timePeriod, $startDate, $endDate, $teamMemberIds, $expectedExceptionMessage) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);


            try {
                $teamActivityService = new TeamActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $teamActivityService->validateTeamActivityDatesAndTeamMembers($timePeriod, $startDate, $endDate, $teamMemberIds);

            } catch (\Synapse\CoreBundle\Exception\SynapseValidationException $e) {

                if (!$expectException) {
                    $this->fail('ValidationException Should Not have Been Thrown With Valid Data');
                } else {
                    $this->assertEquals($expectedExceptionMessage, $e->getMessage());
                }

            }

        }, ['examples' => [
            //throw ValidationException Start Date Cannot be Greater than End Date
            [true, 'custom', '2016-02-01', '2016-01-01', [333], 'Start date cannot be greater than end date'],
            //throw ValidationException Start Date and end date are mandatory
            [true, 'custom', '', '', [333], 'Start date and end date are not valid.  This filter is mandatory for custom filter'],
            //throw ValidationException for not valid Start Date
            [true, 'custom', '', '2016-01-01', [333], 'Start date and end date are not valid.  This filter is mandatory for custom filter'],
            //throw ValidationException for not valid End Date
            [true, 'custom', '2016-01-01', '', [333], 'Start date and end date are not valid.  This filter is mandatory for custom filter'],
            //throw ValidationException Team Member ids Cannot be empty
            [true, 'custom', '2016-01-01', '2016-02-01', [], 'Team member ids filter cannot be empty'],
            //Do Not throw Exception, all data correct
            [false, 'custom', '2016-01-01', '2016-02-01', [333], ''],
            //Time Period is Not Custom, Team Ids correct, Do not throw errors on anything
            [false, 'week', '', '', [333], '']
        ]]);
    }


    public function testGetActivityCodesForTeamActivities()
    {
        $this->specify("Testing mapping of Activity to Activity Codes", function ($activityType, $expectedFeatureName) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $teamActivityService = new TeamActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $teamActivityService->getActivityCodesForTeamActivities($activityType);

            $this->assertEquals($expectedFeatureName, $result);

        }, ['examples' => [
            ['open-referral', ['R']],
            ['interaction', ['A', 'C', 'N', 'R']],
            ['login', ['L']],
            ['All', ['A', 'C', 'E', 'L', 'N', 'R']]
        ]]);
    }

}