<?php

namespace Synapse\AuthenticationBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\AuthenticationBundle\Service\Impl\SAMLAuthService;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Entity\Error;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Synapse\RestBundle\Controller\AbstractSynapseController;

/**
 * SAML controller
 *
 * @package Synapse\AuthenticationBundle\Controller
 *
 *
 * @Rest\Prefix("saml")
 *
 */
class SamlController extends AbstractSynapseController
{
    /**
     *
     * @DI\Inject("saml_auth_service")
     *
     * @var SAMLAuthService
     */
    private $samlAuthService;

    /**
     * Get metadata
     *
     * @Rest\Get("/metadata/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function getMetadataAction($orgId)
    {
        if ($metadata = $this->samlAuthService->getMetadata($orgId)) {
            header('Content-Type: text/xml');
            echo $metadata;
            exit();
        }

    }

    /**
     * SSO
     *
     * @Rest\Get("/sso/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=301)
     *
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function doSsoAction($orgId)
    {
        $url = $this->samlAuthService->getRedirectURLForSAMLAuthentication($orgId);

        header("Location: $url");
        exit();
    }

    /**
     * Consume
     *
     * @Rest\Post("/consume/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function doConsumeAction($orgId)
    {
        $url = $this->samlAuthService->getRedirectURLForSAMLConsume($orgId);

        header("Location: $url");
        exit();
    }

    /**
     * SLO
     *
     * @Rest\Get("/slo/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=301)
     *
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function doSloAction($orgId)
    {
        $url = $this->samlAuthService->getRedirectURLForSAMLLogout($orgId);

        header("Location: $url");
        exit();
    }

}
