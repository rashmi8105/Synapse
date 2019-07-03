<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\ReferralsDTO;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
require_once 'tests/functional/FunctionalBaseTest.php';

class StudentServiceTest extends FunctionalBaseTest{
    /**
     * @var UnitTester
     */
    protected $tester;
    
    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    /**
     * @var \Synapse\CoreBundle\Service\Impl\StudentService
     */
    private $studentService;
    
    /**
     * @var \Synapse\CoreBundle\Service\Impl\AppointmentsService
     */
    private $appointmentsService;
    
    
    private $personId = 1;
    private $personStudentId = 8;
    private $invalidStudentId = -10;
    private $personProxyId = 2;
    private $organizationId = 1;
    private $isFreeStanding = false;
    private $type = "S";
    private $timezone = "Pacific";
    private $studentId = 6;
    private $invalidOrg = -2;
    
    
    
    /**
     * {@inheritDoc}
     */
    public function _before()
    {
    	$this->container = $this->getModule('Symfony2')->kernel->getContainer();
    	$this->studentService = $this->container
    	->get('student_service');
    	$this->appointmentsService = $this->container
    	->get('appointments_service');
		$this->referralsService = $this->container
    	->get('referral_service');
		$this->studentServiceWithRback = $this->createServiceWithRbacMock('student_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }
    
    private function createAppointmentsDto(){
        $start= new \DateTime('now');
        $end= clone $start;
        $start->add(new \DateInterval('PT20M'));
        $end->add(new \DateInterval('PT40M'));
    	$appDto = new AppointmentsDto();
    	$appDto->setPersonId($this->personId);
    	$appDto->setPersonIdProxy(0);
    	$appDto->setOrganizationId($this->organizationId);
    	$appDto->setDetail("Reason details");
    	$appDto->setDetailId(1);
    	$appDto->setLocation("Stepojevac");
    	$appDto->setDescription("dictumst etiam faucibus cursus elementum");
    	$appDto->setOfficeHoursId(2);
    	$appDto->setIsFreeStanding($this->isFreeStanding);
    	$appDto->setType($this->type);
    	$appDto->setAttendees($this->createAttendees("create"));
    	$appDto->setSlotStart($start);
    	$appDto->setSlotEnd($end);
    	$appDto->setShareOptions($this->shareOptions());
    
    	return $appDto;
    }
    
    private function createAttendees($action = "create")
    {
    	$return = [];
    	$attDto = new AttendeesDto();
    	$attDto->setStudentId(2);
    	if($action == "create")
    	{
    		$attDto->setIsSelected(true);
    		$attDto->setIsAddedNew(true);
    	}
    	$return = $attDto;
    
    	return $return;
    }
    
    private function shareOptions(){
    	$return = array();
    	$sharaOption = new ShareOptionsDto();
    	$sharaOption->setPrivateShare(true);
    	$sharaOption->setPublicShare(true);
    	$sharaOption->setTeamsShare(true);
    	$sharaOption->setTeamIds($this->teams());
    
    	return array($sharaOption);
    }
    
    private function teams(){
    	$return = array();
    	$teams = array();
    	$team = new TeamIdsDto();
    	for($i=1; $i<=3; $i++){
    		$team->setId($i);
    		$team->setIsTeamSelected(true);
    		$teams[] = $team;
    	}
    	return $teams;
    }
    
    private function intrestedParties(){
    	$interested_parties = array();
    	$interested_parties[] = ["id" => $this->personId];    	
    	return $interested_parties;
    }
    
    public function testGetStudentsOpenAppointments()
    {
        //$this->markTestSkipped("Errored");
        $this->initializeRbac();
        $appointmentDto = $this->createAppointmentsDto();
        $appointment = $this->appointmentsService->create($appointmentDto);
        $studentOpenApp = $this->studentServiceWithRback->getStudentsOpenAppointments($this->personStudentId, $this->personId, $this->organizationId, $this->timezone);
		$this->assertInstanceOf("Synapse\RestBundle\Entity\StudentOpenAppResponseDto", $studentOpenApp);
        $this->assertEquals($this->personStudentId, $studentOpenApp->getPersonStudentId());
        $this->assertEquals($this->personId, $studentOpenApp->getPersonStaffId());
        $this->assertObjectHasAttribute("appointments", $studentOpenApp);
    }
    
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    
    public function testGetStudentsOpenAppointmentsInvalidStudent()
    {       
    	$studentOpenApp = $this->studentService->getStudentsOpenAppointments($this->invalidStudentId, $this->personId, $this->organizationId, $this->timezone);    	
		$this->assertSame('{"errors": ["Not a valid Student Id."],
			"data": [],
			"sideLoaded": []
			}', $studentOpenApp);
    }

	/**
	 * TODO: Commented for the testing of failing nightly
	 */
	public function testListOpenReferrals()
	{
        //$this->markTestSkipped("Errored");
		$referralsDTO = new ReferralsDTO();
		$referralsDTO->setPersonStaffId($this->personId); 
		$referralsDTO->setPersonStudentId($this->personStudentId);
		$referralsDTO->setOrganizationId($this->organizationId);
		$referralsDTO->setReasonCategorySubitemId(19);
		$referralsDTO->setShareOptions($this->shareOptions());
		
		$referralsDTO->setAssignedToUserId($this->personId);
		$referralsDTO->setInterestedParties($this->intrestedParties());
		$referralsDTO->setComment("Test Case");
		$referralsDTO->setIssueDiscussedWithStudent(true);
		$referralsDTO->setHighPriorityConcern(true);
		$referralsDTO->getStudentIndicatedToLeave(true);
		$referralsDTO->setNotifyStudent(false);		
		$this->referralsService->setUser(new \Synapse\CoreBundle\Entity\Person());
		$referrals = $this->referralsService->createReferral($referralsDTO);	
		$openReferrals = $this->studentService->listReferrals($this->personId,$this->personStudentId);
		$this->assertEquals($this->personStudentId, $openReferrals->getPersonStudentId());
        $this->assertEquals($this->personId, $openReferrals->getPersonStaffId());
        $this->assertObjectHasAttribute("referrals", $openReferrals);
        $this->assertNotEmpty($openReferrals);
		
	}
	/*@FIXME
	public function testListContactsCount()
	{
		$contacts = $this->studentService->studentContacts($this->personId, $this->personStudentId ,$this->organizationId);		
		$this->assertObjectHasAttribute("personStudentId", $contacts);
		$this->assertObjectHasAttribute("personStaffId", $contacts);
		$this->assertObjectHasAttribute("totalContacts", $contacts);		
		$this->assertEquals($this->personStudentId, $contacts->getPersonStudentId());
        $this->assertEquals($this->personId, $contacts->getPersonStaffId());
	}
	*/
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testGetStudentGroupsListInvalidStudent()
	{
	    $groups = $this->studentService->getStudentGroupsList($this->organizationId, $this->invalidStudentId);
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetStudentGroupsListInvalidOrg()
	{
		$groups = $this->studentService->getStudentGroupsList($this->invalidOrg, $this->studentId);
	}
}
