<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\ContactsDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
require_once 'tests/functional/FunctionalBaseTest.php';

class ContactsServiceTest extends FunctionalBaseTest
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
	 * @var \Synapse\CoreBundle\Service\Impl\ContactsService
	 */
	private $contactsService;
	
	private $org = 1;
	
	private $langId = 1;
	
	private $invalidOrg = -2;
	
	private $userId = 1;
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->contactsService = $this->createServiceWithRbacMock('contacts_service');
	}
	protected function initializeRbac()
	{
	    // Bootstrap Rbac Authorization.
	    /** @var Manager $rbacMan */
	    $rbacMan = $this->container->get('tinyrbac.manager');
	    $rbacMan->initializeForUser($this->userId);
	}
	
	private function createContactsDto()
	{
		$contactsDto = new ContactsDto();
		$contactsDto->setOrganizationId($this->org);        
        $contactsDto->setPersonStudentId(8);
        $contactsDto->setPersonStaffId(1);
        $contactsDto->setReasonCategorySubitemId(19);
        $contactsDto->setContactTypeId(3);
        $contactsDto->setDateOfContact(new \DateTime('now'));
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
	
	
	public function testCreateContact()
	{
	    $this->initializeRbac();
	    $contactsDto = $this->createContactsDto();
		
		$contacts = $this->contactsService->createContact($contactsDto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\ContactsDto', $contacts);
		$this->assertEquals($this->org, $contacts->getOrganizationId());
		$this->assertEquals($this->langId, $contacts->getLangId());
		$this->assertEquals(1, $contacts->getPersonStudentId());
		$this->assertEquals(1, $contacts->getPersonStaffId());
		$this->assertEquals(19, $contacts->getReasonCategorySubitemId());
		$this->assertEquals(3, $contacts->getContactTypeId());
		$this->assertEquals('Test comment', $contacts->getComment());
		$this->assertNotNull($contacts->getContactId());
		$this->assertEquals($this->shareOptions(), $contacts->getShareOptions());
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateContactInvalidOrg()
	{
		$contactsDto = $this->createContactsDto();
		$contactsDto->setOrganizationId(mt_rand());
		
		$contacts = $this->contactsService->createContact($contactsDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateContactInvalidPersonStudent()
	{
		$contactsDto = $this->createContactsDto();
		$contactsDto->setPersonStudentId(-1);
	
		$contacts = $this->contactsService->createContact($contactsDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateContactInvalidPersonStaff()
	{
		$contactsDto = $this->createContactsDto();
		$contactsDto->setPersonStaffId(-1);
	
		$contacts = $this->contactsService->createContact($contactsDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateContactInvalidReasonCategorySubitem()
	{
		$contactsDto = $this->createContactsDto();
		$contactsDto->setReasonCategorySubitemId(-1);
	
		$contacts = $this->contactsService->createContact($contactsDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateContactInvalidContactType()
	{
		$contactsDto = $this->createContactsDto();
		$contactsDto->setContactTypeId(-1);
	
		$contacts = $this->contactsService->createContact($contactsDto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateContactInvalidTeams()
	{
	    $this->initializeRbac();
		$contactsDto = $this->createContactsDto();
		$teams = array();
		$team = new TeamIdsDto();
		$team->setId(-1);
		$team->setIsTeamSelected(true);
		$teams[] = $team;
		$shareOption = $this->shareOptions();
		$shareOptions = $shareOption[0];
		$shareOptions->setTeamIds($teams);
		$shareOption[] = $shareOptions;
		$contactsDto->setShareOptions($shareOption);
	
		$contacts = $this->contactsService->createContact($contactsDto);
	}
	
	
	public function testGetContactTypes()
	{
		$contactTypes = $this->contactsService->getContactTypes();
		$this->assertInternalType('array', $contactTypes);
		$this->assertArrayHasKey('contact_type_groups', $contactTypes);
		$this->assertArrayHasKey('group_item_key', $contactTypes['contact_type_groups'][0]);
		$this->assertArrayHasKey('group_item_value', $contactTypes['contact_type_groups'][0]);
		$this->assertArrayHasKey('subitems', $contactTypes['contact_type_groups'][0]);
		$this->assertArrayHasKey('group_item_key', $contactTypes['contact_type_groups'][1]);
		$this->assertArrayHasKey('group_item_value', $contactTypes['contact_type_groups'][1]);
		$this->assertArrayHasKey('subitems', $contactTypes['contact_type_groups'][1]);
	}
}
