<?php
require_once 'tests/api/SynapseTestHelper.php';
class AppointmentsCest extends SynapseTestHelper 
{
	private $token;
	private $organization = 1;
	private $inValidOrganization = -1;
	private $person = 1;
	private $delegateToPerson = 2;
	private $isSelectedTrue = true;
	private $isSelectedFalse = true;
	private $isDeletedTrue = true;
	private $isDeletedFalse = false;
    private $invalidPersonId = -1;
	private $proxyPersonId = 2;
	private $invalidProxyPersonId = -1;
	private $managedPersonId = 1;
	
	private $personId = 1;
	private $organizationId = 1;
	private $officeHoursId = 2;
	private $isFreeStanding = false;
	private $slotStart = "";
	private $slotEnd = "";
	
	private $invalidSlotEnd = "2014-12-26 19:30:00";
	private $invalidAppointmentId = -1;
	
	private $validStudent = 8;

	public function _before(ApiTester $I) {
		$this->token = $this->authenticate ( $I );
		$start = new \DateTime("now",new DateTimeZone('Asia/Kolkata'));
		$start->add(new \DateInterval("PT1H"));
		$end = clone $start;
		$end->add(new \DateInterval("PT1H"));
		
		$this->slotStart = $start->format("Y-m-d\TH:i:sO");
		$this->slotEnd = $end->format("Y-m-d\TH:i:sO");
		
		$invalid = clone $start;
		$invalid->sub(new \DateInterval('P1Y'));
		$this->invalidSlotEnd = $invalid->format("Y-m-d\TH:i:sO");
	}
	
	private function getDelegatesArray($organization, $person, $delegatedToPersonId, $isSelected, $isDeleted) {
		$delegator = array ();
		$delegator ['person_id'] = $person;
		$delegator ['organization_id'] = $organization;
		$delegates = array (
				"delegated_to_person_id" => $delegatedToPersonId,
				"is_selected" => $isSelected,
				"is_deleted" => $isDeleted 
		);
		$delegator ['delegated_users'] [] = $delegates;
		return json_encode ( $delegator );
	}
	public function testcreateDelegateUserInvalidAuthentication(ApiTester $I) {
		$delegateParams = $this->getDelegatesArray ( $this->organization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedFalse );
		$I->wantTo ( 'Create delegate with invalid authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( "invalid_token" );
		$I->sendPOST ( 'appointments/'.$this->organization.'/proxy', $delegateParams );
		$I->seeResponseCodeIs ( 401 );
		$I->seeResponseIsJson ();
	}
  public function testcreateDelegateUserValidValues(ApiTester $I, $scenario) {
    //$scenario->skip("Failed");
		$delegateParams = $this->getDelegatesArray ( $this->organization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedFalse );

		$I->wantTo ( 'Create delegate with invalid authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPOST ( 'appointments/'.$this->organization.'/proxy', $delegateParams );
		$I->seeResponseContains ( 'person_id' );
		$I->seeResponseContains ( 'organization_id' );
		$I->seeResponseContains ( 'delegated_users' );
		$I->seeResponseContains ( 'calendar_sharing_id' );
		$I->seeResponseIsJson ( array (
				'organization_id' => $this->organization
		) );
		$I->seeResponseIsJson ( array (
				'person_id' => $this->person
		) );
		$I->seeResponseCodeIs ( 201 );
		$I->seeResponseIsJson ();
	}
	/*
	public function testcreateDelegateUserInValidValues(ApiTester $I) {
		$delegateParams = $this->getDelegatesArray ( $this->inValidOrganization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedFalse );

		$I->wantTo ( 'Create delegate with invalid authentication by API' );
		$I->haveHttpHeader ( 'Content-Type', 'application/json' );
		$I->amBearerAuthenticated ( $this->token );
		$I->sendPOST ( 'appointments/'.$this->organization.'/proxy', $delegateParams );
		$I->seeResponseCodeIs ( 400 );
		$I->seeResponseIsJson ();
	}
	*/

	public function testProxySelectedInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get proxy selected list invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated("invalid_token");
		$I->sendGET('appointments/'.$this->organization.'/proxySelected');
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();

	}
	public function testProxySelectedWithValidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get proxy selected list with valid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('appointments/'.$this->organization.'/proxySelected?user_id='.$this->person);
		$I->seeResponseIsJson ( array (
				'organization_id' => $this->organization
		) );
		$I->seeResponseIsJson ( array (
				'person_id' => $this->person
		) );
		$I->seeResponseContains('delegated_users');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	public function testManagedUsersInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get managed Users list invalid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated("invalid_token");
		$I->sendGET('appointments/'.$this->organization.'/managedUsers');
		$I->seeResponseCodeIs(401);
		$I->seeResponseIsJson();

	}
	public function testManagedUsersWithValidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get Managed Users list with valid authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('appointments/'.$this->organization.'/managedUsers?person_id_proxy='.$this->person);
		$I->seeResponseIsJson ( array (
				'organization_id' => $this->organization
		) );
		$I->seeResponseIsJson ( array (
				'person_id_proxy' => $this->person
		) );
		$I->seeResponseContains('managed_users');

		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}

	public function testGetApponitmentsbyValidProxyPerson(ApiTester $I)
	{
		$I->wantTo('Get the list of appointments for the given proxy');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->proxyPersonId.'?managed_person_id='.$this->managedPersonId);
		$I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}
	public function testGetApponitmentsbyInvalidProxyPerson(ApiTester $I)
	{
		$I->wantTo('Test Invalid Proxy Person ID');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->invalidProxyPersonId.'?managed_person_id='.$this->managedPersonId);
        $I->seeResponseContains("Person Not Found.");
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}

	public function testGetCurrentApponitments(ApiTester $I)
	{
		$I->wantTo('Get the list of appointments for the current frequency');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->proxyPersonId.'?frequency=current&managed_person_id='.$this->managedPersonId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}
	public function testGetNextWeekApponitments(ApiTester $I)
	{
		$I->wantTo('Get the list of appointments for the Next frequency');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->proxyPersonId.'?frequency=next&managed_person_id='.$this->managedPersonId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}
	public function testGetNextTwoWeekApponitments(ApiTester $I)
	{
		$I->wantTo('Get the list of appointments for the frequency of next two weeks');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->proxyPersonId.'?frequency=next-two&managed_person_id='.$this->managedPersonId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}
	public function testGetNextMonthApponitments(ApiTester $I)
	{
		$I->wantTo('Get the list of appointments for the frequency of next month');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->proxyPersonId.'?frequency=next-month&managed_person_id='.$this->managedPersonId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}
	public function testGetPastApponitments(ApiTester $I)
	{
		$I->wantTo('Get the list of appointments for the frequency of Past');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->proxyPersonId.'?frequency=&managed_person_id='.$this->managedPersonId);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
	}

	public function testGetCurrentApponitmentsasDefault(ApiTester $I)
	{
		$I->wantTo('Get the list of appointments for the current frequency as default');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->sendGET('appointments/'.$this->organization.'/proxy/'.$this->proxyPersonId.'?frequency=current&managed_person_id='.$this->managedPersonId);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}

	public function testCreateAppointments(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create an Appointment by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
            "organization_id" => $this->organizationId,
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
			"slot_start" => $this->slotStart,
			"slot_end" => $this->slotEnd,
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
        $I->seeResponseCodeIs(201);
		$I->seeResponseIsJson ( array (
				'person_id' => $this->personId, 'organization_id' => $this->organizationId,
				'attendees[0]["student_id"]'=> 2,'attendees[0]["is_selected"]'=> true
		) );
		$I->seeResponseContains('appointment_id');
        $I->seeResponseIsJson();
    }

    
    public function testCreateAppointmentsInvalidProxy(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create an Appointment with invalid person_id_proxy by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->invalidProxyPersonId,
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
								"student_id" => 2,
								"is_selected" => true,
								"is_added_new" => true
								]

        ],
			"slot_start" => $this->slotStart,
			"slot_end" => $this->slotEnd

                                ]); 


		$I->seeResponseContains ( "PersonIdProxy Not Found.");
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    } 
              
    public function testCreateAppointmentsInvalidSlotEnd(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Create an Appointment with invalid slot end date by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
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
                                "student_id" => 2,
                                "is_selected" => true,
                                "is_added_new" => true
								]
                                                
        ],
		"slot_start" => $this->slotStart,
		"slot_end" => $this->invalidSlotEnd
                                
		]);
        $I->seeResponseContains("End date\/time should be greater then start date\/time");
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }          
                 
    public function testEditAppointments(ApiTester $I, $scenario)
    {
        //$scenario->skip("Errored");
	    $I->wantTo('Edit an Appointment by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept-Language', null);
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
            "organization_id" => $this->organizationId,
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
			"slot_start" => $this->slotStart,
			"slot_end" => $this->slotEnd,
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

			$appointment = json_decode($I->grabResponse());
			$I->sendPUT('appointments/'.$this->organizationId.'/'.$appointment->data->appointment_id, [            
							"appointment_id" => $appointment->data->appointment_id,
							"person_id" => $appointment->data->person_id,
							"person_id_proxy" => $appointment->data->person_id_proxy,
							"organization_id"=> $appointment->data->organization_id,
			                "activity_log_id" => null,
							"detail" => "api edit Reason",
							"detail_id" => 1,
							"location" => "edit - Stepojevac",
							"description" => "api edit - dictumst etiam faucibus",
							"office_hours_id" => $this->officeHoursId,
							"is_free_standing" => $this->isFreeStanding,
							"type" => "S",
							"attendees"=> [
							[
								"student_id"=> 2,
								"is_selected" => true,
								"is_added_new" => false
							]
			],
			"slot_start" => $this->slotStart,
			"slot_end" => $this->slotEnd,
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
			$I->seeResponseCodeIs(200);
			$I->seeResponseIsJson ( array (
				'person_id' => $this->personId, 'organization_id' => $this->organizationId,
				'location' => 'edit - Stepojevac',
				'attendees[1]["student_id"]'=> 4,'attendees[1]["is_selected"]'=> false
			) );
			$I->seeResponseContains('appointment_id');
			$I->seeResponseIsJson();

    }
               
    public function testEditAppointmentsInvalidAppointmentId(ApiTester $I, $scenario)
    {	   
        //$scenario->skip("Failed");
		$I->wantTo('Edit an Appointment with an invalid appointment id by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
		$today = date("Y-m-d");		
		$slotStart = date('Y-m-d H:i:s', strtotime($today.'+ 1 days')); 
		$slotEnd = date('Y-m-d H:i:s', strtotime($today.'+ 2 days')); 		
		$I->sendPUT('appointments/'.$this->organizationId.'/0', [            
						"appointment_id" => $this->invalidAppointmentId,
						"person_id" => $this->personId,
						"person_id_proxy" => $this->proxyPersonId,
						"organization_id"=> $this->organizationId,
		                "activity_log_id" => null,
						"detail" => "api edit Reason",
						"detail_id" => 1,
						"location" => "edit - Stepojevac",
						"description" => "api edit - dictumst etiam faucibus",
						"office_hours_id" => $this->officeHoursId,
						"is_free_standing" => $this->isFreeStanding,
						"type" => "S",
						"attendees"=> [
						[
							"student_id"=> 2,
							"is_selected" => true,
							"is_added_new" => false
						]
		],
						"slot_start" => $this->slotStart,
						"slot_end" => $this->slotEnd,
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
		
		$appointment = json_decode($I->grabResponse());	
		$I->seeResponseContains("Appointment Not Found.");
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
                                
    }   
                
	public function testCancelAppointments(ApiTester $I, $scenario)
    {
//        $scenario->skip("Errored");
        $I->wantTo('Cancel an Appointment by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
            "organization_id" => $this->organizationId,
            "activity_log_id" => null,
            "detail" => "api - Test details for delete",
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
			"slot_start" => $this->slotStart,
			"slot_end" => $this->slotEnd,
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
        $appointment = json_decode($I->grabResponse());
        $I->sendDELETE('appointments/'.$this->organizationId.'/appointmentId?appointmentId='.$appointment->data->appointment_id);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
	}

	public function testViewAnAppointment(ApiTester $I, $scenario)
    {
//        $scenario->skip("Errored");
    	$I->wantTo('View an Appointment by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
            "organization_id" => $this->organizationId,
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
                                                "slot_start" => $this->slotStart,
                                                "slot_end" => $this->slotEnd,
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
    	
    	$appointment = json_decode($I->grabResponse());
		
		$I->sendGET('appointments/'.$this->organizationId.'/'.$this->personId.'/appointmentId?appointmentId='.$appointment->data->appointment_id);		
    	$I->seeResponseContainsJson(array('appointment_id' => $appointment->data->appointment_id));
    	$I->seeResponseContains('person_id');		
    	$I->seeResponseContains('organization_id');
    	$I->seeResponseContains('detail');
    	$I->seeResponseContains('detail_id');
    	$I->seeResponseContains('location');
    	$I->seeResponseContains('description');
    	$I->seeResponseContains('is_free_standing');
    	$I->seeResponseContains('type');
		$I->seeResponseContains('attendees');
		$I->seeResponseContains('slot_start');
    	$I->seeResponseContains('slot_end');  
		$I->seeResponseIsJson ( array (
			'person_id' => $this->personId, 
			'organization_id' => $this->organizationId,
			'detail' => $appointment->data->detail,
			'detail_id' => $appointment->data->detail_id,
			'location' => $appointment->data->location,
			'description' => $appointment->data->description,
			'is_free_standing' => $appointment->data->is_free_standing,
			'type' => $appointment->data->type,			
			'attendees[0]["student_id"]'=> 2,
			'attendees[0]["is_selected"]'=> true,
			'slot_start' => $appointment->data->slot_start,
			'slot_end' => $appointment->data->slot_end          
        ) );
		
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();    	
    }

	public function testViewAnInvalidPersonAppointment(ApiTester $I, $scenario)
    {
//        $scenario->skip("Errored");
    	$I->wantTo('View an Invalid Person Appointment by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
            "organization_id" => $this->organizationId,
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
                                                "slot_start" => $this->slotStart,
                                                "slot_end" => $this->slotEnd,
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
    	
    	$appointment = json_decode($I->grabResponse());
		
		$I->sendGET('appointments/'.$this->organizationId.'/'.$this->invalidPersonId.'/appointmentId?appointmentId='.$appointment->data->appointment_id);		
    	$I->seeResponseContains('Person Not Found');    	
    	$I->seeResponseCodeIs(400);
    	$I->seeResponseIsJson();    	
    }

	public function testSaveAppointmentAttendees(ApiTester $I, $scenario)
    {
//        $scenario->skip("Errored");
    	$I->wantTo('Save an Appointment to Attendees by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated( $this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('appointments/'.$this->organizationId.'/'.$this->personId, [
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
            "organization_id" => $this->organizationId,
		    "activity_log_id" => null,
            "detail" => "api - create",    
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
			"slot_start" => $this->slotStart,
			"slot_end" => $this->slotEnd,
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
		$appointment = json_decode($I->grabResponse());
							
		$I->sendPOST('appointments/attendees', [
           "appointment_id" => $appointment->data->appointment_id,
            "person_id" => $this->personId,
            "person_id_proxy" => $this->proxyPersonId,
			"organization_id" => $this->organizationId,
            "attendees" => [
								[
									"student_id" => 2,
									"is_attended" => true
									
								]													
							]                                
        ]);    	    	 	
    	$I->seeResponseCodeIs(204);
    	$I->seeResponseIsJson();    	
    }

    public function testViewAnAppointmentActivity(ApiTester $I, $scenario)
    {
    	$I->wantTo('View an Activity Appointment by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendPOST('appointments/' . $this->organizationId . '/' . $this->personId, [
    			"person_id" => $this->personId,
    			"person_id_proxy" => $this->proxyPersonId,
    			"organization_id" => $this->organizationId,
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
    			"student_id" => $this->validStudent,
    			"is_selected" => true,
    			"is_added_new" => true
    			]
    			]
    			,
    			"slot_start" => $this->slotStart,
    			"slot_end" => $this->slotEnd,
    			"share_options" => [
    			[
    			"private_share" => false,
    			"public_share" => true,
    			"teams_share" => false,
    			"team_ids" => [
    			[
    			"id" => "",
    			"is_team_selected" => false
    			]
    			]
    			]
    			]
    			]
    	);
    
    	$appointment = json_decode($I->grabResponse());
    	$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=appointment&is-interaction=false');
    	$data = json_decode($I->grabResponse());
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
    
    public function testViewAnAppointmentActivityRelated(ApiTester $I, $scenario)
    {
    	$I->wantTo('View an Activity Related Appointment by API');
    	$I->haveHttpHeader('Content-Type', 'application/json');
    	$I->amBearerAuthenticated($this->token);
    	$I->haveHttpHeader('Accept', 'application/json');
    	$I->sendPOST('appointments/' . $this->organizationId . '/' . $this->personId, [
    			"person_id" => $this->personId,
    			"person_id_proxy" => $this->proxyPersonId,
    			"organization_id" => $this->organizationId,
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
    			"student_id" => $this->validStudent,
    			"is_selected" => true,
    			"is_added_new" => true
    			]
    			]
    			,
    			"slot_start" => $this->slotStart,
    			"slot_end" => $this->slotEnd,
    			"share_options" => [
    			[
    			"private_share" => false,
    			"public_share" => true,
    			"teams_share" => false,
    			"team_ids" => [
    			[
    			"id" => "",
    			"is_team_selected" => false
    			]
    			]
    			]
    			]
    			]
    	);
    
    	$appointment = json_decode($I->grabResponse());
    	$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=appointment&is-interaction=false');
    	$lists = json_decode($I->grabResponse());
    	$activityLogId = $lists->data->activities[0]->activity_log_id;
    	$I->sendPOST('appointments/' . $this->organizationId . '/' . $this->personId, [
    			"person_id" => $this->personId,
    			"person_id_proxy" => $this->proxyPersonId,
    			"organization_id" => $this->organizationId,
    			"activity_log_id" => $activityLogId,
    			"detail" => "api - Test details",
    			"detail_id" => 1,
    			"location" => "api - Stepojevac",
    			"description" => "api - dictumst etiam faucibus",
    			"office_hours_id" => $this->officeHoursId,
    			"is_free_standing" => $this->isFreeStanding,
    			"type" => "S",
    			"attendees" => [
    			[
    			"student_id" => $this->validStudent,
    			"is_selected" => true,
    			"is_added_new" => true
    			]
    			]
    			,
    			"slot_start" => $this->slotStart,
    			"slot_end" => $this->slotEnd,
    			"share_options" => [
    			[
    			"private_share" => false,
    			"public_share" => true,
    			"teams_share" => false,
    			"team_ids" => [
    			[
    			"id" => "",
    			"is_team_selected" => false
    			]
    			]
    			]
    			]
    			]
    	);
    	$I->sendGET('students/activity?student-id='.$this->validStudent.'&category=all&is-interaction=false');
    	$data = json_decode($I->grabResponse());
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseIsJson();
    }
}
