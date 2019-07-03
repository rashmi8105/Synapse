<?php
use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class PredefinedCest extends SynapseTestHelper
{

    private $personFacultyId = 1;
    
    private $studentSearchKey = 'student_search';
    private $surveySearchKey = 'survey_search';
    private $academicSearchKey = 'academic_update_search';
    private $activitySearchKey = 'activity_search';

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
    
    public function testGetSearchKeysStudents(ApiTester $I){
    
        $I->wantTo('Fetch the student search Keys');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=category&category=".$this->studentSearchKey."&facultyid=$this->personFacultyId");
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'search_key' => 'All_My_Student'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'My_Primary_Campus_Connection'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Class_Level'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'At_Risk'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'High_Priority_Students'
        ));
        $I->seeResponseCodeIs(200);
    }
	
    public function testGetSearchKeysSurveys(ApiTester $I){
    
        $I->wantTo('Fetch the survey search Keys');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=category&category=".$this->surveySearchKey."&facultyid=$this->personFacultyId");
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'search_key' => 'Respondents_To_Current_Survey'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Non_Respondents_To_Current_Survey'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Accessed_Current_Survey_Report'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Not_Accessed_Current_Survey_Report'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'High_Intent_To_Leave'
        ));
        $I->seeResponseCodeIs(200);
    }
    
    
    public function testGetSearchKeysAcademic(ApiTester $I){
    
        $I->wantTo('Fetch the Academic Keys');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=category&category=".$this->academicSearchKey."&facultyid=$this->personFacultyId");
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'search_key' => 'At_Risk_Of_Failure'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Missed_3_Classes'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'In-progress_Grade_Of_C_Or_Below'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Final_Grade_Of_D_Or_Below'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'In-progress_Grade_Of_D_Or_Below'
        ));
        
        $I->seeResponseContainsJson(array(
            'search_key' => 'Final_Grade_Of_D_Or_Below'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Students_With_More_Than_One_In-progress_Grade_Of_D_Or_Below'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Students_With_More_Than_One_Final_Grade_Of_D_Or_Below'
        ));
        $I->seeResponseCodeIs(200);
    }
    
    public function testGetSearchKeysActivity(ApiTester $I){
    
        $I->wantTo('Fetch the Activity Keys');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=category&category=".$this->activitySearchKey."&facultyid=$this->personFacultyId");
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'search_key' => 'Interaction_Activity'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Non-interaction_Activity'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Have_Not_Been_Reviewed'
        ));
        $I->seeResponseCodeIs(200);
    }
    
    public function testGetSearchKeysInvalidtype(ApiTester $I){
    
        $I->wantTo('search with invalid  type');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=categorsy&category=".$this->activitySearchKey."&facultyid=$this->personFacultyId");
        $I->seeResponseCodeIs(400);
    }
    
    public function testGetSearchKeysInvalidcategory(ApiTester $I){
    
        $I->wantTo('search with invalid  type');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=category&category=invalid&facultyid=$this->personFacultyId");
        $I->seeResponseCodeIs(400);
    }
    
    public function testGetSearchResult(ApiTester $I){
    
        $I->wantTo('search with sub category by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=search&sub_category_key=All_My_Student&facultyid=1");
        $I->seeResponseContainsJson(array(
            'person_id' => 1
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetSearchKeysStudentsDebug(ApiTester $I){
    
        $I->wantTo('Fetch the student search Keys');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch/debug?type=category&category=".$this->studentSearchKey."&facultyid=$this->personFacultyId");
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'search_key' => 'All_My_Student'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'My_Primary_Campus_Connection'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'Class_Level'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'At_Risk'
        ));
        $I->seeResponseContainsJson(array(
            'search_key' => 'High_Priority_Students'
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetSearchResultDebug(ApiTester $I){
    
        $I->wantTo('search with sub category by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch/debug?type=search&sub_category_key=All_My_Student&facultyid=1");
        $I->seeResponseContainsJson(array(
            'person_id' => 1
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetSearchKeysInvalidtypeDebug(ApiTester $I){
    
        $I->wantTo('search with invalid  type');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch/debug?type=categorsy&category=".$this->activitySearchKey."&facultyid=$this->personFacultyId");
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains("Invalid Type");
        $I->seeResponseIsJson();
    }
    
    public function testGetSearchResultForInteractionActivity(ApiTester $I){
    
        $I->wantTo('search with sub category For Interaction_Activity by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=search&sub_category_key=Interaction_Activity&facultyid=1");
        $I->seeResponseContainsJson(array(
            'person_id' => 1
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetSearchResultForNonInteractionActivity(ApiTester $I){
    
        $I->wantTo('search with sub category For Non Interaction_Activity by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=search&sub_category_key=Non-Interaction_Activity&facultyid=1");
        $I->seeResponseContainsJson(array(
            'person_id' => 1
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetSearchResultInvalidSubCategoryKey(ApiTester $I){
    
        $I->wantTo('search with sub category For Invalid SubCategory Key by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=search&sub_category_key=Activity&facultyid=1");

        $I->seeResponseCodeIs(400);
        $I->seeResponseContains("Invalid Query key");
        $I->seeResponseIsJson();
    }
    
    public function testGetSearchResultForHighPriorityStudents(ApiTester $I){
    
        $I->wantTo('search with sub category For High_Priority_Students by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET("predefinedsearch?type=search&sub_category_key=High_Priority_Students&facultyid=1");
        $I->seeResponseContainsJson(array(
            'person_id' => 1
        ));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetPredefinedSearchResultForCSV(ApiTester $I){
    
    	$I->wantTo('Get Predefined Search Result And Download As CSV by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->sendGET("predefinedsearch?type=search&sub_category_key=All_My_Student&facultyid=1&output-format=csv");
    	$I->seeResponseContains('You may continue to use Mapworks while your download completes. We will notify you when it is available.');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
}