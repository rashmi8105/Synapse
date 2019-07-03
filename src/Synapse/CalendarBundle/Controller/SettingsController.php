<?php
namespace Synapse\CalendarBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CalendarBundle\Job\InitialSyncJob;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Exception\ResqueJobRunDeniedException;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class SettingsController
 *
 * @package Synapse\CalendarBundle\Controller
 *
 * @Rest\Prefix("/settings")
 */
class SettingsController extends AbstractAuthController
{

    /**
     * @var CalendarIntegrationService
     *
     *      @DI\Inject(CalendarIntegrationService::SERVICE_KEY)
     */
    private $calendarIntegrationService;

    /**
     * @var JobService
     *
     *      @DI\Inject(JobService::SERVICE_KEY)
     */
    private $jobService;

    /**
     * Get the sync status of faculty.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Sync Status",
     * output = "Synapse\CalendarBundle\EntityDto\SyncFacultySettingsDto",
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
     * @Rest\Get("/calendar_sync/google", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getFacultySyncStatusAction()
    {
        $loggedUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $response = $this->calendarIntegrationService->getGoogleSyncStatus($organizationId, $loggedUserId);
        return new Response($response);
    }

    /**
     * Gets the Cronofy Redirection URL. UI will redirect the users to this URL to get the authentication details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Cronofy Authentication Redirect URL",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Cronofy Calendar Integration",
     * statusCodes = {
     *                  200 = "Returned when successful",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\GET("/calendar_sync/google/enable", requirements={"_format"="json"})
     * @QueryParam(name="token", description="user's access token.")
     * @QueryParam(name="type", description="to identify which option is enabled whether pcs to maf or maf to pcs")
     *
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function enableOauthAction(ParamFetcher $paramFetcher)
    {
        $accessToken = $paramFetcher->get('token');
        $type = $paramFetcher->get('type');
        $isProxyUser = false;
        if ($this->container->has('proxy_user')) {
            $isProxyUser = true;
        }
        $loggedInUser = $this->getUser();
        $loggedUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();

        $personCalendarSettings = $this->calendarIntegrationService->facultyCalendarSettings($organizationId, $loggedUserId);
        
        if (!$personCalendarSettings['campusStatus']) {
            throw new ResqueJobRunDeniedException("Calendar Sync has already been disabled.");
        }
        $this->jobService->getUnBlockedPendingJobActions($organizationId, InitialSyncJob::JOB_KEY, $loggedUserId, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
        $oAuthUrl = $this->calendarIntegrationService->redirectToOauth($loggedUserId, $organizationId, $accessToken, $type, $isProxyUser);
        return new Response($oAuthUrl);
    }
}    