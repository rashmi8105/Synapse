<?php
require_once 'tests/api/SynapseTestHelper.php';

class HelpUpdateCest extends SynapseTestHelper
{

    private $organizationId = 1;

    private $helpId;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testUpdateHelp(ApiTester $I)
    {
        $I->wantTo('Update an Help Link by API');
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
        $I->sendPUT('help/' . $this->organizationId, [
            'id' => $this->helpId,
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testUpdateHelpWithTitleContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Update an Help Link without Title Contstraint by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = '';
        $desc = 'Focus on the user and all else will follow';
        $link = 'http://google.com';
        
        $I->sendPUT('help/' . $this->organizationId, [
            'id' => $this->helpId,
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
/*
* as per story desc can be empty. So this test case validation for not empty is invalid
*/
    public function testUpdateHelpWithDescContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Update an Help Link without Description Contstrant Voilation by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Google Help';
        $desc = '';
        $link = 'http://google.com';
        
        $I->sendPUT('help/' . $this->organizationId, [
            'id' => $this->helpId,
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
       
        $resp = json_decode($I->grabResponse());
        /*$I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        */
        $I->seeResponseCodeIs(200);

        $I->seeResponseIsJson();
    }

    public function testUpdateHelpWithLinkContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Update an Help Link without Help Link Contstrant Voilation by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Google Help';
        $desc = 'Focus on the user and all else will follow';
        $link = '';
        
        $I->sendPUT('help/' . $this->organizationId, [
            'id' => $this->helpId,
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUpdateHelpWithOutHttpSchemaLink(ApiTester $I)
    {
        $I->wantTo('Update an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Yahoo Help';
        $desc = 'Focus on the user and all else will follow';
        $link = 'yahoo.com';
        
        $I->sendPUT('help/' . $this->organizationId, [
            'id' => $this->helpId,
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'title' => $title,
            'description' => $desc,
            'link' => 'http://' . $link
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testUpdateHelpWithInvalidHelpId(ApiTester $I)
    {
        $I->wantTo('Update an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Yahoo Help';
        $desc = 'Focus on the user and all else will follow';
        $link = 'yahoo.com';
        
        $I->sendPUT('help/' . $this->organizationId, [
            'id' => mt_rand(),
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Help Not Found'
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testUpdateHelpWithInvalidOrgId(ApiTester $I)
    {
        $I->wantTo('Update an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Yahoo Help';
        $desc = 'Focus on the user and all else will follow';
        $link = 'yahoo.com';
        
        $I->sendPUT('help/' . mt_rand(), [
            'id' => $this->helpId,
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Organization ID Not Found'
        ));
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}