<?php
use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class StudentSurveyCest extends SynapseTestHelper
{

    private $token;

    private $personStudentId = 6;

    private $surveyId = 1;

    private $invalidPersonStudentId = - 1;

    private $invalidSurveyId = - 1;

    private $secondSurveyId = 2;


    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testListStudentSurveys(ApiTester $I)
    {
        $I->wantTo('List Student surveys by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/surveys');
        $surveys = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'student_id' => $surveys->data->student_id
        ));
        $I->seeResponseContains('survey_id');
        $I->seeResponseContains('survey_name');
        $I->seeResponseContains('open_date');
        $I->seeResponseContains('organization_id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testListStudentIsqQuestions(ApiTester $I)
    {
        $I->wantTo('List Student ISQ questions by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/surveys/' . $this->surveyId . '/isq');
        $isqs = json_decode($I->grabResponse());
       
        $I->seeResponseContainsJson(array(
            'survey_id' => $isqs->data->survey_id
        ));
        $I->seeResponseContains('survey_id');
        $I->seeResponseContains('survey_name');
        $I->seeResponseContains('id');
        $I->seeResponseContains('name');
        $I->seeResponseContains('response');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }


    public function testListStudentSurveysInvlaidAuthentication(ApiTester $I)
    {
        $I->wantTo('List Student surveys by API with invalid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/surveys');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListStudentIsqQuestionsInvlaidAuthentication(ApiTester $I)
    {
        $I->wantTo('List Student ISQ questions by API with invalid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/surveys/' . $this->surveyId . '/isq');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListStudentSurveysInvalidStudent(ApiTester $I)
    {
        $I->wantTo('List Student surveys by API with invalid student');
        $I->sendGET('students/' . $this->invalidPersonStudentId . '/surveys');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListStudentIsqQuestionsInvalidStudent(ApiTester $I)
    {
        $I->wantTo('List Student ISQ questions by API with invalid student');
        $I->sendGET('students/' . $this->invalidPersonStudentId . '/surveys/' . $this->surveyId . '/isq');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListStudentIsqQuestionsInvalidSurvey(ApiTester $I)
    {
        $I->wantTo('List Student ISQ questions by API with invalid survey');
        $I->sendGET('students/' . $this->personStudentId . '/surveys/' . $this->invalidSurveyId . '/isq');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListStudentSurveysCompare(ApiTester $I)
    {
        $I->wantTo('List Student survey compare by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/surveys/compare?survey_ids=' . $this->surveyId . ',' . $this->secondSurveyId);
        $surveyCompare = json_decode($I->grabResponse());
        
        $I->seeResponseContains('survey_id');
        $I->seeResponseContains('response_decimal');
        $I->seeResponseContains('question_text');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testListStudentSurveysCompareInvalidStudent(ApiTester $I)
    {
        $I->wantTo('List Student survey compare by API with invalid student');
        $I->sendGET('students/' . $this->invalidPersonStudentId . '/surveys/compare?survey_ids=' . $this->surveyId . ',' . $this->secondSurveyId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListStudentSurveysCompareInvalidSurvey(ApiTester $I)
    {
        $I->wantTo('List Student survey compare by API with invalid survey');
        $I->sendGET('students/' . $this->personStudentId . '/surveys/compare?survey_ids=' . $this->invalidSurveyId . ',' . $this->secondSurveyId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListStudentSurveysCompareInvlaidAuthentication(ApiTester $I)
    {
        $I->wantTo('List Student survey compare by API with invalid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/surveys/compare?survey_ids=' . $this->surveyId . ',' . $this->secondSurveyId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
}
