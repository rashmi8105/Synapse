<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;

use Synapse\RestBundle\Entity\OfficeHoursSeriesDto;
use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\AttendeesDto;
require_once 'tests/functional/FunctionalBaseTest.php';

class GetAppointmentsByUserTest extends FunctionalBaseTest
{

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    private $appointmentsService;

    private $officeHoursService;

    private $personId = 1;

    private $organizationId = 1;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        //$this->appointmentsService = $container->get('appointments_service');
        $this->appointmentsService = $this->createServiceWithRbacMock('appointments_service');
        //$this->officeHoursService = $container->get('officehours_service');
        $this->officeHoursService = $this->createServiceWithRbacMock('officehours_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }
    public function createOfficeHoursDto()
    {
        $officeHoursDto = new OfficeHoursDto();
        $officeHoursDto->setPersonId($this->personId);
        $officeHoursDto->setPersonIdProxy(0);
        $officeHoursDto->setOrganizationId($this->organizationId);
        $officeHoursDto->setSlotType("I");
        $start = new \DateTime("now");
        $start->modify("+1 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+7 Days");
        $end->setTime(15, 0, 0);
        $officeHoursDto->setSlotStart($start->format('Y-m-d\TH:i:sO'));
        $officeHoursDto->setSlotEnd($end->format('Y-m-d\TH:i:sO'));
        $officeHoursDto->setLocation("Porto Real");
        
        return $officeHoursDto;
    }

    public function createAppointmentsDto()
    {
        $appDto = new AppointmentsDto();
        $appDto->setPersonId($this->personId);
        
        $appDto->setOrganizationId($this->organizationId);
        $appDto->setDetail("Reason details");
        $appDto->setDetailId(1);
        $appDto->setLocation("Stepojevac");
        $appDto->setDescription("dictumst etiam faucibus cursus elementum");
        $appDto->setOfficeHoursId(3);
        $appDto->setIsFreeStanding(false);
		$appDto->setShareOptions($this->shareOptions());
        $appDto->setType('I');
        $appDto->setAttendees($this->createAttendees("create"));
        
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
        if ($action == "create") {
            $attDto->setIsSelected(true);
            $attDto->setIsAddedNew(true);
        }
        $attDto = new AttendeesDto();
        $attDto->setStudentId(2);
        if ($action == "create") {
            $attDto->setIsSelected(true);
            $attDto->setIsAddedNew(true);
        }
        $return = $attDto;
        
        return $return;
    }

    public function createSeriesInfoDaily()
    {
        $seriesInfo = new OfficeHoursSeriesDto();
        $seriesInfo->setMeetingLength("60");
        $seriesInfo->setRepeatPattern("D");
        $seriesInfo->setRepeatEvery(1);
        
        $seriesInfo->setRepeatRange("D");
        
        return $seriesInfo;
    }

    public function testGetAppointmentsByUserByNextWeek()
    {
        $this->initializeRbac();
        $officeHoursDto = $this->createOfficeHoursDto();
        $seriesInfo = $this->createSeriesInfoDaily();
        $officeHoursDto->setSeriesInfo($seriesInfo);
        $officeHoursDto->setSlotType("S");
        
        $start = new \DateTime("now");
        $start->modify("+7 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+7 Days");
        $end->setTime(15, 0, 0);
        $officeHoursDto->setSlotStart($start);
        $officeHoursDto->setSlotEnd($end);
        
        $officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, $this->personId, 'next', null);
        $officeHourSlots = $officeHourSlots->getCalendarTimeSlots();
        $start = $officeHoursDto->getSlotStart();        
        $end = clone $start;
        $end->setTime(15, 0, 0);
        
        foreach ($officeHourSlots as $officeHourSlot) {
		if($officeHourSlot->getOfficeHoursId() != null)
			$this->assertEquals(0, $officeHourSlot->getAppointmentId());			
			$this->assertEmpty($officeHourSlot->getReasonId());
			$this->assertEmpty($officeHourSlot->getReason());
			$this->assertEquals("S", $officeHourSlot->getSlotType());
			$this->assertGreaterThan(0, $officeHourSlot->getOfficeHoursId());
			$this->assertEmpty($officeHourSlot->getAttendees());			
			$start->add(new \DateInterval('P1D'));
			$end->add(new \DateInterval('P1D'));
        }
    }

    public function testGetAppointmentsByUserByNextWeekAppointment()
    {
        $this->initializeRbac();
        $officeHoursDto = $this->createOfficeHoursDto();
        $seriesInfo = $this->createSeriesInfoDaily();
        $officeHoursDto->setSeriesInfo($seriesInfo);
        $officeHoursDto->setSlotType("S");
        
        $start = new \DateTime("now");
        $start->modify("+7 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+7 Days");
        $end->setTime(15, 0, 0);
        $officeHoursDto->setSlotStart($start);
        $officeHoursDto->setSlotEnd($end);
        $officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, $this->personId, 'next', null);
        $appointmentsDto = $this->createAppointmentsDto();
        $appointmentsDto->setSlotStart($start);
        $nextTime = clone $start;
        $nextTime->setTime(15, 0, 0);
        $appointmentsDto->setSlotEnd($nextTime);
        $appointmentsDto->setOfficeHoursId($officeHourSlots->getCalendarTimeSlots()[0]
            ->getOfficeHoursId());
        $appointments = $this->appointmentsService->create($appointmentsDto);
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, $this->personId, 'next', null);
        $officeHourSlots = $officeHourSlots->getCalendarTimeSlots();
        
        $start = $officeHoursDto->getSlotStart();
        
        $end = clone $start;
        $end->setTime(15, 0, 0);
        $officeHourSlot = $officeHourSlots[0];
        $this->assertGreaterThan(0, $officeHourSlot->getAppointmentId());
        $this->assertEquals("Stepojevac", $officeHourSlot->getLocation());
        
        $this->assertEquals("S", $officeHourSlot->getSlotType());
        $this->assertGreaterThan(0, $officeHourSlot->getOfficeHoursId());
        $this->assertObjectHasAttribute('attendees',$officeHourSlot);
      
        $index = 0;
        foreach ($officeHourSlots as $officeHourSlot) {
            if ($index != 0) {
                $this->assertEquals(0, $officeHourSlot->getAppointmentId());
                $this->assertEmpty($officeHourSlot->getReasonId());
                $this->assertEmpty($officeHourSlot->getReason());
                $this->assertEmpty($officeHourSlot->getAttendees());                
            }
            $index ++;
            
            $this->assertEquals("S", $officeHourSlot->getSlotType());
            $this->assertGreaterThan(0, $officeHourSlot->getOfficeHoursId());           
       
            $start->add(new \DateInterval('P1D'));
            $end->add(new \DateInterval('P1D'));
        }
    }

    public function testGetAppointmentsByUserByCurrentWeek()
    {
        $this->initializeRbac();
        $officeHoursDto = $this->createOfficeHoursDto();
        $seriesInfo = $this->createSeriesInfoDaily();
        $officeHoursDto->setSeriesInfo($seriesInfo);
        $officeHoursDto->setSlotType("S");
        
        $start = new \DateTime("now");
        $start->modify("+1 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+1 Days");
        $end->setTime(15, 0, 0);
        $officeHoursDto->setSlotStart($start);
        $officeHoursDto->setSlotEnd($end);
        
        $officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
        
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, $this->personId, 'current', null);
        $officeHourSlots = $officeHourSlots->getCalendarTimeSlots();
        
        $start = $officeHoursDto->getSlotStart();
        
        $end = clone $start;
        $end->setTime(15, 0, 0);
        
        foreach ($officeHourSlots as $officeHourSlot) {
                    
            $this->assertGreaterThanOrEqual(0, $officeHourSlot->getOfficeHoursId());
          
            $start->add(new \DateInterval('P1D'));
            $end->add(new \DateInterval('P1D'));
        }
    }

    public function testGetAppointmentsByUserByNextTwoWeek()
    {
        $officeHoursDto = $this->createOfficeHoursDto();
        $seriesInfo = $this->createSeriesInfoDaily();
        $officeHoursDto->setSeriesInfo($seriesInfo);
        $officeHoursDto->setSlotType("S");
        
        $start = new \DateTime("now");
        $start->modify("+14 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+14 Days");
        $end->setTime(15, 0, 0);
        $officeHoursDto->setSlotStart($start);
        $officeHoursDto->setSlotEnd($end);
        
        $officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
        
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, $this->personId, 'next-two', null);
        $officeHourSlots = $officeHourSlots->getCalendarTimeSlots();
        
        $start = $officeHoursDto->getSlotStart();
        
        $end = clone $start;
        $end->setTime(15, 0, 0);
        
        foreach ($officeHourSlots as $officeHourSlot) {
            $this->assertEquals(0, $officeHourSlot->getAppointmentId());            
            $this->assertEmpty($officeHourSlot->getReasonId());
            $this->assertEmpty($officeHourSlot->getReason());            
            $this->assertGreaterThan(0, $officeHourSlot->getOfficeHoursId());
            $this->assertEmpty($officeHourSlot->getAttendees());            
            $start->add(new \DateInterval('P1D'));
            $end->add(new \DateInterval('P1D'));
        }
    }

    public function testGetAppointmentsByUserByNextMonth()
    {
        $officeHoursDto = $this->createOfficeHoursDto();
        $seriesInfo = $this->createSeriesInfoDaily();
        $officeHoursDto->setSeriesInfo($seriesInfo);
        $officeHoursDto->setSlotType("S");
        
        $start = new \DateTime("now");
        $start->modify("+30 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+30 Days");
        $end->setTime(15, 0, 0);
        $officeHoursDto->setSlotStart($start);
        $officeHoursDto->setSlotEnd($end);
        
        $officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
        
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, $this->personId, 'next-month', null);
        $officeHourSlots = $officeHourSlots->getCalendarTimeSlots();
        
        $start = $officeHoursDto->getSlotStart();
        
        $end = clone $start;
        $end->setTime(15, 0, 0);
        
        foreach ($officeHourSlots as $officeHourSlot) {
            $this->assertEquals(0, $officeHourSlot->getAppointmentId());
            $this->assertEquals("Porto Real", $officeHourSlot->getLocation());
            $this->assertEmpty($officeHourSlot->getReasonId());
            $this->assertEmpty($officeHourSlot->getReason());
            $this->assertEquals("S", $officeHourSlot->getSlotType());
            $this->assertGreaterThan(0, $officeHourSlot->getOfficeHoursId());
            $this->assertEmpty($officeHourSlot->getAttendees());       
            $start->add(new \DateInterval('P1D'));
            $end->add(new \DateInterval('P1D'));
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAppointmentsByUserByInvalidPerson()
    {
        $officeHoursDto = $this->createOfficeHoursDto();
        $seriesInfo = $this->createSeriesInfoDaily();
        $officeHoursDto->setSeriesInfo($seriesInfo);
        $officeHoursDto->setSlotType("S");
        
        $start = new \DateTime("now");
        $start->modify("+7 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+7 Days");
        $end->setTime(15, 0, 0);
        $officeHoursDto->setSlotStart($start);
        $officeHoursDto->setSlotEnd($end);
        
        $officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
        
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, - 1, 'next', null);
    }

    public function testGetAppointmentsByUserByNextWeekFreeStandingAppointment()
    {
        $start = new \DateTime("now");
        $start->modify("+7 Days");
        $start->setTime(14, 0, 0);
        $end = clone $start;
        $end->modify("+7 Days");
        $end->setTime(15, 0, 0);        
        
        $appointmentsDto = $this->createAppointmentsDto();
        $appointmentsDto->setSlotStart($start);
        $nextTime = clone $start;
        $nextTime->setTime(15, 0, 0);
        $appointmentsDto->setSlotEnd($nextTime);
        $appointmentsDto->setType('F');
        $appointmentsDto->setOfficeHoursId(NULL);
        $appointmentsDto->setIsFreeStanding(true);
        $appointments = $this->appointmentsService->create($appointmentsDto);        
        $officeHourSlots = $this->appointmentsService->getAppointmentsByUser($this->organizationId, $this->personId, 'next', null);
        
        $officeHourSlots = $officeHourSlots->getCalendarTimeSlots();
        
        $end = clone $start;
        $end->setTime(15, 0, 0);
        $officeHourSlot = $officeHourSlots[0];	
       
        foreach ($officeHourSlots as $officeHourSlot) {
			if(	$officeHourSlot->getAppointmentId() == $appointments->getAppointmentId())
			{		
				$this->assertGreaterThan(0, $officeHourSlot->getAppointmentId());
				$this->assertNotEmpty($officeHourSlot->getReasonId());
			    $this->assertNotEmpty($officeHourSlot->getReason());
				$this->assertEmpty($officeHourSlot->getAttendees());
				$this->assertEquals("Stepojevac", $officeHourSlot->getLocation());
				
				$this->assertEquals("F", $officeHourSlot->getSlotType());
				$this->assertEquals(0, $officeHourSlot->getOfficeHoursId());				
				
				$start->add(new \DateInterval('P1D'));
				$end->add(new \DateInterval('P1D'));
			}			
		}		
    }
}
