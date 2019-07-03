<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\RestBundle\Entity\OfficeHoursSeriesDto;
use \DateTime;
use JMS\Serializer\Tests\Fixtures\Publisher;

class OfficeHoursServiceTest extends \Codeception\TestCase\Test
{
	/**
	 *
	 * @var UnitTester
	 */
	protected $tester;
	
	/**
	 *
	 * @var Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 *
	 * @var \Synapse\CoreBundle\Service\Impl\OfficeHoursService
	 */
	private $officeHoursService;
	
	/**
	 *
	 * @var \Synapse\CoreBundle\Service\Impl\AppointmentsService
	 */
	private $appointmentsService;
	
	/**
	 * {@inheritDoc}
	 */
	private $personId = 1;
	
	private $organizationId = 1;
	
	private $invalidPersonId = -1;
	
	private $officeHoursId = 1;
	
	private $invalidOfficehoursId = -1;
	
	
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->officeHoursService = $this->container->get('officehours_service');
		$this->appointmentsService = $this->container->get('appointments_service');
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
		$officeHoursDto->setSlotStart(new \DateTime("+ 10 hour"));
		$officeHoursDto->setSlotEnd(new \DateTime("+ 11 hour"));
		$officeHoursDto->setLocation("Porto Real");
		$officeHoursDto->setAppointmentId(1);
		$officeHoursDto->setOfficeHoursId($this->officeHoursId);
		
		return $officeHoursDto;
	}
	
	
	public function createSeriesInfoNone()
	{
		$seriesInfo = new OfficeHoursSeriesDto();
		$seriesInfo->setMeetingLength("15");
		$seriesInfo->setRepeatDays(NULL);
		$seriesInfo->setRepeatPattern("N");
		$seriesInfo->setRepeatEvery(NULL);
		$seriesInfo->setRepeatOccurence(NULL);
		$seriesInfo->setRepeatRange(NULL);
		
		return $seriesInfo;
	}
	
	
	public function createSeriesInfoDaily()
	{
		$seriesInfo = new OfficeHoursSeriesDto();
		$seriesInfo->setMeetingLength("60");
		$seriesInfo->setRepeatPattern("D");
		$seriesInfo->setRepeatEvery(2);
		$seriesInfo->setRepeatOccurence(5);
		$seriesInfo->setRepeatRange("E");
	
		return $seriesInfo;
	}
	
	
	public function createSeriesInfoWeekly()
	{
		$seriesInfo = new OfficeHoursSeriesDto();
		$seriesInfo->setMeetingLength("60");
		$seriesInfo->setRepeatPattern("W");
		$seriesInfo->setRepeatEvery(2);
		$seriesInfo->setRepeatDays("0100000");
	
		return $seriesInfo;
	}
	
	
	public function createSeriesInfoMonthly()
	{
		$seriesInfo = new OfficeHoursSeriesDto();
		$seriesInfo->setMeetingLength("60");
		$seriesInfo->setRepeatPattern("M");
		$seriesInfo->setRepeatEvery(2);
		$seriesInfo->setRepeatDays("0100001");
		$seriesInfo->setRepeatOccurence(NULL);
		$seriesInfo->setRepeatRange(NULL);
		$seriesInfo->setRepeatMonthlyOn(1);
	
		return $seriesInfo;
	}
	
	
	public function testCreateOfficeHoursSeriesNone()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoNone();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertNotNull($officeHours->getOfficeHoursId());
		$this->assertEquals($this->personId, $officeHours->getPersonId());
		$this->assertEquals(0, $officeHours->getPersonIdProxy());
		$this->assertEquals($this->organizationId, $officeHours->getOrganizationId());
		$this->assertEquals('S', $officeHours->getSlotType());
		$this->assertEquals("Porto Real", $officeHours->getLocation());
		$this->assertEquals("15", $officeHours->getSeriesInfo()->getMeetingLength());
		$this->assertNull($officeHours->getSeriesInfo()->getRepeatDays());
		$this->assertEquals("N",$officeHours->getSeriesInfo()->getRepeatPattern());
		$this->assertNull($officeHours->getSeriesInfo()->getRepeatEvery());
		$this->assertNull($officeHours->getSeriesInfo()->getRepeatOccurence());
		$this->assertNull($officeHours->getSeriesInfo()->getRepeatRange());
	}
	
	
	public function testCreateOfficeHoursSeriesDaily()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertNotNull($officeHours->getOfficeHoursId());
		$this->assertEquals($this->personId, $officeHours->getPersonId());
		$this->assertEquals(0, $officeHours->getPersonIdProxy());
		$this->assertEquals($this->organizationId, $officeHours->getOrganizationId());
		$this->assertEquals('S', $officeHours->getSlotType());
		$this->assertEquals("Porto Real", $officeHours->getLocation());
		$this->assertEquals("60", $officeHours->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("D", $officeHours->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHours->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals(5, $officeHours->getSeriesInfo()->getRepeatOccurence());
		$this->assertEquals("E", $officeHours->getSeriesInfo()->getRepeatRange());	
	}
	
	
	public function testCreateOfficeHoursSeriesWeeklyNoRepeatOccurance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoWeekly();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertNotNull($officeHours->getOfficeHoursId());
		$this->assertEquals($officeHoursDto->getPersonId(), $officeHours->getPersonId());
		$this->assertEquals($officeHoursDto->getPersonIdProxy(), $officeHours->getPersonIdProxy());
		$this->assertEquals($officeHoursDto->getOrganizationId(), $officeHours->getOrganizationId());
		$this->assertEquals('S', $officeHours->getSlotType());
		$this->assertEquals("Porto Real", $officeHours->getLocation());
		$this->assertEquals("60", $officeHours->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("W", $officeHours->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHours->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100000", $officeHours->getSeriesInfo()->getRepeatDays());
	}
	
	
	public function testCreateOfficeHoursSeriesWeeklyRepeatOccurance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoWeekly();
		$seriesInfo->setRepeatRange("E");
		$seriesInfo->setRepeatOccurence(5);
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertNotNull($officeHours->getOfficeHoursId());
		$this->assertEquals($officeHoursDto->getPersonId(), $officeHours->getPersonId());
		$this->assertEquals($officeHoursDto->getPersonIdProxy(), $officeHours->getPersonIdProxy());
		$this->assertEquals($officeHoursDto->getOrganizationId(), $officeHours->getOrganizationId());
		$this->assertEquals('S', $officeHours->getSlotType());
		$this->assertEquals("Porto Real", $officeHours->getLocation());
		$this->assertEquals("60", $officeHours->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("W", $officeHours->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHours->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100000", $officeHours->getSeriesInfo()->getRepeatDays());
		$this->assertEquals("E", $officeHours->getSeriesInfo()->getRepeatRange());
	}
	
	
	public function testCreateOfficeHoursSeriesMonthlyNoRepeatOccurance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoMonthly();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHoursDto->setSlotEnd(new \DateTime("+ 3 months"));
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertNotNull($officeHours->getOfficeHoursId());
		$this->assertEquals($officeHoursDto->getPersonId(), $officeHours->getPersonId());
		$this->assertEquals($officeHoursDto->getPersonIdProxy(), $officeHours->getPersonIdProxy());
		$this->assertEquals($officeHoursDto->getOrganizationId(), $officeHours->getOrganizationId());
		$this->assertEquals('S', $officeHours->getSlotType());
		$this->assertEquals("Porto Real", $officeHours->getLocation());
		$this->assertEquals("60", $officeHours->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("M", $officeHours->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHours->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100001", $officeHours->getSeriesInfo()->getRepeatDays());
		$this->assertEquals(1, $officeHours->getSeriesInfo()->getRepeatMonthlyOn());
		$this->assertNull($officeHours->getSeriesInfo()->getRepeatRange());
	}
	
	
	public function testCreateOfficeHoursSeriesMonthlyRepeatOccurance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoMonthly();
		$seriesInfo->setRepeatRange("E");
		$seriesInfo->setRepeatOccurence(5);
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHoursDto->setSlotEnd(new \DateTime("+ 3 months"));
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertNotNull($officeHours->getOfficeHoursId());
		$this->assertEquals($officeHoursDto->getPersonId(), $officeHours->getPersonId());
		$this->assertEquals($officeHoursDto->getPersonIdProxy(), $officeHours->getPersonIdProxy());
		$this->assertEquals($officeHoursDto->getOrganizationId(), $officeHours->getOrganizationId());
		$this->assertEquals('S', $officeHours->getSlotType());
		$this->assertEquals("Porto Real", $officeHours->getLocation());
		$this->assertEquals("60", $officeHours->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("M", $officeHours->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHours->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100001", $officeHours->getSeriesInfo()->getRepeatDays());
		$this->assertEquals(1, $officeHours->getSeriesInfo()->getRepeatMonthlyOn());
		$this->assertEquals("E",$officeHours->getSeriesInfo()->getRepeatRange());
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateOfficeHoursSeriesInvalidPerson()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoNone();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHoursDto->setPersonId($this->invalidPersonId);
		
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateOfficeHoursSeriesInvalidSlotStartDate()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoNone();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$start = new \DateTime("now");
        $start->modify("-10 Days");
        $start->setTime(14, 0, 0);
		$officeHoursDto->setSlotStart($start);
		
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateOfficeHoursSeriesInvalidSlotEndDate()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoNone();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHoursDto->setSlotEnd(new \DateTime("+ 1 hour"));
	
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	}
	
	
	public function testEditOfficeHoursSeriesNone()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
		
		$seriesinfos = $this->createSeriesInfoNone();
		$officeHours->setSeriesInfo($seriesinfos);
		$officeHour = $this->officeHoursService->editOfficeHourSeries($officeHours);
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertEquals($officeHours->getSlotStart(), $officeHour->getSlotStart());
		$this->assertEquals($officeHours->getSlotEnd(), $officeHour->getSlotEnd());
		$this->assertEquals("15", $officeHour->getSeriesInfo()->getMeetingLength());
		$this->assertNull($officeHour->getSeriesInfo()->getRepeatDays());
		$this->assertEquals("N",$officeHour->getSeriesInfo()->getRepeatPattern());
		$this->assertNull($officeHour->getSeriesInfo()->getRepeatEvery());
		$this->assertNull($officeHour->getSeriesInfo()->getRepeatOccurence());
		$this->assertNull($officeHour->getSeriesInfo()->getRepeatRange());
	}
	
	
	public function testEditOfficeHoursSeriesDaily()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoNone();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
		
		$seriesinfos = $this->createSeriesInfoDaily();
		$officeHours->setSeriesInfo($seriesinfos);
		$officeHour = $this->officeHoursService->editOfficeHourSeries($officeHours);

		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertEquals($officeHours->getSlotStart(), $officeHour->getSlotStart());
		$this->assertEquals($officeHours->getSlotEnd(), $officeHour->getSlotEnd());
		$this->assertEquals("60", $officeHour->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("D", $officeHour->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHour->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals(5, $officeHour->getSeriesInfo()->getRepeatOccurence());
		$this->assertEquals("E", $officeHour->getSeriesInfo()->getRepeatRange());
	}
	
	
	public function testEditOfficeHoursSeriesWeeklyNoRepeatOccurrance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	
		$seriesinfos = $this->createSeriesInfoWeekly();
		$officeHours->setSeriesInfo($seriesinfos);
		$officeHour = $this->officeHoursService->editOfficeHourSeries($officeHours);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertEquals($officeHours->getSlotStart(), $officeHour->getSlotStart());
		$this->assertEquals($officeHours->getSlotEnd(), $officeHour->getSlotEnd());
		$this->assertEquals("60", $officeHour->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("W", $officeHour->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHour->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100000", $officeHour->getSeriesInfo()->getRepeatDays());
	}
	
	
	public function testEditOfficeHoursSeriesWeeklyRepeatOccurrance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	
		$seriesinfos = $this->createSeriesInfoWeekly();
		$seriesinfos->setRepeatRange("E");
		$seriesinfos->setRepeatOccurence(5);
		$officeHours->setSeriesInfo($seriesinfos);
		$officeHour = $this->officeHoursService->editOfficeHourSeries($officeHours);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertEquals($officeHours->getSlotStart(), $officeHour->getSlotStart());
		$this->assertEquals($officeHours->getSlotEnd(), $officeHour->getSlotEnd());
		$this->assertEquals("60", $officeHour->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("W", $officeHour->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHour->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100000", $officeHour->getSeriesInfo()->getRepeatDays());
		$this->assertEquals("E", $officeHour->getSeriesInfo()->getRepeatRange());
		$this->assertEquals(5, $officeHour->getSeriesInfo()->getRepeatOccurence());
	}
	
	
	public function testEditOfficeHoursSeriesMonthlyNoRepeatOccurance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	
		$seriesinfos = $this->createSeriesInfoMonthly();
		$officeHours->setSeriesInfo($seriesinfos);
		$officeHour = $this->officeHoursService->editOfficeHourSeries($officeHours);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHour);
		$this->assertEquals($officeHours->getSlotStart(), $officeHour->getSlotStart());
		$this->assertEquals($officeHours->getSlotEnd(), $officeHour->getSlotEnd());
		$this->assertEquals("60", $officeHour->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("M", $officeHour->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHour->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100001", $officeHour->getSeriesInfo()->getRepeatDays());
		$this->assertEquals(1, $officeHour->getSeriesInfo()->getRepeatMonthlyOn());
		$this->assertNull($officeHour->getSeriesInfo()->getRepeatOccurence());
		$this->assertNull($officeHour->getSeriesInfo()->getRepeatRange());
	}
	
	
	public function testEditOfficeHoursSeriesMonthlyRepeatOccurrance()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
	
		$seriesinfos = $this->createSeriesInfoMonthly();
		$seriesinfos->setRepeatRange("E");
		$seriesinfos->setRepeatOccurence(5);
		$officeHours->setSeriesInfo($seriesinfos);
		$officeHour = $this->officeHoursService->editOfficeHourSeries($officeHours);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHour);
		$this->assertEquals($officeHours->getSlotStart(), $officeHour->getSlotStart());
		$this->assertEquals($officeHours->getSlotEnd(), $officeHour->getSlotEnd());
		$this->assertEquals("60", $officeHour->getSeriesInfo()->getMeetingLength());
		$this->assertEquals("M", $officeHour->getSeriesInfo()->getRepeatPattern());
		$this->assertEquals(2, $officeHour->getSeriesInfo()->getRepeatEvery());
		$this->assertEquals("0100001", $officeHour->getSeriesInfo()->getRepeatDays());
		$this->assertEquals(1, $officeHour->getSeriesInfo()->getRepeatMonthlyOn());
		$this->assertEquals("E", $officeHour->getSeriesInfo()->getRepeatRange());
		$this->assertEquals(5, $officeHour->getSeriesInfo()->getRepeatOccurence());
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testEditOfficeHoursSeriesInvalid()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHoursDto->setOfficeHoursId($this->invalidOfficehoursId);
		
		$officeHour = $this->officeHoursService->editOfficeHourSeries($officeHoursDto);
	}
	
	
	public function testGetOfficeHourSeries()
	{
        //$this->markTestSkipped("Errored");
	    $this->initializeRbac();
	    $officeHoursDto = $this->createOfficeHoursDto();
		$seriesInfo = $this->createSeriesInfoDaily();
		$officeHoursDto->setSeriesInfo($seriesInfo);
		$officeHoursDto->setSlotType("S");
		$officeHours = $this->officeHoursService->createOfficeHourSeries($officeHoursDto);
		
		$result = $this->appointmentsService->getAppointmentsByUser($officeHours->getOrganizationId(),$this->personId,NULL,NULL);
		$id = $result->getCalendarTimeSlots()[0]->getOfficeHoursId();
		
		$officeHour = $this->officeHoursService->getOfficeHourSeries($id);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHour);
		$this->assertEquals($officeHours->getOfficeHoursId(), $officeHour->getOfficeHoursId());
		$this->assertNotNull($officeHour->getPersonId());
		$this->assertNotNull($officeHour->getOrganizationId());
		$this->assertNotNull($officeHour->getSlotStart());
		$this->assertNotNull($officeHour->getSlotEnd());
		$this->assertNull($officeHour->getIsCancelled());
		$this->assertNotNull($officeHour->getSeriesInfo());
		$this->assertNotNull($officeHour->getSeriesInfo()->getRepeatPattern());		 
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetOfficeHourSeriesInvalid()
	{
		$officeHour = $this->officeHoursService->getOfficeHourSeries($this->invalidOfficehoursId);
	}
	
	public function testGetOfficeHour()
	{
		$officeHoursDto = $this->createOfficeHoursDto();		
		$officeHoursDto->setSlotType("I");
		$officeHours = $this->officeHoursService->createOfficeHour($officeHoursDto);
		
		$officeHour = $this->officeHoursService->getOfficeHour($officeHours->getOfficeHoursId());
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHour);
		$this->assertEquals($officeHours->getOfficeHoursId(), $officeHour->getOfficeHoursId());
		$this->assertNotNull($officeHour->getPersonId());
		$this->assertNotNull($officeHour->getOrganizationId());
		$this->assertNotNull($officeHour->getSlotStart());
		$this->assertNotNull($officeHour->getSlotEnd());
		$this->assertNull($officeHour->getIsCancelled());		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetOfficeHourInvalid()
	{
		$officeHour = $this->officeHoursService->getOfficehour($this->invalidOfficehoursId);
	}
	
	
	public function createOfficeHour()
	{
		$officeHours = new officeHoursDto();
		$officeHours->setPersonId("1");
		$officeHours->setPersonIdProxy("2");
		$officeHours->setOrganizationId("1");
		$officeHours->setSlotType("I");
		$officeHoursDto->setSlotStart(new \DateTime("+ 3 hour"));
		$officeHoursDto->setSlotEnd(new \DateTime("+ 4 hour"));
		$officeHours->setLocation("My location");
		
		return $officeHours;
	}
	
	
		
	public function testCreateOfficeHoursInvalidSlotStartDate()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$officeHoursDto->setSlotType("I");
		$officeHoursDto->setSlotStart(new \DateTime("- 3 hour"));
		
		$officeHours = $this->officeHoursService->createOfficeHour($officeHoursDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateOfficeHoursInvalidSlotEndDate()
	{
		$officeHoursDto = $this->createOfficeHoursDto();
		$officeHoursDto->setSlotType("I");
		$officeHoursDto->setSlotEnd(new \DateTime("+ 1 hour"));
	
		$officeHours = $this->officeHoursService->createOfficeHour($officeHoursDto);
	}
	
	
	public function testEditOfficeHour()
	{
		$officeHoursDto = $this->createOfficeHoursDto();		
		$officeHoursDto->setSlotType("I");
		$officeHours = $this->officeHoursService->createOfficeHour($officeHoursDto);
		
		$officeHour = $this->officeHoursService->editOfficeHour($officeHours);
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\OfficeHoursDto', $officeHours);
		$this->assertEquals($officeHours->getSlotStart(), $officeHour->getSlotStart());
		$this->assertEquals($officeHours->getSlotEnd(), $officeHour->getSlotEnd());
		$this->assertEquals($officeHours->getLocation(), $officeHour->getLocation());				
		$this->assertNull($officeHour->getIsCancelled());			
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    */
	public function testCancelOfficeHourWithInvalidOfficeHourId()
	{
		$officeHour = $this->officeHoursService->cancel($this->personId,$isproxy=1, $this->invalidOfficehoursId);
        $this->assertInternalType('object', $officeHour,"Officehour Not Found.");
		$this->assertSame('{"errors": ["Officehour Not Found."],
			"data": [],
			"sideLoaded": []
			}',$officeHour);
	}	
}
