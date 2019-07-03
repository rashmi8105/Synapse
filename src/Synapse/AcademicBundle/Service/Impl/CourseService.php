<?php
namespace Synapse\AcademicBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\DTO\CourseFacultyDto;
use Synapse\AcademicBundle\Entity\OrgAcademicTerms;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\Entity\OrgCourseFaculty;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\Entity\OrgCourseStudent;
use Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseDTO;
use Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseListDTO;
use Synapse\AcademicBundle\EntityDto\AddUserCourseDto;
use Synapse\AcademicBundle\EntityDto\CoordinatorCourseDto;
use Synapse\AcademicBundle\EntityDto\CourseDTO;
use Synapse\AcademicBundle\EntityDto\CourseFacultyListDTO;
use Synapse\AcademicBundle\EntityDto\CourseListDTO;
use Synapse\AcademicBundle\EntityDto\CourseNavigationDto;
use Synapse\AcademicBundle\EntityDto\CourseNavigationListDto;
use Synapse\AcademicBundle\EntityDto\CourseSearchResultDTO;
use Synapse\AcademicBundle\EntityDto\CourseStudentListDTO;
use Synapse\AcademicBundle\EntityDto\CourseStudentsDto;
use Synapse\AcademicBundle\EntityDto\FacultyPermissionDto;
use Synapse\AcademicBundle\EntityDto\SingleCourseDto;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicRecordRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestCourseRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\InvalidArgumentException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\FacultyService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\StudentService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Service\Impl\CourseFacultyUploadService;
use Synapse\UploadBundle\Service\Impl\CourseStudentUploadService;

/**
 * @DI\Service("course_service")
 */
class CourseService extends CourseServiceHelper
{

    const SERVICE_KEY = 'course_service';

    //Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    // Repository

    /**
     * @var AcademicRecordRepository
     */
    private $academicRecordRepository;

    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var AcademicUpdateRequestCourseRepository
     */
    private $academicUpdateRequestCourseRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgCourseFacultyRepository
     */
    private $orgCourseFacultyRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCourseRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    // Service

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var APIValidationService
     */
    private $apiValidationService;

    /**
     * @var CourseFacultyStudentValidatorService
     */
    private $courseFacultyStudentValidatorService;

    /**
     * @var CourseFacultyUploadService
     */
    private $courseFacultyUploadService;

    /**
     * @var CourseStudentUploadService
     */
    private $courseStudentUploadService;

    /**
     * @var CSVUtilityService
     */
    private $CSVUtilityService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    /**
     * @var FacultyService
     */
    private $facultyService;

    /**
     * @var IDConversionService
     */
    private $idConversionService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var StudentService
     */
    private $studentService;

    /**
     * CourseService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        //Repositories
        $this->academicRecordRepository = $this->repositoryResolver->getRepository(AcademicRecordRepository::REPOSITORY_KEY);
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->academicUpdateRequestCourseRepository = $this->repositoryResolver->getRepository(AcademicUpdateRequestCourseRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
        $this->orgCourseRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

        //Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->apiValidationService = $this->container->get(APIValidationService::SERVICE_KEY);
        $this->courseFacultyStudentValidatorService = $this->container->get(CourseFacultyStudentValidatorService::SERVICE_KEY);
        $this->courseFacultyUploadService = $this->container->get(CourseFacultyUploadService::SERVICE_KEY);
        $this->courseStudentUploadService = $this->container->get(CourseStudentUploadService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->CSVUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->entityValidationService = $this->container->get(EntityValidationService::SERVICE_KEY);
        $this->facultyService = $this->container->get(FacultyService::SERVICE_KEY);
        $this->idConversionService = $this->container->get(IDConversionService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->studentService = $this->container->get(StudentService::SERVICE_KEY);
    }

    /**
     * Deletes faculty member from a specified course
     *
     * @param int $courseId
     * @param int $facultyId
     * @param boolean $rebuildDataFile
     * @param boolean $isInternal
     * @return int
     * @throws AccessDeniedException|SynapseValidationException
     */
    public function deleteFacultyFromCourse($courseId, $facultyId, $rebuildDataFile = true, $isInternal = true)
    {
        if ($isInternal) {
            $this->rbacManager->checkAccessToOrganizationUsingPersonId($facultyId);
            if (!$this->rbacManager->hasCoordinatorAccess())
                throw new AccessDeniedException();
        }

        // Find course using course internal id
        $course = $this->orgCourseRepository->find($courseId, new SynapseValidationException("Course not found"));
        $organizationId = $course->getOrganization()->getId();

        // Find faculty using course_id and faculty_id
        $facultyCourse = $this->orgCourseFacultyRepository->findOneBy([
            'course' => $courseId,
            'person' => $facultyId
        ]);
        if (empty($facultyCourse)) {
            if (!$isInternal) {
                $validationPersonErrors = [
                    [$course->getExternalId() => 'Faculty not found at this course']
                ];
                $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $validationPersonErrors);
            }
            throw new SynapseValidationException("Faculty not found at this course");
        }

        // Remove faculty
        $this->orgCourseFacultyRepository->delete($facultyCourse);

        if ($rebuildDataFile) {
            $this->courseFacultyUploadService->updateDataFile($organizationId);
        }

        return $courseId;
    }

    /**
     * Deletes a course from a specified student
     *
     * @param int $courseId
     * @param int $studentId
     * @param boolean $rebuildDataFile
     * @param boolean $isInternal
     * @return int
     * @throws AccessDeniedException|SynapseValidationException
     */
    public function deleteStudentCourse($courseId, $studentId, $rebuildDataFile = true, $isInternal = true)
    {
        if ($isInternal) {
            $this->rbacManager->checkAccessToOrganizationUsingPersonId($studentId);
            if (!$this->rbacManager->hasCoordinatorAccess()) {
                throw new AccessDeniedException();
            }
        }

        // Find course using course internal id
        $course = $this->orgCourseRepository->find($courseId, new SynapseValidationException("Course not found"));

        // Get organization_id from $course
        $organizationId = $course->getOrganization()->getId();

        $studentCourse = $this->orgCourseStudentRepository->findOneBy(['course' => $courseId, 'person' => $studentId]);
        if (!$studentCourse) {
            $errorMessage = "Student not found at this course";
            if (!$isInternal) {
                $validationCourseError = [[$courseId => $errorMessage]];
                $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $validationCourseError);
            }
            throw new SynapseValidationException($errorMessage);
        }

        $this->orgCourseStudentRepository->delete($studentCourse);
        if ($rebuildDataFile) {
            $this->courseStudentUploadService->updateDataFile($organizationId);
        }

        return $courseId;
    }

    /**
     * Gets course details.
     *
     * @param string $type
     * @param string $viewMode
     * @param int $userId
     * @param int $courseId
     * @param int $organizationId
     * @return SingleCourseDto|string
     * @throws AccessDeniedException
     */
    public function getCourseDetails($type, $viewMode, $userId, $courseId, $organizationId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $this->isUserTypeExists($type);

        $course = $this->orgCourseRepository->findOneBy([
            'id' => $courseId,
            'organization' => $organizationId
        ]);
        $this->isObjectExist($course, 'Course not found', 'course_not_found');
        $this->isFacultyExist($type, $this->orgCourseFacultyRepository, $organizationId, $courseId, $userId);
        $facultyList = $this->orgCourseRepository->getSingleCourseFacultiesDetails($courseId, $organizationId);
        $studentList = $this->orgCourseRepository->getParticipantStudentsInCourse($courseId, $organizationId);
        if ($viewMode == "json") {
            $courseDTO = $this->getCourseDetailsJSON($courseId, $facultyList, $studentList, $course, $type, $organizationId, $userId);
        } else {
            $courseData = array_merge($facultyList, $studentList);
            $csvHeader = [
                'lastname' => 'Last Name',
                'firstname' => 'First Name',
                'primary_email' => 'Email',
                'external_id' => 'External ID',
                'permissionset' => 'Permission Set'
            ];
            $fileName = $organizationId . "-" . $courseId . "-view-course-roaster.csv";
            $CSVFilename = $this->CSVUtilityService->generateCSV(SynapseConstant::S3_ROOT . SynapseConstant::S3_ROASTER_UPLOAD_DIRECTORY . "/", $fileName, $courseData, $csvHeader);
            return $CSVFilename;
        }
        return $courseDTO;
    }

    /**
     * List courses a student is enrolled in
     *
     * @param int $studentId
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param string $yearString -'all'|'current'|\d+
     * @return CoordinatorCourseDto
     * @throws AccessDeniedException
     */
    public function listCoursesForStudent($studentId, $loggedInUserId, $organizationId, $yearString = 'all')
    {
        $this->rbacManager->checkAccessToOrganizationUsingPersonId($studentId);

        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        //TODO: Remove use of this DTO in this method. Not a good DTO for this use.
        $coordinatorCourseDto = new CoordinatorCourseDto();
        $coordinatorCourseDto->setStudentId($studentId);
        $courseListArray = array();

        // check view courses permission for the logged in staff with respect to student
        $viewCoursesFlag = $this->rbacManager->hasStudentAccess('viewCourses', null, $studentId, $loggedInUserId);
        $createViewAcademicUpdateFlag = $this->rbacManager->hasStudentAccess('createViewAcademicUpdate', null, $studentId, $loggedInUserId);
        $viewAllAcademicUpdateCoursesFlag = $this->rbacManager->hasStudentAccess('viewAllAcademicUpdateCourses', null, $studentId, $loggedInUserId);
        $viewAllFinalGradesFlag = $this->rbacManager->hasStudentAccess('viewAllFinalGrades', null, $studentId, $loggedInUserId);

        //If the user has permissions to view courses for that student, get all associated courses.
        //Otherwise, return that the student is in 0 courses.
        if ($viewCoursesFlag) {
            $coordinatorCourseDto->setCreateViewAcademicUpdate($createViewAcademicUpdateFlag);
            $coordinatorCourseDto->setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCoursesFlag);
            $coordinatorCourseDto->setViewAllFinalGrades($viewAllFinalGradesFlag);

            if ($yearString == 'current') {
                $currentOrgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicYear($organizationId);
                $yearString = $currentOrgAcademicYear['year_id'];
            }

            //Based on the user type of $personId, get the associated courses.
            $courseDetails = $this->orgCourseRepository->getCoursesForStudent($organizationId, $studentId, '', $yearString);

            //Get faculty for this organization.
            $faculty = $this->getFaculty($organizationId);
            //TODO: Fix this method of getting faculty/course association. It's highly inefficient, as it's grabbing
            // every course-faculty combination in the organization just to populate this student's course list.

            //Get & set the total course count.
            $total = count($courseDetails);
            $coordinatorCourseDto->setTotalCourse($total);

            //Format the course list into groups based on year, term, college, and department.
            $courseList = $this->formatStudentCourseList($courseDetails);
            if ($total) {

                //For each grouping of year / term / college / department, format each individual course underneath into the proper format.
                foreach ($courseList as $courses) {
                    $courseListDto = new AcademicUpdateCourseListDTO();
                    $courseListDto->setYear($courses['year_id']);
                    $courseListDto->setTerm($courses['term_name']);
                    $courseListDto->setCollege($courses['college_code']);
                    $courseListDto->setDepartment($courses['dept_code']);
                    $courseListDto->setCurrentOrFutureTerm($courses['current_or_future_term_course']);
                    if (!empty($courses['courses'])) {
                        $courseDtoArray = array();
                        foreach ($courses['courses'] as $course) {
                            $courseId = $course['org_course_id'];
                            $courseDto = new AcademicUpdateCourseDTO();
                            $courseDto->setCourseId($courseId);
                            $courseDto->setUniqueCourseSectionId($course['course_section_id']);
                            $subjectCourse = $course['subject_code'] . $course['course_number'];
                            $courseDto->setSubjectCourse($subjectCourse);
                            $courseDto->setCourseTitle($course['course_name']);
                            $courseDto->setSectionId($course['section_number']);
                            if (array_key_exists('days_times', $course) && isset($course['days_times'])) {
                                $courseDto->setTime($course['days_times']);
                            } else {
                                $courseDto->setTime('');
                            }
                            $courseDto->setLocation($course['location']);

                            $academicRecord = $this->academicRecordRepository->findOneBy(
                                [
                                    'personStudent' => $studentId,
                                    'organization' => $organizationId,
                                    'orgCourses' => $courseId
                                ]
                            );

                            //If the course is a current or future academic course and the logged in user has access to the student's academic updates,
                            //Show the in progress grades, absences, and comments from any academic updates on those courses. Otherwise, show the final grade on the course.
                            if ($academicRecord) {
                                if ($course['current_or_future_term_course'] == "1") {
                                    //TODO:CreateViewAcademicUpdateFlag only gives them permission to view their own courses. This is giving them permission to view all courses.
                                    if ($createViewAcademicUpdateFlag || $viewAllAcademicUpdateCoursesFlag) {
                                        $studentAbsences = $academicRecord->getAbsence();
                                        $inProgressGrade = $academicRecord->getInProgressGrade();
                                        $comments = $academicRecord->getComment();
                                        $absencesUpdateDateTime = $academicRecord->getAbsenceUpdateDate();
                                        $inProgressGradeUpdateDateTime = $academicRecord->getInProgressGradeUpdateDate();

                                        if ($absencesUpdateDateTime) {
                                            $absencesUpdateDateTime = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $absencesUpdateDateTime);
                                        }

                                        if ($inProgressGradeUpdateDateTime) {
                                            $inProgressGradeUpdateDateTime = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $inProgressGradeUpdateDateTime);
                                        }

                                        $courseDto->setAbsense($studentAbsences);
                                        $courseDto->setAbsencesUpdateDate($absencesUpdateDateTime);
                                        $courseDto->setInProgressGrade($inProgressGrade);
                                        $courseDto->setInProgressGradeUpdateDate($inProgressGradeUpdateDateTime);
                                        $courseDto->setComments($comments);
                                    }
                                } else {
                                    $finalGrade = $academicRecord->getFinalGrade();
                                    $finalGradeUpdateDateTime = $academicRecord->getFinalGradeUpdateDate();

                                    if ($finalGradeUpdateDateTime) {
                                        $finalGradeUpdateDateTime = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $finalGradeUpdateDateTime);
                                    }

                                    if ($viewAllFinalGradesFlag) {
                                        $courseDto->setFinalGrade($finalGrade);
                                        $courseDto->setFinalGradeUpdateDate($finalGradeUpdateDateTime);
                                    }
                                }

                                $courseDto->setDateStamp($academicRecord->getUpdateDate());
                            }


                            $facultyDetailsArray = $this->getFacultyDTO($faculty, $courseId);
                            $courseDto->setFacultyDetails($facultyDetailsArray);
                            $courseDtoArray[] = $courseDto;
                        }
                        $courseListDto->setCourse($courseDtoArray);
                        $courseListArray[] = $courseListDto;
                    }
                    $coordinatorCourseDto->setCourseListTable($courseListArray);
                }
            }
        } else {
            $coordinatorCourseDto->setTotalCourse(0);
            $coordinatorCourseDto->setCourseListTable($courseListArray);
        }
        return $coordinatorCourseDto;
    }

    /**
     * Deletes a specified course by its $courseId
     *
     * @param int $courseId
     * @param boolean $isInternal
     * @return int
     * @throws SynapseValidationException|AccessDeniedException
     */
    public function deleteCourse($courseId, $isInternal = true)
    {
        $course = $this->orgCourseRepository->find($courseId);
        if (empty($course)) {
            throw new SynapseValidationException("Course not found");
        }

        if ($isInternal) {
            $this->rbacManager->checkAccessToOrganization($course->getOrganization()->getId());
        }

        $academicUpdates = $this->academicUpdateRepository->findBy([
            "orgCourses" => $courseId
        ]);
        $academicUpdatesRequestCourseInstanceArray = $this->academicUpdateRequestCourseRepository->findBy([
            "orgCourses" => $courseId
        ]);
        if (!empty($academicUpdates) || !empty($academicUpdatesRequestCourseInstanceArray)) {
            throw new SynapseValidationException("Can't remove as academic updates are submitted for course");
        }

        $courseStudents = $this->orgCourseStudentRepository->findBy([
            "course" => $courseId
        ]);
        if (isset($courseStudents)) {
            foreach ($courseStudents as $courseStudent) {
                $this->orgCourseStudentRepository->delete($courseStudent);
            }
        }

        $courseFaculties = $this->orgCourseFacultyRepository->findBy([
            "course" => $courseId
        ]);
        if (isset($courseFaculties)) {
            foreach ($courseFaculties as $courseFaculty) {
                $this->orgCourseFacultyRepository->delete($courseFaculty);
            }
        }

        // Remove the course
        $this->orgCourseRepository->delete($course);

        // Initialize here as getting Circular reference detected for service
        $courseUploadService = $this->container->get('course_upload_service');
        $courseUploadService->updateDataFile($course->getOrganization()->getId());

        return $courseId;
    }

    /**
     * Creates the specified courses.
     *
     * @param CourseListDTO $courseListDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @return array
     */
    public function createCourses($courseListDTO, $organization, $loggedInUser)
    {
        $courseList = $courseListDTO->getCourseList();
        $successfullyCreatedCourses = [];
        $errorCourses = [];

        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($courseList as $courseDTO) {
            $courseObject = new OrgCourses();
            try {
                // get year id
                $yearId = $courseDTO->getYearId();
                $dataProcessingExceptionHandler->addErrors("Year Id $yearId is not valid for this organization.", 'YearId', 'required');
                $organizationAcademicYear = $this->orgAcademicYearRepository->findOneBy(
                    [
                        'yearId' => $yearId,
                        'organization' => $organization
                    ],
                    $dataProcessingExceptionHandler
                );
                $dataProcessingExceptionHandler->resetAllErrors();

                // get term Id
                $termId = $courseDTO->getTermId();
                $dataProcessingExceptionHandler->addErrors("Term Id $termId is not valid for this organization.", 'TermId', 'required');
                $organizationAcademicTerm = $this->orgAcademicTermRepository->findOneBy(
                    [
                        'termCode' => $termId,
                        'orgAcademicYearId' => $organizationAcademicYear,
                        'organization' => $organization
                    ],
                    $dataProcessingExceptionHandler
                );
                $dataProcessingExceptionHandler->resetAllErrors();

                $courseObject = $this->setCourseObject($courseObject, $courseDTO, $organization, $loggedInUser, $organizationAcademicYear, $organizationAcademicTerm);


                $courseObject->setExternalId($courseDTO->getCourseSectionId());
                $validationGroup = [
                    'required' => true,
                    null => false
                ];
                // Validate course
                $dataProcessingExceptionHandler = $this->entityValidationService->validateAllDoctrineEntityValidationGroups($courseObject, $dataProcessingExceptionHandler, $validationGroup);

                // set optional parameters to null
                $courseEmptyObject = new OrgCourses();
                $courseObject = $this->entityValidationService->restoreErroredProperties($courseObject, $courseEmptyObject, $dataProcessingExceptionHandler);

                $this->orgCourseRepository->persist($courseObject);
                $successfullyCreatedCourses[] = $courseDTO;
                unset($courseObject);

                // Throw error object if error object having any optional error
                $this->entityValidationService->throwErrorIfContains($dataProcessingExceptionHandler);

            } catch (DataProcessingExceptionHandler $dpeh) {
                $errors = $dpeh->getAllErrors();
                //Build output error array

                $perCourseError = $this->buildCourseErrorArray($courseDTO, $errors);
                $errorCourses [] = $perCourseError;

            }
            $dataProcessingExceptionHandler->resetAllErrors();
        }

        //Build the response.
        $responseArray = [];
        $responseArray['created_count'] = count($successfullyCreatedCourses);
        $responseArray['created_records'] = $successfullyCreatedCourses;

        $responseErrorArray['error_count'] = count($errorCourses);
        $responseErrorArray['error_records'] = $errorCourses;

        $returnArray['data'] = $responseArray;
        $returnArray['errors'] = $responseErrorArray;
        return $returnArray;
    }

    /**
     * Builds a course error array
     *
     * @param CourseDTO $courseDto
     * @param array $errorArray
     * @return array
     */
    private function buildCourseErrorArray($courseDto, $errorArray)
    {

        $responseErrorArray = [];
        $responseErrorArray['year_id'] = $courseDto->getYearId();
        $responseErrorArray['term_id'] = $courseDto->getTermId();
        $responseErrorArray['college_code'] = $courseDto->getCollegeCode();
        $responseErrorArray['department_code'] = $courseDto->getDepartmentCode();
        $responseErrorArray['subject_code'] = $courseDto->getSubjectCode();
        $responseErrorArray['course_number'] = $courseDto->getCourseNumber();
        $responseErrorArray['course_name'] = $courseDto->getCourseName();
        $responseErrorArray['course_section_id'] = $courseDto->getCourseSectionId();
        $responseErrorArray['location'] = $courseDto->getLocation();
        $responseErrorArray['days_times'] = $courseDto->getDaysTimes();
        $responseErrorArray['credit_hours'] = $courseDto->getCreditHours();

        $keyMapping = [
            'orgAcademicYear' => 'YearId',
            'orgAcademicTerms' => 'TermId',
            'deptCode' => 'departmentCode'
        ];

        foreach ($errorArray as $error) {
            $setErrorArray = [];
            foreach ($error as $camelCaseKey => $errorMessage) {
                if (array_key_exists($camelCaseKey, $keyMapping)) {
                    $camelCaseKey = $keyMapping[$camelCaseKey];
                }
                $underscoreKey = $this->dataProcessingUtilityService->convertCamelCasedStringToUnderscoredString($camelCaseKey);
                $getName = 'get' . $camelCaseKey;
                $errorValue = $courseDto->$getName();
                $setErrorArray['value'] = $errorValue;
                $setErrorArray['message'] = $errorMessage;
                $responseErrorArray[$underscoreKey] = $setErrorArray;
                break;
            }
        }
        return $responseErrorArray;
    }

    /**
     * Sets a course object
     *
     * @param OrgCourses $courseObject
     * @param CourseDTO $courseDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @param OrgAcademicYear $organizationAcademicYear
     * @param OrgAcademicTerms $organizationAcademicTerm
     * @return OrgCourses
     */
    private function setCourseObject($courseObject, $courseDTO, $organization, $loggedInUser, $organizationAcademicYear, $organizationAcademicTerm)
    {
        $currentDateTime = new \DateTime();

        $courseObject->setOrganization($organization);
        $courseObject->setCreatedAt($currentDateTime);
        $courseObject->setCreatedBy($loggedInUser);
        $courseObject->setModifiedBy($loggedInUser);
        $courseObject->setModifiedAt($currentDateTime);
        $courseObject->setOrgAcademicYear($organizationAcademicYear);
        $courseObject->setOrgAcademicTerms($organizationAcademicTerm);

        $courseObject->setCollegeCode($courseDTO->getCollegeCode());
        $courseObject->setCourseName($courseDTO->getCourseName());
        $courseObject->setCourseSectionId($courseDTO->getCourseSectionId());
        $courseObject->setCourseNumber($courseDTO->getCourseNumber());
        $courseObject->setCreditHours($courseDTO->getCreditHours());
        $courseObject->setDaysTimes($courseDTO->getDaysTimes());
        $courseObject->setDeptCode($courseDTO->getDepartmentCode());
        $courseObject->setSubjectCode($courseDTO->getSubjectCode());
        $courseObject->setSectionNumber($courseDTO->getSectionNumber());
        $courseObject->setLocation($courseDTO->getLocation());

        return $courseObject;
    }

    /**
     * Get Course Navigation
     *
     * @param int $organizationId
     * @param string $type
     * @param string $queryParam
     * @param string $userType
     * @param int $loggedInUserId
     * @param boolean $isSearch
     * @return CourseNavigationDto
     */
    public function getCourseNavigation($organizationId, $type, $queryParam = '', $userType = '', $loggedInUserId = null, $isSearch = false)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $courseNavigation = new CourseNavigationDto();
        $courseNavigation->setOrganizationId($organizationId);
        $courseNavigation->setType($type);
        if (! empty($queryParam)) {
            $courseNavigation->setYearId($queryParam['year']);
            $courseNavigation->setTermId($queryParam['term']);
            $courseNavigation->setCollegeCode($queryParam[CourseConstant::COLLEGE]);
            $courseNavigation->setDepartmentId($queryParam[CourseConstant::DEPARTMENT]);
            $courseNavigation->setSubjectCode($queryParam['subject']);
            $courseNavigation->setCourseNumber($queryParam[CourseConstant::COURSE_FIELD]);
        }
        $courses = array();
        $orgainzation = $this->organizationRepository->find($organizationId);
        $currentDate = $this->getTimeZone(CourseConstant::NAVIGATION, $orgainzation);
        if ($type == 'year') {
            $coursesYear = $this->orgAcademicYearRepository->findBy([
                CourseConstant::ORGANIZATION => $organizationId
            ], [
                CourseConstant::ARRAY_YEARID => 'ASC'
            ]);
            foreach ($coursesYear as $years) {
                $courseList = new CourseNavigationListDto();
                $courseList->setKey($years->getYearId()->getId());
                if ($userType == 'coordinator') {
                    $yearName = $years->getName() . '(' . $years->getYearId()->getId() . ')';
                } else {
                    $yearName = $years->getName();
                }
                $courseList->setValue($yearName);
                /*
                 * Setting current_year flag to determine current year
                 */
                $startDate = $years->getStartDate()->format('Y-m-d');
                $endDate = $years->getEndDate()->format('Y-m-d');
                if ($startDate <= $currentDate && $endDate >= $currentDate) {
                    $courseList->setCurrentYear(true);
                } else {
                    $courseList->setCurrentYear(false);
                }
                $courses[] = $courseList;
            }
        } else {
            $viewCoursesFlag = $this->rbacManager->hasAccess(['viewCourses']);

            $courseValues = $this->orgCourseRepository->getCourseDetailList($organizationId, $type, $queryParam, $currentDate, $loggedInUserId,$userType,$isSearch,$viewCoursesFlag);
            if ($courseValues) {
                $courses = $this->courseNavigationDTO($courseValues, $type, $queryParam, $organizationId, $userType);
            }
        }
        $courseNavigation->setCourseNavigation($courses);
        return $courseNavigation;
    }

    /**
     * Find course by organization external_id
     *
     * @param int $externalId
     * @param int $organizationId
     * @param boolean $checkAuth
     * @return object|null
     */
    public function findOneByExternalIdOrg($externalId, $organizationId, $checkAuth = true)
    {
        if( $checkAuth )
        {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }
        return $this->orgCourseRepository->findOneBy([
            'externalId' => $externalId,
            CourseConstant::ORGANIZATION => $organizationId
        ]);
    }

    /**
     * Flushes the entity manager
     */
    public function flush()
    {
        $this->orgCourseRepository->flush();
    }

    /**
     * Flushes the entity manager. Identical to the function above
     */
    public function clear()
    {
        $this->orgCourseRepository->flush();
    }

    /**
     * Updates course permissions for a faculty member
     *
     * @param FacultyPermissionDto $facultyPermissionDto
     * @param null $loggedInUser - variable not used
     * @throws AccessDeniedException
     */
    public function updateFacultyCoursePermissionset(FacultyPermissionDto $facultyPermissionDto, $loggedInUser = null)
    {

        $course = $this->orgCourseRepository->findOneBy([
            'id' => $facultyPermissionDto->getCourseId()
        ]);

        $this->isObjectExist($course, CourseConstant::COURSE_NOT_FOUND, CourseConstant::COURSE_NOT_FOUND_KEY);

        $person = $this->personRepository->find($facultyPermissionDto->getPersonId());
        $this->isObjectExist($person, CourseConstant::PERSON_NOT_FOUND, CourseConstant::PERSON_NOT_FOUND_KEY);

        $this->rbacManager->checkAccessToOrganization($course->getOrganization()->getId());
        if (! $this->rbacManager->hasCoordinatorAccess())
            throw new AccessDeniedException();

        $facultyPermission = $this->orgCourseFacultyRepository->findOneBy([
            'course' => $facultyPermissionDto->getCourseId(),
            'organization' => $facultyPermissionDto->getOrganizationId(),
            'person' => $facultyPermissionDto->getPersonId()
        ]);
        $this->isObjectExist($facultyPermission, CourseConstant::FACULTY_NOT_ASSIGNED, CourseConstant::FACULTY_NOT_ASSIGNED_KEY);
        $orgPermissionset = $this->orgPermissionsetRepository->find($facultyPermissionDto->getPermissionsetId());
        $this->isObjectExist($orgPermissionset, CourseConstant::ERROR_PERMISSIONSET_NOT_FOUND, CourseConstant::ERROR_PERMISSIONSET_NOT_FOUND_KEY);
        $facultyPermission->setOrgPermissionset($orgPermissionset);
        $this->orgCourseFacultyRepository->flush();
        $facultyUploadService = $this->container->get(CourseConstant::COURSE_FACULTY_UPLOAD_SERVICE);
        $facultyUploadService->updateDataFile($course->getOrganization()->getId());
    }

    /**
     * Adds a faculty member to a course.
     *
     * @param AddUserCourseDto $addUserCourseDto
     * @param int $organizationId
     * @param int $loggedInUserId
     * @return AddUserCourseDto
     * @throws ValidationException
     */
    public function addFacultyToCourse(AddUserCourseDto $addUserCourseDto, $organizationId, $loggedInUserId)
    {
        $userToCourseData = $this->getUserToCourseData($addUserCourseDto, $organizationId, $loggedInUserId, 'Faculty');
        // Added this to check Faculty in course.
        $this->courseFacultyStudentValidatorService->validateAdditionOfFacultyToCourse($userToCourseData['personId'], $organizationId, $userToCourseData['courseId']);
        $orgCourseFaculty = new OrgCourseFaculty();
        $orgCourseFaculty->setOrganization($userToCourseData['organization']);
        $orgCourseFaculty->setCourse($userToCourseData['course']);
        $orgCourseFaculty->setPerson($userToCourseData['person']);
        $this->orgCourseFacultyRepository->persist($orgCourseFaculty);
        $this->courseFacultyUploadService->updateDataFile($organizationId);
        return $addUserCourseDto;
    }

    /**
     * Adds the specified faculty to the specified courses.
     *
     * @param CourseFacultyListDTO $courseFacultyListDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @param boolean $isInternal
     * @return array
     */
    public function addFacultyToCourses(CourseFacultyListDTO $courseFacultyListDTO, $organization, $loggedInUser, $isInternal = true)
    {
        $courseFacultyList = $courseFacultyListDTO->getCourseFacultyList();
        $successfullyCreatedCourseFaculty = [];
        $errorCourseFaculty = [];

        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($courseFacultyList as $courseFaculty) {
            $courseFacultyObject = new OrgCourseFaculty();
            try {
                $inputCourseId = $courseFaculty->getCourseId();
                $inputFacultyId = $courseFaculty->getFacultyId();

                // validate course
                if ($isInternal) {
                    $fieldName = 'id';
                } else {
                    $fieldName = 'courseSectionId';
                }
                $dataProcessingExceptionHandler->addErrors("Course ID $inputCourseId is not valid for this organization.", 'course_id', 'required');
                $courseObject = $this->orgCourseRepository->findOneBy(
                    [
                        $fieldName => $inputCourseId,
                        'organization' => $organization
                    ],
                    $dataProcessingExceptionHandler
                );
                $dataProcessingExceptionHandler->resetAllErrors();

                $internalCourseId = $courseObject->getId();

                // Validate the faculty's person ID.
                if ($isInternal) {
                    $fieldName = 'id';
                } else {
                    $fieldName = 'externalId';
                }
                $dataProcessingExceptionHandler->addErrors("Person ID $inputFacultyId is not valid at the organization.", 'faculty_id');
                $personObject = $this->personRepository->findOneBy(
                    [
                        $fieldName => $inputFacultyId,
                        'organization' => $organization
                    ],
                    $dataProcessingExceptionHandler
                );
                $dataProcessingExceptionHandler->resetAllErrors();

                $internalPersonId = $personObject->getId();

                // validate for person being faculty
                $isPersonFaculty = $this->facultyService->isPersonAFaculty($internalPersonId);
                if (!$isPersonFaculty) {
                    $dataProcessingExceptionHandler->addErrors("Faculty ID $inputFacultyId is not valid at the organization.", "faculty_id");
                    throw $dataProcessingExceptionHandler;
                }

                // validate for faculty being active
                $isFacultyActive = $this->facultyService->isFacultyActive($internalPersonId);
                if (!$isFacultyActive) {
                    $dataProcessingExceptionHandler->addErrors("Faculty ID $inputFacultyId is not active.", "faculty_id");
                    throw $dataProcessingExceptionHandler;
                }


                // Validate that the faculty ID contained is not already in the course as a student
                $isFacultyInCourseAsStudent = $this->isFacultyInCourseAsStudent($internalPersonId, $internalCourseId);
                if ($isFacultyInCourseAsStudent) {
                    $personName = $personObject->getFirstname() ." ".  $personObject->getLastname();
                    $dataProcessingExceptionHandler->addErrors("$personName is already in the course as a student. Cannot add as Faculty/Staff", "faculty_id");
                    throw $dataProcessingExceptionHandler;
                }

                // Validate that the faculty is not already in the course
                $isNotAlreadyInCourse = $this->isFacultyInCourse($internalPersonId, $internalCourseId);
                if ($isNotAlreadyInCourse) {
                    $dataProcessingExceptionHandler->addErrors("Faculty ID $inputFacultyId already exist at this course $inputCourseId .", "faculty_id");
                    throw $dataProcessingExceptionHandler;
                }

                // Validate permissionset name
                $permissionSetName = $courseFaculty->getPermissionsetName();
                if ($permissionSetName) {
                    $dataProcessingExceptionHandler->addErrors("Permissionset name $permissionSetName is not a valid permissionset at this organization.", 'permissionset_name');
                    $permissionSetObject = $this->orgPermissionsetRepository->findOneBy(
                        [
                            'permissionsetName' => $permissionSetName,
                            'organization' => $organization
                        ],
                        $dataProcessingExceptionHandler
                    );
                    $dataProcessingExceptionHandler->resetAllErrors();
                    $courseFacultyObject->setOrgPermissionset($permissionSetObject);
                }

                // Create Course Faculty
                $courseFacultyObject->setPerson($personObject);
                $courseFacultyObject->setCourse($courseObject);
                $courseFacultyObject->setOrganization($organization);
                $courseFacultyObject->setCreatedBy($loggedInUser);
                $courseFacultyObject->setModifiedBy($loggedInUser);

                // validate org course faculty
                $dataProcessingExceptionHandler = $this->entityValidationService->validateDoctrineEntity($courseFacultyObject, $dataProcessingExceptionHandler);
                $this->orgCourseFacultyRepository->persist($courseFacultyObject);
                $successfullyCreatedCourseFaculty[] = $this->buildCourseFacultyResponseArray($courseFaculty, [], $isInternal);
                unset($courseFacultyObject);
            } catch (DataProcessingExceptionHandler $dpeh) {
                $errors = current($dpeh->getAllErrors());

                //Build output error array
                $perCourseFacultyError = $this->buildCourseFacultyResponseArray($courseFaculty, $errors, $isInternal);
                $errorCourseFaculty [] = $perCourseFacultyError;
            }
            $dataProcessingExceptionHandler->resetAllErrors();
        }
        //Build the response.
        $responseArray = [];
        $responseArray['created_count'] = count($successfullyCreatedCourseFaculty);
        $responseArray['created_records'] = $successfullyCreatedCourseFaculty;

        $responseErrorArray['error_count'] = count($errorCourseFaculty);
        $responseErrorArray['error_records'] = $errorCourseFaculty;

        $returnArray['data'] = $responseArray;
        $returnArray['errors'] = $responseErrorArray;
        return $returnArray;
    }

    /**
     * Checks to see if the faculty is already in the course
     *
     * @param int $facultyId
     * @param int $courseId
     * @return boolean
     */
    public function isFacultyInCourse($facultyId, $courseId)
    {
        $courseFacultyObject =  $this->orgCourseFacultyRepository->findOneBy([
            'course' => $courseId,
            'person' => $facultyId
        ]);

        if ($courseFacultyObject) {
            $isFacultyInCourse = true;
        } else {
            $isFacultyInCourse = false;
        }

        unset($courseFacultyObject);
        return $isFacultyInCourse;
    }

    /**
     * Checks to see if the faculty is already in the course as a student.
     *
     * @param int $facultyId
     * @param int $courseId
     * @return bool
     */
    public function isFacultyInCourseAsStudent($facultyId, $courseId)
    {
        $courseStudentObject =  $this->orgCourseStudentRepository->findOneBy([
            'course' => $courseId,
            'person' => $facultyId
        ]);

        if ($courseStudentObject) {
            $isFacultyInCourseAsStudent = true;
        } else {
            $isFacultyInCourseAsStudent = false;
        }
        unset($courseStudentObject);
        return $isFacultyInCourseAsStudent;
    }

    /**
     * Formats the return data when creating course faculty
     *
     * @param \Synapse\AcademicBundle\EntityDto\CourseFacultyDTO $courseFacultyDTO
     * @param array $errorMessageArray
     * @param boolean $isInternal
     * @return array
     * @throws SynapseValidationException
     */
    private function buildCourseFacultyResponseArray($courseFacultyDTO, $errorMessageArray = [], $isInternal = true)
    {
        $responseArray = [];
        $courseArray['course_id'] = $courseFacultyDTO->getCourseId();
        $courseArray['faculty_id'] = $courseFacultyDTO->getFacultyId();
        $courseArray['permissionset_name'] = $courseFacultyDTO->getPermissionsetName();
        if (empty($errorMessageArray)) {
            $responseArray = $courseArray;
        } else {
            foreach ($errorMessageArray as $errorKey => $errorMessage) {
                if ($isInternal) {
                    throw new SynapseValidationException($errorMessage);
                } else {
                    $responseArray = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($courseArray, $errorMessageArray);
                }
            }
        }

        return $responseArray;
    }

    /**
     * Adds a student to a course
     *
     * @param AddUserCourseDto $addUserCourseDto
     * @param int $organizationId
     * @param int $loggedInUserId
     * @return AddUserCourseDto
     * @throws ValidationException
     */
    public function addStudentToCourse(AddUserCourseDto $addUserCourseDto, $organizationId, $loggedInUserId)
    {
        $userToCourseData = $this->getUserToCourseData($addUserCourseDto, $organizationId, $loggedInUserId, 'Student');
        // Added this to check Student in course.
        $this->courseFacultyStudentValidatorService->validateAdditionOfStudentToCourse($userToCourseData['personId'], $organizationId, $userToCourseData['courseId']);
        $orgCourseStudent = new OrgCourseStudent();
        $orgCourseStudent->setOrganization($userToCourseData['organization']);
        $orgCourseStudent->setCourse($userToCourseData['course']);
        $orgCourseStudent->setPerson($userToCourseData['person']);
        $this->orgCourseStudentRepository->persist($orgCourseStudent);
        $this->courseStudentUploadService->updateDataFile($organizationId);

        return $addUserCourseDto;
    }

    /**
     * Gets person, course, and organization data for user
     *
     * @param AddUserCourseDto $addUserCourseDto
     * @param int $organizationId
     * @param int $loggedInUserId
     * @param string $userType
     * @return array
     * @throws AccessDeniedException|InvalidArgumentException
     */
    private function getUserToCourseData(AddUserCourseDto $addUserCourseDto, $organizationId, $loggedInUserId, $userType)
    {
        $loggedInUserObject = $this->personService->findPerson($loggedInUserId);

        if (!$this->rbacManager->hasCoordinatorAccess($loggedInUserObject)) {
            throw new AccessDeniedException();
        }

        $courseId = $addUserCourseDto->getCourseId();
        $personId = $addUserCourseDto->getPersonId();

        $course = $this->orgCourseRepository->findOneBy([
            'id' => $courseId
        ]);
        $this->isObjectExist($course, CourseConstant::COURSE_NOT_FOUND, CourseConstant::COURSE_NOT_FOUND_KEY);
        $courseOrgId = $course->getOrganization()->getId();
        $this->rbacManager->checkAccessToOrganization($courseOrgId);
        $person = $this->personService->findPerson($personId);

        $personOrgId = $person->getOrganization()->getId();
        if ($personOrgId != $organizationId) {
            throw new InvalidArgumentException('Person does not belong to your organization', '400', 400);
        }

        $organization = $this->orgService->find($organizationId);

        return array('personId' => $personId, 'courseId' => $courseId, 'organization' => $organization, 'course' => $course, 'person' => $person);
    }

    /**
     * Get the list of student Ids specific to course
     *
     * @param int $courseId - Internal course Id
     * @param int $organizationId
     * @param string $externalCourseID
     * @return CourseStudentsDto
     */
    public function getStudentIdsInCourse($courseId, $organizationId, $externalCourseID)
    {
        $courseStudentsDto = [];
        $courseStudentIds = $this->orgCourseRepository->getAllStudentsInCourse($courseId, $organizationId);
        if (!empty($courseStudentIds)) {
            $courseStudentsDto = new CourseStudentsDto();
            $courseStudentsDto->setCourseId($externalCourseID);
            foreach ($courseStudentIds as $studentId) {
                $studentIds[] = $studentId['student_id'];
            }
            $courseStudentsDto->setStudents($studentIds);
        }
        return $courseStudentsDto;
    }

    /**
     * Adds the specified students to the specified courses
     *
     * @param int $organizationId
     * @param Person $loggedInUser
     * @param CourseStudentListDTO $courseStudentListDTO
     * @param boolean $isInternal
     * @return array
     */
    public function addStudentsToCourses($organizationId, $loggedInUser, $courseStudentListDTO, $isInternal = true)
    {
        $error = [];
        $createdRecords = [];
        $courseStudentList = $courseStudentListDTO->getCourseStudentList();
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($courseStudentList as $courseStudent) {
            $courseSectionId = $courseStudent->getCourseId();
            $studentExternalId = $courseStudent->getStudentId();
            try {

                if ($isInternal) {
                    $orgCourseRepositoryCriteria = [
                        'id' => $courseSectionId,
                        'organization' => $organizationId
                    ];
                    $personRepositoryCriteria = [
                        'id' => $studentExternalId,
                        'organization' => $organizationId
                    ];
                } else {
                    $orgCourseRepositoryCriteria = [
                        'courseSectionId' => $courseSectionId,
                        'organization' => $organizationId
                    ];
                    $personRepositoryCriteria = [
                        'externalId' => $studentExternalId,
                        'organization' => $organizationId
                    ];
                }

                // Validate course
                $dataProcessingExceptionHandler->addErrors("Course ID {$courseSectionId} is not valid at the organization.", "course_id");
                $convertedCourseObject = $this->orgCourseRepository->findOneBy($orgCourseRepositoryCriteria, $dataProcessingExceptionHandler);
                $dataProcessingExceptionHandler->resetAllErrors();

                $convertedCourseId = $convertedCourseObject->getId();

                // Validate the student's person ID.
                $dataProcessingExceptionHandler->addErrors("Person ID {$studentExternalId} is not valid at the organization.", "student_id");
                $convertedPersonObject = $this->personRepository->findOneBy($personRepositoryCriteria, $dataProcessingExceptionHandler);
                $dataProcessingExceptionHandler->resetAllErrors();

                $convertedPersonId = $convertedPersonObject->getId();

                // Validate that the person is a valid student
                $dataProcessingExceptionHandler->addErrors("Student {$studentExternalId} is not valid at this course.", "student_id");
                $this->orgPersonStudentRepository->findOneBy([
                    'person' => $convertedPersonId,
                    'organization' => $organizationId
                ], $dataProcessingExceptionHandler);
                $dataProcessingExceptionHandler->resetAllErrors();

                // Validate that the student is not already in the course as a faculty
                $isStudentInCourseAsFaculty = $this->orgCourseFacultyRepository->findOneBy([
                    'course' => $convertedCourseId,
                    'person' => $convertedPersonId
                ]);
                if($isStudentInCourseAsFaculty) {
                    $dataProcessingExceptionHandler->addErrors("Student ID {$studentExternalId} is already in the course as a faculty.", "student_id");
                    throw $dataProcessingExceptionHandler;
                }
                $dataProcessingExceptionHandler->resetAllErrors();

                $courseStudentObject = $this->setCourseStudentObject($convertedPersonObject, $convertedCourseObject, $loggedInUser);
                // Validate that the student is not already in the course
                $dataProcessingExceptionHandler->addErrors("Student ID {$studentExternalId} already exist at this course", "student_id");
                $this->entityValidationService->validateDoctrineEntity($courseStudentObject, $dataProcessingExceptionHandler, "required", true);
                $dataProcessingExceptionHandler->resetAllErrors();
                $this->orgCourseStudentRepository->persist($courseStudentObject);

                $createdRecords[] = $this->buildCourseResponseArray($courseStudent);

                unset($orgCourseStudent);

            } catch (DataProcessingExceptionHandler $dpeh) {
                $errorMessage = current($dpeh->getAllErrors());
                $errorMessageArray = $this->buildCourseResponseArray($courseStudent, $errorMessage, $isInternal);
                $error[] = $errorMessageArray;
            }

            // Reset all errors
            $dataProcessingExceptionHandler->resetAllErrors();
        }

        return [
            'data' => [
                'created_count' => count($createdRecords),
                'created_records' => $createdRecords,
            ],
            'errors' => $error
        ];

    }

    /**
     * Setting students in course
     *
     * @param Person $convertedPersonObject
     * @param OrgCourses $convertedCourseObject
     * @param Person $loggedInUser
     * @return OrgCourseStudent
     */
    private function setCourseStudentObject($convertedPersonObject, $convertedCourseObject, $loggedInUser)
    {
        $orgCourseStudent = new OrgCourseStudent();
        $orgCourseStudent->setPerson($convertedPersonObject);
        $orgCourseStudent->setCourse($convertedCourseObject);
        $orgCourseStudent->setOrganization($loggedInUser->getOrganization());
        $orgCourseStudent->setCreatedBy($loggedInUser);
        $orgCourseStudent->setModifiedBy($loggedInUser);

        return $orgCourseStudent;
    }

    /**
     * Formats the return data and errors when adding student to course
     *
     * @param CourseStudentsDto $courseStudentsDto
     * @param array $errorMessageArray
     * @param boolean $isInternal
     * @return array
     * @throws SynapseValidationException
     */
    private function buildCourseResponseArray($courseStudentsDto, $errorMessageArray = [], $isInternal = true)
    {
        $responseArray = [];
        $courseArray['course_id'] = $courseStudentsDto->getCourseId();
        $courseArray['student_id'] = $courseStudentsDto->getStudentId();

        if (empty($errorMessageArray)) {
            $responseArray = $courseArray;
        } else {
            foreach ($errorMessageArray as $errorKey => $errorMessage) {
                if ($isInternal) {
                    throw new SynapseValidationException($errorMessage);
                } else {
                    $responseArray = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($courseArray, $errorMessageArray);
                }
            }
        }

        return $responseArray;
    }

    /**
     * Checks to see if the student is already in the course as a faculty.
     *
     * @param int $studentId
     * @param int $courseId
     * @return boolean
     */
    public function isStudentInCourseAsFaculty($studentId, $courseId)
    {
        $courseFacultyObject = $this->orgCourseFacultyRepository->findOneBy([
            'course' => $courseId,
            'person' => $studentId
        ]);

        if ($courseFacultyObject) {
            return true;
        }

        return false;
    }

    /**
     * Checks to see if the student is already in the course
     *
     * @param int $studentId
     * @param int $courseId
     * @return boolean
     */
    public function isStudentInCourse($studentId, $courseId)
    {
        $courseStudentObject = $this->orgCourseStudentRepository->findOneBy([
            "course" => $courseId,
            "person" => $studentId
        ]);
        if ($courseStudentObject) {
            return true;
        }

        return false;
    }

    /**
     * Updates the specified courses.
     *
     * @param CourseListDTO $courseListDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @return array
     */
    public function updateCourses($courseListDTO, $organization, $loggedInUser)
    {
        $courseList = $courseListDTO->getCourseList();
        $successfullyUpdatedCourses = [];
        $errorCourses = [];

        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

        foreach ($courseList as $courseDTO) {
            try {
                // get year id
                $yearId = $courseDTO->getYearId();
                $dataProcessingExceptionHandler->addErrors("Year Id $yearId is not valid for this organization.", 'YearId', 'required');
                $organizationAcademicYear = $this->orgAcademicYearRepository->findOneBy(
                    [
                        'yearId' => $yearId,
                        'organization' => $organization
                    ],
                    $dataProcessingExceptionHandler
                );
                $dataProcessingExceptionHandler->resetAllErrors();

                // get term Id
                $termId = $courseDTO->getTermId();
                $dataProcessingExceptionHandler->addErrors("Term Id $termId is not valid for this organization.", 'TermId', 'required');
                $organizationAcademicTerm = $this->orgAcademicTermRepository->findOneBy(
                    [
                        'termCode' => $termId,
                        'orgAcademicYearId' => $organizationAcademicYear,
                        'organization' => $organization
                    ],
                    $dataProcessingExceptionHandler
                );
                $dataProcessingExceptionHandler->resetAllErrors();

                // validate course
                $courseSectionId = $courseDTO->getCourseSectionId();
                $dataProcessingExceptionHandler->addErrors("Course Section ID $courseSectionId is not valid for this organization.", 'courseSectionId', 'required');
                $courseObject = $this->orgCourseRepository->findOneBy(
                    [
                        'courseSectionId' => $courseSectionId,
                        'organization' => $organization
                    ],
                    $dataProcessingExceptionHandler
                );
                $dataProcessingExceptionHandler->resetAllErrors();

                $cloneCourseObject = clone($courseObject);
                $courseObject = $this->setCourseObject($courseObject, $courseDTO, $organization, $loggedInUser, $organizationAcademicYear, $organizationAcademicTerm);

                // Nullify fields of Course object
                $fieldsToClearArray = $courseDTO->getClearFields();
                if (count($fieldsToClearArray)) {
                    $courseObject = $this->entityValidationService->nullifyFieldsToBeCleared($courseObject, $fieldsToClearArray);
                }

                $validationGroup = [
                    'required' => true,
                    null => false
                ];
                // Validate course
                $dataProcessingExceptionHandler = $this->entityValidationService->validateAllDoctrineEntityValidationGroups($courseObject, $dataProcessingExceptionHandler, $validationGroup);


                $optionalErrors = $dataProcessingExceptionHandler->doesErrorHandlerContainError(null);
                if ($optionalErrors) {
                    // Restore the optional parameter
                    $courseObject = $this->entityValidationService->restoreErroredProperties($courseObject, $cloneCourseObject, $dataProcessingExceptionHandler);
                }

                $this->orgCourseRepository->persist($courseObject);
                $successfullyUpdatedCourses[] = $courseDTO;
                unset($courseObject);

                // Throw error object if error object having any optional error
                $this->entityValidationService->throwErrorIfContains($dataProcessingExceptionHandler);

            } catch (DataProcessingExceptionHandler $dpeh) {
                $errors = $dpeh->getAllErrors();

                //Build output error array
                $perCourseError = $this->buildCourseErrorArray($courseDTO, $errors);
                $errorCourses [] = $perCourseError;
            }
            $dataProcessingExceptionHandler->resetAllErrors();
        }

        //Build the response.
        $responseArray = [];
        $responseArray['updated_count'] = count($successfullyUpdatedCourses);
        $responseArray['updated_records'] = $successfullyUpdatedCourses;

        $responseErrorArray['error_count'] = count($errorCourses);
        $responseErrorArray['error_records'] = $errorCourses;

        $returnArray['data'] = $responseArray;
        $returnArray['errors'] = $responseErrorArray;
        return $returnArray;

    }

    /**
     * Gets the course list for the user based on the user's type as a JSON response.
     *
     * @param integer $personId
     * @param integer $organizationId
     * @param string $userType
     * @param string $year
     * @param string $term
     * @param string $college
     * @param string $department
     * @param string $filter
     * @param int|null $recordsPerPage
     * @param int|null $pageNumber
     * @param boolean $isInternal
     * @param boolean $formatResultSet
     * @param boolean $allCourses
     * @return array|string
     * @throws SynapseValidationException|AccessDeniedException
     */
    public function getCoursesByRoleAsJSON($personId, $organizationId, $userType, $year = '', $term = '', $college = '', $department = '', $filter = '', $recordsPerPage = null, $pageNumber = null, $isInternal = true, $formatResultSet = true, $allCourses = false)
    {
        if ($isInternal) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }

        if ($year == "current") {
            $year = $this->academicYearService->getCurrentOrganizationAcademicYearYearID($organizationId, true);
        }

        if ($userType == 'coordinator') {
            $this->organizationRoleRepository->findOneBy(['person' => $personId, 'organization' => $organizationId], new AccessDeniedException("You do not have access to the organization's courses as a coordinator"));
        }

        if (!is_numeric($pageNumber)) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }

        if (is_numeric($recordsPerPage)) {
            $offset = ($pageNumber * $recordsPerPage) - $recordsPerPage;
        } else {
            if ($formatResultSet && !$isInternal) {
                $offset = SynapseConstant::DEFAULT_RECORD_COUNT * (SynapseConstant::DEFAULT_PAGE_NUMBER - 1);
            } else {
                $offset = null;
            }
        }

        $currentDateTimeObject = new \DateTime();
        $currentDate = $currentDateTimeObject->format(SynapseConstant::DATE_YMD_FORMAT);

        $permissionToViewAllCourse = false;
        if ($allCourses && $userType == 'faculty') {
            $permissionToViewAllCourse = $this->rbacManager->hasAccess(['viewCourses']);
        }

        if (($userType == 'coordinator') || ($permissionToViewAllCourse)) {
            $courseList = $this->orgCourseRepository->getCoursesForOrganization($organizationId, $currentDate, $year, $term, $college, $department, $filter, $recordsPerPage, $offset, $formatResultSet, $isInternal, false);
            $totalOrganizationCourseCount = $this->orgCourseRepository->getCoursesForOrganization($organizationId, $currentDate, $year, $term, $college, $department, $filter, $recordsPerPage, $offset, $formatResultSet, $isInternal, true);
        } else {
            $courseList = $this->orgCourseRepository->getCoursesForFaculty($organizationId, $personId, $currentDate, $year, $term, $college, $department, $filter);
            $totalOrganizationCourseCount = $this->orgCourseRepository->getCoursesForFaculty($organizationId, $personId, $currentDate, $year, $term, $college, $department, $filter, true);
        }
        $filteredCourseId = array_column($courseList, 'org_course_id');

        if ($formatResultSet) {
            $courseFacultyArray = $this->orgCourseRepository->getCountOfFacultyInCourse($organizationId, $filteredCourseId);
            $courseIdFacultyArray = array_column($courseFacultyArray, 'faculty_count', 'course_id');

            $courseStudentArray = $this->orgCourseRepository->getCountOfStudentInCourse($organizationId, $filteredCourseId);
            $courseIdStudentArray = array_column($courseStudentArray, 'student_count', 'course_id');

            $resultSet = $this->formatCourseList($courseList, $courseIdFacultyArray, $courseIdStudentArray, $userType, $totalOrganizationCourseCount);
        } else {
            if (!$totalOrganizationCourseCount) {
                $recordsPerPage = 0;
                $pageNumber = 0;
            }

            // If there is no pagination data, the records per page would be the total number of records
            if (!is_numeric($recordsPerPage)) {
                $recordsPerPage = $totalOrganizationCourseCount;
            }

            $courseListDTO = new CourseSearchResultDTO();
            $courseListDTO->setTotalPages(ceil($totalOrganizationCourseCount / $recordsPerPage));
            $courseListDTO->setTotalRecords($totalOrganizationCourseCount);
            $courseListDTO->setRecordsPerPage($recordsPerPage);
            $courseListDTO->setCurrentPage($pageNumber);
            $courseListDTO->setCourseList($courseList);
            $resultSet = $courseListDTO;
        }

        return $resultSet;
    }

    /**
     * Generates the course list CSV export file.
     *
     * @param int $personId
     * @param int $organizationId
     * @param string $userType
     * @param string $year
     * @param string $term
     * @param string $college
     * @param string $department
     * @param string $filter
     * @param string $export - (courses|staff|students|everything)
     * @param boolean $allCourses
     * @throws SynapseValidationException|AccessDeniedException
     * @return string
     */
    public function getCoursesByRoleAsCSV($personId, $organizationId, $userType, $year = '', $term = '', $college = '', $department = '', $filter = '', $export = '', $allCourses = false)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        if ($year == "current") {
            $year = $this->academicYearService->getCurrentOrganizationAcademicYearYearID($organizationId, true);
        }

        if ($userType == 'coordinator') {
            $this->organizationRoleRepository->findOneBy(['person' => $personId, 'organization' => $organizationId], new AccessDeniedException("You do not have access to the organization's courses as a coordinator"));
        }

        $currentDateTimeObject = new \DateTime();
        $currentDate = $currentDateTimeObject->format(SynapseConstant::DEFAULT_DATE_FORMAT);

        $permissionToViewAllCourse = false;
        if ($allCourses && $userType == 'faculty') {
            $permissionToViewAllCourse = $this->rbacManager->hasAccess(['viewCourses']);
        }

        if ($userType == 'coordinator' || $permissionToViewAllCourse) {
            $courseList = $this->orgCourseRepository->getCoursesForOrganization($organizationId, $currentDate, $year, $term, $college, $department, $filter, null, null, true, true, false);
        } else {
            $courseList = $this->orgCourseRepository->getCoursesForFaculty($organizationId, $personId, $currentDate, $year, $term, $college, $department, $filter);
        }

        $filteredCourseId = array_column($courseList, 'org_course_id');

        $courseFacultyArray = $this->orgCourseRepository->getCountOfFacultyInCourse($organizationId, $filteredCourseId);
        $courseIdFacultyArray = array_column($courseFacultyArray, 'faculty_count', 'course_id');
        $courseStudentArray = $this->orgCourseRepository->getCountOfStudentInCourse($organizationId, $filteredCourseId);
        $courseIdStudentArray = array_column($courseStudentArray, 'student_count', 'course_id');
        $courseData = $this->prepareData($courseList, $courseIdFacultyArray, $courseIdStudentArray);
        $csvHeader = $this->prepareCSVHeader($courseData, $export);

        $fileName = $organizationId . '-' . $personId . $userType . $year . $term . $college . $department . $filter . 'csv' . $export . '-list-course.csv';

        $CSVFilename = $this->CSVUtilityService->generateCSV(SynapseConstant::S3_ROOT . SynapseConstant::S3_ROASTER_UPLOAD_DIRECTORY . "/", $fileName, $courseData, $csvHeader);

        return $CSVFilename;
    }

    /**
     * Preparing course csv header
     *
     * @param array $courseDetails
     * @param string $export
     * @return array
     */
    private function prepareCSVHeader($courseDetails, $export)
    {
        $csvHeader = [
            'year_id' => 'Year Id',
            'term_code' => 'Term Id',
            'term_name' => 'Term Name',
            'course_section_id' => 'Unique Course Section Id',
            'subject_code' => 'Subject Code',
            'course_number' => 'Course Number',
            'section_number' => 'Section Number',
            'course_name' => 'Course Name',
            'college_code' => 'College Code',
            'dept_code' => 'Dept Code'
        ];
        if (count($courseDetails) > 0) {

            if ($export !== 'students' && $export !== 'courses') {
                $csvHeader['count_of_faculty'] = 'Count Of Faculty';
            }

            if ($export !== 'staff' && $export !== 'courses') {
                $csvHeader['count_of_students'] = 'Count Of Students';
            }

        }
        return $csvHeader;
    }

    /**
     * Preparing course csv additional data
     *
     * @param array $courseDetails
     * @param array $facultyCount
     * @param array $studentCount
     * @return array
     */
    private function prepareData($courseDetails, $facultyCount, $studentCount)
    {
        foreach ($courseDetails as &$courseDetail) {
            //count of students
            if ($studentCount[$courseDetail['org_course_id']]) {
                $courseDetail['count_of_students'] = $studentCount[$courseDetail['org_course_id']];
            } else {
                $courseDetail['count_of_students'] = 0;
            }
            //count of faculty
            if ($facultyCount[$courseDetail['org_course_id']]) {
                $courseDetail['count_of_faculty'] = $facultyCount[$courseDetail['org_course_id']];
            } else {
                $courseDetail['count_of_faculty'] = 0;
            }
        }
        return $courseDetails;
    }

    /**
     * Gets the list of faculty within the specified course
     *
     * @param int $organizationId
     * @param OrgCourses $courseObject
     * @param boolean $isInternal
     * @return CourseFacultyDto
     */
    public function getFacultyByCourse($organizationId, $courseObject, $isInternal = true)
    {
        $courseId = $courseObject->getId();
        $facultyList = $this->orgCourseRepository->getSingleCourseFacultiesDetails($courseId, $organizationId, $isInternal);

        $courseFacultyDto = new CourseFacultyDto();
        $courseFacultyDto->setCourseExternalId($courseObject->getExternalId());
        $courseFacultyDto->setFacultyList($facultyList);

        return $courseFacultyDto;
    }
}
