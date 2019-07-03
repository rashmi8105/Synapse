<?php
require_once 'tests/api/SynapseTestHelper.php';

class CoursesListCest extends SynapseTestHelper
{

    private $token;

    private $courseId = 1;

    private $personId = 1;

    private $invalidCourseId = 0;

    private $invalidPersonId = 0;

    private $courseName = 'Basic Economics';

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetSingleCourseDetails(ApiTester $I)
    {
        $I->wantTo('Get Single Course Details by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('courses/' . $this->courseId . '?user_type=coordinator&viewmode=json');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('course_id');
        $I->seeResponseIsJson(array(
            'course_id' => $this->courseId
        ));
        $I->seeResponseIsJson(array(
            'course_name' => $this->courseName
        ));
        $I->seeResponseContains('faculty_details');
    }

    public function testGetInvalidSingleCourseDetails(ApiTester $I)
    {
        $I->wantTo('Get Single Course Details with Invalid Course Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('courses/' . $this->invalidCourseId . '?user_type=coordinator&viewmode=json');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Course not found');
    }

    public function testListCourseforFaculty(ApiTester $I)
    {
        $I->wantTo('Get List Course for a faculty by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('courses?user_type=faculty&year=all&term=all&college=all&department=all&filter=&viewmode=json&export=');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('total_course');
        $I->seeResponseContains('course_list_table');
    }

    public function testListInvalidYearCourseforFaculty(ApiTester $I)
    {
        $I->wantTo('Get List Course for a faculty with Invalid Year by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('courses?user_type=faculty&year=xyz&term=all&college=all&department=all&filter=&viewmode=json&export=');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testListInvalidUserTypeCourseforFaculty(ApiTester $I)
    {
        $I->wantTo('Get List Course for a faculty with Invalid User Type by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGet('courses?user_type=xyz&year=all&term=all&college=all&department=all&filter=&viewmode=json&export=');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Invalid User Type');
    }

    public function testAddFacultyToInvalidCourse(ApiTester $I)
    {
        $I->wantTo('Add Faculty To Invalid Course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('courses/roster', [
            "type" => 'faculty',
            "course_id" => $this->invalidCourseId,
            "person_id" => $this->personId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Course not found');
    }

    public function testAddInvalidFacultyToCourse(ApiTester $I)
    {
        $I->wantTo('Add Invalid Faculty To Course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('courses/roster', [
            "type" => 'faculty',
            "course_id" => $this->courseId,
            "person_id" => $this->invalidPersonId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Person Not Found.');
    }

    public function testAddFacultytToCourseInvalidUserType(ApiTester $I)
    {
        $I->wantTo('Add faculty To Course with Invalid User Type by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('courses/roster', [
            "type" => 'xyz',
            "course_id" => $this->courseId,
            "person_id" => $this->personId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Invalid filter type');
    }

    public function testAddStudentToInvalidCourse(ApiTester $I)
    {
        $I->wantTo('Add Student To Invalid Course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('courses/roster', [
            "type" => 'student',
            "course_id" => $this->invalidCourseId,
            "person_id" => $this->personId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Course not found');
    }

    public function testAddInvalidStudentToCourse(ApiTester $I)
    {
        $I->wantTo('Add Invalid Student To Course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('courses/roster', [
            "type" => 'student',
            "course_id" => $this->courseId,
            "person_id" => $this->invalidPersonId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Person Not Found.');
    }

    public function testAddStudentToCourseInvalidUserType(ApiTester $I)
    {
        $I->wantTo('Add Student To Course with Invalid User Type by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('courses/roster', [
            "type" => 'xyz',
            "course_id" => $this->courseId,
            "person_id" => $this->personId
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Invalid filter type');
    }
    
    public function testGetCourseListForFaculty(ApiTester $I)
    {
        $I->wantTo('Get Course List For Faculty');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendPOST('courses/roster',[
            "type"=> "faculty",
            "course_id"=> $this->courseId,
            "person_id"=> 2
            ]);
        $courses = json_decode($I->grabResponse());
    
        $I->sendGET('courses/faculty/'.$courses->data->person_id);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->sendDELETE('courses/'.$this->courseId.'/faculty/'.$courses->data->person_id);
    }
    
    /* public function testDeleteCourse(ApiTester $I) {
        $I->wantTo('Delete a course');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        
        $I->sendPOST('courses/roster',[
            "type"=> "faculty",
            "course_id"=> 3,
            "person_id"=> 2
            ]);
        
        $I->sendPOST('courses/roster',[
            "type"=> "student",
            "course_id"=> 3,
            "person_id"=> 6
            ]);
        
        $I->sendDELETE('courses/3/sections/1');
        $I->seeResponseCodeIs(204);
    } */
    
    public function testDeleteCourseWithAcademicUpdate(ApiTester $I) {
        $I->wantTo('Delete a course With Academic Update');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
    
        $I->sendDELETE('courses/'.$this->courseId.'/sections/1');
        $I->seeResponseContains("Can't remove as academic updates are submitted for course");
        $I->seeResponseCodeIs(400);
    }
}