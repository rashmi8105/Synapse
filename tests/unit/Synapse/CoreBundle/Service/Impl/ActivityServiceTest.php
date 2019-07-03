<?php


use Synapse\CoreBundle\Service\Impl\ActivityService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;

class ActivityServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    private $featuresArray = [
        'Referrals',
        'Referrals Reason Routed',
        'Log Contacts',
        'Booking',
        'Notes',
        'Email'
    ];

    private $expectedAllPermissionArray = [
        'Referrals' => [
            'public_view' => 1,
            'team_view' => 1
        ],

        'Referrals Reason Routed' => [
            'public_view' => 1,
            'team_view' => 1
        ],

        'Notes' => [
            'public_view' => 1,
            'team_view' => 1
        ],

        'Log Contacts' => [
            'public_view' => 1,
            'team_view' => 1
        ],

        'Booking' => [
            'public_view' => 1,
            'team_view' => 1
        ],

        'Email' => [
            'public_view' => 1,
            'team_view' => 1
        ]
    ];

    private $userHavingAllPermissions = [
        'user_feature_permissions' => [
            [
                'referrals' => 1,
                'referrals_share' => [
                    'direct_referral' => [
                        'public_share' => [
                            'create' => 1,
                            'view' => 1
                        ],

                        'private_share' => [
                            'create' => 1,
                            'view' => 1
                        ],

                        'teams_share' => [
                            'create' => 1,
                            'view' => 1
                        ]

                    ],

                    'reason_routed_referral' => [
                        'public_share' => [
                            'create' => 1,
                            'view' => 1
                        ],

                        'private_share' => [
                            'create' => 1,
                            'view' => 1
                        ],

                        'teams_share' => [
                            'create' => 1,
                            'view' => 1
                        ]

                    ],

                    'receive_referrals' => 1
                ],

                'notes' => 1,
                'notes_share' => [
                    'public_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'private_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'teams_share' => [
                        'create' => 1,
                        'view' => 1
                    ]
                ],

                'log_contacts' => 1,
                'log_contacts_share' => [
                    'public_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'private_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'teams_share' => [
                        'create' => 1,
                        'view' => 1
                    ]

                ],

                'booking' => 1,
                'booking_share' => [
                    'public_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'private_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'teams_share' => [
                        'create' => 1,
                        'view' => 1
                    ]
                ],

                'email' => 1,
                'email_share' => [
                    'public_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'private_share' => [
                        'create' => 1,
                        'view' => 1
                    ],

                    'teams_share' => [
                        'create' => 1,
                        'view' => 1
                    ]
                ]
            ]
        ]
    ];

    private $studentFeaturePermission =
        [
            'referrals' => [
                'public_share' => [
                    'create' => 1,
                    'view' => 1
                ],

                'private_share' => [
                    'create' => 1,
                    'view' => 1
                ],

                'teams_share' => [
                    'create' => 1,
                    'view' => 1
                ]

            ],

            'referrals_reason_routed' => [
                'public_share' => [
                    'create' => 1,
                    'view' => 1
                ],
                'private_share' =>[
                    'create' => 1,
                    'view' => 1
                ],

                'teams_share' => [
                    'create' => 1,
                    'view' => 1
                ]

            ],

            'notes' => [
                'public_share' => [
                    'create' => 1,
                    'view' => 1
                ],

                'private_share' => [
                    'create' => 1,
                    'view' => 1
                ],

                'teams_share' => [
                    'create' => 1,
                    'view' => 1
                ]

            ],

            'log_contacts' => [
                'public_share' => [
                    'create' => 1,
                    'view' => 1
                ],

                'private_share' => [
                    'create' => 1,
                    'view' => 1
                ],

                'teams_share' => [
                    'create' => 1,
                    'view' => 1
                ],

            ],

            'booking' => [
                'public_share' => [
                    'create' => 1,
                    'view' => 1
                ],

                'private_share' => [
                    'create' => 1,
                    'view' => 1
                ],

            'teams_share' => [
                    'create' => 1,
                    'view' => 1
                ]

        ],

    'email' => [
            'public_share' => [
                    'create' => 1,
                    'view' => 1
                ],

            'private_share' => [
                    'create' => 1,
                    'view' => 1
                ],

            'teams_share' => [
                    'create' => 1,
                    'view' => 1
                ]

        ]

];

    public function testVerifyAccessToActivity()
    {
        $this->specify("Testing whether the User has access to the activity", function ($isPrimaryCoordinator, $activity, $featureAccess, $personTeamIdsArray, $myTeamId, $personsWithAccessToActivity, $expectedBoolean) {

            $organizationId = 1;
            $loggedUserId = 1;
            $facultyId = $personsWithAccessToActivity[0];
            $secondFacultyId = $personsWithAccessToActivity[1];
            $thirdFacultyId = $personsWithAccessToActivity[2];

            //For Making Testing Easier, making the feature set match the activity type of the activity
            $isActivityPublic = $featureAccess['public_view'] || $featureAccess['reason_referrals_public_view'];
            $isActivityTeam = $featureAccess['team_view'] || $featureAccess['reason_referrals_teams_view'];
            $isActivityReasonRoutedTeam = $featureAccess['reason_referrals_teams_view'];
            $isActivityReasonRoutedPublic = $featureAccess['reason_referrals_public_view'];
            $isReasonRouted = $isActivityReasonRoutedTeam || $isActivityReasonRoutedPublic;


            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockRbacManager = $this->getMock('rbacManager', []);

            //Mock Repostories/Services
            $mockReferralRepository = $this->getMock('referralRepository', ['find']);
            $mockContactRepository = $this->getMock('contactsRepository', ['find']);
            $mockNoteRepository = $this->getMock('noteRepository', ['find']);
            $mockAppointmentsRepository = $this->getMock('appointmentsRepository', ['find']);
            $mockAppointmentsTeamsRepository = $this->getMock('appointmentsTeamsRepository', ['findBy']);
            $mockContactsTeamsRepository = $this->getMock('contactsTeamsRepository', ['findBy']);
            $mockNoteTeamsRepository = $this->getMock('noteTeamsRepository', ['findBy']);
            $mockReferralsTeamsRepository = $this->getMock('referralsTeamsRepository', ['findBy']);
            $mockReferralsInterestedPartiesRepository = $this->getMock('referralsInterestedPartiesRepository', ['findBy']);
            $mockReferralRoutingRulesRepository = $this->getMock('referralRoutingRulesRepository', ['findOneBy']);
            $mockOrganizationRoleRepository = $this->getMock('organizationRoleRepository', ['findFirstPrimaryCoordinatorIdAlphabetically']);

            //Mock Objects
            $mockActivityObject = $this->getMock('activityObject', ['getPersonIdFaculty', 'getPersonAssignedTo', 'getAccessPublic', 'getAccessTeam', 'getPersonIdProxy', 'getPersonId', 'getIsReasonRouted', 'getActivityCategoryId']);
            $mockInterestedPartiesObject = $this->getMock('interestedParty', ['getPerson']);
            $mockPersonObject = $this->getMock('personObject', ['getId']);
            $mockTeamsBelongingToActivity = $this->getMock('team', ['getTeams']);
            $mockIndividualTeamObject = $this->getMock('team', ['getId']);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                ["SynapseCoreBundle:Referrals", $mockReferralRepository],
                ["SynapseCoreBundle:Contacts", $mockContactRepository],
                ["SynapseCoreBundle:Note", $mockNoteRepository],
                ['SynapseCoreBundle:Appointments', $mockAppointmentsRepository],
                ['SynapseCoreBundle:ContactsTeams', $mockContactsTeamsRepository],
                ['SynapseCoreBundle:AppointmentsTeams', $mockAppointmentsTeamsRepository],
                ['SynapseCoreBundle:NoteTeams', $mockNoteTeamsRepository],
                ['SynapseCoreBundle:ReferralsTeams', $mockReferralsTeamsRepository],
                ['SynapseCoreBundle:ReferralsInterestedParties', $mockReferralsInterestedPartiesRepository],
                ['SynapseCoreBundle:ReferralRoutingRules', $mockReferralRoutingRulesRepository],
                ['SynapseCoreBundle:OrganizationRole', $mockOrganizationRoleRepository]

            ]);

            if (isset($activity['referrals_id'])) {

                $mockReferralRepository->method('find')->willReturn($mockActivityObject);
                $mockReferralsInterestedPartiesRepository->method('findBy')->willReturn([$mockInterestedPartiesObject]);
                $mockActivityObject->method('getPersonIdFaculty')->willReturn($mockPersonObject);
                $mockPersonObject->method('getId')->willReturn($facultyId);

                if ($secondFacultyId === null || $isPrimaryCoordinator) {
                    $mockActivityObject->method('getPersonAssignedTo')->willReturn(null);
                    $mockOrganizationRoleRepository->method('findFirstPrimaryCoordinatorIdAlphabetically')->willReturn($thirdFacultyId);
                } else {
                    $mockActivityObject->method('getPersonAssignedTo')->willReturn($mockPersonObject);
                    $mockPersonObject->method('getId')->willReturn($secondFacultyId);
                }

                $mockInterestedPartiesObject->method('getPerson')->willReturn($mockPersonObject);
                $mockPersonObject->method('getId')->willReturn($thirdFacultyId);

                $mockActivityObject->method('getAccessPublic')->willReturn($isActivityPublic);
                $mockActivityObject->method('getAccessTeam')->willReturn($isActivityTeam);
                $mockActivityObject->method('getIsReasonRouted')->willReturn($isReasonRouted);


                if ($personTeamIdsArray) {
                    $mockReferralsTeamsRepository->method('findBy')->willReturn([$mockTeamsBelongingToActivity]);
                    $mockTeamsBelongingToActivity->method('getTeams')->willReturn($mockIndividualTeamObject);
                    $mockIndividualTeamObject->method('getId')->willReturn($myTeamId);
                }
            } elseif (isset($activity['note_id'])) {
                $mockNoteRepository->method('find')->willReturn($mockActivityObject);
                $mockActivityObject->method('getPersonIdFaculty')->willReturn($mockPersonObject);
                $mockPersonObject->method('getId')->willReturn($facultyId);
                $mockActivityObject->method('getAccessPublic')->willReturn($isActivityPublic);
                $mockActivityObject->method('getAccessTeam')->willReturn($isActivityTeam);

                if ($personTeamIdsArray) {
                    $mockNoteTeamsRepository->method('findBy')->willReturn([$mockTeamsBelongingToActivity]);
                    $mockTeamsBelongingToActivity->method('getTeams')->willReturn($mockIndividualTeamObject);
                    $mockIndividualTeamObject->method('getId')->willReturn($myTeamId);
                }
            } elseif (isset($activity['contacts_id'])) {
                $mockContactRepository->method('find')->willReturn($mockActivityObject);
                $mockActivityObject->method('getPersonIdFaculty')->willReturn($mockPersonObject);
                $mockPersonObject->method('getId')->willReturn($facultyId);
                $mockActivityObject->method('getAccessPublic')->willReturn($isActivityPublic);
                $mockActivityObject->method('getAccessTeam')->willReturn($isActivityTeam);

                if ($personTeamIdsArray) {
                    $mockContactsTeamsRepository->method('findBy')->willReturn([$mockTeamsBelongingToActivity]);
                    $mockTeamsBelongingToActivity->method('getTeams')->willReturn($mockIndividualTeamObject);
                    $mockIndividualTeamObject->method('getId')->willReturn($myTeamId);
                }

            } elseif (isset($activity['appointments_id'])) {
                $mockAppointmentsRepository->method('find')->willReturn($mockActivityObject);
                $mockActivityObject->method('getPersonIdFaculty')->willReturn($mockPersonObject);
                $mockPersonObject->method('getId')->willReturn($facultyId);
                $mockActivityObject->method('getAccessPublic')->willReturn($isActivityPublic);
                $mockActivityObject->method('getAccessTeam')->willReturn($isActivityTeam);

                if ($personTeamIdsArray) {
                    $mockAppointmentsTeamsRepository->method('findBy')->willReturn([$mockTeamsBelongingToActivity]);
                    $mockTeamsBelongingToActivity->method('getTeams')->willReturn($mockIndividualTeamObject);
                    $mockIndividualTeamObject->method('getId')->willReturn($myTeamId);
                }
            }

            $activityService = new ActivityService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockRbacManager);
            $result = $activityService->verifyAccessToActivity($organizationId, $loggedUserId, $activity, $featureAccess, $personTeamIdsArray);

            $this->assertEquals($expectedBoolean, $result);

        }, ['examples' => [
            //Referral Public Allowed
            [false, ['referrals_id' => 1, 'note_id' => 1], ['public_view' => 1, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], true],
            //Referral Private Allowed
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [1, 2, 3], true],
            //Referral Team Allowed
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [555], 555, [3, 5, 6], true],


            //Referral Reason Routed Team, Reason Routed Not Coordinator
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 1], [555], 555, [0, 0, 0], true],
            //Referral Reason Routed Public, Reason Routed Not Coordinator
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 1, 'reason_referrals_teams_view' => 0], [555], null, [0, 0, 0], true],


            //Referral Reason Routed Public, Reason Routed goes to Coordinator
            [true, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 1, 'reason_referrals_teams_view' => 0], [555], null, [null, null, 1], true],
            //Referral Assignee is null (allowed goes to Primary Coordinator)
            [true, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [555], null, [null, null, 1], true],
            //Referral Reason Routed Team (NOT ALLOWED)
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [555], null, [null, null, null], false],


            //Referral Public Not Allowed
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Referral Private Not Allowed
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Referral Team Not Allowed
            [false, ['referrals_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [554], 555, [3, 5, 6], false],
            //Note Public Allowed
            [false, ['note_id' => 1], ['public_view' => 1, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], true],
            //Note Private Allowed
            [false, ['note_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [1, 2, 3], true],
            //Note Team Allowed
            [false, ['note_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [555], 555, [3, 5, 6], true],
            //Note Public Not Allowed
            [false, ['note_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Note Private Not Allowed
            [false, ['note_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Note Team Not Allowed
            [false, ['note_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [554], 555, [3, 5, 6], false],
            //Contact Public Allowed
            [false, ['contacts_id' => 1], ['public_view' => 1, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], true],
            //Contact Private Allowed
            [false, ['contacts_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [1, 2, 3], true],
            //Contact Team Allowed
            [false, ['contacts_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [555], 555, [3, 5, 6], true],
            //Contact Public Not Allowed
            [false, ['contacts_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Contact Private Not Allowed
            [false, ['contacts_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Contact Team Not Allowed
            [false, ['contacts_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [554], 555, [3, 5, 6], false],
            //Appointments Public Allowed
            [false, ['appointments_id' => 1], ['public_view' => 1, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], true],
            //Appointments Private Allowed
            [false, ['appointments_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [1, 2, 3], true],
            //Appointments Team Allowed
            [false, ['appointments_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [555], 555, [3, 5, 6], true],
            //Appointments Public Not Allowed
            [false, ['appointments_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Appointments Private Not Allowed
            [false, ['appointments_id' => 1], ['public_view' => 0, 'team_view' => 0, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], null, null, [4, 2, 3], false],
            //Appointments Team Not Allowed
            [false, ['appointments_id' => 1], ['public_view' => 0, 'team_view' => 1, 'reason_referrals_public_view' => 0, 'reason_referrals_teams_view' => 0], [554], 555, [3, 5, 6], false]
        ]]);
    }


    public function testGetAllowedPersonIdsFromActivityObject()
    {
        $this->specify("testing ability to get allowed PersonIds", function ($activityClass, $type, $expectedPersonIds) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockRbacManager = $this->getMock('rbacManager', []);

            $activityObject = new $activityClass();
            $personObject = new \Synapse\CoreBundle\Entity\Person();
            $personObject->setId(1);

            $activityObject->setPersonIdFaculty($personObject);

            if ($type === 'Referral') {
                $personObject2 = new \Synapse\CoreBundle\Entity\Person();
                $personObject2->setId(2);
                $activityObject->setPersonAssignedTo($personObject2);
            }


            $activityService = new ActivityService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockRbacManager);
            $personIds = $activityService->getAllowedPersonIdsFromActivityObject($activityObject, $type);

            $this->assertEquals($expectedPersonIds, $personIds);


        }, ['examples' => [
            //Appointment
            ['\Synapse\CoreBundle\Entity\Appointments', 'Appointment', [1]],
            //Contact
            ['\Synapse\CoreBundle\Entity\Contacts', 'Contact', [1]],
            //Note
            ['\Synapse\CoreBundle\Entity\Note', 'Note', [1]],
            //Referral
            ['\Synapse\CoreBundle\Entity\Referrals', 'Referral', [1, 2]]


        ]]);
    }

    public function testGetActivityId()
    {
        $this->specify("Get My Team Activities Validation", function ($activityCode, $activityTag, $expectedResponse) {
            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);


            $activity = [];
            $activity[$activityTag] = 1;
            $activity['activity_code'] = $activityCode;

            $teamService = new ActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $teamService->getActivityId($activity);

            $this->assertEquals($expectedResponse, $result);

        }, ['examples' => [
            //null gives null
            [null, 'appointments_id', null],
            //A gives back Id
            ['A', 'appointments_id', 1],
            //C gives back Id
            ['C', 'contacts_id', 1],
            //N gives back Id
            ['N', 'note_id', 1],
            //R gives back Id
            ['R', 'referrals_id', 1],
            //E gives back email Id
            ['E', 'email_id', 1],
            //L gives back nothing, no seperate id table
            ['L', null, null],
        ]]);
    }

    public function testGetSharingAccess()
    {
        $this->specify("Test get Sharing Access", function ($facultyId, $studentId, $permissionArray, $expectedResult) {


            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);


            $mockOrgPermissionSetService = $this->getMock("OrgPermissionsetService", ['getStudentFeatureOnly', 'getUserFeaturesPermission', 'formatNameUpperCase']);

             $mockOrgPermissionSetService->method('getStudentFeatureOnly')->willReturn($permissionArray);
            $mockOrgPermissionSetService->method('getUserFeaturesPermission')->willReturn($permissionArray);

            $mockOrgPermissionSetService->method('formatNameUpperCase')->willReturnCallback(function ($featureName) {
                $name = (strpos($featureName, '_')) ? ucwords(str_replace('_', ' ', $featureName)) : ucwords($featureName);
                return $name;
            });

            $mockContainer->method('get')->willReturnMap(
                [
                    [
                        OrgPermissionsetService::SERVICE_KEY,
                        $mockOrgPermissionSetService
                    ]
                ]

            );
            $activityService = new ActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $activityService->getSharingAccess($facultyId, $studentId);


            $this->assertEquals($result, $expectedResult);


        }, ['examples' => [
            // has all permissions  with student and faculty id
            [1, 1, $this->studentFeaturePermission, $this->expectedAllPermissionArray],
            // has all permissions, except for notes  with student and faculty id
            [1, 1, $this->unsetStudentFeaurePermission('notes'), $this->setPermissionToNoneForStudentFeature('Notes')],
            // has all permissions, except for Email  with student and faculty id
            [1, 1, $this->unsetStudentFeaurePermission('email'), $this->setPermissionToNoneForStudentFeature('Email')],
            // has all permissions, except for appointment  with student and faculty id
            [1, 1, $this->unsetStudentFeaurePermission('booking'), $this->setPermissionToNoneForStudentFeature('Booking')],
            // has all permissions, except for contacts  with student and faculty id
            [1, 1, $this->unsetStudentFeaurePermission('log_contacts'), $this->setPermissionToNoneForStudentFeature('Log Contacts')],

            // has all permissions  with student as null and faculty id
            [1, null, $this->userHavingAllPermissions, $this->expectedAllPermissionArray],
            // has all permissions,except for notes  with student as null and faculty id
            [1, null, $this->unsetPermisionVariable('notes'), $this->setPermissionToNoneForaFeature('Notes')],
            // has all permissions,except for email  with student as null and faculty id
            [1, null, $this->unsetPermisionVariable('email'), $this->setPermissionToNoneForaFeature('Email')],
            // has all permissions,except for appointment  with student as null and faculty id
            [1, null, $this->unsetPermisionVariable('booking'), $this->setPermissionToNoneForaFeature('Booking')],
            // has all permissions,except for contacts  with student as null and faculty id
            [1, null, $this->unsetPermisionVariable('log_contacts'), $this->setPermissionToNoneForaFeature('Log Contacts')]
        ]
        ]);
    }


    private function unsetPermisionVariable($keyToUnset)
    {

        $permissionArray = $this->userHavingAllPermissions;
        unset($permissionArray['user_feature_permissions'][0][$keyToUnset]);
        unset($permissionArray['user_feature_permissions'][0][$keyToUnset . "_share"]);
        return $permissionArray;
    }


    private function setPermissionToNoneForaFeature($feature)
    {
        $expectedPermissionArray = $this->expectedAllPermissionArray;
        $expectedPermissionArray[$feature] = [
            'public_view' => 0,
            'team_view' => 0
        ];
        return $expectedPermissionArray;
    }


    private function unsetStudentFeaurePermission($keyToUnset){

        $studentFeaturePermission = $this->studentFeaturePermission;
        unset($studentFeaturePermission[$keyToUnset]);
        return $studentFeaturePermission;
    }

    private function  setPermissionToNoneForStudentFeature($feature){
        $studentFeaturePermission = $this->expectedAllPermissionArray;
        $studentFeaturePermission[$feature] = [
            'public_view' => 0,
            'team_view' => 0
        ];
        return $studentFeaturePermission;

    }
}