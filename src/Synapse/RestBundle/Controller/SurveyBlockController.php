<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\SurveyBundle\EntityDto\SurveyBlockDto;
use Synapse\SurveyBundle\Service\Impl\SurveyBlockService;

/**
 * SurveyBlockController
 *
 * @package Synapse\RestBundle\Controller
 *          @Rest\Prefix("/surveyblocks")
 */
class SurveyBlockController extends AbstractAuthController
{

    /**
     * @var SurveyBlockService
     *
     *      @DI\Inject(SurveyBlockService::SERVICE_KEY)
     */
    private $survey;

    /**
     * Get all survey Blocks
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get All Survey Blocks",
     * output = "Synapse\SurveyBundle\EntityDto\SurveyBlockDto",
     * section = "Survey Block",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *        
     * @return Response
     */
    public function getSurveyBlocksAction()
    {
        $this->ensureAdminAccess();

        $surveyBlocks = $this->survey->getSurveyBlocks();
        return new Response($surveyBlocks);
    }

    /**
     * Create Survey Block
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Connections for Student and Campus",
     * input = "Synapse\SurveyBundle\EntityDto\SurveyBlockDto",
     * output = "Synapse\SurveyBundle\EntityDto\SurveyBlockDto",
     * section = "Survey Block",
     * statusCodes = {
     *                  201 = "Survey block was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param SurveyBlockDto $surveyBlockDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createSurveyBlockAction(SurveyBlockDto $surveyBlockDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($surveyBlockDto, $errors), 400);
        } else {
            $resp = $this->survey->createSurveyBlock($surveyBlockDto);
        }
        
        return new Response($resp);
    }

    /**
     * Get Survey Block Details
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Block Details",
     * output = "Synapse\SurveyBundle\EntityDto\SurveyBlockDetailsResponseDto",
     * section = "Survey Block",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getSurveyBlockDetailsAction($id)
    {
        $this->ensureAdminAccess();
        $surveyBlockDetails = $this->survey->getSurveyBlockDetails($id);
        return new Response($surveyBlockDetails);
    }

    /**
     * Delete survey block
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Survey Block",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey Block",
     * statusCodes = {
     *                  200 = "Resource(s) deleted. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function deleteSurveyBlockAction($id)
    {
        $this->ensureAdminAccess();
        $surveyBlock = $this->survey->deleteSurveyBlock($id);
        return new Response($surveyBlock);
    }

    /**
     * Delete survey block question
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Survey Block",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey Block",
     * statusCodes = {
     *                  200 = "Resource(s) deleted. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{id}/data/{dataid}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @param int $dataid
     * @return Response
     */
    public function deleteSurveyBlockQuestionAction($id, $dataid)
    {
        $this->ensureAdminAccess();
        $surveyBlockQuestion = $this->survey->deleteSurveyBlockQuestion($id, $dataid);
        return new Response($surveyBlockQuestion);
    }

    /**
     * Edit survey block.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Survey Block",
     * input = "Synapse\SurveyBundle\EntityDto\SurveyBlockDto",
     * output = "Synapse\SurveyBundle\EntityDto\SurveyBlockDto",
     * section = "Survey Block",
     * statusCodes = {
     *                  201 = "Resources(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param SurveyBlockDto $surveyBlockDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function editSurveyBlockAction(SurveyBlockDto $surveyBlockDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($surveyBlockDto, $errors), 400);
        } else {
            $surveyBlock = $this->survey->editSurveyBlock($surveyBlockDto);
            return new Response($surveyBlock);
        }
    }
}