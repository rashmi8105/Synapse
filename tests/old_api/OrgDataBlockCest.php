<?php

use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class OrgDataBlockCest extends SynapseTestHelper
{
	private $token;
	
	private $organization = 1;
	
	private $invalidOrganization = -1;
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}
	

	public function testGetIsqDataBlockValid(ApiTester $I)
	{
			$I->wantTo('Get Isq data block organization by API');
			$I->haveHttpHeader('Content-Type', 'application/json');
			$I->amBearerAuthenticated( $this->token);
			$I->haveHttpHeader('Accept', 'application/json');
			$I->sendGET('orgdatablock/isq?orgid='.$this->organization);
			$data = json_decode($I->grabResponse());
			
			$I->seeResponseContainsJson(array('organization_id' => $this->organization));
			$I->seeResponseContainsJson(array('data_block_type' => 'survey'));
			$I->seeResponseContains('lang_id');
			$I->seeResponseContains('data_blocks');
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson();
				
	}
	
	
	public function testGetIsqDataBlockInvalid(ApiTester $I)
	{
		$I->wantTo('Get Isq data block for invalid organization by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('orgdatablock/isq?orgid='.$this->invalidOrganization);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	/* Need to be Fixed
	public function testGetIspDataBlockValid(ApiTester $I)
	{
			$I->wantTo('Get Isp data block organization by API');
			$I->haveHttpHeader('Content-Type', 'application/json');
			$I->amBearerAuthenticated( $this->token);
			$I->haveHttpHeader('Accept', 'application/json');
			$I->sendGET('orgdatablock/isp?orgid='.$this->organization);
			$I->seeResponseContainsJson(array('organization_id' => $this->organization));
			$I->seeResponseContainsJson(array('data_block_type' => 'profile'));
			$I->seeResponseContains('isp');
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson();
			
	}
	*/
	
	
	public function testGetIspDataBlockInalid(ApiTester $I)
	{
			$I->wantTo('Get Isp data block for invalid organization by API');
			$I->haveHttpHeader('Content-Type', 'application/json');
			$I->amBearerAuthenticated( $this->token);
			$I->haveHttpHeader('Accept', 'application/json');
			$I->sendGET('orgdatablock/isp?orgid='.$this->invalidOrganization);
			$I->seeResponseCodeIs(400);
			$I->seeResponseIsJson();
			
	}

	
	public function testGetIsqDataBlockInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get Isq data block for organization with invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('orgdatablock/isq?orgid='.$this->organization);
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
			
	}
	
	public function testGetIspDataBlockInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get Isp for organization with invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('orgdatablock/isp?orgid='.$this->organization);
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}
}