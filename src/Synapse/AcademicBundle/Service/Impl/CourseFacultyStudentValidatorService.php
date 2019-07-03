<?php
namespace Synapse\AcademicBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Service\Impl\AbstractService;

/**
 * Handle Faculty/Student validation in a Course
 *
 * @DI\Service("course_faculty_student_validator_service")
 */
class CourseFacultyStudentValidatorService extends AbstractService
{
    
    const SERVICE_KEY = 'course_faculty_student_validator_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var OrgCourseFacultyRepository
     */
    private $orgCourseFacultyRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * CourseFacultyStudentValidatorService constructor.
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container"),
     * })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseStudent');
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');
    }

    /**
     * Validates that the given person ID is not already in a course as a faculty member or student and throws an error
     * if they are already found in the course. Error messages are applicable for when attempting to add the person as a faculty
     *
     * @param int $personId
     * @param int $organizationId
     * @param int $courseId
     */
    public function validateAdditionOfFacultyToCourse($personId, $organizationId, $courseId)
    {
        $orgCourseStudent = $this->orgCourseStudentRepository->findOneBy(['course' => $courseId, 'person' => $personId, 'organization' => $organizationId]);
        $orgCourseFaculty = $this->orgCourseFacultyRepository->findOneBy(['course' => $courseId, 'person' => $personId, 'organization' => $organizationId]);

        if (isset($orgCourseStudent)) {
            $personFullName = $orgCourseStudent->getPerson()->getFirstname() . " " . $orgCourseStudent->getPerson()->getLastname();
            $courseName = $orgCourseStudent->getCourse()->getCourseName();
            $validationErrorMessage = "$personFullName is already in course $courseName as student. Cannot add as faculty";
            $this->logger->error($validationErrorMessage);
            throw new ValidationException([$validationErrorMessage], $validationErrorMessage, "User_already_assigned");
        }

        if (isset($orgCourseFaculty)) {
            $this->logger->error("Course - Adding Faculty to a Course - " . CourseConstant::USER_ASSIGNED);
            throw new ValidationException([
                CourseConstant::USER_ASSIGNED
            ], CourseConstant::USER_ASSIGNED, "User_already_assigned");
        }
    }

    /**
     * Validates that the given person ID is not already in a course as a faculty member or student and throws an error
     * if they are already found in the course. Error messages are applicable for when attempting to add the person as a student
     *
     * @param int $personId
     * @param int $organizationId
     * @param int $courseId
     */
    public function validateAdditionOfStudentToCourse($personId, $organizationId, $courseId)
    {
        $orgCourseStudent = $this->orgCourseStudentRepository->findOneBy(['course' => $courseId, 'person' => $personId, 'organization' => $organizationId]);
        $orgCourseFaculty = $this->orgCourseFacultyRepository->findOneBy(['course' => $courseId, 'person' => $personId, 'organization' => $organizationId]);

        if (isset($orgCourseFaculty)) {
            $personFullName = $orgCourseFaculty->getPerson()->getFirstname() . " " . $orgCourseFaculty->getPerson()->getLastname();
            $courseName = $orgCourseFaculty->getCourse()->getCourseName();
            $validationErrorMessage = "$personFullName is already in course $courseName as faculty. Cannot add as student";
            $this->logger->error($validationErrorMessage);
            throw new ValidationException([$validationErrorMessage], $validationErrorMessage, "User_already_assigned");
        }

        if (isset($orgCourseStudent)) {
            $this->logger->error("Course - Adding Student to a Course - " . CourseConstant::USER_ASSIGNED);
            throw new ValidationException([
                CourseConstant::USER_ASSIGNED
            ], CourseConstant::USER_ASSIGNED, "User_already_assigned");
        }
    }

}
