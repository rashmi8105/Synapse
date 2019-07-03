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
use Synapse\SearchBundle\EntityDto\SharedSearchDto;
use Synapse\SearchBundle\Service\Impl\SharedSearchService;

/**
 * Class Appointments Controller
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/sharedsearches")
 */
class SharedSearchController extends AbstractAuthController
{

    /**
     * @var SharedSearchService
     *
     *      @DI\Inject(SharedSearchService::SERVICE_KEY)
     */
    private $sharedSearchService;

    /**
     * Creates a saved search.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create a Saved Search",
     * input = "Synapse\SearchBundle\Entity\SharedSearchDto",
     * output = "Synapse\SearchBundle\Entity\SharedSearchDto",
     * section = "Searches",
     * statusCodes = {
     *                  201 = "Shared Search was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param SharedSearchDto $sharedSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createSharedSearchAction(SharedSearchDto $sharedSearchDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($sharedSearchDto, $errors), 400);
        } else {
            $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
        }
        
        return new Response($sharedSearch);
    }

    /**
     * Gets a list of shared searches.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Shared Searches",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Searches",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getSharedSearchesAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $timezone = $this->getLoggedInUserOrganization()->getTimeZone();

        $sharedSearch = $this->sharedSearchService->getSharedSearches($loggedInUserId, $timezone, $organizationId);
        return new Response($sharedSearch);
    }

    /**
     * Edits a shared search
     *
     * API format shared
     * @ApiDoc(
     * resource = true,
     * description = "Edit Shared Search",
     * input = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * output = "Synapse\SearchBundle\Entity\SaveSearchDto",
     * section = "Searches",
     * statusCodes = {
     *                  204 = "Shared search was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param SaveSearchDto $saveSearchDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function editSharedSearchAction(SaveSearchDto $saveSearchDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($saveSearchDto, $errors), 400);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $editSharedSearch = $this->sharedSearchService->edit($saveSearchDto, $loggedInUserId);
        }
        
        return new Response($editSharedSearch);
    }

    /**
     * Deletes a shared search.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Shared Search",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Searches",
     * statusCodes = {
     *                  204 = "Shared search was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{searchId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     * @QueryParam(name="shared_search_id", description="Id of the shared search being deleted")
     * @QueryParam(name="shared_by_user_id", description="Id of the user that shared the search")
     *
     * @param int $searchId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function deleteSharedSearchAction($searchId, ParamFetcher $paramFetcher)
    {
        $shared_search_id = $paramFetcher->get('shared_search_id');
        $shared_by_user_id = $paramFetcher->get('shared_by_user_id');
        $deleteSearch = $this->sharedSearchService->delete($searchId, $shared_search_id, $shared_by_user_id);

        return new Response($deleteSearch);
    }
} 