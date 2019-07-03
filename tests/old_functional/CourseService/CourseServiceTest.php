<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;

use Synapse\AcademicBundle\EntityDto\FacultyPermissionDto;
use Synapse\AcademicBundle\EntityDto\AddUserCourseDto;
use Synapse\AcademicBundle\Entity\OrgCourses;

class CourseServiceTest extends \Codeception\TestCase\Test
{

    private $facultyIdForCourse = 1;

    private $courseFacultyId = 1;

    private $courseStudentId = 1;

    private $studentIdForCourse = 1;

    private $invalidCourseId = - 1;

    private $invalidCourseStudentId = - 1;

    private $invalidCourseFacultyId = - 1;

    private $coordinatorId = 1;

    private $invalidYearId = - 1;

    private $organizationId = 1;

    private $yearId = '201415';

    private $courseId = 1;

    private $invalidUserType = "cooordinator";

    private $college = 'IIT';

    private $filter = 'all';

    private $department = "IT";

    private $viewMode = 'json';

    private $export = '';

    private $userId = 1;

    private $termId = 1;
    
    private $validCourseId =  1;
    
    private $orgService;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->courseService = $this->container->get('course_service');
        $this->orgService = $this->container->get('org_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->courseFacultyId);
    }

    public function testDeleteFacultyforCourse()
    {
        $this->initializeRbac();
        
        $facultyCourseDto = $this->getAddFacultyToCourseDto();
        $createCourse = $this->courseService->addFacultyStudentCourse($facultyCourseDto, $this->organizationId, $this->coordinatorId);
        
        $deleteFacultyCourse = $this->courseService->deleteFacultyCourse($this->courseId, $createCourse->getPersonId(),false,false);
        $this->assertEquals($this->courseId, $deleteFacultyCourse);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteFacultyInvalidCourseId()
    {
        $this->initializeRbac();
        $facultyCourse = $this->courseService->deleteFacultyCourse($this->invalidCourseId, $this->courseFacultyId,false,false);
        $this->assertSame('{"errors": ["Course not found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testDeleteFacultyInvalidFacultyId()
    {
        $this->initializeRbac();
        $facultyCourse = $this->courseService->deleteFacultyCourse($this->courseId, $this->invalidCourseFacultyId, false);
        $this->assertSame('{"errors": ["Faculty not found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    public function testDeleteStudentforCourse()
    {
        $this->initializeRbac();
        
        $studentCourseDto = $this->getAddStudentToCourseDto();
        $createCourseStudent = $this->courseService->addFacultyStudentCourse($studentCourseDto, $this->organizationId, $this->coordinatorId);
        
        $deleteFacultyCourse = $this->courseService->deleteStudentCourse($this->courseId, $createCourseStudent->getPersonId(),false,false);
        
        $this->assertEquals($this->courseId, $deleteFacultyCourse);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteStudentInvalidCourseId()
    {
        //$this->markTestSkipped("Failed");
        $this->initializeRbac();
        $facultyCourse = $this->courseService->deleteStudentCourse($this->invalidCourseId, $this->courseStudentId,false,false);
        $this->assertSame('{"errors": ["Course not found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testDeleteStudentInvalidStudentIdAccessDenied()
    {
        $facultyCourse = $this->courseService->deleteStudentCourse($this->courseId, $this->invalidCourseStudentId);
        $this->assertSame('{"errors": ["Student Not Found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    public function testListCoordinatorCourse()
    {
        //$this->markTestSkipped("ERRORED: PHP Fatal error:  Call to a member function getCollege() on a non-object in synapse-backend/tests/functional/CourseService/CourseServiceTest.php on line 143");
        $this->initializeRbac();
        $searchParam['year'] = $this->yearId;
        $searchParam['term'] = $this->termId;
        $searchParam['college'] = $this->college;
        $searchParam['department'] = $this->department;
        $searchParam['filter'] = '';
        $courseList = $this->courseService->listCoursesByRole($this->coordinatorId, 'coordinator', $searchParam, $this->viewMode, $this->export);
        $this->assertNotEmpty($courseList);
        $this->assertObjectHasAttribute("totalCourse", $courseList);
        $this->assertObjectHasAttribute("totalFaculty", $courseList);
        $this->assertObjectHasAttribute("totalStudents", $courseList);
        $this->assertObjectHasAttribute("courseListTable", $courseList);
        $this->assertEquals($courseList->getCourseListTable()[0]
            ->getCollege(), $this->college);
        $this->assertEquals($courseList->getCourseListTable()[0]
            ->getDepartment(), $this->department);
        $this->assertEquals($courseList->getCourseListTable()[0]
            ->getYear(), 'Academic year');
        $this->assertEquals($courseList->getCourseListTable()[0]
            ->getTerm(), 'Term 1');
        $this->assertInternalType('array', $courseList->getCourseListTable());
        $this->assertInternalType('array', $courseList->getCourseListTable()[0]
            ->getCourse());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testListCoordinatorCourseForInvalidYear()
    {
        $this->initializeRbac();
        $searchParam['year'] = $this->invalidYearId;
        $searchParam['term'] = $this->termId;
        $searchParam['college'] = $this->college;
        $searchParam['department'] = $this->department;
        $searchParam['filter'] = '';
        $courseList = $this->courseService->listCoursesByRole($this->coordinatorId, 'coordinator', $searchParam, $this->viewMode, $this->export);
        $this->assertSame('{"errors": ["Year Id does not exist."],
			"data": [],
			"sideLoaded": []
			}', $courseList);
    }

    public function testGetCourseDetailsforFaculty()
    {
        $this->initializeRbac();
        
        $facultyCourseDto = $this->getAddFacultyToCourseDto();
        $createCourse = $this->courseService->addFacultyStudentCourse($facultyCourseDto, $this->organizationId, $this->coordinatorId);
        
        $courseList = $this->courseService->getCourseDetails('faculty', 'json', $createCourse->getPersonId(), $this->courseId, $this->organizationId);

        $this->assertNotEmpty($courseList);
        $this->assertObjectHasAttribute("totalStudents", $courseList);
        $this->assertObjectHasAttribute("totalFaculties", $courseList);
        $this->assertObjectHasAttribute("courseId", $courseList);
        $this->assertObjectHasAttribute("courseName", $courseList);
        $this->assertEquals($courseList->getCourseId(), $this->courseId);
        $this->assertEquals($courseList->getCourseName(), "Computer Networks");
        $this->assertInternalType('array', $courseList->getFacultyDetails());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetCourseDetailsforInvalidUserType()
    {
        $this->initializeRbac();
        $courseList = $this->courseService->getCourseDetails($this->invalidUserType, 'json', $this->userId, $this->courseId, $this->organizationId);
        $this->assertSame('{"errors": ["Invalid User Type"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    public function testGetCourseNavigationForAllYear()
    {
        $this->initializeRbac();
        $courseList = $this->courseService->getCourseNavigation($this->organizationId, 'year');
        $this->assertNotEmpty($courseList);
        $this->assertObjectHasAttribute("organizationId", $courseList);
        $this->assertEquals($courseList->getOrganizationId(), $this->organizationId);
        $this->assertEquals($courseList->getType(), 'year');
        $this->assertInternalType('array', $courseList->getCourseNavigation());
    }

    public function testGetCourseNavigationForAllTerm()
    {
        $this->initializeRbac();
        $queryParam['year'] = 'all';
        $queryParam['term'] = '';
        $queryParam['college'] = '';
        $queryParam['department'] = '';
        $queryParam['subject'] = '';
        $queryParam['course'] = '';
        $courseList = $this->courseService->getCourseNavigation($this->organizationId, 'term', $queryParam);
        $this->assertNotEmpty($courseList);
        $this->assertObjectHasAttribute("organizationId", $courseList);
        $this->assertEquals($courseList->getOrganizationId(), $this->organizationId);
        $this->assertEquals($courseList->getType(), 'term');
        $this->assertInternalType('array', $courseList->getCourseNavigation());
    }

    public function testListStudentCourse()
    {
        //$this->markTestSkipped("Errored");
        $this->initializeRbac();
        
        $studentCourseDto = $this->getAddStudentToCourseDto();
        $createCourseStudent = $this->courseService->addFacultyStudentCourse($studentCourseDto, $this->organizationId, $this->coordinatorId);
        
        $courseList = $this->courseService->listCoursesForPerson($createCourseStudent->getPersonId(), 'student', $this->userId, $this->organizationId);

        $this->assertNotEmpty($courseList);
        $this->assertObjectHasAttribute("totalCourse", $courseList);
        $this->assertObjectHasAttribute("studentId", $courseList);
        $this->assertEquals($createCourseStudent->getPersonId(), $courseList->getStudentId());
        $this->assertObjectHasAttribute("courseListTable", $courseList);
        /* @FIXME - fixed */
        $this->assertEquals($courseList->getCourseListTable()[0]
            ->getYear(), "Academic year");
        $this->assertEquals($courseList->getCourseListTable()[1]
            ->getTerm(), "Term 3");
        $this->assertEquals($courseList->getCourseListTable()[1]
            ->getCollege(), 'MMG');
        $this->assertEquals($courseList->getCourseListTable()[1]
            ->getDepartment(), 'ECOM');
        
        $this->assertEquals($courseList->getCourseListTable()[0]
            ->getCourse()[0]
            ->getCourseId(), '1');
        
        $this->assertInternalType('array', $courseList->getCourseListTable());
        $this->assertInternalType('array', $courseList->getCourseListTable()[0]
            ->getCourse());
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testListStudentCourseForInvalidStudentId()
    {
        $courseList = $this->courseService->listCoursesForPerson($this->invalidCourseStudentId, 'student', $this->userId, $this->organizationId);
        $this->assertSame('{"errors": ["Person Not Found"],
			"data": [],
			"sideLoaded": []
			}', $courseList);
    }

    /* public function testUpdateFacultyCoursePermission()
    {
        $this->initializeRbac();
        
        $facultyCourseDto = $this->getAddFacultyToCourseDto();
        $createCourse = $this->courseService->addFacultyStudentCourse($facultyCourseDto, $this->organizationId, $this->coordinatorId);
        
        $facultyPermissionDto = new FacultyPermissionDto();
        $facultyPermissionDto->setCourseId($this->courseId);
        $facultyPermissionDto->setPersonId($createCourse->getPersonId());
        $facultyPermissionDto->setPermissionsetId(1);
        $facultyPermissionDto->setOrganizationId($this->organizationId);

        $courseList = $this->courseService->updateFacultyCoursePermissionset($facultyPermissionDto);
    }
 */
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateFacultyCoursePermissionForInvalidCourseId()
    {
        $facultyPermissionDto = new FacultyPermissionDto();
        $facultyPermissionDto->setCourseId($this->invalidCourseId);
        $facultyPermissionDto->setPersonId($this->courseFacultyId);
        $facultyPermissionDto->setPermissionsetId(1);
        $facultyPermissionDto->setOrganizationId($this->organizationId);
        $courseList = $this->courseService->updateFacultyCoursePermissionset($facultyPermissionDto);
        $this->assertSame('{"errors": ["Course not found"],
			"data": [],
			"sideLoaded": []
			}', $courseList);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateFacultyCoursePermissionForInvalidFaculty()
    {
        $this->initializeRbac();
        $facultyPermissionDto = new FacultyPermissionDto();
        $facultyPermissionDto->setCourseId($this->courseId);
        $facultyPermissionDto->setPersonId($this->invalidCourseFacultyId);
        $facultyPermissionDto->setPermissionsetId(1);
        $facultyPermissionDto->setOrganizationId($this->organizationId);
        $courseList = $this->courseService->updateFacultyCoursePermissionset($facultyPermissionDto);
        $this->assertSame('{"errors": ["Person Not Found"],
			"data": [],
			"sideLoaded": []
			}', $courseList);
    }
    
    private function getAddFacultyToCourseDto(){
        
        $addUserCourseDto = new AddUserCourseDto();
        $addUserCourseDto->setType("faculty");
        $addUserCourseDto->setCourseId($this->courseId);
        $addUserCourseDto->setPersonId(2);
        
        return $addUserCourseDto;
    }
    
    private function getAddStudentToCourseDto(){
    
        $addStudentCourseDto = new AddUserCourseDto();
        $addStudentCourseDto->setType("student");
        $addStudentCourseDto->setCourseId($this->courseId);
        $addStudentCourseDto->setPersonId(6);
    
        return $addStudentCourseDto;
    }
    
   /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteFacultyInvalidFacultyForCourse()
    {
        $this->initializeRbac();
        $facultyCourse = $this->courseService->deleteFacultyCourse($this->courseId, $this->invalidCourseFacultyId, false, false);
    }
    
    public function testDeleteFacultyInvalidFacultyForCourseWithGenerateCSV()
    {
        $this->initializeRbac();
        
        $facultyCourseDto = $this->getAddFacultyToCourseDto();
        $createCourse = $this->courseService->addFacultyStudentCourse($facultyCourseDto, $this->organizationId, $this->coordinatorId);
        
        $facultyCourse = $this->courseService->deleteFacultyCourse($this->courseId, $createCourse->getPersonId(), true, false);
        $this->assertEquals($this->courseId, $facultyCourse);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteStudentInvalidStudentId()
    {
        $facultyCourse = $this->courseService->deleteStudentCourse($this->courseId, $this->invalidCourseStudentId, false, false);
        $this->assertSame('{"errors": ["Student Not Found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }
    
    public function testDeleteStudentGenerateCSV()
    {
        $this->initializeRbac();
        
        $studentCourseDto = $this->getAddStudentToCourseDto();
        $createCourseStudent = $this->courseService->addFacultyStudentCourse($studentCourseDto, $this->organizationId, $this->coordinatorId);
        
        $facultyCourse = $this->courseService->deleteStudentCourse($this->courseId, $createCourseStudent->getPersonId(), true, false);
        $this->assertEquals($this->courseId, $facultyCourse);
    }
    
    private function createOrgCourse()
    {
        $course = new OrgCourses();
        $course->setCollegeCode('ITI');
        $course->setCourseName('Instruments');
        $course->setCourseNumber('1001');
        $course->setCourseSectionId('1001-ITI');
        $course->setCreditHours('20');
        $organization = $this->orgService->find($this->organizationId);
        $course->setOrganization($organization);
        $course->setDaysTimes('10');
        
        return $course;
    }
    
    public function testCreateCourse()
    {
        $this->initializeRbac();
        $course = $this->createOrgCourse();
        
        $courseInstance = $this->courseService->createCourse($course);
        $this->assertEquals('ITI', $courseInstance->getCollegeCode());
        $this->assertEquals('Instruments', $courseInstance->getCourseName());
        $this->assertEquals('1001', $courseInstance->getCourseNumber());
        $this->assertEquals('1001-ITI', $courseInstance->getCourseSectionId());
        $this->assertEquals('20', $courseInstance->getCreditHours());
        $this->assertEquals('10', $courseInstance->getDaysTimes());
        $this->assertEquals($this->organizationId, $courseInstance->getOrganization()->getId());
        
    }
    
    public function testDeleteCourse()
    {
        $this->initializeRbac();

        $deleteCourse = $this->courseService->deleteCourse(2, 1);
        $this->assertEquals(2, $deleteCourse);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteCourseInvalidCourseId()
    {
        $this->initializeRbac();
    
        $deleteCourse = $this->courseService->deleteCourse($this->invalidCourseId, 1);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteCourseWithAcademicUpdateData()
    {
        $this->initializeRbac();
        $deleteCourse = $this->courseService->deleteCourse($this->courseId, 1);
    }
    
    public function testDeleteCourseWithStudentAndFaculty()
    {
        $this->initializeRbac();
        
        $facultyCourseDto = $this->getAddFacultyToCourseDto();
        $facultyCourseDto->setCourseId(2);
        $createCourse = $this->courseService->addFacultyStudentCourse($facultyCourseDto, $this->organizationId, $this->coordinatorId);
        
        $studentCourseDto = $this->getAddStudentToCourseDto();
        $studentCourseDto->setCourseId(2);
        $createCourseStudent = $this->courseService->addFacultyStudentCourse($studentCourseDto, $this->organizationId, $this->coordinatorId);
        
        $deleteCourse = $this->courseService->deleteCourse(2, 1);
        $this->assertEquals(2, $deleteCourse);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddCourseFacultyAlreadyAssigned()
    {
        $this->initializeRbac();
        
        $facultyCourseDto = $this->getAddFacultyToCourseDto();
        $createCourse = $this->courseService->addFacultyStudentCourse($facultyCourseDto, $this->organizationId, $this->coordinatorId);
        
        $facultyCourseDto = $this->getAddFacultyToCourseDto();
        $createCourse = $this->courseService->addFacultyStudentCourse($facultyCourseDto, $this->organizationId, $this->coordinatorId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddCourseStudentAlreadyAssigned()
    {
        $this->initializeRbac();
    
        $studentCourseDto = $this->getAddStudentToCourseDto();
        $createCourseStudent = $this->courseService->addFacultyStudentCourse($studentCourseDto, $this->organizationId, $this->coordinatorId);
    
        $studentCourseDto = $this->getAddStudentToCourseDto();
        $createCourseStudent = $this->courseService->addFacultyStudentCourse($studentCourseDto, $this->organizationId, $this->coordinatorId);
    }
    
    public function testFindOneByExternalIdOrg()
    {
        $this->initializeRbac();
        
        $courseInstance = $this->courseService->findOneByExternalIdOrg(7545, $this->organizationId, false);
        
        $this->assertEquals('IIT', $courseInstance->getCollegeCode());
        $this->assertEquals('Computer Networks', $courseInstance->getCourseName());
        $this->assertEquals('0087', $courseInstance->getCourseNumber());
        $this->assertEquals('SEC A', $courseInstance->getCourseSectionId());
        $this->assertEquals('12.00', $courseInstance->getCreditHours());
        $this->assertEquals('12', $courseInstance->getDaysTimes());
        $this->assertEquals($this->organizationId, $courseInstance->getOrganization()->getId());
        
    }
    
    public function testFlush(){
        
        $courseInstance = $this->courseService->flush();
    } 
    
    public function testClear(){
    
        $courseInstance = $this->courseService->clear();
    }
}
