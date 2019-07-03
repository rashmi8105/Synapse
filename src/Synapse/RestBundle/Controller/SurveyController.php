<?php

namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\OrgQuestionService;
use Synapse\CoreBundle\Service\Impl\SurveyService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\OrgCoordinatorNotificationDto;
use Synapse\RestBundle\Entity\OrgQuesDto;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\WessOrgResponseDto;
use Synapse\RestBundle\Entity\WessSurveyResponseDto;
use Synapse\SurveyBundle\EntityDto\WessLinkDto;
use Synapse\SurveyBundle\EntityDto\WessLinkInsertDto;
use Synapse\SurveyBundle\Service\Impl\SurveyBlockService;
use Synapse\SurveyBundle\Service\Impl\SurveyPermissionService;
use Synapse\SurveyBundle\Service\Impl\SurveyQuestionsService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;


/**
 * Class Survey Controller
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/surveys")
 *         
 */
class SurveyController extends AbstractAuthController
{
    /**
     * @var AcademicYearService
     *
     *      @DI\Inject(AcademicYearService::SERVICE_KEY)
     */
    private $academicYearService;

    /**
     * @var OrgQuestionService
     *
     *      @DI\Inject(OrgQuestionService::SERVICE_KEY)
     */
    private $orgQuestionService;

    /**
     * @var SurveyBlockService
     *
     *      @DI\Inject(SurveyBlockService::SERVICE_KEY)
     */
    private $surveyBlockService;

    /**
     * @var SurveyPermissionService
     *
     *      @DI\Inject(SurveyPermissionService::SERVICE_KEY)
     */
    private $surveyPermissionService;

    /**
     * @var SurveyQuestionsService
     *
     *      @DI\Inject(SurveyQuestionsService::SERVICE_KEY)
     */
    private $surveyQuestionsService;

    /**
     * @var SurveyService
     *
     *      @DI\Inject(SurveyService::SERVICE_KEY)
     */
    private $surveyService;

    /**
     * @var UploadFileLogService
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * Gets survey data with open/close date and status for Admin user otherwise
     * returns basic survey data (survey ids, names, and years) for the logged-in user's organization,
     * ordered by year_id and then by survey_id, both with the most recent listed first.
     * If the status query parameter is used, surveys will be included if they have one of the listed statuses
     * for at least one cohort in the organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Activity",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="org_academic_year_id", requirements="\d+", description="org_academic_year_id")
     * @QueryParam(name="status", requirements="(launched|closed|launched,closed)", description="survey status")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSurveysAction(ParamFetcher $paramFetcher)
    {
        $orgAcademicYearId = $paramFetcher->get('org_academic_year_id');
        $status = $paramFetcher->get('status');

        if ($status) {
            $statusArray = explode(',', $status);
        } else {
            $statusArray = [];
        }

        $organizationId = $this->getLoggedInUserOrganizationId();
        if ($orgAcademicYearId) {
            $this->academicYearService->validateAcademicYear($orgAcademicYearId, $organizationId);
        }

        $result = [];
        $result['organization_id'] = $organizationId;
        if ($organizationId == SynapseConstant::ADMIN_ORGANIZATION_ID) {
            $result['surveys'] = $this->surveyService->getAllSurveys();
        } else {
            $result['surveys'] = $this->surveyService->getSurveysForOrganization($organizationId, $orgAcademicYearId, $statusArray);
        }

        return new Response($result);
    }


    /**
     * Get the information for a survey.
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Survey",
     * output = "Synapse\RestBundle\Entity\SurveyDto",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/status", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function viewSurveyAction()
    {
        $result = $this->surveyService->viewSurvey();
        return new Response($result);
    }

    /**
     * Get survey block data.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Block Data",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/data", requirements={"_format"="json"})
     * @QueryParam(name="survey_id", strict=false, description="Survey Id")
     * @QueryParam(name="type", strict=true, description="type")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getDataAction(ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        $surveyId = $paramFetcher->get('survey_id');
        $data = $this->surveyBlockService->getDataForBlocks($type, $surveyId);
        return new Response($data);
    }

    /**
     * Get pending survey uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Survey Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey",
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
     * @Rest\Get("/pending/{orgId}")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getPendingSurveyUploadAction()
    {
        if (! ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))) {
            throw new AccessDeniedException();
        }
        $upload = $this->uploadFileLogService->hasPendingView(null, 'SU');
        return new Response([
            'upload' => $upload
        ], []);
    }

    /**
     * Edit Wess link. DEPRECATED
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Wess Link",
     * input = "Synapse\SurveyBundle\EntityDto\WessLinkDto",
     * output = "Synapse\SurveyBundle\EntityDto\WessLinkDto",
     * section = "Survey",
     * statusCodes = {
     *                  204 = "Wess link was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/wess", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param WessLinkDto $wessLinkDto            
     * @return Response
     * @deprecated
     */
    public function editWessLinkAction(WessLinkDto $wessLinkDto)
    {
        $wessLink = $this->surveyService->editWessLink($wessLinkDto);
        return new Response($wessLink);
    }

    /**
     * Insert data from Wess to wess Link. DEPRECATED
     *
     * @ApiDoc(
     * resource = true,
     * description = "Insert Wess Data to Wess Link",
     * input = "Synapse\SurveyBundle\EntityDto\WessLinkInsertDto",
     * output = "Synapse\SurveyBundle\EntityDto\WessLinkInsertDto",
     * section = "Survey",
     * statusCodes = {
     *                  204 = "Wess link was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/orders", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param WessLinkInsertDto $wessLinkInsertDto
     * @return Response
     * @deprecated
     */
    public function insertWessLinkAction(WessLinkInsertDto $wessLinkInsertDto)
    {
        $wessLink = $this->surveyService->insertWessLink($wessLinkInsertDto);
        return new Response($wessLink);
    }

    /**
     * Update wess link data. DEPRECATED
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Wess Link Data",
     * input = "Synapse\SurveyBundle\EntityDto\WessLinkInsertDto",
     * output = "Synapse\SurveyBundle\EntityDto\WessLinkInsertDto",
     * section = "Survey",
     * statusCodes = {
     *                  204 = "Wess link was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/orders", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param WessLinkInsertDto $wessLinkInsertDto
     * @return Response
     * @deprecated
     */
    public function updateWessLinkAction(WessLinkInsertDto $wessLinkInsertDto)
    {
        $wessLink = $this->surveyService->updateWess($wessLinkInsertDto);
        return new Response($wessLink);
    }


    /**
     * Get synapse campus data.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Synapse Campus Data",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/campus", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function synapseCampusAction()
    {
        $campusData = $this->surveyService->generateCampusDump();
        return new Response($campusData);
    }

    /**
     * Update synapse survey response. DEPRECATED
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Survey Response",
     * input = "Synapse\SurveyBundle\EntityDto\WessLinkInsertDto",
     * output = "Synapse\SurveyBundle\EntityDto\WessLinkInsertDto",
     * section = "Survey",
     * statusCodes = {
     *                  204 = "Survey response was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{surveyId}/responses", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $surveyId
     * @param WessSurveyResponseDto $wessSurveyResponseDto
     * @return Response
     * @deprecated
     */
    public function synapseSurveyResponseAction($surveyId, WessSurveyResponseDto $wessSurveyResponseDto)
    {
        $campusData = $this->surveyService->updateSurveyResponse($wessSurveyResponseDto);
        return new Response($campusData);
    }

    /**
     * Updates an ISQ(Institution Specific Question) DEPRECATED
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update ISQ",
     * input = "Synapse\RestBundle\Entity\WessOrgResponseDto",
     * output = "Synapse\RestBundle\Entity\WessOrgResponseDto",
     * section = "Survey",
     * statusCodes = {
     *                  204 = "Survey response was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{orgId}/orgresponses", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $orgId
     * @param WessOrgResponseDto $wessOrgResponseDto
     *
     * @return Response
     * @deprecated
     */
    public function synapseOrgResponseAction($orgId, WessOrgResponseDto $wessOrgResponseDto)
    {
        $campusData = $this->surveyService->updateISQ($wessOrgResponseDto, $orgId);
        return new Response($campusData);
    }

    /**
     * Edit an organization's questions.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Organization Question",
     * input = "Synapse\RestBundle\Entity\OrgQuesDto",
     * output = "Synapse\RestBundle\Entity\OrgQuesDto",
     * section = "Survey",
     * statusCodes = {
     *                  204 = "Organization question was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/orgquestions", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param OrgQuesDto $orgQuesDto
     *
     */
    public function synapseOrgQuestionAction(OrgQuesDto $orgQuesDto)
    {
        $this->surveyService->updateOrgQuestions($orgQuesDto);
    }

    /**
     * Returns a list of all survey questions on the given survey that the user is permitted to see,
     * excluding short answer and long answer questions, along with the options available for each question.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Questions",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/{surveyId}/questions/{langId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $surveyId
     * @param int $langId
     * @return Response
     */
    public function getSurveyQuestionsAction($surveyId, $langId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $data = $this->surveyQuestionsService->getSurveyQuestions($organizationId, $surveyId, $loggedInUserId);
        return new Response($data);
    }
    
    /**
     * Gets an ISQ(Institution Specific Question).
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get ISQ",
     * output = "Synapse\SurveyBundle\EntityDto\ISQResponseDto",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/{surveyId}/isq/{langId}", requirements={"_format"="json"})
     * @QueryParam(name="cohort_id", strict=false, description="cohortId")
     * @Rest\View(statusCode=200)
     *
     * @param $surveyId
     * @param $langId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getISQAction($surveyId, $langId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $cohortId = $paramFetcher->get('cohort_id');
        $data = $this->surveyQuestionsService->getISQWithOptions($organizationId, $langId, $surveyId, $loggedInUserId, $cohortId);
        return new Response($data);
    }
    
    /**
     * Get a survey's completion status.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Completion Status",
     * output = "Synapse\SurveyBundle\EntityDto\SurveyListDetailsDto",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/details/{langId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param $langId
     * @return Response
     */
    public function getSurveyCompletionStatusAction($langId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $timeZone = $this->getLoggedInUserOrganization()->getTimeZone();
        $data = $this->surveyQuestionsService->getSurveyCompletionStatus($organizationId, $langId, $timeZone);
        return new Response($data);
    }
    
    /**
     * Create a coordinator notification for an ISQ.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Organization Question",
     * input = "Synapse\RestBundle\Entity\OrgCoordinatorNotificationDto",
     * output = "Synapse\RestBundle\Entity\OrgCoordinatorNotificationDto",
     * section = "Survey",
     * statusCodes = {
     *                  201 = "Coordinator ISQ notification was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/isq-notification", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OrgCoordinatorNotificationDto $OrgCoordinatorNotification
     * @return Response
     */
    public function createNotificationIsqAction(OrgCoordinatorNotificationDto $OrgCoordinatorNotification)
    {   
        $this->ensureArtMember();
        $data = $this->surveyQuestionsService->notifiyToCoordinator($OrgCoordinatorNotification);
        return new Response($data);
    }
    
    /**
     * Get a survey cohort's ISQ.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Cohort ISQ",
     * output = "Synapse\SurveyBundle\EntityDto\SurveyQuestionsResponseDto",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/{surveyId}/cohort/{cohortId}/isqs", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $surveyId
     * @param int $cohortId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSurveyCohortISQAction($surveyId, $cohortId, ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $data = $this->surveyQuestionsService->getSurveyCohortISQ($organizationId, $surveyId, $cohortId);
        return new Response($data);
    }

    /**
     * Gets surveys and cohorts associated with those surveys at the user's organization.
     * If include_completion_data=true, includes the number of students from each cohort that were assigned the survey and the number that completed the survey.
     *
     * If purpose=isq_access, only includes survey and cohort combinations for which the user has ISQ access.
     * The is_aggregate_reporting query parameter is meant to be used when purpose=isq_access to determine which permission sets to include when determining ISQ access.
     * If is_aggregate_reporting=true (e.g., in report filters), all permission sets will be included for determining ISQ access.
     * If is_aggregate_reporting=false (e.g., in custom search), only individual permission sets will be included for determining ISQ access.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Surveys and Cohorts from Organization",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\GET("/cohorts", requirements={"_format"="json"})
     * @QueryParam(name="org_academic_year_id", requirements="\d+", description="org_academic_year_id")
     * @QueryParam(name="status", requirements="(launched|closed|launched,closed)", description="survey status")
     * @QueryParam(name="survey_id", requirements="\d+", description="Survey id")
     * @QueryParam(name="include_completion_data", requirements="true", description="Include survey completion data for this group")
     * @QueryParam(name="purpose", requirements="isq_access", description="only include survey and cohort combinations for which the user has ISQ access")
     * @QueryParam(name="is_aggregate_reporting", requirements="true|false", description="Is this API being used in a place that only returns aggregate results?")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSurveysAndCohortsAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $orgAcademicYearId = $paramFetcher->get('org_academic_year_id');
        $statusString = $paramFetcher->get('status');
        $surveyId = (int)$paramFetcher->get('survey_id');
        $includeCompletionDataFlag = filter_var($paramFetcher->get('include_completion_data'), FILTER_VALIDATE_BOOLEAN);
        $purpose = $paramFetcher->get('purpose');
        $isAggregateReporting = filter_var($paramFetcher->get('is_aggregate_reporting'), FILTER_VALIDATE_BOOLEAN);

        if ($statusString) {
            $surveyStatus = explode("," , $statusString);
        } else {
            $surveyStatus = null;
        }

        $responseData = $this->surveyService->getSurveysAndCohorts($organizationId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $surveyId, $includeCompletionDataFlag, $purpose, $isAggregateReporting);
        return new Response($responseData);

    }


    /**
     * Gets high-level data about the logged-in user's survey-related permissions,
     * including whether he/she can access any survey blocks, the free-response survey block in particular, and any ISQs.
     * If the student_id query parameter is set, these permissions are specific to only groups and courses that the student is in.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get User's Survey Permissions",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Survey",
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
     * @Rest\Get("/permissions", requirements={"_format"="json"})
     * @QueryParam(name="student_id", requirements="\d+", description="person_id for a student")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getSurveyPermissionsAction(ParamFetcher $paramFetcher)
    {
        $studentId = $paramFetcher->get('student_id');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        if (isset($studentId)) {
            $response = $this->surveyPermissionService->getSurveyPermissionsForFacultyAndStudent($loggedInUserId, $studentId, $organizationId);
        } else {
            $response = $this->surveyPermissionService->getSurveyPermissionsForFaculty($loggedInUserId, $organizationId);
        }

        return new Response($response);
    }

    /**
     * Get all surveys and cohorts with respect to the years depending on purpose.
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Get all surveys and cohorts with respect to the years depending on purpose",
     *          output = "Synapse\RestBundle\Entity\Response",
     *          section = "Survey",
     *          statusCodes = {
     *                          200 = "Request was successful. Representation of resource(s) was returned",
     *                          400 = "Validation errors have occurred.",
     *                          500 = "There was either errors with the body of the request or an internal server error.",
     *                          504 = "Request has timed out."
     *                        },
     *
     * )
     *
     * @Rest\GET("/year", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="org_academic_year_id", requirements="\d+", description="yearId in which survey appears")
     * @QueryParam(name="status", requirements="(launched|closed|launched,closed)", description="survey status either survey launched or closed")
     * @QueryParam(name="exclude_question_type", requirements="(LA|SA|MR|LA,SA,MR)", description="To exclude questions of type 'LA' , 'SA' and 'MR'")
     * @QueryParam(name="purpose", requirements="isq|survey_specific", description="if API used to list cohorts and surveys for ISQ permissions or survey setup")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @throws SynapseValidationException
     */
    public function getCohortsAndSurveysWithRespectToYearsAction(ParamFetcher $paramFetcher)
    {
        $orgAcademicYearId = $paramFetcher->get('org_academic_year_id');
        $status = $paramFetcher->get('status');
        $purpose = $paramFetcher->get('purpose');

        if ($status) {
            $surveyStatus = explode(',', $status);
        }

        $queryParamExcludeQuestionType = $paramFetcher->get('exclude_question_type');
        $excludeQuestionType = [];
        if ($queryParamExcludeQuestionType) {
            $excludeQuestionType = explode(',', $queryParamExcludeQuestionType);
        }

        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        if ($orgAcademicYearId) {
            $this->academicYearService->validateAcademicYear($orgAcademicYearId, $organizationId);
        }
        if ($purpose == 'isq') {
            $response = $this->orgQuestionService->getISQcohortsAndSurveysWithRespectToYears($organizationId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
        } elseif ($purpose == 'survey_specific') {
            $response = $this->orgQuestionService->getSurveysAndCohortsWithRespectToYears($organizationId, $loggedInUserId, $orgAcademicYearId, $surveyStatus, $excludeQuestionType);
        } else {
            throw new SynapseValidationException('Invalid purpose parameter value');
        }
        return new Response($response);
    }

    /**
     * Returns a list of question options available for each question
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Returns a list of question along with the options for a given question and survey",
     *          output = "Synapse\RestBundle\Entity\Response",
     *          section = "Survey",
     *          statusCodes = {
     *                          200 = "Returned when successful",
     *                          400 = "Validation errors have occurred.",
     *                          404 = "Not Found",
     *                          500 = "Internal server error",
     *                          504 = "Request has timed out. Please re-try."
     *                        },
     * )
     *
     * @Rest\GET("/question/options", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="question_id", requirements="\d+", description="question id for ISQ or survey questions")
     * @QueryParam(name="survey_id", requirements="\d+", description="surveyId of a given survey")
     * @QueryParam(name="purpose", requirements="isq|survey_specific", description="isq for ISQ Questions or survey_specific for Survey Questions")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @throws SynapseValidationException
     */
    public function getSurveyQuestionOptionsAction(ParamFetcher $paramFetcher)
    {
        $questionId = (int)$paramFetcher->get('question_id');
        $surveyId = (int)$paramFetcher->get('survey_id');
        $purpose = $paramFetcher->get('purpose');

        if ($purpose == 'isq') {
            $data = $this->orgQuestionService->getISQsurveyQuestionOptions($surveyId, $questionId);
        } elseif ($purpose == 'survey_specific') {
            $data = $this->surveyQuestionsService->getSurveyQuestionOptions($surveyId, $questionId);
        } else {
            throw new SynapseValidationException('Invalid purpose parameter value');
        }
        return new Response($data);
    }

}