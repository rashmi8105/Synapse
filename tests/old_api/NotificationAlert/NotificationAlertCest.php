<?php
require_once 'tests/api/SynapseTestHelper.php';
use Codeception\Scenario;

class NotificationAlertCest extends SynapseTestHelper {
	
	private $token;
	private $organization = 1;	
	private $person = 1;	
	private $proxyPersonId = 2;
	private $managedPersonId = 1;	
	private $personId = 1;
	private $organizationId = 1;
	private $officeHoursId = 2;
	private $isFreeStanding = false;
	private $invalidAlertId = -1;
	private $alertId = 1;
	private $invalidStudentId = - 1;
	private $slotStart = "";
	private $slotEnd = "";
	
	public function _before(ApiTester $I) {
		$this->token = $this->authenticate ( $I );
		$currentDate = new \DateTime("now");
		$this->slotStart = date("Y-m-d H:i:s", strtotime('+1 hours'));
		$this->slotEnd = date("Y-m-d H:i:s", strtotime('+2 hours'));
	}

	public function testGetNotifications(ApiTester $I, $scenario)
	{
		$I->wantTo('Get the list of notifications for the user');
        $I->haveHttpHeader('Content-Type', 'application/json');        
        $I->amBearerAuthenticated($this->token);        
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
			"attendees" =>  [
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

		$I->sendGET('notification');		
        $I->seeResponseCodeIs(200);		
		$I->seeResponseIsJson();			
		$I->seeResponseContainsJson(array("alerts" =>array("activity_type" => 'Appointment')));
		$I->seeResponseContainsJson(array("alerts" =>array("alert_reason" => "Appointment_Created")));		
	}

	
	private function getNotification(ApiTester $I){
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
	        "attendees" =>  [
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
	    
	    $I->sendGET('notification');
	    return $I->grabResponse();
	}
	public function testDeleteViewStatus(ApiTester $I, Scenario $scenario)
	{
		$I->wantTo('Delete notification view status');
        $I->haveHttpHeader('Content-Type', 'application/json');        
        $I->amBearerAuthenticated($this->token);
        $notifications = json_decode($this->getNotification($I));
		$I->sendDelete('notification?alert-id='.$notifications->data->alerts[0]->alert_id);		
		$I->seeResponseCodeIs(204);
	}
}
