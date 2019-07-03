<?php
require_once 'tests/api/SynapseTestHelper.php';

class ProfileBlockCreateCest extends SynapseTestHelper
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
        $I->seeResponseIsJson(array(
            'profile_block_name' => $blockame
            
        ));
        $I->seeResponseContains('profile_block_id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    public function testCreateProfileBlockWithProfile(ApiTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Create an Profile Block by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $blockame = uniqid("Block_",true);
        $profileItemsArray = [];
        $profileItemsArray[0]['id'] = 3;
        $I->sendPOST('profileblocks', ['profile_block_name'=> $blockame,'profile_items' => $profileItemsArray]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'profile_block_name' => $blockame
    
        ));
        $I->seeResponseContains('profile_block_id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
    
    public function testCreateProfileBlockUniqeError(ApiTester $I)
    {
        $I->wantTo('Create an Profile Block by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $blockame = uniqid("Block_",true);
        $I->sendPOST('profileblocks', ['profile_block_name'=> $blockame]);
        $I->sendPOST('profileblocks', ['profile_block_name'=> $blockame]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Datablock Name already exists'
    
        ));
       
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
       
        $I->sendPOST('profileblocks',['profile_block_name'=> '']);
      
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
    
        ));
         
       $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}
