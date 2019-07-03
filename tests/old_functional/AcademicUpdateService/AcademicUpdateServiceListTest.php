<?php
use Codeception\Util\Stub;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaffDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesDto;
use Synapse\AcademicUpdateBundle\EntityDto\GroupsDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaticListDto;

class AcademicUpdateServiceListTest extends \Codeception\TestCase\Test
{

    private $academicUpdateService;

    private $organizationId = 1;

    private $facultyId = 4;

    private $invalidOrganizationId = -20;

    private $invalidRequestId = 0;

    private $randomNumber = 0;

    private $requestName = "";

    private $requestDesc = "";

    private $coordinatorId = 1;

    private $userType = "coordinator";

    private $requestFilterAll = "all";

    private $requestFilterOpen = "myopen";

    private $jsonMode = "json";

    private $orgNotFound = "Organization Not Found.";

    private $auNotFound = "Academic Update Request Not Found.";

    private $accessError = "You do not have coordinator access";

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->academicUpdateService = $this->container->get('academicupdate_service');
        $this->academicUpdateCreateService = $this->container->get('academicupdatecreate_service');
        $this->randomNumber = mt_rand();
        $this->requestName = "Science AU Request " . $this->randomNumber;
        $this->requestDesc = "AU test description";
    }

    public function testGetStudentsByOrganizationCourse()
    {
        $students = $this->academicUpdateService->getStudentsByOrganizationCourse($this->organizationId);
        $this->assertInternalType('object', $students);
        $this->assertNotEmpty($students);
        $this->assertEquals($this->organizationId, $students->getOrganizationId());
        $this->assertNotNull($students->getTotalStudentsCount(), "Total students not empty");
        $this->assertObjectHasAttribute("studentDetails", $students);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentsByInvalidOrganizationCourse()
    {
        $students = $this->academicUpdateService->getStudentsByOrganizationCourse($this->invalidOrganizationId);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $students);
    }

    public function testFacultiesByOrganizationCourse()
    {
        $faculties = $this->academicUpdateService->getFacultiesByOrganizationCourse($this->organizationId);
        $this->assertInternalType('object', $faculties);
        $this->assertNotEmpty($faculties);
        $this->assertEquals($this->organizationId, $faculties->getOrganizationId());
        $this->assertNotNull($faculties->getTotalStaffCount(), "Total faculties not empty");
        $this->assertObjectHasAttribute("staffDetails", $faculties);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetFacultiesByInvalidOrganizationCourse()
    {
        $faculties = $this->academicUpdateService->getFacultiesByOrganizationCourse($this->invalidOrganizationId);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $faculties);
    }

    public function testActiveCourseByOrganization()
    {
        $courses = $this->academicUpdateService->getActiveCourseByOrganization($this->organizationId);
        $this->assertInternalType('object', $courses);
        $this->assertNotEmpty($courses);
        $this->assertEquals($this->organizationId, $courses->getOrganizationId());
        $this->assertNotNull($courses->getTotalCourseCount(), "Total courses not empty");
        $this->assertObjectHasAttribute("courseDetails", $courses);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testActiveCourseByInvalidOrganization()
    {
        $courses = $this->academicUpdateService->getActiveCourseByOrganization($this->invalidOrganizationId);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $courses);
    }

    public function testGroupByOrganization()
    {
        $groups = $this->academicUpdateService->getGroupByOrganization($this->organizationId);
        $this->assertInternalType('object', $groups);
        $this->assertNotEmpty($groups);
        $this->assertEquals($this->organizationId, $groups->getOrganizationId());
        $this->assertNotNull($groups->getTotalGroupCount(), "Total groups not empty");
        $this->assertObjectHasAttribute("groupDetails", $groups);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGroupByInvalidOrganization()
    {
        $groups = $this->academicUpdateService->getGroupByOrganization($this->invalidOrganizationId);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $groups);
    }

    public function testCreateAcademicUpdate()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        
        $academicUpdate = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        if ($academicUpdate) {
            $this->assertEquals($this->requestName, $academicUpdate->getRequestName());
            $this->assertEquals($this->requestDesc, $academicUpdate->getRequestDescription());
        }
    }

    public function testCancelAcadamicUpdate()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->academicUpdateService->cancelAcademicUpdateRequest($this->organizationId, $newAU->getId(), $this->coordinatorId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCancelAcadamicUpdateWithInvalidRequestId()
    {
        $this->academicUpdateService->cancelAcademicUpdateRequest($this->organizationId, $this->invalidRequestId, $this->coordinatorId);
        $this->assertInternalType('object', $appointments, $this->auNotFound);
        $this->assertSame('{"errors": ["' . $this->auNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $appointments);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCancelAcadamicUpdateWithInvalidUser()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $auCancel = $this->academicUpdateService->cancelAcademicUpdateRequest($this->organizationId, $newAU->getId(), $this->facultyId);
        $this->assertInternalType('object', $auCancel, $this->auNotFound);
        $this->assertSame('{"errors": ["' . $this->auNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $auCancel);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSendReminderToFacultyWithRequestId()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $auReminder = $this->academicUpdateService->sendReminderToFaculty($this->organizationId, $this->invalidRequestId, $this->coordinatorId);
        $this->assertInternalType('object', $auReminder, $this->auNotFound);
        $this->assertSame('{"errors": ["' . $this->auNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $auReminder);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSendReminderToFacultyWithInvalidUser()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $auReminder = $this->academicUpdateService->sendReminderToFaculty($this->organizationId, $newAU->getId(), $this->facultyId);
        $this->assertInternalType('object', $auReminder, $this->accessError);
        $this->assertSame('{"errors": ["' . $this->accessError . '"],
			"data": [],
			"sideLoaded": []
			}', $auReminder);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSendRemiderAcadamicUpdateWithInvalidOrganization()
    {
        $reminder = $this->academicUpdateService->sendReminderToFaculty($this->invalidOrganizationId, $this->invalidRequestId, $this->coordinatorId);
        $this->assertInternalType('object', $reminder, $this->orgNotFound);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $reminder);
    }

    public function testGetAURequestForCoordinator()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $auList = $this->academicUpdateService->getAcademicUpdateRequestList($this->userType, $this->organizationId, $this->requestFilterAll, '', $this->jsonMode, $this->coordinatorId);
        $this->assertInstanceOf('Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateResponseDto', $auList);
    }

    public function testGetAURequestForCoordinatorWithMyOpen()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $auList = $this->academicUpdateService->getAcademicUpdateRequestList($this->userType, $this->organizationId, $this->requestFilterOpen, '', $this->jsonMode, $this->coordinatorId);
        $this->assertInstanceOf('Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateResponseDto', $auList);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAURequestForCoordinatorWithInvalidOrganization()
    {
        $auList = $this->academicUpdateService->getAcademicUpdateRequestList($this->userType, $this->invalidOrganizationId, $this->requestFilterAll, '', $this->jsonMode, $this->coordinatorId);
        $this->assertInternalType('object', $auReminder, $this->accessError);
        $this->assertSame('{"errors": ["' . $this->accessError . '"],
			"data": [],
			"sideLoaded": []
			}', $auList);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAURequestForFacultyWithInvalidUser()
    {
        $auList = $this->academicUpdateService->getAcademicUpdateRequestList($this->userType, $this->invalidOrganizationId, $this->requestFilterAll, '', $this->jsonMode, $this->facultyId);
        $this->assertInternalType('object', $auReminder, $this->accessError);
        $this->assertSame('{"errors": ["' . $this->accessError . '"],
			"data": [],
			"sideLoaded": []
			}', $auList);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAURequestForFacultyWithInvalidOrganization()
    {
        $auList = $this->academicUpdateService->getAcademicUpdateRequestList($this->userType, $this->invalidOrganizationId, $this->requestFilterAll, '', $this->jsonMode, $this->facultyId);
        $this->assertInternalType('object', $auReminder, $this->orgNotFound);
        $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $auList);
    }

    public function createAcademicUpdateDto()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName($this->requestName);
        $academicUpdateCreateDto->setRequestDescription($this->requestDesc);
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
        
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("6,8");
        
        $academicUpdateCreateDto->setStudents($studentsDto);
        
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("4,5");
        
        $academicUpdateCreateDto->setStaff($staffDto);
        
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll(false);
        $coursesDto->setSelectedCourseIds("");
        
        $academicUpdateCreateDto->setCourses($coursesDto);
        
        $groupsDto = new GroupsDto();
        $groupsDto->setIsAll(false);
        $groupsDto->setSelectedGroupIds("");
        
        $academicUpdateCreateDto->setGroups($groupsDto);
		
		$staticListDto = new StaticListDto();
		$staticListDto->setIsAll(false);
		$staticListDto->setSelectedStaticIds("");
		
		$academicUpdateCreateDto->setStaticList($staticListDto);
        
        return $academicUpdateCreateDto;
    }
}