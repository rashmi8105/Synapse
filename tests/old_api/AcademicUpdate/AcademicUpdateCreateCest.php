<?php
require_once 'tests/api/SynapseTestHelper.php';

class AcademicUpdateCreateCest extends SynapseTestHelper
{

    private $organizationId = 1;

    private $studentId = 6;

    private $facultyId = 4;

    private $dueDate = "";
    
    private $requestId = 1;

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
        $dDate = new \DateTime("+ 7 day");
        $this->dueDate = $dDate->format("m/d/Y");
    }

    public function testCreateAURWithValidationError(ApiTester $I)
    {
        $I->wantTo('Create academic update by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicupdates', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
	
    public function testCreateAURWithSelectedStudent(ApiTester $I)
    {
        $I->wantTo('Create academic update by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicupdates', $this->getAurRequestSelectedStudent());
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        //$I->seeResponseContains('id');
    }
    
    public function testPendingUpload(ApiTester $I)
    {
    	$I->wantTo('Pending Upload academic update by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$organizationId = $this->organizationId;
    	$I->sendGET("academicupdates/pending/$organizationId");
    	$I->seeResponseCodeIs(200);
    }
    
    public function testSendReminderToFaculty(ApiTester $I)
    {
    	$I->wantTo('Pending Upload academic update by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$organizationId = $this->organizationId;
    	$requestId = $this->requestId;
    	$I->sendPUT("academicupdates/$organizationId/reminder?requestId=$requestId");
    	$I->seeResponseCodeIs(204);
    }
    
    public function testGetAcademicUpdateRequest(ApiTester $I)
    {
    	$I->wantTo('Get academic update by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$organizationId = $this->organizationId;
    	$id = $this->requestId;
    	$I->sendGET("academicupdates/$organizationId/$id");
    	$I->seeResponseCodeIs(200);
    }
	
    public function testDownloadCount(ApiTester $I)
    {
    	$I->wantTo('Get Download Count academic update by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendGET("academicupdates/uploadcount");
    	$I->seeResponseCodeIs(200);
    }

	/* Need to be Fixed */
    public function testCreateAdhocRequest(ApiTester $I)
    {
        $I->wantTo('Create academic update by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicupdates', $this->getAurRequestAdhoc());
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        //$I->seeResponseContains('request_id');
        $I->seeResponseContains('request_name');
        
        $I->seeResponseContains('academic_year_name');
        $I->seeResponseContains('academic_term_name');
        $I->seeResponseContains('subject_course');
        
        $I->seeResponseContains('academic_update_id');
        $I->seeResponseContains('student_id');
        $I->seeResponseContains('student_firstname');
        $I->seeResponseContains('student_lastname');
        $I->seeResponseContains('student_risk');
        $I->seeResponseContains('student_absences');
         
    }
	
	/* Need to be Fixed */
    public function testGetaSingleByCoordinator(ApiTester $I)
    {
        $I->wantTo('Get a single Request By Coordinator');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        //$I->sendPOST('academicupdates', $this->getAurRequestSelectedStudent());
        
        //$academicUpdateDetails = json_decode($I->grabResponse());
        //$aurId = $academicUpdateDetails->data->id;
        
        $I->sendGET('academicupdates/' . $this->organizationId . "/1");

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('request_id');
        $I->seeResponseContains('request_name');
        
        $I->seeResponseContains('academic_year_name');
        $I->seeResponseContains('academic_term_name');
        $I->seeResponseContains('subject_course');
        
        $I->seeResponseContains('academic_update_id');
        $I->seeResponseContains('student_id');
        $I->seeResponseContains('student_firstname');
        $I->seeResponseContains('student_lastname');
        $I->seeResponseContains('student_risk');
        $I->seeResponseContains('student_absences');
       
        
    }
	
    public function testGetAllAUByFaculty(ApiTester $I)
    {
    	$I->wantTo('Get All AU By Faculty');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$orgId= $this->organizationId;    
    	$I->sendGET("academicupdates?user_type=faculty&org_id=$orgId&request=all&filter=&viewmode=json");

    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    	
    }
	
	/* Need to be Fixed */
    public function testGetaSingleByFaculty(ApiTester $I)
    {
        $I->wantTo('Get a single Request By Faculty');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
       
        $I->wantTo('Authenication');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('http://127.0.0.1:8080/oauth/v2/token', [
            'client_id' => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
            'client_secret' => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
            'grant_type' => "password",
            'username' => "facultyjobtest549076f97cd58@mnv-tech.com",
            "password" => "ramesh@1974"
        ]);
        $token = json_decode($I->grabResponse());
        $token = $token->access_token;
        
        $I->amBearerAuthenticated($token);
        
        $I->sendGET('academicupdates/' . $this->organizationId . "/1?user_type=faculty");

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContains('total_records');
        $I->seeResponseContains('total_pages');
        $I->seeResponseContains('records_per_page');
        $I->seeResponseContains('current_page');
       
    }
	
    
	/* Need to be Fixed 
    public function testUpdateAcademicUpdate(ApiTester $I)
    {
        $I->wantTo('Update Academic Update');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('academicupdates', $this->getAurRequestSelectedStudent());
        
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

    private function getAurRequestSelectedStudent()
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

    private function getAurRequestAdhoc()
    {
        $request = [
            "organization_id" => $this->organizationId,
            "request_name" => '',
            "is_create" => true,
            "is_adhoc" => true,
            "request_description" => "maths Update",
            "request_due_date" => $this->dueDate,
            "request_email_subject" => "test-update email subject",
            "request_email_optional_message" => "optional_message for test-update",
            "students" => [
                "is_all" => false,
                "selected_student_ids" => ""
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
                "selected_course_ids" => "3"
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