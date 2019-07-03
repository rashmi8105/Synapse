<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\EmailDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
require_once 'tests/functional/FunctionalBaseTest.php';

class EmailActivityServiceTest extends FunctionalBaseTest
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
	 * @var \Synapse\CoreBundle\Service\Impl\EmailActivityService
	 */
	private $emailActivityService;
	
	private $organization = 1;
	
	private $invalidEmailId = -1;
	
	private $invalidOrganizationId = -100;
	
	private $userId = 1;
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->emailActivityService = $this->createServiceWithRbacMock('email_activity_service');
	}
	protected function initializeRbac()
	{
	    // Bootstrap Rbac Authorization.
	    /** @var Manager $rbacMan */
	    $rbacMan = $this->container->get('tinyrbac.manager');
	    $rbacMan->initializeForUser($this->userId);
	}
	
	private function shareOptions(){
		$return = array();
		$sharaOption = new ShareOptionsDto();
		$sharaOption->setPrivateShare(false);
		$sharaOption->setPublicShare(false);
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

	
	private function createEmailDto()
	{
	    $emailDto = new EmailDto();
	    $emailDto->setOrganizationId(1);
	    $emailDto->setPersonStudentId(1);
	    $emailDto->setPersonStaffId(1);
	    $emailDto->setPersonStaffName('Staff');
	    $emailDto->setReasonCategorySubitemId(1);	    
	    $emailDto->setReasonCategorySubitem('Test');
	    $emailDto->setEmailSubject('Test Email');
	    $emailDto->setEmailBody('Test Email');
	    $emailDto->setEmailBccList('one@mailinator.com');
	    $emailDto->setShareOptions($this->shareOptions());
	    
	    $emailDto->setEmailId(1);
	    return $emailDto;
	}
	
	public function testCreateEmail()
	{
        //$this->markTestSkipped("Errored");
	    $this->initializeRbac();
	    $emaildto = $this->createEmailDto();		
		$email = $this->emailActivityService->createEmail($emaildto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\EmailDto', $email);
		$this->assertEquals($emaildto->getReasonCategorySubitemId(), $email->getReasonCategorySubitemId());
		$this->assertEquals($emaildto->getReasonCategorySubitem(), $email->getReasonCategorySubitem());
		$this->assertEquals($emaildto->getPersonStaffId(), $email->getPersonStaffId());
		$this->assertEquals($emaildto->getPersonStudentId(), $email->getPersonStudentId());
		$this->assertEquals($emaildto->getShareOptions(), $email->getShareOptions());
		$this->assertEquals($emaildto->getEmailBody(), $email->getEmailBody());		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateEmailInvalidStaff()
	{
		$emaildto = $this->createEmailDto();
		$emaildto->setPersonStaffId(-1);		
		$Email = $this->emailActivityService->createEmail($emaildto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateEmailInvalidReasonCategorySubitem()
	{
		$emaildto = $this->createEmailDto();
		$emaildto->setReasonCategorySubitemId(-1);
	
		$Email = $this->emailActivityService->createEmail($emaildto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateEmailInvalidTeams()
	{
        //$this->markTestSkipped("Failed");
	    $this->initializeRbac();
		$emaildto = $this->createEmailDto();		
		$teams = array();
		$team = new TeamIdsDto();
		$team->setId(-1);
		$team->setIsTeamSelected(true);
		$teams[] = $team;
		$shareOption = $this->shareOptions();
		$shareOptions = $shareOption[0];
		$shareOptions->setTeamIds($teams);
		$shareOption[] = $shareOptions;		
		$emaildto->setShareOptions($shareOption);	
		$Email = $this->emailActivityService->createEmail($emaildto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	
	public function testViewEmailInvalidEmailId()
	{   
	    $this->initializeRbac();		
		$result = $this->emailActivityService->viewEmail(-1);
		$this->assertInternalType('array', $result,"email Not Found.");	
	}
	
	public function testViewEmail()
	{	
	    $this->initializeRbac();
		$emaildto = $this->createEmailDto();		
		$newEmail = $this->emailActivityService->createEmail($emaildto);				
		$result = $this->emailActivityService->viewEmail($newEmail->getEmailId());			
		$this->assertEquals($newEmail->getPersonStudentId(), $result->getPersonStudentId());	
		$this->assertEquals($newEmail->getPersonStaffId(), $result->getPersonStaffId());
			
		$this->assertEquals($newEmail->getReasonCategorySubitemId(), $result->getReasonCategorySubitemId());	
		$this->assertEquals($newEmail->getEmailSubject(), $result->getEmailSubject());
		$this->assertEquals($newEmail->getEmailBody(), $result->getEmailBody());		
		$this->assertEquals($newEmail->getShareOptions()[0]->getPrivateShare(), $result->getShareOptions()[0]->getPrivateShare());
		
	}
	
	/**
    * @expectedException Synapse\RestBundle\Exception\ValidationException
    */
	public function testDeleteEmailWithInvalidEmailId()
	{
	    $this->initializeRbac();
		$Email = $this->emailActivityService->deleteEmail($this->invalidEmailId);
        $this->assertInternalType('object', $Email,"Email Not Found.");
		$this->assertSame('{"errors": ["email Not Found."],
			"data": [],
			"sideLoaded": []
			}',$officeHour);
	}
	
	public function testDeleteEmail()
	{
        //$this->markTestSkipped("Errored");
	    $this->initializeRbac();
		$emailDto = $this->createEmailDto();				
		$newEmail = $this->emailActivityService->createEmail($emailDto);				
		$emailDelet = $this->emailActivityService->deleteEmail($newEmail->getEmailId());		
	}
}
