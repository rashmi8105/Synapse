<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\StudentTalkingPointsDto;
use Synapse\RestBundle\Entity\TalkingPointsDto;
use Synapse\RestBundle\Entity\StudentProfileResponseDto;

class StudentServiceTalkingPointsTest extends \Codeception\TestCase\Test{
	
	/**
	* @var Symfony\Component\DependencyInjection\Container
	*/
	private $container;
	
	/**
	* @var \Synapse\CoreBundle\Service\Impl\StudentService
	*/
	private $studentService;

	private $studentId = 2;
	private $personId = 1;
	private $organizationId = 1;
	private $invalidStudentId = -1;

	
	/**
	* {@inheritDoc}
	*/
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->studentService = $this->container
		->get('student_service');
	}
	protected function initializeRbac()
	{
	    // Bootstrap Rbac Authorization.
	    /** @var Manager $rbacMan */
	    $rbacMan = $this->container->get('tinyrbac.manager');
	    $rbacMan->initializeForUser($this->studentId);
	}
	
	public function testgetTalkingPointsWithPrePopulatedData()
	{
		$this->markTestSkipped("Errored");
		//this is skipped due to the annotation is not able to import in OrgTalkingPointsRepository
	    $this->initializeRbac();
		// This functionality DB data are pre populated as service yet to be created
		$talkingPoints = $this->studentService->getTalkingPoints($this->studentId,$this->personId,$this->organizationId);
		$this->assertEquals($this->studentId, $talkingPoints->getPersonStudentId());
		$this->assertEquals($this->personId, $talkingPoints->getPersonStaffId());
		$this->assertEquals($this->organizationId, $talkingPoints->getOrganizationId());
		$this->assertEquals($talkingPoints->getTalkingPointsWeaknessCount(), count($talkingPoints->getWeakness()));
		$this->assertEquals($talkingPoints->getTalkingPointsStrengthsCount(), count($talkingPoints->getStrength()));
	}
	
	/**
	* 	@expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	*/
	public function testgetTalkingPointsWithInvalidStudentId()
	{
		// This functionality DB data are pre populated as service yet to be created
		$talkingPoints = $this->studentService->getTalkingPoints($this->invalidStudentId,$this->personId,$this->organizationId);
		$this->assertSame('{"errors": ["Not a valid Student Id"],
			"data": [],
			"sideLoaded": []
		}',$talkingPoints);
	}
}
