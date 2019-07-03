<?php
require_once 'tests/api/SynapseTestHelper.php';

class ProfileBlockUpdateCest extends SynapseTestHelper
{

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testUpdateProfileBlock(ApiTester $I)
    {
        $I->wantTo('Update an Profile Block by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $blockame = uniqid("UBlock_", true);
        
        $I->sendPOST('profileblocks', [
            'profile_block_name' => $blockame
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->profile_block_id;
        $blockame = uniqid("UpdateBlock_", true);
        $I->sendPUT('profileblocks', [
            'profile_block_id' => $id,
            'profile_block_name' => $blockame
        ]);
        
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }

    public function testUpdateProfileBlockUniqeError(ApiTester $I)
    {
        $I->wantTo('Update an Profile Block by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $blockname1 = uniqid("Block_", true);
        $I->sendPOST('profileblocks', [
            'profile_block_name' => $blockname1
        ]);
        $resp = json_decode($I->grabResponse());
        
        $blockname = uniqid("Block_", true);
        $I->sendPOST('profileblocks', [
            'profile_block_name' => $blockname
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->profile_block_id;
        
        $I->sendPUT('profileblocks', [
            'profile_block_id' => $id,
            'profile_block_name' => $blockname1
        ]);
        $I->seeResponseIsJson(array(
            'error' => 'Datablock Name already exists'
        )
        );
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateProfileBlockContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Create an Profile Block by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPUT('profileblocks', [
            'profile_block_id' => 123,
            'profile_block_name' => null
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        )
        );
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}