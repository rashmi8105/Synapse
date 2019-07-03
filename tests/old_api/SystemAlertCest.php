<?php
require_once 'SynapseTestHelper.php';

class SystemAlertCest extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        //$this->token = $this->authenticate($I);
    }
	public function testEbiAdmin($I)
	{
		$I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
		'client_id' => "3_14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884",
		'client_secret' => "4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg",
		'grant_type' => "password",
		'username' => "david.warner@gmail.com",
		"password" => "ramesh@1974"
		]);
		$token = json_decode($I->grabResponse());
		$this->token = $token->access_token;
	}
    public function testSystemAlert(ApiTester $I)
    {
        $I->wantTo('Create a System Alert by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('alerts', [
            'start_date_time' => '2014-09-12 12:00AM',
            "end_date_time" => "2014-09-22 12:00PM",
            "description" => "System Alert Test Data..",
            "message" => "test message"
        ]);
        $I->seeResponseContainsJson(array('message' => "test message"));
        $I->seeResponseContainsJson(array('start_date_time' => '2014-09-12 12:00 AM'));
        $I->seeResponseContainsJson(array('end_date_time' => '2014-09-22 12:00 PM'));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        
        $I->seeResponseIsJson();
    }
    
    
    public function testSystemAlertInvalidAuthentication(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $this->authenticate($I);
    	$I->wantTo('Create a System Alert with Invalid Authentication by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendPOST('alerts', [
    			'start_date_time' => '2014-09-12 12:00AM',
    			"end_date_time" => "2014-09-22 12:00PM",
    			"description" => "System Alert Test Data..",
    			"message" => "test message"
    			]);
    	
    	$I->seeResponseCodeIs(403);
    
    	$I->seeResponseIsJson();
    }

    public function testSystemAlertNoStartDate(ApiTester $I)
    {
        $I->wantTo('Create a System Alert by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('alerts', [
            
            "description" => "System Alert Test Data..",
            "message" => "test message"
        ]);
        $I->seeResponseContainsJson(array('message' => "test message"));
        $I->seeResponseContains('id');
        $I->seeResponseContains('start_date_time');
        $I->seeResponseContains('end_date_time');
        $I->seeResponseCodeIs(201);
        
        $I->seeResponseIsJson();
    }

    public function testSystemAlertInvalidDateFormat(ApiTester $I)
    {
        $I->wantTo('Create a System Alert by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('alerts', [
            
            'start_date_time' => '2014-09-12 12:00:00',
            "end_date_time" => "2014-09-22 12:00:00",
            "description" => "System Alert Test Data..",
            "message" => "test message"
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}
