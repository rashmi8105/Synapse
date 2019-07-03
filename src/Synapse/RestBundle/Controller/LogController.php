<?php
namespace Synapse\RestBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\PermissionConstInterface;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\SynapseConstant;

/**
 * LogController.
 *
 * @package Synapse\RestBundle\Controller
 */
abstract class LogController extends AbstractSynapseController implements PermissionConstInterface
{
    // Scaffolding

    /**
     * TODO: Controller level API logger should be removed from existing controllers.
     *
     * @var Logger
     * @DI\Inject(SynapseConstant::CONTROLLER_LOGGING_CHANNEL)
     */
    private $apiRestfulServiceLogger;

    /**
     * @DI\Inject(SynapseConstant::LOGGER_KEY)
     */
    private $logger;

    // Service initialization
    /**
     * @var APIValidationService
     * @DI\Inject(APIValidationService::SERVICE_KEY)
     */
    protected $apiValidationService;

    /**
     * Call the logError method of the AbstractService this is be called when to log Notice in dev logs
     *
     * @param string $functionName
     * @param string $message
     * @return bool
     */
    public function logError($functionName, $message)
    {
        $errorText = "{\"FunctionName\": \"{$functionName}\", \"Message\": \"{$message}\"}";
        $this->logger->error($errorText);
        return true;
    }

    /**
     * Get a user from the Security Token Storage.
     * Implementation is in Controller Framework, which returns the user from the token.
     *
     * @return mixed
     * @throws \LogicException If SecurityBundle is not available
     *
     */
    public function getLoggedInUser()
    {
        return $this->getUser();
    }

    /**
     * Get the logged in user id from the Security Token Storage.
     *
     * @return int| null
     * @throws \LogicException If SecurityBundle is not available
     */
    public function getLoggedInUserId()
    {
        if ($this->getLoggedInUser()) {
            return $this->getLoggedInUser()->getId();
        } else {
            return null;
        }
    }

    /**
     * Get the logged in user Organization.
     *
     * @return Organization| null
     * @throws \LogicException If SecurityBundle is not available
     */
    public function getLoggedInUserOrganization()
    {
        if ($this->getLoggedInUser()) {
            return $this->getLoggedInUser()->getOrganization();
        } else {
            return null;
        }
    }

    /**
     * Get the logged in user's organization id
     *
     * @return int| null
     * @throws \LogicException If SecurityBundle is not available
     */
    public function getLoggedInUserOrganizationId()
    {
        if ($this->getLoggedInUserOrganization()) {
            return $this->getLoggedInUserOrganization()->getId();
        } else {
            return null;
        }
    }

    /**
     * Get the JSON from the request object.
     *
     * @return string
     * @throws \LogicException If SecurityBundle is not available
     */

    public function getRequestBodyAsString()
    {
        return $this->get("request")->getContent();
    }

    /**
     * Get the Security Context instance, using this user id, organization, authentication can be retrieved.
     *
     * @return object
     * @throws \LogicException If SecurityBundle is not available
     */
    public function getSecurityContextObject()
    {
        return $this->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY);
    }

    /**
     * Call the logAction method of the AbstractService this is not be used it is called by ensureAction
     *
     * @param string $controllerActionName
     * @param string $requestPath
     * @param string $queryString
     * @param bool $isDevLog - indicates whether the request details be stored in dev log or api log
     * @return bool
     */
    public function logAction($controllerActionName, $requestPath, $queryString, $isDevLog = true)
    {
        $message = "; PersonId: {$this->getLoggedInUserId()}; OrganizationId: {$this->getLoggedInUserOrganizationId()}; IP Address: {$this->getClientIp()};
        Path: {$requestPath}; Query String: {$queryString}; Request Body: {$this->getRequestBodyAsString()}";
        if ($isDevLog) {
            $this->logNotice($controllerActionName, $message);
        } else {
            $this->logRestfulAPILogger($controllerActionName, $message);
        }
        return true;
    }

    /**
     * Get the users IP Address
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
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
     * Call the logNotice method of the AbstractService this is be called when to log Notice in dev logs
     *
     * @param string $functionName
     * @param string $message
     * @return bool
     */
    public function logNotice($functionName, $message)
    {
        $this->logger->notice($functionName . "; Message: $message");
        return true;
    }

    /**
     * Call the logNotice method of the AbstractService this is be called for api logs.
     *
     * @param string $functionName
     * @param string $message
     * @return bool
     */
    public function logRestfulAPILogger($functionName, $message)
    {
        $this->apiRestfulServiceLogger->notice($functionName . "; Message: $message");
        return true;
    }

    /**
     * Log back end error
     *
     * @param string $controllerActionName
     * @param string $requestPath
     * @param string $queryString
     */
    public function logErrorBE($controllerActionName, $requestPath, $queryString)
    {
        $message = "Origin: BackEnd; PersonId: {$this->getLoggedInUserId()}; OrganizationId: {$this->getLoggedInUserOrganizationId()}; IP Address: {$this->getClientIp()};
            Path: {$requestPath}; Query String: {$queryString}; Request Body: {$this->getRequestBodyAsString()}";
        $this->logError($controllerActionName, $message);
    }
}