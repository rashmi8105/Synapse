<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\UserDTO;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\RoleDto;
use Synapse\MultiCampusBundle\EntityDto\PromoteUserDto;

class TierUserServiceTest extends \Codeception\TestCase\Test
{

    private $tierService;

    private $orgId = 1;

    private $coordinatorId = 242;
    
    private $invalidOrgId = -2;
	
	private $primaryTierId = 128;
	
	private $secondaryTierId = 129;
	
	private $invalidPrimaryTierId = -120;
	
	private $role = 1;
	
	private $invalidRole = -1;
	
	private $campusId = 130;
	
	private $tierUser = 240;
	
	private $invalidUser = -100;
	
	public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->userService = $container->get('users_service');
		$this->tierService = $container->get('tier_service');
		$this->tierUserService = $container->get('tier_users_service');
    }
	
	public function testCreatePrimaryTierUser()
	{
		$tierDto = new TierDto;
		$tierDto->setPrimaryTierId('CMPS1');
		$tierDto->setPrimaryTierName('Tier1');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('primary');
		$tierDto->setLangid(1);
		$tierDetails = $this->tierService->createTier($tierDto);		
		$userDTO = new UserDTO();
		$userDTO->setTierId($tierDetails->getId());
		$userDTO->setTierLevel('primary');
		$userDTO->setFacultyId('ABC123');
		$userDTO->setFirstname('Robert');
		$userDTO->setLastname('Bob');
		$userDTO->setEmail('robertbob@mailinator.com');
		$userDTO->setTitle('tier user');
		$userDTO->setIsmobile('No');
		$userDTO->setPhone('94778545');
		$primaryTierUser = $this->userService->createPrimaryTierUser($userDTO);
		$this->assertEquals ( 'primary', $primaryTierUser->getTierLevel());
		$this->assertEquals ( 'Robert', $primaryTierUser->getFirstname());
		$this->assertEquals ( 'Bob', $primaryTierUser->getLastname());
		$this->assertEquals ( 'tier user', $primaryTierUser->getTitle());
		$this->assertEquals ( 'robertbob@mailinator.com', $primaryTierUser->getEmail());		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testCreatePrimaryTierwithInvalidTierId()
	{
		$userDTO = new UserDTO();
		$userDTO->setTierId($this->invalidUser);
		$userDTO->setTierLevel('primary');
		$userDTO->setFacultyId('ABC123');
		$userDTO->setFirstname('Robert');
		$userDTO->setLastname('Bob');
		$userDTO->setEmail('robertbob@mailinator.com');
		$userDTO->setTitle('tier user');
		$userDTO->setIsmobile('No');
		$userDTO->setPhone('94778545');
		$primaryTierUser = $this->userService->createPrimaryTierUser($userDTO);
		$this->assertSame('{"errors": ["Primary Tier Not Found."],
			"data": [],
			"sideLoaded": []
			}',$primaryTierUser);
	}
	
	public function testCreateSecondaryTierUser()
	{
		$tierDto = new TierDto;
		$tierDto->setPrimaryTierId('CMPS1');
		$tierDto->setPrimaryTierName('Tier1');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('primary');
		$tierDto->setLangid(1);
		$tierDetails = $this->tierService->createTier($tierDto);		
		$tierDto = new TierDto;
		$tierDto->setSecondaryTierId('CMPS2');
		$tierDto->setSecondaryTierName('secondaryTier');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('secondary');
		$tierDto->setPrimaryTierId($tierDetails->getId());
		$tierDto->setLangid(1);
		$secondaryTier = $this->tierService->createTier($tierDto);				
		$userDTO = new UserDTO();
		$userDTO->setTierId($secondaryTier->getId());
		$userDTO->setTierLevel('secondary');
		$userDTO->setFacultyId('ABC123');
		$userDTO->setFirstname('Robert');
		$userDTO->setLastname('Bob');
		$userDTO->setEmail('robertbob@mailinator.com');
		$userDTO->setTitle('tier user');
		$userDTO->setIsmobile('No');
		$userDTO->setPhone('94778545');
		$secondaryTierUser = $this->userService->createSecondaryTierUser($userDTO);		
		$this->assertEquals ( 'secondary', $secondaryTierUser->getTierLevel());
		$this->assertEquals ( 'Robert', $secondaryTierUser->getFirstname());
		$this->assertEquals ( 'Bob', $secondaryTierUser->getLastname());
		$this->assertEquals ( 'tier user', $secondaryTierUser->getTitle());
		$this->assertEquals ( 'robertbob@mailinator.com', $secondaryTierUser->getEmail());		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testCreateSecondaryTierUserWithInvalidTierId()
	{					
		$userDTO = new UserDTO();
		$userDTO->setTierId($this->invalidUser);
		$userDTO->setTierLevel('secondary');
		$userDTO->setFacultyId('ABC123');
		$userDTO->setFirstname('Robert');
		$userDTO->setLastname('Bob');
		$userDTO->setEmail('robertbob@mailinator.com');
		$userDTO->setTitle('tier user');
		$userDTO->setIsmobile('No');
		$userDTO->setPhone('94778545');
		$secondaryTierUser = $this->userService->createSecondaryTierUser($userDTO);				
		$this->assertSame('{"errors": ["Secondary Tier Not Found."],
			"data": [],
			"sideLoaded": []
			}',$secondaryTierUser);	
	}	
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testListPrimaryTierUsersWithInvalidTierId()
	{			
		$tierUsers = $this->tierUserService->listExistingUsers($this->invalidPrimaryTierId, 'primary');
		$this->assertSame('{"errors": ["Primary Tier Not Found."],
			"data": [],
			"sideLoaded": []
			}',$tierUsers);	
	}
	
	public function testChangeCoordinator()
	{	
		$roleDto = new RoleDto();
		$roleDto->setCampusId($this->campusId);
		$roleDto->setRoleId($this->role);
		$coordinatorRole = $this->tierUserService->updateCoordinatorRole($this->coordinatorId, $roleDto);		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testChangeCoordinatorwithInvalidRole()
	{	
		$roleDto = new RoleDto();
		$roleDto->setCampusId($this->campusId);
		$roleDto->setRoleId($this->invalidRole);
		$coordinatorRole = $this->tierUserService->updateCoordinatorRole($this->coordinatorId, $roleDto);
		$this->assertSame('{"errors": ["Role Not Found"],
			"data": [],
			"sideLoaded": []
			}',$tierUsers);	
	}
	
	public function testPromoteUsertoCoordinator()
	{
		$promoteDto = new PromoteUserDto();
		$promoteDto->setUserId($this->coordinatorId);
		$promoteDto->setTierId($this->primaryTierId);
		$promoteDto->setTierLevel('primary');		
		$this->tierUserService->promoteUserToTierUser($promoteDto);
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testPromoteUsertoCoordinatorWithInvalidUser()
	{
		$promoteDto = new PromoteUserDto();
		$promoteDto->setUserId($this->invalidUser);
		$promoteDto->setTierId($this->primaryTierId);
		$promoteDto->setTierLevel('primary');
		$promoteUser = $this->tierUserService->promoteUserToTierUser($promoteDto);
		$this->assertSame('{"errors": ["Person not found"],
			"data": [],
			"sideLoaded": []
			}',$tierUsers);
	}
	
	public function testGetRoleName()
	{
		$role = $this->tierUserService->getUserRole($this->coordinatorId, $this->campusId);
		$this->assertEquals ( 'Mapworks Admin', $role );		
	}
	
	public function testListPrimaryCoordinators()
	{
		$coordinators = $this->tierUserService->listPrimaryTierCoordinators($this->campusId);
		$this->assertInternalType ( 'array', $coordinators );
		$this->assertInternalType ( 'object', $coordinators[0] );
		$this->assertEquals ( 241, $coordinators[0]->getUserId() );				
		$this->assertEquals(129, $coordinators[0]->getCampusId());
		$this->assertEquals('John', $coordinators[0]->getFirstName());
		$this->assertEquals('Mathew', $coordinators[0]->getLastName());		
	}
	
	public function testListPrimaryTierUsers()
	{
		$tierUsers = $this->tierUserService->listTierUsers('primary', $this->primaryTierId);
		$this->assertInternalType('object', $tierUsers );
		$this->assertInternalType('array', $tierUsers->getUsers());		
		$this->assertEquals($this->tierUser, $tierUsers->getUsers()[0]->getUserId());
		$this->assertEquals("Robert", $tierUsers->getUsers()[0]->getFirstName());
		$this->assertEquals("Gruz", $tierUsers->getUsers()[0]->getLastName());
		$this->assertEquals("robert@mailinator.com", $tierUsers->getUsers()[0]->getEmail());
		$this->assertEquals("547895654", $tierUsers->getUsers()[0]->getPhone());		
	}
	
	public function testListSecondaryTierUsers()
	{
		$tierUsers = $this->tierUserService->listTierUsers('secondary', $this->secondaryTierId);		
		$this->assertInternalType('object', $tierUsers );
		$this->assertInternalType('array', $tierUsers->getUsers());		
		$this->assertEquals(241, $tierUsers->getUsers()[0]->getUserId());
		$this->assertEquals("John", $tierUsers->getUsers()[0]->getFirstName());
		$this->assertEquals("Mathew", $tierUsers->getUsers()[0]->getLastName());
		$this->assertEquals("mathew@mailinator.com", $tierUsers->getUsers()[0]->getEmail());
		$this->assertEquals("8874521036", $tierUsers->getUsers()[0]->getPhone());		
		$this->assertEquals(129, $tierUsers->getSecondaryTierId());	
		$this->assertEquals(128, $tierUsers->getPrimaryTierId());	
	}
	
	public function testDeletePrimaryTierUser()
	{
		$tierUsers = $this->tierUserService->deleteTierUser($this->tierUser, 'primary', $this->primaryTierId);			
	}
	
	public function testDeleteSecondaryTierUser()
	{
		$tierUsers = $this->tierUserService->deleteTierUser(241, 'secondary', $this->secondaryTierId);			
	}
	
	public function testDeleteTierUsers()
	{
		$tierUsers = $this->tierUserService->deleteTierAndCampusUser('', 241, 'secondary', $this->secondaryTierId);
		
	}
		
	public function testListExistingUsers()
	{	
		$tierUsers = $this->tierUserService->listExistingUsers($this->secondaryTierId, 'secondary');
		$this->assertInternalType('array', $tierUsers );
		$this->assertInternalType('object', $tierUsers[0] );
		$this->assertEquals($this->tierUser, $tierUsers[0]->getUserId());	
		$this->assertEquals(128, $tierUsers[0]->getCampusId());	
		$this->assertEquals("Robert", $tierUsers[0]->getFirstName());	
		$this->assertEquals("Gruz", $tierUsers[0]->getLastName());	
		$this->assertEquals("robert@mailinator.com", $tierUsers[0]->getEmail());	
		$this->assertEquals("Faculty", $tierUsers[0]->getUserType());	
		$this->assertEquals("ROBERT", $tierUsers[0]->getExternalId());	
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public 	function testUpdateInvalidCoordinatorRole()
	{
		$roleDto = new RoleDto();
		$roleDto->setUserId($this->invalidUser);
		$roleDto->setCampusId($this->campusId);
		$roleDto->setRoleId(1);
		$roleChange = $this->tierUserService->updateCoordinatorRole($this->invalidUser, $roleDto);
		$this->assertSame('{"errors": ["Coordinator not found"],
			"data": [],
			"sideLoaded": []
			}',$roleChange);
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testPromoteInvalidTierUser()
	{
		$promoteUserDto = new PromoteUserDto();
		$promoteUserDto->setUserId($this->invalidUser);
		$promoteUserDto->setTierId($this->secondaryTierId);
		$promoteUserDto->setTierLevel('secondary');
		$promoteUser = $this->tierUserService->promoteUserToTierUser($promoteUserDto);
		$this->assertSame('{"errors": ["Person not found"],
			"data": [],
			"sideLoaded": []
			}',$promoteUser);
	}
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */	
	public function testPromoteInvalidCampusId()
	{
		$promoteUserDto = new PromoteUserDto();
		$promoteUserDto->setUserId($this->coordinatorId);
		$promoteUserDto->setTierId($this->invalidUser);
		$promoteUserDto->setTierLevel('secondary');
		$promoteUser = $this->tierUserService->promoteUserToTierUser($promoteUserDto);
		$this->assertSame('{"errors": ["Campus not found"],
			"data": [],
			"sideLoaded": []
			}',$promoteUser);
	}
	
	public function testListCampusCoordinators()
	{
		$campusUsers = $this->tierUserService->listCampusCoordinatorsAction($this->campusId);
		$this->assertInternalType('array', $campusUsers);
		$this->assertInternalType('object', $campusUsers[0]);
		$this->assertEquals(246, $campusUsers[0]->getUserId());	
		$this->assertEquals($this->campusId, $campusUsers[0]->getCampusId());	
		$this->assertEquals('Anil', $campusUsers[0]->getFirstName());	
		$this->assertEquals('Ram', $campusUsers[0]->getLastName());	
		$this->assertEquals('Technical coordinator', $campusUsers[0]->getRole());	
	}
	
	public function testListActiveCampuses()
	{
		$campuses = $this->tierUserService->listActiveCampusTiersforUser($this->tierUser);
		$this->assertInternalType('object', $campuses);
		$this->assertInternalType('array', $campuses->getTiles());
	}
}