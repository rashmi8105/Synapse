<?php
use Codeception\Util\Stub;

class FunctionalBaseTest extends \Codeception\TestCase\Test
{

    public function createSecuritymock()
    {
        if (! isset($this->securityContext)) {
            $this->serContainer = $this->getModule('Symfony2')->kernel->getContainer();
            $this->personService = $this->serContainer->get('person_service');
            $personObj = $this->personService->find(1);
            $this->securityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
                ->disableOriginalConstructor()
                ->setMethods([
                'getToken',
                'isAuthenticated',
                'getUser'
            ])
                ->getMock();
            $this->securityContext->expects($this->any())
                ->method('getToken')
                ->will($this->returnSelf());
            $this->securityContext->expects($this->any())
                ->method('isAuthenticated')
                ->will($this->returnValue(true));
            $this->securityContext->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($personObj));
        }
        
        return $this->securityContext;
    }

    public function createServiceWithMock($service, $rbac = false)
    {
        $this->createSecuritymock();
        $this->serContainer = $this->getModule('Symfony2')->kernel->getContainer();
        $requestedService = $this->serContainer->get($service);
        $requestedService->setSecurity($this->securityContext);
        if ($rbac) {
            $requestedService->rbacManager->setSecurity($this->securityContext);
        }
        return $requestedService;
    }

    public function createServiceWithRbacMock($service)
    {
        $this->createSecuritymock();
        $this->serContainer = $this->getModule('Symfony2')->kernel->getContainer();
        $requestedService = $this->serContainer->get($service);
        if(isset($requestedService->rbacManager)){
            $requestedService->rbacManager->setSecurity($this->securityContext);
        }
        return $requestedService;
    }
}