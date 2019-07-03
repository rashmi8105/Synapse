<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\AcademicBundle\EntityDto\AcademicTermDto;
use Synapse\AcademicBundle\Service\Impl\AcademicTermService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class AcademicTermController
 *
 * @package Synapse\RestBundle\Controller
 *          @Rest\Prefix("/academicterms")
 *
 */
class AcademicTermController extends AbstractAuthController
{
    /**
     * @var AcademicTermService
     *
     *      @DI\Inject(AcademicTermService::SERVICE_KEY)
     */
    private $academicTermService;


    /**
     * Creates an academic term
     *
     * @ApiDoc(
     * resource = true,
     * description = "Creates an Academic Term",
     * input = "Synapse\AcademicBundle\EntityDto\AcademicTermDto",
     * output = "Synapse\AcademicBundle\EntityDto\AcademicTermDto",
     * section = "Academic Year",
     * statusCodes = {
     *                  201 = "Academic term was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param AcademicTermDto $academicTermDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createAcademicTermAction(AcademicTermDto $academicTermDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);

        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($academicTermDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $academicTerm = $this->academicTermService->createAcademicTerm($academicTermDto, $loggedInUserId);
       }

        return new Response($academicTerm);
    }

    /**
     * Gets the specified academic term.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets an Academic Term",
     * output = "Synapse\RestBundle\Controller\academicTerm",
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
     * @Rest\Get("/{orgId}/{yearId}/{termId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param integer $orgId
     * @param integer $yearId
     * @param integer $termId
     * @return Response
     */
    public function getAcademicTermAction($orgId, $yearId, $termId)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);

        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $academicTerm = $this->academicTermService->getAcademicTerm($organizationId, $yearId, $termId, $loggedInUserId);
        return new Response($academicTerm);
    }

    /**
     * Gets the academic terms based on the specified parameters.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Lists an Academic Term",
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
     * @Rest\Get("/{orgId}/{yearId}", requirements={"_format"="json"})
     * @QueryParam(name="userType", strict=false, description="userType(staff||coordinator)")
     * @Rest\View(statusCode=200)
     *
     * @param integer $orgId
     * @param integer $yearId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listAcademicTermsAction($orgId, $yearId, ParamFetcher $paramFetcher)
    {
        $userType = $paramFetcher->get('userType');
        if(trim($userType) != 'staff'){
            $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        }
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $academicTerm = $this->academicTermService->listAcademicTerms($organizationId, $yearId, $loggedInUserId, $userType);
        return new Response($academicTerm);
    }

    /**
     * Edits the specific academic term.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edits an Academic Term",
     * input = "Synapse\AcademicBundle\EntityDto\AcademicTermDto",
     * output = "Synapse\AcademicBundle\EntityDto\AcademicTermDto",
     * section = "Academic Year",
     * statusCodes = {
     *                  201 = "The academic term was updated. Representation of resources(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param AcademicTermDto $academicTermDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function editAcademicTermAction(AcademicTermDto $academicTermDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);

        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($academicTermDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $academicTerm = $this->academicTermService->editAcademicTerm($academicTermDto, $loggedInUserId);
         }
        return new Response($academicTerm);
    }

    /**
     * Deletes the academic term by using the given academic term Id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Deletes an Academic Term",
     * section = "Academic Year",
     * statusCodes = {
     *                  204 = "The academic term was deleted. No representation of resources was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $id
     * @return Response
     */
    public function deleteAcademicTermAction($id)
    {   
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $academicTerm = $this->academicTermService->deleteAcademicTerm($id, $organizationId, $loggedInUserId);
        return new Response($academicTerm);
    }
}