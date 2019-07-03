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
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\SearchBundle\Service\Impl\RiskService;

/**
 * Class RisksController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("risks")
 */
class RisksController extends AbstractAuthController
{
    /**
     * @var RiskService Risk service
     *     
     *      @DI\Inject(RiskService::SERVICE_KEY)
     */
    private $riskService;

    /**
     * Gets risk indicators.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Indicators",
     * output = "Synapse\SearchBundle\EntityDto\IntentToLeaveDto",
     * section = "Risks",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="type", description="risk indicator type , intent to leave")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRiskIndicatorsAction(ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        $riskIndicators = $this->riskService->getRiskIndicatorsOrIntentToLeave($type);
        return new Response($riskIndicators);
    }
}
?>