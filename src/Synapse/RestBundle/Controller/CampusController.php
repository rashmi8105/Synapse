<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MultiCampusBundle\EntityDto\CampusChangeRequestDto;
use Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictDto;
use Synapse\MultiCampusBundle\Service\Impl\CampusService;
use Synapse\MultiCampusBundle\Service\Impl\TierUsersService;
use Synapse\MultiCampusBundle\Service\Impl\UserConflictsService;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\OrganizationDTO;
use Synapse\RestBundle\Entity\Response;

/**
 * Class CampusController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/campuses")
 */
class CampusController extends AbstractAuthController
{

    /**
     * @var CampusService campus service
     *
     *      @DI\Inject(CampusService::SERVICE_KEY)
     *
     */
    private $campusService;

    /**
     * @var OrganizationService organization service
     *     
     *      @DI\Inject(OrganizationService::SERVICE_KEY)
     *     
     */
    private $orgService;

    /**
     * @var OrganizationlangService
     *     
     *      @DI\Inject(OrganizationlangService::SERVICE_KEY)
     */
    private $organizationLangService;

    /**
     * @var TierUsersService tier_users service
     *     
     *      @DI\Inject(TierUsersService::SERVICE_KEY)
     */
    private $tierUsersService;

    /**
     * @var UserConflictsService users_conflicts service
     *     
     *      @DI\Inject(UserConflictsService::SERVICE_KEY)
     */
    private $userConflictsService;

    /**
     * Create a SoloCampus
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates a new Organization",
     * input = "Synapse\RestBundle\Entity\OrganizationDTO",
     * output = "Synapse\CoreBundle\Entity\OrganizationDTO",
     * section = "Campus",
     * statusCodes = {
     *                  201 = "Solo campus was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\View(statusCode=201)
     * @Rest\Post("", requirements={"_format"="json"})
     *
     * @param OrganizationDTO $organizationDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createSoloCampusAction(OrganizationDTO $organizationDTO, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureEBIorCoordinatorUserAccess($organizationDTO->getCampusid());
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($organizationDTO, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $organization = $this->orgService->createOrganization($organizationDTO);
            return new Response($organization, array());
        }
    }


    /**
     * Delete Campus
     *
     * @ApiDoc(
     * resource = true,
     * description = "Deletes Solo Campus",
     * section = "Campus",
     * statusCodes = {
     *                  204 = "Solo campus was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{campusId}", requirements={"_format"="json"})
     * @QueryParam(name="type",requirements="(standalone)", strict=true, description="User Type")
     * @Rest\View(statusCode=204)
     *
     * @param integer $campusId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function deleteSoloCampusAction($campusId, ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->ensureEBIorCoordinatorUserAccess($organizationId);
        $paramFetcher->get('type');
        $organization = $this->orgService->deleteOrganization($campusId);
        return new Response($organization);
    }

    /**
     * List change request
     *
     * @ApiDoc(
     * resource = true,
     * description = "List of change requests",
     * section = "Campus",
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
     * @Rest\Get("/changerequest", requirements={"_format"="json"})
     * @QueryParam(name="type",requirements="(received|sent)", strict=true, description="User Type")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated
     */
    public function listChangeRequestAction(ParamFetcher $paramFetcher)
    {           
        $type = $paramFetcher->get('type');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);

        $campusList = $this->campusService->listChangeRequest($type, $loggedInUserId, $organizationId);
        return new Response($campusList);
    }

    /**
     * List hierarchy campus coordinators
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Hierarchy campus coordinators",
     * section = "Campus",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/coordinators", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function listHierarchyCampusCoordinatorsAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);

        $coordinators = $this->tierUsersService->listCampusCoordinatorsAction($organizationId);
        return new Response($coordinators);
    }

    /**
     * Get the list of solo campuses
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get the list of solo campuses",
     * section = "Campus",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/{campusId}", requirements={"_format"="json"})
     * @QueryParam(name="type",requirements="(standalone)", strict=true, description="User Type")
     * @Rest\View(statusCode=200)
     *
     * @param integer $campusId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function viewSoloCampusAction($campusId, ParamFetcher $paramFetcher)
    {
        $orgDetails = $this->organizationLangService->getOrganization($campusId);
        return new Response($orgDetails);
    }

    /**
     * List hierarchy of the campus
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Hierarchy Campus",
     * section = "Campus",
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
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function listSoloCampusesAction()
    {
        $campusList = $this->campusService->listSoloCampuses();
        return new Response($campusList);
    }

    /**
     * Creates a change request
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Change Request",
     * input = "Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto",
     * output = "Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto",
     * section = "Campus",
     * statusCodes = {
     *                  201 = "Change request was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Post("/changerequest", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ChangeRequestDto $changeRequestDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return mixed
     * @return Response
     * @deprecated
     */
    public function createChangeRequestAction(ChangeRequestDto $changeRequestDto, ConstraintViolationListInterface $validationErrors)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($changeRequestDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {

            $changeRequest = $this->campusService->createChangeRequest($changeRequestDto);
            return new Response($changeRequest);
        }
    }

    /**
     * Update change request
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Change Request",
     * import = "Synapse\MultiCampusBundle\EntityDto\CampusChangeRequestDto",
     * output = "Synapse\MultiCampusBundle\EntityDto\CampusChangeRequestDto",
     * section = "Campus",
     * statusCodes = {
     *                  201 = "Change request was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("/changerequest", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param CampusChangeRequestDto $campusChangeRequestDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     * @deprecated
     */
    public function updateChangeRequestAction(CampusChangeRequestDto $campusChangeRequestDto, ConstraintViolationListInterface $validationErrors)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($campusChangeRequestDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $changeRequest = $this->campusService->updateChangeRequest($campusChangeRequestDto, $organizationId);
            return new Response($changeRequest);
        }
    }

    /**
     * Cancel change request
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete change request",
     * section = "Campus",
     * statusCodes = {
     *                  204 = "Change request was canceled. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Delete("/changerequest/{requestId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $requestId
     * @return Response
     * @deprecated
     */
    public function cancelChangeRequestAction($requestId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $organization = $this->campusService->deleteChangeRequest($requestId);
        return new Response($organization);
    }

    /**
     * @deprecated
     * List hierarchy campus coordinators
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Existing Campus Users",
     * section = "Campus",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/{campusId}/users", requirements={"_format"="json"})
     * @QueryParam(name="filter", description="Person firstname lastname email external ID")
     * @Rest\View(statusCode=200)
     *
     * @param integer $campusId
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated
     */
    public function listExistingCampusUsersAction($campusId, ParamFetcher $paramFetcher)
    {
        $filter = $paramFetcher->get('filter');
        $campusUsers = $this->tierUsersService->listPrimaryTierCoordinators($campusId, $filter);

        return new Response($campusUsers);
    }

    /**
     * Get Faculty/Student Conflict Record Details
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets Conflict User Details",
     * section = "Campus",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/conflict/{conflictId}", requirements={"_format"="json"})
     * @QueryParam(name="auto-resolve-id", description="Get the Tier Users for the tier level")
     * @Rest\View(statusCode=200)
     *
     * @param integer $conflictId
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated
     */
    public function getConflictUserDetailsAction($conflictId, ParamFetcher $paramFetcher)
    {
        $this->ensureAdminAccess();
        $autoResolveId = $paramFetcher->get('auto-resolve-id');
        $campusUsers = $this->userConflictsService->viewConflictUserDetails($conflictId, $autoResolveId);
        return new Response($campusUsers);
    }

    /**
     * Updates a resolve single conflict action.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Resolve Single Conflict",
     * input = "Synapse\MultiCampusBundle\EntityDto\ConflictDto",
     * output = "Synapse\MultiCampusBundle\EntityDto\ConflictDto",
     * section = "Campus",
     * statusCodes = {
     *                  201 = "Resolve single conflicts was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("/resolve", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ConflictDto $conflictDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     * @deprecated
     */
    public function updateResolveSingleConflictAction(ConflictDto $conflictDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($conflictDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $resolveConflict = $this->userConflictsService->updateResolveSingleConflict($conflictDto);
            return new Response($resolveConflict);
        }
    }

    /**
     * Update campus information from the Admin application
     *
     * @ApiDoc(
     * resource = true,
     * import = "Synapse\RestBundle\Entity\OrganizationDTO",
     * output = "Synapse\RestBundle\Entity\OrganizationDTO",
     * description = "Update Campus Information",
     * section = "Campus",
     * statusCodes = {
     *                  201 = "Solo campus was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @return mixed
     * @Rest\Put("", requirements={"_format"="json"})
     * @QueryParam(name="tier-level",requirements="standalone", strict=true, description="Tier Level")
     * @Rest\View(statusCode=201)
     *
     * @param OrganizationDto $organizationDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function updateSoloCampusAction(OrganizationDTO $organizationDTO, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureEBIorCoordinatorUserAccess($organizationDTO->getCampusid());
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($organizationDTO, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $organization = $this->organizationLangService->updateOrganization($organizationDTO);
            return new Response($organization, array());
        }
    }
}
