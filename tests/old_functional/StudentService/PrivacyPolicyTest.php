<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;

use Synapse\RestBundle\Entity\StudentPolicyDto;

class PrivacyPolicyTest extends \Codeception\TestCase\Test{	
    
	private $studentId = 8;
    	
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
	
	public function testUpdatePrivacyPolicy()
	{		
		$studentPolicyDto = $this->createPolicyDto($this->studentId);
		$this->studentService->updatePolicy($studentPolicyDto);		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testUpdatePrivacyPolicyWithInvalidStudentId()
	{
		$studentPolicyDto = $this->createPolicyDto($this->invalidStudentId);
		$privacyPolicy = $this->studentService->updatePolicy($studentPolicyDto);		
		$this->assertSame('{"errors": ["Person Not Found."],
			"data": [],
			"sideLoaded": []
			}', $privacyPolicy);		
	}
	
	private function createPolicyDto($personId)
	{
		$studentPolicyDto = new StudentPolicyDto();
		$studentPolicyDto->setStudentId($personId);
		$studentPolicyDto->setOrganizationId($this->organizationId);
		$studentPolicyDto->setPrivacyPolicy('y');
		return $studentPolicyDto;
	}
}