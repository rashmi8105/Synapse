<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\CoreBundle\Service\TeamsServiceInterface;
use Synapse\RestBundle\Entity\TeamsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Entity\TeamMembersDto;
use Synapse\RestBundle\Entity\TeamActivityCountDto;
use Synapse\RestBundle\Entity\TeamMembersActivitiesDto;
use Synapse\RestBundle\Entity\ContactsDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
require_once 'tests/functional/FunctionalBaseTest.php';

class MyTeamsTest extends FunctionalBaseTest
{
	private $container;
	private $teamService;
	private $orgService;
	//private $personService;
	private $contactsService;
	
	private $person1 = 1;
	private $person2 = 2;
	private $personFirstName = "Ramesh";
	private $personLastName = "Kumhar";
	private $organization = 1;
	private $langId = 1;
	private $teamMemberId = "1,2";
	private $personInvalid = -1;
	

	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->orgService = $this->container->get('org_service');
		//$this->personService = $this->container->get('person_service');
		$this->teamService = $this->container->get('teams_service');
		$this->contactsService = $this->container->get('contacts_service');
		$this->contactsServiceM = $this->createServiceWithRbacMock('contacts_service');
	}
	protected function initializeRbac()
	{
	    // Bootstrap Rbac Authorization.
	    /** @var Manager $rbacMan */
	    $rbacMan = $this->container->get('tinyrbac.manager');
	    $rb = $rbacMan->initializeForUser($this->person1);
	}
	private function createTeam()
	{
		$teamsDto = new TeamsDto();

		$staff = array(
			array( "person_id" => $this->person1,"action" => "add","is_leader" => "1"),
			array( "person_id" => $this->person2,"action" => "add","is_leader" => "0")
		);

		$teamsDto->setTeamName(uniqid("Team",true));
		$teamsDto->setOrganization($this->organization);
		$teamsDto->setStaff($staff);

		$teamsDto->setTeamDescription("Test the team");
		$teamsDto->setIsTeamLeader('Y');

		return $teamsDto;		
	}
	private function createContactsDto()
	{
	    $contactDate = new \DateTime('now');
	    $contactDate->format ( 'm/d/Y' );
		$contactsDto = new ContactsDto();
		$contactsDto->setOrganizationId($this->organization);
		$contactsDto->setLangId(1);
		$contactsDto->setContactId(0);
		$contactsDto->setPersonStudentId(1);
		$contactsDto->setPersonStaffId(1);
		$contactsDto->setReasonCategorySubitemId(19);
		$contactsDto->setActivityLogId(null);
		$contactsDto->setContactTypeId(3);
		$contactsDto->setDateOfContact($contactDate);
		$contactsDto->setComment('Test comment');
		$contactsDto->setIssueDiscussedWithStudent(true);
		$contactsDto->setIssueRevealedToStudent(true);
		$contactsDto->setHighPriorityConcern(true);
		$contactsDto->setStudentIndicatedToLeave(true);
		$contactsDto->setShareOptions($this->shareOptions());

		return $contactsDto;
	}
	private function shareOptions(){
		$return = array();
		$sharaOption = new ShareOptionsDto();
		$sharaOption->setPrivateShare(false);
		$sharaOption->setPublicShare(false);
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
	
	public function testGetMyTeams()
	{
		$teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$myTeam = $this->teamService->getMyTeams($this->person1);
		$this->assertEquals($this->person1, $myTeam->getPersonId());
		foreach($myTeam->getTeamIds() as $team){
			if($team->getId() == $newTeam['team']->getId())
			{
				$this->assertEquals($newTeam['team']->getId(), $team->getId());
				$this->assertEquals($newTeam['team']->getTeamName(), $team->getTeamName());
			}			
		}
		$this->assertInstanceOf('Synapse\RestBundle\Entity\TeamsDto', $myTeam);
	}
	
	/**
	* 	@expectedException Synapse\RestBundle\Exception\ValidationException
	*/	
	public function testGetMyTeamsByInvalidPerson()
	{
		$myTeam = $this->teamService->getMyTeams($this->personInvalid);
		$this->assertSame('{"errors": ["Person Not Found."],
			"data": [],
			"sideLoaded": []
		}',$myTeam);
	}
	
	public function testGetTeamMembers()
	{
		$teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$myTeamMembers = $this->teamService->getTeamMembersByPerson($this->person1,$newTeam['team']->getId());
		$this->assertEquals($this->person1, $myTeamMembers->getPersonId());
		$this->assertEquals($newTeam['team']->getId(), $myTeamMembers->getTeamId());
		foreach($myTeamMembers->getTeamMembers() as $team){
			if($team->getId() == $this->person1)
			{
				$this->assertEquals($this->person1, $team->getId());
			}			
		}
		$this->assertInstanceOf('Synapse\RestBundle\Entity\TeamsDto', $myTeamMembers);
	}

	/**
	* 	@expectedException Synapse\RestBundle\Exception\ValidationException
	*/	
	public function testGetTeamMembersByInvalidPerson()
	{
		$teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$myTeamMembers = $this->teamService->getMyTeams($this->personInvalid,$newTeam['team']->getId());
		$this->assertSame('{"errors": ["Person Not Found."],
			"data": [],
			"sideLoaded": []
		}',$myTeamMembers);
	}
	
	public function testGetMyTeamsRecentActivities()
	{
    //$this->markTestSkipped("Errored");
	    $this->initializeRbac();
	    $teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$contactsDto = $this->createContactsDto();
		$contacts = $this->contactsServiceM->createContact($contactsDto);
		$recentActivities = $this->teamService->getMyTeamsRecentActivities($this->person1, 'month');
		
		$this->assertEquals($this->person1, $recentActivities->getPersonId());
		
		foreach($recentActivities->getRecentActivities() as $recent){
			if($recent->getTeamId() == $newTeam['team']->getId())
			{
				$this->assertEquals($newTeam['team']->getId(),$recent->getTeamId());
			}			
		}
		$this->assertInstanceOf('Synapse\RestBundle\Entity\TeamsDto', $recentActivities);
	}
	
	public function testGetMyTeamsActivitiesInDetail()
	{
        //$this->markTestSkipped("Errored");
	    $this->initializeRbac();
		$teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$contactsDto = $this->createContactsDto();
		$contacts = $this->contactsService->createContact($contactsDto);

		$activities = $this->teamService->getMyTeamActivitiesDetail($this->person1,$newTeam['team']->getId(),$this->teamMemberId,'all','month','','','1','25','');
		$this->assertEquals($this->person1, $activities->getPersonId());
		$this->assertEquals($newTeam['team']->getId(), $activities->getTeamId());
		$this->assertEquals('All', $activities->getActivityType());
		foreach($activities->getTeamMembersActivities() as $activity){
			if($activity->getTeamMemberId() == $this->person1)
			{
				$this->assertEquals($this->person1,$activity->getTeamMemberId());
			}			
		}
		$this->assertInstanceOf('Synapse\RestBundle\Entity\TeamsDto', $activities);
	}

	public function testGetMyTeamsActivitiesInDetailWithCustomDate()
	{
      //$this->markTestSkipped("Errored");
		$teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$contactsDto = $this->createContactsDto();
		$contacts = $this->contactsService->createContact($contactsDto);
		
		$tomorrow_timestamp = strtotime("+ 1 day");
		$start_date = date("Y-m-d");
		$end_date = date("Y-m-d", $tomorrow_timestamp);
		$activities = $this->teamService->getMyTeamActivitiesDetail($this->person1,$newTeam['team']->getId(),$this->teamMemberId,'all','custom',$start_date,$end_date,'1','25','');
		$this->assertEquals($this->person1, $activities->getPersonId());
		$this->assertEquals($newTeam['team']->getId(), $activities->getTeamId());
		$this->assertEquals('All', $activities->getActivityType());
		foreach($activities->getTeamMembersActivities() as $activity){
			if($activity->getTeamMemberId() == $this->person1)
			{
				$this->assertEquals($this->person1,$activity->getTeamMemberId());
			}			
		}
		$this->assertInstanceOf('Synapse\RestBundle\Entity\TeamsDto', $activities);
	}
	
	/**
	* 	@expectedException Synapse\RestBundle\Exception\ValidationException
	*/	
	public function testGetMyTeamsActivitiesInDetailInvalidCustomDate()
	{
        //$this->markTestSkipped("Failed");
		$teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$contactsDto = $this->createContactsDto();
		$contacts = $this->contactsService->createContact($contactsDto);
		
		$tomorrow_timestamp = strtotime("+ 1 day");
		$start_date = date("Y-m-d", $tomorrow_timestamp);
		$end_date = date("Y-m-d");
		$activities = $this->teamService->getMyTeamActivitiesDetail($this->person1,$newTeam['team']->getId(),$this->teamMemberId,'all','custom',$start_date,$end_date,'1','25','');
		$this->assertSame('{"errors": ["Start date cannot be grater than end date"],
			"data": [],
			"sideLoaded": []
		}',$activities);
	}
	
	/**
	* 	@expectedException Synapse\RestBundle\Exception\ValidationException
	*/	
	public function testGetMyTeamsActivitiesInDetailInvalidPerson()
	{
        //$this->markTestSkipped("Failed");
		$teamDto = $this->createTeam();
		$newTeam = $this->teamService->createNewTeam($teamDto);
		$contactsDto = $this->createContactsDto();
		$contacts = $this->contactsService->createContact($contactsDto);
		
		$activities = $this->teamService->getMyTeamActivitiesDetail($this->personInvalid,$newTeam['team']->getId(),$this->teamMemberId,'all','month','','','1','25','');
		$this->assertSame('{"errors": ["Person Not Found."],
			"data": [],
			"sideLoaded": []
		}',$activities);
	}

}
