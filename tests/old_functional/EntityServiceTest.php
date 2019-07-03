<?php

use Codeception\Util\Stub;

class EntityServiceTest extends \Codeception\TestCase\Test
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
	 * @var \Synapse\CoreBundle\Service\Impl\EntityService
	 */
	private $entityService;
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->entityService = $this->container
		->get('entity_service');
	}
	
	
	public function testGetUserTypeByIdStaff()
	{	
		$type = 'staff';
		$userType = $this->entityService->getUserTypeById($type);		
		$this->assertEquals(3, $userType->getId());
	}
	public function testGetUserTypeByIdStudent()
	{
		$type = 'student';
		$userType = $this->entityService->getUserTypeById($type);
		$this->assertEquals(2, $userType->getId());
	}
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetUserTypeByIdInvalidType()
	{
		$type = 'invalid_student';
		$userType = $this->entityService->getUserTypeById($type);		
	}
}