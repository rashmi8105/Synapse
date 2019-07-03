<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Class CampusConnectionController
 *
 * @package Synapse\RestBundle\Controller
 *
 *
 *          @Rest\Prefix("campusconnections")
 *
 */
class CampusConnectionController extends AbstractAuthController
{

    /**
     *
     * @var CampusConnectionService
     *
     *      @DI\Inject(CampusConnectionService::SERVICE_KEY)
     */
    private $campusConnectionService;

    /**
     * Assign a primary campus connection to a student(s)
     *
     * @ApiDoc(
     * resource = true,
     * input = "Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto",
     * output = "Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto",
     * description = "Assign Primary Campus Connection",
     * section = "Campus Connections",
     * statusCodes = {
     *                  201 = "Assign Primary Connection. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/primaryconnection", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param AssignPrimaryRequestDto $assignPrimaryRequestDto
     * @return Response
     */
    public function assignPrimaryConnectionAction(AssignPrimaryRequestDto $assignPrimaryRequestDto)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $response = $this->campusConnectionService->assignPrimaryConnection($assignPrimaryRequestDto, $loggedInUserId);

        return new Response($response);
    }

    /**
     * Get list of Student Faculty Connections
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Faculty Connections",
     * section = "Campus Connections",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/faculty", requirements={"_format"="json"})
     * @QueryParam(name="studentids", strict=true, description="comma seperated student ids")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentFacultyConnectionsAction(ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $studentIds = $paramFetcher->get('studentids');
        $facultyList = $this->campusConnectionService->getStudentFacultyConnections($organizationId, $studentIds);

        return new Response($facultyList);
    }

    /**
     * Reassign a primary campus connection to a student(s)
     *
     * @ApiDoc(
     * resource = true,
     * input = "Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto",
     * output = "Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto",
     * description = "Reassign Primary Campus Connection",
     * section = "Campus Connections",
     * statusCodes = {
     *                  201 = "Primary Connection was reassigned. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/primaryconnection", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param AssignPrimaryRequestDto $assignPrimaryRequestDto
     * @return Response
     */
    public function reassignPrimaryConnectionAction(AssignPrimaryRequestDto $assignPrimaryRequestDto)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $response = $this->campusConnectionService->assignPrimaryConnection($assignPrimaryRequestDto, $loggedInUserId);

        return new Response($response);
    }

    /**
     * List Students Campus Connections
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Students Campus Connections",
     * section = "Campus Connections",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{studentid}", requirements={"_format"="json"})
     * @QueryParam(name="orgId", strict=true, description="organziation id")
     * @Rest\View(statusCode=200)
     *
     * @param integer $studentid
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentCampusConnectionsAction($studentid, ParamFetcher $paramFetcher)
    {
        $this->checkAccessToStudent($studentid);
        $organizationId = $this->getLoggedInUserOrganizationId();
        $facultyList = $this->campusConnectionService->getStudentCampusConnections($organizationId, $studentid);

        return new Response($facultyList);
    }

    /**
     * Remove primary status from a student in a given organization
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cancel Primary Connection",
     * section = "Campus Connections",
     * statusCodes = {
     *                  204 = "Primary Connection was canceled. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/primaryconnection", requirements={"_format"="json"})
     * @QueryParam(name="studentId", requirements="\d+", default ="", strict=true, description="Student Id")
     * @Rest\View(statusCode=204)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function cancelPrimaryConnectionAction(ParamFetcher $paramFetcher)
    {
        $studentId = $paramFetcher->get('studentId');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->campusConnectionService->removePrimaryCampusConnection($organizationId, $studentId, $loggedInUserId);
    }
    
    /**
     * List Students Faculty Connections
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Faculty Connections",
     * section = "Campus Connections",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/faculty", requirements={"_format"="json"})
     * @RequestParam(name="studentids", strict=true, description="comma seperated student ids")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentsFacultyConnectionsAction(ParamFetcher $paramFetcher)
    {
    	$studentIds = $paramFetcher->get('studentids');

        $organizationId = $this->getLoggedInUserOrganizationId();
        $facultyList = $this->campusConnectionService->getStudentFacultyConnections($organizationId, $studentIds);

        return new Response($facultyList);
    }
}