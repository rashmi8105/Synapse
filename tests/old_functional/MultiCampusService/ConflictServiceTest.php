<?php
use Codeception\Util\Stub;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictDto;
use Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto;
use Synapse\MultiCampusBundle\EntityDto\CampusChangeRequestDto;
use Synapse\MultiCampusBundle\EntityDto\UserConflictsDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictsByCategoryDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictPersonDeatilsDto;

class ConflictServiceTest extends \Codeception\TestCase\Test
{

	private $campusService;
	
	private $primaryTierId = 128;
	
	private $secondaryTierId = 129;
	
	private $campusId = 130;
	
	private $sourceCampus = 131;
	
	private $destinationCampus = 130;
	
	private $requestedBy = 242;
	
	private $requestedFor = 243;

	private $changeRequestId = 9;
	
	private $conflictId = 1;
	
	private $autoConflictId = 2;
	
	private $invalidConflictId = -1;
	
	public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->userService = $container->get('users_service');		
		$this->conflictService = $container->get('user_conflicts_service');
		$this->campusService = $container->get('campus_service');
    }
	
	public function testListConflictsUsers()
	{
		$conflicts = $this->conflictService->listConflicts($this->sourceCampus, $this->destinationCampus, 'json');		
		$this->assertInternalType('array', $conflicts);
		$this->assertInternalType('array', $conflicts['user_conflicts'][0]);
		$this->assertEquals('students', $conflicts['user_conflicts'][0]['conflict_category']);
		$this->assertEquals(1, $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getConflictId());
		$this->assertEquals("Biju", $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getFirstname());
		$this->assertEquals("Mon", $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getLastname());
		$this->assertEquals("SOLO", $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getCampusId());
		$this->assertEquals("winss@mailinator.com", $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getEmail());
		$this->assertEquals($this->sourceCampus, $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getOrgId());
		$this->assertEquals(248, $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getPersonId());
		$this->assertEquals("WNS", $conflicts['user_conflicts'][0]['conflicts'][0]['conflict_records'][0]->getExternalId());
	}
	
	public function testListConflicts()
	{
		$conflicts = $this->conflictService->listConflicts('', '', 'json');
		$this->assertInternalType('object', $conflicts);
		$this->assertInternalType('array', $conflicts->getConflicts());
		$this->assertInternalType('object', $conflicts->getConflicts()[0]);		
		$this->assertEquals(1, $conflicts->getConflicts()[0]->getCountConflicts());
		$this->assertEquals("Solo Campus", $conflicts->getConflicts()[0]->getSourceOrg()[0]->getCampusName());
		$this->assertEquals($this->sourceCampus, $conflicts->getConflicts()[0]->getSourceOrg()[0]->getCampusId());
		$this->assertEquals("hierarchy", $conflicts->getConflicts()[0]->getDestinationOrg()[0]->getType());
		$this->assertEquals(128, $conflicts->getConflicts()[0]->getDestinationOrg()[0]->getPrimaryTierId());
		$this->assertEquals("Primary Tier", $conflicts->getConflicts()[0]->getDestinationOrg()[0]->getPrimaryTierName());
		$this->assertEquals(129, $conflicts->getConflicts()[0]->getDestinationOrg()[0]->getSecondaryTierId());
		$this->assertEquals("Secondary Tier", $conflicts->getConflicts()[0]->getDestinationOrg()[0]->getSecondaryTierName());
	}
	
	public function testListConflictsCSVMode()
	{
		$conflicts = $this->conflictService->listConflicts($this->sourceCampus, $this->destinationCampus, 'csv');		
	}
	
	public function testViewConflictDetails()
	{
		$conflicts = $this->conflictService->viewConflictUserDetails($this->conflictId, $this->autoConflictId);
		$this->assertInternalType('object', $conflicts);
		$this->assertEquals(1, $conflicts->getConflictId());
		$this->assertEquals("Biju", $conflicts->getFirstname());
		$this->assertEquals("Mon", $conflicts->getLastname());
		$this->assertEquals("SOLO", $conflicts->getCampusId());
		$this->assertEquals("winss@mailinator.com", $conflicts->getEmail());
		$this->assertEquals($this->sourceCampus, $conflicts->getSourceOrgId());
		$this->assertEquals($this->destinationCampus, $conflicts->getDestinationOrgId());
		$this->assertEquals("WNS", $conflicts->getExternalId());		
	}
	
	 /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testViewConflictDetailswithInvalidId()
	{
		$conflicts = $this->conflictService->viewConflictUserDetails(-1, $this->conflictId);
		$this->assertSame('{"errors": ["No records in Conflicts"],
			"data": [],
			"sideLoaded": []
			}',$conflicts);
	}
	
	public function testResolveIndividualConflicts()
	{
		$conflictDto = new ConflictDto();
		$conflictDto->setConflictId($this->conflictId);
		$conflictDto->setResolveType('individual');
		$conflictDto->setExternalId('12AB');
		$conflictDto->setAutoResolveId($this->autoConflictId);
		$conflicts = $this->conflictService->updateResolveSingleConflict($conflictDto);
		$this->assertEquals(1, $conflicts->getConflictId());
		$this->assertEquals('individual', $conflicts->getResolveType());
		$this->assertEquals('12AB', $conflicts->getExternalId());
		$this->assertEquals($this->autoConflictId, $conflicts->getAutoResolveId());
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testResolveIndividualConflictsWithInvalidId()
	{
		$conflictDto = new ConflictDto();
		$conflictDto->setConflictId($this->invalidConflictId);
		$conflictDto->setResolveType('individual');
		$conflictDto->setExternalId('12AB');
		$conflictDto->setAutoResolveId($this->autoConflictId);
		$conflicts = $this->conflictService->updateResolveSingleConflict($conflictDto);
		$this->assertSame('{"errors": ["No Conflict Records Found"],
			"data": [],
			"sideLoaded": []
			}',$conflicts);
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testResolveIndividualConflictsWithInvalidExternalId()
	{
		$conflictDto = new ConflictDto();
		$conflictDto->setConflictId($this->conflictId);
		$conflictDto->setResolveType('individual');
		$conflictDto->setExternalId('WNS');
		$conflictDto->setAutoResolveId($this->autoConflictId);
		$conflicts = $this->conflictService->updateResolveSingleConflict($conflictDto);
		$this->assertSame('{"errors": ["User already exists in the system"],
			"data": [],
			"sideLoaded": []
			}',$conflicts);
	}
	
	
	public function testResolveBulkConflict()
	{
		$conflictDto = new ConflictDto();
		$conflictDto->setResolveType('saveForLater');
		$userConflicts = new UserConflictsDto();
		$userConflicts->setConflictCategory('students');		
		$conflictsCategory = new ConflictsByCategoryDto();
		$conflictPerson = new ConflictPersonDeatilsDto();
		$conflictPerson->setPersonId(247);
		$conflictPerson->setConflictId(1);
		$conflictPerson->setIsMaster(true);
		$conflictPerson->setIsHome(false);
		$conflictPerson->setMulticampusUser(true);
		$conflictPerson->setOrgId(130);
		$persons[] = $conflictPerson;
		$conflictPerson = new ConflictPersonDeatilsDto();
		$conflictPerson->setPersonId(248);
		$conflictPerson->setConflictId(2);
		$conflictPerson->setIsMaster(false);
		$conflictPerson->setIsHome(true);
		$conflictPerson->setMulticampusUser(false);
		$conflictPerson->setOrgId(131);
		$persons[] = $conflictPerson;	
		$conflictsCategory->setConflictRecords($persons);		
		$userConflicts->setConflicts($conflictsCategory);
		$users[] = $userConflicts;
		$conflictDto->setUserConflicts($users);
		$conflicts = $this->conflictService->updateResolveSingleConflict($conflictDto);		
	}
		
}