<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Service\Impl\PredefinedSearchService;
use Synapse\SearchBundle\Service\Impl\SearchService;

/**
 * Class Search Controller
 *
 * @package Synapse\RestBundle\Controller
 *         
 * @Rest\Prefix("/search")
 */
class SearchController extends AbstractAuthController
{

    /**
     * @var PredefinedSearchService
     *      @DI\Inject(PredefinedSearchService::SERVICE_KEY)
     */
    private $predefinedSearchService;

    /**
     * @var SearchService
     *      @DI\Inject(SearchService::SERVICE_KEY)
     */
    private $searchService;


    /**
     * Returns the results of a custom search.
     * Depending on the "output-format" query parameter, these results could be either a CSV or a page of results to be displayed in the UI.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates a Custom Search",
     * input = "Synapse\SearchBundle\EntityDto\SaveSearchDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Searches",
     * statusCodes = {
     *                  201 = "Custom search was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred ",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @QueryParam(name="page_number", requirements="\d+", strict=false, description="Page number")
     * @QueryParam(name="records_per_page", requirements="\d+", strict=false, description="Records per page")
     * @QueryParam(name="sort_by", strict=false, description="sorting field")
     * @QueryParam(name="output-format", requirements="csv", strict=false, description="output-format")
     * @Rest\View(statusCode=201)
     *
     * @param SaveSearchDto $customSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @param ParamFetcher $paramFetcher
     * @return View|Response
     */
    public function createCustomSearchAction(SaveSearchDto $customSearchDto, ConstraintViolationListInterface $validationErrors, ParamFetcher $paramFetcher)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($customSearchDto, $errors), 400);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $sortBy =  $paramFetcher->get('sort_by');
            $pageNumber = $paramFetcher->get('page_number');
            $recordsPerPage = $paramFetcher->get('records_per_page');
            $outputFormat = trim($paramFetcher->get('output-format'));
            if ($outputFormat == 'csv') {
                $response = $this->searchService->createCustomSearchJob($customSearchDto, $loggedInUserId, $organizationId, $sortBy);
            } else {
                $response = $this->searchService->getCustomSearchResults($customSearchDto, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage);
            }

            return new Response($response);
        }
    }


    /**
     * Returns a list of the students, including their ids and names, from a custom search or predefined search.
     * This list is used to set up a bulk action.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Returns a list of Students from a Custom/Pre-defined Search",
     * input = "Synapse\SearchBundle\EntityDto\SaveSearchDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Searches",
     * statusCodes = {
     *                  201 = "List of students was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred ",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/students", requirements={"_format"="json"})
     * @QueryParam(name="predefined_key", strict=false, description="Key for which predefined search to perform, if it's a predefined search")
     * @QueryParam(name="status", strict=false, requirements="(active|both)", description="Whether to only include active students")
     * @Rest\View(statusCode=201)
     *
     * @param SaveSearchDto $customSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @param ParamFetcher $paramFetcher
     * @return Response|View
     */
    public function getStudentsBasedSearchFiltersAction(SaveSearchDto $customSearchDto, ConstraintViolationListInterface $validationErrors, ParamFetcher $paramFetcher)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($customSearchDto, $errors), 400);
        } else {
            $predefinedKey = $paramFetcher->get('predefined_key');
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $status = $paramFetcher->get('status');
            if (isset($predefinedKey)) {
                $response = $this->predefinedSearchService->getPredefinedSearchStudentIdsAndNames($predefinedKey, $loggedInUserId, $organizationId);
            } else {
                $response = $this->searchService->getCustomSearchStudentIdsAndNames($customSearchDto, $loggedInUserId, $organizationId, $status);
            }

            return new Response($response);
        }
    }
}