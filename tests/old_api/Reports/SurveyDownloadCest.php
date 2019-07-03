<?php
require_once 'tests/api/SynapseTestHelper.php';

class SurveyDownloadCest extends SynapseTestHelper
{

    private $cohortId = 1;

    private $invalidCohortId = 200;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testSurveyDataDownloadInvalidCohort(ApiTester $I)
    {
        $I->wantTo('Test Survey Data download with invalid cohort id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('reports/cohortssurveyreport?cohort_id=' . $this->invalidCohortId);
        $response = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
    }

    public function testSurveyDataDownload(ApiTester $I)
    {
        $I->wantTo('Test Survey Data download');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('reports/cohortssurveyreport?cohort_id=' . $this->cohortId);
        $response = json_decode($I->grabResponse());
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('You may continue to use Mapworks while your download completes. We will notify you when it is available.');
    }

    public function testSurveyKeyDownload(ApiTester $I)
    {
        $I->wantTo('Test Survey key download with invalid organization id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('reports/cohortkey?cohort_id=' . $this->cohortId);
        $response = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
    }
}