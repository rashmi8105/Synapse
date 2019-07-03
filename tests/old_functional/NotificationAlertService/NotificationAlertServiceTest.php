<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;

class NotificationAlertServiceTest extends \Codeception\TestCase\Test
{
	private $personId = 1;
    private $personStudentId = 1;
    private $invalidStudentId = -1;
    private $personProxyId = 1;
    private $organizationId = 1;
    private $isFreeStanding = false;
    private $type = "S";    
	private $invalidPersonId = -1;
    private $invalidAlertId = -1;
	private $alertId = 1;
	public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->alertNotificationsService = $this->container->get('alertNotifications_service');	
		$this->appointmentsService = $this->container->get('appointments_service');		
    }
	protected function initializeRbac()
	{
	    // Bootstrap Rbac Authorization.
	    /** @var Manager $rbacMan */
	    $rbacMan = $this->container->get('tinyrbac.manager');
	    $rbacMan->initializeForUser($this->personId);
	}
    
	private function createAppointmentsDto(){
    	$appDto = new AppointmentsDto();
		$appDto->setPersonId($this->personId);
		$appDto->setPersonIdProxy($this->personProxyId);
		$appDto->setOrganizationId($this->organizationId);
		$appDto->setActivityLogId(null);
		$appDto->setDetail("Reason details");
		$appDto->setDetailId(1);
		$appDto->setLocation("Stepojevac");
		$appDto->setDescription("dictumst etiam faucibus cursus elementum");
		$appDto->setOfficeHoursId(3);
		$appDto->setIsFreeStanding($this->isFreeStanding);
		$appDto->setType($this->type);
		$appDto->setAttendees($this->createAttendees("create"));
		$appDto->setSlotStart(new \DateTime("+ 3 hour"));
		$appDto->setSlotEnd(new \DateTime("+ 4 hour"));
		$appDto->setShareOptions($this->shareOptions());
    	return $appDto;
    }
	
	private function createAttendees($action = "create")
    {
    	$return = [];
    	$attDto = new AttendeesDto();
    	$attDto->setStudentId(1);
    	if($action == "create")
    	{
    		$attDto->setIsSelected(true);
    		$attDto->setIsAddedNew(true);
    	}
    	$attDto = new AttendeesDto();
    	$attDto->setStudentId($this->personStudentId);
    	if($action == "create")
    	{
    		$attDto->setIsSelected(true);
    		$attDto->setIsAddedNew(true);
    	}
    	$return = $attDto;    
    	return $return;
    }
	
    private function shareOptions(){
    	$return = array();
    	$sharaOption = new ShareOptionsDto();
    	$sharaOption->setPrivateShare(true);
    	$sharaOption->setPublicShare(true);
    	$sharaOption->setTeamsShare(true);
    	$sharaOption->setTeamIds($this->teams());
    	return array($sharaOption);
    }
    
    
    private function teams(){
    	$return = array();
    	$teams = array();
    	$team = new TeamIdsDto();
    
    	for($i=1; $i<=3; $i++){
    		$team->setId($i);
    		$team->setIsTeamSelected(true);
    		$teams[] = $team;
    	}
    
    	return $teams;
    }
    
	public function testListNotifications()
    {		
        //$this->markTestSkipped("Errored");
        $this->initializeRbac();
        $appointmentDto = $this->createAppointmentsDto();
        $appointment = $this->appointmentsService->create($appointmentDto); 
		$notificationAlert = $this->alertNotificationsService->listNotifications($this->personId);        
        $this->assertNotEmpty($notificationAlert);		
		$this->assertEquals($this->personId, $notificationAlert->getPersonId());				
        $this->assertObjectHasAttribute("students", $notificationAlert);      
		$this->assertObjectHasAttribute("notViewedCount", $notificationAlert);
		$this->assertObjectHasAttribute("alertReason", $notificationAlert);
		$this->assertObjectHasAttribute("reason", $notificationAlert);
    }
	
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testListNotificationsforInvalidLoggedinID()
    {
		$notificationAlert = $this->alertNotificationsService->listNotifications($this->invalidPersonId);
        $this->assertSame('{"errors": ["No Alert Found"],
			"data": [],
			"sideLoaded": []
			}',$notificationAlert);		    
    }
	
		
	public function testDeleteNotificationViewStatus()
	{
		$alertNotification = $this->alertNotificationsService->deleteNotificationViewStatus($this->alertId);
		$this->assertNotEmpty($alertNotification);
		$this->assertEquals($this->alertId, $alertNotification);
	}
}
