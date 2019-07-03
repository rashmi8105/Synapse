<?php
use Synapse\RiskBundle\Util\Constants\RiskErrorConstants;

require_once 'tests/api/SynapseTestHelper.php';

class RiskVariableBucketValidateCest extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateRiskVariableWithBoundaryOverlap(ApiTester $I)
    {
        $I->wantTo('Create risk variable bucket Validator');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true, "profile");
        $variableData['bucket_details'][1]['min'] = 0.2;
        
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(400);
    }
    
    public function testCreateRiskVariableWithOneValueEmpty(ApiTester $I)
    {
        $I->wantTo('Create risk variable bucket Validator');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true, "profile");
        $variableData['bucket_details'][1]['min'] = null;
    
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(400);
    }
    
    public function testCreateRiskVariableWithMinGrtMax(ApiTester $I)
    {
        $I->wantTo('Create risk variable bucket Validator');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true, "profile");
        $variableData['bucket_details'][6]['min'] = 1.6;
    
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(400);
    }
    
    public function testCreateRiskVariableWithDuplicates(ApiTester $I)
    {
        $I->wantTo('Create risk variable bucket Validator');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $variableData = $this->getVariableRequest(true, "profile");
        $variableData['bucket_details'][0]['max'] = 0.1;
    
        $I->sendPOST('riskvariables', $variableData);
        $I->seeResponseCodeIs(400);
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
                "calculation_start_date" => "12/31/2015",
                "calculation_stop_date" => "12/31/2015",
                "calculation_type" => "Sum"
            ],
            "bucket_details" => $buckets
        ];
        
        return $request;
    }

    private function getBucketData($isContinuous, $overlapped)
    {
        if ($isContinuous) {
            $bucket = $this->getRange($overlapped);
        } else {
            $bucket = $this->getCategorical();
        }
        return $bucket;
    }

    private function getSurveyData($sourceType)
    {
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

    private function getRange($overlapped)
    {
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