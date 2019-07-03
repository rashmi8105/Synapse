<?php

use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class EbiDataBlockCest extends SynapseTestHelper
{
	private $token;
	
	private $langId = 1;
	
	private $invalidLangId = -1;
	
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

	public function testGetProfileDataBlockInvalid(ApiTester $I)
	{
		$I->wantTo('Get profile data block for invalid language by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('permissionset/'.$this->invalidLangId.'/type/profile');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
		
	public function testGetProfileDataBlockValid(ApiTester $I)
	{
			$I->wantTo('Get profile data block  by API');
			$I->haveHttpHeader('Content-Type', 'application/json');
			$I->amBearerAuthenticated( $this->token);
			$I->haveHttpHeader('Accept', 'application/json');
			$I->sendGET('permissionset/'.$this->langId.'/type/profile');
			$data = json_decode($I->grabResponse());				
			$I->seeResponseContainsJson(array('data_block_type' => 'profile'));
			$I->seeResponseContains('profile');
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson();
			
	}
	
	public function testGetSurveyDataBlockInvalid(ApiTester $I)
	{
		$I->wantTo('Get data block for invalid language by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('permissionset/'.$this->invalidLangId.'/type/survey');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testGetSurveyDataBlockValid(ApiTester $I)
	{
			$I->wantTo('Get Survey data block by API');
			$I->haveHttpHeader('Content-Type', 'application/json');
			$I->amBearerAuthenticated( $this->token);
			$I->haveHttpHeader('Accept', 'application/json');
			$I->sendGET('permissionset/'.$this->langId.'/type/survey');
			$data = json_decode($I->grabResponse());			
			$I->seeResponseContainsJson(array('data_block_type' => 'survey'));
			$I->seeResponseContains('survey');
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson();
				
	}
	
	
	public function testGetIsqDataBlockInvalidAuthentication(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
	    $this->authenticate($I);
		$I->wantTo('Get data block with invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('permissionset/'.$this->langId.'/type/profile');
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
			
	}
	
	
}
