<?php
namespace Synapse\CalendarBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CalendarBundle\EntityDto\SyncFacultySettingsDto;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Exception\ResqueJobRunDeniedException;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Controller\AbstractAuthController;

/**
 * Class CalendarController
 *
 * @package Synapse\CalendarBundle\Controller
 *
 * @Rest\Prefix("/calendar")
 */
class CalendarController extends AbstractAuthController
{

    /**
     * @var CalendarIntegrationService
     *
     * @DI\Inject("calendarintegration_service")
     */
    private $calendarIntegrationService;

    /**
     * @var JobService
     * @DI\Inject(JobService::SERVICE_KEY)
     */
    private $jobService;

    /**
     * Update external calendar settings for faculty.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Update external calendar settings for faculty",
     *  input = "Synapse\CalendarBundle\EntityDto\SyncFacultySettingsDto",
     *  section = "Cronofy",
     *  statusCodes = {
     *      201 = "Resource(s) updated. Representation of resource(s) was returned.",
     *      400 = "Validation errors have occurred.",
     *      403 = "Access denied.",
     *      404 = "Not found",
     *      500 = "There were errors either in the body of the request or an internal server error",
     *      504 = "Request has timed out."
     *  }
     * )
     *
     * @Rest\Put("/syncFacultySettings", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param SyncFacultySettingsDto $facultySettingsDto
     *
     * @return Response
     */
    public function facultySettingsAction(SyncFacultySettingsDto $facultySettingsDto)
    {
        $loggedInPersonId = $this->getUser()->getId();
        $facultySettings = $this->calendarIntegrationService->updateFacultySyncStatus($facultySettingsDto, $loggedInPersonId);
        return new Response($facultySettings);
    }

    /**
     * Returns whether or not the calendar sync can be turned off for an organization or a person based on the type parameter.
     *
     * TODO: Change the route of this API to something more applicable to what it's actually doing.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Calendar Disable Check",
     * section = "Cronofy",
     * statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Validation errors have occurred.",
     *     500 = "There was either errors with the body of the request or an internal server error.",
     *     504 = "Request has timed out. Please re-try."
     * })
     *
     * @Rest\Get("/organization_person_sync_status", requirements={"_format"="json"})
     * @QueryParam(name="type", description="'organization' for the sync status of the organization, or anything else for the logged in person's sync status")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getOrganizationPersonSyncStatusAction(ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getUser()->getOrganization()->getId();
        $type = $paramFetcher->get('type');
        if ($type == 'organization') {
            $this->jobService->getUnBlockedPendingJobActions($organizationId, SynapseConstant::ORG_CALENDAR_JOB, null, SynapseConstant::ORG_PENDING_JOB_ERROR, false);
        } else {
            if ($this->container->has('proxy_user')) {
                $loggedInUserId = $this->container->get('proxy_user')->getId();
            } else {
                $loggedInUserId = $this->getUser()->getId();
            }
            $personCalendarSettings = $this->calendarIntegrationService->facultyCalendarSettings($organizationId, $loggedInUserId);
            if (!$personCalendarSettings['campusStatus']) {
                throw new ResqueJobRunDeniedException("Calendar Sync has already been disabled.");
            }
            $this->jobService->getUnBlockedPendingJobActions($organizationId, SynapseConstant::JOB_KEY_REMOVE_EVENT, $loggedInUserId, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
        }
        return new Response(true);
    }
}