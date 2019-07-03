<?php
require_once 'tests/api/SynapseTestHelper.php';

class RiskModelCreateCest extends SynapseTestHelper
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
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }
    
    
    public function testCreateRiskGroupInvaliddate(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $start = new \DateTime();
        $start->sub(new DateInterval('P10D'));
    
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
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    
    }
    
    
    public function testCreateRiskGroupWithoutName(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
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
          
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    
        
    }

    public function testCreateRiskGroupDuplicateNmae(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
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
        $name = "Risk Model_1234_A";
        $I->sendPOST('riskmodels', array(
            'risk_model_name' => $name,
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
        
        $I->sendPOST('riskmodels', array(
            'risk_model_name' => $name,
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => $this->getRequestIndicator()
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        
        
    }

    public function testCreateRiskGroupOverLap1(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
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
            'risk_model_name' => 'Model-A',
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => array(
                0 => array(
                    'name' => 'red2low',
                    'min' => 1.1,
                    'max' => 1.2
                ),
                1 => array(
                    'name' => 'red2high',
                    'min' => 1.3,
                    'max' => 1.5
                ),
                2 => array(
                    'name' => 'redlow',
                    'min' => 1.5,
                    'max' => 1.6
                ),
                3 => array(
                    'name' => 'redhigh',
                    'min' => 1.7,
                    'max' => 1.8
                ),
                4 => array(
                    'name' => 'yellowlow',
                    'min' => 1.9,
                    'max' => 2
                ),
                5 => array(
                    'name' => 'yellowhigh',
                    'min' => 2.1,
                    'max' => 2.2
                ),
                6 => array(
                    'name' => 'greenlow',
                    'min' => 2.5,
                    'max' => 2.7
                )
            )
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }

    public function testCreateRiskGroupOverLap2(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
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
            'risk_model_name' => 'Model-A',
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => array(
                0 => array(
                    'name' => 'red2low',
                    'min' => 1.1,
                    'max' => 1.2
                ),
                1 => array(
                    'name' => 'red2high',
                    'min' => 1.3,
                    'max' => 1.4
                ),
                2 => array(
                    'name' => 'redlow',
                    'min' => 1.6,
                    'max' => 1.5
                ),
                3 => array(
                    'name' => 'redhigh',
                    'min' => 1.7,
                    'max' => 1.8
                ),
                4 => array(
                    'name' => 'yellowlow',
                    'min' => 1.9,
                    'max' => 2
                ),
                5 => array(
                    'name' => 'yellowhigh',
                    'min' => 2.1,
                    'max' => 2.2
                ),
                6 => array(
                    'name' => 'greenlow',
                    'min' => 2.5,
                    'max' => 2.7
                )
            )
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }

    public function testCreateRiskGroupOverLap3(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
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
            'risk_model_name' => 'Model-A',
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => array(
                0 => array(
                    'name' => 'red2low',
                    'min' => 1.1,
                    'max' => 1.2
                ),
                1 => array(
                    'name' => 'red2high',
                    'min' => 1.3,
                    'max' => 1.4
                ),
                2 => array(
                    'name' => 'redlow',
                    'min' => 1.5,
                    'max' => 1.6
                ),
                3 => array(
                    'name' => 'redhigh',
                    'min' => 1.7,
                    'max' => 1.8
                ),
                4 => array(
                    'name' => 'yellowlow',
                    'min' => 1.9,
                    'max' => 2.0
                ),
                5 => array(
                    'name' => 'yellowhigh',
                    'min' => 2.1,
                    'max' => 2.5
                ),
                6 => array(
                    'name' => 'greenlow',
                    'min' => 2.4,
                    'max' => 2.7
                )
            )
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }

    public function testCreateRiskGroupOverLap4(ApiTester $I)
    {
        $I->wantTo('Create Risk Group by API');
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
            'risk_model_name' => 'Model-A',
            'calculation_start_date' => $start->format('m/d/Y'),
            'calculation_stop_date' => $end->format('m/d/Y'),
            'enrollment_end_date' => $enroll->format('m/d/Y'),
            'model_state' => 'Unassigned',
            'risk_indicators' => array(
                0 => array(
                    'name' => 'red2low',
                    'min' => 1.1,
                    'max' => 1.2
                ),
                1 => array(
                    'name' => 'red2high',
                    'min' => 1.2,
                    'max' => 1.5
                ),
                2 => array(
                    'name' => 'redlow',
                    'min' => 1.5,
                    'max' => 1.6
                ),
                3 => array(
                    'name' => 'redhigh',
                    'min' => 1.7,
                    'max' => 1.8
                ),
                4 => array(
                    'name' => 'yellowlow',
                    'min' => 1.9,
                    'max' => 2
                ),
                5 => array(
                    'name' => 'yellowhigh',
                    'min' => 2.1,
                    'max' => 2.2
                ),
                6 => array(
                    'name' => 'greenlow',
                    'min' => 2.5,
                    'max' => 2.7
                )
            )
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        
        $I->seeResponseContains('id');
    }

    public function getRequestIndicator()
    {
        return array(
            0 => array(
                'name' => 'red2',
                'min' => 1.1,
                'max' => 1.2
            ),
            1 => array(
                'name' => 'red',
                'min' => 1.3,
                'max' => 1.4
            )/*,
            2 => array(
                'name' => 'redlow',
                'min' => 1.5,
                'max' => 1.6
            ),
            3 => array(
                'name' => 'redhigh',
                'min' => 1.7,
                'max' => 1.8
            ),
            4 => array(
                'name' => 'yellowlow',
                'min' => 1.9,
                'max' => 2
            ),
            5 => array(
                'name' => 'yellowhigh',
                'min' => 2.1,
                'max' => 2.2
            ),
            6 => array(
                'name' => 'greenlow',
                'min' => 2.5,
                'max' => 2.7
            )*/
        );
    }
}