<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\CoreBundle\Service\Impl\AppointmentsService;
use Synapse\CoreBundle\Service\Impl\AppointmentsProxyService;
require_once 'tests/functional/FunctionalBaseTest.php';

class AppointmentsProxyListTest extends FunctionalBaseTest
{
	/**
     *
     * @var Symfony\Component\DependencyInjection\Container
    */
    private $container;
	
	/**
     *
     * @var \Synapse\CoreBundle\Service\Impl\AppointmentsService
    */
    private $appointmentsService;

	/* @var \Synapse\CoreBundle\Service\Impl\AppointmentsProxyService */
	private $appointmentsProxyService;
	
	private $langId = 1;
	private $organizationId = 1;
    private $invalidOrganizationId = -2; 
	private $personId = 1; 
	private $invalidPersonId = -1;
	private $personProxyId = 1;
	private $invalidPersonProxyId = -1;
	private $type = "S";
	private $isFreeStandingTrue = true;
	private $isFreeStandingFalse = true;
	private $managedPersonId = 1;
	public function _before()
    {
    //$this->markTestSkipped("Errored");
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		//$this->appointmentsService = $this->container->get('appointments_service');
		$this->appointmentsService = $this->createServiceWithRbacMock('appointments_service');
		//$this->appointmentsProxyService = $this->container->get('appointmentsProxy_service');
		$this->appointmentsProxyService = $this->createServiceWithRbacMock('appointmentsProxy_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }
	public function createAppointmentsDtoWithAllData($startTime, $endTime)
	{
    	$appDto = new AppointmentsDto();
		$appDto->setPersonId($this->personId);
		$appDto->setPersonIdProxy($this->personProxyId);
		$appDto->setOrganizationId($this->organizationId);
		$appDto->setDetail("Reason details");
		$appDto->setDetailId(1);
		$appDto->setLocation("Stepojevac");
		$appDto->setDescription("dictumst etiam faucibus cursus elementum");
		$appDto->setOfficeHoursId(3);
		$appDto->setShareOptions($this->shareOptions());
		$appDto->setIsFreeStanding($this->isFreeStandingFalse);
		$appDto->setType($this->type);
		$appDto->setAttendees($this->createAttendees("create"));	
		$appDto->setSlotStart(new \DateTime($startTime));
		$appDto->setSlotEnd(new \DateTime($endTime));
    	return $appDto;
    }
	
	public function shareOptions()
	{
		$return = [];
		$shareOptionsDto = new ShareOptionsDto();
		$shareOptionsDto->setPublicShare(1);
		$return = array($shareOptionsDto);
		return $return;
	}
	
	public function createAttendees($action = "create")
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
		$attDto->setStudentId(2);
		if($action == "create")
		{
			$attDto->setIsSelected(true);
			$attDto->setIsAddedNew(true);
		}		
		$return = $attDto;		
		return $return;		
	}
	
	public function testListCurrentAppointmentsforProxy()
  {		
	    $this->initializeRbac();
	    $appointmentsDto = $this->createAppointmentsDtoWithAllData("+ 3 hour", "+ 4 hour");
		$appointments = $this->appointmentsService->create($appointmentsDto);			
		$result = $this->appointmentsProxyService->listProxyAppointments($this->personProxyId,'current',$this->managedPersonId);							
		
		$this->assertInternalType('array', $result);   				
		$this->assertArrayHasKey('organization_id', $result);
		$this->assertArrayHasKey('person_id_proxy', $result);			
		$fromDate = date("Y-m-d");
		$toDate = $this->getNext(date("Y-m-d"), $key='sunday');		
		$this->assertEquals($this->organizationId, $result['organization_id']);	
		$this->assertEquals($this->managedPersonId, $result['calendar_time_slots'][0]['managed_person_id']);	
		$this->assertEquals($this->personProxyId, $result['person_id_proxy']);
		$this->assertEquals($appointmentsDto->getDetail(), $result['calendar_time_slots'][0]['reason']);		
		$this->assertEquals($appointmentsDto->getType(), $result['calendar_time_slots'][0]['slot_type']);				
		$this->assertGreaterThanOrEqual($fromDate,$result['calendar_time_slots'][0]['slot_start']->format('Y-m-d')); 
		$this->assertLessThanOrEqual($toDate, $result['calendar_time_slots'][0]['slot_end']->format('Y-m-d'));			
		$this->assertArrayHasKey('appointment_id', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('location', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('reason', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('slot_type', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('office_hours_id', $result['calendar_time_slots'][0]);               	        
    }
	
	private function getNext($currentDate, $key='next Monday')
	{
		$parts = explode("-", $currentDate);
		$nextDate = date("Y-m-d",strtotime($key,mktime(0,0,0,$parts[1],$parts[2],$parts[0])));
		return $nextDate;
	}	
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    */
	
	public function testListCurrentAppointmentsforProxyByInvalidPersonId()
	{		
		$appointmentsDto = $this->createAppointmentsDtoWithAllData("+ 3 hour", "+ 4 hour");
		$appointments = $this->appointmentsService->create($appointmentsDto);
		$result = $this->appointmentsProxyService->listProxyAppointments($this->invalidPersonProxyId,'current',$this->managedPersonId);		
		$this->assertInternalType('array', $result,"Person Not Found");
	}
	public function testListNextweekAppointmentsforProxy()
	{		
		$fromDate = $this->getNext(date("Y-m-d"), $key='next monday');
		$toDate = $this->getNext($fromDate, $key='next sunday');
		$endTime = date('Y-m-d',strtotime("+1 days", strtotime($fromDate)));		
		$appointmentsDto = $this->createAppointmentsDtoWithAllData($fromDate, $endTime);		
		$appointments = $this->appointmentsService->create($appointmentsDto);		
		$result = $this->appointmentsProxyService->listProxyAppointments($this->personProxyId,'next',$this->managedPersonId);						
		$this->assertInternalType('array', $result);		
		$this->assertArrayHasKey('organization_id', $result);
		$this->assertArrayHasKey('person_id_proxy', $result);				
		$this->assertEquals($this->organizationId, $result['organization_id']);	
		$this->assertEquals($this->managedPersonId, $result['calendar_time_slots'][0]['managed_person_id']);	
		$this->assertEquals($this->personProxyId, $result['person_id_proxy']);			
		$this->assertGreaterThanOrEqual(strtotime($result['calendar_time_slots'][0]['slot_start']->format('Y-m-d')),strtotime($fromDate),'',5); 
		$this->assertLessThanOrEqual($toDate, $result['calendar_time_slots'][0]['slot_end']->format('Y-m-d'));			
		$this->assertArrayHasKey('appointment_id', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('location', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('reason', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('slot_type', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('office_hours_id', $result['calendar_time_slots'][0]);
		$this->assertEquals($appointmentsDto->getDetail(), $result['calendar_time_slots'][0]['reason']);		
		$this->assertEquals($appointmentsDto->getType(), $result['calendar_time_slots'][0]['slot_type']);		
				
	}
	
	public function testListNextTwoweekAppointmentsforProxy()
	{		
		$fromDate = $this->getNext(date("Y-m-d"), $key='monday');
		$toDate = $this->getNext($fromDate, $key='second sunday');	
		$endTime = date('Y-m-d',strtotime("+1 days", strtotime($fromDate)));		
		$appointmentsDto = $this->createAppointmentsDtoWithAllData($fromDate, $endTime);
		$appointments = $this->appointmentsService->create($appointmentsDto);
		$result = $this->appointmentsProxyService->listProxyAppointments($this->personProxyId,'next-two',$this->managedPersonId);
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('organization_id', $result);
		$this->assertArrayHasKey('person_id_proxy', $result);			
		$this->assertEquals($this->organizationId, $result['organization_id']);	
		$this->assertEquals($this->managedPersonId, $result['calendar_time_slots'][0]['managed_person_id']);	
		$this->assertEquals($this->personProxyId, $result['person_id_proxy']);			
		$this->assertGreaterThanOrEqual($result['calendar_time_slots'][0]['slot_start']->format('Y-m-d'),$fromDate); 
		$this->assertLessThanOrEqual($toDate, $result['calendar_time_slots'][0]['slot_end']->format('Y-m-d'));		
		$this->assertArrayHasKey('appointment_id', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('location', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('reason', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('slot_type', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('office_hours_id', $result['calendar_time_slots'][0]); 
		$this->assertEquals($appointmentsDto->getDetail(), $result['calendar_time_slots'][0]['reason']);		
		$this->assertEquals($appointmentsDto->getType(), $result['calendar_time_slots'][0]['slot_type']);		
				
	}
	
	public function testListNextMonthAppointmentsforProxy()
	{		
		$fromDate = $this->getNext(date("Y-m-d"), $key='first day of next month');
		$toDate = $this->getNext($fromDate, $key='last day of next month');
		$endTime = date('Y-m-d',strtotime("+1 days", strtotime($fromDate)));		
		$appointmentsDto = $this->createAppointmentsDtoWithAllData($fromDate,$endTime );
		$appointments = $this->appointmentsService->create($appointmentsDto);
		$result = $this->appointmentsProxyService->listProxyAppointments($this->personProxyId,'next-month',$this->managedPersonId);						
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('organization_id', $result);
		$this->assertArrayHasKey('person_id_proxy', $result);				
		$this->assertEquals($this->organizationId, $result['organization_id']);	
		$this->assertEquals($this->managedPersonId, $result['calendar_time_slots'][0]['managed_person_id']);	
		$this->assertEquals($this->personProxyId, $result['person_id_proxy']);			
		$this->assertGreaterThanOrEqual($result['calendar_time_slots'][0]['slot_start']->format('Y-m-d'),$fromDate); 
		$this->assertLessThanOrEqual($toDate, $result['calendar_time_slots'][0]['slot_end']->format('Y-m-d'));		
		$this->assertArrayHasKey('appointment_id', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('location', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('reason', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('slot_type', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('office_hours_id', $result['calendar_time_slots'][0]);
		$this->assertEquals($appointmentsDto->getDetail(), $result['calendar_time_slots'][0]['reason']);		
		$this->assertEquals($appointmentsDto->getType(), $result['calendar_time_slots'][0]['slot_type']);		
				
	}
	
	public function testListPastAppointmentsforProxy()
	{		
		$fromDate = date("Y-m-d");
		$toDate = date("Y-m-d");
		$fromDate = date('Y-m-d',strtotime("-1 days", strtotime($fromDate)));		
		$appointmentsDto = $this->createAppointmentsDtoWithAllData($fromDate,$toDate);		
		$appointments = $this->appointmentsService->create($appointmentsDto);
		$result = $this->appointmentsProxyService->listProxyAppointments($this->personProxyId,'past',$this->managedPersonId);				
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('organization_id', $result);
		$this->assertArrayHasKey('person_id_proxy', $result);		
		$this->assertEquals($this->organizationId, $result['organization_id']);	
		$this->assertEquals($this->managedPersonId, $result['calendar_time_slots'][0]['managed_person_id']);	
		$this->assertEquals($this->personProxyId, $result['person_id_proxy']);			
		$this->assertLessThanOrEqual($fromDate,$result['calendar_time_slots'][0]['slot_start']->format('Y-m-d')); 
		$this->assertLessThanOrEqual($toDate, $result['calendar_time_slots'][0]['slot_end']->format('Y-m-d'));		
		$this->assertArrayHasKey('appointment_id', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('location', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('reason', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('slot_type', $result['calendar_time_slots'][0]);
        $this->assertArrayHasKey('office_hours_id', $result['calendar_time_slots'][0]);  
		$this->assertEquals($appointmentsDto->getDetail(), $result['calendar_time_slots'][0]['reason']);		
		$this->assertEquals($appointmentsDto->getType(), $result['calendar_time_slots'][0]['slot_type']);				
	}	

	/**
	* @expectedException Synapse\RestBundle\Exception\ValidationException
	*/
	public function testListPastAppointmentsforInvalidProxy()
	{
		$fromDate = date("Y-m-d");
		$toDate = date("Y-m-d");
		$fromDate = date('Y-m-d',strtotime("-1 days", strtotime($fromDate)));		
		$appointmentsDto = $this->createAppointmentsDtoWithAllData($fromDate,$toDate);		
		$appointments = $this->appointmentsService->create($appointmentsDto);
		$result = $this->appointmentsProxyService->listProxyAppointments($this->invalidPersonProxyId,'past',$this->managedPersonId);
		$this->assertInternalType('array', $appointments, "Persons Not Found.");
		$this->assertSame('{"errors": ["Persons Not Found."],
			"data": [],
			"sideLoaded": []
		}',$appointments);	
	}
	
	public function testListCurrentAppointmentsforProxyAsDefault()
	{		
		$appointmentsDto = $this->createAppointmentsDtoWithAllData("+ 3 hour", "+ 4 hour");
		$appointments = $this->appointmentsService->create($appointmentsDto);			
		$result = $this->appointmentsProxyService->listProxyAppointments($this->personProxyId,'',$this->managedPersonId);							

		$this->assertInternalType('array', $result);   				
		$this->assertArrayHasKey('organization_id', $result);
		$this->assertArrayHasKey('person_id_proxy', $result);			
		$fromDate = date("Y-m-d");
		$toDate = $this->getNext(date("Y-m-d"), $key='sunday');		
		$this->assertEquals($this->organizationId, $result['organization_id']);	
		$this->assertEquals($this->managedPersonId, $result['calendar_time_slots'][0]['managed_person_id']);	
		$this->assertEquals($this->personProxyId, $result['person_id_proxy']);
		$this->assertEquals($appointmentsDto->getDetail(), $result['calendar_time_slots'][0]['reason']);		
		$this->assertEquals($appointmentsDto->getType(), $result['calendar_time_slots'][0]['slot_type']);				
		$this->assertGreaterThanOrEqual($fromDate,$result['calendar_time_slots'][0]['slot_start']->format('Y-m-d')); 
		$this->assertLessThanOrEqual($toDate, $result['calendar_time_slots'][0]['slot_end']->format('Y-m-d'));			
		$this->assertArrayHasKey('appointment_id', $result['calendar_time_slots'][0]);
		$this->assertArrayHasKey('location', $result['calendar_time_slots'][0]);
		$this->assertArrayHasKey('reason', $result['calendar_time_slots'][0]);
		$this->assertArrayHasKey('slot_type', $result['calendar_time_slots'][0]);
		$this->assertArrayHasKey('office_hours_id', $result['calendar_time_slots'][0]);               	        
	}
}
