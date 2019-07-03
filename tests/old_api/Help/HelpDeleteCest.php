<?php
require_once 'tests/api/SynapseTestHelper.php';

class HelpDeleteCest extends SynapseTestHelper
{

    private $organizationId = 1;

    private $helpId;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testDeleteHelpWithInvalidHelpId(ApiTester $I)
    {
        $I->wantTo('Delete Help Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendDELETE('help/' . $this->organizationId . '/' . mt_rand());
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Help Not Found'
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteHelpWithInvalidOrgId(ApiTester $I)
    {
        $I->wantTo('Delete Help Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = "GoogleHelp update";
        $desc = 'Update on Description Focus on the user and all else will follow';
        $link = 'http://google.com';
        
        $I->sendPOST('help/' . $this->organizationId, [
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        $resp = json_decode($I->grabResponse());
        $this->helpId = $resp->data->id;
        
        $I->sendDELETE('help/' . mt_rand() . '/' . $this->helpId);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Organization ID Not Found'
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteHelp(ApiTester $I)
    {
        $I->wantTo('Delete Help Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('help/' . $this->organizationId . '/' . $this->helpId);
        $I->seeResponseCodeIs(204);
    }
}