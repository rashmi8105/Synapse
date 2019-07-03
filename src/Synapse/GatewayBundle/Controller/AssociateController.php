<?php
namespace Synapse\GatewayBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Util\Constants\AssociateConstant;
use Synapse\GatewayBundle\Service\Impl\AssociateService;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Associate controller
 *
 * @package Synapse\GatewayBundle\Controller
 *
 *          @Rest\Prefix("associate")
 */
class AssociateController extends AbstractSynapseController
{

    /**
     * @var AssociateService
     *
     *      @DI\Inject(AssociateService::SERVICE_KEY)
     */
    private $associateService;

    /**
     * @var EbiConfigService
     *
     *      @DI\Inject(EbiConfigService::SERVICE_KEY)
     */
    private $ebiConfigService;


    /**
     * Creates an association.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Association",
     * output = "Symfony\Component\HttpFoundation\RedirectResponse",
     * section = "Associate",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="assoc_token")
     *
     * @param ParamFetcher $paramFetcher
     * @return RedirectResponse
     * @deprecated
     */
    public function createAssociationAction(ParamFetcher $paramFetcher)
    {
        $token = $paramFetcher->get('assoc_token');
        return $this->redirect($this->ebiConfigService->get(AssociateConstant::SYSTEM_URL) . '#/sso?assoc_token=' . $token);
    }

    /**
     * Authorizes an association.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Authorize association",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Associate",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/authorize", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="assoc_token")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated
     */
    public function authorizeAssociationAction(ParamFetcher $paramFetcher)
    {
        $token = $paramFetcher->get('assoc_token');
        if (! $this->getUser()) {
            return new Response([
                AssociateConstant::AUTHED => false,
                AssociateConstant::REDIRECT_URI => $this->ebiConfigService->get(AssociateConstant::SYSTEM_URL) . '#/login?assoc_token=' . $token
            ]);
        }
        
        $url = $this->associateService->createAssociation($token);
        if ($url) {
            return new Response([
                AssociateConstant::AUTHED => true,
                AssociateConstant::REDIRECT_URI => $url
            ]);
        }
        
        return new Response([
            AssociateConstant::AUTHED => false,
            AssociateConstant::REDIRECT_URI => $this->ebiConfigService->get(AssociateConstant::SYSTEM_URL) . '#/login'
        ]);
    }
}
