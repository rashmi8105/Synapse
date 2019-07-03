<?php
require_once 'tests/api/SynapseTestHelper.php';

class MultiCampusCest extends SynapseTestHelper
{
	private $academicYear = 201415;
	
	private $factorReportId = 10;
	
	private $surveyId = 1;
	
	private $cohortId = 1;
	
	private $personId = 1;
	
	private $orgId = 1;
	
	private $factorId = 1;
	
	
	public function _before(ApiTester $I)
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
	
	/*public function testCreateSoloCampus(ApiTester $I)
	{
		$I->wantTo('Create Solo Campus');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('campuses', ["campus_name" => "Atlanta University",
								"langid" => "1",
								"campus_nick_name" => "Atlanta University",
								"subdomain" => "atlantas",
								"campus_id" => 'ATL',
								"timezone" => 'Pacific',
								"status" => 'Active'
								]
						);
		$response = json_decode($I->grabResponse());
		$I->seeResponseContainsJson(array(
            "subdomain" => "atlanta",
			"timezone" => "Pacific",
			"campus_id" => "ATL",
			"status" => "Active"
        ));
	}*/
	
	public function testChangeRequest(ApiTester $I)
	{				
		$I->wantTo('List Change Request');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('campuses/changerequest?type=received');
		$response = json_decode($I->grabResponse());	
	}
	
	public function testListTierUsers(ApiTester $I)
	{
		$I->wantTo('List Change Request');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('campuses/coordinators');
		$response = json_decode($I->grabResponse());	        
	}
	
	public function testListSoloCampus(ApiTester $I)
	{
		$I->wantTo('List the Solo campuses');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('campuses');
		$response = json_decode($I->grabResponse());
		$I->seeResponseCodeIs (200);
		$I->seeResponseContainsJson(array(
            "campus_name" => "Solo Campus",
			"subdomain" => "solocampus",
			"campus_id" => "SOLO"			
        ));
	}
	
	public function testCreateChangeRequest(ApiTester $I)
	{
		$I->wantTo('Create Change Request');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('campuses/changerequest', [
												"source_campus" => 130,
												"destination_campus" => 133,
												"requested_by" => 242,
												"requested_for" => 243
					]);
		$response = json_decode($I->grabResponse());
		$I->seeResponseContainsJson(array(
            "source_campus" => 130,
			"destination_campus" => 133,
			"requested_by" => 242,
			"requested_for" => 243
        ));									
	}
	
	public function testUpdateChangeRequestWithYes(ApiTester $I)
	{
		$I->wantTo('Update Change Request');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPUT('campuses/changerequest', [
												"request_id" => 9,
												"status" => 'yes'
					]);
		$response = json_decode($I->grabResponse());
		$I->seeResponseContainsJson(array(
            "request_id" => 9,
			"status" => 'yes'
        ));
	}
	
	public function testUpdateChangeRequestWithNo(ApiTester $I)
	{
		$I->wantTo('Update Change Request');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPUT('campuses/changerequest', [
												"request_id" => 9,
												"status" => 'no'
					]);
		$response = json_decode($I->grabResponse());
		$I->seeResponseContainsJson(array(
            "request_id" => 9,
			"status" => 'no'
        ));
	}
	
	public function testListExistingCampusUsers(ApiTester $I)
	{
		$I->wantTo('List Existing campus users');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('campuses/130/users');
		$response = json_decode($I->grabResponse());
		$I->seeResponseContainsJson(array(
            "campus_id" => 129,
			"first_name" => 'John',
			"last_name" => 'Mathew',
			"user_type" => 'Faculty',
			"external_id" => 'JOHN'
        ));
	}
}