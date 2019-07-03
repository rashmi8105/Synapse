<?php
use GuzzleHttp\json_decode;
use Synapse\SearchBundle\Entity\IntentToLeave;
require_once 'SynapseTestHelper.php';

class RiskIndicatorsCest extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetRiskIndicators(ApiTester $I)
    {
        $I->wantTo('Get Risk Indicators by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('risks?type=indicators');
        $res = json_decode($I->grabResponse());
        
        foreach($res->data->risk_levels as $riskLevel){
            
            $I->seeResponseContainsJson(array('risk_level' => 2));
            $I->seeResponseContainsJson(array('risk_text' => 'gray'));
            $I->seeResponseContainsJson(array('image_name' => 'risk-level-icon-gray.png'));
            $I->seeResponseContainsJson(array('color_hex' => '#cccccc'));
            break;
        }
        
        $I->seeResponseContains('risk_level');
        $I->seeResponseContains('risk_text');
        $I->seeResponseContains('image_name');
        $I->seeResponseContains('color_hex');
    }

    public function testGetRiskIndicatorsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Risk Indicators with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated('xxxxx');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('risks?type=indicators');
        $res = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }

    public function testGetIntentToLeave(ApiTester $I)
    {
        $I->wantTo('Get Intent to Leave by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('risks?type=intent_to_leave');
        $res = json_decode($I->grabResponse());
        $intentToLeave = $res->data->intent_to_leave_types;
        foreach ($intentToLeave as $intentLeave) {
            $I->seeResponseContainsJson(array(
                'id' => $intentLeave->id
            ));
            $I->seeResponseContainsJson(array(
                'text' => $intentLeave->text
            ));
            $I->seeResponseContainsJson(array(
                'image_name' => $intentLeave->image_name
            ));
            $I->seeResponseContainsJson(array(
                'color_hex' => $intentLeave->color_hex
            ));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
        }
    }

    public function testGetIntentToLeaveInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Intent To Leave with Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated('xxxxx');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('risks?type=intent_to_leave');
        $res = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }
    
    public function testGetRiskIndicatorsInvalidType(ApiTester $I)
    {
        $I->wantTo('Get Risk Indicators by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('risks?type=indicat');
        $res = json_decode($I->grabResponse());

        $I->seeResponseContains('Incorrect Filter Type.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}