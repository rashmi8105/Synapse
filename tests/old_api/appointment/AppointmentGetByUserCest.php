<?php
require_once 'tests/api/SynapseTestHelper.php';

class AppointmentGetByUserCest extends SynapseTestHelper
{

    private $token;

    private $organization = 1;

    private $inValidOrganization = - 1;

    private $person = 1;

    private $proxyPersonId = 2;

    private $invalidProxyPersonId = - 1;

    private $isFreeStanding = false;

    private $officeHoursId = 2;

    private $invalidFilter = 'Todays';

    public function _before(ApiTester $I)
    {
        $this->token = $this->authenticate($I);
    }

    public function testGetAppointmentsByUser(ApiTester $I)
    {
        $start = new \DateTime("now",new DateTimeZone('Asia/Kolkata'));
        $start = new \DateTime("now");
        $start->modify("+7 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+7 Days");
        $end->setTime(15, 0, 0);
        
        $loc = uniqid();
        
        $request = [
            'person_id' => $this->person,
            'organization_id' => $this->organization,
            'slot_type' => 'S',
            'slot_start' => $start->format('Y-m-d\TH:i:sO'),
            'slot_end' => $end->format('Y-m-d\TH:i:sO'),
            'location' => $loc,
            'series_info' => [
                "meeting_length"=> 60,
                "repeat_pattern"=> "D",
                "repeat_every"=> 1,
                "repeat_days"=> "0000000",
                "repeat_range"=> "D",
                "repeat_monthly_on"=> 0
            ]
        ];

        $I->wantTo('Get Appointments By User by API');
        
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendPOST('booking', $request);
        $I->sendGET("appointments/$this->organization/$this->person?filter=next");
        
        $data = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array(
            'person_id' => 1
        ));
        $I->seeResponseContainsJson(array(
            'first_name' => 'Ramesh'
        ));
        $I->seeResponseContainsJson(array(
            'last_name' => 'Kumhar'
        ));
        $I->seeResponseContainsJson(array(
            'person_id_proxy' => 0
        ));
        $I->seeResponseContainsJson(array(
            'organization_id' => 1
        ));
        
        $I->seeResponseContainsJson(array(
            'location' => $loc
        ));
    }

    public function testGetAppointmentsByUserInvlidToken(ApiTester $I)
    {
		$I->wantTo('Get Appointments By User by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated("42345234534rfedgfdsgfghfh");
		$I->sendGET("appointments/$this->organization/$this->person?filter=next");
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();
    }

 public function testViewTodayAppointment(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $start = new \DateTime("now",new DateTimeZone('Asia/Kolkata'));
        $start->add(new \DateInterval("PT30M"));
        $end = clone $start;
        $end->add(new \DateInterval("PT40M"));
    	$I->wantTo('View Today an Appointment by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('appointments/'.$this->organization.'/'.$this->person, [
            "person_id" => $this->person,
            "person_id_proxy" => 0,
            "organization_id" => $this->organization,
		    "activity_log_id" => null,
            "detail" => "api - Test details",    
                                                "detail_id" => 1,
                                                "location" => "api - Stepojevac",
                                                "description" => "api - dictumst etiam faucibus",
                                                "office_hours_id" => $this->officeHoursId,
                                                "is_free_standing" => $this->isFreeStanding,
                                                "type" => "S",
                                                "attendees" => [
                                                                [
                                "student_id" => 2,
                                "is_selected" => true,
                                "is_added_new" => true
                 ]
                                                
        ],
                                                "slot_start" => $start->format("Y-m-d\TH:i:sO"),
                                                "slot_end" => $end->format("Y-m-d\TH:i:sO"),
		    "share_options" => [
		    [
		    	"private_share" => false,
		    	"public_share"=> true,
		    	"teams_share"=> false,
		    	"team_ids"=>[
		    	[
		    		"id"=> "",
		    		"is_team_selected"=> false
		    	]
		    	]
		    ]
		    ]
                                
]);

		$I->sendGET('appointments?filter=today');	
    	$appointment = json_decode($I->grabResponse());
        $I->seeResponseContainsJson(array('person_id' => $appointment->data->person_id));
        $I->seeResponseContainsJson(array('todays_total_appointments' => $appointment->data->todays_total_appointments));
    	$I->seeResponseContains('person_id');
    	$I->seeResponseContains('todays_total_appointments');
    	$I->seeResponseContains('student_id');
    	$I->seeResponseContains('student_first_name');
    	$I->seeResponseContains('student_last_name');
    	$I->seeResponseContains('reason_id');
    	$I->seeResponseContains('reason_text');
    	$I->seeResponseContains('appointment_start');
    	$I->seeResponseContains('appointment_end');
	    $I->seeResponseIsJson ( array (
			'appointment_id' => $appointment->data->appointments[0]->appointment_id,
	        'student_id' => $appointment->data->appointments[0]->attendees[0]->student_id,
	        'student_first_name' => $appointment->data->appointments[0]->attendees[0]->student_first_name,
	        'student_last_name' => $appointment->data->appointments[0]->attendees[0]->student_last_name,
	        'reason_id' => $appointment->data->appointments[0]->reason_id,
	        'reason_text' => $appointment->data->appointments[0]->reason_text,
	        'appointment_start' => $appointment->data->appointments[0]->appointment_start,
	        'appointment_end' => $appointment->data->appointments[0]->appointment_end
        ) );
		
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();   
    	 
    }

    public function testViewTodayAppointmentWithInvalidFilter(ApiTester $I)
    {
        $I->wantTo('View Today an Appointment With Invalid Filter by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET('appointments?filter=' . $this->invalidFilter);
        $I->seeResponseContains('Filter Today Not Found');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testViewTodayAppointmentWithInvalidAuthentication(ApiTester $I)
    {
        $I->wantTo('View Today an Appointment With Invalid Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
		$I->amBearerAuthenticated('dfdfdfdfdfdfdf');
        $I->sendGET('appointments?filter=today');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }
}
