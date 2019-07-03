<?php
require_once 'tests/api/SynapseTestHelper.php';

class RiskModelUpdateCest extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
	/* Need to be Fixed
    public function testUpdateRiskGroup(ApiTester $I)
    {
        $I->wantTo('Update Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $start = new \DateTime();
        $start->add(new DateInterval('P2D'));
        
        $end = new \DateTime();
        $end->add(new DateInterval('P7D'));
        
        $enroll = new \DateTime();
        $enroll->add(new DateInterval('P6D'));
        
        $I->sendPOST('riskmodels', array(
            'risk_model_name' => 'Model' . uniqid(),
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
        
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
        

        $I->sendPUT('riskmodels', array(
            'id' => $id,
            'risk_model_name' => 'Model' . uniqid(),
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }
	*/
    
    
    public function testUpdateRiskGroupInvalidId(ApiTester $I)
    {
        $I->wantTo('Update Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $start = new \DateTime();
        $start->add(new DateInterval('P2D'));
    
        $end = new \DateTime();
        $end->add(new DateInterval('P7D'));
    
        $enroll = new \DateTime();
        $enroll->add(new DateInterval('P6D'));
    
       
    
        $I->sendPUT('riskmodels', array(
            'id' => -1,
            'risk_model_name' => 'Model' . uniqid(),
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
    
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    
        $I->seeResponseContains('id');
    }
    

    public function testUpdateRiskGroupStartEndDateValidation(ApiTester $I)
    {
        $I->wantTo('Update Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $start = new \DateTime();
        $start->add(new DateInterval('P2D'));
    
        $end = new \DateTime();
        $end->add(new DateInterval('P7D'));
    
        $enroll = new \DateTime();
        $enroll->add(new DateInterval('P6D'));
    
        $I->sendPOST('riskmodels', array(
            'risk_model_name' => 'Model' . uniqid(),
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
    
        $resp = json_decode($I->grabResponse());
        $id = $resp->data->id;
    
    
        $start = new \DateTime();
        $start->add(new DateInterval('P7D'));
    
        $end = new \DateTime();
        $end->sub(new DateInterval('P2D'));
    
        $I->sendPUT('riskmodels', array(
            'id' => $id,
            'risk_model_name' => 'Model' . uniqid(),
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
    
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    
        $I->seeResponseContains('id');
    }
    
    public function testUpdateRiskGroupNonEditable(ApiTester $I)
    {
        $I->wantTo('Update Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $start = new \DateTime();
        $start->add(new DateInterval('P2D'));
    
        $end = new \DateTime();
        $end->add(new DateInterval('P7D'));
    
        $enroll = new \DateTime();
        $enroll->add(new DateInterval('P6D'));
        $I->sendGET("riskmodels?status=Active");
        $resp = json_decode($I->grabResponse());
        $modelid = 0;
        foreach ($resp->data->risk_models as $models)
        {
            if($models->model_name == 'RiskModel_TestCase_A')
            {
                $modelid = $models->id;
                break;
            }
        }
        $indicator  = $this->getRequestIndicator();
       
        $indicator[1]['max'] = 3.0;
        $I->sendPUT('riskmodels', array(
            'id' => $modelid,
            'risk_model_name' => 'RiskModel_TestCase_A',
            'calculation_start_date' =>'05/31/2015',
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' =>$indicator
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    
       
    }
    
    
    public function testUpdateRiskGroupNonEditableCutpoint(ApiTester $I)
    {
        $I->wantTo('Update Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
       
        $I->sendGET("riskmodels?status=Active");
        $resp = json_decode($I->grabResponse());
        $modelid = 0;
        foreach ($resp->data->risk_models as $models)
        {
            if($models->model_name == 'RiskModel_TestCase_A')
            {
                $modelid = $models->id;
                break;
            }
        }
        $indicator  = $this->getRequestIndicator();
         
        $indicator[3]['max'] = 6.9;
        $I->sendPUT('riskmodels', array(
            'id' => 2,
            'risk_model_name' => 'RiskModel_TestCase_A',
            'calculation_start_date' => '05/10/2015',
            'calculation_stop_date' => '05/11/2016',
            'enrollment_end_date' => '12/20/2015',
            'model_state' => 'Unassigned',
            'risk_indicators' => $indicator
        ));
    
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    
    
    }
    
  
    
    public function getRequestIndicator()
    {
        return array(
            0 => array(
                'name' => 'red',
                'min' => 1.1,
                'max' => 1.2
            ),
            1 => array(
                'name' => 'red2',
                'min' => 1.3,
                'max' => 1.4
            ),
            2 => array(
                'name' => 'yellow',
                'min' => 1.5,
                'max' => 1.6
            ),
            3 => array(
                'name' => 'green',
                'min' => 1.7,
                'max' => 1.8
            )
        );
    }
}