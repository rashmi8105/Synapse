<?php

namespace Synapse\RestBundle\Controller\V2;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\AcademicBundle\EntityDto\CourseListDTO;
use Synapse\AcademicBundle\EntityDto\CourseFacultyListDTO;
use Synapse\AcademicBundle\EntityDto\CourseStudentListDTO;
use Synapse\AcademicBundle\Service\Impl\CourseService;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response as Response;


/**
 * Class CourseController
 *
 * @package Synapse\RestBundle\Controller
 *
 * @Rest\Version("2.0")
 * @Rest\Prefix("/courses")
 *
 */
class CoursesController extends AbstractAuthV2Controller
{
    /**
     * @var CourseService
     * @DI\Inject(CourseService::SERVICE_KEY)
     */
    private $courseService;

    /**
     * @var APIValidationService
     * @DI\Inject(APIValidationService::SERVICE_KEY)
     */
    protected $apiValidationService;

    /**
     * @var IDConversionService
     * @DI\Inject(IDConversionService::SERVICE_KEY);
     */
    private $idConversionService;

    /**
     * Get list of students associated with specific course.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get list of students associated with specific course",
     * section = "Course",
     * views = { "public" },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Validation errors have occurred.",
     *     500 = "There was either errors with the body of the request or an internal server error.",
     *     504 = "Request has timed out. Please re-try."     *
     * })
     *
     * @Rest\GET("/{courseID}/students", requirements={"_format"="json"})
     *
     * @Rest\View(statusCode=200)
     *
     * @param int $courseID - External course Id
     * @return Response
     */
    public function getStudentsInCourseAction($courseID)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $convertedCourseIDs = $this->idConversionService->convertCourseIDs($courseID, $organizationId, false);

        $validationErrors = $this->idConversionService->validationErrors;
        $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $validationErrors);
        if ($validationErrors) {
            $validationErrorsArray = array_map('current', $validationErrors);
            $validationErrorString = implode(',', $validationErrorsArray);
            throw new SynapseValidationException("The following errors occurred when validating your request: " . $validationErrorString);
        }

        $studentIds = $this->courseService->getStudentIdsInCourse($convertedCourseIDs[0], $organizationId, $courseID);
        if (empty($studentIds)) {
            $studentIds = "There are no students found for the specified course.";
        }
        return new Response($studentIds);
    }

    /**
     * Creates the specified courses
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Courses",
     * input = "Synapse\RestBundle\Entity\CoursesListDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Courses",
     * statusCodes = {
     *                    201 = "Courses created. Representation of Courses was returned.",
     *                    400 = "Validation errors have occurred.",
     *                    403 = "Access denied exception",
     *                    404 = "Not found.",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CourseListDTO $courseListDTO
     * @return Response
     */
    public function createCoursesAction(CourseListDTO $courseListDTO)
    {
        $requestKey = "course_list";
        $this->isRequestSizeAllowed($requestKey);
        $response = $this->courseService->createCourses($courseListDTO, $this->getLoggedInUserOrganization(), $this->getLoggedInUser());
        return new Response($response['data'], $response['errors']);
    }

    /**
     * Updates the specified courses
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Courses",
     * input = "Synapse\RestBundle\Entity\CoursesListDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Courses",
     * statusCodes = {
     *                    201 = "Courses updated. Representation of Courses was returned.",
     *                    400 = "Validation errors have occurred.",
     *                    403 = "Access denied exception.",
     *                    404 = "Not found.",
     *                    500 = "There was either errors with the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CourseListDTO $courseListDTO
     * @return Response
     */
    public function updateCoursesAction(CourseListDTO $courseListDTO)
    {
        $requestKey = "course_list";
        $this->isRequestSizeAllowed($requestKey);
        $response = $this->courseService->updateCourses($courseListDTO, $this->getLoggedInUserOrganization(), $this->getLoggedInUser());
        return new Response($response['data'], $response['errors']);
    }

    /**
     * Get the course list for an organization
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Get Organization Course List",
     *  output={
     *     "class"="Synapse\RestBundle\Entity\Response"
     *  },
     *  section="Courses",
     *  statusCodes = {
     *      200 = "Request was successful. Representation of resource(s) was returned.",
     *      400 = "Validation error has occurred.",
     *      403 = "Access denied.",
     *      404 = "Not found",
     *      500 = "Internal server error.",
     *      504 = "Request has timed out."
     *  },
     *  view = {"public"}
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @QueryParam(name="year", default="all", description="Skyfactor defined year ID filter to narrow down the result set.")
     * @QueryParam(name="term", default="all", description="Organization external term ID filter to narrow down the result set.")
     * @QueryParam(name="college", default="all", description="College code filter to narrow down the result set")
     * @QueryParam(name="department", default="all", description="Department code filter to narrow down the result set")
     * @QueryParam(name="filter", description="String filter compared against the subject code, course number, course name, or the subject code appended with the course number.")
     * @QueryParam(name="records_per_page", description="Number of records per page of the paginated result set.")
     * @QueryParam(name="page_number", description="Page number of the paginated result set.")
     * @QueryParam(name="format_result_set", requirements="true|false", description="Boolean to indicate whether or not the user desires the course list formatted by year, then term, then college, then department, then course, then section.")
     *
     * @return Response
     */
    public function getCoursesForOrganizationAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $year = $paramFetcher->get('year');
        $term = $paramFetcher->get('term');
        $college = $paramFetcher->get('college');
        $department = $paramFetcher->get('department');
        $filter = $paramFetcher->get('filter');
        $recordsPerPage = $paramFetcher->get('records_per_page');
        $pageNumber = $paramFetcher->get('page_number');
        $formatResultSet = filter_var($paramFetcher->get('format_result_set'), FILTER_VALIDATE_BOOLEAN);

        $courseList = $this->courseService->getCoursesByRoleAsJSON($loggedInUserId, $organizationId, "coordinator", $year, $term, $college, $department, $filter, $recordsPerPage, $pageNumber, false, $formatResultSet);

        return new Response($courseList);
    }

    /**
     * Adds the specified students to the specified courses.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Add students to courses",
     *  input= "Synapse\AcademicBundle\EntityDTO\CourseStudentListDTO",
     *  section="Courses",
     *  statusCodes = {
     *      201 = "Resources created. Representation of resources was returned.",
     *      400 = "Validation error has occurred.",
     *      403 = "Access denied.",
     *      404 = "Not found.",
     *      500 = "There were errors either in the body of the request or an internal server error",
     *      504 = "Request has timed out."
     *  },
     *  view = {"public"}
     * )
     *
     * @Rest\Post("/students", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CourseStudentListDTO $courseStudentListDTO
     *
     * @return Response
     */
    public function addStudentsToCoursesAction(CourseStudentListDTO $courseStudentListDTO)
    {
        $loggedInUser = $this->getLoggedInUser();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $response = $this->courseService->addStudentsToCourses($organizationId, $loggedInUser, $courseStudentListDTO, false);
        return new Response($response['data'], $response['errors']);
    }

    /**
     * Deletes the specified course
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Delete a course",
     *  output={
     *     "class"="Synapse\RestBundle\Entity\Response"
     *  },
     *  section="Courses",
     *  statusCodes = {
     *      204 = "Object was deleted. Representation of resources was not returned.",
     *      400 = "Validation error has occurred.",
     *      404 = "Not found",
     *      500 = "There was an internal server error.",
     *      504 = "Request has timed out.",
     *  },
     *  view = {"public"}
     * )
     *
     * @Rest\Delete("/{courseId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $courseId - External course Id
     *
     * @return Response
     * @throws SynapseValidationException
     */
    public function deleteCoursesAction($courseId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $convertedCourseObject = $this->idConversionService->getConvertedCourseObject($courseId, $organizationId, false);

        $course = $this->courseService->deleteCourse($convertedCourseObject->getId(), false);
        $responseArray = [];
        if ($course) {
            $responseArray[$courseId] = 'Course successfully deleted.';
        }
        return new Response($responseArray);
    }

    /**
     * Gets the faculty within the specified course
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Gets course faculty",
     *  output={
     *     "class"="Synapse\RestBundle\Entity\Response"
     *  },
     *  section="Courses",
     *  statusCodes = {
     *      200 = "Request was successful. Representation of resources was returned.",
     *      400 = "Validation error has occurred.",
     *      404 = "Not found",
     *      500 = "There was an internal server error.",
     *      504 = "Request has timed out.",
     *  },
     *  view = {"public"}
     * )
     *
     * @Rest\Get("/{courseId}/faculty", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $courseId - course external id
     *
     * @return Response
     */
    public function getFacultyInCourseAction($courseId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $convertedCourseObject = $this->idConversionService->getConvertedCourseObject($courseId, $organizationId, false);
        $facultyList = $this->courseService->getFacultyByCourse($organizationId, $convertedCourseObject, false);
        return new Response($facultyList);
    }

    /**
     * Deletes the specified Faculty to the specified course.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Delete Faculty from course",
     *  section="Courses",
     *  statusCodes = {
     *      204 = "Object was deleted. Representation of resources was not returned.",
     *      400 = "Validation error has occurred.",
     *      404 = "Not found",
     *      500 = "There was an internal server error.",
     *      504 = "Request has timed out.",
     *  },
     *  view = {"public"}
     * )
     *
     * @Rest\Delete("/{courseId}/faculty/{facultyId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param string $courseId - Course Section Id
     * @param string $facultyId - Faculty External Id
     *
     * @return Response
     */
    public function deleteFacultyFromCourseAction($courseId, $facultyId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $isInternal = false;
        $convertedCourseObject = $this->idConversionService->getConvertedCourseObject($courseId, $organizationId, $isInternal);

        $convertedPersonIds = $this->idConversionService->convertPersonIds($facultyId, $organizationId, $isInternal);
        $validationPersonErrors = $this->idConversionService->validationErrors;
        $this->apiValidationService->addErrorsToOrganizationAPIErrorCount($organizationId, $validationPersonErrors, $isInternal);

        $course = $this->courseService->deleteFacultyFromCourse($convertedCourseObject->getId(), $convertedPersonIds[0], true, $isInternal);
    }

    /**
     * Deletes the specified student to the specified course.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Delete student from course",
     *  section="Courses",
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned for bad request",
     *      403 = "Unauthorized access",
     *      500 = "There was an internal server error.",
     *      504 = "Request has timed out. Please re-try.",
     *  },
     *  view = {"public"}
     * )
     *
     * @Rest\Delete("/{courseId}/students/{studentId}", requirements={"_format"="json"})
     *
     * @Rest\View(statusCode=204)
     *
     * @param string $courseId - External course Id
     * @param string $studentId - External student Id
     * @return Response
     */
    public function deleteStudentFromCourseAction($courseId, $studentId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $isInternal = false;
        $convertedCourseObject = $this->idConversionService->getConvertedCourseObject($courseId, $organizationId, $isInternal);

        $convertedPersonIds = $this->idConversionService->convertPersonIds($studentId, $organizationId, $isInternal);
        $validationPersonErrors = $this->idConversionService->validationErrors;
        $this->apiValidationService->addErrorsToOrganizationAPIErrorCount($organizationId, $validationPersonErrors, $isInternal);

        $course = $this->courseService->deleteStudentCourse($convertedCourseObject->getId(), $convertedPersonIds[0], true, $isInternal);
    }

    /**
     * Adds the specified faculty to the specified courses.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Add faculty to courses",
     *  input= "Synapse\AcademicBundle\EntityDto\CourseFacultyListDTO",
     *  section="Courses",
     *  statusCodes = {
     *      201 = "Course Faculty created. Representation of Course Faculty was returned.",
     *      400 = "Validation error has occurred.",
     *      403 = "Access denied.",
     *      404 = "Not found.",
     *      500 = "There were errors either in the body of the request or an internal server error.",
     *      504 = "Request has timed out.",
     *  },
     *  view = {"public"}
     * )
     *
     * @Rest\Post("/faculty", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CourseFacultyListDTO $courseFacultyListDTO
     *
     * @return Response
     */
    public function addFacultyToCoursesAction(CourseFacultyListDTO $courseFacultyListDTO)
    {
        $response = $this->courseService->addFacultyToCourses($courseFacultyListDTO, $this->getLoggedInUserOrganization(), $this->getLoggedInUser(), false);
        return new Response($response['data'], $response['errors']);
    }
}