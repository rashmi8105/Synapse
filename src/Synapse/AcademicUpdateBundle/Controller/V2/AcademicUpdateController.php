<?php

namespace Synapse\AcademicUpdateBundle\Controller\V2;


use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\AcademicUpdateBundle\EntityDto\CourseAdhocAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesStudentsAdhocAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsDto;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response as Response;

/**
 * Class AcademicUpdateController
 *
 * @package Synapse\RestBundle\Controller
 *
 * @Rest\Version("2.0")
 *
 */
class AcademicUpdateController extends AbstractAuthController
{
    /**
     * @var Logger
     * @DI\Inject(SynapseConstant::CONTROLLER_LOGGING_CHANNEL)
     */
    private $apiLogger;

    /**
     * @var AcademicUpdateService
     * @DI\Inject(AcademicUpdateService::SERVICE_KEY)
     */
    private $academicUpdateService;


    /**
     * @var IDConversionService
     * @DI\Inject(IDConversionService::SERVICE_KEY);
     */
    private $idConversionService;

    /**
     * Get the latest academic updates for all/specified students with the specific course external id.
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Get Academic update for a specific course - external id",
     *          input = "Synapse\AcademicUpdateBundle\EntityDto\StudentsDto",
     *          output = "Synapse\AcademicUpdateBundle\Entity\CourseAdhocAcademicUpdateDTO",
     *          section = "Academic Updates",
     *          statusCodes = {
     *                          201 = "Returned when successful",
     *                          504 = "Request has timed out. Please re-try.",
     *                          500 = "There was either errors with the body of the request or an internal server error.",
     *                          400 = "Validation errors have occurred."
     *                        },
     *          views = { "public" }
     *
     * )
     * @Rest\Post("/courses/{courseExternalId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param int $courseExternalId - Course external ID.
     * @param StudentsDto $studentsDto - Comma separated list of student IDs.
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function getLatestCourseAcademicUpdatesAction($courseExternalId, StudentsDto $studentsDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($studentsDto, $errors), 400);
        }

        $ipAddress = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $loggedInUserId = $this->getUser()->getId();
        $organizationId = $this->getUser()->getOrganization()->getId();
        $studentIds = $studentsDto->getStudentIds();
        $this->apiLogger->notice(__FUNCTION__ . "; PersonId: $loggedInUserId; OrganizationId: $organizationId; Course External Id: $courseExternalId; Student's External Id: $studentIds ; IP Address: $ipAddress");

        $this->apiValidationService->isAPIIntegrationEnabled();
        $this->apiValidationService->isOrganizationAPICoordinator($organizationId, $loggedInUserId);

        $convertedPersonIds = $this->idConversionService->convertPersonIds($studentIds, $organizationId, false);
        $convertCourseIDs = $this->idConversionService->convertCourseIDs($courseExternalId, $organizationId, false);
        $validationErrors = $this->idConversionService->validationErrors;
        $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $validationErrors);
        if ($validationErrors) {
            // un-nest the array
            $validationErrorsArray = array_map('current', $validationErrors);
            $validationErrorString = implode(',', $validationErrorsArray);
            throw new SynapseValidationException("The following errors occurred when validating your request: " . $validationErrorString);
        }
        
        $latestCourseAcademicUpdates = $this->academicUpdateService->getLatestCourseAcademicUpdates($convertCourseIDs[0], $organizationId, $convertedPersonIds);
        if (empty($latestCourseAcademicUpdates)) {
            $latestCourseAcademicUpdates = "For the specified course ID and student IDs, there is no academic update data";
        }
        return new Response($latestCourseAcademicUpdates);
    }

    /**
     * Creates an adhoc academic update
     *
     * @ApiDoc(
     * resource = true,
     * description = "Adhoc Academic Update",
     * input = "Synapse\AcademicUpdateBundle\EntityDto\CoursesStudentsAdhocAcademicUpdateDTO",
     * output = "Synapse\AcademicUpdateBundle\EntityDto\CoursesStudentsAdhocAcademicUpdateDTO",
     * section = "Academic Updates",
     * statusCodes = {
     *                    201 = "Returned when successful",
     *                    400 = "Validation errors have occurred.",
     *                    403 = "Access denied exception",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out. Please re-try."
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Post("/adhoc", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createAdhocAcademicUpdatesAction(CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($coursesStudentsAdhocAcademicUpdateDTO, $errors), 400);
        }

        $ipAddress = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();
        
        $requestJSON = $this->get("request")->getContent();
        $this->apiLogger->notice(__FUNCTION__ . "; Request Json: $requestJSON; PersonId: $loggedInUserId; OrganizationId: $organizationId; IP Address: $ipAddress");

        $this->apiValidationService->isAPIIntegrationEnabled();
        $this->apiValidationService->isOrganizationAPICoordinator($organizationId, $loggedInUserId);
        $this->academicUpdateService->isRequestSizeAllowedForAcademicUpdate($requestJSON);

        $response = $this->academicUpdateService->createAcademicRecord($coursesStudentsAdhocAcademicUpdateDTO, $loggedInUser, true, 'adhoc');

        return new Response($response['data'],$response['errors']);

    }

}