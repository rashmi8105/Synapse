<?php

use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class UploadHistoryCest extends SynapseTestHelper
{
	private $token;
	
	private $organization = 1;
	
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}

	public function testListHistory(ApiTester $I)
	{		
		$I->wantTo('List all type of upload history.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendGET('upload/history/'.$this->organization.'?filter={"type":""}&offset=25&page_no=1&sortBy=+file_name');
	       
		$I->seeResponseContains('total_records');
		$I->seeResponseContains('total_pages');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	
	public function testListHistoryCSV(ApiTester $I)
	{
	    $I->wantTo('List all type of upload history data for CSV.');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated($this->token);
	    $I->sendGET('upload/history/'.$this->organization.'?filter={"type":""}&offset=25&page_no=1&sortBy=+file_name&output-format=csv');	
	    $I->seeResponseContains('You may continue to use Mapworks while your download completes. We will notify you when it is available.');	  
	    $I->seeResponseCodeIs(200);
	    $I->seeResponseIsJson();
	}
	
		
}