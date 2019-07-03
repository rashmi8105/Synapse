<?php
require_once 'tests/api/SynapseTestHelper.php';

class StudentAppointmentServiceCest extends SynapseTestHelper
{

    private $personStudentId = 2;

    private $personFacultyId = 1;

    private $orgId = 1;

    private $invalidStudentId = 0;

    private $invalidFaculty = 0;

    private $invalidOrganization = 0;

    private $notValidStudent = "Not a valid Student Id.";

    private $orgNotFound = "Organization Not Found.";

    private $officeHours = "";

    private $officeHoursId = "";

    private $appointmentNotFound = "Appointment Not Found.";

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }
	//Need to be Fixed
    public function testGetStudentCampuses(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->studentAuthenticate($I));
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->personStudentId . '/campuses');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'campus' => array(
                'organization_id' => $this->orgId
            )
        ));
    }
	

    public function testGetStudentCampusesByInvalidStudentId(ApiTester $I)
    {
        $I->wantTo('Get Student Faculty Campus Connections with Invalid Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('students/' . $this->invalidStudentId . '/campuses');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains($this->notValidStudent);
        $I->seeResponseIsJson();
    }

    public function testGetFacultyOfficeHours(ApiTester $I)
    {
        $I->wantTo('Create a OfficeHour by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $currData = new DateTime("now");
        /* $sdate = new DateTime("+ 20 hour");
        $edate = new DateTime("+ 21 hour"); */
        $sdate = $currData->add(new DateInterval("PT20H"));
        $edate = $currData->add(new DateInterval("PT21H"));
        $I->sendPOST('booking', [
            "person_id" => 1,
            "person_id_proxy"=> null,
            "organization_id"=> 1,
            "slot_type"=> "I",
            "slot_start"=> $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO'),
            "location" => "Porto Real",
            "series_info"=> [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
        ],
        "meeting_length"=> 60
        ]);
        $this->officeHours = json_decode($I->grabResponse());
        $I->sendGET('officehours?facultyId=' . $this->personFacultyId . '&orgId=' . $this->orgId . '&filter=month');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'person_id' => $this->personFacultyId
        ));
    }

    public function testCreateStudentAppointment(ApiTester $I)
    {
        $I->wantTo('Create an Appointment by Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token );
        $I->haveHttpHeader('Accept', 'application/json');
        $sHrs = rand(100,200);
        $eHrs = $sHrs+1;
		$sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $I->sendPOST('booking', [
            "person_id" => $this->personFacultyId,
            "person_id_proxy" => 0,
            "organization_id" => $this->orgId,
            "slot_type" => "I",    
			"slot_start" => $sdate->format('Y-m-d\TH:i:sO'),	
			"slot_end" => $edate->format('Y-m-d\TH:i:sO'),
			"location" => "Porto Real",	

            "series_info" => [
                "meeting_length"=> 60,
                "repeat_pattern"=> "D",
                "repeat_every"=> 1,
                "repeat_days"=> "",
                "repeat_occurence"=> "0",
                "repeat_range"=> "N",
                "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
            
        ]);
        $officeHours = json_decode($I->grabResponse());
        $I->sendPOST('students/' . $this->personStudentId . '/appointments', [
            "person_id" => $this->personFacultyId,
            "organization_id" => $this->orgId,
            "detail" => "api - create",
            "detail_id" => 19,
            "location" => "api - Stepojevac",
            "description" => "api - dictumst etiam faucibus",
            "office_hours_id" => $officeHours->data->office_hours_id,
            "type" => "S",
            "slot_start" => $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO')
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'person_id' => $this->personFacultyId
        ));
        $I->seeResponseIsJson(array(
            'location' => 'api - Stepojevac'
        ));
        $I->seeResponseContains('appointment_id');
    }
	

    public function testCreateStudentAppointmentInvalidStudent(ApiTester $I)
    {
        $I->wantTo('Create an Appointment by Invalid Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sdate = new DateTime("+ 20 hour");
        $edate = new DateTime("+ 21 hour");
        //$this->officeHoursId = $this->officeHours->data->office_hours_id;
        $I->sendPOST('students/' . $this->invalidStudentId . '/appointments', [
            "person_id" => $this->personFacultyId,
            "organization_id" => $this->orgId,
            "detail" => "api - create",
            "detail_id" => 1,
            "location" => "api - Stepojevac",
            "description" => "api - dictumst etiam faucibus",
            "office_hours_id" => null,
            "type" => "S",
            "slot_start" => $sdate->format('Y-m-d H:i:s'),
            "slot_end" => $edate->format('Y-m-d H:i:s')
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testCancelStudentAppointmentInvalidAppointment(ApiTester $I)
    {
        $I->wantTo('Create an Appointment by Invalid Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendDELETE('students/' . $this->personStudentId . '/appointments?appointmentId=0');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains($this->appointmentNotFound);
        $I->seeResponseIsJson();
    }
    
    public function testCancelStudentAppointment(ApiTester $I)
    {
        $I->wantTo('Create an Appointment by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sHrs = rand(201,300);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $I->sendPOST('booking', [
            "person_id" => $this->personFacultyId,
            "person_id_proxy" => 0,
            "organization_id" => $this->orgId,
            "slot_type" => "I",
            "slot_start" => $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO'),
            "location" => "Porto Real",
        
            "series_info" => [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
        
            ]);
        $officeHours = json_decode($I->grabResponse());
        $I->sendPOST('students/' . $this->personStudentId . '/appointments', [
            "person_id" => $this->personFacultyId,
            "organization_id" => $this->orgId,
            "detail" => "api - create",
            "detail_id" => 19,
            "location" => "api - Stepojevac",
            "description" => "api - dictumst etiam faucibus",
            "office_hours_id" => $officeHours->data->office_hours_id,
            "type" => "S",
            "slot_start" => $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO')
            ]);
        $app = json_decode($I->grabResponse());
        $I->sendDELETE('students/'.$this->personStudentId.'/appointments?appointmentId='.$app->data->appointment_id);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
    
    public function testGetStudentsUpcomingAppointments(ApiTester $I)
    {
        $I->wantTo('Get Student Upcoming Appointment by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $sHrs = rand(301,400);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $I->sendPOST('booking', [
            "person_id" => $this->personFacultyId,
            "person_id_proxy" => 0,
            "organization_id" => $this->orgId,
            "slot_type" => "I",
            "slot_start" => $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO'),
            "location" => "Porto Real",
        
            "series_info" => [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
        
            ]);
        $officeHours = json_decode($I->grabResponse());
        $I->sendPOST('students/' . $this->personStudentId . '/appointments', [
            "person_id" => $this->personFacultyId,
            "organization_id" => $this->orgId,
            "detail" => "api - create",
            "detail_id" => 19,
            "location" => "api - Stepojevac",
            "description" => "api - dictumst etiam faucibus",
            "office_hours_id" => $officeHours->data->office_hours_id,
            "type" => "S",
            "slot_start" => $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO')
            ]);
        $app = json_decode($I->grabResponse());
        $I->sendGET('students/'.$this->personStudentId.'/appointments?type=upcoming');
        $getApp = json_decode($I->grabResponse());
        if(count($getApp->data->calendar_time_slots) > 0){
            foreach($getApp->data->calendar_time_slots as $app){
                $I->seeResponseContains('appointment_id');
                $I->seeResponseContains('organization_id');
                $I->seeResponseContains('person_id');
                $I->seeResponseContains('location');
                $I->seeResponseContains('description');
            }
        }
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
    
    public function testGetStudentsUpcomingAppointmentsInvalidStudent(ApiTester $I)
    {
        $I->wantTo('Get Student Upcoming Appointment Invalid Student by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');

        $I->sendGET('students/'.$this->invalidStudentId.'/appointments?type=upcoming');
        $getApp = json_decode($I->grabResponse());
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testGetFacultyOfficeHoursByTerm(ApiTester $I)
    {
        $I->wantTo('Create a OfficeHour By Term by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $currData = new DateTime("now");
        $sHrs = rand(401,500);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $I->sendPOST('booking', [
            "person_id" => 1,
            "person_id_proxy"=> null,
            "organization_id"=> 1,
            "slot_type"=> "I",
            "slot_start"=> $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO'),
            "location" => "Porto Real",
            "series_info"=> [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
            ]);
        $this->officeHours = json_decode($I->grabResponse());
        $I->sendGET('officehours?facultyId=' . $this->personFacultyId . '&orgId=' . $this->orgId . '&filter=term');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'person_id' => $this->personFacultyId
        ));
    }
    
    public function testGetFacultyOfficeHoursByToday(ApiTester $I)
    {
        $I->wantTo('Create a OfficeHour By Today by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $currData = new DateTime("now");
        $sHrs = rand(501,600);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $I->sendPOST('booking', [
            "person_id" => 1,
            "person_id_proxy"=> null,
            "organization_id"=> 1,
            "slot_type"=> "I",
            "slot_start"=> $sdate->format('Y-m-d\TH:i:sO'),
            "slot_end" => $edate->format('Y-m-d\TH:i:sO'),
            "location" => "Porto Real",
            "series_info"=> [
            "meeting_length"=> 60,
            "repeat_pattern"=> "D",
            "repeat_every"=> 1,
            "repeat_days"=> "",
            "repeat_occurence"=> "0",
            "repeat_range"=> "N",
            "repeat_monthly_on"=> 0
            ],
            "meeting_length"=> 60
            ]);
        $this->officeHours = json_decode($I->grabResponse());
        $I->sendGET('officehours?facultyId=' . $this->personFacultyId . '&orgId=' . $this->orgId . '&filter=today');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(array(
            'person_id' => $this->personFacultyId
        ));
    }
    
}