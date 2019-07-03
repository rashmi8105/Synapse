<?php
use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class SurveyBlockCest extends SynapseTestHelper
{

    private $token;

    private $langId = 1;

    private $invalidLangId = - 1;

    private $invalidId = - 1;

    private $dataid = 7;

    private $id = 1;

    private $invalidDataid = - 1;

    private $qid = 4;

    public function _before(ApiTester $I)
    {
        $I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
        		'client_id' => "3_14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884",
        		'client_secret' => "4v5p8idswhs0404owsws48gwwccc4wksw4c8s80wcocwskockg",
        		'grant_type' => "password",
        		'username' => "david.warner@gmail.com",
        		"password" => "ramesh@1974"
        		]);
        $token = json_decode($I->grabResponse());
        $this->token = $token->access_token;
    }

    public function testCreateSurveyBlockWithValidAuthentication(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Create Survey Block with valid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => "First Test survey block"
        ]);
        $surveyBlock = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'lang_id' => $surveyBlock->data->lang_id
        ));
        $I->seeResponseContainsJson(array(
            'survey_block_name' => $surveyBlock->data->survey_block_name
        ));
        $I->seeResponseContainsJson(array(
            'id' => $surveyBlock->data->id
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('lang_id');
        $I->seeResponseContains('survey_block_name');
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function testCreateSurveyBlockWithInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Create Survey Block with invalid authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testCreateSurveyBlockInvalidLang(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create Survey Block with invalid language by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->invalidLangId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        $I->seeResponseContains('Language not found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCreateSurveyBlocknvalidSurveyBlockName(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create Survey Block with invalid Survey Block Name by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => "First Test survey block"
        ]);
        $I->seeResponseContains('Survey Block already exists');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetSurveyBlockDetails(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo('Get Survey Block Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        $surveyBlockDetails = json_decode($I->grabResponse());
        $I->sendGET('surveyblocks/' . $surveyBlockDetails->data->id);
        $getSurveyBlocks = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'survey_block_name' => $getSurveyBlocks->data->survey_block_name
        ));
        $I->seeResponseContainsJson(array(
            'id' => $getSurveyBlocks->data->id
        ));
        $I->seeResponseContainsJson(array(
            'total_count' => $getSurveyBlocks->data->total_count
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('survey_block_name');
        $I->seeResponseContains('total_count');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetSurveyBlockDetailsInvalidId(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Get Survey Block Details with invalid id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('surveyblocks/' . $this->invalidId);
        $I->seeResponseContains('Invalid data block id');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testGetSurveyBlocks(ApiTester $I)
    {
        $I->wantTo("Get all survey blocks by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        $I->sendGET('surveyblocks');
        $surveyBlocks = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'survey_block_name' => $surveyBlocks->data->survey_blocks[0]->survey_block_name
        ));
        $I->seeResponseContainsJson(array(
            'id' => $surveyBlocks->data->survey_blocks[0]->id
        ));
        $I->seeResponseContainsJson(array(
            'count' => $surveyBlocks->data->count
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('survey_block_name');
        $I->seeResponseContains('count');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testDeleteSurveyBlock(ApiTester $I)
    {
        $I->wantTo("Delete Survey Block by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        $surveyBlock = json_decode($I->grabResponse());
        $I->sendDELETE('surveyblocks/' . $surveyBlock->data->id);
        $I->seeResponseCodeIs(204);
    }*/

    public function testDeleteSurveyBlockInvalidId(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo("Delete Survey Block with invalid id by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('surveyblocks/' . $this->invalidId);
        $I->seeResponseContains('Invalid data block id');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteSurveyBlockQuestion(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo("Delete Survey Block Question by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        $surveyBlock = json_decode($I->grabResponse());
        $I->sendDELETE('surveyblocks/' . $this->dataid . '/data/' . $this->qid);
        $I->seeResponseCodeIs(204);
    }

    public function testDeleteSurveyBlockQuestionInvalidId(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo("Delete Survey Block question with invalid id by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('surveyblocks/' . $this->invalidId . '/data/' . $this->dataid);
        $I->seeResponseContains('Invalid data block id or question id');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteSurveyBlockQuestionInvalidDataBlockId(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo("Delete Survey Block question with invalid data block id by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('surveyblocks/' . $this->id . '/data/' . $this->invalidDataid);
        $I->seeResponseContains('Invalid data block id or question id');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testEditSurveyBlock(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
        $I->wantTo("Edit Survey Block by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('surveyblocks', [
            "lang_id" => $this->langId,
            "survey_block_name" => "Ninth Test survey block2"
        ]);
        $surveyBlocks = json_decode($I->grabResponse());
        $I->sendPUT('surveyblocks', [
            "id" => $surveyBlocks->data->id,
            "lang_id" => $this->langId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testEditSurveyBlockInvalidLangId(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo("Edit Survey Block with invalid language id by API");
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('surveyblocks', [
            "id" => $this->id,
            "lang_id" => $this->invalidLangId,
            "survey_block_name" => uniqid("Survey_Block_", true)
        ]);
        $I->seeResponseContains('Language not found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
}
