<?php
require_once 'tests/api/SynapseTestHelper.php';

class HelpCreateCest extends SynapseTestHelper
{

    private $organizationId = 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCreateHelp(ApiTester $I)
    {
        $I->wantTo('Create an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = uniqid("GoogleHelp_", true);
        $desc = 'Focus on the user and all else will follow';
        $link = 'http://google.com';
        
        $I->sendPOST('help/' . $this->organizationId, [
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateHelpWithTitleContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Create an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = '';
        $desc = 'Focus on the user and all else will follow';
        $link = 'http://google.com';
        
        $I->sendPOST('help/' . $this->organizationId, [
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
    public function testCreateHelpWithDescContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Create an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Google Help';
        $desc = '';
        $link = 'http://google.com';
        
        $I->sendPOST('help/' . $this->organizationId, [
            'title' => $title,
            'desc' => $desc,
            'link' => $link
        ]);

        $resp = json_decode($I->grabResponse());
		
        /*$I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        */
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateHelpWithLinkContstraintVoilation(ApiTester $I)
    {
        $I->wantTo('Create an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Google Help';
        $desc = 'Focus on the user and all else will follow';
        $link = '';
        
        $I->sendPOST('help/' . $this->organizationId, [
            'title' => $title,
            'desc' => $desc,
            'help_link' => $link
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'validation_error'
        ));
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateHelpWithOutHttpSchemaLink(ApiTester $I)
    {
        $I->wantTo('Create an Help Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $title = 'Yahoo Help';
        $desc = 'Focus on the user and all else will follow';
        $link = 'yahoo.com';
        
        $I->sendPOST('help/' . $this->organizationId, [
            'title' => $title,
            'description' => $desc,
            'link' => $link
        ]);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'name' => $title,
            'description' => $desc,
            'link' => 'http://' . $link
        ));
        $I->seeResponseContains('id');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }
}