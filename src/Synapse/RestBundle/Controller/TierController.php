<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\Service\Impl\CampusService;
use Synapse\MultiCampusBundle\Service\Impl\TierService;
use Synapse\RestBundle\Entity\Response;

/**
 * Class Tier Controller
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/tiers")
 */
class TierController extends AbstractAuthController
{

    /**
     * @var CampusService
     *
     *      @DI\Inject(CampusService::SERVICE_KEY)
     */
    private $campusService;

    /**
     * @var TierService
     *
     *      @DI\Inject(TierService::SERVICE_KEY)
     */
    private $tierService;

    /**
     * Creates a new Tier
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create New Tier",
     * input = "Synapse\MultiCampusBundle\EntityDto\TierDto",
     * output = "Synapse\CoreBundle\Entity\Organization",
     * section = "Tier",
     * statusCodes = {
     *                  201 = "Tier was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param TierDto $tierDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createTiersAction(TierDto $tierDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($tierDto, $errors), 400);
        } else {
            $tier = $this->tierService->createTier($tierDto);
            return new Response($tier, array());
        }
    }

    /**
     * Updates a tier.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update an Tier",
     * input = "Synapse\MultiCampusBundle\EntityDto\TierDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
     * statusCodes = {
     *                  204 = "Tier was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param TierDto $tierDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateTiersAction(TierDto $tierDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($tierDto, $errors), 400);
        } else {
            $tier = $this->tierService->updateTier($tierDto);
            return new Response($tier, array());
        }
    }

    /**
     * Gets a tier.
     *
     * @ApiDoc(
     * resource = true,
     * description = "View a Tier",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
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
     * @Rest\Get("/{id}", requirements={"_format"="json"})
     * @QueryParam(name="tier-level",requirements="(primary|secondary)", strict=true, description="Tier Level")
     * @Rest\View(statusCode=200)
     *
     * @param int $id //Tier ID
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getTierAction($id, ParamFetcher $paramFetcher)
    {   
        $this->ensureAdminAccess();
        $tierLevel = $paramFetcher->get(TierConstant::TIERLEVEL);
        $tierDetails = $this->tierService->viewTier($id, $tierLevel);
        return new Response($tierDetails);
    }

    /**
     * Get a tier list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List a Tier",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
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
     * @Rest\Get("/list", requirements={"_format"="json"})
     * @QueryParam(name="primary-tier-id", default="", description="Tier id")
     * @QueryParam(name="tier-level", requirements="(primary|secondary)", strict=true, description="Tier Level")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listTierAction(ParamFetcher $paramFetcher)
    {   
        $this->ensureAdminAccess();
        $primaryTierId = $paramFetcher->get('primary-tier-id');
        $tierLevel = $paramFetcher->get(TierConstant::TIERLEVEL);
        $listTierDetails = $this->tierService->listTier($primaryTierId, $tierLevel);
        return new Response($listTierDetails);
    }

    /**
     * Create Hierarchy Campus under Secondary Tier
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Hierarchy Campus",
     * input = "Synapse\MultiCampusBundle\EntityDto\CampusDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
     * statusCodes = {
     *                  201 = "Tier was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{tierId}/campuses",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="tierId", description="Tier Level")
     *
     * @param int $tierId
     * @param CampusDto $campusDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createHierarchyCampusAction($tierId, CampusDto $campusDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($campusDto, $errors), 400);
        } else {
            $campus = $this->campusService->createHierarchyCampus($tierId, $campusDto);
            return new Response($campus);
        }
    }

    /**
     * Update a campus' hierarchy level.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Campus Hierarchy",
     * input = "Synapse\MultiCampusBundle\EntityDto\CampusDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
     * statusCodes = {
     *                  204 = "Campus Hierarchy was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{tierId}/campus",requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     * @RequestParam(name="tierId", description="Tier Level")
     *
     * @param int $tierId
     * @param CampusDto $campusDto
     * @return Response
     */
    public function updateMoveHierarchyCampusAction($tierId, CampusDto $campusDto)
    {   
        $this->ensureAdminAccess();
        $campus = $this->campusService->updateMoveHierarchyCampus($tierId, $campusDto);
        return new Response($campus);
    }

    /**
     * View a campus' hierarchy.
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Campus Hierarchy",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
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
     * @Rest\Get("/{tierId}/campuses/{campusId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $tierId
     * @param int $campusId
     * @return Response
     */
    public function viewHierarchyCampusAction($tierId, $campusId)
    {
        $this->ensureAdminAccess();
        $campusDetail = $this->campusService->viewCampuses($tierId, $campusId);
        return new Response($campusDetail);
    }

    /**
     * Gets a list of the hierarchy of a campus.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Hierarchy List",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
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
     * @Rest\Get("/{tierId}/campuses", requirements={"_format"="json"})
     * @QueryParam(name="campus", description="Campus Type")
     * @QueryParam(name="filter", description="Campus name or Campus ID")
     * @Rest\View(statusCode=200)
     *
     * @param int $tierId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listHierarchyCampusAction($tierId, ParamFetcher $paramFetcher)
    {
        $this->ensureAdminAccess();
        $campusList = $this->campusService->listHierarchyCampus($tierId, $paramFetcher);
        return new Response($campusList);
    }

    /**
     * Deletes a secondary tier.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Secondary Tier",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
     * statusCodes = {
     *                  204 = "Secondary tier was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @QueryParam(name="tier-level", requirements="\w+", strict=true, description="Tier Level")
     * @Rest\View(statusCode=204)
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function deleteSecondaryTierAction($id, ParamFetcher $paramFetcher)
    {
        $this->ensureAdminAccess();
        $tierLevel = $paramFetcher->get(TierConstant::TIERLEVEL);
        $tier = $this->tierService->deleteSecondaryTier($id, $tierLevel);
        return new Response($tier);
    }

    /**
     * Delete campus hierarchy.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Hierarchy Campus",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tier",
     * statusCodes = {
     *                  204 = "Campus hierarchy was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{tierId}/campuses/{campusId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $tierId
     * @param int $campusId
     * @return Response
     */
    public function deleteHierarchyCampusAction($tierId, $campusId)
    {
        $this->ensureAdminAccess();
        $campus = $this->campusService->deleteHierarchyCampus($tierId, $campusId);
        return new Response($campus);
    }
}