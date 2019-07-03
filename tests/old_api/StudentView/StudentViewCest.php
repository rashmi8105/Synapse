<?php
require_once 'tests/api/SynapseTestHelper.php';

class StudentViewCest extends SynapseTestHelper
{
    private $personStudentId = 2;
    private $invalidPersonStudent = -1;
    private $invalidStudent = 3;
    private $organizationId = 1;
    private $personFacultyId = 1;
    private $assignedTo = 2;
    public function _before(ApiTester $I)
    {
    	$this->token = $this->authenticate($I);
    }
    
    public function testGetCourseListForStudents(ApiTester $I)
    {
        $I->wantTo('Get Students Course List by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->studentAuthenticate($I));
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId .'/courses?view=student');
        $courses = json_decode($I->grabResponse());
        if(!is_null($courses)){
            $I->seeResponseContainsJson(array('student_id' => $this->personStudentId));
            $I->seeResponseContains('total_course');
            $I->seeResponseContains('course_list_table');
            if(!is_null($courses->data->course_list_table)){
                $I->seeResponseContains('year');
                $I->seeResponseContains('term');
                $I->seeResponseContains('campus_name');
                $I->seeResponseContains('courses');
            }
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetCourseListForStudentsInvalidPerson(ApiTester $I, $scenario)
    {
        $I->wantTo('Get Students Course List with invalid person by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->invalidPersonStudent .'/courses?view=student');
        $I->seeResponseContains('Access Denied');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetCourseListForStudentsInvalidStudent(ApiTester $I)
    {
    	$I->wantTo('Get Students Course List with invalid student by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('students/' . $this->invalidStudent .'/courses?view=student');
    	$I->seeResponseContains('Access Denied');
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    public function testGetAllCampusConnectionsForStudent(ApiTester $I)
    {
        $I->wantTo('Get Students Campus Connections by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        /**
         *  Create Campus Connection
         */
        $I->sendPOST('campusconnections/primaryconnection', [
            "organization_id" => "1",
            "student_list" => [
            [
            "student_id" => "6",
            "staff_id" => 2
            ]
            ]
            ]);
        $I->sendGET('students/6/campusconnections');
        $connections = json_decode($I->grabResponse());
        $I->seeResponseContains('campus_connection_list');
        if(!empty($connections->data->campus_connection_list)){
            $I->seeResponseContains('campus_id');
            $I->seeResponseContains('campus_name');
            $I->seeResponseContains('campus_connections');
            $I->seeResponseContains('person_id');
            $I->seeResponseContains('primary_connection');
            $I->seeResponseContains('groups');
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetAllCampusConnectionsForStudentInvalidStudent(ApiTester $I)
    {
    	$I->wantTo('Get Students Campus Connections with invalid Student by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('students/' . $this->invalidPersonStudent .'/campusconnections');
    	$I->seeResponseContains('Student Not Found.');
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();
    }
    
    
    public function testGetStudentCampusConnection(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections with Invalid Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/6/campusconnections?orgId=' . $this->organizationId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetStudentCampusConnectionInvalidStudentId(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections with Invalid Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->invalidPersonStudent . '/campusconnections?orgId=' . $this->organizationId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testGetStudentCampusConnectionInvalidOrgId(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections with Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->invalidPersonStudent . '/campusconnections?orgId=-10');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

     public function testGetStudentOpenReferrals(ApiTester $I)
    {
        $I->wantTo('Get Students Open Referrals by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('referrals', [
        		"organization_id" => $this->organizationId,
        		"person_student_id" => 6,
        		"person_staff_id" => $this->personFacultyId,
        		"reason_category_subitem_id" => "20",
        		"assigned_to_user_id" => 3,
        		"interested_parties" => [],
        		"comment" => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea",
        		"issue_discussed_with_student" => true,
        		"high_priority_concern" => false,
        		"issue_revealed_to_student" => true,
        		"student_indicated_to_leave" => false,
        		"notify_student" => true,
        		"share_options" => [
            		[
                		"private_share" => false,
                		"public_share" => true,
                		"teams_share" => false,
                		 "team_ids" => [] 
        		    ]
        		]
        	]);

        $I->amBearerAuthenticated($this->authenticate($I));
        $I->sendGET('students/6/referrals?view=student');//print_r(json_decode($I->grabResponse()));exit;
        $I->seeResponseContainsJson(array('organization_id' => $this->organizationId));
        $I->seeResponseContains('referral_id');
        $I->seeResponseContains('campus_name');
        $I->seeResponseContains('referral_date');
        $I->seeResponseContains('created_by');
        $I->seeResponseContains('created_by_email');
        $I->seeResponseContains('assigned_to');
        $I->seeResponseContains('assigned_to_email');
        $I->seeResponseContainsJson(array('description' => "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea"));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    } 
    
    public function testGetStudentOpenReferralsInvalidStudent(ApiTester $I)
    {
        $I->wantTo('Get Students Open Referrals by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->invalidPersonStudent .'/referrals?view=student');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testGetCourseListForStudentsInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Get Students Course List by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('students/' . $this->personStudentId .'/courses?view=student');
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    public function testGetAllCampusConnectionsForStudentInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Get Students Campus Connections by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('students/' . $this->personStudentId .'/campusconnections');
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
    
    public function testGetStudentOpenReferralsInvalidAuthentication(ApiTester $I)
    {
    	$I->wantTo('Get Students Open Referrals by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET('students/' . $this->personStudentId .'/referrals?view=student');
    	$I->seeResponseCodeIs(403);
    	$I->seeResponseIsJson();
    }
}
