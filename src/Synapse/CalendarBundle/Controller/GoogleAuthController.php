<?php
namespace Synapse\CalendarBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;

/**
 * Class GoogleAuthController
 *
 * @package Synapse\CalendarBundle\Controller
 *
 * @Rest\Prefix("/oauth")
 */
class GoogleAuthController
{
    /**
     * @var CalendarIntegrationService
     *
     *      @DI\Inject(CalendarIntegrationService::SERVICE_KEY)
     */
    private $calendarIntegrationService;

    /**
     * Get Google Authorization Header
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Google Authorization Header",
     * section = "Google Calendar Integration",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/google", requirements={"_format"="json"})
     * @QueryParam(name="state", description="filter")
     * @QueryParam(name="code", description="filter")
     * @QueryParam(name="error", description="access_denied will be return from Google if users denied the permission")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getGoogleAuthAction(ParamFetcher $paramFetcher)
    {
        $state = $paramFetcher->get('state');
        $code = $paramFetcher->get('code');
        $error = $paramFetcher->get('error');

        $oAuthUrl = $this->calendarIntegrationService->processOauthToken($state, $code, $error);

        header("location:$oAuthUrl");
        exit();
    }

    /**
     * Get Cronofy Authorization Header.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Cronofy Authorization Header",
     * section = "Cronofy Calendar Integration",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/cronofy", requirements={"_format"="json"})
     * @QueryParam(name="state", description="filter")
     * @QueryParam(name="code", description="filter")
     * @QueryParam(name="error", description="access_denied will be return from Google if users denied the permission")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCronofyAuthAction(ParamFetcher $paramFetcher)
    {
        $state = $paramFetcher->get('state');		
        $code = $paramFetcher->get('code');
        $error = $paramFetcher->get('error');
        $oAuthUrl = $this->calendarIntegrationService->processCronofyToken($state, $code, $error);

        header("location:$oAuthUrl");
        exit();
    }
}