<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class GetGroupsByUserCest extends SynapseTestHelper
{

    private $token;
    
    private $userId = 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetGroupsByUser(ApiTester $I)
    {
        $I->wantTo('Get Groups By User by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('groups?user='.$this->userId);
        $groupsByUser = json_decode($I->grabResponse());
        $groupsUser = $groupsByUser->data->groups;
        foreach ($groupsUser as $group) {
            $I->seeResponseContainsJson(array(
                'group_id' => $group->group_id
            ));
            $I->seeResponseContainsJson(array(
                'group_name' => $group->group_name
            ));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
        }
    }

    public function testGetGroupByUserInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get GroupBy User with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated('xxxxx');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('groups?user='.$this->userId);
        $res = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }  
    
}