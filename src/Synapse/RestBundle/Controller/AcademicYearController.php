<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\AcademicBundle\EntityDto\AcademicYearDto;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;

/**
 * AcademicYearController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/academicyears")
 */
class AcademicYearController extends AbstractAuthController
{

    /**
     * @var AcademicYearService
     *     
     *      @DI\Inject(AcademicYearService::SERVICE_KEY)
     */
    private $academicyearService;


    /**
     * Creates an academic year.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates Academic Year",
     * input = "Synapse\AcademicBundle\EntityDto\AcademicYearDto",
     * output = "Synapse\AcademicBundle\EntityDto\AcademicYearDto",
     * section = "Academic Year",
     * statusCodes = {
     *                  201 = "Academic year was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param AcademicYearDto $academicYearDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createAcademicyearAction(AcademicYearDto $academicYearDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($academicYearDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUser = $this->getLoggedInUser();
            $academicYear = $this->academicyearService->createAcademicyear($academicYearDto, $loggedInUser);
           }
        
        return new Response($academicYear);
    }

    /**
     * Lists org academic years.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Lists Academic Year Id's",
     * section = "Academic Year",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\GET("/year", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function listYearAction()
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $organizationId = $this->getLoggedInUserOrganizationId();
        $year = $this->academicyearService->listYear($organizationId);

        return new Response($year);
    }

    /**
     * Get a list of all the academic years for the organization
     *
     * @ApiDoc(
     * resource = true,
     * description = "Lists All Academic Years",
     * section = "Academic Year",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}", requirements={"_format"="json"})
     * @QueryParam(name="exclude_future_years", requirements="true", description="exclude future academic years from the list")
     * @QueryParam(name="exclude_past_years", requirements="true", default ="false", description="exclude past academic years from the list")
     * @Rest\View()
     *
     * @param integer $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listAcademicYearsAction($orgId, ParamFetcher $paramFetcher)
    {
        $excludeFutureYears = filter_var($paramFetcher->get('exclude_future_years'), FILTER_VALIDATE_BOOLEAN);     // Convert string "true" to boolean true.
        $excludePastYears = filter_var($paramFetcher->get('exclude_past_years'), FILTER_VALIDATE_BOOLEAN);
        $organizationId = $this->getLoggedInUserOrganizationId();
        $responseArray = $this->academicyearService->listAcademicYears($organizationId, $excludeFutureYears, $excludePastYears);

        return new Response($responseArray);
    }

    /**
     * Gets academic year metadata based on the academic year's ID value.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Academic Year",
     * output = "Synapse\AcademicBundle\EntityDto\AcademicYearDto",
     * section = "Academic Year",
     * statusCodes = {
     *                  200 = "Returned when successful",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/{orgId}/{id}", requirements={"_format"="json"})
     * @Rest\View()
     *
     * @param integer $orgId
     * @param integer $id
     * @return Response
     */
    public function getAcademicYearAction($orgId, $id)
    {
        $loggedInUser = $this->getLoggedInUser();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $academicYearDto = $this->academicyearService->getAcademicYear($organizationId, $id, $loggedInUser);
        return new Response($academicYearDto);
    }

    /**
     * Edits an academic year.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Academic Year",
     * input = "Synapse\AcademicBundle\EntityDto\AcademicYearDto",
     * output = "Synapse\AcademicBundle\EntityDto\AcademicYearDto",
     * section = "Academic Year",
     * statusCodes = {
     *                  201 = "Academic Year was Edited. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param AcademicYearDto $academicYearDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function editAcademicYearAction(AcademicYearDto $academicYearDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($academicYearDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUser = $this->getLoggedInUser();
            $academicYear = $this->academicyearService->editAcademicYear($academicYearDto, $loggedInUser);

            return new Response($academicYear);
        }
    }

    /**
     * Delete an academic year.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Academic Year",
     * section = "Academic Year",
     * statusCodes = {
     *                  204 = "Academic year was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{orgId}/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $orgId
     * @param integer $id
     * @return Response
     */
    public function deleteAcademicYearAction($orgId, $id)
    {
        $loggedInUser = $this->getLoggedInUser();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $academicYear = $this->academicyearService->deleteAcademicYear($organizationId, $id, $loggedInUser);
        return new Response($academicYear);
    }
}