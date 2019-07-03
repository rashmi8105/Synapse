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
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\AuthenticationBundle\EntityDto\AuthConfigDto;
use Synapse\CoreBundle\Util\Helper;

/**
 * Settings controller
 *
 * @package Synapse\AuthenticationBundle\Controller
 *
 *
 * @Rest\Prefix("auth")
 *
 */
class SettingsController extends AbstractAuthController
{
    /**
     *
     * @DI\Inject("auth_config_service")
     */
    private $authConfigService;

    /**
     * Get auth settings
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         200 = "Returned when successful"
     *     }
     * )
     *
     * @Rest\Get("/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function getSettingsAction($orgId)
    {
        //$organization = $this->getUser()->getOrganization();
        $config = $this->authConfigService->getConfig($orgId);
        if ($config) {
            return new Response($config);
        }

        return new Error(404, 'No settings found');
    }

    /**
     * Store auth settings
     * @ApiDoc(
     *     resource = true,
     *     input = "Synapse\AuthenticationBundle\EntityDto\AuthConfigDto",
     *     statusCodes = {
     *         201 = "Returned when successful"
     *     }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @return \Synapse\RestBundle\Entity\Response
     */
    public function saveSettingsAction(AuthConfigDto $authConfigDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($authConfigDto, $errors), 400);
        } else {
            $config = $this->authConfigService->saveConfig($authConfigDto);

            return new Response($config);
        }
    }

}
