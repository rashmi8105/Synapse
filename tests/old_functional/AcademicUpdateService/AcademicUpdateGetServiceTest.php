<?php
use Codeception\Util\Stub;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaffDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesDto;
use Synapse\AcademicUpdateBundle\EntityDto\GroupsDto;

use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsStudentDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto;

class AcademicUpdateGetServiceListTest extends \Codeception\TestCase\Test
{

    private $academicUpdateService;

    private $academicUpdateCreateService;

    private $academicUpdateGetService;

    private $organizationId = 1;

    private $facultyId = 4;

    private $studentId = 6;

    private $invalidOrganizationId = -2;

    private $invalidStudentId = 0;
    
    private $invalidCourseId = 0;

    private $randomNumber = 0;

    private $requestName = "";

    private $requestDesc = "";

    private $coordinatorId = 1;

    private $orgNotFound = "Organization Not Found.";

    private $courseNotFound = "Course not found";

    private $accessError = "You do not have coordinator access";
    
    private $personNotFound = "Person Not Found";

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->academicUpdateService = $this->container->get('academicupdate_service');
        $this->academicUpdateCreateService = $this->container->get('academicupdatecreate_service');
        $this->academicUpdateGetService = $this->container->get('academicupdateget_service');
        $this->randomNumber = mt_rand();
        $this->requestName = "Science AU Request " . $this->randomNumber;
        $this->requestDesc = "AU test description";
    }

    public function testGetAcademicUpdateStudentGradeHistory()
    {
        $this->markTestSkipped('$courseId = $course->getCourseDetails()[0]->getCourseId(); Causing Fatal Error');
        $course = $this->academicUpdateService->getActiveCourseByOrganization($this->organizationId);
        $courseId = $course->getCourseDetails()[0]->getCourseId();
        if ($courseId && $courseId > 0) {
            $auHistory = $this->academicUpdateGetService->getAcademicUpdateStudentHistory($this->organizationId, $courseId, $this->studentId, $this->coordinatorId);
            $this->assertInternalType('object', $auHistory);
            $this->assertNotEmpty($auHistory);
            $this->assertObjectHasAttribute("academicUpdateHistory", $auHistory);
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAcademicUpdateStudentGradeHistoryInvalidOrg()
    {
        $this->markTestSkipped("Fatal error");
        $course = $this->academicUpdateService->getActiveCourseByOrganization($this->organizationId);
        $courseId = $course->getCourseDetails()[0]->getCourseId();
        if ($courseId && $courseId > 0) {
            $auHistory = $this->academicUpdateGetService->getAcademicUpdateStudentHistory($this->invalidOrganizationId, $courseId, $this->studentId, $this->coordinatorId);
            $this->assertSame('{"errors": ["' . $this->orgNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $auHistory);
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAcademicUpdateStudentGradeHistoryInvalidCourse()
    {
            $auHistory = $this->academicUpdateGetService->getAcademicUpdateStudentHistory($this->organizationId, $this->invalidCourseId, $this->studentId, $this->coordinatorId);
            $this->assertSame('{"errors": ["' . $this->courseNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $auHistory);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAcademicUpdateStudentGradeHistoryInvalidStudent()
    {
        $this->markTestSkipped("Fatal error");
        $course = $this->academicUpdateService->getActiveCourseByOrganization($this->organizationId);
        $courseId = $course->getCourseDetails()[0]->getCourseId();
        if ($courseId && $courseId > 0) {
            $auHistory = $this->academicUpdateGetService->getAcademicUpdateStudentHistory($this->organizationId, $courseId, $this->invalidStudentId, $this->coordinatorId);
            $this->assertSame('{"errors": ["' . $this->personNotFound . '"],
			"data": [],
			"sideLoaded": []
			}', $auHistory);
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAcademicUpdateStudentGradeHistoryInvalidUser()
    {
        $this->markTestSkipped("Fatal error");
        $course = $this->academicUpdateService->getActiveCourseByOrganization($this->organizationId);
        $courseId = $course->getCourseDetails()[0]->getCourseId();
        if ($courseId && $courseId > 0) {
            $auHistory = $this->academicUpdateGetService->getAcademicUpdateStudentHistory($this->organizationId, $courseId, $this->invalidStudentId, $this->facultyId);
            $this->assertSame('{"errors": ["' . $this->accessError . '"],
			"data": [],
			"sideLoaded": []
			}', $auHistory);
        }
    }

    public function createAcademicUpdateDto()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
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
        
        return $academicUpdateCreateDto;
    }

    public function getAcademicUpdateDto($requestId, $acIdOne, $acIdTwo)
    {
        $academicUpdateDto = new AcademicUpdateDto();
        $academicUpdateDto->setRequestId($requestId);
        $details = new AcademicUpdateDetailsDto();
        
        $studentDetails = new AcademicUpdateDetailsStudentDto();
        $studentDetails->setAcademicUpdateId($acIdOne);
        $studentDetails->setStudentId(6);
        $studentDetails->setStudentAbsences(2);
        $studentDetails->setStudentAbsences(2);
        $studentDetails->setStudentSend(true);
        $details->setStudentDetails([
            $studentDetails
        ]);
        $academicUpdateDto->setRequestDetails([
            $details
        ]);
        return $academicUpdateDto;
    }
}