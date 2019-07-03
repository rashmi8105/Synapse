<?php
namespace Synapse\RestBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\EbiUserService;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Base controller for user authorization.
 *
 * @package Synapse\RestBundle\Controller
 */
abstract class AbstractAuthController extends LogController
{
    // Scaffolding

    /**
     * @DI\Inject("logger")
     */
    private $logger;

    /**
     * @var Manager
     * @DI\Inject(Manager::SERVICE_KEY)
     */
    protected $rbacManager;

    // Services

    /**
     * @var APIValidationService
     * @DI\Inject(APIValidationService::SERVICE_KEY)
     */
    protected $apiValidationService;

    /**
     * @var EbiUserService
     * @DI\Inject(EbiUserService::SERVICE_KEY)
     */
    private $ebiUser;

    /**
     * @var UserManagementService
     * @DI\Inject(UserManagementService::SERVICE_KEY)
     */
    private $userManagementService;

    // Private Variables

    /**
     * @var array
     */
    protected $requiredPermissions = array();

    /**
     * @var Person
     */
    protected $loggedInUser;

    /**
     * @var Person
     */
    protected $personBeingProxiedInAs;


    /**
     * Loads the controller's required permissions from the config file.
     *
     * @return array
     */
    public function fetchRequiredPermissionsFromConfig()
    {
        /**
         *
         * @var Request $request
         */
        $request = $this->get('request_stack')->getCurrentRequest();
        $actionParams = explode('::', $request->attributes->get('_controller'));
        
        // Strip the word 'Controller' from the controller name.
        $controllerName = substr($actionParams[0], 0, - 10);
        // Strip the word 'Action' from the action name.
        $actionName = substr($actionParams[1], 0, - 6);
        
        $configName = "permissions.{$controllerName}.{$actionName}";
        if ($this->container->hasParameter($configName)) {
            $requiredPermissions = $this->container->getParameter($configName);
            return $requiredPermissions;
        } else {
            // Let's see if the short class name was used in the Synapse namespace.
            if (!($pos = strpos($controllerName, '\\'))) {
                return array();
            }
            
            // We want to limit this to namespaces explicitly controlled by the organization.
            // This is for proper security compliance, so that hackers do not upload, say,
            // MyPwnedNamespace\OrgPermissions and have it treated as Synapse\OrgPermissions.
            $firstNamespace = substr($controllerName, 0, $pos);
            if ($firstNamespace !== 'Synapse') {
                return array();
            }
            
            // Get the short class name.
            $controllerName = basename(str_replace('\\', '/', $controllerName));
            $configName = "permissions.{$controllerName}.{$actionName}";
            if ($this->container->hasParameter($configName)) {
                $requiredPermissions = $this->container->getParameter($configName);
                return $requiredPermissions;
            }
            
            return array();
        }
    }

    public function getRequiredPermissions()
    {
        return $this->requiredPermissions;
    }

    /**
     * Required for utilizing the AuthListener functionality.
     *
     * @param array $requiredPermissions            
     */
    public function setRequiredPermissions($requiredPermissions)
    {
        $this->requiredPermissions = $requiredPermissions;
    }

    /**
     * Do the process of access check. Get the logged in user id from the Security Token Storage.
     *
     * @param array $permissions
     * @return bool
     * @throws AccessDeniedException
     */
    public function ensureAccess(array $permissions)
    {
        // check authentication for logged in user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $request = $this->getRequest();
        $isServiceAccount = $this->isOrganizationAPICoordinator(false);

        /**
         * TODO: isServiceAccountAccessV1API and isOrganizationUserAccessingV2API calls should be removed once all V2 controllers are extended from AbstractAuthV2Controller. While removing this, un comment "throw new AccessDeniedException()".
         */
        if ($isServiceAccount) {
            // If the user is a service account, don't allow to access V1 API's
            // throw new AccessDeniedException('Service Accounts are not allowed to access the version 1 APIs.');
            $this->isServiceAccountAccessV1API($request);
        } else {
            $this->isOrganizationUserAccessingV2API($request);
            $switchUser = $request->headers->get('switch-user');
            $securityContextObject = $this->getSecurityContextObject();
            $securityContextUserObject = $this->getLoggedInUser();
            $this->loggedInUser = $securityContextUserObject;
            $securityContextUserOrganizationObject = $this->getLoggedInUserOrganization();

            if (trim($switchUser) != "") {
                $proxyUserObject = $this->container->get('proxy_user');
                $this->personBeingProxiedInAs = $proxyUserObject;
                if ($proxyUserObject) {
                    $securityContextObject->getToken()->setUser($proxyUserObject);
                    $securityContextUserObject = $securityContextObject->getToken()->getUser();
                    $securityContextUserOrganizationObject = $securityContextUserObject->getOrganization();
                }
            }
            if ($securityContextUserOrganizationObject) {
                $organizationStatus = $securityContextUserOrganizationObject->getStatus();
                $organizationStatus = strtoupper($organizationStatus);
                if ($securityContextUserOrganizationObject) {
                    $organizationIdLoggedInUser = $securityContextUserOrganizationObject->getId();
                }
                if ($organizationStatus == SynapseConstant::INACTIVE_ORGANIZATION_STATUS && $organizationIdLoggedInUser > 0) {
                    $message = "Users from an inactive organization do not have permission to access.";
                    throw new AccessDeniedException($message, "inactive_organization", 403, $message);

                }
            }

            // Non participant or inactive student or inactive faculty should not be allowed to login
            $userId = $securityContextUserObject->getId();
            if (!$this->userManagementService->isUserAllowedToLogin($userId)) {
                $message = "The user is not active or not participating in the current academic year";
                throw new AccessDeniedException($message, "inactive_user", 403, $message);
            }

            $this->rbacManager->initializeForUser();
            if (empty($permissions)) {
                return true;
            }
            if (!$this->rbacManager->hasAccess($permissions)) {
                throw new AccessDeniedException('Unauthorized access: ' . $permissions[0]);
            }
        }
        return true;
    }

    public function ensureAdminAccess() {
        $this->logger->info("ensure admin access- check if not an authenticated user.");
        // check authentication for logged in user
        $this->checkIfAuthenticated(); 
         
        if($this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()){
            $userId = $this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()->getId();
            $this->logger->info("Authenticated user Id - ".$userId);
    
            $isEbiUser = $this->ebiUser->isEbiUser($userId);
            if(!$isEbiUser) {
                $this->logger->error("ensure Admin access- not an ebi user.");
                throw new AccessDeniedException();
            }
    
            return true;
        }
    }

    public function ensureEBIorCoordinatorUserAccess($orgId){
        // check authentication for logged in user
        $this->checkIfAuthenticated();
        if($this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()){
            $this->setProxyUser(); // set the proxy user if available
            $isEbiUser = $this->checkIfEbiUser();
            if ($isEbiUser) {
                return true;
            }
            // if coordinator then checkAccessToOrganization ($orgId)
            $this->logger->info("ensureEBIorCoordinatorUserAccess - if coordinator then checkAccessToOrganization");
            $this->rbacManager->checkAccessToOrganization($orgId);
                            
            // check for coordinator
            $isCoordinator = $this->rbacManager->hasCoordinatorAccess();
            if(!$isCoordinator){
                $this->logger->error("ensureEBIorCoordinatorUserAccess - not an coordinator.");
                throw new AccessDeniedException();
            }
    
        }
    }
    
    private function checkIfAuthenticated(){
        // check for ebi admin user
        $this->logger->info("checkIfAuthenticated- check if an authenticated user.");
        // check authentication for logged in user
        if (!($this->get(UploadConstant::SECURITY_CONTEXT)->isGranted('IS_AUTHENTICATED_FULLY'))) {
            $this->logger->error("ensure admin access- not an authenticated user.");
            throw new AccessDeniedException();
        }
        else {
            //Authenticated!! Do nothing.
        }
    }
    
    private function checkIfEbiUser(){
        $userId = $this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()->getId();
        $this->logger->info("Authenticated user Id - ".$userId);
        
        $isEbiUser = $this->ebiUser->isEbiUser($userId);        
        return $isEbiUser;
    }

    /**
     * Checks faculty access to the student.
     *
     * @param int $studentId
     * @throws AccessDeniedException
     */
    public function checkAccessToStudent($studentId){
        // Check student participation check for current academic year.
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        $flag = $this->rbacManager->checkAccessToStudent($studentId);
        if(!$flag){
            $this->logger->error("Logged in user doesn't have access to this student");
            throw new AccessDeniedException();
        }
    }
    
    public function checkLoggedInUserAPIAccess($personId = null){
        $loggedInPersonId = $this->getUser()->getId();
    	if($loggedInPersonId!=$personId){
    	    $delegateAcccessFlag = $this->rbacManager->delegateAcccessForStaff($personId);
    	    if(!$delegateAcccessFlag){
        		$this->logger->error("Logged in user doesn't have access to this api.");
        		throw new AccessDeniedException();
    	    }
    	}
    }

    /**
     * Verifies that the person has access to create an appointment as someone else (delegate access)
     *
     * @param integer|null $personId
     * @return bool
     */
    public function checkDelegateAccessForStaff($personId = null)
    {
        $loggedInPersonId = $this->getUser()->getId();
        if ($loggedInPersonId != $personId) {
            $delegateAccessFlag = $this->rbacManager->delegateAcccessForStaff($personId);
            if ($delegateAccessFlag) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function isCoordinator($userId)
    {
        // check authentication for logged in user
        $this->checkIfAuthenticated();

        // if coordinator then checkAccessToOrganization ($orgId)
        $this->logger->info("ensureCoordinatorOrgAccess - checking deleted user organization check");
        $this->rbacManager->checkAccessToOrganizationUsingPersonId($userId);

        return $this->rbacManager->hasCoordinatorAccess();
    }
    
    public function ensureCoordinatorOrgAccess($userId)
    {
        // check for coordinator
        $isCoordinator = $this->isCoordinator($userId);
        if (!$isCoordinator) {
            $this->logger->error("ensureEBIorCoordinatorUserAccess - not an coordinator.");
            throw new AccessDeniedException();
        }
    }
    

    /**
     * Check permission for specific user
     * @param $permissions
     * @param string $personId
     * @throws AccessDeniedException
     */
    public function checkUserPermission($permissions, $personId){
        
        $userAccess = $this->rbacManager->checkUserPermission($permissions, $personId);
        if (! $userAccess) {
            $this->logger->info(">>> Check User Permission - Do not have necessory permission to access feature.");
            throw new AccessDeniedException('Unauthorized access to user: ' . $permissions[0]);
        }
        
    }

   
    /**
     * This method is created for our debuging purpose
     * will be remove it later
     * @param string $show
     */
    public function showtree($show,$userId){
        if($show == 'accesstree') {
            $am = $this->rbacManager->getAccessTree();
        }
        else {
            $am = $this->rbacManager->getAccessMap($userId);
        }
        print_r($am); exit;
    }
    
    public function ensureSkyfactorAdminAccess() {
    	$this->logger->info("ensure skyfactor admin access- check if not an authenticated user.");
    	// check authentication for logged in user
    	$this->checkIfAuthenticated();
    	 
    	if($this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()){
    		$userId = $this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()->getId();
    		$this->logger->info("Authenticated user Id - ".$userId);
    
    		$isSkyfactorUser = $this->ebiUser->isSkyfactorUser($userId);
    		if(!$isSkyfactorUser) {
    			$this->logger->error("ensure skyfactor Admin access- not an skyfactor user.");
    			throw new AccessDeniedException();
    		}
    
    		return true;
    	}
    }
    
    public function ensureArtMember() {
        $this->logger->info("ensure art member access- check if not an authenticated user.");
        // check authentication for logged in user
        $this->checkIfAuthenticated();
         
        if($this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()){
            $userId = $this->get(UploadConstant::SECURITY_CONTEXT)->getToken()->getUser()->getId();
            $this->logger->info("Authenticated user Id - ".$userId);
    
            $isEbiUser = $this->ebiUser->isARTUser($userId);
            if(!$isEbiUser) {
                $this->logger->error("ensure art member access- not an ebi user.");
                throw new AccessDeniedException();
            }    
            return true;
        }
    }
    
    private function setProxyUser()
    {
        /*
         * Check if proxy user is present and settting it in the user object
         */
        $request = Request::createFromGlobals();
        $switchUser = $request->headers->get('switch-user');
        if (trim($switchUser) != "") {
            $proxyUserObj = $this->container->get('proxy_user');
            if ($proxyUserObj) {
                $this->container->get(UploadConstant::SECURITY_CONTEXT)
                    ->getToken()
                    ->setUser($proxyUserObj);
            }
        }
    }

    /**
     * Call the isOrganizationAPICoordinator method of the AbstractService
     * @param bool $throwException
     * @return bool
     */
    public function isOrganizationAPICoordinator($throwException = true)
    {
        return $this->apiValidationService->isOrganizationAPICoordinator($this->getLoggedInUserOrganizationId(), $this->getLoggedInUserId(), $throwException);
    }

    /**
     * @deprecated This function should be removed once all V2 controllers are extending from AbstractAuthV2Controller
     * @param object $request
     * @return bool
     */
    public function isServiceAccountAccessV1API($request)
    {
        // if the user is a service account, don't allow to access V1 api's
        $uriPathRequested = $request->getRequestUri();
        $uriPathArray = explode("/", $uriPathRequested);
        if (in_array('v1', $uriPathArray)) {
            throw new AccessDeniedException('Service Accounts are not allowed to access the version 1 APIs.');

        }
        return true;
    }

    /**
     * @deprecated This function should be removed once all V2 controllers are extending from AbstractAuthV2Controller
     * @param object $request
     * @return bool
     */
    public function isOrganizationUserAccessingV2API($request)
    {
        // if the user is not a service account , don't allow to access V2 api's
        $uriPathRequested = $request->getRequestUri();
        $uriPathArray = explode("/", $uriPathRequested);
        if (!in_array('v1', $uriPathArray)) {
            throw new AccessDeniedException('Only service accounts are allowed to access the version 2 APIs.');
        }
        return true;
    }
}
