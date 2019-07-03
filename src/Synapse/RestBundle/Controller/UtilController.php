<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\ActivityCategoryService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RiskBundle\Service\Impl\RiskVariableService;

/**
 * Class UtilController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("")
 */
class UtilController extends AbstractAuthController
{
    /**
     * @var ActivityCategoryService
     *
     *      @DI\Inject(ActivityCategoryService::SERVICE_KEY)
     */
    private $activityCategoryService;

    /**
     * @var OrganizationService
     *
     *      @DI\Inject(OrganizationService::SERVICE_KEY)
     */
    private $organizationService;

    /**
     * @var RiskVariableService
     *
     *      @DI\Inject(RiskVariableService::SERVICE_KEY)
     */
    private $riskVariableService;

    /**
     * Get timezones.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Timezones",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Util",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/timezone", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getTimezonesAction()
    {
        $timezone_list = $this->organizationService->getTimezones();
        return new Response($timezone_list);
    }

    /**
     * Get ReasonCategories
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Reason Categories",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Util",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/reasonCategories", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getReasonCategoriesAction()
    {
        $reason_category_list = $this->activityCategoryService->getActivityCategory();
        return new Response($reason_category_list);
    }

    /**
     * Get a list of campuses.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus List",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Util",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/campusids", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getListCampusesAction()
    {
        $listCampuses = $this->organizationService->getListCampuses();
        return new Response($listCampuses);
    }

    /**
     * Get resource types
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Resource Types",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Util",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/risksourcetypes", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getResourceTypesAction()
    {
        $resourceTypes = $this->riskVariableService->getResourceTypes();
        return new Response($resourceTypes);
    }

    /**
     * Gets resource types Ids.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Resource Types Ids",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Util",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/risksourceids", requirements={"_format"="json"})
     * @QueryParam(name="type", description="profile,surveyquestion,surveyfactor,ISP,ISQ,questionbank")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getResourceIdsAction(ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        $resourceTypes = $this->riskVariableService->getRiskSourceIds($type);
        return new Response($resourceTypes);
    }
}
?>