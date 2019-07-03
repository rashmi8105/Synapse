<?php
use Codeception\Util\Stub;

use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\CoreBundle\Entity\OfficeHours;
use Synapse\RestBundle\Entity\AppointmentsDto;

class StudentAppointmentServiceTest extends \Codeception\TestCase\Test
{

    /**
     *
     * @var \Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService
     */
    private $studentAppointmentService;

    /**
     *
     * @var \Synapse\CoreBundle\Service\Impl\OfficeHoursService
     */
    private $officeHoursService;

    private $personStudentId = 6;

    private $timezone = "Pacific";

    private $invalidStudentId = 0;

    private $orgId = 1;

    private $invalidOrgId = -20;

    private $personFacultyId = 4;

    private $officeHoursId = 0;

    private $invalidFaculty = 0;

    private $notValidStudent = "Not a valid Student Id.";

    private $orgNotFound = "Organization Not Found.";

    private $personNotFound = "Person Not Found.";

    private $slotNotFound = "The appointment slot you have selected is already booked or removed";

    private $appointmentNotFound = "Appointment Not Found.";

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->studentAppointmentService = $this->container->get('studentappointment_service');
        $this->officeHoursService = $this->container->get('officehours_service');
    }

    public function testGetStudentCampuses()
    {
        $campuses = $this->studentAppointmentService->getStudentCampuses($this->personStudentId);
        $this->assertInstanceOf("Synapse\MultiCampusBundle\EntityDto\ListCampusDto", $campuses);
        $this->assertNotEmpty($campuses);
        $this->assertNotNull($campuses->getCampus(), "Campuses not empty");
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusesByInvalidStudentId()
    {
        $campuses = $this->studentAppointmentService->getStudentCampuses($this->invalidStudentId);
        $this->assertSame('{"errors": ["' . $this->notValidStudent . '"],
			"data": [],
			"sideLoaded": []
			}', $campuses);
    }

    public function testGetStudentCampusConnection()
    {
        $campusCon = $this->studentAppointmentService->getStudentCampusConnections($this->personStudentId, $this->orgId, $this->timezone);
        $this->assertInstanceOf("Synapse\StudentViewBundle\EntityDto\ListCampusConnectionDto", $campusCon);
        $this->assertNotEmpty($campusCon);
        $this->assertNotNull($campusCon->getCampusConnection(), "Campus connection not empty");
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusConnectionInvalidStudentId()
    {
        $campusCon = $this->studentAppointmentService->getStudentCampusConnections($this->invalidStudentId, $this->orgId, $this->timezone);
        $this->assertSame('{"errors": ["' . $this->notValidStudent . '"],
			"data": [],
			"sideLoaded": []
			}', $campuses);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusConnectionInvalidOrgId()
    {
        $campusCon = $this->studentAppointmentService->getStudentCampusConnections($this->personStudentId, $this->invalidOrgId, $this->timezone);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $campusCon);
    }

    public function testGetFacultyOfficeHours()
    {
        $openSlot = $this->studentAppointmentService->getFacultyOfficeHours($this->orgId, $this->timezone, $this->personFacultyId, 'week');
        if (count($openSlot) > 0) {
            $this->assertNotEmpty($openSlot);
            $this->assertInstanceOf("Synapse\RestBundle\Entity\AppointmentsReponseDto", $openSlot);
            $this->assertNotNull($openSlot->getCalendarTimeSlots(), "Open time slot is not empty");
            $this->officeHoursId = $openSlot->getCalendarTimeSlots()[0]->getOfficeHoursId();
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetFacultyOfficeHoursInvalidFaculty()
    {
        $openSlot = $this->studentAppointmentService->getFacultyOfficeHours($this->orgId, $this->timezone, $this->invalidFaculty, 'week');
        $this->assertSame('{"errors": ["' . $this->personNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $openSlot);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetFacultyOfficeHoursInvalidOrganization()
    {
        $openSlot = $this->studentAppointmentService->getFacultyOfficeHours($this->orgId, $this->timezone, $this->invalidFaculty, 'week');
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $openSlot);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetUpcomingAppointmentsInvalidStudent()
    {
        $upcomingAppointments = $this->studentAppointmentService->getStudentsUpcomingAppointments($this->invalidStudentId, $this->timezone);
        $this->assertSame('{"errors": ["' . $this->notValidStudent . '"],
			"data": [],
			"sideLoaded": []
			}', $upcomingAppointments);
    }

    public function testCreateStudentAppointment()
    {
		$appointmentDto = $this->createAppointmentsDto();        
        $openSlot = $this->studentAppointmentService->getFacultyOfficeHours($this->orgId, $this->timezone, $this->personFacultyId, 'week');
        if (count($openSlot) > 0) {
            $this->officeHoursId = $openSlot->getCalendarTimeSlots()[0]->getOfficeHoursId();
        }
        $appointmentDto->setOfficeHoursId($this->officeHoursId);
        $appointments = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appointmentDto, $this->timezone);
        if (count($appointments) > 0) {
            $this->assertNotEmpty($appointments);
            $this->assertInstanceOf("Synapse\RestBundle\Entity\AppointmentsDto", $appointments);
            $this->assertNotNull($appointments->getAppointmentId(), "Appointment id is not empty");
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateStudentAppointmentInvalidStudent()
    {
        $appointmentDto = $this->createAppointmentsDto();
        $appointments = $this->studentAppointmentService->createStudentAppointment($this->invalidStudentId, $appointmentDto, $this->timezone);
        $this->assertSame('{"errors": ["' . $this->personNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $appointments);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateStudentAppointmentInvalidFaculty()
    {
        $appointmentDto = $this->createAppointmentsDto();
        $appointmentDto->setPersonId($this->invalidFaculty);
        $appointments = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appointmentDto, $this->timezone);
        $this->assertSame('{"errors": ["' . $this->personNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $appointments);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateStudentAppointmentInvalidOrganization()
    {
        $appointmentDto = $this->createAppointmentsDto();
        $appointmentDto->setOrganizationId($this->invalidOrgId);
        $appointments = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appointmentDto, $this->timezone);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $appointments);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateStudentAppointmentInvalidSlot()
    {
        $appointmentDto = $this->createAppointmentsDto();
        $appointmentDto->setOfficeHoursId(0);
        $appointments = $this->studentAppointmentService->createStudentAppointment($this->personStudentId, $appointmentDto, $this->timezone);
        $this->assertSame('{"errors": ["' . $this->slotNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $appointments);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCancelStudentAppointmentInvalidAppointment()
    {
        $appointments = $this->studentAppointmentService->cancelStudentAppointment($this->personStudentId, 0);
        $this->assertSame('{"errors": ["' . $this->notValidStudent . '"],
			"data": [],
			"sideLoaded": []
			}', $appointments);
    }

    public function createAppointmentsDto()
    {
        $appointments = new AppointmentsDto();
        $appointments->setPersonId($this->personFacultyId);
        $appointments->setOrganizationId($this->orgId);
        $appointments->setDetail("Reason");
        $appointments->setDetailId(1);
        $appointments->setLocation("My Test Location");
        $appointments->setDescription("Student description");
        $appointments->setOfficeHoursId($this->officeHoursId);
        $appointments->setType("I");
        $appointments->setSlotStart(new \DateTime("+ 20 hours"));
        $appointments->setSlotEnd(new \DateTime("+ 21 hours"));
        return $appointments;
    }
}