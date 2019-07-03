<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\ReferralsDTO;
use Synapse\RestBundle\Entity\ShareOptionsDto;
class StudentViewServiceTest extends \Codeception\TestCase\Test
{
    private $personStudentId = 6;
    private $invalidPersonStudent = -1;
    private $invalidStudent = 3;
    private $organizationId = 1;
    private $usertype = 'student';
    private $personFacultyId = 1;
    private $assignedTo = 2;
    private $comment = "massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea";
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
     * @var \Synapse\StudentViewBundle\Service\Impl\StudentCourseService
     */
    private $studentCourseService;
    
    /**
     *
     * @var \Synapse\StudentViewBundle\Service\Impl\StudentReferralService
     */
    private $studentReferralService;
    
    /**
     *
     * @var \Synapse\StudentViewBundle\Service\Impl\StudentCampusConnectionService
     */
    private $studentCampusConnectionService;
    
    private $referralService;
    
    public function _before() {
    	$this->container = $this->getModule ( 'Symfony2' )->container;
    	$this->studentCourseService = $this->container->get ( 'studentcourse_service' );
    	$this->studentReferralService = $this->container->get ( 'studentreferral_service' );
    	$this->studentCampusConnectionService = $this->container->get ( 'studentcampusconnection_service' );
    	$this->referralService = $this->container->get ( 'referral_service' );
    }
    
    public function testListCoursesForStudent()
    {
        $courses = $this->studentCourseService->listCoursesForStudent($this->personStudentId, $this->usertype, $this->organizationId);
        $this->assertInstanceOf('Synapse\StudentViewBundle\EntityDto\StudentCourseListDto', $courses);
        $this->assertEquals($this->personStudentId, $courses->getStudentId());
        if($courses->getCourseListTable()){
            foreach($courses->getCourseListTable() as $courseList){
                $this->assertInstanceOf('Synapse\StudentViewBundle\EntityDto\StudentCoursesArrayDto', $courseList);
                $this->assertNotEmpty($courseList->getYear());
                $this->assertNotEmpty($courseList->getTerm());
                $this->assertNotEmpty($courseList->getCampusName());
                if($courseList->getCourses()){
                    foreach($courseList->getCourses() as $course){
                        $this->assertInstanceOf('Synapse\StudentViewBundle\EntityDto\StudentCourseDto', $course);
                        $this->assertNotEmpty($course->getCourseId());
                        $this->assertNotEmpty($course->getSectionId());
                        if($course->getFaculties()){
                            foreach($course->getFaculties() as $faculty){
                                $this->assertInstanceOf('Synapse\AcademicBundle\EntityDto\FacultyDetailsDto', $faculty);
                            }
                        }
                        
                    }
                }
            }
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testListCoursesForStudentInvalidPerson()
    {
        $courses = $this->studentCourseService->listCoursesForStudent($this->invalidPersonStudent, $this->usertype, $this->organizationId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testListCoursesForStudentInvalidStudent()
    {
    	$courses = $this->studentCourseService->listCoursesForStudent($this->invalidStudent, $this->usertype, $this->organizationId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentOpenReferralsInvalidStudent()
    {
        $referrals = $this->studentReferralService->getStudentOpenReferrals($this->invalidPersonStudent);
    }
    
    public function testGetCampusConnectionsForStudent()
    {
        $campusConnections = $this->studentCampusConnectionService->getCampusConnectionsForStudent($this->personStudentId);
        $this->assertInternalType('array', $campusConnections);
        if(!empty($campusConnections['campus_connection_list'])){
            foreach($campusConnections['campus_connection_list'] as $conn){
                $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\StudentCampusConnectionsDto', $conn);
                $this->assertNotEmpty($conn->getOrganizationId());
                $this->assertNotEmpty($conn->getCampusId());
                $this->assertNotEmpty($conn->getCampusName());
                $this->assertInternalType('array', $conn->getCampusConnections());
                if(!empty($conn->getCampusConnections())){
                    foreach($conn->getCampusConnections() as $faculty){
                        $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\CampusConnectionsArrayDto', $faculty);
                        $this->assertNotEmpty($faculty->getPersonId());
                        $this->assertNotEmpty($faculty->getPersonFirstname());
                    }
                }
            }
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetCampusConnectionsForInvalidStudent()
    {
        $campusConnections = $this->studentCampusConnectionService->getCampusConnectionsForStudent($this->invalidPersonStudent);
    }
    
    public function testGetStudentOpenReferrals()
    {
        $this->initializeRbac();
        $referralDto = $this->createReferralsDto();
        $referrals = $this->referralService->createReferral($referralDto, true);
        
        $referralsList = $this->studentReferralService->getStudentOpenReferrals($this->personStudentId);
        if(count($referralsList) > 0){
            foreach($referralsList['referrals'] as $referral){
                $this->assertNotEmpty($referral->getReferralId());
                $this->assertNotEmpty($referral->getOrganizationId());
                $this->assertNotEmpty($referral->getCampusName());
                $this->assertNotEmpty($referral->getReferralDate());
                $this->assertNotEmpty($referral->getAssignedTo());
                $this->assertNotEmpty($referral->getAssignedToEmail());
                $this->assertNotEmpty($referral->getReason());
            }
        }
    }
    
    public function testGetStudentOpenReferralsWithCentralCoordinator()
    {
        $this->initializeRbac();
        $referralDto = $this->createReferralsDto();
        $referralDto->setAssignedToUserId(0);
        $referrals = $this->referralService->createReferral($referralDto, true);
    
        $referralsList = $this->studentReferralService->getStudentOpenReferrals($this->personStudentId);
        if(count($referralsList) > 0){
            foreach($referralsList['referrals'] as $referral){
                $this->assertNotEmpty($referral->getReferralId());
                $this->assertNotEmpty($referral->getOrganizationId());
                $this->assertNotEmpty($referral->getCampusName());
                $this->assertNotEmpty($referral->getReferralDate());
                $this->assertNotEmpty($referral->getAssignedTo());
                $this->assertNotEmpty($referral->getAssignedToEmail());
                $this->assertNotEmpty($referral->getReason());
            }
        }
    }
    
    private function createReferralsDto(){
        
        $referralDto = new ReferralsDTO();
        $referralDto->setOrganizationId($this->organizationId);
        $referralDto->setPersonStudentId($this->personStudentId);
        $referralDto->setPersonStaffId($this->personFacultyId);
        $referralDto->setReasonCategorySubitemId(20);
        $referralDto->setAssignedToUserId(3);
        $referralDto->setInterestedParties([]);
        $referralDto->setComment($this->comment);
        $referralDto->setIssueDiscussedWithStudent(true);
        $referralDto->setHighPriorityConcern(false);
        $referralDto->setIssueRevealedToStudent(true);
        $referralDto->setStudentIndicatedToLeave(false);
        $referralDto->setNotifyStudent(true);
        
        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPublicShare(true);
        $shareOptionsDto->setPrivateShare(false);
        $shareOptionsDto->setTeamsShare(false);
        $shareOptions = array();
        $shareOptions[] = $shareOptionsDto;
        $referralDto->setShareOptions($shareOptions);
        return $referralDto;
    }
    
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personFacultyId);
    }
}