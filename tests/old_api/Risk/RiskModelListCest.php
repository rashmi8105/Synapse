<?php
require_once 'tests/api/SynapseTestHelper.php';

class RiskModelListCest extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetModelById(ApiTester $I)
    {
        $I->wantTo('Get Risk Model by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('riskmodels/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
        $I->seeResponseContains('model_name');
    }

    public function testGetModelByInvalidId(ApiTester $I)
    {
        $I->wantTo('Get Risk Model by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('riskmodels/9999');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetModelList(ApiTester $I)
    {
        $I->wantTo('Get Risk Model by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('riskmodels?status=Active');
        
        $I->seeResponseContains('id');
        $I->seeResponseContains('total_count');
        $I->seeResponseContains('total_archived_count');
        
        $I->seeResponseContains('id');
        $I->seeResponseContains('model_name');
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
  
    public function testGetRiskModelsAssignments(ApiTester $I)
    {
        $I->wantTo('Get Risk Model by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendGET('riskmodels/assignments?filter=all&viewmode=json');
    
        $I->seeResponseContains('total_assigned_models_count');
        $I->seeResponseContains('risk_model_assignments');
        $I->seeResponseContains('campus_id');    
        $I->seeResponseContains('campus_name');
        
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	
}