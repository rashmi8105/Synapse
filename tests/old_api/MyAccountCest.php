<?php
require_once 'SynapseTestHelper.php';
class MyAccountCest extends SynapseTestHelper
{
    private $token;
    
    private $personId = 1;
    private $invalidPersonId = -1;
    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
    public function testGetMyAccount (ApiTester $I)
    {
        $I->wantTo('Test Get My Account API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('myaccount/basic');
        $feature = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('person_id' => $this->personId));
        $I->seeResponseContains('person_type');
        $I->seeResponseContains('person_first_name');
        $I->seeResponseContains('person_last_name');
		$I->seeResponseContains('person_email');
		$I->seeResponseContains('person_mobile');		
        $I->seeResponseContains('organization_id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
    }
    
  
    
    
    public function testGetMyAccountInvalidAuthentication (ApiTester $I)
    {
    	$I->wantTo('Test Get My Account with Invalid Authentication by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('myaccount/basic');
    	
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    
    }
    
    public function testGetLoggedInfo(ApiTester $I)
    {
        $I->wantTo('Test Get Logged In User Info');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
       
       
        $I->sendGET('myaccount');
        $details = json_decode($I->grabResponse());
		//print_r($details);exit;
        $I->seeResponseContains('type');
        $I->seeResponseContains('firstname');
        $I->seeResponseContains('lastname');
        $I->seeResponseContains('email');
		$I->seeResponseContains('mobile');
        $I->seeResponseContains('organization_id');
        $I->seeResponseContains('organization_name');
        $I->seeResponseContains('lang_id');
        $I->seeResponseContains('lang_code');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    
    public function testGetLoggedInfoInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Test Get Logged In User Info with Invalid Authentication');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('myaccount');
    	
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    public function testUpdateMyAccount(ApiTester $I)
    {
        $I->wantTo('Update MyAccount');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('myaccount', [
            'person_id' => $this->personId,
            'person_mobile' => "1234567892",
            'is_mobile_changed' => true
            ]);
        $details = json_decode($I->grabResponse());
        $I->seeResponseContains('person_type');
        $I->seeResponseContainsJson(array('person_id' => $this->personId));
        $I->seeResponseContainsJson(array('person_mobile' => "1234567892"));
        $I->seeResponseContains('person_first_name');
        $I->seeResponseContains('person_last_name');
        $I->seeResponseContains('person_email');
        $I->seeResponseContains('organization_id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    
    public function testUpdateMyAccountInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Update MyAccount');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendPUT('myaccount', [
    			'person_id' => $this->personId,
    			'person_mobile' => "1234567892",
    			'is_mobile_changed' => true
    			]);
    	
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
}