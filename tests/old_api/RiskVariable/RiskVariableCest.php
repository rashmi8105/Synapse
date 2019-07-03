<?php
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;
use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class RiskVariableCest extends SynapseTestHelper
{

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateRiskVariable(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"profile");
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('bucket_details');
    }
    
    public function testCreateRiskVariableSurvey(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"surveyquestion");
        $variableData['source_id']['survey_id'] = 1;
        $variableData['source_id']['question_id'] = 1;
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('bucket_details');
    }
    
    public function testCreateRiskVariableFactor(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"surveyfactor");
        $variableData['source_id']['survey_id'] = 1;
        $variableData['source_id']['factor_id'] = 1;
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('bucket_details');
    }
    
    public function testCreateRiskVariableQuestionBank(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"questionbank");
        
        $variableData['source_id']['question_bank_id'] = 1;
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('bucket_details');
    }
    
    public function testCreateRiskVariableIsp(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"isp");
    
        $variableData['source_id']['isp_id'] = 1;
        $variableData['source_id']['campus_id'] = 'ORG1';
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('bucket_details');
    }
    
    public function testCreateRiskVariableInvalidIsp(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"isp");
    
        $variableData['source_id']['isp_id'] = 1;
        $variableData['source_id']['campus_id'] = 'ORG';
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(400);
        
    }
    
    public function testCreateRiskVariableInvalidIsq(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"isq");
    
        $variableData['source_id']['isq_id'] = 1;
        $variableData['source_id']['campus_id'] = 'ORG';
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(400);
    
    }
 
    public function testCreateRiskVariableIsq(ApiTester $I)
    {
        $I->wantTo('Create risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"isq");
    
        $variableData['source_id']['isq_id'] = 1;
        $variableData['source_id']['campus_id'] = 'ORG1';
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('bucket_details');
    }
	
    public function testCreateRiskVariableInvalidJson(ApiTester $I)
    {
        $I->wantTo('Create risk variable invalid json');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');        
        $I->sendPOST('riskvariables', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    public function testCreateRiskVariableValidateOverlapped(ApiTester $I)
    {
        $I->wantTo('Create risk variable with overlapped values');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true, "profile", true);
         
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains(RiskErrorConstants::RISK_M_001);
    }
 
    public function testEditRiskVariable(ApiTester $I)
    {
        $I->wantTo('Edit risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"profile");
        $I->sendPOST('riskvariables', $variableData);
        $variable = json_decode($I->grabResponse());
        $id = $variable->data->id;
        $name = $variable->data->risk_variable_name;
        $editVariableData = $this->getEditVariableRequest(true,"profile",false, $id, $name);        
        $I->sendPUT('riskvariables', $editVariableData);
        $I->seeResponseCodeIs(200);        
    }
	
    
    public function testEditRiskVariableWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Edit risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $editVariableData = $this->getEditVariableRequest(true,"profile",false, -1, "fffff");
        $editVariableData['id'] = -1;
        $I->sendPUT('riskvariables', $editVariableData);
        $I->seeResponseCodeIs(400);
    }
    
    public function testGetRiskVariableList(ApiTester $I)
    {
        $I->wantTo('List risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"profile");
        $I->sendPOST('riskvariables', $variableData);        
        $I->sendGET('riskvariables?status=Active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
       // $I->seeResponseContains('total_count');
     //   $I->seeResponseContains('risk_variables');
        $I->seeResponseContains('id');
        $I->seeResponseContains('source_type');
        $I->seeResponseContains('source_id');
        $I->seeResponseContains('is_assigned');
    }
    
    public function testGetRiskVariable(ApiTester $I)
    {
        $I->wantTo('Get risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"profile");
        $I->sendPOST('riskvariables', $variableData);
        $variable = json_decode($I->grabResponse());
        $id = $variable->data->id;
        $I->sendGET('riskvariables/'.$id);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('risk_variable_name');
        $I->seeResponseContains('is_continuous');
        $I->seeResponseContains('is_calculated');
        $I->seeResponseContains('source_id');
    }
    
    public function testMarkArchivedRiskVariable(ApiTester $I)
    {
        $I->wantTo('Mark Archive or delete to risk variable');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true,"profile");
        $I->sendPOST('riskvariables', $variableData);
        $variable = json_decode($I->grabResponse());
        $id = $variable->data->id;
        $I->sendDELETE('riskvariables/'.$id);
        $I->seeResponseCodeIs(204);
    }
	
    
    public function testUpdateRiskVariableInvalidJson(ApiTester $I)
    {
        $I->wantTo('Create risk variable invalid json');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('riskvariables', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    private function getVariableRequest($isContinuous = true, $sourceType, $overlapped = false)
    {
        $buckets = $this->getBucketData($isContinuous, $overlapped);
        $surveyId = $this->getSurveyData($sourceType);
        $id = uniqid();
        $request = [
            "risk_variable_name" => "C_HSGradYear_" . $id,
            "source_type" => $sourceType,
            "source_id" => $surveyId,
            "is_continuous" => TRUE,
            "is_calculated" => TRUE,
            "calculated_data" => [
                "calculation_start_date" => "12/12/2016",
                "calculation_stop_date" => "12/30/2016",
                "calculation_type" => "Sum"
            ],
            "bucket_details" => $buckets
        ];
        
        return $request;
    }
    private function getEditVariableRequest($isContinuous = true, $sourceType, $overlapped = false, $id, $name)
    {
        $buckets = $this->getBucketData($isContinuous, $overlapped);
        $surveyId = $this->getSurveyData($sourceType);        
        $request = [
        "id" => $id,
        "risk_variable_name" => $name,
        "source_type" => $sourceType,
        "source_id" => $surveyId,
        "is_continuous" => TRUE,
        "is_calculated" => false,
        "calculated_data" => [
        "calculation_start_date" => "15/31/2015",
        "calculation_stop_date" => "15/31/2015",
        "calculation_type" => "Sum"
            ],
            "bucket_details" => $buckets
            ];
    
        return $request;
    }
    private function getSurveyData($sourceType){
        $survey = [
                "survey_id" => "",
                "question_id" => "",
                "factor_id" => "",
                "question_bank_id" => "",
                "ebi_profile_id" => 1,
                "isp_id" => "",
                "isq_id" => "",
                "campus_id" => ""
            ];
        
        return $survey;
    }
    private function getBucketData($isContinuous, $overlapped)
    {
        if($isContinuous) {
            $bucket =  $this->getRange($overlapped);
        }
        else {
            $bucket =  $this->getCategorical();
        }        
        return $bucket;
    }
    private function getRange($overlapped){        
        $bucket1Max = ($overlapped) ? 0.1 : 0.2;        
        $bucket = [
        [
        "bucket_value" => 1,
        "min" => 0.1,
        "max" => $bucket1Max
        ],
        [
        "bucket_value" => 2,
        "min" => 0.3,
        "max" => 0.4
        ],
        [
        "bucket_value" => 3,
        "min" => 0.5,
        "max" => 0.6
        ],
        [
        "bucket_value" => 4,
        "min" => 0.7,
        "max" => 0.8
        ],
        [
        "bucket_value" => 5,
        "min" => 0.9,
        "max" => 1.0
        ],
        [
        "bucket_value" => 6,
        "min" => 1.1,
        "max" => 1.2
        ],
        [
        "bucket_value" => 7,
        "min" => 1.3,
        "max" => 1.4
        ]
        ];
        
        return $bucket;
    }
}