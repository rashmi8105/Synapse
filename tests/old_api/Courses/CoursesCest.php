<?php
require_once 'tests/api/SynapseTestHelper.php';

class CoursesCest extends SynapseTestHelper
{

    private $token;

    private $courseId = 1;

    private $facultyId = 1;

    private $studentId = 1;

    private $invalidCourseId = - 1;

    private $invalidYearId = - 1;

    private $invalidUserType = 'manager';

    private $invalidTermId = - 1;

    private $orgainzationId = 1;

    private $yearId = 201415;

    private $termId = 1;

    private $collegeName = "IIT";

    private $viewMode = 'json';

    private $invalidStudentId = - 1;

    private $invalidFacultyId = - 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testCourseNavigation(ApiTester $I)
    {
        $I->wantTo('List coordinator course list');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses/navigation?orgId=' . $this->orgainzationId . '&type=year&user_type=coordinator');
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            "organization_id" => $this->orgainzationId
        ));
        $I->seeResponseContainsJson(array(
            "type" => "year"
        ));
        $I->seeResponseContainsJson(array(
            "course_navigation" => array(
                "key" => '201415'
            )
        ));
    }

    public function testCourseNavigationByYear(ApiTester $I)
    {
        $I->wantTo('List coordinator course list');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses/navigation?orgId=' . $this->orgainzationId . '&type=term&user_type=coordinator&year_id=' . $this->yearId);
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            "organization_id" => $this->orgainzationId
        ));
        $I->seeResponseContainsJson(array(
            "type" => "term"
        ));
    }

    public function testCourseNavigationByTerm(ApiTester $I)
    {
        $I->wantTo('List coordinator course list');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses/navigation?orgId=' . $this->orgainzationId . '&type=college&user_type=coordinator&year_id=all&term_id=all&college=' . $this->termId);
        $I->seeResponseContainsJson(array(
            "organization_id" => $this->orgainzationId
        ));
        $I->seeResponseContainsJson(array(
            "type" => "college"
        ));
    }

    public function testCourseNavigationByCollege(ApiTester $I)
    {
        $I->wantTo('List coordinator course list');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses/navigation?orgId=' . $this->orgainzationId . '&type=department&user_type=coordinator&year_id=all&term_id=all&college=' . $this->collegeName);
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            "organization_id" => $this->orgainzationId
        ));
        $I->seeResponseContainsJson(array(
            "type" => "department"
        ));
    }

    public function testListCourseListforCoordinator(ApiTester $I)
    {
        $I->wantTo('List coordinator course list');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses?user_type=coordinator&year=all&term=all&college=all&department=all&filter=&viewmode=json&export=');
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('total_course');
        $I->seeResponseContains('total_faculty');
        $I->seeResponseContains('total_students');
        $I->seeResponseContains('course_list_table');
        $I->seeResponseContainsJson(array(
            "course_list_table" => array(
                "department" => 'IT'
            )
        ));
        $I->seeResponseContainsJson(array(
            "course_list_table" => array(
                "year" => 'Academic year'
            )
        ));
        $I->seeResponseContainsJson(array(
            "course_list_table" => array(
                "college" => 'IIT'
            )
        ));
        $I->seeResponseContainsJson(array(
            "course_list_table" => array(
                "term" => 'Term 1'
            )
        ));
    }

    public function testFacultyCourseListView(ApiTester $I)
    {
        $I->wantTo('List courses for the faculty');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses/' . $this->courseId . '?user_type=coordinator&viewmode=' . $this->viewMode);
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('total_students');
        $I->seeResponseContains('total_faculties');
        $I->seeResponseContains('course_id');
        $I->seeResponseIsJson(array(
            'course_id' => $this->courseId
        ));
        $I->seeResponseContains('course_name');
        $I->seeResponseContains('faculty_details');
        $I->seeResponseContains("faculty_details");
        $I->seeResponseContains("student_details");
    }

    
    public function testListCourseListforInvalidYearCoordinator(ApiTester $I)
    {
        $I->wantTo('List coordinator course list for invalid year');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses?user_type=coordinator&year=' . $this->invalidYearId . '&term=all&college=all&department=all&filter=');
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
    }

    public function testListCourseListforInvalidTermCoordinator(ApiTester $I)
    {
        $I->wantTo('List coordinator course list for invalid term');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses?user_type=coordinator&year=all&term=' . $this->invalidTermId . '&college=all&department=all&filter=');
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(400);
    }

    public function testListCourseListforInvalidUserType(ApiTester $I)
    {
        $I->wantTo('List course list for invalid user type');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses?user_type=' . $this->invalidUserType . '&year=all&term=all&college=all&department=all&filter=');
        $I->seeResponseCodeIs(400);
    }
    
    
     public function testDeleteFacultyforCourse(ApiTester $I)
     {
         $I->wantTo('Delete a faculty for a course'); 
         $I->haveHttpHeader('Content-Type', 'application/json'); 
         $I->amBearerAuthenticated( $this->token); 
         $I->haveHttpHeader('Accept', 'application/json'); 
         
         $I->sendPOST('courses/roster',[
             "type"=> "faculty",
             "course_id"=> $this->courseId,
             "person_id"=> 2
             ]);
         
         $courses = json_decode($I->grabResponse());
         $I->sendDELETE('courses/'.$this->courseId.'/faculty/'.$courses->data->person_id);
         $I->seeResponseCodeIs(204);
      }
    
    
    public function testDeleteStudentforCourse(ApiTester $I)
    {
        $I->wantTo('Delete a student for a course');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		
		$I->sendPOST('courses/roster',[
		    "type"=> "student",
		    "course_id"=> $this->courseId,
		    "person_id"=> 6
		    ]);
		 
		$courses = json_decode($I->grabResponse());
        $I->sendDELETE('courses/'.$this->courseId.'/student/'.$courses->data->person_id);
        $I->seeResponseCodeIs(204);
    }
    
    public function testDeleteStudentforInvalidCourse(ApiTester $I)
    {
        $I->wantTo('Delete a student for invalid course');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('courses/' . $this->invalidCourseId . '/student/' . $this->studentId);
        $I->seeResponseCodeIs(400);
    }

    public function testDeleteFacultyforInvalidCourse(ApiTester $I)
    {
        $I->wantTo('Delete a faculty for a course');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('courses/' . $this->invalidCourseId . '/faculty/' . $this->facultyId);
        $I->seeResponseCodeIs(400);
    }
    
    public function testDeleteInvalidCourseId(ApiTester $I)
    {
        $I->wantTo('Delete a course');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('courses/' . $this->invalidCourseId . '/sections/1');
        $I->seeResponseCodeIs(400);
    }

    public function testStudentCourseListView(ApiTester $I)
    {
        $I->wantTo('List courses for the student');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('courses/roster',[
            "type"=> "faculty",
            "course_id"=> $this->courseId,
            "person_id"=> 2
            ]);
        
        $courseFaculty = json_decode($I->grabResponse());
        
        $I->sendPOST('courses/roster',[
            "type"=> "student",
            "course_id"=> $this->courseId,
            "person_id"=> 6
            ]);
        $courseStudent = json_decode($I->grabResponse());
        $I->sendGET('courses/student/6');
        $data = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('total_course');
        $I->seeResponseContainsJson(array('student_id'=> 6));
        $I->seeResponseContains('course_list_table');
        
        foreach($data->data->course_list_table as $courseList){
            $I->seeResponseContainsJson(array(
                    "year" => 'Academic year'
                )
            );
            $I->seeResponseContainsJson(array(
                    "term" => 'Term 1'
                )
            );
            $I->seeResponseContainsJson(array(
                    "college" => 'IIT'
                )
            );
            $I->seeResponseContainsJson(array(
                    "department" => 'IT'
                )
            );
        }
        $I->sendDELETE('courses/'.$this->courseId.'/faculty/'.$courseFaculty->data->person_id);
        $I->sendDELETE('courses/'.$this->courseId.'/student/'.$courseStudent->data->person_id);
    }

    public function testStudentCourseListViewForInvalidStudentId(ApiTester $I)
    {
        $I->wantTo('List courses for the invalid student id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('courses/student/' . $this->invalidStudentId);
        $I->seeResponseCodeIs(403);
    }
	
    
    public function testUpdateFacultyCoursePermission(ApiTester $I)
    {
        $I->wantTo('Update faculty course Permission');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('courses/roster',[
            "type"=> "faculty",
            "course_id"=> $this->courseId,
            "person_id"=> 2
            ]);
        
        $course = json_decode($I->grabResponse());
        $I->sendPut('courses/permissions', [
            'course_id' => $this->courseId,
            'person_id' => $course->data->person_id,
            'permissionset_id' => 1,
            'organization_id' => $this->orgainzationId
        ]);
        
        $I->seeResponseCodeIs(204);
        $I->sendDELETE('courses/'.$this->courseId.'/faculty/'.$course->data->person_id);
    }
	

    public function testUpdateFacultyCoursePermissionForInvalidPersonId(ApiTester $I)
    {
        $I->wantTo('Update faculty course Permission for invalid faculty Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPut('courses/permissions', [
            'course_id' => $this->courseId,
            'person_id' => 6,
            'permissionset_id' => 1,
            'organization_id' => $this->orgainzationId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains("Faculty not assigned for this course");
    }

	
    public function testUpdateFacultyCoursePermissionForInvalidCourseId(ApiTester $I)
    {
        $I->wantTo('Update faculty course Permission with Invalid course Id');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPut('courses/permissions', [
            'course_id' => $this->invalidCourseId,
            'person_id' => $this->facultyId,
            'permissionset_id' => 1,
            'organization_id' => $this->orgainzationId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains("Course not found");
    }
    
    public function testAddFacultyforCourse(ApiTester $I)
    {
        $I->wantTo('add a fACulty for a course');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('courses/roster',[
            "type"=> "faculty",
            "course_id"=> $this->courseId,
            "person_id"=> 2
            ]);
        	
        $courses = json_decode($I->grabResponse());
        
        $I->seeResponseContainsJson(array('type' => 'faculty'));
        $I->seeResponseContainsJson(array('course_id' => $this->courseId));
        $I->seeResponseContainsJson(array('person_id' => 2));
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        
        $I->sendDELETE('courses/'.$this->courseId.'/faculty/'.$courses->data->person_id);
    }
    
    public function testAddFacultyforCourseForAlreadyAssignedFaculty(ApiTester $I)
    {
        $I->wantTo('add a faculty for a course For Already Assigned Faculty ');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('courses/roster',[
            "type"=> "faculty",
            "course_id"=> $this->courseId,
            "person_id"=> 2
            ]);
        $courses = json_decode($I->grabResponse());
        
        $I->sendPOST('courses/roster',[
            "type"=> "faculty",
            "course_id"=> $this->courseId,
            "person_id"=> 2
            ]);
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains("User already assigned to course section");
        $I->seeResponseIsJson();
    
        $I->sendDELETE('courses/'.$this->courseId.'/faculty/'.$courses->data->person_id);
    }
    /**
     * 
     * @param ApiTester $I
     */
    
    public function testAddStudentforCourse(ApiTester $I)
    {
        $I->wantTo('add a student for a course');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('courses/roster',[
            "type"=> "student",
            "course_id"=> $this->courseId,
            "person_id"=> 6
            ]);
         
        $courses = json_decode($I->grabResponse());
    
        $I->seeResponseContainsJson(array('type' => 'student'));
        $I->seeResponseContainsJson(array('course_id' => $this->courseId));
        $I->seeResponseContainsJson(array('person_id' => 6));
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    
        $I->sendDELETE('courses/'.$this->courseId.'/student/'.$courses->data->person_id);
    }
    
    public function testAddStudentforCourseForAlreadyAssignedStudent(ApiTester $I)
    {
        $I->wantTo('add a student for a course For Already Assigned student ');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('courses/roster',[
            "type"=> "student",
            "course_id"=> $this->courseId,
            "person_id"=> 6
            ]);
        $courses = json_decode($I->grabResponse());
    
        $I->sendPOST('courses/roster',[
            "type"=> "student",
            "course_id"=> $this->courseId,
            "person_id"=> 6
            ]);
    
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains("User already assigned to course section");
        $I->seeResponseIsJson();
    
        $I->sendDELETE('courses/'.$this->courseId.'/student/'.$courses->data->person_id);
    }
}