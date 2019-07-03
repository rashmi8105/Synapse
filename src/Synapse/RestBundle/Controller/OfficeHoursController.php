<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;

/**
 * Class OfficeHoursController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("officehours")
 */
class OfficeHoursController extends AbstractAuthController
{

    /**
     *
     * @var StudentAppointmentService
     *
     *      @DI\Inject(StudentAppointmentService::SERVICE_KEY)
     */
    private $studentAppointmentService;
    
    /**
     * Gets a list of office hour slots for a faculty within a given campus.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Office Hours",
     * section = "Office Hours",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="facultyId", requirements="\d+", strict=true, description="Faculty Id")
     * @QueryParam(name="orgId", requirements="\d+", strict=true, description="Organization Id")
     * @QueryParam(name="filter", requirements="(today|week|month|term)", default ="week", strict=true, description="Filter")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getFacultyOfficeHoursAction(ParamFetcher $paramFetcher)
    {
        $organization = $this->getLoggedInUserOrganization();
        $timezone = $organization->getTimezone();
        $facultyId = $paramFetcher->get('facultyId');
        $org_id = $paramFetcher->get('orgId');
        $filter = $paramFetcher->get('filter');
        $result = $this->studentAppointmentService->getFacultyOfficeHours($org_id, $timezone, $facultyId, $filter);
        return new Response($result);
    }
}