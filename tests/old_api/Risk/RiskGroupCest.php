<?php
require_once 'tests/api/SynapseTestHelper.php';

class RiskGroup extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateRiskGroup(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            'group_name' => "risk_group_1",
            'group_description' => "risk_group_desc"
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }

    public function testCreateRiskGroupWithOutGroupName(ApiTester $I)
    {
        $I->wantTo('Create Risk Group With Out Group Name');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            
            'group_description' => "risk_group_desc"
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testEditRiskGroup(ApiTester $I)
    {
        $I->wantTo('Edit Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            'group_name' => "risk_group_1",
            'group_description' => "risk_group_desc"
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendPUT('riskgroups', [
            'id' => $id,
            'group_name' => "risk_group_2",
            'group_description' => "risk_group_desc"
        ]);
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testEditRiskGroupWithoutGroupName(ApiTester $I)
    {
        $I->wantTo('Edit Risk Group With Out Group Name');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            'group_name' => "risk_group_1",
            'group_description' => "risk_group_desc"
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendPUT('riskgroups', [
            'id' => $id,
            
            'group_description' => "risk_group_desc"
        ]);
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetRiskGroupList(ApiTester $I)
    {
        $I->wantTo('Get Risk Group List');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            'group_name' => "risk_group_1",
            'group_description' => "risk_group_desc"
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendGet('riskgroups');
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('id');
        $I->seeResponseContains('total_count');
        $I->seeResponseContains('risk_group_1');
        $I->seeResponseIsJson(array(
            'id' => $id
        )
        );
        $I->seeResponseIsJson();
    }
    
    public function testGetRiskGroupOrgList(ApiTester $I)
    {
        $I->wantTo('Get Risk Group complete list by organization');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            'group_name' => "risk_group_1",
            'group_description' => "risk_group_desc"
            ]);
        $resp = json_decode($I->grabResponse());
        
        $I->sendGet('riskgroups/org?orgId=1');    
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('total_assigned_models_count');
        $I->seeResponseContains('students_with_no_risk');
        $I->seeResponseContains('risk_model_assignments');    
        $I->seeResponseIsJson();
    }

    public function testGetRiskGroup(ApiTester $I)
    {
        $I->wantTo('Get Risk Group By Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            'group_name' => "risk_group_1",
            'group_description' => "risk_group_desc"
        ]);
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        $I->sendGet('riskgroups/'.$id);
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('id');
        
        $I->seeResponseContains('risk_group_1');
        $I->seeResponseIsJson(array(
            'id' => $id,
            'group_name' => "risk_group_1",
            'group_description' => "risk_group_desc"
        )
        );
        $I->seeResponseIsJson();
    }
    
    public function testGetRiskGroupByInvalidId(ApiTester $I)
    {
        
        $I->wantTo('Get Risk Group By Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('riskgroups/-1');
        $I->seeResponseCodeIs(400);
    }
    
    public function testEditRiskGroupInvalidId(ApiTester $I)
    {
        $I->wantTo('Edit Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
         
        $I->sendPUT('riskgroups', [
            'id' => 9999,
            'group_name' => "risk_group_2",
            'group_description' => "risk_group_desc"
            ]);
         
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testCreateRiskGroupNameLength(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('riskgroups', [
            'group_name' =>str_repeat("Group", 25),
            'group_description' => "risk_group_desc"
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    
   
    
}
