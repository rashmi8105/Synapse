<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\RestBundle\Entity\OfficeHoursSeriesDto;

class StudentAppointmentServiceTest extends \Codeception\TestCase\Test
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
    
    private $studentAppointmentService;
    private $officeHourService;
    private $personStudentId = 6;
    private $invalidPersonStudent = -1;
    private $invalidStudent = 3;
    private $organizationId = 1;
    private $usertype = 'student';
    private $personFacultyId = 1;
    private $timezone = "Pacific";
    private $invaldOrg = -10;
    
    public function _before() {
        $this->container = $this->getModule ( 'Symfony2' )->container;
        $this->studentAppointmentService = $this->container->get ( 'studentappointment_service' );
        $this->officeHourService = $this->container->get ( 'officehours_service' );
    }
    
    
    private function createOfficeHoursDto()
    {
        $offHrsDto = new OfficeHoursDto();
        $offHrsDto->setPersonId($this->personFacultyId);
        $offHrsDto->setPersonIdProxy(0);
        $offHrsDto->setOrganizationId($this->organizationId);
        $offHrsDto->setSlotType("I");
        $sHrs = rand(100,200);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $offHrsDto->setSlotStart($sdate);
        $offHrsDto->setSlotEnd($edate);
        
        $seriesInfo = new OfficeHoursSeriesDto();
        $seriesInfo->setMeetingLength(60);
        $seriesInfo->setRepeatPattern("D");
        $seriesInfo->setRepeatEvery(1);
        $seriesInfo->setRepeatOccurence("0");
        $seriesInfo->setRepeatRange("N");
        $seriesInfo->setRepeatMonthlyOn(0);
        
        $offHrsDto->setSeriesInfo($seriesInfo);
        $offHrsDto->setMeetingLength(60);
        return $offHrsDto;
    }
    private function createAppointmentDto()
    {
        $appDto = new AppointmentsDto();
        $appDto->setPersonId($this->personFacultyId);
        $appDto->setOrganizationId($this->organizationId);
        $appDto->setDetailId(19);
        $appDto->setLocation("api - Stepojevac");
        $offceHours = $this->officeHourService->create($this->createOfficeHoursDto());

        $appDto->setOfficeHoursId($offceHours->getOfficeHoursId());
        $appDto->setType("S");
        $appDto->setSlotStart($offceHours->getSlotStart());
        $appDto->setSlotEnd($offceHours->getSlotEnd());
        return $appDto;
    }
    
    public function testGetStudentUpcomingAppointments()
    {
        $appDto = $this->createAppointmentDto();
        $appointment = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appDto, $this->timezone);

        $appList = $this->studentAppointmentService->getStudentsUpcomingAppointments($this->personStudentId, $this->timezone);
        $this->assertInstanceOf("Synapse\RestBundle\Entity\AppointmentsReponseDto", $appList);
        if(count($appList->getCalendarTimeSlots()) > 0){
            foreach($appList->getCalendarTimeSlots() as $app){
                $this->assertNotEmpty($app->getAppointmentId());
                $this->assertNotEmpty($app->getOrganizationId());
                $this->assertNotEmpty($app->getSlotStart());
                $this->assertNotEmpty($app->getSlotEnd());
                $this->assertNotEmpty($app->getPersonId());
                $this->assertNotEmpty($app->getOfficeHoursId());
            }
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentUpcomingAppointmentsInvalidStudent()
    {
        $appList = $this->studentAppointmentService->getStudentsUpcomingAppointments($this->invalidPersonStudent, $this->timezone);
    }
    
    public function testGetStudentCampuses()
    {
        $studentCampuses = $this->studentAppointmentService->getStudentCampuses($this->personStudentId);
        $this->assertInstanceOf("Synapse\MultiCampusBundle\EntityDto\ListCampusDto", $studentCampuses);
        if(count($studentCampuses->getCampus()) > 0){
            foreach($studentCampuses->getCampus() as $campus){
                $this->assertInstanceOf("Synapse\MultiCampusBundle\EntityDto\CampusDto", $campus);
                $this->assertNotEmpty($campus->getOrganizationId());
                $this->assertNotEmpty($campus->getCampusName());
                $this->assertNotEmpty($campus->getOrgFeatures());
            }
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusesInvalidStudent()
    {
        $studentCampuses = $this->studentAppointmentService->getStudentCampuses($this->invalidPersonStudent);
    }
    
    public function testGetStudentCampusConnections()
    {
        $studentCampuseConn = $this->studentAppointmentService->getStudentCampusConnections($this->personStudentId, $this->organizationId, $this->timezone);
        $this->assertInstanceOf("Synapse\StudentViewBundle\EntityDto\ListCampusConnectionDto", $studentCampuseConn);
        if(count($studentCampuseConn->getCampusConnection()) > 0){
            foreach($studentCampuseConn->getCampusConnection() as $conn){
                $this->assertNotEmpty($conn->getPersonId());
                $this->assertNotEmpty($conn->getPersonFirstname());
                $this->assertNotEmpty($conn->getPersonLastname());
            }
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusConnectionsinvaliOrganization()
    {
        $studentCampuseConn = $this->studentAppointmentService->getStudentCampusConnections($this->personStudentId, $this->invaldOrg, $this->timezone);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusConnectionsinvaliStudent()
    {
        $studentCampuseConn = $this->studentAppointmentService->getStudentCampusConnections($this->invalidPersonStudent, $this->organizationId, $this->timezone);
    }
    
    public function testGetFacultyOfficeHours()
    {
        $officeHours = $this->createOfficeHoursDto();
        $sHrs = rand(10,20);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $officeHours->setSlotStart($sdate);
        $officeHours->setSlotEnd($edate);
        $offceHours = $this->officeHourService->create($officeHours);
        $officeHoursList = $this->studentAppointmentService->getFacultyOfficeHours($this->organizationId, $this->timezone, $this->personFacultyId,'month');
        $this->assertInstanceOf("Synapse\RestBundle\Entity\AppointmentsReponseDto", $officeHoursList);
        $this->assertEquals($this->personFacultyId, $officeHoursList->getPersonId());
        $this->assertInternalType('array', $officeHoursList->getCalendarTimeSlots());
        if(count($officeHoursList->getCalendarTimeSlots()) > 0){
            foreach($officeHoursList->getCalendarTimeSlots() as $offhrs){
                $this->assertNotEmpty($offhrs->getOrganizationId());
                $this->assertNotEmpty($offhrs->getSlotStart());
                $this->assertNotEmpty($offhrs->getSlotEnd());
                $this->assertNotEmpty($offhrs->getOfficeHoursId());
            }
        }
    }
    
    public function testGetFacultyOfficeHoursByTerm()
    {
        $officeHours = $this->createOfficeHoursDto();
        $sHrs = rand(10,20);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $officeHours->setSlotStart($sdate);
        $officeHours->setSlotEnd($edate);
        $offceHours = $this->officeHourService->create($officeHours);
        $officeHoursList = $this->studentAppointmentService->getFacultyOfficeHours($this->organizationId, $this->timezone, $this->personFacultyId,'term');
        $this->assertInstanceOf("Synapse\RestBundle\Entity\AppointmentsReponseDto", $officeHoursList);
        $this->assertEquals($this->personFacultyId, $officeHoursList->getPersonId());
        $this->assertInternalType('array', $officeHoursList->getCalendarTimeSlots());
        if(count($officeHoursList->getCalendarTimeSlots()) > 0){
            foreach($officeHoursList->getCalendarTimeSlots() as $offhrs){
                $this->assertNotEmpty($offhrs->getOrganizationId());
                $this->assertNotEmpty($offhrs->getSlotStart());
                $this->assertNotEmpty($offhrs->getSlotEnd());
                $this->assertNotEmpty($offhrs->getOfficeHoursId());
            }
        }
    }
    
    public function testGetFacultyOfficeHoursByToday()
    {
        $officeHours = $this->createOfficeHoursDto();
        $sHrs = rand(10,20);
        $eHrs = $sHrs+1;
        $sdate=new DateTime("+ $sHrs hour");
        $edate=new DateTime("+ $eHrs hour");
        $officeHours->setSlotStart($sdate);
        $officeHours->setSlotEnd($edate);
        $offceHours = $this->officeHourService->create($officeHours);
        $officeHoursList = $this->studentAppointmentService->getFacultyOfficeHours($this->organizationId, $this->timezone, $this->personFacultyId,'today');
        $this->assertInstanceOf("Synapse\RestBundle\Entity\AppointmentsReponseDto", $officeHoursList);
        $this->assertEquals($this->personFacultyId, $officeHoursList->getPersonId());
        $this->assertInternalType('array', $officeHoursList->getCalendarTimeSlots());
        if(count($officeHoursList->getCalendarTimeSlots()) > 0){
            foreach($officeHoursList->getCalendarTimeSlots() as $offhrs){
                $this->assertNotEmpty($offhrs->getOrganizationId());
                $this->assertNotEmpty($offhrs->getSlotStart());
                $this->assertNotEmpty($offhrs->getSlotEnd());
                $this->assertNotEmpty($offhrs->getOfficeHoursId());
            }
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetFacultyOfficeHoursInvalidOrganization()
    {
        $officeHoursList = $this->studentAppointmentService->getFacultyOfficeHours($this->invaldOrg, $this->timezone, $this->personFacultyId,'today');
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetFacultyOfficeHoursInvalidFaculty()
    {
        $officeHoursList = $this->studentAppointmentService->getFacultyOfficeHours($this->organizationId, $this->timezone, $this->invalidPersonStudent,'today');
    }
    
    public function testCreateStudentAppointment()
    {    
        $appDto = $this->createAppointmentDto();
        $appointment = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appDto, $this->timezone);
        $this->assertInstanceOf("Synapse\RestBundle\Entity\AppointmentsDto", $appointment);
        $this->assertNotEmpty($appointment->getAppointmentId());
        $this->assertEquals($this->personFacultyId, $appointment->getPersonId());
        $this->assertEquals($this->organizationId, $appointment->getOrganizationId());
        $this->assertEquals($appDto->getDetailId(), $appointment->getDetailId());
        $this->assertEquals($appDto->getLocation(), $appointment->getLocation());
        $this->assertEquals($appDto->getOfficeHoursId(), $appointment->getOfficeHoursId());
        $this->assertEquals($appDto->getType(), $appointment->getType());
        $this->assertEquals($appDto->getSlotStart(), $appointment->getSlotStart());
        $this->assertEquals($appDto->getSlotEnd(), $appointment->getSlotEnd());
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateStudentAppointmentInvalidOrganization()
    {
        $appDto = $this->createAppointmentDto();
        $appDto->setOrganizationId($this->invaldOrg);
        $appointment = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appDto, $this->timezone);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateStudentAppointmentInvalidStudent()
    {
        $appDto = $this->createAppointmentDto();
        $appointment = $this->studentAppointmentService->createStudentAppointment($this->invalidPersonStudent, $appDto, $this->timezone);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateStudentAppointmentInvalidFaculty()
    {
        $appDto = $this->createAppointmentDto();
        $appDto->setPersonId($this->invalidPersonStudent);
        $appointment = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appDto, $this->timezone);
    }
    
    public function testCancelStudentAppointment()
    {
        $appDto = $this->createAppointmentDto();
        $appointment = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appDto, $this->timezone);
        $cancelApp = $this->studentAppointmentService->cancelStudentAppointment($this->personStudentId, $appointment->getAppointmentId(), true);
        $this->assertEquals($appointment->getAppointmentId(), $cancelApp);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCancelStudentAppointmentInvalidAppointment()
    {
        $cancelApp = $this->studentAppointmentService->cancelStudentAppointment($this->personStudentId, -1, true);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCancelStudentAppointmentInvalidStudent()
    {
        $appDto = $this->createAppointmentDto();
        $appointment = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appDto, $this->timezone);
        $cancelApp = $this->studentAppointmentService->cancelStudentAppointment($this->invalidPersonStudent, $appointment->getAppointmentId(), true);
    }
}