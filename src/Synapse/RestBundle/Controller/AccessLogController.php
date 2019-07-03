<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Service\Impl\AccessLogService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\AccessLogDto;
use Synapse\RestBundle\Entity\Response;


/**
 * AccessLogController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/accesslog")
 */
class AccessLogController extends AbstractAuthController
{

    /**
     * @var AccessLogService AccessLog service
     *     
     *      @DI\Inject(AccessLogService::SERVICE_KEY)
     */
    private $accessLogService;


    /**
     * Creates an access log.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates Access Log",
     * input = "Synapse\RestBundle\Entity\AccessLogDto",
     * output = "Synapse\RestBundle\Entity\AccessLogDto",
     * section = "Access Log",
     * statusCodes = {
     *                  201 = "Access log was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param AccessLogDto $accessLogDto
     * @return Response
     */
    public function createAccessLogAction(AccessLogDto $accessLogDto)
    {
        $accessLog = $this->accessLogService->createAccessLog($accessLogDto);
        return new Response($accessLog);
    }
}