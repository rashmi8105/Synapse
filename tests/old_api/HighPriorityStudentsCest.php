<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';
class HighPriorityStudentsCest extends SynapseTestHelper
{
	private $token;

	public function _before(ApiTester $I)
    {
       $this->token = $this->authenticate($I);
    }
	public function testGetPriorityStudents(ApiTester $I, $scenario)
  {
        //$scenario->skip("Errored");
        $I->wantTo('Get Priority Students by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('priorityStudents');
		$res = json_decode($I->grabResponse());				
		$totalstudents = $res->data->total_students;
		$totalstudents_data = 0;
		$highpriority_data = 0;
		/*foreach($res->data->risk_levels as $risk_levels)
		{
			$totalstudents_data += $risk_levels->total_students;
			if($risk_levels->risk_level == 'red' || $risk_levels->risk_level == 'red2')
			{
				$highpriority_data += $risk_levels->total_students;
			}
		}*/			
		/*$highprioritystudents = $res->data->total_high_priority_students;											
		
		$I->seeResponseContainsJson(array('total_students' =>$totalstudents));
        $I->seeResponseContainsJson(array('total_students' =>$totalstudents_data));
		$I->seeResponseContainsJson(array('total_high_priority_students' =>$highprioritystudents));*/
        $I->seeResponseContains('personid');
        $I->seeResponseContains('total_students');
        /*$I->seeResponseContains('total_high_priority_students');
        $I->seeResponseContains('risk_levels');        
		$I->seeResponseContains('risk_level');
		$I->seeResponseContains('risk_percentage');  
		$I->seeResponseContains('color_value'); */ 
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}
	
	public function testGetPriorityStudentsInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Create a Person with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated('xxxxx');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('priorityStudents');
		$res = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
	}

	
	
	public function testGetPriorityStudentsByRiskLevel(ApiTester $I)
    {
        $I->wantTo('Get Total Students List by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('priorityStudents?filter=risk-level');
		$res = json_decode($I->grabResponse());			
       
        $I->seeResponseContains('personid');
        $I->seeResponseContains('total_students_list');
        $I->seeResponseContains('student_id');
        $I->seeResponseContains('student_first_name');        
		$I->seeResponseContains('student_last_name'); 
		$I->seeResponseContains('student_risk_status'); 
		$I->seeResponseContains('student_intent_to_leave'); 
		$I->seeResponseContains('student_cohorts'); 
		$I->seeResponseContains('student_logins'); 
		$I->seeResponseContains('last_activity'); 
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}	
	
	public function testGetPriorityStudentsByRiskLevelInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Create a Person with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated('xxxxx');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('priorityStudents?filter=risk-level');
		$res = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
	}
	

	public function testGetPriorityStudentsByPriority(ApiTester $I)
    {
        $I->wantTo('Get High Priority Students List by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);       
	    $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('priorityStudents?filter=priority');
		$res = json_decode($I->grabResponse());		
        
        $I->seeResponseContains('personid');
        $I->seeResponseContains('high_priority_students_list');
      /*$I->seeResponseContains('student_id');
        $I->seeResponseContains('student_first_name');        
		$I->seeResponseContains('student_last_name'); 
		$I->seeResponseContains('student_risk_status'); 
		$I->seeResponseContains('student_intent_to_leave'); 
		$I->seeResponseContains('student_cohorts'); 
		$I->seeResponseContains('student_logins'); 
		$I->seeResponseContains('student_last_activity');*/ 
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}
	
	public function testGetPriorityStudentsByPriorityInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Create a Person with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated('xxxxx');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('priorityStudents?filter=priority');
		$res = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
	}
	
	public function testGetPriorityStudentsByRiskLevelToCSV(ApiTester $I)
	{
	    $I->wantTo('Get Total Students List To CSV by API');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->haveHttpHeader('Accept', 'application/json');
	    $I->sendGET('priorityStudents?filter=risk-level&output-format=csv');
	    $res = json_decode($I->grabResponse());
	    $I->seeResponseCodeIs(200);
	    $I->seeResponseIsJson();
	}
	
	public function testGetPriorityStudentsByPriorityCSV(ApiTester $I)
	{
	    $I->wantTo('Create a Person with CSV by API');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->haveHttpHeader('Accept', 'application/json');
	    $I->sendGET('priorityStudents?filter=priority');
	    $res = json_decode($I->grabResponse());
	    $I->seeResponseCodeIs(200);
	    $I->seeResponseIsJson();
	}
	
	public function testGetPriorityStudentsByLevelAsRiskLevelToCSV(ApiTester $I)
	{
	    $I->wantTo('Get Total Students List By Level As Risk Level To CSV by API');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated( $this->token);
	    $I->haveHttpHeader('Accept', 'application/json');
	    $I->sendGET('priorityStudents?filter=&level=red2&output-format=csv');
	    $res = json_decode($I->grabResponse());
	    $I->seeResponseCodeIs(200);
	    $I->seeResponseIsJson();
	}
}
