<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Service\Impl\PriorityStudentsService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;

/**
 * PriorityController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/priorityStudents")
 */
class PriorityController extends AbstractAuthController
{

    /**
     * @var PriorityStudentsService
     *     
     *      @DI\Inject(PriorityStudentsService::SERVICE_KEY)
     */
    private $priorityStudentsService;


    /**
     * Gets the my students dashboard page.
     * ToDo: Separate this into two APIs, one for the counts and one for the drilldown lists.
     * ToDo: Start using query parameters that make sense. Currently "filter" has value "risk-level" when fetching the list of all students, and is empty when drilling down on a risk color.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get My Students Dashboard",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Priority",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="filter", description="priority students list")
     * @QueryParam(name="level", requirements="\w+", description="risk level")
     * @QueryParam(name="page_number", requirements="\d+", default=1, description="Page number")
     * @QueryParam(name="records_per_page", requirements="\d+", default=25, description="Records per page")
     * @QueryParam(name="sort_by", description="Column to sort by (student_last_name|student_risk_status|student_intent_to_leave|student_classlevel|student_logins|last_activity) and direction to sort (desc is indicated by '-' preceding column name, asc is indicated by '+' or just the column name)")
     * @QueryParam(name="output-format", strict=false, requirements="csv|names_only", description="output format as csv, or only names for setting up a bulk action")
     * @QueryParam(name="onlyIncludeActiveStudents", requirements="true|false", description="true to hide in-active student")
     *
     * @Rest\View(statusCode=200)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getMyStudentsDashboardAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $filter = $paramFetcher->get('filter');
        $riskLevel = $paramFetcher->get('level');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $sortBy = $paramFetcher->get('sort_by');
        $outputFormat = $paramFetcher->get('output-format');
        $onlyIncludeActiveStudents = $paramFetcher->get('onlyIncludeActiveStudents');
        // convert to boolean
        $onlyIncludeActiveStudents = filter_var($onlyIncludeActiveStudents, FILTER_VALIDATE_BOOLEAN);
        if ($filter == 'risk-level') {
            $searchKey = 'my_students';
        } elseif ($filter == 'priority') {
            $searchKey = 'high_priority_students';
        } elseif (!empty($riskLevel)) {
            $searchKey = $riskLevel;
        }

        if (empty($searchKey)) {
            $response = $this->priorityStudentsService->getMyStudentsDashboard($loggedInUserId);
        } else {
            if ($outputFormat == 'csv') {
                $response = $this->priorityStudentsService->createMyStudentsJob($searchKey, $loggedInUserId, $organizationId, $sortBy, $onlyIncludeActiveStudents);
            } elseif ($outputFormat == 'names_only') {
                $response = $this->priorityStudentsService->getIdsAndNamesOfMyStudents($searchKey, $loggedInUserId, $organizationId, $onlyIncludeActiveStudents);
            } else {
                $response = $this->priorityStudentsService->getMyStudents($searchKey, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage, $onlyIncludeActiveStudents);
            }
        }
        return new Response($response);
    }
}