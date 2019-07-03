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
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\SurveyBundle\EntityDto\FactorDto;
use Synapse\SurveyBundle\EntityDto\FactorReorderDto;
use Synapse\SurveyBundle\Service\Impl\FactorService;

/**
 * Class FactorController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/factors")
 */
class FactorsController extends AbstractAuthController
{

    /**
     * @var FactorService
     *
     * @DI\Inject(FactorService::SERVICE_KEY)
     */
    private $factorService;

    const SECURITY_CONTEXT = 'security.context';

    /**
     * Creates a new factor. Returns the new factor after it has been created.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Factor",
     * input = "Synapse\SurveyBundle\EntityDto\FactorDto",
     * output = "Synapse\SurveyBundle\EntityDto\FactorDto",
     * section = "Factors",
     * statusCodes = {
     *                  201 = "Factor was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param FactorDto $factorDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createFactorAction(FactorDto $factorDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($factorDto, $errors), 400);
        } else {
            $resp = $this->factorService->createFactor($factorDto);
            return new Response($resp);
        }
    }

    /**
     * Gets a list of factors.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Factor List",
     * section = "Factors",
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
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function listFactorAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
		$resp = $this->factorService->listFactor($organizationId, $loggedInUserId);
        return new Response($resp);
    }
    
    /**
     * Gets a list of factors based on user's permissions.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Factors Based on Permission",
     * section = "Factors",
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
     * @QueryParam(name="survey_id", strict=true, description="Survey id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listFactorOnPermissionAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $surveyId = $paramFetcher->get('survey_id');
        $resp = $this->factorService->listFactorOnPermission($organizationId, $loggedInUserId ,$surveyId);
        return new Response($resp);
    }

    /**
     * Update Api that re-orders factors.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Factors Based on Permission",
     * input = "Synapse\SurveyBundle\EntityDto\FactorReorderDto",
     * output = "Synapse\SurveyBundle\EntityDto\FactorReorderDto",
     * section = "Factors",
     * statusCodes = {
     *                  201 = "Factor order was updated. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/reorder", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param FactorReorderDto $factorReorderDto
     * @return Response
     */
    public function reorderFactorsAction(FactorReorderDto $factorReorderDto)
    {
        $resp = $this->factorService->reorderFactorSequence($factorReorderDto);
        return new Response($resp);
    }
    
    /**
     * Gets a list of factor questions.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Factor Questions",
     * section = "Factors",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function listFactorQuestionsAction($id)
    {
        $resp = $this->factorService->getFactorQuestions($id);
        return new Response($resp);
    }
    
    /**
     * Deletes a factor question.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Factor Question",
     * section = "Courses",
     * statusCodes = {
     *                  204 = "Factor question was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{factorId}/{ebiQuestionId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $factorId
     * @param int $ebiQuestionId
     * @return Response
     */
    public function deleteFactorQuestionAction($factorId, $ebiQuestionId)
    {
        $resp = $this->factorService->deleteFactorQuestion($factorId, $ebiQuestionId);
        return new Response($resp);
    }
    
    /**
     * Edits a factor. Returns the updated factor after the factor has been updated.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Factor",
     * input = "Synapse\SurveyBundle\EntityDto\FactorDto",
     * output = "Synapse\SurveyBundle\EntityDto\FactorDto",
     * section = "Factors",
     * statusCodes = {
     *                  201 = "Factor was updated. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param FactorDto $factorDto
     * @return Response
     */
    public function editFactorAction(FactorDto $factorDto)
    {
        $resp = $this->factorService->editFactor($factorDto);
        return new Response($resp);
    }
    
    /**
     * Deletes a factor.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Factor",
     * section = "Courses",
     * statusCodes = {
     *                  204 = "Factor was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $id
     * @return Response
     */
    public function deleteFactorAction($id)
    {
        $resp = $this->factorService->deleteFactor($id);
        return new Response($resp);
    }
}