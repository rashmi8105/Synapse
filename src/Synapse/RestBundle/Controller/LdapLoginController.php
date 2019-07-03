<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Response;

/**
 * LdapLoginController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/ldaplogin")
 */
class LdapLoginController extends AbstractSynapseController
{

    /**
     * @var OrganizationlangService
     *     
     *      @DI\Inject(OrganizationlangService::SERVICE_KEY)
     */
    private $organizationLangService;

    /**
     * Gets Ldap login details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Ldap Login Details",
     * section = "Ldap Login",
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
     * @QueryParam(name="subdomain", strict=true, description="subdomain")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getLdapLoginDetailsAction(ParamFetcher $paramFetcher)
    {
        $subdomain = $paramFetcher->get('subdomain');
        $ldapLogin = $this->organizationLangService->getLdapLoginDetails($subdomain);
        return new Response($ldapLogin);
    }
}