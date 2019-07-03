<?php
require_once 'tests/api/SynapseTestHelper.php';
class MyTeamsCest extends SynapseTestHelper {

private $team = "";
private $contact = "";
private $person = 1;
private $organization = 1;
private $validStudent = 2;	
private $validStaff = 1;
private $validActivityCategory = 1;
private $validContactType = 3;
private $teamMemberIds = "1,2";
private $personFirstName = "Ramesh";
private $personLastName = "Kumhar";
private $team_name = "Test case team ";
private $invalidTeam = 0;
private $lang =1;
private $validActivitySubCategory = 1;

	public function _before(ApiTester $I) {
		$this->token = $this->authenticate ( $I );
		$this->team_name .= rand(1,1000);
	}

	public function testCreateTeam(ApiTester $I, $scenario)
	{
		//$scenario->skip("PHP Fatal error:  Call to a member function getId() on a non-object in src/Synapse/CoreBundle/Service/Impl/ContactsService.php on line 157");
		$I->wantTo('Create a team for the Logged In User as Leader by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$this->team = $this->newTeamCreate($I);
	}

	public function testCreateContactAsActivity(ApiTester $I, $scenario)
	{
		//$scenario->skip('PHP Fatal error:  Call to a member function getId() on a non-object in src/Synapse/CoreBundle/Service/Impl/ContactsService.php on line 157');
		//$contactParams = $this->getContactArray($this->organization,$this->validStudent,$this->validStaff, $this->validActivityCategory, $this->validContactType);	
		
		$I->wantTo('Create a contact as an activity by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$this->contact = $this->newContactCreate($I);
	}

   public function testGetMyTeams(ApiTester $I, $scenario)
   {
	   //$scenario->skip("Errored");
		$I->wantTo('Get My Team List for the Logged In User by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		//$newTeam = $this->team;
		$newTeam = $this->newTeamCreate($I);
		$I->sendGET('teams');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$teams = json_decode($I->grabResponse());
		$I->seeResponseContains('person_id');
		$I->seeResponseContains('team_ids');
		
	}
	
	public function testGetTeamMembers(ApiTester $I, $scenario)
	{
		//$scenario->skip("Errored");
		$I->wantTo('Get Team Members for the Logged In User and Given Team Id by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		//$newTeam = $this->team;
		$newTeam = $this->newTeamCreate($I);
		$I->sendGET('teams/'.$newTeam->data->team->id);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$teams = json_decode($I->grabResponse());
		$I->seeResponseIsJson ( array (
		'person_id' => $this->person 
		));
		$I->seeResponseIsJson ( array (
		'team_id' => $newTeam->data->team->id 
		) );
		$I->seeResponseIsJson (array(
		'team_members' => array('id' => $this->person)) 
		);
	}

	public function  testGetTeamMembersWithInvalidTeam(ApiTester $I)
	{
		$I->wantTo('Get Teams Members with Invalid Team Id by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('teams/'.$this->invalidTeam);
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}

	public function testGetMyTeamsRecentActivities(ApiTester $I, $scenario)
	{
		//$scenario->skip("Errored");
		$I->wantTo('Get My Teams Recent Activities Count for the Logged In User by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		//$newTeam = $this->team;
		//$contact = $this->contact;
		$newTeam = $this->newTeamCreate($I);
		$contact = $this->newContactCreate($I);
		$I->sendGET('teams/recent/activities?filter=month');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$teams = json_decode($I->grabResponse());
		$I->seeResponseIsJson (array(
		'person_id' => $this->person 
		) );
		$I->seeResponseIsJson (array(
		'recent_activities' => array('team_id' => $newTeam->data->team->id)) 
		);
		$I->seeResponseIsJson (array(
		'recent_activities' => array('team_name' => $this->team_name)) 
		);
	}

	public function testGetMyTeamsActivitiesInDetail(ApiTester $I, $scenario)
	{
        //$scenario->skip("Failed");
		$I->wantTo('Get My Teams Activities in Detail for the Logged In User by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$newTeam = $this->newTeamCreate($I);
		$contact = $this->newContactCreate($I);
		//$newTeam = $this->team;
		//$contact = $this->contact;
		
		$I->sendGET('teams/activitiesdetail?team-id='.$newTeam->data->team->id.'&team-member-id='.$this->teamMemberIds.'&activity_type=all&filter=month&start-date=&end-date=');
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson ( array (
		'person_id' => $this->person 
		) );
		$I->seeResponseContainsJson ( array (
		'team_id' => $newTeam->data->team->id 
		) );
		$I->seeResponseContainsJson ( array (
		'activity_type' => 'All' 
		) );
		$I->seeResponseContains('team_members_activities');
		/*$I->seeResponseContainsJson (array(
		'team_members_activities' => array('team_member_id' => $this->person)) 
		);
		$I->seeResponseContainsJson (array(
		'team_members_activities' => array('student_id' => 2)) 
		);
		$I->seeResponseContainsJson ( array(
		'team_members_activities' => array('activity_type' => 'contact')) 
		);
		$I->seeResponseContainsJson (array(
		'team_members_activities' => array('activity_id' => $contact->data->contact_id)) 
		);*/	
	}

	public function testGetMyTeamsActivitiesInDetailInvalidCustomDate(ApiTester $I, $scenario)
	{
        //$scenario->skip("Errored");
		$I->wantTo('Get My Teams Activities in Detail With Invalid Custom Date for the Logged In User by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		//$newTeam = $this->team;
		//$contact = $this->contact;
		$newTeam = $this->newTeamCreate($I);
		$contact = $this->newContactCreate($I);
		$I->sendGET('teams/activitiesdetail?team-id='.$newTeam->data->team->id.'&team-member-id='.$this->teamMemberIds.'&activity_type=all&filter=custom&start-date=2015-01-06&end-date=2015-01-05');
		$I->seeResponseIsJson();
		$I->seeResponseContains ("Start date cannot be grater than end date");
        $I->seeResponseCodeIs(400);
		//$I->sendDELETE('teams/delete/'.$newTeam->data->team->id);
		//$I->sendDELETE('contacts/'.$contact->data->contact_id);
	}

	private function getContactArray($organization, $student, $staff, $activityCat, $contactType){		
		$contact = array();
		$shareOption = array();
		$teams = array();
		$contact['organization_id'] = $organization;
		$contact['person_student_id'] = $student;
		$contact['person_staff_id'] = $staff;
		$contact['reason_category_subitem_id'] = $activityCat; 
		$contact['contact_type_id'] = $contactType;
		$contact['date_of_contact'] = date('m/d/Y');
		$contact['comment'] = "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea"; 
		$contact['issue_discussed_with_student'] = true;
		$contact['high_priority_concern'] = false;
		$contact['issue_revealed_to_student'] = true;
		$contact['student_indicated_to_leave'] = false;	
		$teams = array(
			array("id"=> "1","is_team_selected"=> true),
			array("id"=> "1","is_team_selected"=> true),
			array("id"=> "1","is_team_selected"=> true)
		);
		$shareOption['private_share'] = true;
		$shareOption['public_share'] = true;
		$shareOption['teams_share'] = true;
		$shareOption['team_ids'] = $teams;
		$contact['share_options'][] = $shareOption;
		return json_encode($contact);
	}
	
	private function newTeamCreate($I){
	    
	    $I->sendPOST('teams/new', [
	    		"staff" => [
	    		[
	    		"person_id" => $this->person,
	    		"is_leader" => "1",
	    		"first_name" => $this->personFirstName,
	    		"last_name" => $this->personLastName,
	    		"action" => "update",
	    		"role"=> "Leader",
	    		"selectedIndex" => 0
	    		],
	    		[
	    		"person_id" => $this->validStudent,
	    		"is_leader" => "0",
	    		"first_name" => "Maha Mana Madan Mohan Malviya",
	    		"last_name" => "Pradhan Pradhan Pradhan Pradhan Pradhan Pradh",
	    		"action" => "update",
	    		"role" => "Member",
	    		"selectedIndex" => 1
	    		],
	    		],
	    		"team_name" => $this->team_name,
	    		"organization" => $this->organization
	    		]);
	    $newteam = json_decode($I->grabResponse());
	    return $newteam;
	}
	
	private function newContactCreate($I){
		 
		$I->sendPOST('contacts', [
				"organization_id" => $this->organization,
				"lang_id" => $this->lang,
				"contact_id"  => 0,
				"person_student_id" => $this->validStudent,
				"person_staff_id" => $this->person,
				"reason_category_subitem_id"  => $this->validActivitySubCategory,
				"activity_log_id" => null,
				"contact_type_id" => $this->validContactType,
				"date_of_contact" => date('m/d/Y'),
				"comment" => "Test case contact",
				"issue_discussed_with_student" => false,
				"high_priority_concern" => false,
				"issue_revealed_to_student" => false,
				"student_indicated_to_leave" => false,
				"share_options" => [
				[
				"private_share" => false,
				"public_share" => true,
				"teams_share" => false,
				"team_ids" => []
				]
				]
				]);
		$newContact = json_decode($I->grabResponse());
		return $newContact;
	}
}
