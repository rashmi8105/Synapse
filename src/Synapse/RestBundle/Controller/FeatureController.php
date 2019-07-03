<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\FeatureService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\FeatureDTO;
use Synapse\RestBundle\Entity\Response;

/**
 * Class FeatureController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/features")
 */
class FeatureController extends AbstractAuthController
{

    /**
     *
     * @var FeatureService
     *     
     *      @DI\Inject(FeatureService::SERVICE_KEY)
     */
    private $featureService;

    /**
     * Gets a list of campus features.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Features List",
     * section = "Features",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{organizationId}/{langid}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $organizationId
     * @param int $langid
     * @return Response
     */
    public function getCampusFeaturesAction($organizationId, $langid)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $feature = $this->featureService->getListFeatures($organizationId, $langid);
        return new Response($feature);
    }

    /**
     * Updates a feature.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Feature",
     * input = "Synapse\RestBundle\Entity\FeatureDTO",
     * output = "Synapse\RestBundle\Entity\FeatureDTO",
     * section = "Features",
     * statusCodes = {
     *                  201 = "Feature was updated. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param FeatureDTO $featureDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function updateFeatureAction(FeatureDTO $featureDTO, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($featureDTO, $errors), 400);
        } else {
            $feature = $this->featureService->updateFeatures($featureDTO);
            return new Response($feature);
        }
    }

    /**
     * Get all Features based on langid
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Features List",
     * section = "Features",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/list", requirements={"_format"="json"})
     * @QueryParam(name="langid", description="language id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listFeaturesAction(ParamFetcher $paramFetcher)
    {
        $langid = $paramFetcher->get('langid');
        $features = $this->featureService->listFeatures($langid);
        return new Response($features);
    }
}