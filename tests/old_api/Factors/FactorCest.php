<?php

use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class factorCest extends SynapseTestHelper
{
	private $token;	
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}


	public function testListApiPerm(ApiTester $I){
	
	
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->sendGET('factors/list?survey_id=11');
	    $data = json_decode($I->grabResponse());
	      $I->seeResponseContainsJson(array(
	        'lang_id' => 1
	    ));
	    $I->seeResponseContains('total_count');
	    $I->seeResponseContainsJson(array(
	        'factor_name' => "Factor 1"
	    ));
	    $I->seeResponseCodeIs(200);
	    return json_encode($data);
	}
	
	public function testCreateFactor(ApiTester $I)
	{
		$I->wantTo('Crete Factor');	
		$inputData = $this->createFactorJson();
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$I->sendPOST('factors',$inputData);
		$data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'factor_name' => $this->factorName
        ));
		
		$I->seeResponseCodeIs(201);		
	}
	
	public function testListFactor(ApiTester $I){
	    
	    $I->wantTo('List Factor');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->sendGET('factors');
	    $data = json_decode($I->grabResponse());
	   
	    $I->seeResponseContainsJson(array(
	        'lang_id' => 1
	    ));
	    $I->seeResponseContains('total_count');
	    $I->seeResponseContainsJson(array(
	        'factor_name' => "Factor 1"
	    ));
	    $I->seeResponseCodeIs(200);
	    
	}
	
	private function createFactorJson(){
	    
	    $data = array();
	    $this->factorName = "factor".rand();
	    $data['lang_id'] = 1;
	    $data['factor_name'] = $this->factorName;
        return json_encode($data);
	    
	}
	
	public function testEditFactor(ApiTester $I){
	
	    $inputData = $this->editFactorJson();
	
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->sendPUT('factors',$inputData);
	    $data = json_decode($I->grabResponse());
	    $I->seeResponseContainsJson(array(
	        'factor_name' => "Factor 1"
	    ));
	     
	    $I->seeResponseCodeIs(200);
	    return json_encode($data);
	     
	}
	
	public function testReorderData(ApiTester $I){
	    
	    $inputData = $this->reorderFactordata();
	    
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->sendPUT('factors/reorder',$inputData);
	    $data = json_decode($I->grabResponse());
	    $I->seeResponseContainsJson(array(
	        'factor_id' => 1,
	        'sequence' => 1
	    ));
	    
	    $I->seeResponseCodeIs(200);
	    return json_encode($data);
	}
	
	
	
	
	private function editFactorJson(){
	     
	    $data = array();
	    $data['id'] = 1;
	    $data['factor_name'] = "Factor 1";
	    return json_encode($data);
	     
	}
	
	private function reorderFactordata(){
	    
	    $data["factor_id"] = 1;
	    $data["sequence"] = 1;
	    return json_encode($data);
	    
	}
	
}
