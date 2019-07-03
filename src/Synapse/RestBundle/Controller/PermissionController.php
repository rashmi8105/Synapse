<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\PermissionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;

/**
 * PermissionController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/permission")
 */
class PermissionController extends AbstractAuthController
{

    /**
     * @var PermissionService
     *
     *      @DI\Inject(PermissionService::SERVICE_KEY)
     */
    private $permissionService;

    /**
     * Searches for students using the given search string, and returns 100 (or less) of the closest matching results.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Students",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Students",
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
     * @Rest\Get("/{organizationId}/mystudents", requirements={"_format"="json"})
     * @QueryParam(name="personId", requirements="\d+", strict=true, description="Person Id")
     * @QueryParam(name="search_text", description="search_text")
     * @QueryParam(name="appointmentPermission",  description="To check appointment permission")
     * @QueryParam(name="user_type", description="If coordinator, verifies user is a coordinator and then searches with coordinator permissions")
     * @Rest\View(statusCode=200)
     *
     * @param int $organizationId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentsAction($organizationId, ParamFetcher $paramFetcher)
    {
        $loggedInUserOrganizationId = $this->getLoggedInUserOrganizationId();
        $personId = $paramFetcher->get('personId');
        $searchText = $paramFetcher->get('search_text');

        $this->checkLoggedInUserAPIAccess($personId);

        if ($loggedInUserOrganizationId != $organizationId) {
            if ($loggedInUserOrganizationId != SynapseConstant::ADMIN_ORGANIZATION_ID) {
                throw new AccessDeniedException("You do not have access to organization $organizationId");
            }
        }

        $requiresAppointmentPermission = (bool)$paramFetcher->get('appointmentPermission');

        $userType = $paramFetcher->get('user_type');
        if ($userType && $userType == 'coordinator') {
            $isCoordinator = true;
            $this->ensureAccess(['coordinator-setup']);
        } else {
            $isCoordinator = false;
        }

        $responseArray = $this->permissionService->searchForStudents($organizationId, $personId, $requiresAppointmentPermission, $isCoordinator, $searchText);
        return new Response($responseArray);
    }

    /**
     * @ApiDoc(
     * resource = true,
     * description = "Get a list of a group's permissionsets.",
     * output ="",
     * statusCodes = {
     * 200 = "returned when successful",
     *
     * }
     * )
     *
     * @Rest\GET("/group/{groupId}/permissionset", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $groupId
     * @return Response
     * @deprecated
     */
    public function getGroupPermissionsListAction($groupId)
    {

    }
}