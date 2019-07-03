<?php
require_once 'tests/api/SynapseTestHelper.php';

class AcademicUpdateListCest extends SynapseTestHelper
{

    private $newAcademicUpdate;

    private $newRequestId = "";

    private $organizationId = 1;

    private $requestName = "";

    private $requestDesc = "AU test description";

    private $userCoordinator = "coordinator";

    private $userFaculty = "faculty";

    private $randomNumber = 0;

    private $studentId = 6;

    private $facultyId = 4;

    private $invalidOrganizationId = 0;

    private $invalidRequestId = 0;

    private $orgNotFound = "Organization Not Found.";

    private $auNotFound = "Academic Update Request Not Found.";

    private $courseNotfound = "Course not found";

    private $dueDate = "";

    private $courseId = "1";

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
        $this->randomNumber = mt_rand();
        $this->requestName .= "Test AU Request " . $this->randomNumber;
        $dDate = new \DateTime("+ 7 day");
        $this->dueDate = $dDate->format("m/d/Y");
    }

    public function testGetStudentsByOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get Students of actively enrolled and associated with currently running course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->organizationId . '/students');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'organization_id' => $this->organizationId
        ));
        $I->seeResponseIsJson(array(
            'student_details' => array(
                'student_id' => $this->studentId
            )
        ));
    }

    public function testGetStudentsByInvalidOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get Students of actively enrolled with currently running course with Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->invalidOrganizationId . '/students');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->orgNotFound);
    }

    public function testGetFacultiesByOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get Faculties of actively enrolled and associated with currently running course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->organizationId . '/faculties');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'organization_id' => $this->organizationId
        ));
        $I->seeResponseIsJson(array(
            'staff_details' => array(
                'staff_id' => $this->facultyId
            )
        ));
    }

    public function testGetFacultiesByInvalidOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get Faculties of actively enrolled with currently running course with Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->invalidOrganizationId . '/faculties');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->orgNotFound);
    }

    public function testGetCoursesByOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get Courses which are currently running by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->organizationId . '/courses');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'organization_id' => $this->organizationId
        ));
    }

    public function testGetCoursesByInvalidOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get Courses which are currently running with Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->invalidOrganizationId . '/courses');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->orgNotFound);
    }

    public function testGetGroupsByOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get list of groups for an organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->organizationId . '/groups');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'organization_id' => $this->organizationId
        ));
    }

    public function testGetGroupsByInvalidOrganizationCourse(ApiTester $I)
    {
        $I->wantTo('Get list of groups for an Invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->invalidOrganizationId . '/groups');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->orgNotFound);
    }
	/* Need to be Fixed*/
    public function testCreateAcademicUpdate(ApiTester $I)
    {
        $I->wantTo('Create academic update by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicupdates', $this->getAurRequestSelectedStudents());
        $this->newAcademicUpdate = json_decode($I->grabResponse());
        //$this->newRequestId = $this->newAcademicUpdate->data->id;
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        //$I->seeResponseContains('id');
    }
	

    public function testGetAURequestForCoordinator(ApiTester $I)
    {
        $I->wantTo('Get Academic update request for a coordinator by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates?user_type=' . $this->userCoordinator . '&org_id=' . $this->organizationId . '&request=all&filter=&viewmode=json');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'academic_updates_open' => array(
                'request_id' => $this->newRequestId
            )
        ));
    }

    public function testGetMyOpenAURequestForCoordinator(ApiTester $I)
    {
        $I->wantTo('Get Academic update request for a coordinator with request filter my open by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates?user_type=' . $this->userCoordinator . '&org_id=' . $this->organizationId . '&request=myopen&filter=&viewmode=json');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'academic_updates_open' => array(
                'request_id' => $this->newRequestId
            )
        ));
    }

    public function testGetAURequestForCoordinatorWithInvalidOrg(ApiTester $I)
    {
        $I->wantTo('Get Academic update request for a coordinator with invalid organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates?user_type=' . $this->userCoordinator . '&org_id=' . $this->invalidOrganizationId . '&request=all&filter=&viewmode=json');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->orgNotFound);
    }
	/* Need to be Fixed*/
    public function testCancelAURequestForCoordinator(ApiTester $I)
    {
        $I->wantTo('Cancel a Academic upadte request by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('academicupdates?requestId=2&org_id=' . $this->organizationId);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
	

    public function testCancelAURequestWithInvalidRequestId(ApiTester $I)
    {
        $I->wantTo('Cancel a Academic upadte request with invalid request Id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('academicupdates?requestId=' . $this->invalidRequestId . '&org_id=1');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->auNotFound);
    }
	/* Need to be Fixed
    public function testUpdateAcademicUpdate(ApiTester $I)
    {
        $I->wantTo('Create academic update by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/' . $this->organizationId . '/courses');
        $course = json_decode($I->grabResponse());
        $this->courseId = $course->data->course_details[0]->course_id;
        
        $I->sendPOST('academicupdates', $this->getAurRequestSelectedStudent($this->courseId));
        
        $academicUpdateDetails = json_decode($I->grabResponse());
        $aurId = $academicUpdateDetails->data->id;
        $I->sendGET('academicupdates/' . $this->organizationId . "/$aurId");
        
        $academicUpdateDetails = json_decode($I->grabResponse());
        
        $I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
            'client_id' => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
            'client_secret' => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
            'grant_type' => "password",
            'username' => "facultyjobtest549076f97cd58@mnv-tech.com",
            "password" => "ramesh@1974"
            ]);
        $token = json_decode($I->grabResponse());
        $token = $token->access_token;
        
        $updateId = $academicUpdateDetails->data->request_details[0]->student_details[0]->academic_update_id;
        $updateDetails = $this->getAcademicUpdate($aurId, $updateId);
        $I->amBearerAuthenticated($token);
        $I->sendPUT('academicupdates', $updateDetails);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
	*/
	/* Need to be Fixed*/
    public function testAcademicUpdateStudentHistory(ApiTester $I)
    {
        $I->wantTo('Get academic update student history by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/history?org_id=' . $this->organizationId . '&course_id=' . $this->courseId . '&student_id=6');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'organization_id' => $this->organizationId
        ));
        $I->seeResponseIsJson(array(
            'course_id' => $this->courseId
        ));
    }
	

    public function testAcademicUpdateStudentHistoryInvalidOrganization(ApiTester $I)
    {
        $I->wantTo('Get academic update student history with invalid organization id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/history?org_id=' . $this->invalidOrganizationId . '&course_id=' . $this->courseId . '&student_id=6');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->orgNotFound);
    }
	/* Need to be Fixed*/
    public function testAcademicUpdateStudentHistoryInvalidCourse(ApiTester $I)
    {
        $I->wantTo('Get academic update student history with invalid course by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('academicupdates/history?org_id=' . $this->organizationId . '&course_id=0&student_id=6');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains($this->courseNotfound);
    }
	

    private function getAcademicUpdate($reqid, $updateId)
    {
        $request = [
            'request_id' => $reqid,
            'save_type' => 'send'
        ];
        $student_details[] = [
            'academic_update_id' => $updateId,
            'student_id' => $this->studentId,
            'student_risk' => 'low',
            'student_grade' => '',
            'student_absences' => 2,
            'student_comments' => '',
            'student_refer' => false,
            'student_send' => true
        ];
        $requestDetails[] = [
            'student_details' => $student_details
        ];
        $request['request_details'] = $requestDetails;
        return $request;
    }

    private function getAurRequestSelectedStudent($courseId)
    {
        $request = [
            "organization_id" => $this->organizationId,
            "request_name" => 'Maths Update' . uniqid(),
            "is_create" => true,
            "request_description" => "maths Update",
            "request_due_date" => $this->dueDate,
            "request_email_subject" => "test-update email subject",
            "request_email_optional_message" => "optional_message for test-update",
            "students" => [
                "is_all" => false,
                "selected_student_ids" => "6"
            ],
            "staff" => [
                "is_all" => false,
                "selected_staff_ids" => ""
            ],
            "staff" => [
                "is_all" => false,
                "selected_staff_ids" => ""
            ],
            "groups" => [
                "is_all" => false,
                "selected_group_ids" => ""
            ],
            "courses" => [
                "is_all" => false,
                "selected_course_ids" => $courseId
            ],
            "profile_items" => [
                "isps" => [],
                "ebi" => []
            ],
            "static_list" => [
                "is_all" => false,
                "selected_static_ids" => ""
            ]
        ];
        return $request;
    }
	private function getAurRequestSelectedStudents()
    {
        $request = [
            "organization_id" => $this->organizationId,
            "is_create" => true,
            "request_name" => 'Maths Update' . uniqid(),
            "request_description" => "maths Update",
            "request_due_date" => $this->dueDate,
            "request_email_subject" => "test-update email subject",
            "request_email_optional_message" => "optional_message for test-update",
            "students" => [
                "is_all" => false,
                "selected_student_ids" => "6"
            ],
            "staff" => [
                "is_all" => false,
                "selected_staff_ids" => ""
            ],
            "staff" => [
                "is_all" => false,
                "selected_staff_ids" => ""
            ],
            "groups" => [
                "is_all" => false,
                "selected_group_ids" => ""
            ],
            "courses" => [
                "is_all" => true,
                "selected_course_ids" => ""
            ],
            "profile_items" => [
                "isps" => [],
                "ebi" => []
            ],
            "static_list" => [
                "is_all" => false,
                "selected_static_ids" => ""
            ]
        ];
        return $request;
    }
}