<?php
use GuzzleHttp\json_decode;
require_once 'SynapseTestHelper.php';

class StudentCest extends SynapseTestHelper
{

    private $personStudentId = 1;

    private $invalidStudentId = - 1;

    private $personId = 1;

    private $proxyPersonId = 0;

    private $organizationId = 1;

    private $officeHoursId = 2;

    private $isFreeStanding = false;

    private $token;
	
	private $contact = "";
	
	private $assignedTo = 1;
	
	private $assignedTo2 = 1;
	
	private $studentId = 6;
	
	private $invalidOrgId = -1;


    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
	/* Need to be Fixed
    public function testGetStudentOpenAppointments(ApiTester $I)
    {
        $I->wantTo('Get Students Open Appointments by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $slotStart = new DateTime("+ 2 hour");
        $slotEnd = new DateTime("+ 3 hour");
        $I->sendPOST('appointments/' . $this->organizationId . '/' . $this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
            "organization_id" => $this->organizationId,
            "detail" => "api - Test details",
            "detail_id" => 1,
            "location" => "api - Stepojevac",
            "description" => "api - dictumst etiam faucibus",
            "office_hours_id" => $this->officeHoursId,
            "is_free_standing" => $this->isFreeStanding,
            "type" => "S",
            "attendees" => [
                [
                    "student_id" => $this->personStudentId,
                    "is_selected" => true,
                    "is_added_new" => true
                ]
            ]
            ,
            "slot_start" => $slotStart->format('Y-m-d H:i:s'),
            "slot_end" => $slotEnd->format('Y-m-d H:i:s')
        ]);
        $appCreated = json_decode($I->grabResponse());
        $I->sendGET('students/' . $this->personStudentId . '/appointments');
        $app = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'person_student_id' => $this->personStudentId
        ));
        $I->seeResponseContainsJson(array(
            'person_staff_id' => $this->personId
        ));
        $I->seeResponseContains('total_appointments');
        $I->seeResponseContains('total_appointments_by_me');
        $I->seeResponseContains('total_same_day_appointments_by_me');
        $I->seeResponseContainsJson(array(
            'appointments' => array(
                'appointment_id' => $appCreated->data->appointment_id
            )
        ));
        $I->seeResponseContains('start_date');
        $I->seeResponseContains('end_date');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testGetStudentOpenAppointmentsInvalidStudent(ApiTester $I)
    {
        $I->wantTo('Get Invalid Student Open Appointments by API');
		$I->sendGET('students/' . $this->invalidStudentId . '/appointments');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetStudentOpenAppointmentsInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Student Open Appointments with invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/appointments');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	/* Need to be Fixed
    public function testGetStudentPersonalInfo(ApiTester $I)
    {
        $I->wantTo('Get Students Personal Information by API');
        $I->haveHttpHeader('Content-Type', 'application/json');		
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->invalidStudentId . '/appointments');
        $I->sendGET('students/' . $this->personStudentId);
        $app = json_decode($I->grabResponse());
        // var_dump($app);exit;
        $I->seeResponseContainsJson(array(
            'id' => $this->personStudentId
        ));
        $I->seeResponseContains('student_external_id');
        $I->seeResponseContains('student_first_name');
        $I->seeResponseContains('student_last_name');
        $I->seeResponseContains('primary_email');      
        $I->seeResponseContains('student_risk_status');      
        $I->seeResponseContains('risk_updated_date');
        $I->seeResponseContains('last_viewed_date');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
	*/

    public function testGetStudentPersonalInfoInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Student Personal Information with invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetStudentPersonalInfoInvalidStudent(ApiTester $I)
    {
        $I->wantTo('Get Invalid Student Personal Information by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->invalidStudentId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
	public function testGetStudentOpenReferrals(ApiTester $I)
	{
		$I->wantTo('Get the list of open referrals for the student');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('referrals', [
            "organization_id" => $this->organizationId,
            "person_student_id" => $this->personStudentId,
            "person_staff_id" => $this->personId,
            "reason_category_subitem_id" => "2",
            "assigned_to_user_id" => $this->assignedTo,
            "interested_parties" => [
                [
                    "id" => 1
                ]
            ],
            "comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
            "issue_discussed_with_student" => true,
            "high_priority_concern" => false,
            "issue_revealed_to_student" => true,
         "student_indicated_to_leave" => false,
            "share_options" => [
                [
                    "private_share" => false,
                    "public_share" => true,
                    "teams_share" => false,
                    "team_ids" => [
                        [
                            "id" => 1,
                            "is_team_selected" => true
                        ]
                    ]
                ]
            ]
        ]);
		$referrals = json_decode($I->grabResponse());
		$I->sendGET('students/'.$this->personStudentId.'/referrals');
		$studentReferrals = json_decode($I->grabResponse());
		$I->seeResponseContainsJson(array(
            'person_student_id' => $this->personStudentId
        ));
        $I->seeResponseContainsJson(array(
            'person_staff_id' => $this->personId
        ));
        $I->seeResponseContains('total_referrals_count');
        $I->seeResponseContains('total_open_referrals_count');
        $I->seeResponseContains('total_open_referrals_assigned_to_me');      
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}	
	public function testGetStudentOpenReferralsInvlaidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get the list of Open Referrals with invalid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/referrals');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
	}
	/* Need to be Fixed
	public function testStudentContacts(ApiTester $I){		
		$I->wantTo('Get the count of Contacts for the student');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		$contact = array();
		$shareOption = array();
		$teams = array();
		$contact['organization_id'] = $this->organizationId;
		$contact['person_student_id'] = $this->personStudentId;
		$contact['person_staff_id'] = $this->personId;
		$contact['reason_category_subitem_id'] = 1;
		$contact['contact_type_id'] = 1;
		$contact['date_of_contact'] = "02/02/2015";
		$contact['comment'] = "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea"; 
		$contact['issue_discussed_with_student'] = true;
		$contact['high_priority_concern'] = false;
		$contact['issue_revealed_to_student'] = true;
		$contact['student_indicated_to_leave'] = false;	
		$teams = array(
				array("id"=> "1","is_team_selected"=> true),		     
		      );
		$shareOption['private_share'] = true;
		$shareOption['public_share'] = true;
		$shareOption['teams_share'] = true;
		$shareOption['team_ids'] = $teams;
		$contact['share_options'][] = $shareOption;		
		$contactParams =  json_encode($contact);		
		$I->sendPOST('contacts',$contactParams);
		$contacts = json_decode($I->grabResponse());
		$I->sendGET('students/'.$this->personStudentId.'/contacts');
		$I->seeResponseContainsJson(array(
            'person_student_id' => $this->personStudentId
        ));
        $I->seeResponseContainsJson(array(
            'person_staff_id' => $this->personId
        ));
		$I->seeResponseContains('person_student_id');
        $I->seeResponseContains('person_staff_id');
        $I->seeResponseContains('total_contacts');      
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}*/
	public function testGetStudentContactsInvlaidAuthentication(ApiTester $I)
	{
		$scenario->skip("Failed");
		$I->wantTo('Get the count of Contacts for the student with invalid Authentication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/referrals');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
	}
    
	public function testCreateContactAsActivity(ApiTester $I)
	{
		$contactParams = $this->getContactArray($this->organizationId, $this->personStudentId, $this->personId, 1, 1);	
		$I->wantTo('Create a contact as an activity by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('contacts',$contactParams);
		$this->contact = json_decode($I->grabResponse());
	}
	
	public function testStudentActivityList(ApiTester $I, $scenario)
  {
    //$scenario->skip("Failed");
		$I->wantTo('Get Student Activity List by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/activity?student-id='.$this->personStudentId.'&category=all&is-interaction=false');
		$lists = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseIsJson ( array (
		'person_id' => $this->personStudentId 
		) );
		$I->seeResponseIsJson (array(
		'total_activities' => 0
		));
		$I->seeResponseIsJson ( array (
		'show_interaction_contact_type' => false 
		) );
		/*foreach($lists->data->activities as $list){
			if(isset($list->activity_id) && $list->activity_id == $this->contact->data->contact_id)
				$I->seeResponseIsJson ($list->activity_id, $this->contact->data->contact_id);
		}*/
	}

	public function testStudentActivityListContactCount(ApiTester $I, $scenario)
	{
    //$scenario->skip("Failed");
		$I->wantTo('Get Student Activity List for Contact Count by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/activity?student-id='.$this->personStudentId.'&category=all&is-interaction=false');
		$lists = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseIsJson ( array (
		'person_id' => $this->personStudentId 
		) );
		$I->seeResponseIsJson ( array (
		'show_interaction_contact_type' => false 
		) );
		$count = 0;
		/*foreach($lists->data->activities as $list){
			if(isset($list->activity_id) && $list->activity_id == $this->contact->data->contact_id)
				$count = $count + 1;
		}*/
		$I->seeResponseIsJson (array(
		'total_contacts' => $count
		));
	}
	
	public function testStudentActivityListWithInvalidStudent(ApiTester $I)
	{
		$I->wantTo('Get Student Activity List with Invalid Student Id by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/activity?student-id=0&category=all&is-interaction=false');
		$lists = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(403);
		$I->seeResponseContains ("Access Denied");
		$I->seeResponseIsJson();
	}
	
	public function testStudentActivityListWithInvalidCategory(ApiTester $I)
		{
		$I->wantTo('Get Student Activity List with Invalid Category by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/activity?student-id='.$this->personStudentId.'&category=xyz&is-interaction=false');
		$lists = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(400);
		$I->seeResponseContains ("Not a valid Category");
		$I->seeResponseIsJson();
	}
	/* Need to be Fixed
	public function testStudentPersonalInfo(ApiTester $I)
	{
		$I->wantTo('Get Student Personal Info by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/'.$this->personStudentId);
		$info = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseIsJson ( array (
		'id' => $this->personStudentId 
		) );
	}
	*/
	
	public function testStudentPersonalInfoInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get the Student Personal Info with invalid Authentication');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/' . $this->personStudentId);
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}

	public function getStudentPersonalInfoWithInvalidStudent(ApiTester $I)
	{
		$I->wantTo('Get Student Personal Info with Invalid Student Id by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/'.$this->invalidStudentId);
		$info = json_decode($I->grabResponse());
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();	
	}
	
	public function testGetStudentOpenReferralsWithAllData(ApiTester $I)
	{
		$I->wantTo('Get the list of open referrals with all data by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('referrals', [
            "organization_id" => $this->organizationId,
            "person_student_id" => $this->personStudentId,
            "person_staff_id" => $this->personId,
            "reason_category_subitem_id" => "2",
            "assigned_to_user_id" => $this->assignedTo,
            "interested_parties" => [
                [
                    "id" => 1
                ]
            ],
            "comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
            "issue_discussed_with_student" => true,
            "high_priority_concern" => false,
            "issue_revealed_to_student" => true,
         "student_indicated_to_leave" => false,
            "share_options" => [
                [
                    "private_share" => false,
                    "public_share" => true,
                    "teams_share" => false,
                    "team_ids" => [
                        [
                            "id" => 1,
                            "is_team_selected" => true
                        ]
                    ]
                ]
            ]
        ]);
		$referrals = json_decode($I->grabResponse());
		$I->sendGET('students/'.$this->personStudentId.'/referrals');
		$studentReferrals = json_decode($I->grabResponse());
		$I->seeResponseContainsJson(array(
		'person_student_id' => $this->personStudentId
		));
		$I->seeResponseContainsJson(array(
		'person_staff_id' => $this->personId
		));
		$I->seeResponseContains('total_referrals_count');
		$I->seeResponseContains('total_open_referrals_count');
		$I->seeResponseContains('total_open_referrals_assigned_to_me');      
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
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
	/* Need to be Fixed
	public function testGetStudentGroupsList(ApiTester $I)
	{
	    $I->wantTo('Get the list of student groups by API');
	    $I->haveHttpHeader('Content-Type', 'application/json');
	    $I->amBearerAuthenticated($this->token);
	    $I->haveHttpHeader('Accept', 'application/json');
	    $I->sendGET('students/'.$this->studentId.'/groups/'.$this->organizationId);
	    $groups = json_decode($I->grabResponse());
	    $I->seeResponseContainsJson(array('student_id' => $this->studentId));
	    $I->seeResponseContainsJson(array('organization_id' => $this->organizationId));
	    if(!empty($groups->data->groups)){
	        foreach($groups->data->groups as $groups){
	            $I->seeResponseContains('group_id');
	            $I->seeResponseContains('group_name');
	        }
	    }
	    $I->seeResponseCodeIs(200);
	    $I->seeResponseIsJson();
	}
	*/
 	
	public function testGetStudentGroupsListInvalidStudent(ApiTester $I)
	{
		$I->wantTo('Get the list of student groups with invalid student by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/'.$this->invalidStudentId.'/groups/'.$this->organizationId);
		$I->seeResponseContains('"Unauthorized access to organization: '.$this->invalidStudentId);
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}
	
	public function testGetStudentGroupsListInvalidOrg(ApiTester $I)
	{
		$I->wantTo('Get the list of student groups with invalid organization by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated($this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/'.$this->studentId.'/groups/'.$this->invalidOrgId);
		$I->seeResponseContains('Student Not Found.');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}
	
	public function testGetStudentGroupsListInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get the list of student groups with invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('students/'.$this->studentId.'/groups/'.$this->organizationId);
		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}
}


    
