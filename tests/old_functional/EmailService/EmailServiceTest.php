<?php

use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\EmailNotificationDto;

class EmailServiceTest extends \Codeception\TestCase\Test
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
	 * @var \Synapse\CoreBundle\Service\Impl\EmailService
	 */
	private $emailService;
	
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->emailService = $this->container
		->get('email_service');
	}
	
	
	public function createEmailNotification()
	{
		$emailNotificationDto = new EmailNotificationDto();
		
		$emailNotificationDto->setRecipientList("pg00359079@techmahindra.com");
		$emailNotificationDto->setSubject("testsubject");
		$emailNotificationDto->setBccList("");
		$emailNotificationDto->setCcList("test@gmail.com");
		$emailNotificationDto->setBody("test body");
		$emailNotificationDto->setEmailKey("cgjfvhjhj");
		$emailNotificationDto->setFromAddress("no-reply@mapworks.com");
		$emailNotificationDto->setOrganization(1);
		
		return $emailNotificationDto;
	}

	public function testSendEmail()
	{
		$emailNotification = $this->createEmailNotification();
		$sendMail = $this->emailService->sendEmail($emailNotification);
		$this->assertNotNull($sendMail,"Email Sending Failed");
	}

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSendEmailInvalid()
    {
        $emailNotification = new EmailNotificationDto();
        $emailNotification->setRecipientList(null);
        $emailNotification->setOrganization(-1);
        $this->emailService->sendEmail($emailNotification);
    }


    public function createTestSendEmailNotification()
    {
        $param=array();
        $param['subject']="testsubject";
        $param['bcc']="";
        $param['to']="test@gmail.com";
        $param['body']="test body";
        $param['emailKey']="cgjfvhjhj";
        $param['from']="no-reply@mapworks.com";
        $param['organizationId']=1;
        return $param;
    }


    public function testSendEmailNotification()
    {
        $param = $this->createTestSendEmailNotification();
        $sendEmailNotification = $this->emailService->sendEmailNotification($param);
        $this->assertEquals("testsubject",$sendEmailNotification->getSubject());
        $this->assertEquals("",$sendEmailNotification->getBccList());
        $this->assertEquals("test@gmail.com",$sendEmailNotification->getRecipientList());
        $this->assertEquals("test body",$sendEmailNotification->getBody());
        $this->assertEquals("cgjfvhjhj",$sendEmailNotification->getEmailKey());
        $this->assertEquals("no-reply@mapworks.com",$sendEmailNotification->getFromAddress());
        $this->assertEquals(1,$sendEmailNotification->getOrganization());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\EmailNotificationDto', $sendEmailNotification);
    }
}