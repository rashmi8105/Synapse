<?php

use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class CreateContactCest extends SynapseTestHelper
{
	private $token;
	
	private $organization = 1;
	
	private $validStudent = 8;	
	private $invalidStudent = -1;

	private $validStaff = 1;
	private $invalidStaff = -1;
	
	private $validActivityCategory = 19;
	private $invalidActivityCategory = -1;
	
	private $validContactType = 3;
	private $invalidContactType = -1;
	private $invalidContactId = -1;
	
	private $invalidOrganizationId = -100;
	
	private $noPermissionStudent = 10;
	
	private $langId = 1;
	
	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}

	private function getContactArray($organization, $student, $staff, $activityCat, $contactType, $activityLogId = null){		
		$contact = array();
		$shareOption = array();
		$teams = array();
		$contact['organization_id'] = $organization;
		$contact['lang_id'] = $this->langId;
		$contact['person_student_id'] = $student;
		$contact['person_staff_id'] = $staff;
		$contact['reason_category_subitem_id'] = $activityCat; 
		$contact['contact_type_id'] = $contactType;
		//$contact['date_of_contact'] = "02/02/2014";
		$contact['comment'] = "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea"; 
		$contact['issue_discussed_with_student'] = true;
		$contact['high_priority_concern'] = false;
		$contact['issue_revealed_to_student'] = true;
		$contact['student_indicated_to_leave'] = false;	
		$teams = array(
		      array("id"=> "1","is_team_selected"=> true)
		      );
		$shareOption['private_share'] = true;
		$shareOption['public_share'] = true;
		$shareOption['teams_share'] = true;
		$shareOption['team_ids'] = $teams;
		$contact['share_options'][] = $shareOption;
		$contact['activity_log_id'] = $activityLogId;
		
		return json_encode($contact);
	}
	
	public function testCreateContactInvalidAuthentication(ApiTester $I)
	{	
		$contactParams = $this->getContactArray($this->organization, $this->validStudent, $this->validStaff, $this->validActivityCategory, $this->validContactType);
		
		$I->wantTo('Create contact with invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');	
		$I->amBearerAuthenticated("invalid_token");
		$I->sendPOST('contacts',$contactParams);
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
			
	}
	/* Need to be Fixed*/
	public function testCreateContactValidValues(ApiTester $I)
	{
		$contactParams = $this->getContactArray($this->organization, $this->validStudent, $this->validStaff, $this->validActivityCategory, $this->validContactType);	
		$I->wantTo('Create contact with all valid values.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);		
		$I->sendPOST('contacts',$contactParams);	
		$I->seeResponseContains('contact_id');
		$I->seeResponseContains('organization_id');
		$I->seeResponseContains('lang_id');
		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
	}
	
	public function testCreateContactInvalidStudent(ApiTester $I)
	{
		$contactParams = $this->getContactArray($this->organization, $this->invalidStudent, $this->validStaff, $this->validActivityCategory, $this->validContactType);
		$I->wantTo('Create contact with invalid student.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('contacts',$contactParams);
		$I->seeResponseContains("Person Not Found.");
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testCreateContactInvalidFaculty(ApiTester $I)
	{
		$contactParams = $this->getContactArray($this->organization, $this->validStudent, $this->invalidStaff, $this->validActivityCategory, $this->validContactType);
		$I->wantTo('Create contact with invalid faculty.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('contacts',$contactParams);
		$I->seeResponseContains("Person Not Found.");
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	public function testCreateContactInvalidActivityCategory(ApiTester $I)
	{
		$contactParams = $this->getContactArray($this->organization, $this->validStudent, $this->validStaff, $this->invalidActivityCategory, $this->validContactType);
		$I->wantTo('Create contact with invalid activity category.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('contacts',$contactParams);
		$I->seeResponseContains("Reason category not found.");
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testCreateContactInvalidContactType(ApiTester $I)
	{
		$contactParams = $this->getContactArray($this->organization, $this->validStudent, $this->validStaff, $this->validActivityCategory, $this->invalidContactType);
		$I->wantTo('Create contact with invalid contact type.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('contacts',$contactParams);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testGetContactType(ApiTester $I)
	{		
		$I->wantTo('List all Contact type.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendGET('contacts/contactTypes');
		$I->seeResponseContains('contact_type_groups');
		$I->seeResponseContains('subitems');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	
	private function getEditContactArray($data, $organization){		
		$contact = array();
		$shareOption = array();
		$teams = array();		
		$contact['contact_id'] = $data->data->contact_id; 
		$contact['person_student_id'] = $data->data->person_student_id;
		$contact['person_staff_id'] = $data->data->person_staff_id;	
		$contact['organization_id'] = $this->organization;
		$contact['contact_type_id'] = $this->validContactType;
		$contact['reason_category_subitem_id'] = $data->data->reason_category_subitem_id;	
		$contact['comment'] = "Comment updated during edit";					
		$teams = array(
		      array("id"=> "1","is_team_selected"=> true),
		      array("id"=> "2","is_team_selected"=> false),
		      array("id"=> "3","is_team_selected"=> true)
		      );
		$shareOption['private_share'] = true;
		$shareOption['public_share'] = false;
		$shareOption['teams_share'] = true;
		$shareOption['team_ids'] = $teams;
		$contact['share_options'][] = $shareOption;
		
		return json_encode($contact);
	}
	/* Need to be Fixed*/
	public function testEditContact(ApiTester $I)
	{		
		$contactParams = $this->getContactArray($this->organization,$this->validStudent, $this->validStaff, $this->validActivityCategory,  $this->validContactType);
		$I->wantTo('Edit a Contact with valid data.');			
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');	
	    $I->sendPOST('contacts',$contactParams);
		$data = json_decode($I->grabResponse());		
	
		$editParams = $this->getEditContactArray($data, $this->organization);					
		$I->sendPUT('contacts',$editParams); 
		$data = json_decode($I->grabResponse());	
		
		$I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();				
	}
	
	 public function testEditContactwithInvalidOrgId(ApiTester $I)
	{
		$I->wantTo('Edit a Contact with Invalid Organization Id.');	
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$contactParams = $this->getContactArray($this->organization, $this->validStudent, $this->validStaff, $this->validActivityCategory,  $this->validContactType);
		$I->sendPOST('contacts',$contactParams);
		$data = json_decode($I->grabResponse());		
		$I->sendPUT('contacts',
					[
						"contact_id"=>  $data->data->contact_id,
						"person_student_id"=> $data->data->person_student_id,
						"person_staff_id"=> $data->data->person_staff_id,
						"organization_id"=>$this->invalidOrganizationId,
						"reason_category_subitem_id"=> $data->data->reason_category_subitem_id,
						"comment"=> "My contact comments...."												
						
					]
				); 
		$I->seeResponseCodeIs(400);
	}
	
	
	public function testViewContact(ApiTester $I)
	{
		$I->wantTo('View Contact details');	
		$contactParams = $this->getContactArray($this->organization,$this->validStudent, $this->validStaff, $this->validActivityCategory ,  $this->validContactType);
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$I->sendPOST('contacts',$contactParams);
		$data = json_decode($I->grabResponse());		
		$I->sendGET('contacts/'.$data->data->contact_id);		
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);				
		
		$I->seeResponseContains('contact_id');		
        $I->seeResponseContains('organization_id');
		$I->seeResponseContains('lang_id');		
		$I->seeResponseContains('person_student_id');
        $I->seeResponseContains('person_staff_id');
		$I->seeResponseContains('reason_category_subitem_id');
		$I->seeResponseContains('reason_category_subitem_id');
		$I->seeResponseContains('reason_category_subitem_id');
		$I->seeResponseContains('reason_category_subitem');
		$I->seeResponseContains('contact_type_id');
		$I->seeResponseContains('contact_type_text');
		$I->seeResponseContains('date_of_contact');        			
		$I->seeResponseContains('comment');
		$I->seeResponseContains('issue_discussed_with_student');
		$I->seeResponseContains('high_priority_concern');
		$I->seeResponseContains('issue_revealed_to_student');
		$I->seeResponseContains('student_indicated_to_leave');		
		$I->seeResponseContains('share_options');
	}
	
	public function testDeleteContact(ApiTester $I)
	{
		$I->wantTo('Delete a Contact');	
		$contactParams = $this->getContactArray($this->organization, $this->validStudent, $this->validStaff, $this->validActivityCategory ,  $this->validContactType);
		$I->haveHttpHeader('Content-Type', 'application/json');		
		$I->amBearerAuthenticated( $this->token);		
		$I->sendPOST('contacts',$contactParams);
		$data = json_decode($I->grabResponse());
		$I->sendDELETE('contacts/'.$data->data->contact_id);		
		$I->seeResponseCodeIs(204);		
	}
	
	public function testCreateContactInvalidStudentPermission(ApiTester $I)
	{
		$contactParams = $this->getContactArray($this->organization, $this->noPermissionStudent, $this->validStaff, $this->validActivityCategory, $this->validContactType);
		$I->wantTo('Create contact with invalid permission student.');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->sendPOST('contacts',$contactParams);
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}
	
	public function testViewContactActivityInteraction(ApiTester $I)
	{
		$I->wantTo('View Contact Activity details');
		$contactParams = $this->getContactArray($this->organization,$this->validStudent, $this->validStaff, $this->validActivityCategory ,  $this->validContactType);
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendPOST('contacts',$contactParams);
		$data = json_decode($I->grabResponse());
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=Contact&is-interaction=true');
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
	}
	
	public function testViewContactActivity(ApiTester $I)
	{
		$I->wantTo('View Contact Activity details');
		$contactParams = $this->getContactArray($this->organization,$this->validStudent, $this->validStaff, $this->validActivityCategory ,  $this->validContactType);
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendPOST('contacts',$contactParams);
		$data = json_decode($I->grabResponse());
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=Contact&is-interaction=false');
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
	}
	
	public function testViewContactActivityRelated(ApiTester $I)
	{
		$I->wantTo('View Related Contact Activity details');
		$contactParams = $this->getContactArray($this->organization,$this->validStudent, $this->validStaff, $this->validActivityCategory ,  $this->validContactType);
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendPOST('contacts',$contactParams);
		
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=Contact&is-interaction=false');
		$lists = json_decode($I->grabResponse());
		$activityLogId = $lists->data->activities[0]->activity_log_id;
		$contactParamsRA = $this->getContactArray($this->organization,$this->validStudent, $this->validStaff, $this->validActivityCategory ,  $this->validContactType, $activityLogId);
		$I->sendPOST('contacts',$contactParamsRA);
		
		$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=all&is-interaction=false');
		$data = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
	}
		
}