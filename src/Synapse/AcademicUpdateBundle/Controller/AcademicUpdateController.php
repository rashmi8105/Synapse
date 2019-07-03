<?php
namespace Synapse\AcademicUpdateBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesStudentsAdhocAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateCreateService;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateGetService;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateRequestService;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Controller\AbstractSynapseController;
use Synapse\RestBundle\Entity\Response;
use Synapse\UploadBundle\Job\ProcessAcademicUpdateUpload;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;

/**
 * Class AcademicUpdateController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/academicupdates")
 */
class AcademicUpdateController extends AbstractAuthController
{

    // Scaffolding

    /**
     * @var Logger
     *
     *      @DI\Inject("monolog.logger.api")
     */
    private $apiLogger;


    // Service

    /**
     *
     * @var AcademicUpdateCreateService
     *
     *      @DI\Inject("academicupdatecreate_service")
     */
    private $academicUpdateCreateService;

    /**
     *
     * @var AcademicUpdateGetService
     *
     *      @DI\Inject("academicupdateget_service")
     */
    private $academicUpdateRequestGetService;

    /**
     *
     * @var AcademicUpdateRequestService
     *
     *      @DI\Inject("academicupdaterequest_service")
     */
    private $academicUpdateRequestService;

    /**
     *
     * @var AcademicUpdateService
     *
     *      @DI\Inject("academicupdate_service")
     */
    private $academicUpdateService;

    /**
     *
     * @var PersonService
     *
     *      @DI\Inject("person_service")
     */
    private $personService;

    /**
     *
     * @var UploadFileLogService
     *
     *      @DI\Inject("uploadfilelog_service")
     */
    private $uploadFileLogService;

    // constants

    const ORG_ID = "org_id";

    /**
     * Saves an academic update underneath a request.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Saves existing records within an academic update",
     * input = "Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto",
     * output = "Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto",
     * section = "Academic Updates",
     * statusCodes = {
     *      204 = "Academic Update Request was updated. No representation of resource was returned",
     *      400 = "Validation error has occurred",
     *      500 = "There were errors either in the body of the request or an internal server error",
     *      504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("/request/saved", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param AcademicUpdateDto $academicUpdateDto
     * @return Response
     */
    public function saveAcademicUpdateUnderRequestAction(AcademicUpdateDto $academicUpdateDto)
    {
        $loggedInUser = $this->getUser();

        if ($this->personService->getUserType($loggedInUser) == 'Student') {
            throw new AccessDeniedException();
        }

        $response = $this->academicUpdateCreateService->saveAcademicUpdateUnderRequest($academicUpdateDto, $loggedInUser);
        return new Response($response);
    }

    /**
     * Get students that are actively enrolled in an organization, as well as associated with the current course.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Actively Enrolled Students associated with the current course",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}/students", requirements={"_format"="json"})
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     * @QueryParam(name="academic_update_id", requirements="\d+", default ="", strict=false, description="Id of the particular Academic Update Request.")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getActiveCourseStudentsAction($orgId, ParamFetcher $paramFetcher)
    {
        $academicUpdateRequestId = $paramFetcher->get('academic_update_id');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $this->apiLogger->notice(__FUNCTION__ . "; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; AcademicUpdateId: $academicUpdateRequestId; ");

        $studentResponse = $this->academicUpdateService->getStudentsByOrganizationCourse($organizationId, $academicUpdateRequestId);
        return new Response($studentResponse);
    }

    /**
     * Get faculties of actively enrolled and associated with currently running course.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Associated with a Running Course",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}/faculties", requirements={"_format"="json"})
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getActiveCourseFacultiesAction($orgId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; ");

        $facultyResponse = $this->academicUpdateService->getFacultiesByOrganizationCourse($organizationId);
        return new Response($facultyResponse);
    }

    /**
     * Get a list of all the currently active and running courses.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get list of all Active Courses",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}/courses", requirements={"_format"="json"})
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getActiveCourseAction($orgId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; ");
        $courseResponse = $this->academicUpdateService->getActiveCourseByOrganization($organizationId);
        return new Response($courseResponse);
    }

    /**
     * Get a list of all groups within an organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get List of Groups within an Organization",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Returned when successful",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{orgId}/groups", requirements={"_format"="json"})
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getGroupByOrganizationAction($orgId)
    {
        $this->authorize();

        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $orgId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $orgId; ");

        $groupResponse = $this->academicUpdateService->getGroupByOrganization($orgId);
        return new Response($groupResponse);
    }

    /**
     * Get Academic Update Requests that are pending for upload. API is used for initial check to see if there are currently executing uploads for academic updates.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Upload Pending Academic Update Requests",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/pending/{orgId}")
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getPendingAcademicUpdatesUploadAction($orgId)
    {
        $this->authorize();

        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $orgId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $orgId; ");

        $upload = $this->uploadFileLogService->hasPendingView($orgId, 'A');
        return new Response([
            'upload' => $upload
        ], []);
    }

    /**
     * Cancels an existing Academic Update Request.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Cancels an Academic Update Request",
     * section = "Academic Updates",
     * statusCodes = {
     *  204 = "Academic update was deleted. No representation of resource was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     * @QueryParam(name="requestId", requirements="\d+", default ="", strict=true, description="Id of the particular Academic Update Request.")
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function cancelAcademicRequestAction(ParamFetcher $paramFetcher)
    {
        $this->authorize();

        $requestId = $paramFetcher->get('requestId');
        $organizationId = $paramFetcher->get('org_id');
        $loggedInUserId = $this->getUser()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; RequestId: $requestId; ");

        $academicUpdate = $this->academicUpdateService->cancelAcademicUpdateRequest($organizationId, $requestId, $loggedInUserId);
        return new Response($academicUpdate);
    }

    /**
     * Send a reminder to faculty included in an Academic Update Request.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Send Reminder to Faculty in an Academic Update Request",
     * section = "Academic Updates",
     * statusCodes = {
     *  204 = "Academic Update Reminder was updated(sent). No representation of resource was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{orgId}/reminder", requirements={"_format"="json"})
     * @QueryParam(name="requestId", requirements="\d+", default ="", strict=true, description="Academic update request Id")
     * @Rest\View(statusCode=204)
     *
     * @param int $orgId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function sendReminderToFacultyAction($orgId, ParamFetcher $paramFetcher)
    {
        $this->authorize();

        $requestId = $paramFetcher->get('requestId');
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $orgId = $loggedInUser->getOrganization()->getId();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $orgId; RequestId: $requestId; ");

        $sendReminder = $this->academicUpdateService->sendReminderToFaculty($orgId, $requestId, $loggedInUserId);
        return new Response($sendReminder);
    }

    /**
     * Get a list of open and closed Academic Update Requests of a faculty member / coordinator.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get a table of open and closed Academic Update requests of a faculty member / coordinator",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="user_type", default ="coordinator|faculty", strict=false, description="Type of user accessing mapworks.")
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     * @QueryParam(name="request", requirements="(all|myopen|myclosed)", default ="all", strict=true, description="Filter determining the status of request being returned.")
     * @QueryParam(name="filter", default ="", strict=false, description="Filter by request name, open/closed status of a Request.")
     * @QueryParam(name="viewmode", requirements="(json|csv)", default ="json", strict=true, description="View Mode, i.e. json or csv.")
     *
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getAcademicUpdateRequestListAction(ParamFetcher $paramFetcher)
    {
        $this->authorize();
        $userType = $paramFetcher->get('user_type');
        if ($userType == 'coordinator') {
            $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        }
        $organizationId = $paramFetcher->get('org_id');
        $request = $paramFetcher->get('request');
        $filter = $paramFetcher->get('filter');
        $viewmode = $paramFetcher->get('viewmode');

        if ($this->container->has('proxy_user')) {
            $loggedInUserId = $this->container->get('proxy_user')->getId();
            $proxyMessage = "Proxy PersonId: $loggedInUserId;";
        } else {
            $loggedInUserId = $this->getUser()->getId();
            $proxyMessage = "";
        }

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; UserType: $userType; Request: $request; Filter: $filter; ViewMode: $viewmode; ProxyMessage: $proxyMessage; ");

        $response = $this->academicUpdateService->getAcademicUpdateRequestList($userType, $organizationId, $request, $filter, $viewmode, $loggedInUserId);
        if ($viewmode == 'csv') {
            return new Response([
                'URL' => $response
            ]);
        }
        return new Response($response);
    }

    /**
     * Gets the history of Academic Updates for a student in a course.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Academic Update history for a student in a course",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     *)
     *
     * @Rest\Get("/history", requirements={"_format"="json"})
     * @QueryParam(name="org_id", requirements="\d+", default ="", strict=true, description="Id of the organization using mapworks.")
     * @QueryParam(name="course_id", requirements="\d+", default ="", strict=true, description="Id of the course of which's Academic Update History is being retreived.")
     * @QueryParam(name="student_id", requirements="\d+", default ="", strict=true, description="Id of the student who's Academic Update History within a particular course is being retreived.")
     *
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getAcademicUpdateStudentHistoryAction(ParamFetcher $paramFetcher)
    {
        $this->authorize();
        $organizationId = $paramFetcher->get('org_id');
        $courseId = $paramFetcher->get('course_id');
        $studentId = $paramFetcher->get('student_id');
        $loggedInUserId = $this->getUser()->getId();
        $response = $this->academicUpdateRequestGetService->getAcademicUpdateStudentHistory($organizationId, $courseId, $studentId, $loggedInUserId);
        return new Response($response);
    }

    /**
     * Gets all academic updates & request information for the specified academic update request.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Academic Update Request details",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     *)
     *
     * @Rest\Get("/{organizationId}/{requestId}", requirements={"organizationId" = "^\d+$", "_format"="json"})
     * @QueryParam(name="user_type", default ="coordinator", strict=false, description="Type of user accessing mapworks.")
     * @QueryParam(name="filter", default ="all", strict=false, description="Filter the search for a specific Academic Update Request.")
     * @QueryParam(name="output-format", default ="json", strict=false, description="Output Format, i.e. json or csv.")
     * @QueryParam(name="page_no", strict=false, description="Page Number.")
     * @QueryParam(name="offset", strict=false, description="Sets the number of results per page.")
     *
     * @Rest\View(statusCode=200)
     *
     * @param int $organizationId
     * @param int $requestId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getAcademicUpdateRequestAction($organizationId, $requestId, ParamFetcher $paramFetcher)
    {
        $this->authorize();
        $userType = $paramFetcher->get('user_type');
        $filter = $paramFetcher->get('filter');
        $outputFormat = $paramFetcher->get('output-format');

        if ($this->personBeingProxiedInAs) {
            $proxiedUser = $this->personBeingProxiedInAs;
            $loggedInUserId = $proxiedUser->getId();
            $organizationId = $proxiedUser->getOrganization()->getId();
        } else {
            $loggedInUser = $this->loggedInUser;
            $loggedInUserId = $loggedInUser->getId();
            $organizationId = $loggedInUser->getOrganization()->getId();
        }

        $pageNumber = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');

        $this->apiLogger->notice("getAcademicUpdateRequestAction(); PersonId: $loggedInUserId; OrganizationId: $organizationId; UserType: $userType; Filter: $filter; Output Format: $outputFormat; Page Number: $pageNumber, Offset: $offset;");

        if ($outputFormat == 'csv') {
            $academicUpdateRequestDataAsCSV = $this->academicUpdateService->getAcademicUpdateRequestByIdAsCSV($organizationId, $requestId, $userType, $filter, $loggedInUserId);
            $response =  [ 'URL' => $academicUpdateRequestDataAsCSV ];
        } else {
            $academicUpdateRequestDataAsJSON = $this->academicUpdateService->getAcademicUpdateRequestByIdAsJSON($organizationId, $requestId, $userType, $filter, $loggedInUserId, $pageNumber, $offset);
            $response = $academicUpdateRequestDataAsJSON;
        }


        return new Response($response);
    }

    /**
     * Creates an academic update request based on the specified filters.
     *
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create an Academic Update Request",
     * input = "Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto",
     * output = "Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto",
     * section = "Academic Updates",
     * statusCodes = {
     *  201 = "Academic Update Request was created. Representation of resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/request", requirements={"_format"="json"})
     *
     * @Rest\View(statusCode=201)
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createAcademicUpdateRequestAction(AcademicUpdateCreateDto $academicUpdateCreateDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->authorize();
        $loggedInUser = $this->getUser();
        $organizationId = $loggedInUser->getOrganization()->getId();

        if (count($validationErrors) > 0) {
            return View::create(new Response($academicUpdateCreateDto, [$validationErrors[0]->getMessage()]), SynapseConstant::INVALID_REQUEST_STATUS_CODE);
        } else {
            if ($this->container->has('proxy_user')) {
                $loggedInUserId = $this->container->get('proxy_user')->getId();
                $proxyMessage = "Proxy PersonId: $loggedInUserId;";
            } else {
                $loggedInUserId = $loggedInUser->getId();
                $proxyMessage = "";
            }

            $requestJson = $this->get("request")->getContent();

            $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; ProxyMessage: $proxyMessage; Request: $requestJson;");

            $response = $this->academicUpdateRequestService->initiateAcademicUpdateRequestJob($academicUpdateCreateDto, $loggedInUser);

            return new Response($response);
        }
    }

    /**
     * Gets the number of Academic Update uploads that have been made by a user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get the number of Academic Uploads by a user",
     * section = "Academic Updates",
     * statusCodes = {
     *  200 = "Request was successful. Representation of Resource(s) was returned",
     *  400 = "Validation error has occurred",
     *  500 = "There were errors either in the body of the request or an internal server error",
     *  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/uploadcount")
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getDownloadCountAction()
    {

        $this->authorize();
        $loggedInUser = $this->getUser();
        $organizationId = $loggedInUser->getOrganization()->getId();

        if ($this->container->has('proxy_user')) {
            $loggedInUserId = $this->container->get('proxy_user')->getId();
            $proxyMessage = "Proxy PersonId: $loggedInUserId;";
        } else {
            $loggedInUserId = $loggedInUser->getId();
            $proxyMessage = "";
        }

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; ProxyMessage: $proxyMessage; ");

        $response = $this->academicUpdateRequestGetService->getAcademicUpdateUploadCount($loggedInUserId);
        return new Response($response);
    }


   /**
    * Updates the count of Academic Updates created in a single session.
    *
    * @ApiDoc(
    * resource = true,
    * description = "Update the Count of Academic Updates created [DEPRECATED]",
    * input = "Synapse\AcademicUpdateBundle\Controller\academicUpdateCreateDto",
    * output = "Synapse\AcademicUpdateBundle\Controller\academicUpdateCreateDto",
    * section = "Academic Updates",
    * statusCodes = {
    *  201 = "Academic Update count was updated. Representation of resource(s) was returned",
    *  400 = "Validation error has occurred",
    *  500 = "There were errors either in the body of the request or an internal server error",
    *  504 = "Request has timed out"
    * }
    * )
    *
    * @Rest\Post("/updatecount")
    * @Rest\View(statusCode=200)
    *
    * @param AcademicUpdateCreateDto $academicUpdateCreateDto
    * @return Response
    * @deprecated
    */
    public function getUpdateCountAction(AcademicUpdateCreateDto $academicUpdateCreateDto)
    {
        $this->authorize();

        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();
        $requestJson = $this->get("request")->getContent();

        $this->apiLogger->notice(__FUNCTION__."; LoggedInUser: $loggedInUser; LoggedInUserId: $loggedInUserId; OrganizationId: $organizationId; Request: $requestJson;");

        $response = $this->academicUpdateRequestCounterService->createRequest($academicUpdateCreateDto, $loggedInUserId);
        return new Response($response);
    }

    private function authorize()
    {
        if (!($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Gets the count of academic updates in the created request.
     *
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets the count of academic updates in the created request.",
     * input = "Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto",
     * output = "Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto",
     * section = "Academic Updates",
     * statusCodes = {
     *                    201 = "Returned when successful",
     *                    400 = "Validation errors have occurred or invalid request",
     *                    403 = "Throw an access denied exception,Returned when user does not have permissions",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out. Please re-try."
     *               }
     * )
     *
     * @Rest\Post("/request/count", requirements={"_format"="json"})
     *
     * @Rest\View(statusCode=201)
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function getAcademicUpdateCountForRequestAction(AcademicUpdateCreateDto $academicUpdateCreateDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->authorize();
        if (count($validationErrors) > 0) {
            return View::create(new Response($academicUpdateCreateDto, [$validationErrors[0]->getMessage()]), SynapseConstant::INVALID_REQUEST_STATUS_CODE);
        } else {
            if ($this->container->has('proxy_user')) {
                $loggedInPersonObject = $this->container->get('proxy_user');
            } else {
                $loggedInPersonObject = $this->getUser();
            }
            $organizationId = $loggedInPersonObject->getOrganization()->getId();
            $loggedInPersonId = $loggedInPersonObject->getId();
            $requestJSON = $this->get("request")->getContent();
            $this->apiLogger->notice(__FUNCTION__ . "; AcademicUpdateCreateDto: $requestJSON ; PersonId: $loggedInPersonId ; OrganizationId : $organizationId");

            $response = $this->academicUpdateRequestService->getAcademicUpdateCountForRequest($academicUpdateCreateDto, $loggedInPersonObject);
            return new Response($response);
        }
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
     *               }
     * )
     *
     * @Rest\Post("/adhoc", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createAdhocAcademicUpdateAction(CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($coursesStudentsAdhocAcademicUpdateDTO, $errors), 400);
        }

        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();
        $requestJSON = $this->get("request")->getContent();
        $this->apiLogger->notice(__FUNCTION__ . "; Request Json: $requestJSON; PersonId: $loggedInUserId; OrganizationId: $organizationId");

        $response = $this->academicUpdateService->createAcademicRecord($coursesStudentsAdhocAcademicUpdateDTO, $loggedInUser, true, 'adhoc');
        return new Response($response['data'], $response['errors']);
    }



    /**
     * Submits a saved academic update request
     *
     * @ApiDoc(
     * resource = true,
     * description = "Submit Saved Academic Update",
     * input = "Synapse\AcademicUpdateBundle\EntityDto\CoursesStudentsAdhocAcademicUpdateDTO",
     * output = "Synapse\AcademicUpdateBundle\EntityDto\CoursesStudentsAdhocAcademicUpdateDTO",
     * section = "Academic Updates",
     * statusCodes = {
     *                    201 = "Returned when successful",
     *                    400 = "Validation errors have occurred.",
     *                    403 = "Access denied exception",
     *                    404 = "Not Found",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out. Please re-try."
     *               },
     * )
     *
     * @Rest\Post("/academicrecord", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function updateAcademicRecordAction(CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO, ConstraintViolationListInterface $validationErrors)
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

        $response = $this->academicUpdateService->createAcademicRecord($coursesStudentsAdhocAcademicUpdateDTO, $loggedInUser, false, 'bulk');
        return new Response($response['data'], $response['errors']);
    }

}
