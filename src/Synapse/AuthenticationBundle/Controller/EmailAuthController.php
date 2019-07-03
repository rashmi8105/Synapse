<?php
namespace Synapse\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;

class EmailAuthController extends Controller
{

    /**
     *
     * @var \Synapse\AuthenticationBundle\Service\Impl\EmailAuthService @DI\Inject("emailauth_service")
     */
    private $emailAuthService;

    /**
     * @Route("/email/{username}")
     * @Template()
     */
    public function indexAction($username)
    {
        $returnUrl = $this->emailAuthService->emailAuth($username);
        header("location:$returnUrl");
        exit();
    }
    /**
     * @Route("/sendauthmail/{$email}")
     * @Template()
     */
    
    public function sendAuthMailAction($email){
        
        $this->emailAuthService->sendStudentLoginLinkEmail($email);
    }

    /**
     * @Route("/email/{username}/academicupdate")
     * @Template()
     */
    public function academicUpdateStudentLoginAction($username)
    {
    	$returnUrl = $this->emailAuthService->emailAuth($username,true);
    	header("location:$returnUrl");
    	exit();
    }
}
