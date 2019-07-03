<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\CreatePasswordDto;

class PasswordServiceTest extends \Codeception\TestCase\Test
{
    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    /**
     * @var \Synapse\CoreBundle\Service\PasswordService
     */
    private $passwordService;
    
    private $orgId ;
    
    private $email ;
    
    private $staffEmail;
    
    private $coordinatorId ;
    
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->passwordService = $this->container
        ->get('password_service');
        $this->orgId = 1;
        $this->email = "bipinbihari.pradhan@techmahindra.com";
        $this->staffEmail = "devadoss.poornachari@techmahindra.com";
        $this->coordinatorId = 2;
    }
    
    public function testGetOrganizationLang()
    {
        $org = $this->passwordService->getOrganizationLang($this->orgId);
        $this->assertInstanceOf('Synapse\CoreBundle\Entity\OrganizationLang', $org);
        $this->assertInstanceOf('Synapse\CoreBundle\Entity\LanguageMaster', $org->getLang());
        $this->assertEquals($this->orgId, $org->getOrganization()->getId());
    }
   
    public function testGenerateForgotPasswordLink()
    {
        $password = $this->passwordService->generateForgotPasswordLink($this->email);
        $this->assertInternalType('array', $password);
        $this->assertArrayHasKey('email_detail', $password);
        $this->assertTrue($password['email_sent_status']);
        $this->assertArrayHasKey('from', $password['email_detail']);
        $this->assertArrayHasKey('subject', $password['email_detail']);
        $this->assertArrayHasKey('bcc', $password['email_detail']);
        $this->assertArrayHasKey('to', $password['email_detail']);
        $this->assertArrayHasKey('emailKey', $password['email_detail']);
        $this->assertArrayHasKey('organizationId', $password['email_detail']);
    }
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGenerateForgotPasswordLinkInvalidEmail()
    {
        $password = $this->passwordService->generateForgotPasswordLink("invalidemail@email.com");
        $this->assertTrue($password['email_sent_status']);
    
    }
    /*
     * 
     * Since the token value is removed from generateForgotPasswordLink(..) response due to security concerns, 
     * this test case cannot be validated with token. Hence test case commented
    public function testValidateActivationLinkWithValidLink()
    {
        $password = $this->passwordService->generateForgotPasswordLink($this->email);
        //print_r($password);exit;
        $token = $password['token'];
        $validateToken = $this->passwordService->validateActivationLink($token);
        $this->assertNotEmpty($validateToken->getActivationToken());
        $this->assertInstanceOf("Synapse\CoreBundle\Entity\Person",$validateToken);
        
    }*/
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testValidateActivationLinkWithInValidLink()
    {
        
        $validateToken = $this->passwordService->validateActivationLink("122342345refgvdfvg");
        
    
    }
    /*
     *
     * Since the token value is removed from generateForgotPasswordLink(..) response due to security concerns,
     * this test case cannot be validated with token. Hence test case commented
    public function testCreatePasswordForCoord ()
    {
        $token = $this->passwordService->generateForgotPasswordLink($this->email);
        
        $passwordDto = new CreatePasswordDto();
        $passwordDto->setClientId("clientid");
        $passwordDto->setIsConfidentialityAccepted(true);
        $passwordDto->setPassword("12345@abcd");
        $passwordDto->setToken($token['token']);
        
        $password = $this->passwordService->createPassword($passwordDto);
        $this->assertTrue($password['signin_status']);
        $this->assertInternalType('array', $password);
        $this->assertArrayHasKey('email_detail', $password);
        $this->assertArrayHasKey('person_id', $password);
        $this->assertArrayHasKey('person_first_name', $password);
        $this->assertArrayHasKey('person_last_name', $password);
        $this->assertArrayHasKey('person_type', $password);
        $this->assertTrue($password['signin_status']);
        $this->assertArrayHasKey('from', $password['email_detail']);
        $this->assertArrayHasKey('subject', $password['email_detail']);
        $this->assertArrayHasKey('bcc', $password['email_detail']);
        $this->assertArrayHasKey('body', $password['email_detail']);
        $this->assertArrayHasKey('to', $password['email_detail']);
        $this->assertArrayHasKey('emailKey', $password['email_detail']);
        
    }*/
    /*
     *
     * Since the token value is removed from generateForgotPasswordLink(..) response due to security concerns,
     * this test case cannot be validated with token. Hence test case commented
    public function testCreatePasswordForStaff ()
    {
        $token = $this->passwordService->generateForgotPasswordLink($this->staffEmail);
    
        $passwordDto = new CreatePasswordDto();
        $passwordDto->setClientId("clientid");
        $passwordDto->setIsConfidentialityAccepted(true);
        $passwordDto->setPassword("12345@abcd");
        $passwordDto->setToken($token['token']);
    
        $password = $this->passwordService->createPassword($passwordDto);
        $this->assertTrue($password['signin_status']);
        $this->assertInternalType('array', $password);
        $this->assertArrayHasKey('email_detail', $password);
        $this->assertArrayHasKey('person_id', $password);
        $this->assertArrayHasKey('person_first_name', $password);
        $this->assertArrayHasKey('person_last_name', $password);
        $this->assertArrayHasKey('person_type', $password);
        $this->assertTrue($password['signin_status']);
        $this->assertArrayHasKey('from', $password['email_detail']);
        $this->assertArrayHasKey('subject', $password['email_detail']);
        $this->assertArrayHasKey('bcc', $password['email_detail']);
        $this->assertArrayHasKey('body', $password['email_detail']);
        $this->assertArrayHasKey('to', $password['email_detail']);
        $this->assertArrayHasKey('emailKey', $password['email_detail']);
    
    }*/
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreatePasswordInvalidToken ()
    {
        $token = $this->passwordService->generateForgotPasswordLink($this->email);
    
        $passwordDto = new CreatePasswordDto();
        $passwordDto->setClientId("clientid");
        $passwordDto->setIsConfidentialityAccepted(true);
        $passwordDto->setPassword("12345@abcd");
        $passwordDto->setToken("fdfsdfg123424234");
    
        $password = $this->passwordService->createPassword($passwordDto);
    
    }
    /*
     *
     * Since the token value is removed from generateForgotPasswordLink(..) response due to security concerns,
     * this test case cannot be validated with token. Hence test case commented
    public function testSendlinkCoordinatorValid()
    {
        $sendLink = $this->passwordService->sendlinkCoordinator($this->orgId,$this->coordinatorId);
        $this->assertTrue($sendLink['email_sent_status']);
        $this->assertArrayHasKey('from', $sendLink['email_detail']);
        $this->assertArrayHasKey('subject', $sendLink['email_detail']);
        $this->assertArrayHasKey('bcc', $sendLink['email_detail']);
        $this->assertArrayHasKey('body', $sendLink['email_detail']);
        $this->assertArrayHasKey('to', $sendLink['email_detail']);
        $this->assertArrayHasKey('emailKey', $sendLink['email_detail']);
        $this->assertInternalType('array', $sendLink);
        $this->assertArrayHasKey('token', $sendLink);
    }*/
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testSendlinkCoordinatorInvalidPerson()
    {
        $sendLink = $this->passwordService->sendlinkCoordinator($this->orgId,-1);
        $this->assertTrue($sendLink['email_sent_status']);
    }
    public function _after()
    {
        
    }
}