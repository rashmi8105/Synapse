<?php
namespace Synapse\CalendarBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\AuthenticationBundle\Service\Impl\EmailAuthService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class MapworksController
 *
 * @package Synapse\CalendarBundle\Controller
 *
 * @Rest\Prefix("/mapworks")
 */
class MapworksController extends AbstractAuthController
{

    /**
     * @var EmailAuthService
     *
     *      @DI\Inject(EmailAuthService::SERVICE_KEY)
     */
    private $emailAuthService;

    /**
     * DEPRECATED. Gets an appointments list page from google calendar.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Google Calendar Appointments List",
     * section = "Google Calendar Integration",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/email/{username}/{type}", requirements={"_format"="json"})
     * @QueryParam(name="id", description="appointments id")
     * @Rest\View(statusCode=200)
     *
     * @param String $username
     * @param String $type
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated
     */
    public function redirectToAppointmentsListAction($username, $type, ParamFetcher $paramFetcher)
    {
        $id = $paramFetcher->get('id');
        $returnUrl = $this->emailAuthService->emailAuth($username, false, $type, $id);

        header("location:$returnUrl");
        exit();
    }
}