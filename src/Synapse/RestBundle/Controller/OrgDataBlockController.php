<?php
namespace Synapse\RestBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\RestBundle\Entity\Response;


/**
 * Class DataBlockController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/orgdatablock")
 */
class OrgDataBlockController extends AbstractAuthController
{

    /**
     * @var OrgPermissionsetService
     *     
     *      @DI\Inject(OrgPermissionsetService::SERVICE_KEY)
     */
    private $permissionsetService;

    /**
     * Get All Organization Related ISQ (Survey) data blocks.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get All Organization Related ISQ (Survey) data blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "PermissionSet",
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
     * @Rest\Get("/isq", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getIsqDataBlockAction()
    {   
        $this->ensureAccess(['coordinator-setup']);
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->permissionsetService->getDataBlocks($organizationId, 'survey');
        return new Response($result);
    }

    /**
     * Get All Organization Related ISP (Profile) data blocks
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get All Organization Related ISP (Profile) data blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "PermissionSet",
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
     * @Rest\Get("/isp", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getIspDataBlockAction()
    {
        $this->ensureAccess(['coordinator-setup']);
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->permissionsetService->getDataBlocks($organizationId, 'profile');
        return new Response($result);
    }
}