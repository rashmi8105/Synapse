<?php
require_once 'tests/api/SynapseTestHelper.php';

class GroupCest extends SynapseTestHelper
{

    private $token;

    private $organization = 1;

    private $invalidOrg = - 1;

    private $langId = 1;

    private $invalidLangId = - 1;

    private $groupId = 1;

    private $invalidGroupId = - 1;

    private $personId = 2;

    private $invalidPersonId = - 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateGroup(ApiTester $I)
    {
        $I->wantTo('Create a Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        
        $I->sendPOST('groups', [
            "organization_id" => $this->organization,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true),
            "external_id" => rand(100,1000),
            "staff_list" => [
                [
                    "staff_id" => 1,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 1
                ],
                [
                    "staff_id" => 3,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 0
                ]
            ]
        ]);
        
        $I->seeResponseContainsJson(array(
            'organization_id' => $this->organization
        ));
        $I->seeResponseContainsJson(array(
            'parent_group_id' => 0
        ));
        $I->seeResponseContainsJson(array(
            'staff_list' => array(
                'staff_id' => 1
            )
        ));
        $I->seeResponseContainsJson(array(
            'staff_list' => array(
                'staff_id' => 3
            )
        ));
        $I->seeResponseContains('group_id');
        $I->seeResponseContains('group_name');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetGroupById(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Get Group Details by Group Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('groups', [
            "organization_id" => $this->organization,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true),
            "external_id" => rand(100,1000),
            "staff_list" => [
                [
                    "staff_id" => 1,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 1
                ],
                [
                    "staff_id" => 3,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 0
                ]
            ]
        ]);
        $group = json_decode($I->grabResponse());
        $I->sendGET('groups/' . $group->data->group_id);
        $group = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => $this->organization
        ));
        $I->seeResponseContainsJson(array(
            'group_id' => $group->data->group_id
        ));
        $I->seeResponseContains('group_name');
        $I->seeResponseContains('parent_group_id');
        $I->seeResponseContains('parent_group_name');
        $I->seeResponseContains('subgroups_student_count');
        $I->seeResponseContains('subgroups_staff_count');
        $I->seeResponseContains('bread_crump');
        $I->seeResponseContains('students_count');
        $I->seeResponseContains('staff_list');
        $I->seeResponseContains('subgroups');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

    }

    public function testGetGroupByIdInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Group Details by Group Id with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('groups/' . $this->groupId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testGetGroupList(ApiTester $I)
    {
        $I->wantTo('Get Group List by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('groups');
        $list = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => $this->organization
        ));
        $I->seeResponseContains('total_groups');
        $I->seeResponseContains('groups');
        $I->seeResponseContains('group_id');
        $I->seeResponseContains('parent_group_name');
        $I->seeResponseContains('subgroups_staff_count');
        
        $I->seeResponseContains('group_name');
        $I->seeResponseContains('subgroup_count');
        $I->seeResponseContains('staff_count');
        $I->seeResponseContains('student_count');
        $I->seeResponseContains('subgroups');
        $I->seeResponseContains('parent_id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testCreateGroupInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create a Group with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('groups', [
            "organization_id" => $this->organization,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true),
            "external_id" => rand(100,1000),
            "staff_list" => [
                [
                    "staff_id" => 1,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 1
                ],
                [
                    "staff_id" => 3,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 0
                ]
            ]
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testDeleteGroup(ApiTester $I)
    {
        $I->wantTo('Delete Group from an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('groups', [
            "organization_id" => $this->organization,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true),
            "staff_list" => [
                [
                    "staff_id" => 1,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 1
                ],
                [
                    "staff_id" => 3,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 0
                ]
            ]
        ]);
        $groups = json_decode($I->grabResponse());
        $I->sendDELETE('groups/' . $groups->data->group_id);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testDeleteGroupInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Delete Group from an Organization with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('groups/' . $this->groupId);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testDeleteGroupInvalidGroup(ApiTester $I)
    {
        $I->wantTo('Delete Invalid Group from an Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendDELETE('groups/' . $this->invalidGroupId);
        $I->seeResponseContains("Group Not Found");
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testEditGroup(ApiTester $I)
    {
        $I->wantTo('Update Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('groups', [
            "organization_id" => $this->organization,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true),
            "external_id" => rand(100,1000),
            "staff_list" => [
                [
                    "staff_id" => 1,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 1
                ],
                [
                    "staff_id" => 3,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 0
                ]
            ]
        ]);
        $groups = json_decode($I->grabResponse());
        $I->sendPUT('groups', [
            "group_id" => $groups->data->group_id,
            "organization_id" => $groups->data->organization_id,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true),
            "external_id" => $groups->data->external_id,
            "staff_list" => [
                [
                    "group_staff_id" => $groups->data->staff_list[0]->group_staff_id,
                    "staff_id" => 1,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 1,
                    "staff_is_remove" => 0
                ],
                [
                    "group_staff_id" => $groups->data->staff_list[1]->group_staff_id,
                    "staff_id" => 3,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 0,
                    "staff_is_remove" => 1
                ]
            ]
        ]);
        $I->seeResponseContainsJson(array(
            'organization_id' => $groups->data->organization_id
        ));
        $I->seeResponseContainsJson(array(
            'group_id' => $groups->data->group_id
        ));
        $I->seeResponseContainsJson(array(
            'parent_group_id' => 0
        ));
        $I->seeResponseContainsJson(array(
            'staff_list' => array(
                'group_staff_id' => $groups->data->staff_list[0]->group_staff_id
            )
        ));
        $I->seeResponseContains('group_name');
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testEditGroupInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Update Group with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('groups', [
            "group_id" => $this->groupId,
            "organization_id" => $this->organization,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true),
            "staff_list" => [
                [
                    "group_staff_id" => 1,
                    "staff_id" => 1,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 1,
                    "staff_is_remove" => 0
                ],
                [
                    "group_staff_id" => 2,
                    "staff_id" => 3,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => 0,
                    "staff_is_remove" => 1
                ]
            ]
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testEditGroupInvalidGroup(ApiTester $I)
    {
        $I->wantTo('Update Invalid Group for organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPUT('groups', [
            "group_id" => $this->invalidGroupId,
            "organization_id" => $this->organization,
            "parent_group_id" => 0,
            "group_name" => uniqid("Group_", true)
        ]);
        $I->seeResponseContains('Group Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetGroupsSearch(ApiTester $I)
    {
        $I->wantTo('Search Group from an Organization Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->sendGET('groups/search/'.$this->organization.'/gro');
        $I->seeResponseContains('parent_group_id');
        $I->seeResponseContains('parent_group_name');
        $I->seeResponseContains('parent_group_student_count');
        $I->seeResponseContains('parent_group_staff_count');
        $I->seeResponseContains('group_id');
        $I->seeResponseContains('group_student_count');
        $I->seeResponseContains('group_staff_count');
        $I->seeResponseContains('subgroup_count');
        $I->seeResponseContains('bread_crump');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetGroupsSearchInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Search Group from an Organization Details with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->sendGET('groups/search/'.$this->organization.'/gro');
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
}
