<?php
require_once(dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class profileBlocksCest extends SynapseRestfulTestBase
{
    private $admin = [
        'email' => 'david.warner@gmail.com',
        'password' => 'ramesh@1974',
        'id' => 99706,
        'orgId' => -1,
        'langId' => 1,
        'type' => 'admin'
    ];

    private $createProfileBlock = [
        'errorShow' => "",
        'profile_block_name' => "Create Profile Block Test",
        'profile_items' => []
    ];

    private $editProfileBlock = [
        'errorShow' => "",
        'profile_block_id' => 1,
        'profile_block_name' => "Profile Block Test Edit",
        'profile_items' => []
    ];

    private $createProfileItem = [
        'calender_assignment' => "N",
        'definition_type' => "E",
        'display_name' => "Profile Item Temp",
        'item_data_type' => "T",
        'item_label' => "profileItemTemp",
        'lang_id' => 1,
        'number_type' => [
            'decimal_points' => "0",
            'max_digits' => "",
            'min_digits' => ""
        ],
        'profile_block_id' => ""
    ];

    private $createProfileItemResponse = [
        'data' => [
            'id' => 89,
            'definition_type' => "E",
            'item_data_type' => "T",
            'item_label' => "profileItemTemp",
            'lang_id' => 1,
            'number_type' => [
                'decimal_points' => "0",
                'max_digits' => "",
                'min_digits' => ""
            ],
            'sequence_no' => 88,
            'calender_assignment' => "N",
            'profile_block_id' => 0,
            'status' => "active",
            'display_name' => "Profile Item Temp"
        ]
    ];

    private $editProfileItem =[
        'calender_assignment' => "N",
        'category_type' => [],
        'definition_type' => "E",
        'display_name' => "Profile Item Test Edit",
        'id' => 3,
        'item_data_type' => "T",
        'item_label' => "profileItemTestEdit",
        'item_subtext' => "",
        'lang_id' => 1,
        'number_type' => [
            'decimal_points' => "0",
            'max_digits' => "",
            'min_digits' => ""
        ],
        'profile_block_id' => 0,
        'profile_block_name' => "",
        'sequence_no' => 3,
        'status' => "active"
    ];

    private $editProfileItemResponse = [
        'data' => [
            'id' => 3,
            'definition_type' => "E",
            'item_data_type' => "T",
            'item_subtext' => "",
            'item_label' => "profileItemTestEdit",
            'lang_id' => 1,
            'category_type' => [],
            'number_type' => [
                'decimal_points' => "0",
                'max_digits' => "",
                'min_digits' => ""
            ],
            'sequence_no' => 3,
            'calender_assignment' => "N",
            'profile_block_id' => 0,
            'profile_block_name' => "",
            'display_name' => "Profile Item Test Edit"
        ]
    ];

    private $archiveProfileItem = [
        'definition_type' => "E",
        'id' => 1,
        'status' => "archive"
    ];

    private $archiveProfileItemResponse = [
        'definition_type' => "E",
        'id' => 1,
        'status' => "archive"
    ];

    private $viewArchivedProfileItems = [
        'data' => [
            'total_archive_count' => 1,
            'profile_items' => [
                '0' => [
                    'id' => '1',
                    'item_label' => "Gender",
                    'display_name' => "Gender",
                    'item_data_type' => "S",
                    'sequence_no' => '1',
                    'profile_block_id' => "0",
                    'profile_block_name' => "",
                    'status' => "archive",
                    'item_used' => true
                ],
            ]
        ]
    ];

    private $makeProfileItemActive = [
        'definition_type' => "E",
        'id' => 89,
        'status' => "active"
    ];

    private $makeProfileItemActiveResponse = [
        'definition_type' => "E",
        'id' => 89,
        'status' => "active"
    ];

    private $deleteProfileItem = [
        'data' => [
            'id' => 89,
            'key' => "profileItemTemp",
            'definition_type' => "E",
            'metadata_type' => "T",
            'scope' => "N",
            'status' => "active"
        ]
    ];


    //Create Profile Block
    public function testCreateProfileBlock(ApiAuthTester $I)
    {
        $I->wantTo('Create a profile block');
        $this->_postAPITestRunner($I, $this->admin, 'profileblocks', $this->createProfileBlock, 201, []);
    }

    //Edit Profile Block
    public function testEditProfileBlock(ApiAuthTester $I)
    {
        $I->wantTo('Edit a profile block');
        $this->_putAPITestRunner($I, $this->admin, 'profileblocks', $this->editProfileBlock, 204, []);
    }

    //Remove Profile Block
    public function testRemoveProfileBlock(ApiAuthTester $I)
    {
        $I->wantTo('Remove a profile block');
        $this->_deleteAPITestRunner($I, $this->admin, 'profileblocks/1', [], 204, []);
    }


    //PROFILE_ITEMS

    //Add Another Profile Item
    public function testAddProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('Add another profile item');
        $this->_postAPITestRunner($I, $this->admin, 'profile_item', $this->createProfileItem, 201, [$this->createProfileItemResponse]);
    }

    //Edit Profile Item
    public function testEditProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('Edit a profile item');
        $this->_putAPITestRunner($I, $this->admin, 'profile_item', $this->editProfileItem, 201, [$this->editProfileItemResponse]);
    }

    //Archive Profile Item
    public function testArchiveProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('Archive a profile item');
        $this->_putAPITestRunner($I, $this->admin, 'profile_item/status', $this->archiveProfileItem, 201, [$this->archiveProfileItemResponse]);
    }

    //View Archived Profile Items
    public function testViewArchivedProfileItems(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('View all archived profile items');
        $this->_getAPITestRunner($I, $this->admin, 'profile_item/ebi?status=archive', [], 200, $this->viewArchivedProfileItems);
    }

    //Make Profile Item Active
    public function testMakeProfileItemActive(ApiAuthTester $I)
    {
        $I->wantTo('Make a profile item active');
        $this->_putAPITestRunner($I, $this->admin, 'profile_item/status', $this->makeProfileItemActive, 201, [$this->makeProfileItemActiveResponse]);
    }

    //Remove Profile Item
    public function testRemoveProfileItem(ApiAuthTester $I)
    {
        $I->wantTo('Remove a profile item');
        $this->_deleteAPITestRunner($I, $this->admin, 'profile_item/89?type=E', [], 201, [$this->deleteProfileItem]);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
