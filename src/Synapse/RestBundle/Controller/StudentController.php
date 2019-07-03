<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CampusResourceBundle\Service\Impl\CampusResourceService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\StudentService;
use Synapse\CoreBundle\Service\Impl\SurveyService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\StudentPolicyDto;
use Synapse\RestBundle\Entity\SurveyAccessStatusDto;
use Synapse\StaticListBundle\EntityDto\StaticListDto;
use Synapse\StaticListBundle\Service\Impl\StaticListService;
use Synapse\StaticListBundle\Service\Impl\StaticListStudentsService;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;
use Synapse\StudentViewBundle\Service\Impl\StudentCampusConnectionService;
use Synapse\StudentViewBundle\Service\Impl\StudentCourseService;
use Synapse\StudentViewBundle\Service\Impl\StudentReferralService;
use Synapse\SurveyBundle\Service\Impl\StudentSurveyService;
use Synapse\SurveyBundle\Service\Impl\SurveyCompareService;
use Synapse\SurveyBundle\Service\Impl\SurveyDashboardService;

/**
 * Class StudentController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *         
 *          @Rest\Prefix("students")
 *         
 */
class StudentController extends AbstractAuthController
{

    const ORG_ID = "orgId";

    const SECURITY_CONTEXT = 'security.context';

    /**
     * @var AcademicYearService
     *
     *      @DI\Inject(AcademicYearService::SERVICE_KEY)
     */
    private $academicYearService;

    /**
     * @var CampusResourceService
     *
     *      @DI\Inject(CampusResourceService::SERVICE_KEY)
     */
    private $campusResourceService;

    /**
     * @var StaticListService
     *
     *      @DI\Inject(StaticListService::SERVICE_KEY)
     */
    private $staticListService;

    /**
     * @var StaticListStudentsService
     *
     *      @DI\Inject(StaticListStudentsService::SERVICE_KEY)
     */
    private $staticListStudentService;

    /**
     * @var StudentAppointmentService
     *
     *      @DI\Inject(StudentAppointmentService::SERVICE_KEY)
     */
    private $studentAppointmentService;

    /**
     * @var StudentCampusConnectionService
     *
     *      @DI\Inject(StudentCampusConnectionService::SERVICE_KEY)
     */
    private $studentCampusConnectionService;

    /**
     * @var StudentCourseService
     *
     *      @DI\Inject(StudentCourseService::SERVICE_KEY)
     */
    private $studentCourseService;

    /**
     * @var StudentReferralService
     *
     *      @DI\Inject(StudentReferralService::SERVICE_KEY)
     */
    private $studentReferralService;

    /**
     * @var StudentService
     *
     *      @DI\Inject(StudentService::SERVICE_KEY)
     */
    private $studentService;

    /**
     * @var StudentSurveyService
     *
     *      @DI\Inject(StudentSurveyService::SERVICE_KEY)
     */
    private $studentSurveyService;

    /**
     * @var SurveyCompareService
     *
     *      @DI\Inject(SurveyCompareService::SERVICE_KEY)
     */
    private $surveyCompareService;

    /**
     * @var SurveyDashboardService
     *
     *      @DI\Inject(SurveyDashboardService::SERVICE_KEY)
     */
    private $surveyDashboardService;

    /**
     * @var SurveyService
     *
     *      @DI\Inject(SurveyService::SERVICE_KEY)
     */
    private $surveyService;

    /**
     * Gets a student's activity.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Activity",
     * output = "Synapse\RestBundle\Entity\StudentListHeaderResponseDto",
     * section = "Student",
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
     * @Rest\Get("/activity", requirements={"_format"="json"})
     * @QueryParam(name="student-id", requirements="\d+", strict=true, description="Student id")
     * @QueryParam(name="category", strict=true, description="category")
     * @QueryParam(name="is-interaction", strict=true, description="is iteraction")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listStudentActivityAction(ParamFetcher $paramFetcher)
    {
        $studentId = (int) $paramFetcher->get('student-id');
        $this->checkAccessToStudent($studentId);
        $category = $paramFetcher->get('category');
        $isInteraction = $paramFetcher->get('is-interaction');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $responseArray = $this->studentService->getStudentActivityList($studentId, $category, $isInteraction, $organizationId, $loggedInUserId);
        return new Response($responseArray);
    }

    /**
     * Gets a student's personal information.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Personal Student Information",
     * output = "Synapse\RestBundle\Entity\StudentDetailsResponseDto",
     * section = "Student",
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
     * @Rest\Get("/{studentId}", defaults={"studentId" = -1},requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @return Response
     */
    public function getStudentProfileAction($studentId)
    {
        $this->checkAccessToStudent($studentId);
        $loggedInUser = $this->loggedInUser;
        $organizationId = $loggedInUser->getOrganization()->getId();
        $loggedInUserId = $loggedInUser->getId();

        $student = $this->studentService->getStudentProfile($organizationId, $studentId, $loggedInUser, $loggedInUserId);
        $response = new Response($student);
        return $response;
    }

    /**
     * Gets a student's open appointments.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student's Open Appointments",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student",
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
     * @Rest\Get("/{studentId}/appointments", requirements={"_format"="json"})
     * @QueryParam(name="type", requirements="(upcoming)", default ="", strict=true, description="type of appointment listing, type = upcoming")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentOpenAppointmentsAction($studentId, ParamFetcher $paramFetcher)
    {        
        $type = $paramFetcher->get('type');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $timeZone = $this->getLoggedInUserOrganization()->getTimeZone();

        if ($type == "upcoming") {
            $result = $this->studentAppointmentService->getStudentsUpcomingAppointments($studentId, $timeZone);
        } else {
            $this->checkAccessToStudent($studentId);
            $this->ensureAccess([self::PERM_INDIVIDUALANDAGGREGATE]);
            $result = $this->studentService->getStudentsOpenAppointments($studentId, $loggedInUserId, $organizationId);
        }
        return new Response($result);
    }

    /**
     * Gets a list of a student's open referrals.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get List of Student's Open Referrals",
     * output = "Synapse\RestBundle\Entity\StudentOpenReferralsDto",
     * section = "Student",
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
     * @Rest\Get("/{studentId}/referrals", requirements={"_format"="json"})
     * @QueryParam(name="view", requirements="(student)", strict=false, description="User type")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listOpenReferralsAction($studentId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $view = $paramFetcher->get('view');

        if (strtolower($view) == 'student') {
            $result = $this->studentReferralService->getStudentOpenReferrals($studentId);
        } else {
            $this->checkAccessToStudent($studentId);
            $this->ensureAccess([self::PERM_INDIVIDUALANDAGGREGATE]);
            $result = $this->studentService->listReferrals($loggedInUserId, $studentId);
        }
        return new Response($result);
    }

    /**
     * Gets a list of a student's contacts.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get List of Student's Contacts",
     * output = "Synapse\RestBundle\Entity\StudentContactsDto",
     * section = "Student",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     * @Rest\Get("/{studentId}/contacts", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @return mixed
     * @return Response
     */
    public function listContactssAction($studentId)
    {
        $this->checkAccessToStudent($studentId);
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $activityData = $this->studentService->studentContacts($loggedInUserId, $studentId, $organizationId);
        return new Response($activityData);
    }

    /**
     * Get Student's Talking Points
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student's Talking Points",
     * output = "Synapse\RestBundle\Entity\StudentTalkingPointsDto",
     * section = "Student",
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
     * @Rest\Get("/{studentId}/talking_point", requirements={"_format"="json"})
     * @QueryParam(name="debug")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentTalkingPointsAction($studentId, ParamFetcher $paramFetcher)
    {
        $this->checkAccessToStudent($studentId);
        $this->ensureAccess([self::PERM_INDIVIDUALANDAGGREGATE]);
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $debug = $paramFetcher->get('debug');

        $result = $this->studentService->getTalkingPoints($studentId, $loggedInUserId, $organizationId ,$debug);
        return new Response($result);
    }

    /**
     * Gets specific details about a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student's Details",
     * output = "Synapse\RestBundle\Entity\StudentProfileResponseDto",
     * section = "Student",
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
     * @Rest\Get("/{studentId}/studentdetails", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @return Response
     */
    public function getStudentDetailsAction($studentId)
    {
        $this->checkAccessToStudent($studentId);
        $this->ensureAccess([self::PERM_INDIVIDUALANDAGGREGATE]);
        $loggedInUser = $this->getLoggedInUser();

        $result = $this->studentService->getStudentDetails($studentId, $loggedInUser);
        return new Response($result);
    }

    /**
     * Lists all surveys that the student has been assigned, along with lots of metadata,
     * such as survey status and whether the student has responded.
     * This API can be used by either the student whose id is included or by a faculty/staff user with individual access to the student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "List Student's Assigned Surveys",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Surveys",
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
     * @Rest\Get("/{studentId}/surveys", requirements={"_format"="json"})
     * @QueryParam(name="org_academic_year_id", requirements="\d+", description="org_academic_year_id")
     * @QueryParam(name="has_responses", requirements="true", description="if true, only include surveys the student has responded to")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listStudentSurveysAction($studentId, ParamFetcher $paramFetcher)
    {
        $orgAcademicYearId = $paramFetcher->get('org_academic_year_id');
        $hasResponses = filter_var($paramFetcher->get('has_responses'), FILTER_VALIDATE_BOOLEAN);     // Convert string "true" to boolean true.
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        if (isset($orgAcademicYearId)) {
            $this->academicYearService->validateAcademicYear($orgAcademicYearId, $organizationId);
        }

        $response = $this->studentSurveyService->listSurveysForStudent($studentId, $loggedInUserId, $organizationId, $orgAcademicYearId, $hasResponses);
        return new Response($response);
    }


    /**
     * Gets the name of the pdf file for the student survey report for the given student and survey.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student's PDF Survey Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Surveys",
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
     * @Rest\Get("/{studentId}/surveys/{surveyId}/report", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $surveyId
     * @return Response
     */
    public function getStudentSurveyReportAction($studentId, $surveyId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $response = $this->studentSurveyService->getStudentSurveyReport($studentId, $loggedInUserId, $surveyId);
        return new Response($response);
    }


    /**
     * Gets success marker and topic data for the top level of the Student Survey Dashboard.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Success Marker and Data of Student Survey Dashboard",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Surveys",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{studentId}/surveys/{surveyId}/success_markers", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $surveyId
     * @return Response
     */
    public function listSuccessMarkersAction($studentId, $surveyId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $response = $this->surveyDashboardService->listSuccessMarkersAndTopicsForStudent($studentId, $loggedInUserId, $surveyId);
        return new Response($response);
    }


    /**
     * Gets success marker and topic data for a single success marker.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Single Success Marker",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Surveys",
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
     * @Rest\Get("/{studentId}/surveys/{surveyId}/success_markers/{successMarkerId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $surveyId
     * @param int $successMarkerId
     * @return Response
     */
    public function listTopicsForSuccessMarkerAction($studentId, $surveyId, $successMarkerId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $response = $this->surveyDashboardService->listSuccessMarkersAndTopicsForStudent($studentId, $loggedInUserId, $surveyId, $successMarkerId);
        return new Response($response);
    }


    /**
     * Gets the data for the drilldown on a single topic on the Student Survey Dashboard.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Drilldown for Single Survey Topic",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Surveys",
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
     * @Rest\Get("/{studentId}/surveys/{surveyId}/success_markers/{successMarkerId}/topics/{topicId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $surveyId
     * @param int $successMarkerId
     * @param int $topicId
     * @return Response
     */
    public function getStudentSurveyDrilldownAction($studentId, $surveyId, $successMarkerId, $topicId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $response = $this->surveyDashboardService->getStudentSurveyDrilldown($studentId, $loggedInUserId, $surveyId, $successMarkerId, $topicId);
        return new Response($response);
    }


    /**
     * Gets the three free response questions and responses ("Student Comments") for the success marker page.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Free Response Questions and Responses",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Surveys",
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
     * @Rest\Get("/{studentId}/surveys/{surveyId}/free_response_items", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $surveyId
     * @return Response
     */
    public function listFreeResponseQuestionsAndResponsesAction($studentId, $surveyId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $response = $this->surveyDashboardService->listFreeResponseQuestionsAndResponses($studentId, $loggedInUserId, $surveyId);
        return new Response($response);
    }


    /**
     * Get ISQ Questions and Responses.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get ISQ Questions and Responses",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Surveys",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{id}/surveys/{surveyId}/isq", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @param int $surveyId
     * @return Response
     */
    public function listStudentIsqQuestionsAction($id, $surveyId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $resp = $this->surveyService->listStudentISQQuestions($surveyId, $id, $organizationId, $loggedInUserId);
        return new Response($resp);
    }

    /**
     * Gets student survey questions and responses for comparison
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Survey Questions and Responses for Comparison",
     * output = "Synapse\SurveyBundle\EntityDto\SurveyCompResponseDto",
     * section = "Student Surveys",
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
     * @Rest\Get("/{studentId}/surveys/compare", requirements={"_format"="json"})
     * @QueryParam(name="survey_ids", strict=true, description="comma-separated list of survey ids")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @param int $studentId
     * @return Response
     */
    public function listStudentSurveysCompareAction(ParamFetcher $paramFetcher, $studentId)
    {
        $surveyIds = $paramFetcher->get('survey_ids');
        $surveyIds = explode(',', $surveyIds);
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $response = $this->surveyCompareService->listStudentSurveysCompare($surveyIds, $studentId, $organizationId, $loggedInUserId);
        return new Response($response);
    }


    /**
     * Gets a list of available campuses for the logged in student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campuses for Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Campus",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{studentId}/campuses", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @return Response
     */
    public function getStudentCampusesAction($studentId)
    {
        $result = $this->studentAppointmentService->getStudentCampuses($studentId);
        return new Response($result);
    }

    /**
     *  Show group and course campus connections for a student
     *
     * @ApiDoc(
     * resource = true,
     * description = "Show group and course campus connections for a student",
     * output = "Synapse\CampusConnectionBundle\EntityDto\StudentCampusConnectionsDto",
     * section = "Student Campus",
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
     * @Rest\Get("/{studentId}/campusconnections", requirements={"_format"="json"})
     * @QueryParam(name="show_connections", requirements="true", default ="false", description="show connection")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentCampusConnectionAction($studentId, ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $showConnections = filter_var($paramFetcher->get('show_connections'), FILTER_VALIDATE_BOOLEAN);
        $timeZone = $this->getLoggedInUserOrganization()->getTimeZone();
        if ($showConnections) {
            $result = $this->studentAppointmentService->getStudentCampusConnections($studentId, $organizationId, $timeZone);
        } else {
            $result = $this->studentCampusConnectionService->getCampusConnectionsForStudent($studentId);
        }
        return new Response($result);
    }

    /**
     * Create (Book) an Appointment by a student
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Connections for Student and Campus",
     * input = "Synapse\RestBundle\Entity\AppointmentsDto",
     * output = "Synapse\RestBundle\Entity\AppointmentsDto",
     * section = "Student Appointments",
     * statusCodes = {
     *                  201 = "Academic Update Request was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{studentId}/appointments", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param int $studentId
     * @param AppointmentsDto $appointmentsDto
     * @return Response
     */
    public function createAppointmentsByStudentAction($studentId, AppointmentsDto $appointmentsDto)
    {
        $timeZone = $this->getLoggedInUserOrganization()->getTimeZone();
        $result = $this->studentAppointmentService->createStudentAppointment($studentId, $appointmentsDto, $timeZone);
        return new Response($result);
    }

    /**
     * Cancels appointments by studentId.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cancel Appointments by Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Appointments",
     * statusCodes = {
     *                  200 = "Resource(s) deleted. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{studentId}/appointments", requirements={"_format"="json"})
     * @QueryParam(name="appointmentId", requirements="\d+", default ="", strict=true, description="Appointment Id")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function cancelAppointmentsByStudentAction($studentId, ParamFetcher $paramFetcher)
    {
        $appointmentId = $paramFetcher->get('appointmentId');
        $result = $this->studentAppointmentService->cancelStudentAppointment($studentId, $appointmentId);
        return new Response($result);
    }

    /**
     * Gets a list of courses for a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get List of Courses for Student",
     * output = "Synapse\StudentViewBundle\Service\Impl\studentCourseDto",
     * section = "Student Courses",
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
     * @Rest\Get("/{studentId}/courses", requirements={"_format"="json"})
     * @QueryParam(name="view", requirements="(student)", strict=true, description="User type")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCourseListForStudentsAction($studentId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        if ($loggedInUserId != $studentId) {
            throw new AccessDeniedException();
        }
        $courseList = $this->studentCourseService->listCoursesForStudent($studentId);
        return new Response($courseList);
    }

    /**
     * Adds a student to static lists.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add students to a static list",
     * input = "Synapse\StaticListBundle\EntityDto\StaticListDto",
     * output = "Synapse\StaticListBundle\EntityDto\StaticListDetailsDto",
     * section = "Static Lists",
     * statusCodes = {
     *                  201 = "Academic Update Request was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/staticlists",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param StaticListDto $staticListDto
     * @return Response
     */
    public function addStudentToStaticListsAction(StaticListDto $staticListDto)
    {
        $loggedInUser = $this->getLoggedInUser();
        $studentId = $staticListDto->getStudentId();
        $staticLists = $staticListDto->getStaticListDetails();
        $organizationId = $staticListDto->getOrganizationId();
        $result = $this->staticListStudentService->addStudentToStaticLists($organizationId, $studentId, $staticLists, $loggedInUser);
        return new Response($result);
    }

    /**
     * Gets campus resource details by studentID.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus Resource for Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Campus",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{studentId}/campusresources", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @return Response
     */
    public function getCampusResourceForStudentAction($studentId)
    {
        $campusResForStudentResp = $this->campusResourceService->getCampusResourceForStudent($studentId);
        return new Response($campusResForStudentResp);
    }

    /**
     * Gets all static lists that a student is a member of.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Static Lists For Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student",
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
     * @Rest\Get("/{studentId}/staticlists/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $orgId
     * @return Response
     */
    public function getStaticlistsForStudentAction($studentId, $orgId)
    {
        $loggedInUser = $this->getLoggedInUser();
        $result = $this->staticListService->listAllStaticLists($orgId, $loggedInUser, $studentId);
        return new Response($result);
    }

    /**
     * Get Student Groups List
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get the list of groups the student is belonging to",
     * section = "Groups",
     * statusCodes = {
     *  200 = "Request was successful. Representation of resource(s) returned.",
     *  400 = "Validation error has occurred.",
     *  403 = "Throw an access denied exception",
     *  500 = "There was an internal server error OR errors in the body of the request.",
     *  504 = "Request has timed out."
     * })
     *
     * @Rest\Get("/{studentId}/groups/{organizationId}", requirements={"_format"="json"})
     * @QueryParam(name="is_participant_check", requirements="(true|false)", default="true", strict=false, description="This flag controls whether participation check required or not. Participation check may not required while trying to get the group list for non-participant students. ")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $organizationId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentGroupsListAction($studentId, $organizationId, ParamFetcher $paramFetcher)
    {
        // is_participant_check will be string, to convert it to boolean below conversion would be required,
        $isParticipantCheckRequired = filter_var($paramFetcher->get('is_participant_check'), FILTER_VALIDATE_BOOLEAN);

        $result = $this->studentService->getStudentGroupsList($organizationId, $studentId, $isParticipantCheckRequired);
        return new Response($result);
    }

    /**
     * Remove students from static list from bulk action option API
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Group of Students From Static List",
     * input = "Synapse\StaticListBundle\EntityDto\StaticListDto",
     * section = "Student",
     * statusCodes = {
     *                  204 = "Resource(s) updated. No representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\PUT("/staticlists/{id}",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param StaticListDto $staticListDto
     */
    public function removeStudentsToStaticListsAction(StaticListDto $staticListDto)
    {
        $loggedInUser = $this->getLoggedInUser();
        $this->staticListStudentService->removeStudentsFromStaticLists($staticListDto, $loggedInUser);
    }

    // -------------- COORDINATOR STUDENT MANAGEMENT -----------------------

    /**
     * Updates a survey report's status.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Survey Report Status",
     * input = "Synapse\RestBundle\Entity\SurveyAccessStatusDto",
     * output = "Synapse\RestBundle\Entity\SurveyAccessStatusDto",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  201 = "Survey report status was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\POST("/surveyreport",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param SurveyAccessStatusDto $surveyAccessStatusDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateSurveyReportStatusAction(SurveyAccessStatusDto $surveyAccessStatusDto, ConstraintViolationListInterface $validationErrors)
    {
        $request = Request::createFromGlobals();
        $switchUser = $request->headers->get('switch-user');
        if ($switchUser != null) {
            $loggedInUser = $this->container->get('proxy_user');
        } else {
            $loggedInUser = $this->getLoggedInUser();
        }

        if (count($validationErrors) > 0) {
            return View::create(new Response($surveyAccessStatusDto, [$validationErrors[0]->getMessage()]), 400);
        } else {
            $response = $this->surveyService->updateSurveyReportStatus($surveyAccessStatusDto, $loggedInUser);
            return new Response($response);
        }
    }
        
    /** 
     * Allows a Coordinator to soft delete a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Soft-Deletes a Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) deleted. No representation of resource(s)returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{studentId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @return Response
     */
    public function deleteAction($studentId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        try {
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $status = $this->studentService->softDeleteById($studentId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't delete student ($studentId).", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Allows a Coordinator to add a student to a group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Student to Group",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) added. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{studentId}/group/{groupId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $groupId
     * @return Response
     */
    public function addGroupAction($studentId, $groupId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $status = $this->studentService->addGroup($studentId, $groupId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't add group ($groupId) for student ($studentId).", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Allows a Coordinator to remove a course from a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Student From Course",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) removed. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{studentId}/group/{groupId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $groupId
     * @return Response
     */
    public function removeGroupAction($studentId, $groupId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $status = $this->studentService->removeGroup($studentId, $groupId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't remove group ($groupId) for student ($studentId).", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Adds multiple groups to a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Groups to Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{studentId}/addGroups", requirements={"_format"="json"})
     * @RequestParam(name="grouplist", default ="", strict=false, description="Group List JSON")
     * @Rest\View(statusCode=200)
     *
     * @param $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @throws \Exception
     */
    public function addGroupsAction($studentId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $groupIds = $paramFetcher->get('grouplist');
        $status = $this->studentService->manageStudentGroupMembership($studentId, $groupIds);

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Removes multiple groups from a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Groups from Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) removed. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{studentId}/removeGroups", requirements={"_format"="json"})
     * @RequestParam(name="grouplist", default ="", strict=false, description="Group List JSON")
     * @Rest\View(statusCode=200)
     *
     * @param $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function removeGroupsAction($studentId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $groupIds = $paramFetcher->get('grouplist');
        $status = null;
        $errors = [];

        foreach ($groupIds as $groupId) {
            try {
                $status = $this->studentService->removeGroup($studentId, $groupId);
            } catch (\RuntimeException $e) {
                $errors[] = "Couldn't remove student-$studentId from group-$groupId during a bulk operation.\n";
            }
        }

        if ($errors) {
            return new Response([
                'errors' => $errors
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }
    
    /**
     * Allows a Coordinator to add a course for a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Course For Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{studentId}/course/{courseId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param int $studentId
     * @param int $courseId
     * @return Response
     */
    public function addCourseAction($studentId, $courseId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $status = $this->studentService->addCourse($studentId, $courseId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't add course ($$courseId) for student ($studentId). ", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Allows a Coordinator to remove a course from a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Course from Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) removed. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{studentId}/course/{courseId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param int $courseId
     * @return Response
     */
    public function removeCourseAction($studentId, $courseId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $status = $this->studentService->removeCourse($studentId, $courseId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't remove course ($$courseId) for student ($studentId). ", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Adds multiple courses to a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Courses to Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{studentId}/addCourses", requirements={"_format"="json"})
     * @RequestParam(name="courseIds", default ="", strict=false, description="Course List JSON")
     * @Rest\View(statusCode=200)
     *
     * @param $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function addCoursesAction($studentId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $courseIds = $paramFetcher->get('courseIds');
        $status =  $this->studentService->manageStudentCourseMembership($studentId, $courseIds);

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Removes multiple courses from a student.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Courses from Student",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resources(s) removed. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{studentId}/removeCourses", requirements={"_format"="json"})
     * @RequestParam(name="courseIds", requirements="\d+", default ="", strict=true, description="Course Ids")
     * @Rest\View(statusCode=200)
     *
     * @param $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function removeCoursesAction($studentId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $courseIds = $paramFetcher->get('courseIds');
        $status = true;
        $errors = [];

        foreach ($courseIds as $courseId) {
            try {
                $this->studentService->removeCourse($studentId, $courseId);
            } catch (\RuntimeException $e) {
                $errors[] = "Couldn't remove student-$studentId from course-$courseId during a bulk operation.\n";
                $status = false;
            }
        }

        if ($errors) {
            return new Response([
                'errors' => $errors
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

	/**
     * Update student policy.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Student Policy",
     * input = "Synapse\RestBundle\Entity\StudentPolicyDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Student Coordinator",
     * statusCodes = {
     *                  200 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/policy", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param StudentPolicyDto $studentPolicyDto
     * @return Response
     */
    public function updateStudentPolicyAction(StudentPolicyDto $studentPolicyDto)
    {
        $result = $this->studentService->updatePolicy($studentPolicyDto);
        return $result;
    }	
}
?>

