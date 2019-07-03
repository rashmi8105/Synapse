<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\CalendarSharingDto;
use Synapse\RestBundle\Entity\DelegatedUsersDto;
class DelegateUserTest extends \Codeception\TestCase\Test {
	
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
	 * @var \Synapse\CoreBundle\Service\Impl\AppointmentsService
	 */
	private $appointmentsService;
	
	/* @var \Synapse\CoreBundle\Service\Impl\AppointmentsProxyService */
	private $appointmentsProxyService;
	
	/**
	 * {@inheritDoc}
	 */
	
	private $token;
	private $organization = 1;
	private $inValidOrganization = -100;
	private $person = 1;
	private $invalidUserId = -1;
	private $delegateToPerson = 2;
	private $isSelectedTrue = true;
	private $isSelectedFalse = true;
	private $isDeletedTrue = true;
	private $isDeletedFalse = false;
	
	public function _before() {
		$this->container = $this->getModule ( 'Symfony2' )->container;
		$this->appointmentsService = $this->container->get ( 'appointments_service' );
		$this->appointmentsProxyService = $this->container->get ( 'appointmentsProxy_service' );
	}
	public function createDelegate($organization, $person, $delegatedToPersonId, $isSelected, $isDeleted){
		$calanderSharing = new CalendarSharingDto();
		$calanderSharing->setOrganizationId($organization);
		$calanderSharing->setPersonId($person);
		$calanderSharing->setDelegatedUsers($this->delegareUser($delegatedToPersonId,$isSelected,$isDeleted));
		return $calanderSharing;
	}
	private function delegareUser($delegatedToPersonId,$isSelected,$isDeleted){
		$return = array();
		$delegareUser = new DelegatedUsersDto();
		$delegareUser->setDelegatedToPersonId($delegatedToPersonId);
		$delegareUser->setIsSelected($isSelected);
		$delegareUser->setIsDeleted($isDeleted);
		return array($delegareUser);
	}
	
	public function testCreateDelegateUser()
	{
		$calendarSharingDto = $this->createDelegate($this->organization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedFalse);	
			
		$calenderDelegateUser = $this->appointmentsProxyService->createDelegateUser($calendarSharingDto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\CalendarSharingDto', $calenderDelegateUser);
		$this->assertEquals($calendarSharingDto->getOrganizationId(), $calenderDelegateUser->getOrganizationId());
		$this->assertEquals($calendarSharingDto->getPersonId(), $calenderDelegateUser->getPersonId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getCalendarSharingId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getSharedOn());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsSelected(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsSelected());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsDeleted(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsDeleted());
		
	}
	
	public function testUnSelectDelegateUser()
	{
		$calendarSharingDto = $this->createDelegate($this->organization, $this->person, $this->delegateToPerson, $this->isSelectedFalse, $this->isDeletedFalse);
			
		$calenderDelegateUser = $this->appointmentsProxyService->createDelegateUser($calendarSharingDto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\CalendarSharingDto', $calenderDelegateUser);
		$this->assertEquals($calendarSharingDto->getOrganizationId(), $calenderDelegateUser->getOrganizationId());
		$this->assertEquals($calendarSharingDto->getPersonId(), $calenderDelegateUser->getPersonId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getCalendarSharingId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getSharedOn());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsSelected(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsSelected());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsDeleted(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsDeleted());
	}
	public function testDeleteDelegateUser()
	{
		$calendarSharingDto = $this->createDelegate($this->organization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedTrue);
			
		$calenderDelegateUser = $this->appointmentsProxyService->createDelegateUser($calendarSharingDto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\CalendarSharingDto', $calenderDelegateUser);
		$this->assertEquals($calendarSharingDto->getOrganizationId(), $calenderDelegateUser->getOrganizationId());
		$this->assertEquals($calendarSharingDto->getPersonId(), $calenderDelegateUser->getPersonId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers());		
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getSharedOn());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsSelected(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsSelected());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsDeleted(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsDeleted());
	}
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	 
	public function testInvalidOrganizationDelegateUser()
	{
        //$this->markTestSkipped("Errored");
		$calendarSharingDto = $this->createDelegate($this->inValidOrganization, $this->person, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedTrue);
			
		$calenderDelegateUser = $this->appointmentsProxyService->createDelegateUser($calendarSharingDto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\CalendarSharingDto', $calenderDelegateUser);
		$this->assertEquals($calendarSharingDto->getOrganizationId(), $calenderDelegateUser->getOrganizationId());
		$this->assertEquals($calendarSharingDto->getPersonId(), $calenderDelegateUser->getPersonId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getCalendarSharingId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getSharedOn());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsSelected(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsSelected());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsDeleted(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsDeleted());
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testInvalidPersonDelegateUser()
	{
		$calendarSharingDto = $this->createDelegate($this->organization, $this->invalidUserId, $this->delegateToPerson, $this->isSelectedTrue, $this->isDeletedTrue);
			
		$calenderDelegateUser = $this->appointmentsProxyService->createDelegateUser($calendarSharingDto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\CalendarSharingDto', $calenderDelegateUser);
		$this->assertEquals($calendarSharingDto->getOrganizationId(), $calenderDelegateUser->getOrganizationId());
		$this->assertEquals($calendarSharingDto->getPersonId(), $calenderDelegateUser->getPersonId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getCalendarSharingId());
		$this->assertNotNull($calenderDelegateUser->getDelegatedUsers()[0]->getSharedOn());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsSelected(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsSelected());
		$this->assertEquals($calendarSharingDto->getDelegatedUsers()[0]->getIsDeleted(), $calenderDelegateUser->getDelegatedUsers()[0]->getIsDeleted());
	}	
	
	public function testGetManagedUser() {
		$result = $this->appointmentsProxyService->listManagedUsers ( $this->organization, $this->person );
		$this->assertInternalType ( 'array', $result );
		$this->assertEquals ( $this->organization, $result ['organization_id'] );
		$this->assertEquals ( $this->person, $result ['person_id_proxy'] );
		$this->assertNotEquals(0, $result ['organization_id']);
		$this->assertInternalType ( 'array', $result ['managed_users'] );		
	}
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetManagedUserInvalidOrganization() {
		$result = $this->appointmentsProxyService->listManagedUsers ( $this->inValidOrganization, $this->person );
		$this->assertInternalType ( 'array', $result );
		$this->assertEquals ( $this->inValidOrganization, $result ['organization_id'] );
		$this->assertEquals ( $this->person, $result ['person_id_proxy'] );
		$this->assertNotEquals(0, $result ['organization_id']);
		$this->assertInternalType ( 'array', $result ['managed_users'] );
	}
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetManagedUserInvalidUser() {
		$result = $this->appointmentsProxyService->listManagedUsers ( $this->organization, $this->invalidUserId);
		$this->assertInternalType ( 'array', $result );
		$this->assertEquals ( $this->organization, $result ['organization_id'] );
		$this->assertEquals ( $this->invalidUserId, $result ['person_id_proxy'] );
		$this->assertNotEquals(0, $result ['organization_id']);
		$this->assertInternalType ( 'array', $result ['managed_users'] );
	}
}
