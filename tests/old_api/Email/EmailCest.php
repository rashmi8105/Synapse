<?php

use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class EmailCest extends SynapseTestHelper
{
	private $token;	
	private $validStudent = 1;	
	private $invalidStudent = -1;

	private $validStaff = 1;
	private $invalidStaff = -1;
	
	private $validActivityCategory = 19;
	private $invalidActivityCategory = -1;
	
	private $organizationId = 1;	
	
	private $validTeam = 1;
	private $invalidTeam = -1;
	
	private $invalidEmailId = -1;
	
	private $unauthrizeStudent = 52;
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}

	private function getEmailArray($student, $staff, $activityCat, $activityLogId = null){		
		$email = array();
		$shareOption = array();
		$teams = array();
		$email['organization_id'] = $this->organizationId;			 
		$email['person_student_id'] = $student;
		$email['person_staff_id'] = $staff;
		$email['reason_category_subitem_id'] = $activityCat;
		$email['email_bcc_list'] = 'test@gmail.com';
		$email['email_subject'] = 'testcases test subject';
		$email['email_body'] = "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea";
		$teams = array(
		      array("id"=> "1","is_team_selected"=> true),
		      array("id"=> "2","is_team_selected"=> false),
		      array("id"=> "3","is_team_selected"=> false)
		      );
		$shareOption['private_share'] = false;
		$shareOption['public_share'] = true;
		$shareOption['teams_share'] = false;
		$shareOption['team_ids'] = $teams;
		$email['share_options'][] = $shareOption;
		
		$email["activity_log_id"] = $activityLogId;
		
		return json_encode($email);
	}
	
	private function getEmailTeamShareArray($student, $staff, $activityCat, $teamId){		
		$email = array();
		$shareOption = array();
		$teams = array();
		$email['organization_id'] = $this->organizationId;			 
		$email['person_student_id'] = $student;
		$email['person_staff_id'] = $staff;
		$email['reason_category_subitem_id'] = $activityCat;
		$email['email_bcc_list'] = 'test@gmail.com';
		$email['email_subject'] = 'testcases test subject';
		$email['email_body'] = "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea";
		$teams = array(
		      array("id"=> $teamId,"is_team_selected"=> true),
		      array("id"=> "2","is_team_selected"=> false),
		      array("id"=> "3","is_team_selected"=> false)
		      );
		$shareOption['private_share'] = false;
		$shareOption['public_share'] = false;
		$shareOption['teams_share'] = true;
		$shareOption['team_ids'] = $teams;
		$email['share_options'][] = $shareOption;
		
		return json_encode($email);
	}
	
	public function testCreateEmailTeamSharing(ApiTester $I, $scenario)
	{
	    $emailParams = $this->getEmailTeamShareArray($this->validStudent, $this->validStaff, $this->validActivityCategory, $this->validTeam);
	    $I->wantTo('Create email with team sharing all valid values.');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated($this->token);
	    $I->sendPOST('emailactivity',$emailParams);
	    $I->seeResponseContains('email_id');
	    $I->seeResponseContains("reason_category_subitem_id");
	    $I->seeResponseContains("share_options");
	    $I->seeResponseCodeIs(201);
	    $I->seeResponseIsJson();
	}
	
	public function testCreateEmailInvalidTeamSharing(ApiTester $I, $scenario)
	{
	    $emailParams = $this->getEmailTeamShareArray($this->validStudent, $this->validStaff, $this->validActivityCategory, $this->invalidTeam);
	    $I->wantTo('Create email with invalid team sharing all valid values.');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated($this->token);
	    $I->sendPOST('emailactivity',$emailParams);
	    $I->seeResponseContains('Team not found.');
		$I->seeResponseCodeIs(400);
	    $I->seeResponseIsJson();
	}
	
	public function testCreateEmailInvalidAuthentication(ApiTester $I, $scenario)
	{
		$emailParams = $this->getEmailArray($this->invalidStudent, $this->invalidStaff, $this->validActivityCategory);				
		$I->wantTo('Create email with invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('emailactivity',$emailParams);
		$data = json_decode($I->grabResponse());		
		$I->seeResponseContains('Person Not Found.');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
			
	}
	
	public function testCreateEmailValidValues(ApiTester $I, $scenario)
	{
		$emailParams = $this->getEmailArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->wantTo('Create email with all valid values.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('emailactivity',$emailParams);       
		$I->seeResponseContains('email_id');		
        $I->seeResponseContains("reason_category_subitem_id");        
        $I->seeResponseContains("share_options");
		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
	}

	public function testCreateEmailInvalidFaculty(ApiTester $I, $scenario)
	{
		$emailParams = $this->getEmailArray($this->validStudent, $this->invalidStaff, $this->validActivityCategory);
		$I->wantTo('Create email with invalid faculty.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('emailactivity',$emailParams);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testCreateEmailUnauthrizeStudent(ApiTester $I, $scenario)
	{
	    $emailParams = $this->getEmailArray($this->unauthrizeStudent, $this->validStaff, $this->validActivityCategory);
	    $I->wantTo('Create email with unauthrize student.');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated($this->token);
	    $I->sendPOST('emailactivity',$emailParams);
	    $I->seeResponseCodeIs(403);
	    $I->seeResponseIsJson();
	}
	
	
	
	public function testCreateEmailInvalidActivityCategory(ApiTester $I, $scenario)
	{
		$emailParams = $this->getEmailArray($this->validStudent, $this->validStaff, $this->invalidActivityCategory);
		$I->wantTo('Create Email with invalid activity category.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('emailactivity',$emailParams);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testViewEmail(ApiTester $I, $scenario)
	{
		$I->wantTo('View Email details');	
		$emailParams = $this->getEmailArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$I->sendPOST('emailactivity',$emailParams);
		$data = json_decode($I->grabResponse());		
		$I->sendGET('emailactivity/'.$data->data->email_id);
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
		$I->seeResponseContains('email_id');	
        $I->seeResponseContains('person_student_id');
        $I->seeResponseContains('person_staff_id');
        $I->seeResponseContains('email_subject');			
		$I->seeResponseContains('email_body');
		$I->seeResponseContains('share_options');
	}
	
	public function testViewEmailwithInvalidEmailId(ApiTester $I, $scenario)
	{       
		$I->wantTo('View a Email with Invalid Email Id');			
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);				
		$I->sendGET('emailactivity/'.$this->invalidEmailId);		
		$I->seeResponseCodeIs(400);		
	}
	
	public function testDeletEmailwithInvalidEmailId(ApiTester $I, $scenario)
	{
		$I->wantTo('Delete a Email with Invalid Email Id');			
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);				
		$I->sendDELETE('emailactivity/'.$this->invalidEmailId);		
		$I->seeResponseCodeIs(400);		
	}
	
	public function testDeleteEmail(ApiTester $I, $scenario)
	{
		$I->wantTo('Delete a Email');	
		$emailParams = $this->getEmailArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$I->sendPOST('emailactivity',$emailParams);
		$data = json_decode($I->grabResponse());
		$I->sendDELETE('emailactivity/'.$data->data->email_id);		
		$I->seeResponseCodeIs(204);		
	}
	
	public function testViewActivityEmail(ApiTester $I, $scenario)
	{
		$I->wantTo('View Activity Email details');
		$emailParams = $this->getEmailArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendPOST('emailactivity',$emailParams);
		$data = json_decode($I->grabResponse());
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=email&is-interaction=false');
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
	}
	
	public function testViewActivityEmailRelated(ApiTester $I, $scenario)
	{
		$I->wantTo('View Activity Related Email details');
		$emailParams = $this->getEmailArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendPOST('emailactivity',$emailParams);
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=email&is-interaction=false');
		$lists = json_decode($I->grabResponse());
		$activityLogId = $lists->data->activities[0]->activity_log_id;
		$emailParamsRA = $this->getEmailArray($this->validStudent, $this->validStaff, $this->validActivityCategory, $activityLogId);
		$I->sendPOST('emailactivity',$emailParamsRA);
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=all&is-interaction=false');
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
	}
}
