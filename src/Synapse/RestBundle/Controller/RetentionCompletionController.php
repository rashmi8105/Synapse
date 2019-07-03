<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Synapse\CoreBundle\Service\Impl\RetentionCompletionService;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Class MyAccountController
 *
 * @package Synapse\RestBundle\Controller
 *
 * @Rest\Prefix("/retention-completion")
 */
class RetentionCompletionController extends AbstractAuthController
{

    /**
     * @var RetentionCompletionService
     *
     *      @DI\Inject(RetentionCompletionService::SERVICE_KEY)
     */
    private $retentionCompletionService;

    /**
     * Get retention tracking variables for given student or by organization if left blank.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Retention Tracking Variables",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Retention / Completion",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/retention-tracking-variables", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="studentId", description="studentId")
     * @QueryParam(name="yearId", description="yearId")
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRetentionTrackVariablesAction(ParamFetcher $paramFetcher)
    {
        $studentId = $paramFetcher->get("studentId");
        $yearId = $paramFetcher->get("yearId");
        // check if studentId passed if not send an empty array of studentIds to getOrganizationRetentionCompletionVariables
        $studentIds = [];
        if ($studentId != null) {
            $studentIds = [$studentId];
        }
        $loggedInUsedId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $response = $this->retentionCompletionService->getOrganizationRetentionCompletionVariables($loggedInUsedId, $organizationId, $yearId, $studentIds);
        return new Response($response);
    }

    /**
     * Gets retention tracking groups for an organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Retention Tracking Groups",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Retention / Completion",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/retention-tracking-groups", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */

    public function getRetentionTrackGroupsAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $retentionTrackingGroups = $this->retentionCompletionService->getOrganizationRetentionTrackingGroups($loggedInUserId, $organizationId);
        return new Response($retentionTrackingGroups);
    }

    /**
     * Gets variables for a retention tracking group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Retention Tracking Group Variables",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Retention / Completion",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/variables", requirements={"_format"="json"})
     * @QueryParam(name="retentionTrackingYear", description="Retention Tracking year,eg:201516")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getVariablesForRetentionTrackingGroupAction(ParamFetcher $paramFetcher)
    {
        $retentionTrackingGroup = $paramFetcher->get('retentionTrackingYear');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $retentionCompletionVariable = $this->retentionCompletionService->getRetentionTrackGroupVariables($loggedInUserId, $organizationId, $retentionTrackingGroup);
        return new Response($retentionCompletionVariable);

    }
}