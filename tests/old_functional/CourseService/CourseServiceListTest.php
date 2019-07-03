<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\AcademicBundle\EntityDto\AddUserCourseDto;

class CourseServiceListTest extends \Codeception\TestCase\Test
{

    private $courseId = 1;

    private $facultyId = 1;

    private $personId = 1;

    private $organizationId = 1;

    private $filter = 'all';

    private $invalidCourseId = 0;

    private $invalidFacultyId = 0;

    private $subjectCode = "SEC0010087";

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->courseService = $this->container->get('course_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }

    public function testGetSingleCourse()
    {
        $this->initializeRbac();
        $course = $this->courseService->getCourseDetails('coordinator', 'json', $this->personId, $this->courseId, $this->organizationId);

        $this->assertInstanceOf('Synapse\AcademicBundle\EntityDto\SingleCourseDto', $course);
        $this->assertNotEmpty($course->getCourseName());
        $this->assertNotEmpty($course->getSectionNumber());
        $this->assertNotEmpty($course->getCourseId());
        $this->assertNotEmpty($course->getTotalStudents());
        $this->assertNotEmpty($course->getTotalFaculties());
        $this->assertEquals($this->subjectCode, $course->getSubjectCode());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetInvalidSingleCourse()
    {
        $this->initializeRbac();
        $course = $this->courseService->getCourseDetails('coordinator', 'json', $this->personId, $this->invalidCourseId, $this->organizationId);
        $this->assertSame('{"errors": ["Course not found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    public function testListFacultyCourse()
    {
        $this->initializeRbac();
        $searchParam['year'] = 'all';
        $searchParam['term'] = 'all';
        $searchParam['college'] = 'all';
        $searchParam['department'] = 'all';
        $searchParam['filter'] = '';
        $courseList = $this->courseService->listCoursesByRole($this->personId, 'faculty', $searchParam, 'json', '');

        $this->assertInstanceOf("Synapse\AcademicBundle\EntityDto\CoordinatorCourseDto", $courseList);
        $this->assertNotEmpty($courseList);
        $this->assertEquals(1, $courseList->getTotalCourse());
        $this->assertEquals('', $courseList->getTotalFaculty());
        $this->assertNotEmpty($courseList->getCourseListTable());
        
        foreach($courseList->getCourseListTable() as $course){
            
            $this->assertInstanceOf("Synapse\AcademicBundle\EntityDto\CourseListDto", $course);
            $this->assertNotEmpty($course->getYear());
            $this->assertNotEmpty($course->getTerm());
            $this->assertNotEmpty($course->getCollege());
            $this->assertNotEmpty($course->getCourse());
            $this->assertNotEmpty($course->getDepartment());
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testListFacultyWithInvalidUserTypeCourse()
    {
        $searchParam['year'] = 'all';
        $searchParam['term'] = 'all';
        $searchParam['college'] = 'all';
        $searchParam['department'] = 'all';
        $searchParam['filter'] = '';

        $courseList = $this->courseService->listCoursesByRole($this->personId, 'xyz', $searchParam, 'json', '');
        $this->assertSame('{"errors": ["Invalid User Type"],
			"data": [],
			"sideLoaded": []
			}', $courseList);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddFacultyToInvalidCourse()
    {
        $this->initializeRbac();
        $addUserCourseDto = new AddUserCourseDto();
        $addUserCourseDto->setType("faculty");
        $addUserCourseDto->setCourseId($this->invalidCourseId);
        $addUserCourseDto->setPersonId($this->personId);
        $course = $this->courseService->addFacultyStudentCourse($addUserCourseDto, $this->organizationId, $this->personId);
        $this->assertSame('{"errors": ["Course not found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddInvalidFacultyToCourse()
    {
        //$this->markTestSkipped("Failed");
        $this->initializeRbac();
        $addUserCourseDto = new AddUserCourseDto();
        $addUserCourseDto->setType("faculty");
        $addUserCourseDto->setCourseId($this->courseId);
        $addUserCourseDto->setPersonId($this->invalidFacultyId);
        $course = $this->courseService->addFacultyStudentCourse($addUserCourseDto, $this->organizationId, $this->personId);
        $this->assertSame('{"errors": ["Person Not Found."],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddFacultyToCourseInvalidUserType()
    {
        //$this->markTestSkipped("Failed");
        $this->initializeRbac();
        $addUserCourseDto = new AddUserCourseDto();
        $addUserCourseDto->setType("xyz");
        $addUserCourseDto->setCourseId($this->courseId);
        $addUserCourseDto->setPersonId($this->facultyId);
        $course = $this->courseService->addFacultyStudentCourse($addUserCourseDto, $this->organizationId, $this->personId);
        $this->assertSame('{"errors": ["Invalid filter type"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddStudentToInvalidCourse()
    {
        //$this->markTestSkipped("Failed");
        $this->initializeRbac();
        $addUserCourseDto = new AddUserCourseDto();
        $addUserCourseDto->setType("student");
        $addUserCourseDto->setCourseId($this->invalidCourseId);
        $addUserCourseDto->setPersonId($this->personId);
        $course = $this->courseService->addFacultyStudentCourse($addUserCourseDto, $this->organizationId, $this->personId);
        $this->assertSame('{"errors": ["Course not found"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddInvalidStudentToCourse()
    {
        //$this->markTestSkipped("Failed");
        $this->initializeRbac();
        $addUserCourseDto = new AddUserCourseDto();
        $addUserCourseDto->setType("student");
        $addUserCourseDto->setCourseId($this->courseId);
        $addUserCourseDto->setPersonId($this->invalidFacultyId);
        $course = $this->courseService->addFacultyStudentCourse($addUserCourseDto, $this->organizationId, $this->personId);
        $this->assertSame('{"errors": ["Person Not Found."],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAddStudentToCourseInvalidUserType()
    {
        //$this->markTestSkipped("Failed");
        $this->initializeRbac();
        $addUserCourseDto = new AddUserCourseDto();
        $addUserCourseDto->setType("xyz");
        $addUserCourseDto->setCourseId($this->courseId);
        $addUserCourseDto->setPersonId($this->facultyId);
        $course = $this->courseService->addFacultyStudentCourse($addUserCourseDto, $this->organizationId, $this->personId);
        $this->assertSame('{"errors": ["Invalid filter type"],
			"data": [],
			"sideLoaded": []
			}', $facultyCourse);
    }
}
