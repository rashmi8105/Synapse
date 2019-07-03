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
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;
use Synapse\SearchBundle\Service\Impl\PredefinedSearchService;

/**
 * PredefinedSearchController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/predefinedsearch")
 */
class PredefinedSearchController extends AbstractAuthController
{

    /**
     * @var PredefinedSearchService PredefinedSearchService
     *
     *      @DI\Inject(PredefinedSearchService::SERVICE_KEY)
     */
    private $predefinedSearchService;


    /**
     * Depending on the query parameters passed in, this API can be used for getting a list of predefined searches or performing a predefined search.
     * TODO: Create a new API for getting a list of predefined searches, and use this one only for performing a predefined search.
     * TODO: Remove unnecessary query parameters.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Predefined Search",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Predefined Search",
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
     * @Rest\GET("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="type", strict=false, description="not used anymore")
     * @QueryParam(name="category", strict=false, requirements="student_search|academic_update_search|activity_search", description="Category of the predefined search, used when getting the list of searches")
     * @QueryParam(name="facultyid", strict=false, description="useless, but since it's passed in, we'll validate it")
     * @QueryParam(name="sub_category_key", strict=false, description="Key used to determine which predefined search to perform")
     * @QueryParam(name="page_number", requirements="\d+", default=1, description="Page number")
     * @QueryParam(name="records_per_page", requirements="\d+", default=25, description="Records per page")
     * @QueryParam(name="sort_by", description="Column to sort by (student_last_name|student_risk_status|student_intent_to_leave|student_classlevel|student_logins|last_activity) and direction to sort (desc is indicated by '-' preceding column name, asc is indicated by '+' or just the column name)")
     * @QueryParam(name="output-format", strict=false, requirements="csv", description="output format as csv")
     * @QueryParam(name="onlyIncludeActiveStudents", requirements="true|false", description="true to hide in-active student")
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getPredefinedSearchAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $facultyId = $paramFetcher->get('facultyid');

        if ($loggedInUserId != $facultyId) {
            throw new AccessDeniedException();
        }

        $category = $paramFetcher->get('category');
        $predefinedSearchKey = $paramFetcher->get('sub_category_key');
        $pageNumber = $paramFetcher->get('page_number');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $sortBy = $paramFetcher->get('sort_by');
        $outputFormat = $paramFetcher->get('output-format');
        $onlyIncludeActiveStudents = $paramFetcher->get('onlyIncludeActiveStudents');
        // convert to boolean
        $onlyIncludeActiveStudents = filter_var($onlyIncludeActiveStudents, FILTER_VALIDATE_BOOLEAN);
        if (!empty($predefinedSearchKey)) {
            if ($outputFormat == 'csv') {
                $response = $this->predefinedSearchService->createPredefinedSearchJob($predefinedSearchKey, $facultyId, $organizationId, $sortBy, $onlyIncludeActiveStudents);
            } else {
                $response = $this->predefinedSearchService->getPredefinedSearchResults($predefinedSearchKey, $facultyId, $organizationId, $sortBy, $pageNumber, $recordsPerPage, $onlyIncludeActiveStudents);
            }
        } else {
            $response = $this->predefinedSearchService->getPredefinedSearchListByCategory($category, $facultyId);
        }
        return new Response($response);
    }

}