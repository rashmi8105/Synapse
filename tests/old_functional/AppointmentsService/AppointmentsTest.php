<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\CoreBundle\Service\Impl\AppointmentsService;
require_once 'tests/functional/FunctionalBaseTest.php';

class AppointmentsTest extends FunctionalBaseTest
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
	
	private $langId = 1;
	private $organizationId = 1;
    private $invalidOrganizationId = -2; 
	private $personId = 1; 
	private $invalidPersonId = -1;
	private $personProxyId = 3;
	private $invalidPersonProxyId = -1;
	private $type = "S";
	private $isFreeStandingTrue = true;
	private $isFreeStandingFalse = false;
	private $slotStart = "2014-12-27 19:00:00";
	private $slotEnd = "2014-12-27 19:30:00";
	private $pastAppoinmentId = 43;
	private $invalidAppoinmentId = -1;
	private $appointmentId = 1;
	private $filter="today";
	private $invalidFilter="invalid";
	private $timezone="Pacific";
	
	
	public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        //$this->appointmentsService = $this->container->get('appointments_service');
        $this->appointmentsService = $this->createServiceWithRbacMock('appointments_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }
	public function testCreateAppointment()
	{
	    $this->initializeRbac();
	    $appointmentsDto = $this->createAppointmentsDtoWithAllData();
		$attDto = $this->createAttendees($action = "create");
      
        $appointments = $this->appointmentsService->create($appointmentsDto);
		if($appointments){
		$this->assertEquals('Stepojevac', $appointments->getLocation());
		$this->assertEquals('Reason details', $appointments->getDetail());
        $this->assertFalse($appointments->getIsFreeStanding());
		$this->assertEquals($this->personId, $appointments->getPersonId());
		$this->assertInstanceOf('Synapse\RestBundle\Entity\AttendeesDto', $appointments->getAttendees());
		}
		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    */
	public function testCreateAppointmentWithInvalidPerson()
	{
		$appointmentsDto = $this->createAppointmentsDtoWithInvalidPerson();
     	$appointments = $this->appointmentsService->create($appointmentsDto, '', true);
		$this->assertInternalType('array', $appointments, "Persons Not Found.");
		$this->assertSame('{"errors": ["Persons Not Found."],
			"data": [],
			"sideLoaded": []
			}',$appointments);	
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    */
	public function testCreateAppointmentWithInvalidSlotEnd()
	{
		$appointmentsDto = $this->createAppointmentsDtoWithInvalidSlotEnd();
     	$appointments = $this->appointmentsService->create($appointmentsDto, '', true);
		$this->assertInternalType('object', $appointments,"Slot start date cannot be grater than slot end date");
		$this->assertSame('{"errors": ["Slot start date cannot be grater than slot end date"],
			"data": [],
			"sideLoaded": []
			}',$appointments);
	}
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    */
	public function testCancelAppointmentWithInvalidAppointmentId()
	{
		$appointments = $this->appointmentsService->cancelAppointment($this->organizationId,$this->invalidAppoinmentId, true);
        $this->assertInternalType('object', $appointments,"Appointment Not Found.");
		$this->assertSame('{"errors": ["Appointment Not Found."],
			"data": [],
			"sideLoaded": []
			}',$appointments);
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    */	
	public function testEditPastAppointment()
	{
		$appointmentsDto = $this->editAppointmentsDtoWithPast();
		$appointments = $this->appointmentsService->editAppointment($appointmentsDto, true);
        $this->assertInternalType('object', $appointments,"Past appointments cannot be edited");
		$this->assertSame('{"errors": ["Past appointments cannot be edited"],
			"data": [],
			"sideLoaded": []
			}',$appointments);
					
		
	}
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    */	
	public function testEditAppointmentInvalidAppointmentId()
	{
		$appointmentsDto = $this->editAppointmentsDtoWithInvalidAppointmentId();
		$appointments = $this->appointmentsService->editAppointment($appointmentsDto, true);
        $this->assertInternalType('object', $appointments,"Appointment Not Found.");
		$this->assertSame('{"errors": ["Appointment Not Found."],
			"data": [],
			"sideLoaded": []
			}',$appointments);		
	}	
	
	public function createAppointmentsDtoWithAllData()
	{
		$appDto = $this->createAppointmentsDto();
		return $appDto;
	}
	public function createAppointmentsDtoWithInvalidSlotEnd()
	{
		$appDto = $this->createAppointmentsDto();
		$appDto->setSlotEnd(new \DateTime("+ 1 hour"));
		return $appDto;
	}
	public function createAppointmentsDtoWithInvalidPerson()
	{
		$appDto = $this->createAppointmentsDto();
		$appDto->setPersonId(mt_rand());
		return $appDto;
	}
	public function editAppointmentsDtoWithPast()
	{
		$appDto = $this->createAppointmentsDto();
		$appDto->setAppointmentId($this->pastAppoinmentId);
		return $appDto;
	}
	public function editAppointmentsDtoWithInvalidAppointmentId()
	{
		$appDto = $this->createAppointmentsDto();
		$appDto->setAppointmentId($this->invalidAppoinmentId);
		return $appDto;
	}	
	
	public function createAppointmentsDto(){
    	$appDto = new AppointmentsDto();
		$appDto->setPersonId($this->personId);
		$appDto->setPersonIdProxy($this->personProxyId);
		$appDto->setOrganizationId($this->organizationId);
		$appDto->setDetail("Reason details");
		$appDto->setDetailId(1);
		$appDto->setLocation("Stepojevac");
		$appDto->setDescription("dictumst etiam faucibus cursus elementum");
		$appDto->setOfficeHoursId(3);
		$appDto->setIsFreeStanding($this->isFreeStandingFalse);
		$appDto->setType($this->type);
		$appDto->setAttendees($this->createAttendees("create"));
		$appDto->setShareOptions($this->shareOptions());
		$appDto->setSlotStart(new \DateTime("+ 3 hour"));
		$appDto->setSlotEnd(new \DateTime("+ 4 hour"));
		
    	return $appDto;
    }

	public function createAttendees($action = "create")
	{
		$return = [];
		$attDto = new AttendeesDto();
		$attDto->setStudentId(8);
		if($action == "create")
		{
			$attDto->setIsSelected(true);
			$attDto->setIsAddedNew(true);
		}
		$return = $attDto;
		
		return $return;
		
		
	}
	
	public function shareOptions()
	{
		$return = [];
		$shareOptionsDto = new ShareOptionsDto();
		$shareOptionsDto->setPublicShare(1);
		$return = array($shareOptionsDto);
		return $return;
	}

	public function testViewAppointment()
	{
	    $this->initializeRbac();
		$appointmentsDto = $this->createAppointmentsDtoWithAllData();   
        $CreateAppointment = $this->appointmentsService->create($appointmentsDto);	
	
		$appointments = $this->appointmentsService->viewAppointment($this->organizationId,$this->personId, $CreateAppointment->getAppointmentId(), NULL, true);		
		$this->assertEquals($appointments['appointment_id'] ,1);		
		$this->assertEquals($CreateAppointment->getOrganizationId() ,$this->organizationId);
		$this->assertInternalType('array', $appointments);
		$this->assertArrayHasKey('detail', $appointments);
		$this->assertArrayHasKey('detail_id', $appointments);
		$this->assertArrayHasKey('location', $appointments);
		$this->assertArrayHasKey('description', $appointments);
		$this->assertArrayHasKey('office_hours_id', $appointments);
		$this->assertArrayHasKey('is_free_standing', $appointments);
		$this->assertArrayHasKey('type', $appointments);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\AttendeesDto', $CreateAppointment->getAttendees());	
		$this->assertArrayHasKey('slot_start', $appointments);
		$this->assertArrayHasKey('slot_end', $appointments);		
        
	}
	
	public function testViewTodayAppointment()
	{
		$appointmentsDto = $this->createAppointmentsDtoWithAllData();
		$attDto = $this->createAttendees($action = "create");
		$appointmentsDto = $this->appointmentsService->create($appointmentsDto, '', true);
		$appointments = $this->appointmentsService->viewTodayAppointment($this->filter, $this->organizationId, $this->personId, $this->timezone, true);
		$this->assertEquals($appointmentsDto->getPersonId(), $appointments['person_id']);
		$this->assertInternalType('array', $appointments);
		$this->assertArrayHasKey("todays_total_appointments", $appointments);
		$this->assertArrayHasKey("appointments", $appointments);
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testViewTodayAppointmentWithInvalidFilter()
	{
		$this->appointmentsService->viewTodayAppointment($this->invalidFilter, $this->organizationId, $this->personId, $this->timezone, true);
	}
}
