<?php
namespace Synapse\RiskBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RiskBundle\EntityDto\RiskModelDto;
use Synapse\RiskBundle\Service\Impl\RiskModelCreateService;
use Synapse\RiskBundle\Service\Impl\RiskModelListService;

/**
 * Class RiskModelController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("riskmodels")
 */
class RiskModelController extends AbstractAuthController
{
    
    /**
     * @var RiskModelListService
     *
     *      @DI\Inject(RiskModelListService::SERVICE_KEY)
     */
    private $riskModelListService;
    
    /**
     * @var RiskModelCreateService
     *     
     *      @DI\Inject(RiskModelCreateService::SERVICE_KEY)
     */
    private $riskModelCreateService;

    /**
     * Creates a risk model.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Risk Model",
     * output = "Synapse\RiskBundle\EntityDto\RiskModelDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskModelDto",
     * section = "Risk Model",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param RiskModelDto $riskModelDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createRiskModelAction(RiskModelDto $riskModelDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskModelDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $riskVariables = $this->riskModelCreateService->createModel($riskModelDto);
            return new Response($riskVariables);
        }
    }    
    /**
     * Gets a list of risk models.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Models",
     * output = "Synapse\RiskBundle\EntityDto\RiskModelListDto",
     * section = "Risk Model",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="status", requirements="(Active|Archived)", strict=true, description="risk model status")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRiskModelsAction(ParamFetcher $paramFetcher)
    {
        $status = $paramFetcher->get('status');
        $riskModels = $this->riskModelListService->getModelList($status);
        return new Response($riskModels);
    }
    
    /**
     * Updates a risk model.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Risk Model",
     * output = "Synapse\RiskBundle\EntityDto\RiskModelDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskModelDto",
     * section = "Risk Model",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param RiskModelDto $riskModelDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateRiskModelAction(RiskModelDto $riskModelDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskModelDto, [
                $validationErrors[0]->getMessage()]), 400);
        } else {
            $riskVariables = $this->riskModelCreateService->updateModel($riskModelDto);
            return new Response($riskVariables);
        }
    }
    
    /**
     * Gets a risk model by its id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Models",
     * output = "Synapse\RiskBundle\EntityDto\RiskModelListDto",
     * section = "Risk Model",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{id}", requirements={"_format"="json","id" = "\d+"}) 
     * @QueryParam(name="viewmode", requirements="json|csv", default ="json", strict=true, description="view mode")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRiskModelAction($id, ParamFetcher $paramFetcher)
    {        
        $viewType = $paramFetcher->get('viewmode');
        $riskModel = $this->riskModelListService->getModel($id,$viewType);
        return new Response($riskModel);
    }
    /**
     * Gets risk model assignments.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Model Assignments",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Risk Model",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/assignments", requirements={"_format"="json"})
     * @QueryParam(name="filter", requirements="(all|no-group|no-model|group-model)", default="all", strict=true, description="risk model status")
     * @QueryParam(name="viewmode", requirements="json|csv", default ="json", strict=true, description="view mode")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRiskModelsAssignmentsAction(ParamFetcher $paramFetcher)
    {
        $viewType = $paramFetcher->get('viewmode');
        $filter = $paramFetcher->get('filter');
        
        $riskModelsAssignments = $this->riskModelListService->getModelAssignments($filter, $viewType);
        return new Response($riskModelsAssignments);
    }
}