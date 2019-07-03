<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\UploadBundle\Service\Impl\S3PolicyService;

/**
 * Class StorageController
 *
 * @package Synapse\RestBundle\Controller
 *
 * @Rest\Prefix("/storage")
 */
class StorageController extends AbstractAuthController
{
    
    /**
     *
     *      @DI\Inject(SynapseConstant::LOGGER_KEY)
     */
    private $logger;
    
    /**
     * S3 pre-signed policy URL service
     *
     * @var S3PolicyService
     *
     *      @DI\Inject(S3PolicyService::SERVICE_KEY)
     */
    private $s3PolicyService;


    /**
     * Creates pre-signed upload URLs
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates Pre-Signed Upload URLs ",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Storage",
     * statusCodes = {
     *                  201 = "Pre-signed upload urls were created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/signupload")
     * @Rest\View(statusCode=201)
     *
     * @RequestParam(name="file")
     * @return Response
     * @deprecated
     */
    public function createUploadPolicyAction()
    {

        $key = $paramFetcher->get('file');
        $this->logger->debug("Create Upload Policy - key - ".$key);
        $url = $this->s3PolicyService->getSecureUploadUrl($file);
        $this->logger->debug("Create Upload Policy - URL - ".$url);
        return new Response(['url' => $url], []);
    }

    /**
     * Creates pre-signed download URLs
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates Pre-Signed Download URLs ",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Storage",
     * statusCodes = {
     *                  201 = "Pre-signed download urls were created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/signdownload")
     * @Rest\View(statusCode=201)
     *
     * @RequestParam(name="file")
     * @return Response
     * @deprecated
     */
    public function createDownloadPolicyAction()
    {
        $key = $paramFetcher->get('file');
        $this->logger->debug("Create Download Action - key - ".$key);
        $url = $this->s3PolicyService->getSecureUrl($file);
        $this->logger->debug("Create Download Action - URL - ".$url);
        return new Response(['url' => $url], []);
    }
}
?>