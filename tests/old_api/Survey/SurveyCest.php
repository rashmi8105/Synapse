<?php
require_once 'tests/api/SynapseTestHelper.php';

class SurveyCest extends SynapseTestHelper
{

    private $token;

    private $surveyId = 1;
    private $lang = 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testListSurveyforInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('List survey for invalid Authentication');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('surveys/status');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testListSurvey(ApiTester $I)
    {
        $I->wantTo('List of survey for the student');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('surveys/status');
        $survey = json_decode($I->grabResponse());
        $I->seeResponseContains('survey_id');
        $I->seeResponseContains('survey_name');
        $I->seeResponseContains('survey_start_date');
        $I->seeResponseContains('survey_end_date');
        $I->seeResponseContainsJson(array(
            'cohorts' => array(
                'total_students' => 52
            )
        ));
        $I->seeResponseContainsJson(array(
            'cohorts' => array(
                'responded' => 42
            )
        ));
        $I->seeResponseContainsJson(array(
            'cohorts' => array(
                'not_responded' => 10
            )
        ));
        $I->seeResponseContainsJson(array(
            'cohorts' => array(
                'percentage' => 81
            )
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetDataForBank(ApiTester $I)
    {
        $I->wantTo('Get Survey data block for bank by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys/data?survey_id=' . $this->surveyId . '&type=bank');
        $getSurveyBlocks = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'type' => $getSurveyBlocks->data->type
        ));
        $I->seeResponseContainsJson(array(
            'total_count' => $getSurveyBlocks->data->total_count
        ));
        $I->seeResponseContainsJson(array(
            'id' => $getSurveyBlocks->data->questions[0]->id
        ));
        $I->seeResponseContainsJson(array(
            'text' => $getSurveyBlocks->data->questions[0]->text
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('type');
        $I->seeResponseContains('total_count');
        $I->seeResponseContains('text');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetDataForFactor(ApiTester $I)
    {
        $I->wantTo('Get Survey data block for factor by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys/data?survey_id=' . $this->surveyId . '&type=factors');
        $getSurveyBlocks = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'type' => $getSurveyBlocks->data->type
        ));
        $I->seeResponseContainsJson(array(
            'total_count' => $getSurveyBlocks->data->total_count
        ));
        
        $I->seeResponseContainsJson(array(
            'survey_id' => $getSurveyBlocks->data->survey_id
        ));
        
        $I->seeResponseContainsJson(array(
            'id' => $getSurveyBlocks->data->factors[0]->id
        ));
        $I->seeResponseContainsJson(array(
            'text' => $getSurveyBlocks->data->factors[0]->text
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('type');
        $I->seeResponseContains('total_count');
        $I->seeResponseContains('text');
        $I->seeResponseContains('survey_id');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetAllSurveys(ApiTester $I)
    {
        $I->wantTo('Get all surveys by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'lang_id' => $getSurveys->data->lang_id
        ));
        $I->seeResponseContainsJson(array(
            'total_count' => $getSurveys->data->total_count
        ));
        
        $I->seeResponseContainsJson(array(
            'id' => $getSurveys->data->surveys[0]->id
        ));
        $I->seeResponseContainsJson(array(
            'survey_name' => $getSurveys->data->surveys[0]->survey_name
        ));
        $I->seeResponseContains('id');
        $I->seeResponseContains('lang_id');
        $I->seeResponseContains('count');
        $I->seeResponseContains('survey_name');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetSurveysCohort(ApiTester $I)
    {
        $I->wantTo('Get all Survey cohorts by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys/cohort');
        $cohorts = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('academic_year_id' => 2));
        $I->seeResponseContains('survey_cohorts');
        if(!empty($cohorts->data->survey_cohorts)){
            $I->seeResponseContains('cohort_name');
            $I->seeResponseContains('survey_details');
            $I->seeResponseContains('survey_name');
        }
    }
    
    public function testEditWessLink(ApiTester $I){
        $I->wantTo('Edit Wess Link by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPUT('surveys/wess',[
            "wess_survey_id" => 2,
            "wess_cohort_id" => 1,
            "wess_prod_year" => 2015,
            "wess_order_id" => 11,
            "status" => "open",
            "open_date" => "2015-04-24 22:46:11",
            "close_date" => "2015-07-24 22:46:11",
            "wess_launchedflag" => true
        ]);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
    
    public function testEditWessLinkInvalid(ApiTester $I){
    	$I->wantTo('Edit Wess Link invalid by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->sendPUT('surveys/wess',[
    			"wess_survey_id" => -2,
    			"wess_cohort_id" => 1,
    			"wess_prod_year" => 201516,
    			"wess_order_id" => 11,
    			"status" => "open",
    			"open_date" => "2015-04-24 22:46:11",
    			"close_date" => "2015-04-24 22:46:11",
    			"wess_launchedflag" => true
    			]);
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }
    
    public function testlistStudentSurvey(ApiTester $I){
        $I->wantTo('get all survey for a student');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys?studentId=6&list-type=list&viewType=student');
        $getSurveys = json_decode($I->grabResponse());
        
        
        $I->seeResponseContainsJson(array(
            'survey_id' => $getSurveys->data[0]->survey_id
        ));
        $I->seeResponseContainsJson(array(
            'survey_name' => $getSurveys->data[0]->survey_name
        ));
        $I->seeResponseContainsJson(array(
            'status' => $getSurveys->data[0]->status
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testlistStudentSurveyInvalidStudent(ApiTester $I){
        $I->wantTo('get all survey for a invalid student');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys?studentid=-1&list-type=list&viewType=student');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testlistStudentSurveyInvalidType(ApiTester $I){
        $I->wantTo('get all survey with invalid type');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys?studentid=6&list-type=listss&viewType=student');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testGetPendingSurveyUpoad(ApiTester $I){
        
        $I->wantTo('get all pending survey uploads');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys/pending/1');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'upload' => false
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetPendingSurveyUpoadInvalidLogin(ApiTester $I){
    
        $I->wantTo('get all pending survey uploads without authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('surveys/pending/1');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
    
    public function testWessLinkInsert(ApiTester $I){
    
        $wessData = $this->getWessData();
        $I->wantTo('I want to insert Data into Wess');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('surveys/orders',$wessData);
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
    
    public function testWessLinkUpdate(ApiTester $I){
    
        $wessData = $this->getWessData('update');
        $I->wantTo('I want to insert Data into Wess');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPUT('surveys/orders',$wessData);
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
    
    public function getwessData($type = ""){
        
        $wessData = array();
        $wessData['survey_id_external'] =  1647;
        $wessData['customer_id'] = 1;
        $wessData['cohort_id_external'] = 1;
        $wessData['prod_year_external'] = '201617';
        $wessData['order_id_external'] = 12345;
        $wessData['map_order_key_external'] = null;
        $wessData['admin_link_external'] = "testLink";
        
        if($type = "update"){
            $wessData['survey_open_date'] = "2016-02-01 00:00:00";
            $wessData['survey_close_date'] = "2016-08-01 00:00:00";
            $wessData['survey_status'] = "open";
            $wessData['wess_launched_flag'] = 1;
        }
        $wessData =  json_encode($wessData);
        return $wessData;
         
    }
    
    
    public function testGetStudentsForCampus(ApiTester $I){
    
        $I->wantTo('get students for a campus');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys/users?campus_id=ORG1&type=student');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'synapse_id' => 6,
            'organization_id' =>1 
        ));
        
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetStudentsForInvalidCampus(ApiTester $I){
    
        $I->wantTo('get students for a Invalid campus');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys/users?campus_id=ORG2&type=student');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testGetStudentsForCampusInvalidType(ApiTester $I){
    
        $I->wantTo('get students for a campus with invalid type');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('surveys/users?campus_id=ORG2&type=students');
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    
    
    public function testSurveyQuestions(ApiTester $I){
    
        $I->wantTo('get questions for a survey');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET("surveys/$this->surveyId/questions/$this->lang");
        $getSurveys = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'organization_id' => 1,
            'survey_id' => $this->surveyId,
            'survey_questions' => array('id' => 5)
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
}