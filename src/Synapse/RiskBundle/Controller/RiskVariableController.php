<?php
namespace Synapse\RiskBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Entity\Response;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Entity\Error;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\RiskBundle\EntityDto\RiskVariableDto;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\RiskBundle\Service\Impl\RiskVariableService;

/**
 * Class RiskVariableController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("riskvariables")
 */
class RiskVariableController extends AbstractAuthController
{

    /**
     * @var RiskVariableService
     *     
     *      @DI\Inject(RiskVariableService::SERVICE_KEY)
     */
    private $riskvariableService;

    /**
     * Creates a risk variable.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Risk Variable",
     * output = "Synapse\RiskBundle\EntityDto\RiskVariableDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskVariableDto",
     * section = "Risk Variables",
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
     * @param RiskVariableDto $riskVariableDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createRiskVariableAction(RiskVariableDto $riskVariableDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskVariableDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $riskVariables = $this->riskvariableService->create($riskVariableDto, 'insert');
            return new Response($riskVariables);
        }
    }

    /**
     * Updates a risk variable.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Risk Variable",
     * output = "Synapse\RiskBundle\EntityDto\RiskVariableDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskVariableDto",
     * section = "Risk Variables",
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
     * @Rest\View(statusCode=201)
     *
     * @param RiskVariableDto $riskVariableDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateRiskVariableAction(RiskVariableDto $riskVariableDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskVariableDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $riskVariables = $this->riskvariableService->create($riskVariableDto, 'update');
            return new Response($riskVariables);
        }
    }

    /**
     * Gets a list of risk variables by status.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Variables",
     * output = "Synapse\RiskBundle\EntityDto\RiskVariablesListDto",
     * section = "Risk Variables",
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
     * @QueryParam(name="status", description="risk variable status")
     * @QueryParam(name="status", requirements="(active|archived|Active|Archived)", strict=true, description="risk model status")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRiskVariablesAction(ParamFetcher $paramFetcher)
    {
        $status = strtolower($paramFetcher->get('status'));
        $riskVariables = $this->riskvariableService->getRiskVariables($status);
        return new Response($riskVariables);
    }

    /**
     * Gets a risk variable by its id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Variable",
     * output = "Synapse\RiskBundle\EntityDto\RiskVariableResponseDto",
     * section = "Risk Variables",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getRiskVariableAction($id)
    {
        $riskVariable = $this->riskvariableService->getRiskVariable($id);
        return new Response($riskVariable);
    }

    /**
     * Changes the status of a risk variable.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Change Risk Variable Status",
     * section = "Static List",
     * statusCodes = {
     *                  204 = "Resource(s) deleted. No representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $id
     */
    public function changeStatusRiskVariableAction($id)
    {
        $this->riskvariableService->changeStatus($id);
    }
}
?>