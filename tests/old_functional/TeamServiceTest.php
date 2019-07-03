<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\TeamsDto;

class TeamServiceTest extends \Codeception\TestCase\Test
{
	/**
	 * @var UnitTester
	 */
	protected $tester;
	
	/**
	 * @var Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 * @var \Synapse\CoreBundle\Service\TeamsService
	 */
	private $teamService;
	
	private $org = 1;
	
	private $invalidOrg = -200;
	
	private $person1= 1;
	
	private $person2= 3;
	
	private $teamId = 1;
	
	private $InvalidTeamId = -1; 
	
	
	
	/**
	* {@inheritDoc}
	*/

	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->teamService = $this->container
		->get('teams_service');
	}
		
	public function createTeam()
	{
		$teamsDto = new TeamsDto();
		
		$staff = array(
				array( "person_id" => $this->person1,"action" => "add","is_leader" => "0"),
				array( "person_id" => $this->person2,"action" => "add","is_leader" => "1")
		);
		
		$teamsDto->setTeamName(uniqid("Team",true));
		$teamsDto->setOrganization($this->org);
		$teamsDto->setStaff($staff);
		
		$teamsDto->setTeamDescription("Test the team");
		$teamsDto->setIsTeamLeader('Y');
		
		return $teamsDto;		
	}
	
	
	public function testCreateNewTeam()
	{
		$teamDto = $this->createTeam();
		$createTeam = $this->teamService->createNewTeam($teamDto);
		$this->assertInternalType('array', $createTeam,"Teams cannot be created");
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Teams', $createTeam['team']);
		$this->assertEquals($this->org, $createTeam['team']->getOrganization()->getId());
		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateNewTeamInvalidOrg()
	{
		$teamDto = $this->createTeam();
		$teamDto->setOrganization($this->invalidOrg);
		$createTeam = $this->teamService->createNewTeam($teamDto);
	}
	
	
	public function testUpdateTeams()
	{
		$teamsDto = $this->createTeam();
		$createTeam = $this->teamService->createNewTeam($teamsDto);

		$teamsDto->setTeamId($createTeam['team']->getId());
		$staff = array(
				array( "person_id" => $this->person1,"action" => "update","is_leader" => "1"),
				array( "person_id" => $this->person2,"action" => "delete","is_leader" => "0")
		);
		
		$teamsDto->setStaff($staff);
		$updateTeam = $this->teamService->updateTeams($teamsDto);
		$this->assertInternalType('array', $updateTeam,"Teams not found to update");
		$this->assertEquals($createTeam['team']->getId(), $updateTeam[1]['team']->getId());
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Teams', $updateTeam[1]['team']);
		
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testUpdateTeamsInvalid()
	{
		$teamsDto = $this->createTeam();
		$teamsDto->setTeamId($this->InvalidTeamId);
		$updateTeam = $this->teamService->updateTeams($teamsDto);
	}
	
	
	
	public function testUpdateTeamsAddMember()
	{
		$teamsDto = $this->createTeam();
		$createTeam = $this->teamService->createNewTeam($teamsDto);
	
		$teamsDto->setTeamId($createTeam['team']->getId());
		$staff = array(
				array( "person_id" => $this->person1,"action" => "update","is_leader" => "1"),
				array( "person_id" => $this->person2,"action" => "delete","is_leader" => "0"),
				array( "person_id" => 2,"action" => "update","is_leader" => "0")
		);
	
		$teamsDto->setStaff($staff);
		$updateTeam = $this->teamService->updateTeams($teamsDto);
		$this->assertInternalType('array', $updateTeam,"Teams not found to update");
		$this->assertEquals($createTeam['team']->getId(), $updateTeam[1]['team']->getId());
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Teams', $updateTeam[1]['team']);
	}
	
	
	public function testGetTeams()
	{	
		$getTeams = $this->teamService->getTeams($this->org);
		$this->assertInternalType('array', $getTeams,"Teams not found");
		$this->assertNotNull($getTeams[0]['team_id']);
		$this->assertNotNull($getTeams[0]['team_name']);
		$this->assertNotNull($getTeams[0]['team_no_leaders']);
		$this->assertNotNull($getTeams[0]['team_no_members']);
		$this->assertNotNull($getTeams[1]['team_id']);
		$this->assertNotNull($getTeams[1]['team_name']);
		$this->assertNotNull($getTeams[1]['team_no_leaders']);
		$this->assertNotNull($getTeams[1]['team_no_members']);
		
	}
	
	
	public function testGetTeamMembers()
	{
		$teamsDto = $this->createTeam();
		$createTeam = $this->teamService->createNewTeam($teamsDto);
		$getTeamMembers = $this->teamService->getTeamMembers($createTeam['team']->getId(), $this->org);
		$this->assertInternalType('array', $getTeamMembers,"TeamMembers cannot be found");
		$this->assertEquals($createTeam['team']->getId(), $getTeamMembers['team_id']);
		$this->assertEquals($createTeam['team']->getTeamName(), $getTeamMembers['team_name']);
		$this->assertArrayHasKey('staff', $getTeamMembers);
//		$this->assertEquals($createTeam['staff'][0]->getPerson()->getId(), $getTeamMembers['staff'][0]['person_id']);
//		$this->assertEquals($createTeam['staff'][1]->getPerson()->getId(), $getTeamMembers['staff'][1]['person_id']);
	}

	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetTeamMembersInvalid()
	{
		$getTeamMembers = $this->teamService->getTeamMembers($this->InvalidTeamId, $this->org);
	}
	public function testDeleteTeam()
	{
		$teamsDto = $this->createTeam();
		$createTeam = $this->teamService->createNewTeam($teamsDto);
		$deleteTeam = $this->teamService->deleteTeam($createTeam['team']->getId(),1);
		
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Teams', $deleteTeam,"Team cannot be deleted");
		$this->assertEquals($createTeam['team']->getId(), $deleteTeam->getId());
		$this->assertNotNull($deleteTeam->getDeletedAt());
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testFindTeamInvalid()
	{
		$team = $this->teamService->findTeam($this->InvalidTeamId);
	}
	
	
	public function testGetOrganizationTeamByUserId()
	{
		$teams = $this->teamService->getOrganizationTeamByUserId($this->org,$this->person1);
		$this->assertInternalType('array', $teams);
		$this->assertNotNull($teams[0]['team_id']);
		$this->assertNotNull($teams[0]['team_name']);
		$this->assertNotNull($teams[1]['team_id']);
		$this->assertNotNull($teams[1]['team_name']);
		$this->assertNotNull($teams[2]['team_id']);
		$this->assertNotNull($teams[2]['team_name']);
	}
	
	public function testGetTeamActivityDetailsCSV()
	{
	    $teams = $this->teamService->getMyTeamActivitiesDetail($this->person1, $this->person1, '1,2', 'all', 'month', '', '', '', '', '', 'csv');
	    $this->assertInternalType('string', $teams);
	}
}