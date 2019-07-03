<?php

require_once 'SynapseTestHelper.php';

class PermissionCest extends SynapseTestHelper
{
	private $token;

	private $organization = 1;    

	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}

	public function testGetStudentsWithValidAuthentication(ApiTester $I)
	{
			$I->wantTo('Get student list with valid authentication by API');
			$I->haveHttpHeader('Content-Type', 'application/json');
			$I->amBearerAuthenticated( $this->token);
			$I->haveHttpHeader('Accept', 'application/json');
			$I->sendGET('permission/'.$this->organization.'/mystudents?personId=1');
            $I->seeResponseContainsJson(array('organization_id' => $this->organization));
            $I->seeResponseContains('users');          
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson();

	}

	public function testGetStudentsInvalidAuthentication(ApiTester $I)
	{	
		$I->wantTo('Get student list invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');	
		$I->amBearerAuthenticated("invalid_token");
		$I->sendGET('permission/'.$this->organization.'/mystudents');
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
			
	}
	 
}