<?php
use Codeception\Util\Stub;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaffDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesDto;
use Synapse\AcademicUpdateBundle\EntityDto\GroupsDto;
use Synapse\AcademicUpdateBundle\EntityDto\ProfileDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaticListDto;

use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsDto;

use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsStudentDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto;

class AcademicUpdateCreateServiceTest extends \Codeception\TestCase\Test
{

    /**
     * {@inheritDoc}
     */
    private $academicUpdateCreateService;

    private $organizationId = 1;

    private $coordinatorId = 1;
    
    private $facultyId = 4;
    private $academicUpdateService;

    private $academicUpdateCounterService;
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->academicUpdateCreateService = $this->container->get('academicupdatecreate_service');
        $this->academicUpdateService = $this->container->get('academicupdate_service');
        $this->academicUpdateCounterService = $this->container->get('academicupdatecounter_service');
    }

    /**
     * 
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAcademicUpdateCreatedNoAU()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("15");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
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
    
    
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
         
    }
    
    public function testUpdateAcademicUpdate()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDtoAlltrue();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $requestId = $newAU->getId();
    
        $singleRequest = $this->academicUpdateService->getAcademicUpdateRequestById($this->organizationId, $requestId, 'coordinator', 'all', 'json', $this->coordinatorId);        
        $acIdOne = $singleRequest['data']['request_details'][0]['student_details'][0]['academic_update_id'];
        $acIdTwo = $singleRequest['data']['request_details'][0]['student_details'][1]['academic_update_id'];
    
         
        $dto = $this->getAcademicUpdateDto($requestId,$acIdOne,$acIdTwo);
    
    
        $this->academicUpdateCreateService->updateAcademicUpdate($dto, $this->facultyId);
    }

    
    public function testCreate()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }

    public function testCreateAllTrue()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }

    
    public function testAcademicUpdateCreatedSelectedStudent()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
        
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("15");
        
        $academicUpdateCreateDto->setStudents($studentsDto);
        
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
        
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
        
        
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }

    public function testAcademicUpdateCreatedSelectedGroup()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
        $academicUpdateCreateDto->setStaff($staffDto);
    
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll(false);
        $coursesDto->setSelectedCourseIds("");
    
        $academicUpdateCreateDto->setCourses($coursesDto);
    
        $groupsDto = new GroupsDto();
        $groupsDto->setIsAll(false);
        $groupsDto->setSelectedGroupIds("2");
    
        $academicUpdateCreateDto->setGroups($groupsDto);
		
		$staticListDto = new StaticListDto();
		$staticListDto->setIsAll(false);
		$staticListDto->setSelectedStaticIds("");
		
		$academicUpdateCreateDto->setStaticList($staticListDto);
    
    
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    
    public function testAcademicUpdateCreatedAllFaculty()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(true);
        $staffDto->setSelectedStaffIds("");
    
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
    
    
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    public function testAcademicUpdateCreatedSelectedCourse()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
        $academicUpdateCreateDto->setStaff($staffDto);
    
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll(false);
        $coursesDto->setSelectedCourseIds("3");
    
        $academicUpdateCreateDto->setCourses($coursesDto);
    
        $groupsDto = new GroupsDto();
        $groupsDto->setIsAll(false);
        $groupsDto->setSelectedGroupIds("");
    
        $academicUpdateCreateDto->setGroups($groupsDto);
		
		$staticListDto = new StaticListDto();
		$staticListDto->setIsAll(false);
		$staticListDto->setSelectedStaticIds("");
		
		$academicUpdateCreateDto->setStaticList($staticListDto);
    
    
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    public function testAcademicUpdateCreatedAllCourse()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
        $academicUpdateCreateDto->setStaff($staffDto);
    
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll(true);
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
    
    
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    public function testAcademicUpdateCreatedAllGroup()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
        $academicUpdateCreateDto->setStaff($staffDto);
    
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll(false);
        $coursesDto->setSelectedCourseIds("");
    
        $academicUpdateCreateDto->setCourses($coursesDto);
    
        $groupsDto = new GroupsDto();
        $groupsDto->setIsAll(true);
        $groupsDto->setSelectedGroupIds("");
    
        $academicUpdateCreateDto->setGroups($groupsDto);
		
		$staticListDto = new StaticListDto();
		$staticListDto->setIsAll(false);
		$staticListDto->setSelectedStaticIds("");
		
		$academicUpdateCreateDto->setStaticList($staticListDto);
    
    
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    public function testAcademicUpdateCreatedAllStudents()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(true);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
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
    
    
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAcademicUpdateCreatedInvalidDueDate()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("-1 month"));
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAcademicUpdateCreatedDuplicateName()
    {
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $unNamed = 'Scince Update' . uniqid();
        $academicUpdateCreateDto->setRequestName($unNamed);
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        
    }
    
    
    public function testAcademicUpdateCreatedSelectedISP()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
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
		
        $profileDto = new ProfileDto();
        $isps = [];
        $isps[0]['id'] = 1;
        $isps[0]['item_data_type'] = 'N'; 
        
        $isps[0]['is_single'] = true;
        
        $isps[0]['single_value'] = 20;
        $profileDto->setIsps($isps);
        
        $ebi = [];
        $ebi[0]['id'] = 1;
        $ebi[0]['item_data_type'] = 'N';
        
        $ebi[0]['is_single'] = true;
        
        $ebi[0]['single_value'] = 20;
        $profileDto->setEbi($ebi);
        
              
    
        $academicUpdateCreateDto->setProfileItems($profileDto);
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    public function testAcademicUpdateCreatedSelectedISPNumRange()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
    
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
    
        $academicUpdateCreateDto->setStudents($studentsDto);
    
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
    
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
		
        $profileDto = new ProfileDto();
        $isps = [];
        $isps[0]['id'] = 1;
        $isps[0]['item_data_type'] = 'N';
    
        $isps[0]['is_single'] = false;
    
        $isps[0]['min_digits'] = 12;
        $isps[0]['max_digits'] = 22;
    
        
        $profileDto->setIsps($isps);
        
    
        $academicUpdateCreateDto->setProfileItems($profileDto);
        $newAU = $this->academicUpdateCreateService->createRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $newAU->getId());
    }
    
    public function testCountWithProfile ()
    {
        $academicUpdateCounterService = $this->container->get('academicupdatecounter_service');
        
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setIsCreate(false);
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
        
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("");
        
        $academicUpdateCreateDto->setStudents($studentsDto);
        
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("");
        
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
		
        $profileDto = new ProfileDto();
        $isps = [];
        $isps[0]['id'] = 1;
        $isps[0]['item_data_type'] = 'N';
        
        $isps[0]['is_single'] = false;
        
        $isps[0]['min_digits'] = 12;
        $isps[0]['max_digits'] = 22;
        
        
        $profileDto->setIsps($isps);
        
        
        $academicUpdateCreateDto->setProfileItems($profileDto);
        $count = $this->academicUpdateCounterService->getUpdateCountByRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $count);
    }
    public function createAcademicUpdateDto()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
        
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(false);
        $studentsDto->setSelectedStudentIds("8");
        
        $academicUpdateCreateDto->setStudents($studentsDto);
        
        $staffDto = new StaffDto();
        $staffDto->setIsAll(false);
        $staffDto->setSelectedStaffIds("4");
        
        $academicUpdateCreateDto->setStaff($staffDto);
        
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll(false);
        $coursesDto->setSelectedCourseIds("3");
        
        $academicUpdateCreateDto->setCourses($coursesDto);
        
        $groupsDto = new GroupsDto();
        $groupsDto->setIsAll(false);
        $groupsDto->setSelectedGroupIds("2");
        
        $academicUpdateCreateDto->setGroups($groupsDto);
		
		$staticListDto = new StaticListDto();
		$staticListDto->setIsAll(false);
		$staticListDto->setSelectedStaticIds("");
		
		$academicUpdateCreateDto->setStaticList($staticListDto);
        
        return $academicUpdateCreateDto;
    }
    
    public function testCountAllData()
    {
        
        $academicUpdateCreateDto = $this->createAcademicUpdateDto();
        $academicUpdateCreateDto->setIsCreate(false);
        $count = $this->academicUpdateCounterService->getUpdateCountByRequest($academicUpdateCreateDto, $this->coordinatorId);
        $this->assertGreaterThan(0, $count);
    }

    public function createAcademicUpdateDtoAlltrue()
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $academicUpdateCreateDto->setOrganizationId($this->organizationId);
        $academicUpdateCreateDto->setIsCreate(true);
        $academicUpdateCreateDto->setRequestName('Scince Update' . uniqid());
        $academicUpdateCreateDto->setRequestDescription("Academic Update Desc");
        $academicUpdateCreateDto->setRequestDueDate(new \DateTime("+ 3 month"));
        
        $academicUpdateCreateDto->setRequestEmailSubject("science-update email subject");
        $academicUpdateCreateDto->setRequestEmailOptionalMessage("optional-message for science-update");
        
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll(true);
        $studentsDto->setSelectedStudentIds("");
        
        $academicUpdateCreateDto->setStudents($studentsDto);
        
        $staffDto = new StaffDto();
        $staffDto->setIsAll(true);
        $staffDto->setSelectedStaffIds("");
        
        $academicUpdateCreateDto->setStaff($staffDto);
        
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll(true);
        $coursesDto->setSelectedCourseIds("");
        
        $academicUpdateCreateDto->setCourses($coursesDto);
        
        $groupsDto = new GroupsDto();
        $groupsDto->setIsAll(true);
        $groupsDto->setSelectedGroupIds("");
        
        $academicUpdateCreateDto->setGroups($groupsDto);
		
		$staticListDto = new StaticListDto();
		$staticListDto->setIsAll(false);
		$staticListDto->setSelectedStaticIds("");
		
		$academicUpdateCreateDto->setStaticList($staticListDto);
        
        return $academicUpdateCreateDto;
    }
    
    
    public function getAcademicUpdateDto($requestId,$acIdOne,$acIdTwo)
    {
    
        $academicUpdateDto = new AcademicUpdateDto();
        $academicUpdateDto->setRequestId($requestId);
        $details = new AcademicUpdateDetailsDto ();
         
        $studentDetails = new AcademicUpdateDetailsStudentDto ();
        $studentDetails->setAcademicUpdateId($acIdOne);
        $studentDetails->setStudentId(6);
        $studentDetails->setStudentAbsences(2);
        $studentDetails->setStudentAbsences(2);
        $studentDetails->setStudentSend(true);
        $details->setStudentDetails([$studentDetails]);
        $academicUpdateDto->setRequestDetails([$details]);
        return $academicUpdateDto;
    
    }
}