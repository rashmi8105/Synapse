<?php
require_once 'tests/api/SynapseTestHelper.php';

class ProfileBlockDeleteCest extends SynapseTestHelper
{

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateProfileBlockWithOutProfile(ApiTester $I)
    {
        $I->wantTo('Create an Profile Block by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $blockame = uniqid("Block_",true);
        $I->sendPOST('profileblocks', ['profile_block_name'=> $blockame]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->profile_block_id;
        $I->sendDELETE('profileblocks/'.$id);
        $I->seeResponseCodeIs(204);
        
    }
   
}