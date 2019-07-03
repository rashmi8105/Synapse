<?php
require_once 'tests/api/SynapseTestHelper.php';

class HelpGetCest extends SynapseTestHelper
{

    private $organizationId = 1;

    private $helpId;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetHelp(ApiTester $I)
    {
        $I->wantTo('Get an Help Data by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('help/' . $this->organizationId);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        $I->seeResponseContains('title');
        $I->seeResponseContains('description');
        $I->seeResponseContains('link');
        $I->seeResponseContains('type');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetHelpWithInvalidOrgId(ApiTester $I)
    {
        $I->wantTo('Get an Help Data with Invalid OrgnaizationId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('help/' . mt_rand());
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Organization ID Not Found'
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }
	/* Need to be Fixed*/
    public function testGetSupportHelp(ApiTester $I)
    {
        $I->wantTo('Get an Help support Contact Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('help/' . $this->organizationId . '/supportcontact');
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('mapworks_contact');
        $I->seeResponseContains('training_site_url');
        $I->seeResponseContains('campus_name');
        $I->seeResponseContains('email');
        $I->seeResponseContains('phone');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleHelp(ApiTester $I)
    {
        $I->wantTo('Get an single Help Data by API');
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
        
        $I->sendGET('help/' . $this->organizationId . '/' . $this->helpId);
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('id');
        $I->seeResponseContains('title');
        $I->seeResponseContains('description');
        $I->seeResponseContains('link');
        $I->seeResponseContains('type');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleHelpWithInvalidOrgId(ApiTester $I)
    {
        $I->wantTo('Get an single Help with Invalid OrgnaizationId by API');
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
        
        $I->sendGET('help/' . mt_rand() . '/' . $this->helpId);
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Organization ID Not Found'
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testGetSingleHelpWithInvalidHelpId(ApiTester $I)
    {
        $I->wantTo('Get an single Help With Invalid HelpId by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('help/' . $this->organizationId . '/' . mt_rand());
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseIsJson(array(
            'error' => 'Help Not Found'
        ));
        $I->canSeeResponseCodeIs(400);
        $I->canSeeResponseIsJson();
    }

    public function testGetTicketSubdomain(ApiTester $I)
    {
        $I->wantTo('Get Ticket Subdomain by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('tickets/subdomain');
        
        $resp = json_decode($I->grabResponse());
        $I->seeResponseContains('subdomain');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }

    public function testGetZendeskSsoTokenUrl(ApiTester $I)
    {
        $I->wantTo('Get Zendesk Sso Token Url by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendGET('zendesk/sso/url');
        
        $resp = json_decode($I->grabResponse());
        $resonseData = $resp->data;
        $I->seeResponseContains('url');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
    }
}