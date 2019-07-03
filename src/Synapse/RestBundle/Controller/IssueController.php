<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\SynapseConstant;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\SurveyBundle\EntityDto\IssueCreateDto;
use Synapse\SurveyBundle\Service\Impl\IssueService;

/**
 * IssueController
 *
 * @package Synapse\RestBundle\Controller
 *          @Rest\Prefix("/issues")
 */
class IssueController extends AbstractAuthController
{

    /**
     * @var IssueService
     *
     *      @DI\Inject(IssueService::SERVICE_KEY)
     */
    private $issueService;

    /**
     * Admin Api to get issues based on a surveyId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Issues",
     * section = "Issues",
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
     * @QueryParam(name="surveyId", description="Survey Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listIssuesAction(ParamFetcher $paramFetcher)
    {
        $surveyId = $paramFetcher->get('surveyId');
        $response = $this->issueService->listIssues($surveyId);
        return new Response($response);
    }

    /**
     * Deletes an issue based on its issueId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Issue",
     * section = "Issues",
     * statusCodes = {
     *                  204 = "Issue was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("", requirements={"_format"="json"})
     * @QueryParam(name="issueId", description="Issue Id")
     * @Rest\View(statusCode=204)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */

    public function deleteIssueAction(ParamFetcher $paramFetcher)
    {
        $issueId = $paramFetcher->get('issueId');
        $response = $this->issueService->deleteIssue($issueId);
        return new Response($response);
    }

    /**
     * Admin Api to create a new issue.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create new issue",
     * input = "Synapse\SurveyBundle\EntityDto\IssueCreateDto",
     * output = "Synapse\SurveyBundle\EntityDto\IssueCreateDto",
     * section = "Issues",
     * statusCodes = {
     *                  201 = "Issue was created. Representation of resource(s) was returned",
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
     * @param IssueCreateDto $issueCreateDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createIssueAction(IssueCreateDto $issueCreateDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($issueCreateDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $response = $this->issueService->createIssue($issueCreateDto);
            return new Response($response);
        }
    }

    /**
     * Admin Api to upload an icon for a specific issue.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Upload Issue Icon",
     * section = "Issues",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/upload/{issueId}", requirements={"_format"="json"})
     * @QueryParam(name="file_name", strict=false, description="file Name")
     * @Rest\View(statusCode=200)
     *
     * @param integer $issueId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function uploadAction($issueId, ParamFetcher $paramFetcher)
    {
        $fileName = $paramFetcher->get('file_name');
        $response = $this->issueService->uploadIssueIcon($issueId, $fileName);
        return new Response($response);
    }

    /**
     * Admin Api to edit issue based on issueId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Issue",
     * input = "Synapse\SurveyBundle\EntityDto\IssueCreateDto",
     * output = "Synapse\SurveyBundle\EntityDto\IssueCreateDto",
     * section = "Issues",
     * statusCodes = {
     *                  201 = "Issue was edited. Representation of resource(s) was returned",
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
     * @param IssueCreateDto $issueCreateDto
     * @return Response
     */
    public function editIssueAction(IssueCreateDto $issueCreateDto)
    {
        $response = $this->issueService->editIssue($issueCreateDto);
        return new Response($response);
    }

    /**
     * Gets an issue by its issueId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit issue based on issueId",
     * output = "Synapse\SurveyBundle\EntityDto\IssueCreateDto",
     * section = "Issues",
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
     * @param integer $id
     * @return Response
     */
    public function getIssueAction($id)
    {
        $response = $this->issueService->getIssue($id);
        return new Response($response);
    }

}