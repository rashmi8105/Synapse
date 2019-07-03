<?php

use GuzzleHttp\json_decode;
require_once 'tests/api/SynapseTestHelper.php';

class NotesCest extends SynapseTestHelper
{
	private $token;	
	private $validStudent = 1;	
	private $invalidStudent = -1;

	private $validStaff = 1;
	private $invalidStaff = -1;
	
	private $validActivityCategory = 19;
	private $invalidActivityCategory = -1;
	
	private $organizationId = 1;	
	private $InvalidorganizationId = -1;
	
	private $invalidNoteId = -1;
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}

	private function getNoteArray($student, $staff, $activityCat, $activityLogId = null){		
		$note = array();
		$shareOption = array();
		$teams = array();		
		$note['reason_category_subitem_id'] = $activityCat; 
		$note['notes_student_id'] = $student;
		$note['staff_id'] = $staff;		
		$note['comment'] = "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea";
		$teams = array(
		      array("id"=> "1","is_team_selected"=> true),
		      array("id"=> "2","is_team_selected"=> true),
		      array("id"=> "3","is_team_selected"=> true)
		      );
		$shareOption['private_share'] = true;
		$shareOption['public_share'] = true;
		$shareOption['teams_share'] = true;
		$shareOption['team_ids'] = $teams;
		$note['share_options'][] = $shareOption;
		
		$note["activity_log_id"] = $activityLogId;
		
		return json_encode($note);
	}
	
	public function testCreateNoteInvalidAuthentication(ApiTester $I, $scenario)
	{	
        //$scenario->skip("Failed");
		$noteParams = $this->getNoteArray($this->invalidStudent, $this->invalidStaff, $this->validActivityCategory);				
		$I->wantTo('Create note with invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('notes',$noteParams);
		$data = json_decode($I->grabResponse());		
		$I->seeResponseContains('Person Not Found.');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
			
	}
	
	public function testCreateNotetValidValues(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->wantTo('Create note with all valid values.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('notes',$noteParams);       
		$I->seeResponseContains('notes_id');
		$I->seeResponseContains("notes_updated_on");
        $I->seeResponseContains("reason_category_subitem_id");        
        $I->seeResponseContains("share_options");
		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
	}

	public function testCreateNoteInvalidFaculty(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
		$noteParams = $this->getNoteArray($this->validStudent, $this->invalidStaff, $this->validActivityCategory);
		$I->wantTo('Create Note with invalid faculty.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('notes',$noteParams);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	public function testCreateNoteInvalidActivityCategory(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->invalidActivityCategory);
		$I->wantTo('Create Note with invalid activity category.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('notes',$noteParams);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	private function getEditNoteArray($data, $organizationId){		
		$note = array();
		$shareOption = array();
		$teams = array();		
		$note['notes_id'] = $data->data->notes_id; 
		$note['notes_student_id'] = $data->data->notes_student_id;
		$note['staff_id'] = $data->data->staff_id;	
		$note['organization_id'] = $this->organizationId;
		$note['reason_category_subitem_id'] = $data->data->reason_category_subitem_id;	
		$note['comment'] = "Comment updated during edit";					
		$teams = array(
		      array("id"=> "1","is_team_selected"=> true),
		      array("id"=> "2","is_team_selected"=> false),
		      array("id"=> "3","is_team_selected"=> true)
		      );
		$shareOption['private_share'] = true;
		$shareOption['public_share'] = false;
		$shareOption['teams_share'] = true;
		$shareOption['team_ids'] = $teams;
		$note['share_options'][] = $shareOption;
		
		return json_encode($note);
	}
	
	public function testEditNote(ApiTester $I, $scenario)
	{		
//        $scenario->skip("Errored");
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->wantTo('Edit a Note with valid data.');			
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');	
	    $I->sendPOST('notes',$noteParams);
		$data = json_decode($I->grabResponse());		
		$editParams = $this->getEditNoteArray($data, $this->organizationId);					
		$I->sendPUT('notes',$editParams);         		
		$I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();				
	}
	
	/*
	public function testEditNotewithInvalidOrgId(ApiTester $I)
	{
		$I->wantTo('Edit a Note with Invalid Organization Id.');	
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->sendPOST('notes',$noteParams);
		$data = json_decode($I->grabResponse());		
		$I->sendPUT('notes',
					[
						"notes_id"=>  $data->data->notes_id,
						"notes_student_id"=> $data->data->notes_student_id,
						"staff_id"=> $data->data->staff_id,
						"organization_id"=>$this->InvalidorganizationId,
						"reason_category_subitem_id"=> $data->data->reason_category_subitem_id,
						"comment"=> "My notes....",												
						"comment"=> "My notes....",												
					]
				); 
		$I->seeResponseCodeIs(400);
	}
	*/
	
	public function testEditNotewithInvalidNoteId(ApiTester $I, $scenario)
	{
//        $scenario->skip("Errored");
		$I->wantTo('Edit a Note with Invalid Note Id.');	
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->sendPOST('notes',$noteParams);
		$data = json_decode($I->grabResponse());
		$I->sendPUT('notes',
					[
						"notes_id"=> $this->invalidNoteId,
						"notes_student_id"=>$data->data->notes_student_id,
						"staff_id"=>$data->data->staff_id,
						"organization_id"=>$this->organizationId,
						"reason_category_subitem_id"=>$data->data->reason_category_subitem_id,
						"comment"=> "My notes....",												
					]
				); 
		$I->seeResponseCodeIs(400);
	}
	
	public function testViewNote(ApiTester $I, $scenario)
	{
//        $scenario->skip("Errored");
		$I->wantTo('View Note details');	
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$I->sendPOST('notes',$noteParams);
		$data = json_decode($I->grabResponse());		
		$I->sendGET('notes/'.$data->data->notes_id);		
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);				
		$I->seeResponseContains('notes_id');		
        $I->seeResponseContains('notes_student_id');
        $I->seeResponseContains('staff_id');
        $I->seeResponseContains('organization_id');			
		$I->seeResponseContains('comment');
		$I->seeResponseContains('share_options');
	}
	
	public function testViewNotewithInvalidNoteId(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
		$I->wantTo('View a Note with Invalid Note Id');			
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);				
		$I->sendGET('notes/'.$this->invalidNoteId);		
		$I->seeResponseCodeIs(400);		
	}
	
	public function testDeletNotewithInvalidNoteId(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
		$I->wantTo('Delete a Note with Invalid Note Id');			
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);				
		$I->sendDELETE('notes/'.$this->invalidNoteId);		
		$I->seeResponseCodeIs(400);		
	}
	
	public function testDeleteNote(ApiTester $I, $scenario)
	{
//        $scenario->skip("Errored");
		$I->wantTo('Delete a Note');	
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$I->sendPOST('notes',$noteParams);
		$data = json_decode($I->grabResponse());
		$I->sendDELETE('notes/'.$data->data->notes_id);		
		$I->seeResponseCodeIs(204);		
	}
	
	public function testViewActivityNote(ApiTester $I, $scenario)
	{
		$I->wantTo('View Activity Note details');
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendPOST('notes',$noteParams);
		$data = json_decode($I->grabResponse());
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=note&is-interaction=false');
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);		
	}
	
	public function testViewActivityNoteRelated(ApiTester $I, $scenario)
	{
		$I->wantTo('View Related Activity Note details');
		$noteParams = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory);
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendPOST('notes',$noteParams);
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=note&is-interaction=false');		
		$lists = json_decode($I->grabResponse());
		$activityLogId = $lists->data->activities[0]->activity_log_id;
		$noteParamsRA = $this->getNoteArray($this->validStudent, $this->validStaff, $this->validActivityCategory, $activityLogId);
		$I->sendPOST('notes',$noteParamsRA);
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=all&is-interaction=false');
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
	}
}
