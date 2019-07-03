<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class MySavedSearchCest extends SynapseTestHelper
{

    private $token;

    private $organization = 1;

    private $savedSearchName = "My saved search name ";

    private $invalidSaveSearchId = 0;

    private $savedSearch = "";

    private $saveSearchId = 0;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
        $this->savedSearchName .= rand(1000, 20000);
    }

    public function testCreateSavedSearches(ApiTester $I)
    {
        $I->wantTo('Create Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('savedsearches', [
            "organization_id" => $this->organization,
            "saved_search_name" => $this->savedSearchName,
            "search_attributes" => [
                "risk_indicator_ids" => "2",
                "intent_to_leave_ids" => "10,20",
                "group_ids" => "1,5,7",
                "referral_status" => "open",
                "contact_types" => "interaction"
            ]
        ]);
        $this->savedSearch = json_decode($I->grabResponse());
        $this->saveSearchId = $this->savedSearch->data->saved_search_id;
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_search_id');
    }

    public function testGetSavedSearches(ApiTester $I)
    {
        $I->wantTo('Get Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('savedsearches/' . $this->saveSearchId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_search_id');
        $I->seeResponseIsJson(array(
            'saved_search_id' => $this->saveSearchId
        ));
        $I->seeResponseIsJson(array(
            'saved_search_name' => $this->savedSearchName
        ));
    }

    public function testGetInvalidSavedSearches(ApiTester $I)
    {
        $I->wantTo('Get Saved Search With Invalid Saved Search Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('savedsearches/' . $this->invalidSaveSearchId);
        $I->seeResponseContains('Saved Query Not Found.');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testListMySavedSearches(ApiTester $I)
    {
        $I->wantTo('List My Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('savedsearches');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('saved_searches');
        $I->seeResponseIsJson(array(
            'saved_search_id' => $this->saveSearchId
        ));
        $I->seeResponseIsJson(array(
            'search_name' => $this->savedSearchName
        ));
    }

    public function testCancelSavedSearch(ApiTester $I)
    {
        $I->wantTo('Cancel a Saved Search by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('savedsearches/' . $this->saveSearchId);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
}