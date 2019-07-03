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
use Synapse\AcademicBundle\EntityDto\AddUserCourseDto;
use Synapse\AcademicBundle\EntityDto\CourseFacultyListDTO;
use Synapse\AcademicBundle\EntityDto\CourseStudentListDTO;
use Synapse\AcademicBundle\EntityDto\FacultyPermissionDto;
use Synapse\AcademicBundle\Service\Impl\CourseService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Class CoursesController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/courses")
 */
class CoursesController extends AbstractAuthController
{

    /**
     * @var CourseService CourseService
     *
     *      @DI\Inject(CourseService::SERVICE_KEY)
     */
    private $coursesService;
    
    /**
     * @var PersonService PersonService
     *
     *      @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;

    /**
     * Delete faculty from a course
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete a faculty from course",
     * section = "Courses",
     * statusCodes = {
     *                  204 = "Resource deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{courseId}/faculty/{facultyId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $courseId - Course Internal Id
     * @param int $facultyId - Person Internal Id
     *
     * @return Response
     */
    public function deleteFacultyCourseAction($courseId, $facultyId)
    {
        $deleteFacultyFromCourseResponse = $this->coursesService->deleteFacultyFromCourse($courseId, $facultyId);
        return new Response($deleteFacultyFromCourseResponse);
    }

    /**
     * Deletes a student from a course
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Student From Course",
     * section = "Courses",
     * statusCodes = {
     *                  204 = "Student was deleted from course. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{courseId}/student/{studentId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $courseId
     * @param integer $studentId
     * @return Response
     */
    public function deleteStudentCourseAction($courseId, $studentId)
    {
        $coursesService = $this->coursesService->deleteStudentCourse($courseId, $studentId);
        return new Response($coursesService);
    }

    /**
     * Get the list of courses for a coordinator/faculty
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Courses for Coordinator/Faculty",
     * section="Courses",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="user_type",requirements="(faculty|coordinator)", default ="")
     * @QueryParam(name="year", requirements="(all|current|\d+)", strict=true)
     * @QueryParam(name="term", requirements="(all|future|current|\d+)", strict=true)
     * @QueryParam(name="college", strict=true)
     * @QueryParam(name="department", strict=true)
     * @QueryParam(name="filter", strict=true)
     * @QueryParam(name="viewmode", requirements="(json|csv)", strict=true)
     * @QueryParam(name="export", requirements="(courses|staff|students|everything)", strict=true, default="")
     * @QueryParam(name="all_courses", requirements="true|false")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function listCoursesByRoleAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $userType = $paramFetcher->get('user_type');
        if ($userType == 'coordinator') {
            $this->ensureAccess(['coordinator-setup']);
        }
        $year = $paramFetcher->get('year');
        $term = $paramFetcher->get('term');
        $college = $paramFetcher->get('college');
        $department = $paramFetcher->get('department');
        $filter = $paramFetcher->get('filter');
        $viewMode = $paramFetcher->get('viewmode');
        $export = $paramFetcher->get('export');
        $allCourses = $paramFetcher->get('all_courses');
        if ($viewMode == 'csv') {
            $courseList = $this->coursesService->getCoursesByRoleAsCSV($loggedInUserId, $organizationId, $userType, $year, $term, $college, $department, $filter, $export, $allCourses);
            return new Response(['URL' => $courseList]);
        } else {
            $courseList = $this->coursesService->getCoursesByRoleAsJSON($loggedInUserId, $organizationId, $userType, $year, $term, $college, $department, $filter, null, null, true, true, $allCourses);
            return new Response($courseList);
        }
    }

    /**
     * Deletes a course
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete a course",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Courses",
     * statusCodes = {
     *                  204 = "Course was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{courseId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $courseId - Internal course Id
     * @return Response
     */
    public function deleteCourseAction($courseId)
    {
        $course = $this->coursesService->deleteCourse($courseId);
        return new Response($course);
    }

    /**
     * Get course navigation list
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Course Navigation List",
     * section = "Courses",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/navigation", requirements={"_format"="json"})
     * @QueryParam(name="user_type", description="User type")
     * @QueryParam(name="type", description="Filter type", strict=true)
     * @QueryParam(name="year_id", description="Year ID")
     * @QueryParam(name="term_id", description="Term ID")
     * @QueryParam(name="college", description="College Code")
     * @QueryParam(name="department_id", description="department code")
     * @QueryParam(name="subject", description="subject code")
     * @QueryParam(name="course", description="course number")
     * @QueryParam(name="section", description="section number")
     * @QueryParam(name="issearch", description="is search")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCourseNavigationAction(ParamFetcher $paramFetcher)
    {

        $type = $paramFetcher->get('type');
        if($type == 'coordinator'){
            $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        }
        $userType = $paramFetcher->get(UploadConstant::USER_TYPE);
        $queryParam[CourseConstant::YEAR] = $paramFetcher->get('year_id');
        $queryParam[CourseConstant::TERM] = $paramFetcher->get('term_id');
        $queryParam[CourseConstant::COLLEGE] = $paramFetcher->get(CourseConstant::COLLEGE);
        $queryParam[CourseConstant::DEPARTMENT] = $paramFetcher->get('department_id');
        $queryParam['subject'] = $paramFetcher->get('subject');
        $queryParam['course'] = $paramFetcher->get('course');
        $queryParam['section'] = $paramFetcher->get('section');
        $isSearch = $paramFetcher->get('issearch');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $course = $this->coursesService->getCourseNavigation($organizationId, $type, $queryParam, $userType, $loggedInUserId, $isSearch);
        return new Response($course);
    }

    /**
     * Create (Add) a faculty or student to a course
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty/Student to Course",
     * input = "Synapse\AcademicBundle\EntityDto\AddUserCourseDto",
     * section = "Courses",
     * statusCodes = {
     *                  201 = "Faculty or student added. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/roster", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param AddUserCourseDto $addUserCourseDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     * @throws ValidationException
     */
    public function addFacultyStudentCourseAction(AddUserCourseDto $addUserCourseDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($addUserCourseDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUser = $this->getLoggedInUser();
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            if($this->personService->getUserType($loggedInUser) == 'Student')
            {
                throw new AccessDeniedException();
            }

            $saveType = $addUserCourseDto->getType();

            if($saveType === 'faculty'){
                $course = $this->coursesService->addFacultyToCourse($addUserCourseDto, $organizationId, $loggedInUserId);
            }else if($saveType === 'student'){
                $course = $this->coursesService->addStudentToCourse($addUserCourseDto, $organizationId, $loggedInUserId);
            }else {
                throw new ValidationException([
                    "Invalid filter type"
                ], "Invalid filter type", "invalid_type");
            }

            return new Response($course);
        }
    }

    /**
     * Gets a single course's details
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get a Single Course's Details",
     * section = "Courses",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{courseId}", requirements={"_format"="json"})
     * @QueryParam(name="user_type", requirements="(faculty|coordinator)", strict=true, description="User type, user=coordinator | faculty")
     * @QueryParam(name="viewmode", requirements="(json|csv)", strict=true, description="View mode of the response, viewmode= json|csv")
     * @Rest\View(statusCode=200)
     *
     * @param integer $courseId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCourseDetailsAction($courseId, ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get(UploadConstant::USER_TYPE);
        if($type == 'coordinator'){
            $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        }
        $viewmode = $paramFetcher->get('viewmode');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $courseList = $this->coursesService->getCourseDetails($type, $viewmode, $loggedInUserId, $courseId, $organizationId);
        if ($viewmode == 'csv') {
            return new Response(['URL' => $courseList]);
        }
        return new Response($courseList);
    }

    /**
     * Get course list of courses for a student
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Course List for a Student",
     * section = "Courses",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/student/{studentId}", requirements={"_format"="json"})
     * @QueryParam(name="year", requirements="(all|current|\d+)",  default="all", description="Year")
     * @Rest\View(statusCode=200)
     *
     * @param int $studentId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getCourseListForStudentsAction($studentId, ParamFetcher $paramFetcher)
    {
        $yearString = $paramFetcher->get('year');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $courseList = $this->coursesService->listCoursesForStudent($studentId, $loggedInUserId, $organizationId, $yearString);
        return new Response($courseList);
    }

    /**
     * Update Course Faculty Permission
     *
     * @ApiDoc(
     * resource = true,
     * input = "Synapse\AcademicBundle\EntityDto\FacultyPermissionDto",
     * section = "Courses",
     * statusCodes = {
     *                  201 = "Course faculty permission updated. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/permissions", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param FacultyPermissionDto $facultyPermissionDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateFacultyCoursePermissionsetAction(FacultyPermissionDto $facultyPermissionDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($facultyPermissionDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUser = $this->getLoggedInUser();
            $courseList = $this->coursesService->updateFacultyCoursePermissionset($facultyPermissionDto, $loggedInUser);
            return new Response($courseList);
        }
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
     *      500 = "There were errors in the body of the request or an internal server error",
     *      504 = "Request has timed out.",
     *  }
     * )
     *
     * @Rest\Post("/students", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CourseStudentListDTO $courseStudentListDTO
     *
     * @return Response
     */
    public function addStudentsToCourseAction(CourseStudentListDTO $courseStudentListDTO)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $loggedInUser = $this->getLoggedInUser();
        $response = $this->coursesService->addStudentsToCourses($organizationId, $loggedInUser, $courseStudentListDTO);
        return new Response($response['data'], $response['errors']);
    }

    /**
     * Creates the specified courses
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Courses",
     * input = "Synapse\AcademicBundle\EntityDto\CourseFacultyListDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Courses",
     * statusCodes = {
     *                    201 = "Courses created. Representation of Courses was returned.",
     *                    400 = "Validation error has occurred.",
     *                    403 = "Access denied.",
     *                    404 = "Not found.",
     *                    500 = "There were errors in the body of the request or an internal server error.",
     *                    504 = "Request has timed out."
     *               },
     *  views = {"public"}
     * )
     *
     * @Rest\Post("/faculty", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @param CourseFacultyListDTO $courseFacultyListDTO
     * @return Response
     */
    public function addFacultyToCourseAction(CourseFacultyListDTO $courseFacultyListDTO)
    {
        $response = $this->coursesService->addFacultyToCourses($courseFacultyListDTO, $this->getLoggedInUserOrganization(), $this->getLoggedInUser(), true);
        return new Response($response['data'], $response['errors']);
    }
}
