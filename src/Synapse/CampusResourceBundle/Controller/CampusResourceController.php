<?php
namespace Synapse\CampusResourceBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CampusResourceBundle\EntityDto\CampusResourceDto;
use Synapse\CampusResourceBundle\Service\Impl\CampusResourceService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class CampusResourceController
 *
 * @package Synapse\CampusResourceBundle\Controller
 *
 * @Rest\Prefix("/campusresources")
 */
class CampusResourceController extends AbstractAuthController
{

    /**
     * @var Logger
     *
     * @DI\Inject("monolog.logger.api")
     */
    private $apiLogger;

    /**
     * @var CampusResourceService campusresource service
     *
     * @DI\Inject("campusresource_service")
     */
    private $campusResourceService;


    /**
     * Creates a new Campus Resource
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Campus Resource",
     * input = "Synapse\CampusResourceBundle\EntityDto\CampusResourceDto",
     * output = "Synapse\CampusResourceBundle\EntityDto\CampusResourceDto",
     * section = "Campus Resources",
     * statusCodes = {
     *  201 = "Campus Resource was created. Representation of resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param CampusResourceDto $campusResourceDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createCampusResourceAction(CampusResourceDto $campusResourceDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {

            return View::create(new Response($campusResourceDto, [
                $validationErrors[0]->getMessage()
            ]), 400);

        } else {

            $loggedInUser = $this->getUser();
            $loggedInUserId = $loggedInUser->getId();
            $organizationId = $loggedInUser->getOrganization()->getId();
            $requestJSON = $this->get("request")->getContent();

            $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; Request: $requestJSON");

            $campResCreateResponse = $this->campusResourceService->createCampusResource($campusResourceDto, $organizationId);

            return new Response($campResCreateResponse);
        }
    }

    /**
     * Updates an existing Campus Resource
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Campus Resource",
     * input = "Synapse\CampusResourceBundle\EntityDto\CampusResourceDto",
     * output = "Synapse\CampusResourceBundle\EntityDto\CampusResourceDto",
     * section = "Campus Resources",
     * statusCodes = {
     *  204 = "Campus Resource was updated. No representation of resource was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{campusResourceId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param CampusResourceDto $campusResourceDto
     * @param string|int $campusResourceId
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function updateCampusResourceAction(CampusResourceDto $campusResourceDto, $campusResourceId, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {

            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($campusResourceDto, $errors), 400);

        } else {

            $loggedInUser = $this->getUser();
            $loggedInUserId = $loggedInUser->getId();
            $organizationId = $loggedInUser->getOrganization()->getId();
            $requestJSON = $this->get("request")->getContent();

            $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; Request: $requestJSON");

            $campResUpdateResponse = $this->campusResourceService->updateCampusResource($campusResourceDto, $campusResourceId, $organizationId);

            return new Response($campResUpdateResponse);
        }
    }

    /**
     * Delete Campus Resource by campusresourceId
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Campus Resource",
     * section = "Campus Resources",
     * statusCodes = {
     *  204 = "Returned when successful",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{campusResourceId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param string|int $campusResourceId
     * @return Response
     */
    public function deleteCampusResourceAction($campusResourceId)
    {
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; CampusResourceId: $campusResourceId;");

        $deleteCampusResourceResponse = $this->campusResourceService->deleteCampusResource($campusResourceId, $organizationId, $loggedInUserId);

        return new Response($deleteCampusResourceResponse);
    }

    /**
     * Get a list of campus resources by OrgId
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus Resources by Organization",
     * section = "Campus Resources",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="orgId", requirements="\d+", strict=true, description="Organization Id")
     *
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCampusResourcesAction(ParamFetcher $paramFetcher)
    {
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $paramFetcher->get('orgId');

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId;");

        $getAllCampResResponse = $this->campusResourceService->getCampusResources($organizationId);

        return new Response($getAllCampResResponse);
    }

    /**
     * Get Campus Resource Details by campusresourceID
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Single Campus Resource Details by campusresourceID",
     * section = "Campus Resources",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{campusResourceId}", requirements={"_format"="json"})
     * @Rest\View()
     *
     * @param string|int $campusResourceId
     * @return Response
     */
    public function getCampusResourceDetailsAction($campusResourceId)
    {
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; CampusResourceId: $campusResourceId;");

        $campResDetResponse = $this->campusResourceService->getCampusResourceDetails($campusResourceId);

        return new Response($campResDetResponse);
    }
}
