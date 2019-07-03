<?php
namespace Synapse\StudentViewBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Service\Impl\CourseService;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StudentViewBundle\EntityDto\StudentCourseListDto;
use Synapse\StudentViewBundle\EntityDto\StudentCoursesArrayDto;
use Synapse\StudentViewBundle\EntityDto\StudentCourseDto;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\AcademicBundle\EntityDto\FacultyDetailsDto;
use Synapse\StudentViewBundle\Util\Constants\StudentViewConstants;

/**
 * @DI\Service("studentcourse_service")
 */
class StudentCourseService extends AbstractService
{

    const SERVICE_KEY = 'studentcourse_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    // Services
    /**
     * @var CourseService
     */
    private $courseService;

    //Repositories
    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * StudentCourseService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //Scaffolding
        $this->container = $container;

        //Services
        $this->courseService = $this->container->get("course_service");

        //Repositories
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository("SynapseAcademicUpdateBundle:AcademicUpdate");
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository("SynapseAcademicBundle:OrgCourses");
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonStudent");
        $this->personRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Person");
    }

    /**
     * List courses for student on student's course tab.
     *
     * TODO: This function closely resembles CourseService::listCoursesForStudent()
     * TODO: Does not have the permissions checks for the logged in user since the logged in user is the student.
     * TODO: Migrate the API that calls this function to use the API that calls the above mentioned function
     *
     * @param int $personId
     * @return StudentCourseListDto
     */
    public function listCoursesForStudent($personId)
    {
        //Get person object and verify that person exists
        $person = $this->personRepository->find($personId, new SynapseValidationException('The requested person does not exist.'));

        //Get the organizationID
        $organization = $person->getOrganization();
        $organizationId = $organization->getId();

        //Get the list of courses associated with the student
        $courseDetails = $this->orgCoursesRepository->getCoursesForStudent($organizationId, $personId);
        $total = count($courseDetails);

        //Get the list of academic updates for that student
        $academicUpdates = $this->getAcademicUpdates($organization, $personId);

        //Get the list of faculty for the organization, to later be compared to a list of courses for the organization
        //TODO: Fix this method of getting faculty/course association
        $faculty = $this->getFaculty($organizationId);

        $studentCourseDto = new StudentCourseListDto();
        $studentCourseDto->setStudentId($personId);
        $studentCourseDto->setTotalCourse($total);

        //Format the course list in year / term / college / department sections
        $coursesByTermAndDepartment = $this->courseService->formatStudentCourseList($courseDetails);

        $courseList = array();

        //For each year / term / college / dept. combination, create and set DTOs and values.
        if ($total) {
            foreach ($coursesByTermAndDepartment as $coursesForSingleTermAndDepartment) {
                $courseListDto = new StudentCoursesArrayDto();
                $courseListDto->setYear($coursesForSingleTermAndDepartment['year_id']);
                $courseListDto->setTerm($coursesForSingleTermAndDepartment['term_name']);
                $courseListDto->setCollege($coursesForSingleTermAndDepartment['college_code']);
                $courseListDto->setDepartment($coursesForSingleTermAndDepartment['dept_code']);

                $courseDtoArray = array();
                foreach ($coursesForSingleTermAndDepartment['courses'] as $course) {
                    $courseId = $course['org_course_id'];

                    $courseDto = new StudentCourseDto();
                    $courseDto->setCourseId($courseId);
                    $courseDto->setUniqueCourseSectionId($course['course_section_id']);
                    $subjectCourse = $course['subject_code'] . $course['course_number'];
                    $courseDto->setSubjectCourse($subjectCourse);
                    $courseDto->setCourseTitle($course['course_name']);
                    $courseDto->setSectionId($course['section_number']);
                    $courseDto->setTime($course['days_times']);
                    $courseDto->setLocation($course['location']);

                    if ($course['current_or_future_term_course'] == "1") {
                        $absence = isset($academicUpdates[$courseId]['absence']) ? $academicUpdates[$courseId]['absence'] : '';
                        $inProgressGrade = isset($academicUpdates[$courseId]['inprogressgrade']) ? $academicUpdates[$courseId]['inprogressgrade'] : '';
                        $comments = isset($academicUpdates[$courseId]['comments']) ? $academicUpdates[$courseId]['comments'] : '';

                        $courseDto->setAbsense($absence);
                        $courseDto->setInProgressGrade($inProgressGrade);
                        $courseDto->setComments($comments);

                    } else {
                        $finalGrade = isset($academicUpdates[$courseId]['finalgrade']) ? $academicUpdates[$courseId]['finalgrade'] : '';
                        $courseDto->setFinalGrade($finalGrade);
                    }

                    if (!empty($academicUpdates[$courseId]['date'])) {

                        if (!is_object($academicUpdates[$courseId]['date'])) {
                            $dateObj = new \DateTime($academicUpdates[$courseId]['date']);
                            $dateObj->setTimezone(new \DateTimeZone('UTC'));
                            $academicUpdates[$courseId]['date'] = $dateObj;
                        } else {
                            $academicUpdates[$courseId]['date']->setTimezone(new \DateTimeZone('UTC'));
                        }

                        $courseDto->setDateStamp($academicUpdates[$courseId]['date']);
                    }

                    $courseSpecificFaculty = $this->getFacultyDTO($faculty, $courseId);
                    $courseDto->setFaculties($courseSpecificFaculty);
                    $courseDtoArray[] = $courseDto;
                }

                $courseListDto->setCourses($courseDtoArray);
                $courseList[] = $courseListDto;
                $studentCourseDto->setCourseListTable($courseList);
            }
        }
        return $studentCourseDto;
    }

    private function getFaculty($orgId)
    {
        $staff = [];
        $facultyResults = $this->orgCoursesRepository->getFacultyList($orgId);
        if ($facultyResults) {
            foreach ($facultyResults as $faculty) {
                $staff[$faculty['id']][] = $faculty;
            }
        }
        return $staff;
    }

    /**
     * Get all academic updates associated with the student and organization.
     *
     * @param Organization $organization
     * @param int $personId
     * @return array
     */
    private function getAcademicUpdates($organization, $personId)
    {
        $academicUpdatesDetail = [];

        $academicUpdates = $this->academicUpdateRepository->findBy([
            'org' => $organization,
            'personStudent' => $personId,
            'sendToStudent' => 1,
            'status' => 'closed'
        ], [
            'updateDate' => 'desc'
        ]);

        $isViewAbsent = $organization->getCanViewAbsences();
        $isViewInGrade = $organization->getCanViewInProgressGrade();
        $isViewComments = $organization->getCanViewComments();

        foreach ($academicUpdates as $academicUpdate) {
            $courseId = $academicUpdate->getOrgCourses()->getId();
            if (!in_array($courseId, array_keys($academicUpdatesDetail))) {
                if ($isViewAbsent) {
                    $academicUpdatesDetail[$courseId]['absence'] = $academicUpdate->getAbsence();
                } else {
                    $academicUpdatesDetail[$courseId]['absence'] = '';
                }

                if ($isViewInGrade) {
                    $academicUpdatesDetail[$courseId]['inprogressgrade'] = $academicUpdate->getGrade();
                } else {
                    $academicUpdatesDetail[$courseId]['inprogressgrade'] = '';
                }

                if ($isViewComments) {
                    $academicUpdatesDetail[$courseId]['comments'] = $academicUpdate->getComment();
                } else {
                    $academicUpdatesDetail[$courseId]['comments'] = '';
                }

                $academicUpdatesDetail[$courseId]['finalgrade'] = $academicUpdate->getFinalGrade();
                $academicUpdatesDetail[$courseId]['date'] = $academicUpdate->getUpdateDate();
            }
        }
        return $academicUpdatesDetail;
    }

    private function getFacultyDTO($faculty, $courseId)
    {
        $facultyDetailsArray = [];
        if (!empty($faculty[$courseId])) {
            foreach ($faculty[$courseId] as $staff) {
                $facultyDetailsDto = new FacultyDetailsDto();
                $facultyDetailsDto->setFacultyId($staff['personId']);
                $firstName = ($staff['personFirstName']) ? $staff['personFirstName'] : '';
                $lastName = ($staff['personLastName']) ? $staff['personLastName'] : '';
                $facultyDetailsDto->setFacultyName($lastName . ', ' . $firstName);
                $facultyDetailsArray[] = $facultyDetailsDto;
            }
        }
        return $facultyDetailsArray;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (!isset($object) || empty($object)) {
            $this->logger->error("Student View - Course List - " . $message);
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }
}