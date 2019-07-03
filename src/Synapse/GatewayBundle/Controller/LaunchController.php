<?php

namespace Synapse\GatewayBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\GatewayBundle\Service\Impl\LaunchService;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Launch controller
 *
 * @package Synapse\GatewayBundle\Controller
 *
 * @Rest\Prefix("launch")
 */
class LaunchController extends AbstractSynapseController
{
    /**
     * @var LaunchService
     *
     *      @DI\Inject(LaunchService::SERVICE_KEY)
     */
    private $launchService;

    /**
     * Creates the url to launch a redirect.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Launch",
     * section = "Launch",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="tp_user_id")
     *
     * @param ParamFetcher $paramFetcher
     * @return array
     * @deprecated
     */
    public function createLaunchAction(ParamFetcher $paramFetcher)
    {
        $personId = $paramFetcher->get('tp_user_id');
        $launch = $this->launchService->createLaunch($personId);

        return $launch;
    }

    /**
     * Gets the launch redirect url.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Launch Redirect",
     * section = "Launch",
     * statusCodes = {
     *                  301 = "Moved permanently",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/redirect", requirements={"_format"="json"})
     * @Rest\View(statusCode=301)
     * @RequestParam(name="tp_user_id")
     * @RequestParam(name="access_token")
     * @QueryParam(name="intent")
     *
     * @param ParamFetcher $paramFetcher
     * @deprecated
     */
    public function launchRedirectAction(ParamFetcher $paramFetcher)
    {
        $personId = $paramFetcher->get('tp_user_id');
        $accessToken = $paramFetcher->get('access_token');
        $url = $this->launchService->redirectLaunch($personId, $accessToken);

        header("Location: $url");
        exit();
    }
}
