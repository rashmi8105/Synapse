<?php
namespace Synapse\HelpBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\HelpBundle\Service\Impl\ZendeskService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class ZendeskController
 *
 * @package Synapse\HelpBundle\Controller
 *
 * @Rest\Prefix("/zendesk")
 */
class ZendeskController extends AbstractAuthController
{

    /**
     * @var ZendeskService
     *
     *      @DI\Inject(ZendeskService::SERVICE_KEY)
     */
    private $zendeskService;

    /**
     * Redirect to SSO.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Redirect SSO",
     * output = "Symfony\Component\HttpFoundation\Request",
     * section = "Help",
     * statusCodes = {
     *                  301 = "Moved permanently",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/sso", requirements={"_format" = "json"})
     * @Rest\View(statusCode=301)
     *
     * @QueryParam(name="return_to")
     * @param ParamFetcher $paramFetcher
     * @return \Symfony\Component\HttpFoundation\Response
     * @deprecated
     */
    public function redirectSsoAction(ParamFetcher $paramFetcher)
    {
        $returnUrl = $paramFetcher->get('return_to');
        $organizationId = $this->getLoggedInUserOrganizationId();
        $url = $this->zendeskService->getSsoLoginUrl($returnUrl, $organizationId);
        $view = $this->redirectView($url, 301);

        return $this->handleView($view);
    }

    /**
     * Gets a user's SSO token.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get SSO Token",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Help",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/sso/url", requirements={"_format" = "json"})
     * @Rest\View(statusCode=200)
     *
     * @QueryParam(name="return_to")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSsoTokenUrlAction(ParamFetcher $paramFetcher)
    {
        if (!($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))) {
            throw new AccessDeniedException();
        }

        $returnUrl = $paramFetcher->get('return_to');
        $url = $this->zendeskService->getSsoTokenUrl($returnUrl);

        return new Response(['url' => $url]);
    }

}
