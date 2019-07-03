<?php
require_once 'SynapseTestHelper.php';
class AccessLogCest extends SynapseTestHelper {
	
	private $token;
	
	private $organization = 1;
		
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	
	}
	public function testCreateAccessLog(ApiTester $I){		
		$I->wantTo("Create access log  API");
		$I->haveHttpHeader('Content-Type','application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept','application/json');
		$I->sendPOST('accesslog',[
			'organization'=>$this->organization,
			'person'=>1,
			'event'=>'test_login',
			'browser' => 'Mozilla'					
		]);
		$accessLog = json_decode($I->grabResponse());
		$I->seeResponseContains('organization');
		$I->seeResponseContains('person');
		$I->seeResponseContainsJson(array('organization' => array( 'id' => $this->organization )));
 		$I->seeResponseContainsJson( array('event' => 'test_login') );
 		$I->seeResponseContainsJson( array('browser' => 'Mozilla') );
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
}