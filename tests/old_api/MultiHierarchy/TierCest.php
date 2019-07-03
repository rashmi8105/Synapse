<?php
require_once 'tests/api/SynapseTestHelper.php';

class TierCest extends SynapseTestHelper
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
	
	public function testCreateTier(ApiTester $I)
	{
		$I->wantTo('Create Tier');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('tiers', ["tier_level" => "primary",
								"primary_tier_name" => "ABCUniversity",
								"primary_tier_id" => "ABCUNIV",
								"description" => "description",
								"langid" => 1		
								]
						);		
		$response = json_decode($I->grabResponse());		
		$I->seeResponseCodeIs (201);
		$I->seeResponseContains('tier_level');		
		$I->seeResponseContains('primary_tier_name');
		$I->seeResponseContains('primary_tier_id');
		$I->seeResponseContains('description');
		$I->seeResponseContains('id');	
		$I->seeResponseContainsJson(array(
            "tier_level" => "primary",
			"primary_tier_name" => "ABCUniversity",
			"primary_tier_id" => "ABCUNIV"
        ));
	}
	
	public function testUpdateTier(ApiTester $I)
	{
		$I->wantTo('Update Primary Tier');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('tiers', ["tier_level" => "primary",
								"primary_tier_name" => "XYZUniversity",
								"primary_tier_id" => "XYZUNIV",
								"description" => "description",
								"langid" => 1		
								]
						);		
		$response = json_decode($I->grabResponse());		
		$id= $response->data->id;
		$I->sendPUT('tiers', ["id" => $id,
								"tier_level" => "primary",
								"primary_tier_name" => "XYZUniversity",
								"primary_tier_id" => "XYZUNI",
								"description" => "description",
								"langid" => 1		
								]
						);	
		$response = json_decode($I->grabResponse());		
		$I->seeResponseCodeIs (200);
		$I->seeResponseContains('tier_level');		
		$I->seeResponseContains('primary_tier_name');
		$I->seeResponseContains('primary_tier_id');
		$I->seeResponseContains('description');
		$I->seeResponseContains('id');	
		$I->seeResponseContainsJson(array(
            "id" => $id,
			"tier_level" => "primary",
			"primary_tier_name" => "XYZUniversity",
			"primary_tier_id" => "XYZUNI"
        ));
	}
	
	public function testGetTier(ApiTester $I)
	{
		$I->wantTo('Get Tier details');		
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('tiers', ["tier_level" => "primary",
								"primary_tier_name" => "Boston University",
								"primary_tier_id" => "BOSTON",
								"description" => "description",
								"langid" => 1		
								]
						);		
		$response = json_decode($I->grabResponse());				
		$id= $response->data->id;		
		$I->sendGET('tiers/'.$id.'?tier-level=primary');
		$response = json_decode($I->grabResponse());
		$I->seeResponseCodeIs (200);
		$I->seeResponseContains('tier_level');		
		$I->seeResponseContains('primary_tier_name');
		$I->seeResponseContains('primary_tier_id');
		$I->seeResponseContains('description');
		$I->seeResponseContains('id');	
		$I->seeResponseContainsJson(array(
            "tier_level" => "primary",
			"primary_tier_name" => "Boston University",
			"primary_tier_id" => "BOSTON"
        ));
	}
	
	public function testListTier(ApiTester $I)
	{
		$I->wantTo('List Tiers');
		$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('tiers', ["tier_level" => "primary",
								"primary_tier_name" => "Washington University",
								"primary_tier_id" => "WSHT",
								"description" => "Tier description",
								"langid" => 1		
								]
						);
		$response = json_decode($I->grabResponse());				
		$id= $response->data->id;	
		$I->sendGET('tiers/list?primary-tier-id='.$id.'&tier-level=primary');
		$response = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson(array(
            "primary_tiers" => array(
                "primary_tier_name" => 'Washington University'
            )
        ));
		$I->seeResponseContainsJson(array(
            "primary_tiers" => array(
                "primary_tier_id" => 'WSHT'
            )
        ));		
	}
	
	public function testCreateHierarchyCampus(ApiTester $I)
	{
		$I->wantTo('Create Hierarchy Campus');
		$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('tiers/129/campuses', ["campus_name" => "ABC Educational Board",
								"langid" => "1",
								"campus_nick_name" => "ABC Education",
								"subdomain" => "abc",
								"campus_id" => 'ABCE',
								"timezone" => 'Pacific',
								"status" => 'Active'
								]
						);
		$response = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(201);
		$I->seeResponseContainsJson(array(
            "campus_name" => "ABC Educational Board",
			"campus_nick_name" => "ABC Education",
			"subdomain" => "abc",
			"status" => "Active"
        ));
	}
	
	public function testViewHierarchyCampus(ApiTester $I)
	{
		$I->wantTo('View Hierarchy Campus');
		$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('tiers/129/campuses', ["campus_name" => "ABC College",
								"langid" => "1",
								"campus_nick_name" => "ABC College",
								"subdomain" => "abcc",
								"campus_id" => 'ABCC',
								"timezone" => 'Pacific',
								"status" => 'Active'
								]
						);
		$response = json_decode($I->grabResponse());		
		$id = $response->data->id;		
		$I->sendGET('tiers/129/campuses/'.$id);
		$response = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
		$I->seeResponseContainsJson(array(
            "campus_name" => "ABC College",
			"campus_nick_name" => "ABC College",
			"subdomain" => "abcc",
			"status" => "Active"
        ));
	}
	
	public function testDeleteHierarchyCampus(ApiTester $I)
	{
		$I->wantTo('Delete Hierarchy Campus');
		$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('tiers/129/campuses', ["campus_name" => "XYZ College",
								"langid" => "1",
								"campus_nick_name" => "XYZ College",
								"subdomain" => "xyz",
								"campus_id" => 'XYZ',
								"timezone" => 'Pacific',
								"status" => 'Active'
								]
						);
		$response = json_decode($I->grabResponse());
		$id = $response->data->id;
		$I->sendDELETE('tiers/129/campuses/' . $id);
		$I->seeResponseCodeIs(204);
	}
}
