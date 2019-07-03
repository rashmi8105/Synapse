<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class CoordinatorActionsCest extends SynapseRestfulTestBase
{

    // These are the coordinators that is being used to test the coordinator Actions

    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
        'id' => 99704,
        'orgId' => 542,
        'langId' => 1
    ];

    private $invalidCoordinator = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!',
        'langId' => 1
    ];
    

    private $academicTerm = [
        'academic_year_id' => 110,
        'start_date' => '2016-01-04',
        'end_date' => '2016-02-28',
        'term_code' => 'W16',
        'name' => 'Winter'
    ];

    private $academicTerm2 = [
        'academic_year_id' => 110,
        'start_date' => '2016-03-01',
        'end_date' => '2016-04-30',
        'term_code' => 'S16',
        'name' => 'Spring'
    ];

    private $academicYear = [
        'start_date' => '2017-08-15',
        'end_date' => '2018-05-15',
        'year_id' => '201718',
        'name' => '2017-2018'
    ];

    private $academicYear2 = [
        'start_date' => '2018-08-15',
        'end_date' => '2019-05-15',
        'year_id' => '201819',
        'name' => '2018-2019'
    ];

    private $instituteFeatures = [
        'referral_feature_id' => 1,
        'is_referral_enabled' => true,
        'referral_org_id' => 703,
        'is_notes_enabled' => false,
        'notes_feature_id' => 2,
        'is_log_contacts_enabled' => true,
        'notes_org_id' => 704,
        'log_contacts_feature_id' => 3,
        'is_booking_enabled' => true,
        'log_contacts_org_id' => 705,
        'booking_feature_id' => 4,
        'booking_org_id' => 706,
        'is_student_referral_notification_enabled' => true,
        'student_referral_notification_feature_id' => 5,
        'student_referral_notification_org_id' => 707,
        'reason_routing_feature_id' => 6,
        'reason_routing_org_id' => 708,
        'is_reason_routing_enabled' => false    // If this were true, a 'reason_routing_list' would be required.
    ];

    private $instituteFeatures2 = [
        'referral_feature_id' => 1,
        'is_referral_enabled' => true,
        'referral_org_id' => 703,
        'is_notes_enabled' => true,
        'notes_feature_id' => 2,
        'is_log_contacts_enabled' => false,
        'notes_org_id' => 704,
        'log_contacts_feature_id' => 3,
        'is_booking_enabled' => true,
        'log_contacts_org_id' => 705,
        'booking_feature_id' => 4,
        'booking_org_id' => 706,
        'is_student_referral_notification_enabled' => true,
        'student_referral_notification_feature_id' => 5,
        'student_referral_notification_org_id' => 707,
        'reason_routing_feature_id' => 6,
        'reason_routing_org_id' => 708,
        'is_reason_routing_enabled' => false    // If this were true, a 'reason_routing_list' would be required.
    ];

    private $permissionSetToView = [
        'permission_template_id' => 36295,
        'permission_template_name' => 'Hogwarts_Coach',
        'access_level' => [
            'individual_and_aggregate' => true,
            'aggregate_only' => false
        ],
        'risk_indicator' => true,
        'intent_to_leave' => false,

        'profile_blocks' => [
            ['block_name' => 'High School Grades',
                'block_id' => 3,
                'block_selection' => false]
        ],
        'survey_blocks' => [
            ['block_name' => 'Academic - Behaviors',
                'block_id' => 7,
                'block_selection' => false]
        ],
        'features' => [
            ['id' => 1,
                'name' => 'Referrals',
                'public_share' => ['view' => false, 'create' => false],
                'private_share' => ['create' => true],
                'teams_share' => ['view' => true, 'create' => true],
                'receive_referrals' => true]
        ]

    ];

    private $permissionSet = [
        'organization_id' => 542,
        'permission_template_name' => "TestCreatePermissionSet",
        'access_level' => [
            'individual_and_aggregate' => true,
            'aggregate_only' => false
        ],
        'courses_access' => [
            'create_view_academic_update' => true,
            'view_all_academic_update_courses' => true,
            'view_all_final_grades' => true,
            'view_courses' => true
        ],
        'risk_indicator' => false,
        'intent_to_leave' => true,
        'profile_blocks' => [],

        "survey_blocks" => [],

        'features' => [
            [
                'id' => 1,
                'name' => "Referrals",
                'private_share' => ['create' => true],
                'public_share' => ['create' => true, 'view' => true],
                'teams_share' => ['create' => true, 'view' => true],
                'receive_referrals' => true
            ],

            [
                'id' => 2,
                'name' => "Notes",
                'private_share' => ['create' => true],
                'public_share' => ['create' => true, 'view' => true],
                'teams_share' => ['create' => true, 'view' => true]
            ],

            [
                'id' => 3,
                'name' => "Log Contacts",
                'private_share' => ['create' => true,],
                'public_share' => ['create' => true, 'view' => true],
                'teams_share' => ['create' => true, 'view' => true]
            ],
            [
                'id' => 4,
                'name' => "Booking",
                'private_share' => ['create' => true,],
                'public_share' => ['create' => true, 'view' => true],
                'teams_share' => ['create' => true, 'view' => true]
            ],
        ],
        'isq' => [],
        "isp" => [],

    ];


    private $newGroup = [
        'organization_id' => 542,
        'parent_group_id' => 0,
        'group_name' => 'testgroup2000',
        'staff_list' => []
    ];

    private $newGroup2 = [
        'organization_id' => 542,
        'parent_group_id' => 0,
        'group_name' => 'testgroup222',
        'staff_list' => []
    ];

    private $group = [
        'group_id' => 1264,
        'organization_id' => 542,
        'parent_group_id' => 0,
        'group_name' => 'Ravenclaw',
        'staff_list' => []
    ];

    private $group2 = [
        'group_id' => 1265,
        'organization_id' => 542,
        'parent_group_id' => 0,
        'group_name' => 'Hufflepuff',
        'staff_list' => []
    ];

    private $newProfileItem = [
        'item_label' => 'new item',
        'definition_type' => 'O'
    ];

    private $newProfileItem2 = [
        'item_label' => 'new item 2',
        'definition_type' => 'O'
    ];

    private $profileItem = [
        'id' => 392,
        'item_label' => 'Hogwarts_Personal',
        'definition_type' => 'O',
        'item_data_type' => 'T'
    ];

    private $createValidCoordinator = [
        'campusid' => 542	,
        'email' => "horace.slughorn@mailinator.com"	,
        'externalid' => "C003"	,
        'firstname' => "Horace"	,
        'lastname' => "Slughorn"	,
        'phone' => "555-5555"	,
        'roleid' => 1	,
        'title' => "Walrus Mustache"	,
        'user_type' => "coordinator"	,
    ];

    private $createInvalidCoordinator = [
        'campusid' => 542	,
        'email' => "Cornelius.Fudge@mailinator.com"	,
        'externalid' => "C004"	,
        'firstname' => "Cornelius"	,
        'lastname' => "Fudge"	,
        'phone' => "555-5555"	,
        'roleid' => 1	,
        'title' => "Minister of Magic"	,
        'user_type' => "coordinator"	,
    ];

    // Data that needs to be sent through the
    // API for upgrading a faculty member from
    // a faculty to a coordinator
    private $upgradeFaculty = [
        'campusid' => 542,
        'roleid' => 1
    ];

    private $upgradeFaculty2 = [
        'campusid' => 542,
        'roleid' => 2
    ];

    // The data that needs to be sent through the
    // API for editing a coordinator
    private $editGoodGuyCoordinator = [
        "campusid" => 542,
        "email" => 'Albus.Dumbledore.is.Changing.This.Name@mailinator.com',
        "externalid" => "Albus.Dumbledore.is.Changing.This.Name",
        "firstname" => "Albus.Dumbledore.is.Changing.This.Name",
        "id" => 99707,
        "ismobile" => true	,
        "lastname" => "Albus.Dumbledore.is.Changing.This.Name",
        "phone" => "867-5309",
        "roleid" => 3,
        "title" => "Albus.Dumbledore.is.Changing.This.Name",
        "user_type" => "coordinator",
    ];

    // The data that needs to be sent through the
    // API for editing a coordinator
    private $editBadGuyCoordinator = [
        "campusid" => 542,
        "email" => 'Someone.is.Changing.This.Name@mailinator.com',
        "externalid" => "Someone.is.Changing.This.Name",
        "firstname" => "Someone.is.Changing.This.Name",
        "id" => 99709,
        "ismobile" => true	,
        "lastname" => "Someone.is.Changing.This.Name",
        "phone" => "867-5309",
        "roleid" => 3,
        "title" => "Someone.is.Changing.This.Name",
        "user_type" => "coordinator",
    ];


    public function testCoordinatorLogIn(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can log in to my account.');
        $testData = [
            ['email' => $this->coordinator['email']],
            ['organization_id' => $this->coordinator['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->coordinator, 'myaccount', [], 200, $testData);
    }

    public function testCoordinatorOverviewTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view the information from my Overview Tab.');
        $params = ['pId' => $this->coordinator['id']];
        $testData = [
            ['organization_id' => (string)$this->coordinator['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->coordinator,
            'organization/'.$this->coordinator['orgId'].'/overview', $params, 200, $testData);
    }

    public function testInvalidCoordinatorOverviewTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view information from Overview Tab of another Coordinator at Institute A.');
        $params = ['pId' => $this->coordinator['id']];
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'organization/'.$this->coordinator['orgId'].'/overview', $params, 403, ['data' => []]);
    }

    public function testInstituteSettingsTab(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view the information for my Settings Tab.');
        $testData = [
            ['organization_id' => $this->coordinator['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->coordinator,
            'features/'.$this->coordinator['orgId'].'/'.$this->coordinator['langId'],
            [], 201, $testData);
    }

    public function testInvalidCoordinatorInstituteSettingsTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view information from Settings Tab of another Coordinator at Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'features/'.$this->coordinator['orgId'].'/'.$this->coordinator['langId'],
            [], 403, ['data' => []]);
    }

    public function testCreateAcademicTerm(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can create an academic term for Institute A.');
        $params = array_merge(
            ['organization_id' => (string)$this->coordinator['orgId']], $this->academicTerm);
        $testData = [
            ['organization_id' => $this->coordinator['orgId']],
            $this->academicTerm
        ];
        $this->_postAPITestRunner($I, $this->coordinator, 'academicterms', $params, 201, $testData);
    }


    public function testInvalidCoordinatorCreateAcademicTerm(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create an academic term for Institute A.');
        $params = array_merge(
            ['organization_id' => (string)$this->coordinator['orgId']], $this->academicTerm2);
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'academicterms', $params, 403, ['data' => []]);
    }

    public function testDeleteAcademicTerm(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can delete an academic term for Institute A.');
        // 130 is the id of the academic term to be deleted.
        $this->_deleteAPITestRunner($I, $this->coordinator,
            'academicterms/130', [], 204, []);
    }

    public function testInvalidCoordinatorDeleteAcademicTerm(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to delete an academic term for Institute A.');
        // 123 is the id of the academic term to be deleted.
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator,
            'academicterms/123', [], 400, []);  //Using 400 instead of 403 because the search to find the term before deleting it actually fails and returns 400
    }

    public function testCreateAcademicYear(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can create an academic year for Institute A.');
        $params = array_merge(
            ['organization_id' => (string)$this->coordinator['orgId']], $this->academicYear);
        $testData = [
            ['organization_id' => $this->coordinator['orgId']],
            $this->academicYear
        ];
        $this->_postAPITestRunner($I, $this->coordinator, 'academicyears', $params, 201, $testData);
    }

    public function testInvalidCoordinatorCreateAcademicYear(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create an academic year for Institute A.');
        $params = array_merge(
            ['organization_id' => (string)$this->coordinator['orgId']], $this->academicYear2);
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'academicyears', $params, 403, ['data' => []]);
    }

    public function testDeleteAcademicYear(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can delete an academic year for Institute A.');
        // 111 is the id of the academic year to be deleted.
        $this->_deleteAPITestRunner($I, $this->coordinator,
            'academicyears/'.$this->coordinator['orgId'].'/111', [], 204, []);
    }

    public function testInvalidCoordinatorDeleteAcademicYear(ApiAuthTester $I)
    {
        $I->wantTo('Delete an Academic Year as a coordinator from another institution');
        // 112 is the id of the academic year to be deleted.
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator,
            'academicyears/'.$this->coordinator['orgId'].'/112', [], 403, []);
    }

    public function testSaveInstituteFeaturesSettings(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('As a Coordinator for Institute A, I can set institution feature settings for Institute A.');
        $params = array_merge(
            ['organization_id' => (string)$this->coordinator['orgId']], $this->instituteFeatures);
        $testData = [
            ['organization_id' => $this->coordinator['orgId']],
            $this->instituteFeatures
        ];
        $this->_putAPITestRunner($I, $this->coordinator, 'features', $params, 201, $testData);
    }

    public function testInvalidCoordinatorSaveInstituteFeaturesSettings(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to set institution feature settings for Institute A.');
        $params = array_merge(
            ['organization_id' => (string)$this->coordinator['orgId']], $this->instituteFeatures2);
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'features', $params, 403, ['data' => []]);
    }

    public function testPermissionSetTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view permission set tab information for Institute A.');
        $params = ['orgId' => $this->coordinator['orgId']];
        $testData = [
            ['organization_id' => $this->coordinator['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->coordinator, 'orgpermissionset/list', $params, 200, $testData);
    }

    public function testInvalidCoordinatorPermissionSetTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view permission set tab information for Institute A.');
        $params = ['orgId' => $this->coordinator['orgId']];
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'orgpermissionset/list', $params, 403, ['data' => []]);
    }

    public function testViewPermissionSet(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view a specific permission set for Institute A.');

        $testData = [
            ['organization_id' => $this->coordinator['orgId']],
            $this->permissionSetToView
        ];
        $this->_getAPITestRunner($I, $this->coordinator,
            'orgpermissionset?id='.$this->permissionSetToView['permission_template_id'], [], 200, $testData);
    }

    public function testInvalidCoordinatorViewPermissionSet(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view a specific permission set for Institute A.');
        $params = ['id' => $this->permissionSetToView['permission_template_id']];
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'orgpermissionset', $params, 403, ['data' => []]);
    }

    public function testCreatePermissionSet(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Create a Permission Set as a coordinator');
        $params = array_merge(['organization_id' => $this->coordinator['orgId']], $this->permissionSet);
        $this->_postAPITestRunner($I, $this->coordinator, 'orgpermissionset', $params, 201, []);
    }

    public function testInvalidCoordinatorCreatePermissionSet(ApiAuthTester $I)
    {
        $I->wantTo('Create a Permission Set as an invalid coordinator');
        $params = array_merge(['organization_id' => $this->coordinator['orgId']], $this->permissionSet);
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'orgpermissionset', $params, 403, []);
    }

    /* Upload tests?? not yet done...
    public function testStaffTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view a specific permissionset for Institute A.');
        $testData = [
            ['organization_id' => $this->coordinator['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->coordinator, 'upload/faculty', [], 200, $testData);
    }
    public function testStudentsTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view students tab information for Institute A.');
        $testData = [
            ['organization_id' => $this->coordinator['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->coordinator, 'upload/student', [], 200, $testData);
    }
    */

    public function testCoordinatorGroupTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view group tab information for Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'groups/search/' . $this->coordinator['orgId'], [], 200, []);
    }

    public function testInvalidCoordinatorGroupTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view group tab information for Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];

        $this->_getAPITestRunner($I, $authData, 'groups/search/' . $this->coordinator['orgId'], [], 403, ['data' =>[]]);
    }

    public function testCoordinatorCreateGroup(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('As a Coordinator for Institute A, I can create groups for Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];
        $testData = $this->newGroup;

        $this->_postAPITestRunner($I, $authData, 'groups', $testData, 200, ['data' => $testData]);
    }

    public function testInvalidCoordinatorCreateGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create groups for Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];
        $testData = $this->newGroup2;

        $this->_postAPITestRunner($I, $authData, 'groups', $testData, 403, []);
    }

    public function testCoordinatorEditGroup(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('As a Coordinator for Institute A, I can edit groups for Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];
        $testData = $this->group;

        $this->_putAPITestRunner($I, $authData, 'groups', $testData, 200, ['data' => $this->group]);
    }

    public function testInvalidCoordinatorEditGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to edit groups for Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];
        $testData = $this->group2;

        $this->_putAPITestRunner($I, $authData, 'groups', $testData, 403, []);
    }

    public function testCoordinatorDeleteGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can delete groups for Institute A.');
        $authData = ['email' => $this->coordinator['email'], 'password' => $this->coordinator['password']];

        $this->_deleteAPITestRunner($I, $authData, 'groups/1264', [], 200, []);
    }

    public function testInvalidCoordinatorDeleteGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to delete groups for Institute A.');
        $authData = ['email' => $this->invalidCoordinator['email'], 'password' => $this->invalidCoordinator['password']];

        $this->_deleteAPITestRunner($I, $authData, 'groups/1265', [], 400, []);
        //Using 400 instead of 403 because the search to find the term before deleting it actually fails and returns 400
    }

    public function testISPsTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view the ISP tab information for Institute A.');
        $testData = [
            ['organization_id' => (string)$this->coordinator['orgId']],
            ['id' => 392, 'item_label' => 'Hogwarts_Personal']
        ];
        $this->_getAPITestRunner($I, $this->coordinator,
            'profile_item/org/'.$this->coordinator['orgId'], [], 200, $testData);
    }

    public function testInvalidCoordinatorISPsTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view the ISP tab information for Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'profile_item/org/'.$this->coordinator['orgId'], [], 403, ['data' => []]);
    }

    public function testAddProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can add an ISP for Institute A.');
        $params = array_merge(['organization_id' => (string)$this->coordinator['orgId']],
            $this->newProfileItem);
        $testData = [['organization_id' => $this->coordinator['orgId']],
            $this->newProfileItem];
        $this->_postAPITestRunner($I, $this->coordinator, 'profile_item', $params, 201, $testData);
    }

    public function testInvalidCoordinatorAddProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to add an ISP for Institute A.');
        $params = array_merge(['organization_id' => (string)$this->coordinator['orgId']],
            $this->newProfileItem2);
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'profile_item', $params, 403,
            ['data' => []]);
    }

    public function testViewProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view a specific ISP for Institute A.');
        $testData = [['id' => 392, 'item_label' => 'Hogwarts_Personal']];
        $this->_getAPITestRunner($I, $this->coordinator,
            'profile_item/392', ['type' => 'O'], 200, $testData);
    }

    public function testInvalidCoordinatorViewProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to view a specific ISP for Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'profile_item/392', ['type' => 'O'], 403, ['data' => []]);
    }

    public function testEditProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can edit a specific ISP for Institute A.');
        $this->profileItem['item_data_type'] = 'N';
        $params = array_merge(['organization_id' => (string)$this->coordinator['orgId']],
            $this->profileItem);
        $testData = [['organization_id' => $this->coordinator['orgId']],
            $this->profileItem];
        $this->_putAPITestRunner($I, $this->coordinator, 'profile_item', $params, 201, $testData);
    }

    public function testInvalidCoordinatorEditProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to edit a specific ISP for Institute A.');
        $this->profileItem['item_data_type'] = 'D';
        $params = array_merge(['organization_id' => (string)$this->coordinator['orgId']],
            $this->profileItem);
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'profile_item', $params, 403, ['data' => []]);
    }

    public function testDeleteProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can delete a specific ISP for Institute A.');
        $this->_deleteAPITestRunner($I, $this->coordinator,
            'profile_item/392?type=O', [], 201, []);
    }

    public function testInvalidCoordinatorDeleteProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute B, I should not be able to delete a specific ISP for Institute A.');
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator,
            'profile_item/393?type=O', [], 403, []);
    }

    public function testCoordinatorCreateCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute A, I should be able to create a new Coordinator for Institute A.');
        $this->_postAPITestRunner($I, $this->coordinator, 'users', $this->createValidCoordinator, 201, [$this->createValidCoordinator]);
    }

    public function testInvalidCoordinatorCreateCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute B, I should not be able to create a new Coordinator for Institute A.');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'users', $this->createInvalidCoordinator, 403, []);
    }

    public function testCoordinatorSendCoordinatorInvitation(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute A, I should be able to Send a Coordinator an Invitation to visit Map-Works for Institute A.');
        $this->_getAPITestRunner($I, $this->coordinator, 'users/542/user/99707/sendlink?type=standalone', [], 200, []);


        // Checking to make sure no reset link returned or token
        $I->dontSeeResponseContains('http');
        $I->dontSeeResponseContains('token');
    }

    public function testInvalidCoordinatorSendCoordinatorInvitation(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute B, I should not be able to Send a Coordinator an Invitation to visit Map-Works for Institute A.');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'users/542/user/99709/sendlink?type=standalone', [], 403, []);

        // Checking to make sure no reset link returned or token
        $I->dontSeeResponseContains('http');
        $I->dontSeeResponseContains('token');
    }

    public function testCoordinatorEditCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute A, I should be able to Edit a Coordinator for Institute A.');
        $this->_putAPITestRunner($I, $this->coordinator, 'users/99707', $this->editGoodGuyCoordinator, 200, []);
    }

    public function testInvalidCoordinatorEditCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute B, I should be not able to Edit a Coordinator for Institute A.');
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'users/99709', $this->editBadGuyCoordinator, 403, []);
    }

    public function testCoordinatorUpgradeFacultyToCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute A, I should be able to Upgrade a Faculty Member to a Coordinator Position for Institute A.');
        $this->_putAPITestRunner($I, $this->coordinator, 'users/99443', $this->upgradeFaculty, 200, []);
    }

    public function testCoordinatorUpgradeInvalidCoordinator(ApiAuthTester $I){
        $I->wantTo('Verify that a Coordinator from Institute A cannot upgrade a coordinator from Institute B to a coordinator position in Institute A.');
        $this->_putAPITestRunner($I, $this->coordinator, 'users/99705', $this->upgradeFaculty2, 400, []);
    }

    public function testInvalidCoordinatorUpgradeFacultyToCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute B, I should not be able to Upgrade a Faculty Member to a Coordinator Position for Institute A.');
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'users/99444', $this->upgradeFaculty, 403, []);
    }

    public function testCoordinatorDeleteCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute A, I should be able to Delete a Coordinator for Institute A.');
        $this->_deleteAPITestRunner($I, $this->coordinator, 'users/99707?campus-id='.$this->coordinator['orgId'].'&type=coordinator', [], 204, []);
    }

    public function testInvalidCoordinatorDeleteCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Coordinator for Institute B, I should not be able to Delete a Coordinator for Institute A.');
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator, 'users/99709?campus-id='.$this->coordinator['orgId'].'&type=coordinator', [], 403, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }

}
