<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\RestBundle\Entity\CoordinatorDTO;
use Synapse\RestBundle\Entity\MyAccountDto;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;

class PersonServiceTest extends \Codeception\TestCase\Test
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
	 * @var \Synapse\CoreBundle\Service\Impl\PersonService
	 */
	private $personService;
	
	private $role = 1;
	
	private $extenalId = 123456;
	
	private $organization = 1;
	
	private $personId = 1;
	
	private $invalidOrg = -20;
	
	private $filter = 'primary';
	
	private $filterCoordinator = 'primary coordinator';
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->personService = $this->container
		->get('person_service');
	}	
	
	public function createPersonDto()
	{
		$personDto = new PersonDTO();
		
		$personDto->setFirstName("Alan");
		$personDto->setLastName("Border");
		$personDto->setPrimaryEmail("alanborder@email.com");
		$personDto->setPrimaryMobile("9089785645");
		$personDto->setUsername("alanborder");
		$personDto->setCity("Los Angeles");
		$personDto->setOrganization($this->organization);
		
		return $personDto;
	}
	
	public function testCreatePerson()
	{
		$personDto = $this->createPersonDto();

		$person = $this->personService->createPerson($personDto);
		$this->assertInternalType('array',$person);
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Person', $person['person']);
		$this->assertEquals("Alan", $person['person']->getFirstName());
		$this->assertEquals("Border", $person['person']->getLastName());
		$this->assertEquals("alanborder", $person['person']->getUsername());
		$this->assertEquals($this->organization, $person['person']->getOrganization()->getId());
	}
	
	public function testCreatePersonRaw()
	{
		$personDto = $this->createPersonDto();

		$person = $this->personService->getPersonConv($personDto);
		$contact = $this->personService->getContactInfoConv($personDto);
	
		$personIns = $this->personService->createPersonRaw($person,$contact);
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Person', $personIns);
		$this->assertEquals("Alan", $personIns->getFirstName());
		$this->assertEquals("Border", $personIns->getLastName());
		$this->assertEquals("alanborder", $personIns->getUsername());
		$this->assertEquals($this->organization, $personIns->getOrganization()->getId());
	}
	
	public function testCreatePersonRawWithPersonId()
	{
		$personDto = $this->createPersonDto();
		$personDto->setPersonId($this->personId);
		$person = $this->personService->getPersonConv($personDto);
		$contact = $this->personService->getContactInfoConv($personDto);
	
		$personIns = $this->personService->createPersonRaw($person,$contact);
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Person', $personIns);
		$this->assertEquals("Alan", $personIns->getFirstName());
		$this->assertEquals("Border", $personIns->getLastName());
		$this->assertEquals("alanborder", $personIns->getUsername());
		$this->assertEquals($this->organization, $personIns->getOrganization()->getId());
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */

	public function testCreatePersonWithInvalidOrg()
	{
		$personDto = $this->createPersonDto();
		
		$personDto->setOrganization($this->invalidOrg);
		$person = $this->personService->createPerson($personDto);
	}
	
	
	public function testGetRoleById()
	{
		$role = $this->personService->getRoleById($this->role);
		$this->assertEquals($this->role, $role->getId());
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Role', $role,"Role Not Found");
	}
	
	
	public function testGetRoleByInvalidId()
	{
		$role = $this->personService->getRoleById(-1);
	
		$this->assertInstanceOf('Synapse\RestBundle\Entity\Error', $role);
	}
	
	
	public function testGetRoles()
	{
		$roles = $this->personService->getRoles($this->role);
		$this->assertInternalType('array', $roles);
		$this->assertArrayHasKey('roleid', $roles[0]);
		$this->assertArrayHasKey('coordinator_type', $roles[0]);
	}
	

	public function testGetRolesInvalid()
	{
		$roles = $this->personService->getRoles(-1);
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\Error', $roles);
	}
	
	
	public function testGetCoordinator()
	{
		$coordinator = $this->personService->getCoordinator($this->organization, $this->filterCoordinator);
		$this->assertInternalType('array', $coordinator);
		$this->assertArrayHasKey('coordinators', $coordinator);
		$this->assertArrayHasKey('id', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('firstname', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('lastname', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('welcome_email_sentDate', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('title', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('phone', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('email', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('roleid', $coordinator['coordinators'][0]);
		$this->assertArrayHasKey('role', $coordinator['coordinators'][0]);
		
	}
	
	
	public function testGetCoordinatorInvalid()
	{
		$coordinator = $this->personService->getCoordinator($this->invalidOrg, $this->filter);
		
		$this->assertEquals(null, $coordinator['last_updated']);
		$this->assertInternalType('array', $coordinator);
	}
	
	
	public function testGetCoordinatorById() 
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
		$coordinatorInst = $this->personService->getCoordinatorById($this->organization, $coordinator->getId());
		$this->assertInternalType('array', $coordinatorInst);	
		$this->assertEquals("John", $coordinatorInst[0]['firstname']);
		$this->assertEquals("George", $coordinatorInst[0]['lastname']);
		$this->assertEquals("john@email.com", $coordinatorInst[0]['email']);
		$this->assertEquals($this->role, $coordinatorInst[0]['roleid']);
		$this->assertArrayHasKey('id', $coordinatorInst[0]);
	}
	
	public function testgetCoordinatorByIdInvalid()
	{
		$coordinatorInst = $this->personService->getCoordinatorById(-1,-1);
		$this->assertInternalType('array', $coordinatorInst);
		$this->assertEmpty($coordinatorInst,"Coordinator not found");
	}
	
	public function testDeleteCoordinator()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
		
		$result = $this->personService->deleteCoordinator($this->organization, $coordinator->getId());
		$this->assertEmpty($result);
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testdeleteCoordinatorInvalid()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
	
		$result = $this->personService->deleteCoordinator(-1, $coordinator->getId());
	}

	public function testfindOneByExternalId()
	{
		$personDto = $this->createPersonDto();
		$personDto->setExternalId(2312311);
		$person = $this->personService->createPerson($personDto);
		
		$personIns = $this->personService->findOneByExternalId($person['person']->getExternalId());
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\Person', $personIns);
		$this->assertEquals($person['person']->getExternalId(), $personIns->getExternalId());
		$this->assertEquals($personDto->getFirstName(), $personIns->getFirstName());
		$this->assertEquals($personDto->getLastName(), $personIns->getLastName());
		$this->assertEquals($personDto->getUsername(), $personIns->getUsername());
	}
	
	public function testfindByExternalId()
	{
		$personDto = $this->createPersonDto();
		$personDto->setExternalId(2312311);
		$person = $this->personService->createPerson($personDto);
		
		$personIns = $this->personService->findByExternalId($person['person']->getExternalId());
		$this->assertInternalType('array', $personIns);
		$this->assertEquals($person['person']->getExternalId(), $personIns[0]->getExternalId());
		$this->assertEquals($personDto->getFirstName(), $personIns[0]->getFirstName());
		$this->assertEquals($personDto->getLastName(), $personIns[0]->getLastName());
		$this->assertEquals($personDto->getUsername(), $personIns[0]->getUsername());
		
	}
	
	public function testGetUsersByOrganization()
	{
		$users = $this->personService->getUsersByOrganization($this->organization, "staff");
		$this->assertInternalType('array', $users);
		$this->assertEquals(1, $users['organization_id']);
		$this->assertArrayHasKey('user_id', $users['users'][0]);
		$this->assertArrayHasKey('user_firstname', $users['users'][0]);
		$this->assertArrayHasKey('user_lastname', $users['users'][0]);
		$this->assertArrayHasKey('user_email', $users['users'][0]);
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetUsersByOrganizationInvalid()
	{
		$users = $this->personService->getUsersByOrganization($this->invalidOrg, "staff");
	}
	
	
	public function testGetPerson()
	{
		$personInst = $this->personService->getPerson($this->personId);
		$this->assertEquals($this->personId, $personInst['person_id']);
		$this->assertInternalType('array', $personInst);
		$this->assertArrayHasKey('person_type', $personInst);
		$this->assertArrayHasKey('person_first_name', $personInst);
		$this->assertArrayHasKey('person_last_name', $personInst);
		$this->assertArrayHasKey('organization_id', $personInst);
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetPersonInvalid()
	{
		$personInst = $this->personService->getPerson(-1);
	}
	
	
	public function testupdateMyAccount()
	{
        //$this->markTestSkipped("Errored");
		$myAccountDto = $this->createMyAccountDto();
		
		$result = $this->personService->updateMyAccount($myAccountDto);
		//var_dump($result);die;
		$this->assertInternalType('array', $result);
		$this->assertEquals($myAccountDto->getPersonId(), $result['person_id']);
		$this->assertArrayHasKey('person_first_name', $result);
		$this->assertArrayHasKey('person_last_name', $result);
		$this->assertArrayHasKey('person_email', $result);
		$this->assertArrayHasKey('organization_id', $result);
		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testupdateMyAccountInvalid()
	{
		$myAccountDto = $this->createMyAccountDto();
		$myAccountDto->setPersonId(-1);
		$result = $this->personService->updateMyAccount($myAccountDto);
	}
	
	
	public function testCreateCoordinator()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
		$this->assertNotEmpty($coordinator->getId());
		$this->assertInstanceOf('Synapse\RestBundle\Entity\CoordinatorDTO', $coordinator);
		$this->assertEquals($coordinatorDto->getFirstname(), $coordinator->getFirstname());
		$this->assertEquals($coordinatorDto->getLastname(), $coordinator->getLastname());
		$this->assertEquals($coordinatorDto->getOrganizationid(), $coordinator->getOrganizationid());
		$this->assertEquals($coordinatorDto->getRoleid(), $coordinator->getRoleid());
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateCoordinatorInvalid()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinatorDto->setOrganizationid($this->invalidOrg);
		$coordinatorDto->setRoleid(1);
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateCoordinatorInvalidRole()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinatorDto->setOrganizationid(1);
		$coordinatorDto->setRoleid(-1);
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
	}
	
	
	public function testUpdateCoordinator()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
		$coordinatorDto->setId($coordinator->getId());
		$coordinatorUpdate = $this->personService->updateCoordinator($coordinatorDto);
		$this->assertNotEmpty($coordinatorUpdate->getId());
		$this->assertEquals($coordinatorDto->getId(), $coordinatorUpdate->getId());
		$this->assertEquals($coordinatorDto->getFirstname(), $coordinatorUpdate->getFirstname());
		$this->assertEquals($coordinatorDto->getLastname(), $coordinatorUpdate->getLastname());
		$this->assertEquals($coordinatorDto->getOrganizationid(), $coordinatorUpdate->getOrganizationid());
		$this->assertEquals($coordinatorDto->getRoleid(), $coordinatorUpdate->getRoleid());
		
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testUpdateCoordinatorInvalid()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinatorDto->setId(-1);
		$coordinatorDto->setRoleid(1);
		
		$coordinator = $this->personService->updateCoordinator($coordinatorDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testUpdateCoordinatorInvalidRole()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinatorDto->setId(1);
		$coordinatorDto->setRoleid(-1);
	
		$coordinator = $this->personService->updateCoordinator($coordinatorDto);
	}
	
	public function testIsEmailExstis()
	{
		$coordinatorDto = $this->createCoordinator();
		$coordinator = $this->personService->createCoordinator($coordinatorDto);
		$status = $this->personService->primaryEmailExists($coordinator->getEmail());
		$this->assertEquals(true, $status);
	}
	
	public function testIsEmailExstisInvalid()
	{
		$status = $this->personService->primaryEmailExists("invalid@email.com");
	
		$this->assertEquals(false, $status);
	}
	
	public function testFindPerson()
	{
		$personDto = $this->createPersonDto();
		$personArray = $this->personService->createPerson($personDto);

		$person = $this->personService->findPerson($personArray['person']->getId());
		$this->assertEquals("Alan", $person->getFirstName());
		$this->assertEquals("Border", $person->getLastName());
		$this->assertEquals("alanborder", $person->getUsername());
		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testFindPersonInvalid()
	{
		$person = $this->personService->findPerson(-1);
	}
	
	
	public function createCoordinator()
	{
		$coordinatorDto = new CoordinatorDTO();
		
		$coordinatorDto->setFirstname("John");
		$coordinatorDto->setLastname("George");
		$coordinatorDto->setOrganizationid($this->organization);
		$coordinatorDto->setEmail("john@email.com");
		$coordinatorDto->setId($this->personId);
		$coordinatorDto->setRoleid($this->role);
		return $coordinatorDto;
	}
	
	public function createMyAccountDto()
	{
		$myAccountDto = new MyAccountDto();
		
		$myAccountDto->setPersonId($this->personId);
		$myAccountDto->setPassword("ramesh@1974");
		$myAccountDto->setPersonMobile("9078563421");
		
		return $myAccountDto;
	}
	
	public function createPerson()
	{
		$person = new Person();
		$person->setFirstname('Alan');
		$person->setLastname('Border');
		$person->setUsername("alanborder");
		
		return $person;
	}
	
	
	public function createContact()
	{
		$contact = new ContactInfo();
		$contact->setPrimaryEmail('alanborder@email.com');
		$contact->setPrimaryMobile('9089785645');
		$contact->setCity('Los Angeles');
		
		return $contact;
	}
}
