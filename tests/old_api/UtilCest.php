<?php
require_once 'SynapseTestHelper.php';

class UtilCest extends SynapseTestHelper
{

    private $token;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    /* Getting timezone - Completed & Tested */
    public function testGetTimezones(ApiTester $I)
    {
        $I->wantTo('Get timezones Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('timezone');
        $I->seeResponseContains('timezone_name');
        $I->seeResponseContains('timezone');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function  testGetTimezonesWithoutAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Time Zones Details with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('timezone');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetReasonCategories(ApiTester $I)
    {
        $I->wantTo('Get Reason Categories by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('reasonCategories');
        $I->seeResponseContains('subitems');
        $I->seeResponseContains('category_groups');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetReasonCategoriesWithoutAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Reason Categories with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('reasonCategories');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }


    public function testGetTimezonesInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Get timezones Details with Invalid Authentication by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('timezone');

    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }


}
?>