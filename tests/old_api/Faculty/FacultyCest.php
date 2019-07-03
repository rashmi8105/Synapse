<?php
require_once 'tests/api/SynapseTestHelper.php';

class FacultyCest extends SynapseTestHelper
{
    private $token;
    private $personIdFaculty = 3;
    private $invalidPersonFaculty = -1;
    private $orgId = 1;
    
    public function _before(ApiTester $I) {
        $this->token = $this->authenticate ( $I );
    }
    
    public function testGetPersonalInfo(ApiTester $I){
        $I->wantTo ( "Ensure Access to faculty by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendGET ( 'faculty/'.$this->personIdFaculty);
        
        $response = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testDeleteFaculty(ApiTester $I){
        $I->wantTo ( "Delete faculty by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
        ]);
        $response = json_decode($I->grabResponse());
        $I->sendDELETE ( 'faculty/'.$response->data->id);
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetGroupsList(ApiTester $I){
        $I->wantTo ( "Get Faculty Group List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendGET ( 'faculty/1/groups/'.$this->orgId);
    
        $response = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddGroup(ApiTester $I){
        $I->wantTo ( "Add Faculty Group List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPOST ( 'faculty/'.$response->data->id.'/group/2');
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveGroup(ApiTester $I){
        $I->wantTo ( "Remove Faculty Group List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPOST ( 'faculty/'.$response->data->id.'/group/2');
        $response1 = json_decode($I->grabResponse());
        $I->sendDELETE ( 'faculty/'.$response->data->id.'/group/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddGroups(ApiTester $I){
        $I->wantTo ( "Add Faculty to Groups List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPUT ( 'faculty/'.$response->data->id.'/addGroups',[
            "grouplist" => [
                [
                    "action" => "add",
                    "group_id" => 2,
                    "staff_permissionset_id" => 1,
                    "staff_is_invisible" => false
                ]
            ]
        ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFromGroups(ApiTester $I){
        $I->wantTo ( "Remove From Faculty to Groups List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPUT ( 'faculty/'.$response->data->id.'/addGroups',[
            "grouplist" => [
            [
            "action" => "add",
            "group_id" => 2,
            "staff_permissionset_id" => 1,
            "staff_is_invisible" => false
            ]
            ]
            ]);
        
        $I->sendPUT ( 'faculty/'.$response->data->id.'/removeGroups',[
            "grouplist" => [
                [2]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddFacultyToCourse(ApiTester $I){
        $I->wantTo ( "Add Faculty to Groups List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPOST ( 'faculty/'.$response->data->id.'/course/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testDeleteFacultyToCourse(ApiTester $I){
        $I->wantTo ( "Add Delete to Groups List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendDELETE ( 'faculty/'.$response->data->id.'/course/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddFacultyToCourses(ApiTester $I){
        $I->wantTo ( "Add Faculty To Courses by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPUT ( 'faculty/'.$response->data->id.'/addCourses',[
            "courselist" => [
            [
            "action" => "add",
            "course_id" => 1,
            "staff_permissionset_id" => 1
            ]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFacultyFromCourses(ApiTester $I){
        $I->wantTo ( "Remove Faculty From Courses by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPUT ( 'faculty/'.$response->data->id.'/addCourses',[
            "courselist" => [
            [
            "action" => "add",
            "course_id" => 1,
            "staff_permissionset_id" => 1
            ]
            ]
            ]);
        $I->sendPUT ( 'faculty/'.$response->data->id.'/removeCourses',[
            "courselist" => [
            [1]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddFacultyToTeam(ApiTester $I){
        $I->wantTo ( "Add Faculty to Teams by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPOST ( 'faculty/'.$response->data->id.'/team/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFacultyFromTeam(ApiTester $I){
        $I->wantTo ( "Add Delete Faculty From Teams by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendDELETE ( 'faculty/'.$response->data->id.'/team/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddFacultyToTeams(ApiTester $I){
        $I->wantTo ( "Add Faculty To Teams by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPUT ( 'faculty/'.$response->data->id.'/addTeams',[
            "teamlist" => [
            [
            "action" => "add",
            "team_id" => 1,
            "role" => 1
            ]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFacultyFromTeams(ApiTester $I){
        $I->wantTo ( "Remove Faculty From Teams by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendPUT ( 'faculty/'.$response->data->id.'/addTeams',[
            "teamlist" => [
            [
            "action" => "add",
            "team_id" => 1,
            "role" => 1
            ]
            ]
            ]);
        $I->sendPUT ( 'faculty/'.$response->data->id.'/removeTeams',[
            "teamlist" => [
            [1]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetPersonalInfoForPersonFromOtherOrg(ApiTester $I){
        $I->wantTo ( "Ensure Access to faculty by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendGET ( 'faculty/208');
    
        $response = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }
    
    public function testDeleteFacultyInvalid(ApiTester $I){
        $I->wantTo ( "Delete faculty by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendDELETE ( 'faculty/'.$this->invalidPersonFaculty);
        $response = json_decode($I->grabResponse());
        $I->seeResponseContains("Couldn't delete faculty member (-1).");
        $I->seeResponseContains("Person Not Found.");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddGroupInvalid(ApiTester $I){
        $I->wantTo ( "Add Faculty Group List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'faculty/'.$this->invalidPersonFaculty.'/group/2');
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseContains("Couldn't add group (2) for faculty member (-1).");
        $I->seeResponseContains("Person Not Found.");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveGroupInvalid(ApiTester $I){
        $I->wantTo ( "Remove Faculty Group List by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendDELETE ( 'faculty/'.$this->invalidPersonFaculty.'/group/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFromGroupsInvalid(ApiTester $I){
        $I->wantTo ( "Remove From Faculty to Groups List Invalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPUT ( 'faculty/'.$this->invalidPersonFaculty.'/removeGroups',[
            "grouplist" => [
            [2]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddFacultyToCourseInvalid(ApiTester $I){
        $I->wantTo ( "Add Faculty to Groups List Invalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'faculty/'.$this->invalidPersonFaculty.'/course/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testDeleteFacultyToCourseInvalid(ApiTester $I){
        $I->wantTo ( "Add Delete to Groups List INvalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendDELETE ( 'faculty/'.$this->invalidPersonFaculty.'/course/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFacultyFromCoursesInvalid(ApiTester $I){
        $I->wantTo ( "Remove Faculty From Courses Invalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPUT ( 'faculty/'.$this->invalidPersonFaculty.'/removeCourses',[
            "courselist" => [
            [1]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testAddFacultyToTeamInvalid(ApiTester $I){
        $I->wantTo ( "Add Faculty to Teams Invalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'faculty/'.$this->invalidPersonFaculty.'/team/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFacultyFromTeamInvalid(ApiTester $I){
        $I->wantTo ( "Add Delete Faculty From Teams Invalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendDELETE ( 'faculty/'.$this->invalidPersonFaculty.'/team/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testRemoveFacultyFromTeamsInvalid(ApiTester $I){
        $I->wantTo ( "Remove Faculty From Teams Invalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPUT ( 'faculty/'.$this->invalidPersonFaculty.'/removeTeams',[
            "teamlist" => [
            [1]
            ]
            ]);
        $response1 = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testListFacultyCourse(ApiTester $I)
    {
        $I->wantTo ( "List Faculty Courses by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendPOST ( 'users', [
            "title"=> "FFaculty",
            "firstname"=> "Mapwroks",
            "lastname"=> "Faculty",
            "email"=> "mapwork.faculty".uniqid()."@mailinator.com",
            "phone"=> "9547512474",
            "ismobile"=> true,
            "externalid"=> uniqid("FFaculty_", true),
            "user_type"=> "faculty",
            "campusid"=> 1,
            "is_active"=> 1
            ]);
        $response = json_decode($I->grabResponse());
        $I->sendGET ( 'faculty/'.$response->data->id.'/courses');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testListFacultyCourseInvalid(ApiTester $I)
    {
        $I->wantTo ( "List Faculty Courses Invalid by API" );
        $I->haveHttpHeader ( 'Content-Type', 'application/json' );
        $I->amBearerAuthenticated ( $this->token );
        $I->haveHttpHeader ( 'Accept', 'application/json' );
        $I->sendGET ( 'faculty/'.$this->invalidPersonFaculty.'/courses');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}