<?php

namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\RestBundle\Entity\Response;

/**
 * Class LoggingController
 * @package Synapse\RestBundle\Controller
 *
 * @Rest\Prefix("logging")
 */
class LoggingController extends LogController
{
    /**
     * @var Manager
     * @DI\Inject(Manager::SERVICE_KEY)
     */
    protected $rbacManager;

    /**
     * If any error found in front end, this API will be called to log the error details.
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Log front end error",
     *          section = "Error Logging",
     *          statusCodes = {
     *              201 = "Resource created. Representation of resource was returned.",
     *              400 = "Validation errors have occurred.",
     *              403 = "Access denied.",
     *              404 = "Not found",
     *              500 = "There were errors either in the body of the request or an internal server error",
     *              504 = "Request has timed out."
     *          }
     * )
     *
     * @Rest\Post("/log-frontend-error", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @return Response
     */
    public function logFrontEndErrorAction()
    {
        $controllerActionName = __METHOD__;
        $requestPath = $this->getRequest()->getRequestUri();
        $message = "{\"Origin\": \"FrontEnd\", \"PersonId\": \"{$this->getLoggedInUserId()}\", \"OrganizationId\": \"{$this->getLoggedInUserOrganizationId()}\", 
        \"IPAddress\": \"{$this->getClientIp()}\", \"Path\": \"{$requestPath}\", \"QueryString\": \"\", \"RequestBody\": \"{$this->getRequestBodyAsString()}\" }";
        $this->logError($controllerActionName, $message);
        return new Response(["result" => "success"]);
    }
}