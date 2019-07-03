<?php
namespace Synapse\RestBundle\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Service\Impl\SavedSearchService;

/**
 * Class Savedsearches Controller
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/savedsearches")
 *
 */
class SavedSearchController extends AbstractAuthController
{

    /**
     * @var SavedSearchService saved search service
     *
     *      @DI\Inject(SavedSearchService::SERVICE_KEY)
     */
    private $savedSearchService;

    /**
     * Create (Save) Saved search parameters query
     *
     * @ApiDoc(
     * resource = true,
     * description = "Save Search Query Parameters",
     * input = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * output = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * section = "Saved Search",
     * statusCodes = {
     *                  201 = "Search was saved. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param SaveSearchDto $saveSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createSavedsearchAction(SaveSearchDto $saveSearchDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($saveSearchDto, $errors), 400);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $savedSearch = $this->savedSearchService->createSavedSearches($saveSearchDto, $loggedInUserId);
            return new Response($savedSearch);
        }
    }
    /**
     * Edits the saved search parameters query
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Saved Search Query Parameters",
     * input = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * output = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * section = "Saved Search",
     * statusCodes = {
     *                  201 = "Search parameter was edited. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param SaveSearchDto $saveSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function editSavedsearchAction(SaveSearchDto $saveSearchDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($saveSearchDto, $errors), 400);
        } else {
            $savedSearch = $this->savedSearchService->editSavedSearches($saveSearchDto);
            return new Response($savedSearch);
        }
    }
    /**
     * Cancel the saved search query parameter information
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cancel Saved Search Query Parameter Information",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Saved Search",
     * statusCodes = {
     *                  200 = "Saved search action was cancelled. Representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Delete("/{searchId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param integer $searchId
     * @return Response
     */
    public function cancelSavedsearchAction($searchId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $savedSearch = $this->savedSearchService->cancelSavedsearch($searchId, $loggedInUserId);
        return new Response($savedSearch);
    }
    /**
     * Get saved search query information.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Saved Search Information",
     * output = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * section = "Saved Search",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/{searchId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param integer $searchId
     * @return Response
     */
    public function getSavedSearchAction($searchId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $responseArray = $this->savedSearchService->getSavedSearch($searchId, $organizationId, $loggedInUserId);
        return new Response($responseArray);
    }
    /**
     * Returns list of saved search information
     *
     * @ApiDoc(
     * resource = true,
     * description = "Lists Saved Search Information",
     * output = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * section = "Saved Search",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function listSavedSearchAction()
    {
        $loggedInUser = $this->getLoggedInUser();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $timezone = $this->getLoggedInUserOrganization()->getTimeZone();
        $responseArray = $this->savedSearchService->listSavedSearch($loggedInUser, $organizationId, $timezone);
        return new Response($responseArray);
    }
}