<?php
require_once 'tests/api/SynapseTestHelper.php';

class ProfileBlockGetCest extends SynapseTestHelper
{

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetProfileBlocks(ApiTester $I)
    {
        $I->wantTo('get Profile Blocks by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $blockame = uniqid("Block_", true);
        $I->sendPOST('profileblocks', [
            'profile_block_name' => $blockame
        ]);
        $I->sendGET('profileblocks');
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('profile_block_id');
        $I->seeResponseContains('profile_block_name');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetProfileBlock(ApiTester $I)
    {
        $I->wantTo('get Profile Block by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $blockame = uniqid("Block_", true);
        $I->sendPOST('profileblocks', [
            'profile_block_name' => $blockame
        ]);
        $resp = json_decode($I->grabResponse());
        
        $I->sendGET('profileblocks/'.$resp->data->profile_block_id);
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('profile_block_id');
        $I->seeResponseContains('profile_block_name');
        
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }
    
    public function testGetProfileBlocksForSearch(ApiTester $I)
    {
    	$I->wantTo('get Profile Blocks for search by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept-Language', null);
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$blockame = uniqid("Block_", true);
    	$I->sendPOST('profileblocks', [
    			'profile_block_name' => $blockame
    			]);
    	$I->sendGET('profileblocks?type=search');
    	$resp = json_decode($I->grabResponse());
    	$resonseData = $resp->data;
    	$I->seeResponseContains('profile_block_id');
    	$I->canSeeResponseCodeIs(200);
    	$I->canSeeResponseIsJson();
    }
}