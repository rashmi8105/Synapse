<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\UserDTO;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\CoreBundle\Util\Constants\UsersConstant;

class UserServiceTest extends \Codeception\TestCase\Test
{

    private $person = 1;

    private $campusId = 1;

    private $firstName = 'myfirstname';

    private $lastName = 'mylastname';

    private $title = "Mr";

    private $email;

    private $externalid;

    private $phone = 9894862200;

    private $roleId = 1;

    private $isMobile = TRUE;

    private $userId;

    private $facultyId;

    private $fEmailId;

    private $fExternalId;

    private $studentId;

    private $sEmailId;

    private $sExternalId;
	
	private $coordinatorId = 1;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->usersService = $container->get('users_service');
    }
	
	 public function userDTO()
    {	
		$this->email = uniqid('test_', true) . '@gmail.com';
        $this->externalid = uniqid('EXT_', true);
              
        $userDTO = new UserDTO();
        $userDTO->setFirstname($this->firstName);
        $userDTO->setLastname($this->lastName);
        $userDTO->setEmail($this->email);
        $userDTO->setTitle($this->title);
        $userDTO->setCampusId($this->campusId);
        $userDTO->setExternalid($this->externalid);
        $userDTO->setIsmobile($this->isMobile);
        $userDTO->setPhone($this->phone);
        $userDTO->setUserType(UsersConstant::FILTER_COORDINATOR);
        $userDTO->setRoleid($this->roleId);
        return $userDTO;
    }
	
	public function testCreateOrgCoordinator()
    {
        $userDTO = $this->userDTO();
        $userDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
        $this->userId = $userDetail->getId();
        $this->assertGreaterThan(0, $userDetail->getId());
        $this->assertEquals($this->firstName, $userDetail->getFirstname());
        $this->assertEquals($this->lastName, $userDetail->getLastname());
        $this->assertEquals($this->email, $userDetail->getEmail());
        $this->assertEquals($this->title, $userDetail->getTitle());
        $this->assertEquals($this->externalid, $userDetail->getExternalid());
        $this->assertEquals($this->campusId, $userDetail->getCampusId());
        $this->assertEquals($this->isMobile, $userDetail->getIsmobile());
        $this->assertEquals($this->phone, $userDetail->getPhone());
        $this->assertEquals(UsersConstant::FILTER_COORDINATOR, $userDetail->getUserType());
        $this->assertEquals($this->roleId, $userDetail->getRoleid());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateUserWithInValidCampusId()
    {
		$userDTO = $this->userDTO();
		$userDTO->setCampusId(mt_rand());
		$this->usersService->createUser($userDTO, $this->coordinatorId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateUserAssingInvaliedRoleId()
    {
        $userDTO = $this->userDTO();
		$userDTO->setRoleid(mt_rand());
		$this->usersService->createUser($userDTO, $this->coordinatorId);
    }
	
	public function testCreateOrgFaculty()
    {
        $userDTO = $this->userDTO();
		$this->fExternalId = uniqid('EXT_', true);
		$this->fEmailId = uniqid('test_', true) . '@gmail.com';
		$userDTO->setExternalid($this->fExternalId);
		$userDTO->setEmail($this->fEmailId);
		$userDTO->setUserType(UsersConstant::FILTER_FACULTY);
        $userDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
        $this->facultyId = $userDetail->getId();
		
        $this->assertGreaterThan(0, $userDetail->getId());
        $this->assertEquals($this->firstName, $userDetail->getFirstname());
        $this->assertEquals($this->lastName, $userDetail->getLastname());
        $this->assertEquals($this->fEmailId, $userDetail->getEmail());
        $this->assertEquals($this->title, $userDetail->getTitle());
        $this->assertEquals($this->fExternalId, $userDetail->getExternalid());
        $this->assertEquals($this->campusId, $userDetail->getCampusId());
        $this->assertEquals($this->isMobile, $userDetail->getIsmobile());
        $this->assertEquals($this->phone, $userDetail->getPhone());
        $this->assertEquals(UsersConstant::FILTER_FACULTY, $userDetail->getUserType());
    }
	public function testCreateOrgStudent()
    {
        $userDTO = $this->userDTO();
		$this->sExternalId = uniqid('EXT_', true);
		$this->sEmailId = uniqid('test_', true) . '@gmail.com';
		$userDTO->setExternalid($this->sExternalId);
		$userDTO->setEmail($this->sEmailId);
		$userDTO->setUserType(UsersConstant::FILTER_STUDENT);
        $userDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
		
        $this->studentId = $userDetail->getId();
        $this->assertGreaterThan(0, $this->studentId);
        $this->assertEquals($this->firstName, $userDetail->getFirstname());
        $this->assertEquals($this->lastName, $userDetail->getLastname());
        $this->assertEquals($this->sEmailId, $userDetail->getEmail());
        $this->assertEquals($this->title, $userDetail->getTitle());
        $this->assertEquals($this->sExternalId, $userDetail->getExternalid());
        $this->assertEquals($this->campusId, $userDetail->getCampusId());
        $this->assertEquals($this->isMobile, $userDetail->getIsmobile());
        $this->assertEquals($this->phone, $userDetail->getPhone());
        $this->assertEquals(UsersConstant::FILTER_STUDENT, $userDetail->getUserType());
    }
	public function testUpdateOrgCoordinator()
    {
        $userDTO = $this->userDTO();
		$createDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
        $this->userId = $createDetail->getId();
		//$userDTO->setId($this->userId);
		$this->externalId = uniqid('EXT_', true);
		$this->emailId = uniqid('test_', true) . '@gmail.com';
		$userDTO->setEmail($this->emailId);
		$userDTO->setExternalid($this->externalId);
        $userDetail = $this->usersService->updateUser($userDTO, $this->userId, $this->coordinatorId);      
        $this->assertGreaterThan(0, $userDetail->getId());
        $this->assertEquals($this->firstName, $userDetail->getFirstname());
        $this->assertEquals($this->lastName, $userDetail->getLastname());
        $this->assertEquals($this->emailId, $userDetail->getEmail());
        $this->assertEquals($this->title, $userDetail->getTitle());
        $this->assertEquals($this->externalId, $userDetail->getExternalid());
        $this->assertEquals($this->campusId, $userDetail->getCampusId());
        $this->assertEquals($this->isMobile, $userDetail->getIsmobile());
        $this->assertEquals($this->phone, $userDetail->getPhone());
        $this->assertEquals(UsersConstant::FILTER_COORDINATOR, $userDetail->getUserType());
    }
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateOrgCoordinatorInvalidId()
    {
		$userDTO = $this->userDTO();
		//$userDTO->setId(mt_rand());
        $userDetail = $this->usersService->updateUser($userDTO, mt_rand(), $this->coordinatorId);     
    }
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateOrgCoordinatorInvalidOrgId()
    {
		$userDTO = $this->userDTO();
		$createDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
        $this->userId = $createDetail->getId();
		//$userDTO->setId($this->userId);
		$userDTO->setCampusId(mt_rand());
        $userDetail = $this->usersService->updateUser($userDTO, $this->userId, $this->coordinatorId);
    }
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateCampusResourceInvalidRoleId()
    {
        $userDTO = $this->userDTO();
		$createDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
		$this->userId = $createDetail->getId();
		//$userDTO->setId($this->userId);
		$userDTO->setCampusId($this->campusId);
		$userDTO->setRoleid(mt_rand());
        $userDetail = $this->usersService->updateUser($userDTO, $this->userId, $this->coordinatorId);
    }
	public function testUpdateOrgFaculty()
    {
        $userDTO = $this->userDTO();
		$this->fExternalId = uniqid('EXT_', true);
		$this->fEmailId = uniqid('test_', true) . '@gmail.com';
		$userDTO->setExternalid($this->fExternalId);
		$userDTO->setEmail($this->fEmailId);
		$userDTO->setUserType(UsersConstant::FILTER_FACULTY);
		$createDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
        $this->userId = $createDetail->getId();
		//$userDTO->setId($this->userId);
		$this->fExternalId = uniqid('EXT_', true);
		$this->fEmailId = uniqid('test_', true) . '@gmail.com';
		$userDTO->setEmail($this->fEmailId);
		$userDTO->setExternalid($this->fExternalId);
        $userDetail = $this->usersService->updateUser($userDTO, $this->userId, $this->coordinatorId);      
        $this->assertGreaterThan(0, $userDetail->getId());
        $this->assertEquals($this->firstName, $userDetail->getFirstname());
        $this->assertEquals($this->lastName, $userDetail->getLastname());
        $this->assertEquals($this->fEmailId, $userDetail->getEmail());
        $this->assertEquals($this->title, $userDetail->getTitle());
        $this->assertEquals($this->fExternalId, $userDetail->getExternalid());
        $this->assertEquals($this->campusId, $userDetail->getCampusId());
        $this->assertEquals($this->isMobile, $userDetail->getIsmobile());
        $this->assertEquals($this->phone, $userDetail->getPhone());
        $this->assertEquals(UsersConstant::FILTER_FACULTY, $userDetail->getUserType());
    }
	public function testUpdateOrgStudent()
    {
        $userDTO = $this->userDTO();
		$this->sExternalId = uniqid('EXT_', true);
		$this->sEmailId = uniqid('test_', true) . '@gmail.com';
		$userDTO->setExternalid($this->sExternalId);
		$userDTO->setEmail($this->sEmailId);
		$userDTO->setUserType(UsersConstant::FILTER_STUDENT);
		$createDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
        $this->userId = $createDetail->getId();
		//$userDTO->setId($this->userId);
		$this->sExternalId = uniqid('EXT_', true);
		$this->sEmailId = uniqid('test_', true) . '@gmail.com';
		$userDTO->setEmail($this->sEmailId);
		$userDTO->setExternalid($this->sExternalId);
        $userDetail = $this->usersService->updateUser($userDTO, $this->userId, $this->coordinatorId);      
        $this->assertGreaterThan(0, $userDetail->getId());
        $this->assertEquals($this->firstName, $userDetail->getFirstname());
        $this->assertEquals($this->lastName, $userDetail->getLastname());
        $this->assertEquals($this->sEmailId, $userDetail->getEmail());
        $this->assertEquals($this->title, $userDetail->getTitle());
        $this->assertEquals($this->sExternalId, $userDetail->getExternalid());
        $this->assertEquals($this->campusId, $userDetail->getCampusId());
        $this->assertEquals($this->isMobile, $userDetail->getIsmobile());
        $this->assertEquals($this->phone, $userDetail->getPhone());
        $this->assertEquals(UsersConstant::FILTER_STUDENT, $userDetail->getUserType());
    }

    
	public function testGetOrgCoordinators()
    {
        $users = $this->usersService->getUsersList(NULL, $this->campusId, UsersConstant::FILTER_COORDINATOR, NULL, NULL, NULL);
        $this->assertInternalType('array', $users);
        $this->assertNotEmpty($users);
        $this->assertArrayHasKey("last_updated", $users);
        $this->assertArrayHasKey("coordinators", $users);
		$this->assertNotNull($users["coordinators"][0]['firstname']);
		$this->assertNotNull($users["coordinators"][0]['lastname']);
		$this->assertNotNull($users["coordinators"][0]['welcome_email_sentDate']);
		$this->assertNotNull($users["coordinators"][0]['title']);
		$this->assertNotNull($users["coordinators"][0]['email']);
		$this->assertNotNull($users["coordinators"][0]['externalid']);
		$this->assertNotNull($users["coordinators"][0]['phone']);
		$this->assertNotNull($users["coordinators"][0]['ismobile']);
		$this->assertNotNull($users["coordinators"][0]['role']);
		//$this->assertNotNull($users["coordinators"][0]['role_id']);
    }
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetOrgCoordinatorsInvalidFilter()
    {
        $getUsers = $this->usersService->getUsersList(NULL, $this->campusId, mt_rand(), NULL, NULL, NULL);
    }
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetOrgFacultyWithInvalidOrgId()
    {
        $getUsers = $this->usersService->getUsersList(NULL, mt_rand(),UsersConstant::FILTER_FACULTY, NULL, NULL, NULL);
    }
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetOrgStudentWithInvalidOrgId()
    {
        $getUsers = $this->usersService->getUsersList(NULL, mt_rand(),UsersConstant::FILTER_STUDENT, NULL, NULL, NULL);
    }
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetSingleCampusUserWithInvalidId()
    {
		$singleUser = $this->usersService->getUser(mt_rand(), $this->campusId);
    }
	
	public function testGetSingleCampusResourceDetails()
    {		
		$userDTO = $this->userDTO();
        $userDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
		$userResponse = $this->usersService->getUser($userDetail->getId(), $this->campusId);
		
        $this->assertInternalType('array', $userResponse);
        $this->assertNotEmpty($userResponse);
        $this->assertNotNull("id", $userResponse);
        $this->assertNotNull("firstname", $userResponse);
        $this->assertNotNull("lastname", $userResponse);
        $this->assertNotNull("title", $userResponse);
        $this->assertNotNull("email", $userResponse);
        $this->assertNotNull("externalid", $userResponse);
        $this->assertNotNull("phone", $userResponse);
        $this->assertNotNull("ismobile", $userResponse);
        $this->assertNotNull("user_type", $userResponse);
        $this->assertNotNull("welcome_email_sentDate", $userResponse);
    }
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteUserWithInvalidId()
    {
		$userDelResponse = $this->usersService->deleteUser(mt_rand(), $this->campusId, UsersConstant::FILTER_COORDINATOR);
        $this->assertEmpty($userDelResponse);
    }
	public function testDeleteUser()
    {
        $userDTO = $this->userDTO();
        $userDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
        $deleteUsers = $this->usersService->deleteUser($userDetail->getId(), $this->campusId, UsersConstant::FILTER_COORDINATOR);
        $this->assertEmpty($deleteUsers);
    }
	
	public function testPromoteToCoordinator()
    {
        $userDTO = $this->userDTO();
		$userDTO->setUserType(UsersConstant::FILTER_FACULTY);
        $userDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
		//$userDTO->setId($userDetail->getId());
		$userDTO->setRoleid($this->roleId);
		$userDTO->setCampusId($this->campusId);
		$promoResponse = $this->usersService->updateUser($userDTO, $userDetail->getId(), $this->coordinatorId);
		
		$this->assertGreaterThan(0, $promoResponse->getId());
        $this->assertEquals($userDetail->getId(), $promoResponse->getId());
		$this->assertEquals($userDetail->getCampusId(), $promoResponse->getCampusId());
        $this->assertEquals($userDetail->getRoleid(), $promoResponse->getRoleid());	
    }
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testPromoteToCoordinatorInValidId()
    {
		$userDTO = $this->userDTO();
		//$userDTO->setId(mt_rand());
		$userDTO->setCampusId($this->campusId);
		$userDTO->setRoleid($this->roleId);
		$promoResponse = $this->usersService->updateUser($userDTO, mt_rand(), $this->coordinatorId);
    }
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testPromoteToCoordinatorInValidRoleId()
    {
        $userDTO = $this->userDTO();
		$userDTO->setUserType(UsersConstant::FILTER_COORDINATOR);
        $userDetail = $this->usersService->createUser($userDTO, $this->coordinatorId);
		$userDTO->setCampusId($this->campusId);
		$userDTO->setRoleid(mt_rand());
		$promoResponse = $this->usersService->updateUser($userDTO, $userDetail->getId(), $this->coordinatorId);
    }    
	
	public function testGetOrgFacultyAndStudent()
    {
        //$this->markTestSkipped("Errored");
        $users = $this->usersService->getUsersList(NULL, $this->campusId, "userlist", NULL, NULL, NULL);
        $this->assertInternalType('array', $users);
        $this->assertNotEmpty($users);
		$user = $users[0];
		$this->assertObjectHasAttribute("userId", $user);
        $this->assertObjectHasAttribute("campusId", $user);
        $this->assertObjectHasAttribute("firstName", $user);
        $this->assertObjectHasAttribute("lastName", $user);
        $this->assertObjectHasAttribute("email", $user);
        $this->assertObjectHasAttribute("permissions", $user);
    }
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testGetOrgFacultyAndStudentWithInvalidOrgId()
    {
        $users = $this->usersService->getUsersList(NULL, mt_rand(), "userlist", NULL, NULL, NULL);
    }
}
