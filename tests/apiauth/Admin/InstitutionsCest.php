<?php
require_once(dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class institutionsCest extends SynapseRestfulTestBase
{
    private $admin = [
        'email' => 'david.warner@gmail.com',
        'password' => 'ramesh@1974',
        'id' => 99706,
        'orgId' => -1,
        'langId' => 1,
        'type' => 'admin'
    ];

    private $primaryTiers = [
        'primary_tiers' => [],
    ];

    private $conflicts = [
        'conflicts' => [],
    ];

    private $campuses = [
        'campus' => [
            '0' => [
                'campus_id' =>"nsu",
                'campus_name' =>"Hogwarts School of Witchcraft and Wizardry",
                'count_users' => 4,
                'id' => 542,
                'subdomain' => "hogwarts"
            ],
            '1' => [
                'campus_id' => "ssu",
                'campus_name' => "South State University",
                'count_users' => 1,
                'id' => 543,
                'subdomain' => "ssu"
            ]
        ]
    ];

    private $createPrimaryTierInfo = [
        'langid' => 1,
        'primary_tier_id' => "ptTempID",
        'primary_tier_name' => "Primary Tier Test Name TEMP",
        'tier_level' => "primary"
    ];

    private $editPrimaryTier = [
        'id' => "544",
        'langid' => 1,
        'primary_tier_id' => "ptEditID",
        'primary_tier_name' => "Primary Tier Test Edit",
        'tier_level' => "primary"
    ];

    private $createSecondaryTierInfo = [
        'langid' => 1,
        'primary_tier_id' => 546,
        'secondary_tier_id' => "stTempID",
        'secondary_tier_name' => "Secondary Tier Test Name Temp",
        'tier_level' => "secondary"
    ];

    private $editSecondaryTier = [
        'id' => 545,
        'langid' => 1,
        'secondary_tier_id' => "stEditID",
        'secondary_tier_name' => "Secondary Tier Test Edit",
        'tier_level' => "secondary"
    ];

    private $addCampusInfo = [
        'campus_id' => "cTempID",
        'campus_name' => "Campus Name Test Temp",
        'campus_nick_name' => "Campus Nickname Temp",
        'langid' => 1,
        'subdomain' => "tempSubdomain",
        'timezone' => "Eastern"
    ];

    private $editCampusInfo = [
        'campus_id' => "cEditID",
        'campus_name' => "Campus Name Test Edit",
        'campus_nick_name' => "Campus Nickname Test Edit",
        'id' => 546,
        'langid' => 1,
        'status' => "Inactive",
        'subdomain' => "editSubdomain",
        'timezone' => "Eastern",
        'type' => "edit"
    ];

    private $addCoordinatorInfo = [
        'campusid' => 546,
        'email' => "temp.name@email.com",
        'externalid' => "tempID",
        'firstname' => "First Temp Name",
        'ismobile' => null,
        'lastname' => "Last Temp Name",
        'phone' => "(555)555-5555",
        'roleid' => 1,
        'title' => "Temp Title",
        'user_type' => "coordinator"
    ];

    private $editCoordinatorInfo = [
        'campusid' => 546,
        'email' => "coordinator.editname@email.com",
        'externalid' => "coorEditID",
        'firstname' => "fNameEdit",
        'id' => 99714,
        'ismobile' => 0,
        'lastname' => "lNameEdit",
        'phone' => "(555)555-5555",
        'roleid' => 1,
        'title' => "titleEdit",
        'user_type' => "coordinator"
    ];

    private $sendInviteToCoordinatorInfo = [
        'data' => [
            'email_sent_status' => true,
            'email_detail' => [
                'from' => "no-reply@mapworks.com",
                'subject' => "Welcome to Mapworks",
                'to' => "coordinator.testname@mailinator.com",
                'emailKey' => "Welcome_To_Mapworks",
                'organizationId' => 546
            ],
            'message' => "Mail sent successfully to coordinator.testname@mailinator.com",
        ]
    ];

    private $proxyParameters = [
            'campus_id' => 542,
            'proxy_user_id' => 99704,
            'user_id' => 99706
    ];

    private $proxyTestData = [
        [
            'campus_id' => 542,
            'id' => 1,
            'proxy_user_id' => 99704,
            'user_id' => 99706
        ]
    ];


    //Get Conflicts
    public function testViewConflicts(ApiAuthTester $I)
    {
        $I->wantTo('Get all conflicts');
        $this->_getAPITestRunner($I, $this->admin, 'users/conflicts', [], 200, $this->conflicts);
    }

    //List Primary Tier Level
    public function testListPrimaryTier(ApiAuthTester $I)
    {
        $I->wantTo('List all primary tiers');
        $this->_getAPITestRunner($I, $this->admin, 'tiers/list?tier-level=primary', [], 200, $this->primaryTiers);
    }

    //Get Campuses
    public function testViewCampuses(ApiAuthTester $I)
    {
        $I->wantTo('Get all managed campuses');
        $this->_getAPITestRunner($I, $this->admin, 'campuses', [], 200, $this->campuses);
    }

    //New Primary Tier
    public function testCreateNewPrimaryTier(ApiAuthTester $I)
    {
        $I->wantTo('Create a new primary tier');
        $this->_postAPITestRunner($I, $this->admin, 'tiers', $this->createPrimaryTierInfo, 201, []);
    }

    //Edit Primary Tier
    public function testEditPrimaryTier(ApiAuthTester $I)
    {
        $I->wantTo('Edit an existing primary tier');
        $this->_putAPITestRunner($I, $this->admin, 'tiers', $this->editPrimaryTier, 200, []);
    }

//    //Add Primary Tier User (currently not working on front end 07/27/15)
//    public function testAddPrimaryTierUser(ApiAuthTester $I)
//    {
//        $I->wantTo('Add a user to the primary tier');
//        $this->_postAPITestRunner($I, $this->admin, $apiCall, $paramsForAPI, $responseCode, $testData);
//    }

    //Add Secondary Tier
    public function testAddSecondaryTier(ApiAuthTester $I)
    {
        $I->wantTo('Create a new secondary tier');
        $this->_postAPITestRunner($I, $this->admin, 'tiers', $this->createSecondaryTierInfo, 201, []);
    }

    //Edit Secondary Tier
    public function testEditSecondaryTier(ApiAuthTester $I)
    {
        $I->wantTo('Edit an existing secondary tier');
        $this->_putAPITestRunner($I, $this->admin, 'tiers', $this->editSecondaryTier, 200, []);
    }

//    //Add Secondary Tier User (This feature currently doesn't work 07/27/15
//    public function testAddSecondaryTierUser(ApiAuthTester $I)
//    {
//        $I->wantTo('Add a user to the secondary tier');
//        $this->_postAPITestRunner($I, $this->admin, $apiCall, $paramsForAPI, $responseCode, $testData);
//    }

    //Add Campus
    public function testAddCampus(ApiAuthTester $I)
    {
        $I->wantTo('Add a new Campus to the secondary tier');
        $this->_postAPITestRunner($I, $this->admin, 'tiers/545/campuses', $this->addCampusInfo, 201, []);
    }

    //Edit Campus
    public function testEditCampus(ApiAuthTester $I)
    {
        $I->wantTo('Edit an existing campus');
        $this->_putAPITestRunner($I, $this->admin, 'tiers/545/campus', $this->editCampusInfo, 200, []);
    }

    //New Coordinator
    public function testAddCoordinator(ApiAuthTester $I)
    {
        $I->wantTo('Create a new coordinator');
        $this->_postAPITestRunner($I, $this->admin, 'users', $this->addCoordinatorInfo, 201, []);
    }

    //Send Invitation to Coordinator
    public function testSendInvitationToCoordinator(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Send an invitation to a coordinator');
        $this->_getAPITestRunner($I, $this->admin, 'users/546/user/99714/sendlink?type=campus', [], 200, [$this->sendInviteToCoordinatorInfo]);
    }

    //Edit Coordinator Detail
    public function testEditCoordinator(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Edit information about a coordinator');
        $this->_putAPITestRunner($I, $this->admin, 'users/99714', $this->editCoordinatorInfo, 200, []);
    }

    //Proxy as Campus Coordinator
    public function testProxyAsCampusCoordinator(ApiAuthTester $I)
    {
        $I->wantTo('Proxy as a campus coordinator');
        $this->_postAPITestRunner($I, $this->admin, 'proxy', $this->proxyParameters, 201, $this->proxyTestData);
    }

    //Proxy as Tier Coordinator

    //DELETION TESTS
    //Remove Coordinator
    public function testRemoveCoordinator(ApiAuthTester $I)
    {
        $I->wantTo('Remove a coordinator from a university');
        $this->_deleteAPITestRunner($I, $this->admin, 'users/99714?campus-id=546&type=coordinator', [], 204, []);
    }

    //Delete Campus
    public function testDeleteCampus(ApiAuthTester $I)
    {
        $I->wantTo('Delete an existing campus');
        $this->_deleteAPITestRunner($I, $this->admin, 'tiers/548/campuses/549', [], 204, []);
    }

    //Delete Secondary Tier
    public function testDeleteSecondaryTier(ApiAuthTester $I)
    {
        $I->wantTo('Delete a secondary tier');
        $this->_deleteAPITestRunner($I, $this->admin, 'tiers/551?tier-level=secondary', [], 204, []);
    }

/*
    //Delete Primary Tier
    public function testDeletePrimaryTier(ApiAuthTester $I)
    {
        $I->wantTo('Delete a secondary tier');
        $this->_deleteAPITestRunner($I, $this->admin, 'tiers/547?tier-level=secondary', [], 204, []);
    }
*/

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
