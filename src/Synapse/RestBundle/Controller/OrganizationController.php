<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\PasswordService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Converter\DTOConverter;
use Synapse\RestBundle\Converter\LogoDtoConverter;
use Synapse\RestBundle\Converter\OrganizationDTOConverter;
use Synapse\RestBundle\Entity\CoordinatorDTO;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\LogoDto;
use Synapse\RestBundle\Entity\OrganizationDTO;
use Synapse\RestBundle\Entity\OrganizationProfileDTO;
use Synapse\RestBundle\Entity\OrgGroupDto;
use Synapse\RestBundle\Entity\OrgStmtUpdateDto;
use Synapse\RestBundle\Entity\Response;


/**
 * Class ProfileController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/organization")
 */
class OrganizationController extends AbstractAuthController
{

    /**
     * @var OrganizationService
     *     
     *      @DI\Inject(OrganizationService::SERVICE_KEY)
     */
    private $orgService;

    /**
     * @var OrganizationlangService
     *     
     *      @DI\Inject(OrganizationlangService::SERVICE_KEY)
     */
    private $organizationLangService;

    /**
     * @var OrgPermissionsetService
     *     
     *      @DI\Inject(OrgPermissionsetService::SERVICE_KEY)
     */
    private $permissionsetService;

    /**
     * @var PersonService
     *     
     *      @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;

    /**
     * @var CalendarFactoryService
     *     
     *      @DI\Inject(CalendarFactoryService::SERVICE_KEY)
     */
    private $calendarFactoryService;

    /**
     * @var LogoDtoConverter
     *
     *      @DI\Inject("logodto_converter")
     */
    private $logoDtoConverter;

    /**
     * Creates a new organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create New Organization",
     * input = "Synapse\RestBundle\Entity\OrganizationDTO",
     * section = "Organization",
     * statusCodes = {
     *                  201 = "Organization was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were either errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OrganizationDTO $organizationDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     * @deprecated
     */
    public function createOrganizationAction(OrganizationDTO $organizationDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($organizationDTO, $errors), 400);
        } else {
            $organization = $this->orgService->createOrganization($organizationDTO);
            return new Response($organization, array());
        }
    }

    /**
     * Gets a list of all organizations within mapworks.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get List of Organizations",
     * section = "Organization",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were either errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     * @deprecated
     */
    public function getOrganizationsAction()
    {
        $orgList = $this->organizationLangService->getOrganizations();
        return new Response($orgList);
    }

    /**
     * Gets an organization by it's $orgId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Organization",
     * section = "Organization",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param Organization $orgId
     * @return Response
     * @deprecated
     */
    public function getOrganizationAction($orgId)
    {
        $orgDetails = $this->organizationLangService->getOrganization($orgId);
        return new Response($orgDetails);
    }

    /**
     * Deletes an organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Organization",
     * section = "Organization",
     * statusCodes = {
     *                  204 = "Organization was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were either errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $orgId
     * @return Response
     * @deprecated
     */
    public function deleteOrganizationAction($orgId)
    {
        $organization = $this->orgService->deleteOrganization($orgId);
        return new Response($organization);
    }

    /**
     * Gets an organization's details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Organization's Details",
     * section = "Organization",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/logo/{organizationId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $organizationId
     * @return Response
     */
    public function getOrganizationDetailsAction($organizationId)
    {
        $details = $this->orgService->getOrganizationDetails($organizationId);
        if (!$details instanceof Error) {
            $numberOfUsersEnabledCalendar = $this->calendarFactoryService->getCountOfCalendarSyncUsers($organizationId);
            $details = $this->logoDtoConverter->createLogoResponse($details, $numberOfUsersEnabledCalendar);
        }
        return new Response($details);
    }

    /**
     * Update an organization's details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Organization Details",
     * input = "Synapse\RestBundle\Entity\LogoDto",
     * output = "Synapse\RestBundle\Entity\LogoDto",
     * section = "Organization",
     * statusCodes = {
     *                  201 = "Organization details were updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were either errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/metadata/customization", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param LogoDto $logoDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function updateOrganizationDetailsAction(LogoDto $logoDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($logoDto, $errors), 400);
        } else {
            $organization = $this->orgService->updateOrganizationDetails($logoDto);
            if (!$organization instanceof Error) {
                $organization = $this->logoDtoConverter->createLogoResponse($organization);
            }
            return new Response($organization);
        }
    }

    /**
     * Gets all active permissionsets.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get All Active Permissionsets",
     * section = "Organization",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were either errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}/permission", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getActivePermissionsetAction($orgId)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $persmissionset = $this->permissionsetService->getActivePermissionset($orgId);
        return new Response($persmissionset);
    }

    /**
     * Updates the custom confidentiality statement for an organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Confidentiality Statement",
     * input = "Synapse\RestBundle\Entity\OrgStmtUpdateDto",
     * output = "Synapse\RestBundle\Entity\OrgStmtUpdateDto",
     * section = "Organization",
     * statusCodes = {
     *                  201 = "Confidentiality statement updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{orgId}/custom_confid_stmt", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OrgStmtUpdateDto $orgStmtUpdateDto
     * @return Response
     */
    public function updateCustomConfidStmtAction(OrgStmtUpdateDto $orgStmtUpdateDto)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $group = $this->orgService->updateCustomConfidStmt($orgStmtUpdateDto);
        return new Response($group);
    }

    /**
     * Get's an organization's custom confidentiality statement.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Confidentiality Statement",
     * section = "Organization",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were either errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}/custom_confid_stmt", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getCustomConfidStmtAction($orgId)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $group = $this->orgService->getCustomConfidStmt($orgId);
        return new Response($group);
    }

    /**
     * Gets dashboard overview counts for a user within an organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get all list of users by type",
     * section = "Organization",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were either errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}/overview", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getOverviewAction($orgId)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $person_id = $this->getUser()->getId();
        $searchList = $this->orgService->getOverview($orgId, $person_id);
        return new Response($searchList);
    }
}