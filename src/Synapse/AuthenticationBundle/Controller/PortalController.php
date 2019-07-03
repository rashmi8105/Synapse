<?php

namespace Synapse\AuthenticationBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
 * Portal controller
 *
 * @package Synapse\AuthenticationBundle\Controller
 *
 *
 * @Rest\Prefix("portal")
 *
 */
class PortalController extends AbstractSynapseController
{
    /**
     *
     * @DI\Inject("portal_auth_service")
     */
    private $portalAuthService;

    /**
     * Get portal
     *
     * @Rest\Get("/sso", requirements={"_format"="json"})
     * @QueryParam(name="orgToken", strict=true, description="Organization Token")
     * @QueryParam(name="personToken", strict=true, description="Person Token")
     * @Rest\View(statusCode=301)
     *
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function getPortalSsoAction(ParamFetcher $paramFetcher)
    {
        $orgToken = $paramFetcher->get('orgToken');
        $personToken = $paramFetcher->get('personToken');

        if (!$url = $this->portalAuthService->getAuth($orgToken, $personToken)) {
            $url = $this->getRequest()->headers->get('referer');
        }

        header("Location: $url");
        exit();
    }

}
