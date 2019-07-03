<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class StaffPerformingCoordinatorActionsCest extends SynapseRestfulTestBase
{

 /* this is the staff member that is being tested against!
    M stands for Minerva McGonagall or Sounds like a cool
    double Oh Seven character.*/
    private $M = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];


    private $academicTerm = [
        'academic_year_id' => 110,
        'start_date' => '2016-01-04',
        'end_date' => '2016-02-28',
        'term_code' => 'W16',
        'name' => 'Winter'
    ];

    private $editAcademicTerm = [
        "academic_year_id" => 110,
        "end_date" => "2015-12-01",
        "name" => "Staff Name Change",
        "organization_id" => "542",
        "start_date" => "2015-09-01",
        "term_code" => "F15",
        "term_id" => 123
    ];



    private $academicYear = [
        'start_date' => '2017-08-18',
        'end_date' => '2018-05-19',
        'year_id' => '201819',
        'name' => 'Staff Input 2018-2019'
    ];

    private $academicYear2 = [
        'start_date' => '2018-08-15',
        'end_date' => '2019-05-15',
        'year_id' => '201819',
        'name' => '2018-2019'
    ];

    private $instituteFeatures = [
        'referral_feature_id' => 1,
        "is_referral_enabled" => false,
        'referral_org_id' => 703,
        "is_notes_enabled"=> true,
        'notes_feature_id' => 2,
        "is_log_contacts_enabled" =>true,
        'notes_org_id' => 704,
        'log_contacts_feature_id' => 3,
        "is_booking_enabled" => true,
        'log_contacts_org_id' => 705,
        'booking_feature_id' => 4,
        'booking_org_id' => 706,
        "is_student_referral_notification_enabled" => true,
        'student_referral_notification_feature_id' => 5,
        'student_referral_notification_org_id' => 707,
        'reason_routing_feature_id' => 6,
        'reason_routing_org_id' => 708,
        "is_reason_routing_enabled" => false    // If this were true, a 'reason_routing_list' would be required.
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
        'permission_template_name' => "TEST_createPermissionSet",
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
        'group_name' => 'Dueling Club',
        'staff_list' => []
    ];

    private $newGroup2 = [
        'organization_id' => 542,
        'parent_group_id' => 0,
        'group_name' => 'testgroup222',
        'staff_list' => []
    ];

    private $group = [
        'group_id' => 1419,
        'organization_id' => 542,
        'parent_group_id' => 0,
        'group_name' => 'Gobstones Game Club',
        'staff_list' => []

    ];

    private $newProfileItem = [
        'item_label' => 'O.W.L. Grade',
        'definition_type' => 'O'
    ];


    private $profileItem = [
        'id' => 392,
        'item_label' => 'Hogwarts_Personal',
        'definition_type' => 'O',
        'item_data_type' => 'T'
    ];

    private $createCoordinator = [
        'campusid' => 542	,
        'email' => "remus.lupin@mailinator.com"	,
        'externalid' => "C009"	,
        'firstname' => "Remus"	,
        'lastname' => "Lupin"	,
        'phone' => "555-5555"	,
        'roleid' => 1	,
        'title' => "Werewolf"	,
        'user_type' => "coordinator"	,
    ];

    // Data that needs to be sent through the
    // API for upgrading a faculty member from
    // an faculty to a coordinator
    private $upgradeFaculty = [
        'campusid' => 542,
        'roleid' => 1
    ];

    // The data that needs to be sent through the
    // API for editing a coordinator
    private $editCoordinator = [
        "campusid" => 542,
        "email" => 'NameChange.LastNameChange@mailinator.com',
        "externalid" => "Another Coordinator",
        "firstname" => "Name Change",
        "id" => 99708,
        "ismobile" => true	,
        "lastname" => "Last Name Change",
        "phone" => "867-5309",
        "roleid" => 3,
        "title" => "Title Change",
        "user_type" => "coordinator",
    ];



    public function testStaffLogIn(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I can log in to my account.');
        $testData = [
            ['email' => $this->M['email']],
            ['organization_id' => $this->M['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->M, 'myaccount', [], 200, $testData);
    }

    public function testStaffOverviewTab(ApiAuthTester $I)
    {
        $I->wantTo("As a Staff Member for Institute A, I should not be able to view the Coordinator's overview tab.");
        $params = ['pId' => $this->M['id']];
        $testData = [
            ['organization_id' => (string)$this->M['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->M,
            'organization/'.$this->M['orgId'].'/overview', $params, 403, []);
    }
    public function testInstituteSettingsTab(ApiAuthTester $I)
    {
        $I->wantTo("As a Staff Member for Institute A, I should not be able to view my coordinator's Settings Tab.");
        $this->_getAPITestRunner($I, $this->M, 'features/'.$this->M['orgId'].'/'.$this->M['langId'], [], 403, []);
    }
    public function testCreateAcademicYear(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff member for Institute A, I should not be able to create an academic year for Institute A.');
        $params = array_merge(
            ['organization_id' => (string)$this->M['orgId']], $this->academicYear);
        $this->_postAPITestRunner($I, $this->M, 'academicyears', $params, 403, []);
    }
    public function testDeleteAcademicYear(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can delete an academic year for Institute A.');
        // 111 is the id of the academic year to be deleted.
        $this->_deleteAPITestRunner($I, $this->M,
            'academicyears/'.$this->M['orgId'].'/111', [], 403, []);
    }




    public function testStaffCreateAcademicTerm(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to create an Academic Term.');
        $params = array_merge(
            ['organization_id' => (string)$this->M['orgId']], $this->academicTerm);
        $this->_postAPITestRunner($I, $this->M, 'academicterms', $params, 403, []);
    }

    // Can edit academic term's name's
    // API-type: PUT
    // api = /api/v1/academicterms
    // params:
    /*
academic_year_id: 110
end_date: "2015-12-01"
name: "New Year"
organization_id: "542"
start_date: "2015-09-01"
term_code: "F15"
term_id: 123
*/
    public function testStaffEditAcademicTerm(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to edit an academic term');
        // 124 is the id of the academic term to be deleted.
        $this->_putAPITestRunner($I, $this->M, 'academicterms', $this->editAcademicTerm, 403, []);
    }



//////////////////Eit academic Terms to make sure a term is getting deleted that is not in other class//////////////
    public function testStaffDeleteAcademicTerm(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to delete an academic term');
        // 124 is the id of the academic term to be deleted.
        $this->_deleteAPITestRunner($I, $this->M, 'academicterms/132', [], 403, []);
    }

    public function testStaffSaveInstituteFeaturesSettings(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to Save Institute Feature Settings.');
        $params = array_merge(
            ['organization_id' => (string)$this->M['orgId']], $this->instituteFeatures);
        $testData = [
            ['organization_id' => $this->M['orgId']],
            $this->instituteFeatures
        ];
        $this->_putAPITestRunner($I, $this->M, 'features', $params, 403, []);
    }

    public function testStaffPermissionSetTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to view the institution Permission Set Tab.');
        $params = ['orgId' => $this->M['orgId']];
        $this->_getAPITestRunner($I, $this->M, 'orgpermissionset/list', $params, 403, []);
    }

    public function testStaffViewPermissionSet(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to view a specific permission set for Institute A.');
        $this->_getAPITestRunner($I, $this->M,
            'orgpermissionset?id='.$this->permissionSetToView['permission_template_id'], [], 403, []);
    }



    public function testStaffCreatePermissionSet(ApiAuthTester $I)
    {
        $I->wantTo('Create a Permission Set as a staff member.');
        $params = array_merge(['organization_id' => $this->M['orgId']], $this->permissionSet);
        $this->_postAPITestRunner($I, $this->M, 'orgpermissionset', $params, 403, []);
    }


/* Also failing for wrong reasons

    public function testStaffTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view a specific permissionset for Institute A.');
        $testData = [
            ['organization_id' => $this->M['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->M, 'upload/faculty', [], 400, []);
    }
    public function testStudentsTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Coordinator for Institute A, I can view students tab information for Institute A.');
        $testData = [
            ['organization_id' => $this->M['orgId']]
        ];
        $this->_getAPITestRunner($I, $this->M, 'upload/student', [], 400, []);
    }
*/

    public function testStaffViewCoordinatorGroupTab(ApiAuthTester $I)
    {
        $I->wantTo("As a Staff Member for Institute A, I should not be able to view the coordinator's group tab.");
        $this->_getAPITestRunner($I, $this->M, 'groups/search/' . $this->M['orgId'], [], 403, []);
    }
    public function testStaffCreateGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not create a Group tab for Institute A.');
        $testData = $this->newGroup;
        $this->_postAPITestRunner($I, $this->M, 'groups', $testData, 403, []);
    }

    public function testStaffEditGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff member for Institute A, I should not be able to edit group information for Institute A.');
        $testData = $this->group;
        $this->_putAPITestRunner($I, $this->M, 'groups', $testData, 403, []);
    }

    // Deleting Slytherin's Quidditch team might need to change number later...
    public function testStaffDeleteGroup(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to delete groups for Institute A.');
        $this->_deleteAPITestRunner($I, $this->M, 'groups/1271', [], 403, []);
    }

    //TEST HAS BEEN INVALIDATED BY DEVA, SHOULD WE REMOVE?
    //JIRA  ESPRJ-4281
    /*
    public function testStaffISPsTab(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to view the ISP tab information for Institute A.');
        $this->_getAPITestRunner($I, $this->M, 'profile_item/org/'.$this->M['orgId'], [], 403, []);
    }
    */
    public function testStaffAddProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to add an ISP for Institute A.');


        $params = array_merge(['organization_id' => (string)$this->M['orgId']], $this->newProfileItem);


        $this->_postAPITestRunner($I, $this->M, 'profile_item', $params, 403, []);
    }

    public function testStaffViewProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to view a specific ISP for Institute A.');
        $this->_getAPITestRunner($I, $this->M, 'profile_item/392', ['type' => 'O'], 403, []);
    }


    public function testStaffEditProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to edit a specific ISP for Institute A.');
        $this->profileItem['item_data_type'] = 'N';
        $params = array_merge(['organization_id' => (string)$this->M['orgId']], $this->profileItem);
        $this->_putAPITestRunner($I, $this->M, 'profile_item', $params, 403, []);
    }
    public function testStaffDeleteProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('As a Staff Member for Institute A, I should not be able to delete a specific ISP for Institute A.');
        $this->_deleteAPITestRunner($I, $this->M, 'profile_item/390?type=O', [], 403, []);
    }
    public function testStaffCreateCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Staff Member for Institute A, I should not be able to create a new Coordinator for Institute A.');
        $this->_postAPITestRunner($I, $this->M, 'users', $this->createCoordinator, 403, []);    }

    public function testStaffEditCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Staff Member for Institute A, I should not be able to Edit a Coordinator for Institute A.');
        $this->_putAPITestRunner($I, $this->M, 'users/99708', $this->editCoordinator, 403, []);    }

    public function testStaffSendCoordinatorInvitation(ApiAuthTester $I){
        $I->wantTo('As a Staff Member for Institute A, I should not be able to Send a Coordinator an Invitation to visit Map-Works for Institute A.');
        $this->_getAPITestRunner($I, $this->M, 'users/542/user/99704/sendlink?type=standalone', [], 403, []);
    }

    public function testStaffUpgradeFacultyToCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Staff Member for Institute A, I should not be able to Upgrade a Faculty Member to a Coordinator Position for Institute A.');
        $this->_putAPITestRunner($I, $this->M, 'users/99438', $this->upgradeFaculty, 403, []);
    }

    public function testStaffDeleteCoordinator(ApiAuthTester $I){
        $I->wantTo('As a Staff Member for Institute A, I should not be able to Delete a Coordinator for Institute A.');
        $this->_deleteAPITestRunner($I, $this->M, 'users/99708?campus-id='.$this->M['orgId'].'&type=coordinator', [], 403, []);
    }
    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
