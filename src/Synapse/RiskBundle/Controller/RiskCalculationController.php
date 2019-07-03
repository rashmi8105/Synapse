<?php
namespace Synapse\RiskBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;
use Synapse\RiskBundle\EntityDto\RiskScheduleDto;
use Synapse\RiskBundle\Service\Impl\RiskCalculationService;

/**
 * Class RiskCalculationController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("riskcalculations")
 */
class RiskCalculationController extends AbstractAuthController
{

    /**
     * @var RiskCalculationService
     *     
     *      @DI\Inject(RiskCalculationService::SERVICE_KEY)
     */
    private $riskCalculationService;

    /**
     * Creates input for a risk calculation.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Risk Calculation Input",
     * output = "Synapse\RiskBundle\EntityDto\RiskCalculationInputDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskCalculationInputDto",
     * section = "Risk Calculation",
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
     * @param RiskCalculationInputDto $riskCalculationInputDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createRiskCalculationInputAction(RiskCalculationInputDto $riskCalculationInputDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskCalculationInputDto, [
                $validationErrors[0]->getMessage()]), 400);
        } else {
            $riskVariables = $this->riskCalculationService->createRiskCalculationInput($riskCalculationInputDto);
            return new Response($riskVariables);
        }
    }

    /**
     * Gets calculated risk variables.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Calculated Risk Variables",
     * output = "Synapse\RiskBundle\EntityDto\CalculatedRiskVariableDto",
     * section = "Risk Calculation",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{personId}/riskvariables", requirements={"_format"="json"})
     * @QueryParam(name="start", strict=true, description="Start Date Time")
     * @QueryParam(name="end", strict=true, description="End Date Time")
     * @QueryParam(name="riskmodel", requirements="\d+", strict=true, description="Risk Model")
     * @QueryParam(name="org_id", requirements="\d+", strict=true, description="Organization Id")
     * @Rest\View(statusCode=200)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCalculatedRiskVariablesAction($personId, ParamFetcher $paramFetcher)
    {
        $start = $paramFetcher->get('start');
        $end = $paramFetcher->get('end');
        $riskModel = $paramFetcher->get('riskmodel');
        $org_id = $paramFetcher->get('org_id');
        $riskVariables = $this->riskCalculationService->getCalculatedRiskVariables($personId, $start, $end, $riskModel, $org_id);
        return new Response($riskVariables);
    }
    
    
    /**
     * Gets the risk score for a given person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Risk Score",
     * output = "Synapse\RiskBundle\EntityDto\PersonRiskScoresDto",
     * section = "Risk Calculation",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{personId}/riskscores", requirements={"_format"="json"})
     * @QueryParam(name="start", strict=true, description="Start Date Time")
     * @QueryParam(name="end", strict=true, description="End Date Time")
     * @QueryParam(name="riskmodel", requirements="\d+", strict=true, description="Risk Model")
     * @Rest\View(statusCode=200)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getRiskScoreAction($personId, ParamFetcher $paramFetcher)
    {
        $start = $paramFetcher->get('start');
        $end = $paramFetcher->get('end');
        $riskModel = $paramFetcher->get('riskmodel');
        
        $riskVariables = $this->riskCalculationService->getRiskScores($personId, $start, $end, $riskModel);
        return new Response($riskVariables);
    }
    

    /**
     * Schedules a new risk job.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Schedule Risk Job",
     * output = "Synapse\RiskBundle\EntityDto\RiskScheduleDto",
     * input = "Synapse\RiskBundle\EntityDto\RiskScheduleDto",
     * section = "Risk Calculation",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/schedule", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param RiskScheduleDto $riskScheduleDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function scheduleAction(RiskScheduleDto $riskScheduleDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($riskScheduleDto, [
                $validationErrors[0]->getMessage()]), 400);
        } else {
            $riskVariables = $this->riskCalculationService->scheduleRiskJob($riskScheduleDto);
            return new Response($riskVariables);
        }
    }
}