<?php
namespace Synapse\AcademicUpdateBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\Entity\OrgCourseStudent;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\Entity\AcademicRecord;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\CourseAdhocAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\EntityDto\CourseDetailsResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesStudentsAdhocAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\EntityDto\FacultiesDetailsResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\FacultiesResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\GroupsResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\IndividualAcademicUpdateDTO;
use Synapse\AcademicUpdateBundle\EntityDto\StaffDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaticListDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentAcademicUpdatesDTO;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsDetailsResponseDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsResponseDto;
use Synapse\AcademicUpdateBundle\Job\CreateAcademicUpdateHistoryJob;
use Synapse\AcademicUpdateBundle\Repository\AcademicRecordRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\AcademicUpdateBundle\Util\AcademicUpdateHelper;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\LoggedInPersonService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\RoleService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\SearchBundle\EntityDto\GroupsDto;
use Synapse\UploadBundle\Job\UpdateAcademicUpdateDataFile;
use Synapse\UploadBundle\Service\Impl\OrgCalcFlagsRiskService;


/**
 * @DI\Service("academicupdate_service")
 */
class AcademicUpdateService extends AcademicUpdateServiceHelper
{

    const SERVICE_KEY = 'academicupdate_service';

    /**
     * count of the successful academic update
     *
     * @var int
     */
    private $successfulData;

    /**
     * The array of successful Academic updates
     *
     * @var array
     */
    private $successfulAcademicUpdateData;

    /**
     * an array of the successful academic updates with single student information
     *
     * @var array
     */
    private $successfulStudentData;

    /**
     * an array with the successful academic updates with students and a single course information
     *
     * @var array
     */
    private $successfulCourseData;

    /**
     * an array with the successful academic updates containing all course and student information
     *
     * @var array
     */
    private $successfulDataForJob;


    /**
     * a flat array with the errors that occurred while creating the adhoc academic update records, This is used to track the error count for service accounts for an organization
     *
     * @var array
     */
    private  $errorsForAdhocAcademicUpdates;

    // Scaffolding

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var Resque
     */
    private $resque;

    // Services

    /**
     * @var AcademicUpdateCreateService
     */
    private $academicUpdateCreateService;

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var APIValidationService
     */
    private $apiValidationService;

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
     * @var EmailService
     */
    protected $emailService;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    /**
     * @var IDConversionService
     */

    private $idConversionService;

    /**
     * @var JobService
     */
    private $jobService;

    /**
     * @var LoggedInPersonService
     */
    private $loggedInPersonService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var OrgCalcFlagsRiskService
     */
    private $orgCalcFlagsRiskService;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionsetService;

    /**
     * @var RoleService
     */
    private $roleService;

    // Repositories

    /**
     * @var AcademicRecordRepository
     */
    private $academicRecordRepository;

    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var AcademicUpdateRequestRepository
     */
    private $academicUpdateRequestRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValues;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    // Private variables

    /**
     * @var array
     */
    public $validationErrors = [];

    /**
     * AcademicUpdateService constructor.
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
        parent::__construct($repositoryResolver, $logger, $container);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->apiValidationService = $this->container->get(APIValidationService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->entityValidationService = $this->container->get(EntityValidationService::SERVICE_KEY);
        $this->idConversionService = $this->container->get(IDConversionService::SERVICE_KEY);
        $this->jobService = $this->container->get(JobService::SERVICE_KEY);
        $this->loggedInPersonService = $this->container->get(LoggedInPersonService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->orgCalcFlagsRiskService = $this->container->get(OrgCalcFlagsRiskService::SERVICE_KEY);
        $this->orgPermissionsetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->roleService = $this->container->get(RoleService::SERVICE_KEY);

        // Repositories
        $this->academicRecordRepository = $this->repositoryResolver->getRepository(AcademicRecordRepository::REPOSITORY_KEY);
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->academicUpdateRequestRepository = $this->repositoryResolver->getRepository(AcademicUpdateRequestRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Gets the list of all students in any course for any current academic term.
     * Used when creating an academic update request for "all students" (which really means all students currently enrolled in a course).
     *
     * @param int $organizationId
     * @param int|null $academicUpdateRequestId
     * @return StudentsResponseDto
     * @throws SynapseValidationException
     */
    public function getStudentsByOrganizationCourse($organizationId, $academicUpdateRequestId = null)
    {
        $academicUpdateRequestStudentIds = [];
        if ($academicUpdateRequestId) {
            $academicUpdateRequestStudentIds = $this->academicUpdateRepository->getStudentIdsForAcademicUpdate($organizationId, $academicUpdateRequestId);
        }
        // Get students in current course
        $students = $this->orgCourseStudentRepository->getStudentsInAnyCurrentCourse($organizationId, $academicUpdateRequestStudentIds);

        // Construct students response DTO
        $studentsResponseDto = new StudentsResponseDto();
        if (!empty($students)) {
            $studentsResponseDto->setTotalStudentsCount(count($students));
            $studentsResponseDto->setOrganizationId($organizationId);
            $studentsDetailsDtoArray = array();
            foreach ($students as $student) {
                $studentsDetailsResponseDto = new StudentsDetailsResponseDto();
                $studentsDetailsResponseDto->setStudentId($student['student_id']);
                if (isset($student['external_id'])) {
                    $studentsDetailsResponseDto->setStudentExternalId($student['external_id']);
                } else {
                    $studentsDetailsResponseDto->setStudentExternalId("");
                }

                if (isset($student['firstname'])) {
                    $studentsDetailsResponseDto->setStudentFirstname($student['firstname']);
                } else {
                    $studentsDetailsResponseDto->setStudentFirstname("");
                }

                if (isset($student['lastname'])) {
                    $studentsDetailsResponseDto->setStudentLastname($student['lastname']);
                } else {
                    $studentsDetailsResponseDto->setStudentLastname("");
                }

                if (isset($student['email'])) {
                    $studentsDetailsResponseDto->setStudentEmail($student['email']);
                } else {
                    $studentsDetailsResponseDto->setStudentEmail("");
                }

                if (strlen($student['status'])) {
                    $studentsDetailsResponseDto->setStudentStatus($student['status']);
                } else {
                    $studentsDetailsResponseDto->setStudentStatus("1");
                }

                $studentsDetailsDtoArray[] = $studentsDetailsResponseDto;
            }
            $studentsResponseDto->setStudentDetails($studentsDetailsDtoArray);
        }
        return $studentsResponseDto;
    }

    /**
     * Gets the list of all faculties in any course by the organization
     *
     * @param int $organizationId
     * @return FacultiesResponseDto $facultiesResponseDto
     */
    public function getFacultiesByOrganizationCourse($organizationId)
    {
        $currentDate = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId);
        $staffList = $this->orgPersonFacultyRepository->getFacultiesByOrganizationCourse($organizationId, $currentDate);
        $facultiesResponseDto = new FacultiesResponseDto();
        if (isset($staffList) && count($staffList) > 0) {
            $facultiesResponseDto->setTotalStaffCount(count($staffList));
            $facultiesResponseDto->setOrganizationId($organizationId);
            $staffDetailsDtoArray = array();
            foreach ($staffList as $staff) {

                $facultiesDetailsResponseDto = new FacultiesDetailsResponseDto();
                $facultiesDetailsResponseDto->setStaffId($staff['staff_id']);

                if (isset($staff['externalId'])) {
                    $facultiesDetailsResponseDto->setStaffExternalId($staff['externalId']);
                } else {
                    $facultiesDetailsResponseDto->setStaffExternalId("");
                }

                if (isset($staff['firstname'])) {
                    $facultiesDetailsResponseDto->setStaffFirstname($staff['firstname']);
                } else {
                    $facultiesDetailsResponseDto->setStaffFirstname("");
                }

                if (isset($staff['lastname'])) {
                    $facultiesDetailsResponseDto->setStaffLastname($staff['lastname']);
                } else {
                    $facultiesDetailsResponseDto->setStaffLastname("");
                }

                if (isset($staff['email'])) {
                    $facultiesDetailsResponseDto->setStaffEmail($staff['email']);
                } else {
                    $facultiesDetailsResponseDto->setStaffEmail("");
                }

                $staffDetailsDtoArray[] = $facultiesDetailsResponseDto;
            }
            $facultiesResponseDto->setStaffDetails($staffDetailsDtoArray);
        }
        return $facultiesResponseDto;
    }

    /**
     * get active courses by organization
     *
     * @param int $organizationId
     * @return CoursesResponseDto
     */
    public function getActiveCourseByOrganization($organizationId)
    {
        // current date for this organization
        $currentDate = new \DateTime();
        $currentDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $currentDate);

        // Connect repo to get faculty list
        $courseList = $this->orgCoursesRepository->getActiveCourseByOrganization($organizationId, $currentDate);

        // Construct response dto
        $coursesResponseDto = new CoursesResponseDto();
        if (isset($courseList) && count($courseList) > 0) {
            $coursesResponseDto->setTotalCourseCount(count($courseList));
            $coursesResponseDto->setOrganizationId($organizationId);
            $courseDetailsDtoArray = array();
            foreach ($courseList as $course) {
                $courseDetailsResponseDto = new CourseDetailsResponseDto();
                $courseDetailsResponseDto->setCourseId($course['id']);
                $courseDetailsResponseDto->setTermName($this->isNull($course['name'], ""));
                $courseDetailsResponseDto->setCourseName($this->isNull($course['courseName'], ""));
                $courseDetailsResponseDto->setCourseCode($this->isNull($course['subjectCode'], "") . $this->isNull($course['courseNumber'], ""));
                $courseDetailsResponseDto->setCourseSectionNo($this->isNull($course['sectionNumber'], ""));

                $courseDetailsDtoArray[] = $courseDetailsResponseDto;
            }
            $coursesResponseDto->setCourseDetails($courseDetailsDtoArray);
        }

        return $coursesResponseDto;
    }

    public function getGroupByOrganization($organizationId)
    {
        // Connect repo to get group list
        $groupList = $this->orgGroupRepository->getGroupByOrganization($organizationId);

        // Construct response dto
        $groupsResponseDto = new GroupsResponseDto();
        if (isset($groupList) && count($groupList) > 0) {
            $groupsResponseDto->setTotalGroupCount(count($groupList));
            $groupsResponseDto->setOrganizationId($organizationId);
            $groupDetailsDtoArray = array();
            foreach ($groupList as $group) {
                $groupsDto = new GroupsDto();
                $groupsDto->setGroupId($group['group_id']);
                $groupsDto->setGroupName($this->isNull($group['groupName'], ""));

                $groupDetailsDtoArray[] = $groupsDto;
            }
            $groupsResponseDto->setGroupDetails($groupDetailsDtoArray);
        }
        return $groupsResponseDto;
    }

    /**
     * Gets all the academic updates within the specified request for participating students.
     *
     * @param int $organizationId
     * @param int $academicUpdateRequestId
     * @param string $userType
     * @param string $filter
     * @param int $loggedInUserId
     * @param string $pageNumber
     * @param string $recordCount
     * @return array
     * @throws AccessDeniedException
     */
    public function getAcademicUpdateRequestByIdAsJSON($organizationId, $academicUpdateRequestId, $userType, $filter, $loggedInUserId, $pageNumber = '', $recordCount = '')
    {
        //Get the academic update request object.
        $academicUpdateRequestObject = $this->academicUpdateRequestRepository->findOneBy([
            'id' => $academicUpdateRequestId,
            'org' => $organizationId
        ], new SynapseValidationException('The requested academic update request does not exist.'));

        $currentDate = new DateTime();
        $currentDateAsString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        //Set the number of pages and records per page for pagination.
        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }

        $recordCount = (int)$recordCount;
        if (!$recordCount) {
            $recordCount = SynapseConstant::DEFAULT_RECORD_COUNT;
        }

        $startPoint = ($pageNumber * $recordCount) - $recordCount;
        $endPoint = $recordCount;

        //Get the current academic year for the organization.
        $currentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);

        if ($userType == 'faculty') {
            //If the user is a faculty member, get the count of participating students' academic updates that are tied to the faculty, and those academic updates.
            $academicUpdateCount = $this->academicUpdateRequestRepository->getAcademicUpdatesCountByRequestId($academicUpdateRequestId, $currentAcademicYearId, true, $loggedInUserId, $filter, false, true);
            $academicUpdateDetails = $this->academicUpdateRequestRepository->getAllAcademicUpdateRequestDetailsByIdForFaculty($academicUpdateRequestId, $loggedInUserId, $currentAcademicYearId, $currentDateAsString, $filter, $startPoint, $endPoint);
        } else {
            //Get the organization role object.
            $organizationRoleObject = $this->organizationRoleRepository->findOneBy(['organization' => $organizationId, 'person' => $loggedInUserId]);

            //If the organization role object does not exist, the user is not a coordinator. Throw an error.
            if (!$organizationRoleObject) {
                throw new AccessDeniedException("You do not have coordinator access.");
            }

            //Since the user has to be a coordinator to reach this point, get the count of academic updates for participating students in the request, and those academic updates.
            $academicUpdateCount = $this->academicUpdateRequestRepository->getAcademicUpdatesCountByRequestId($academicUpdateRequestId, $currentAcademicYearId, false, null, $filter, false, true);
            $academicUpdateDetails = $this->academicUpdateRequestRepository->getAllAcademicUpdateRequestDetailsById($academicUpdateRequestId, $currentAcademicYearId, $currentDateAsString, $filter, $startPoint, $endPoint);
        }

        //Create the array that will return the results.
        $response = [];
        $totalPageCount = ceil($academicUpdateCount / $recordCount);
        $response['total_records'] = (int)$academicUpdateCount;
        $response['total_pages'] = (int)$totalPageCount;
        $response['records_per_page'] = (int)$recordCount;
        $response['current_page'] = (int)$pageNumber;

        //Get the count of students that are non-participants with academic updates in this request, and fit the specified filter.
        $response['non_participant_count'] = $this->academicUpdateRequestRepository->getAcademicUpdatesCountByRequestId($academicUpdateRequestId, $currentAcademicYearId, false, null, $filter, true, false);

        //Formats the response data.
        $response['data'] = $this->formatIndividualAcademicUpdateRequestAsJSON($academicUpdateDetails, $academicUpdateRequestObject, $organizationId, $userType, $loggedInUserId);


        return $response;
    }

    /**
     * Gets the academic update request's contents as a CSV file.
     *
     * @param int $organizationId
     * @param int $academicUpdateRequestId
     * @param string $userType
     * @param string $filter
     * @param int $loggedInUserId
     * @return array
     */
    public function getAcademicUpdateRequestByIdAsCSV($organizationId, $academicUpdateRequestId, $userType, $filter, $loggedInUserId)
    {
        //Create the array that will return the results.
        $response = [];

        $currentDate = new DateTime();
        $currentDateAsString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        //Get the current academic year for the organization.
        $currentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);

        if ($userType == 'faculty') {
            //If the user is a faculty member, get the count of participating students' academic updates that are tied to the faculty, and those academic updates.
            $academicUpdateDetails = $this->academicUpdateRequestRepository->getAllAcademicUpdateRequestDetailsByIdForFaculty($academicUpdateRequestId, $loggedInUserId, $currentAcademicYearId, $currentDateAsString, $filter, null, null, 'csv');
        } else {
            //Get the organization role object.
            $organizationRoleObject = $this->organizationRoleRepository->findOneBy(['organization' => $organizationId, 'person' => $loggedInUserId]);

            //If the organization role object does not exist, the user is not a coordinator. Throw an error.
            if (!$organizationRoleObject) {
                throw new AccessDeniedException("You do not have coordinator access.");
            }

            //Since the user has to be a coordinator to reach this point, get the count of academic updates for participating students in the request, and those academic updates.
            $academicUpdateDetails = $this->academicUpdateRequestRepository->getAllAcademicUpdateRequestDetailsById($academicUpdateRequestId, $currentAcademicYearId, $currentDateAsString, $filter, null, null, 'csv');
        }

        //Returns the filename in the response
        $response['data'] =  $this->formatIndividualAcademicUpdateRequestAsCSV($academicUpdateDetails, $loggedInUserId, $organizationId);

        return $response;
    }

    /**
     * Creates an associative array containing the details of an academic update request using various input data passed
     * in as parameters
     *
     * @param array $academicUpdateDetails
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param int $organizationId
     * @param string $userType
     * @param int $loggedInUserId
     * @return string
     */
    public function formatIndividualAcademicUpdateRequestAsJSON($academicUpdateDetails, $academicUpdateRequest, $organizationId, $userType, $loggedInUserId)
    {
        $academicUpdateRequestDetails = [];
        $currentDate = new \DateTime();
        $organizationDetails = $this->organizationRepository->find($organizationId, new SynapseValidationException('Organization Not Found'));
        $isViewAbsent = $organizationDetails->getCanViewAbsences();
        $isViewInGrade = $organizationDetails->getCanViewInProgressGrade();
        $isViewComments = $organizationDetails->getCanViewComments();

        if (count($academicUpdateDetails) > 0) {
            /* If an academic update request detail exists, set that detail */
            if (array_key_exists('request_id', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_id'] = $academicUpdateDetails[0]['request_id'];
            } else {
                $academicUpdateRequestDetails['request_id'] = 0;
            }

            if (array_key_exists('request_name', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_name'] = $academicUpdateDetails[0]['request_name'];
            } else {
                $academicUpdateRequestDetails['request_name'] = null;
            }

            if (array_key_exists('request_description', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_description'] = $academicUpdateDetails[0]['request_description'];
            } else {
                $academicUpdateRequestDetails['request_description'] = null;
            }

            if (array_key_exists('request_status', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_status'] = $academicUpdateDetails[0]['request_status'];
            } else {
                $academicUpdateRequestDetails['request_status'] = null;
            }

            if (array_key_exists('request_due', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_due'] = $academicUpdateDetails[0]['request_due'];
            } else {
                $academicUpdateRequestDetails['request_due'] = null;
            }

            if (array_key_exists('request_created', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_created'] = $academicUpdateDetails[0]['request_created'];
            } else {
                $academicUpdateRequestDetails['request_created'] = null;
            }

            if (array_key_exists('request_from_firstname', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_from_firstname'] = $academicUpdateDetails[0]['request_from_firstname'];
            } else {
                $academicUpdateRequestDetails['request_from_firstname'] = null;
            }

            if (array_key_exists('request_from_lastname', $academicUpdateDetails[0])) {
                $academicUpdateRequestDetails['request_from_lastname'] = $academicUpdateDetails[0]['request_from_lastname'];
            } else {
                $academicUpdateRequestDetails['request_from_lastname'] = null;
            }

            if ($academicUpdateRequest) {
                $academicUpdateRequestDetails['request_attributes'] = $this->getAcademicUpdateRequestFilterCriteria($academicUpdateRequest);
                $createdDate = new \DateTime($academicUpdateDetails[0]['request_created']);

                $dueDate = new \DateTime($academicUpdateDetails[0]['request_due']);
                $dueDate->setTime(23,59,59);


                // Checking Due Date
                if ($currentDate > $dueDate) {
                    //TODO:: This code is masking lapsed academic updates as closed status, when they're actually still open status.
                    $academicUpdateRequestDetails['request_status'] = 'closed';
                } else {
                    $academicUpdateRequestDetails['request_status'] = $academicUpdateDetails[0]['request_status'];
                }

                $academicUpdateRequestDetails['request_created'] = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $createdDate, SynapseConstant::DATE_MDY_SLASHED_FORMAT);
                $academicUpdateRequestDetails['request_due'] = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $dueDate, SynapseConstant::DATE_MDY_SLASHED_FORMAT);

            } else {
                $academicUpdateRequestDetails['request_attributes'] = "";
                $academicUpdateRequestDetails['request_created'] = "";
                $academicUpdateRequestDetails['request_due'] = "";
            }

            if (is_null($isViewInGrade)) {
                $academicUpdateRequestDetails['can_view_in_progress_grade'] = false;
            } else {
                $academicUpdateRequestDetails['can_view_in_progress_grade'] = $isViewInGrade;
            }

            if (is_null($isViewAbsent)) {
                $academicUpdateRequestDetails['can_view_absences'] = false;
            } else {
                $academicUpdateRequestDetails['can_view_absences'] = $isViewAbsent;
            }

            if (is_null($isViewComments)) {
                $academicUpdateRequestDetails['can_view_comments'] = false;
            } else {
                $academicUpdateRequestDetails['can_view_comments'] = $isViewComments;
            }

            $academicUpdateRequestDetails['request_from'] = [
                'firstname' => $academicUpdateDetails[0]['request_from_firstname'],
                'lastname' => $academicUpdateDetails[0]['request_from_lastname']
            ];

            $academicUpdateRequestDetails['request_details'] = [];
            $courseIndex = 0;
            $currentCourseName = "";

            foreach ($academicUpdateDetails as $academicUpdateDetail) {
                $subjectCourse = $academicUpdateDetail['subject_code'] . $academicUpdateDetail['course_number'];


                if (is_null($subjectCourse)) {
                    $subjectCourse = "";
                }

                if (is_null($academicUpdateDetail['department_name'])) {
                    $departmentName = "";
                } else {
                    $departmentName = $academicUpdateDetail['department_name'];
                }

                if (is_null($academicUpdateDetail['academic_year_name'])) {
                    $academicYearName = "";
                } else {
                    $academicYearName = $academicUpdateDetail['academic_year_name'];
                }

                if (is_null($academicUpdateDetail['academic_term_name'])) {
                    $academicTermName = "";
                } else {
                    $academicTermName = $academicUpdateDetail['academic_term_name'];
                }

                if (is_null($academicUpdateDetail['course_section_name'])) {
                    $courseSectionName = "";
                } else {
                    $courseSectionName = $academicUpdateDetail['course_section_name'];
                }

                if (is_null($academicUpdateDetail['course_id'])) {
                    $courseId = "";
                } else {
                    $courseId = $academicUpdateDetail['course_id'];
                }

                if (empty($currentCourseName)) {
                    $academicUpdateRequestDetails['request_details'][$courseIndex] = [
                        'subject_course' => $subjectCourse,
                        'department_name' => $departmentName,
                        'academic_year_name' => $academicYearName,
                        'academic_term_name' => $academicTermName,
                        'course_section_name' => $courseSectionName,
                        'course_id' => $courseId
                    ];
                    $currentCourseName = $academicUpdateDetail['course_section_id'];
                } else {
                    if ($currentCourseName != $academicUpdateDetail['course_section_id']) {
                        $courseIndex++;
                        $currentCourseName = $academicUpdateDetail['course_section_id'];
                        $academicUpdateRequestDetails['request_details'][$courseIndex] = [
                            'subject_course' => $subjectCourse,
                            'department_name' => $departmentName,
                            'academic_year_name' => $academicYearName,
                            'academic_term_name' => $academicTermName,
                            'course_section_name' => $courseSectionName,
                            'course_id' => $courseId
                        ];
                    }
                }

                $requestArray = [
                    'academic_update_id' => 'int',
                    'student_id' => 'int',
                    'student_firstname' => 'string',
                    'student_lastname' => 'string',
                    'student_risk' => 'string',
                    'student_grade' => 'string',
                    'student_absences' => 'string',
                    'student_comments' => 'string',
                    'student_refer' => 'int',
                    'student_send' => 'bool',
                    'is_bypassed' => 'bool',
                    'student_status' => 'string',
                    'academic_update_status' => 'string'
                ];
                if ($userType != 'faculty' && $academicUpdateDetail['academic_update_status'] == 'saved') {
                    $academicUpdateDetail['student_risk'] = null;
                    $academicUpdateDetail['student_grade'] = null;
                    $academicUpdateDetail['student_absences'] = null;
                    $academicUpdateDetail['student_comments'] = null;
                    $academicUpdateDetail['student_refer'] = null;
                    $academicUpdateDetail['student_send'] = null;
                }

                $academicUpdateRequestDetails['request_details'][$courseIndex]['student_details'][] = $this->getAllStudentInfoForAU($academicUpdateDetail, $requestArray);
            }

            if ($userType == 'coordinator') {
                $requestCompletionStatistics = $this->academicUpdateRequestRepository->getAcademicUpdateRequestCompletionStatistics($academicUpdateRequest->getId());
            } else {
                $requestCompletionStatistics = $this->academicUpdateRequestRepository->getAcademicUpdateRequestCompletionStatistics($academicUpdateRequest->getId(), $loggedInUserId);
            }

            $academicUpdateRequestDetails['request_complete_status'] = $requestCompletionStatistics['completion_percentage'];
        }

        return $academicUpdateRequestDetails;
    }

    /**
     * from a 1D array, this function will return all requested fields with type casting
     *
     * @param array $detailsArray
     * @param array $requiredKeys
     * @return array
     */
    private function getAllStudentInfoForAU($detailsArray, $requiredKeys)
    {
        $returnArray = [];
        if (is_array($detailsArray) && count($detailsArray) > 0) {
            foreach ($requiredKeys as $key => $val) {
                $returnArray[$key] = empty($detailsArray[$key]) ? "" : $detailsArray[$key];
                if (empty($detailsArray[$key]) && $detailsArray[$key] != 0) {
                    $returnArray[$key] = "";
                } else {
                    $returnArray[$key] = $detailsArray[$key];
                }
                settype($returnArray[$key], $val);
            }
        }

        return $returnArray;
    }

    /**
     * Cancels an academic update request.
     *
     * @param int $organizationId
     * @param int $academicUpdateRequestId
     * @param int $loggedInUserId
     * @return int
     */
    public function cancelAcademicUpdateRequest($organizationId, $academicUpdateRequestId, $loggedInUserId)
    {
        $this->organizationRoleRepository->findOneBy(['organization' => $organizationId, 'person' => $loggedInUserId], new AccessDeniedException("You do not have coordinator level access."));

        $organizationObject = $this->organizationRepository->find($organizationId, new SynapseValidationException("Organization not found"));

        $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);
        $languageId = $organizationLang->getLang()->getId();

        // current date for this organization
        $currentDate = new \DateTime();
        $currentDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $currentDate);
        $currentDateString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        $facultyAcademicUpdateCancellationList = $this->academicUpdateRepository->getFacultyWithIncompleteAcademicUpdatesInRequest($organizationId, $academicUpdateRequestId, $currentDateString);

        $academicUpdateRequestObject = $this->academicUpdateRequestRepository->findOneBy(array(
            'id' => $academicUpdateRequestId,
            'org' => $organizationId
        ), new SynapseValidationException("Academic Update Request not found."));

        // Bulk status update in Academic Update.
        $this->academicUpdateRepository->updateAcademicUpdateStatus($organizationId, $academicUpdateRequestId, 'closed');
        $academicUpdateRequestObject->setStatus('closed');
        $this->academicUpdateRequestRepository->flush();

        // Send notification to faculty recipients who still had updates to submit
        if (count($facultyAcademicUpdateCancellationList) > 0) {

            $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey('Academic_Update_Cancel_to_Faculty', $languageId);

            $details['submissionPage'] = "";
            $details['action'] = 'cancel';
            $this->buildAcademicUpdateRequestReminderOrCancellationEmail($facultyAcademicUpdateCancellationList, $organizationId, $emailTemplate, 'Academic_Update_Cancel_to_Faculty', $details);
        }

        // Alert notification after cancelling academic update request
        $this->createAcademicUpdateRequestReminderOrCancellationNotifications($facultyAcademicUpdateCancellationList, $organizationObject, $academicUpdateRequestObject, $loggedInUserId, 'academic-updates-cancelled');
        return $academicUpdateRequestId;
    }

    /**
     * Sends Reminder to Faculty to fill AU.
     *
     * @param int $organizationId
     * @param int $requestId
     * @param int $loggedInUserId
     * @return int
     */
    public function sendReminderToFaculty($organizationId, $requestId, $loggedInUserId)
    {
        $this->organizationRoleRepository->findOneBy(['organization' => $organizationId, 'person' => $loggedInUserId], new AccessDeniedException("You do not have coordinator level access."));
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException("Organization not found"));
        $academicUpdateRequest = $this->academicUpdateRequestRepository->findOneBy(['id' => $requestId, 'org' => $organizationId], new SynapseValidationException("Academic Update Request not found"));

        $organizationLang = $this->organizationService->getOrganizationDetailsLang($organizationId);
        $languageId = $organizationLang->getLang()->getId();

        // current date for this organization
        $currentDate = new \DateTime();
        $currentDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $currentDate);
        $currentDateString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        $facultyWithIncompleteAcademicUpdatesInRequest = $this->academicUpdateRepository->getFacultyWithIncompleteAcademicUpdatesInRequest($organizationId, $requestId, $currentDateString);

        // Send notification to faculty recipients who still had updates to submit
        if (count($facultyWithIncompleteAcademicUpdatesInRequest) > 0) {

            $submissionPage = $this->ebiConfigService->generateCompleteUrl('Academic_Update_Reminder_to_Faculty', $organizationId);
            if ($submissionPage) {
                $details['submissionPage'] = $submissionPage . $requestId;
            } else {
                $details['submissionPage'] = "#";
            }

            $details['action'] = 'reminder';

            $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey('Academic_Update_Reminder_to_Faculty', $languageId);
            $this->buildAcademicUpdateRequestReminderOrCancellationEmail($facultyWithIncompleteAcademicUpdatesInRequest, $organizationId, $emailTemplate, 'Academic_Update_Reminder_to_Faculty', $details);
        }

        $type = 'academic-updates-reminder';
        $this->createAcademicUpdateRequestReminderOrCancellationNotifications($facultyWithIncompleteAcademicUpdatesInRequest, $organization, $academicUpdateRequest, $loggedInUserId, $type);
        return $requestId;
    }

    /**
     * Gets the academic update request filter attributes as an array.
     *
     * @param AcademicUpdateRequest$academicUpdateRequest
     * @return array
     */
    private function getAcademicUpdateRequestFilterCriteria($academicUpdateRequest)
    {
        $filterCriteria = [];
        $filterCriteria['students'] = $this->getRequestAttributesStudent($academicUpdateRequest);
        $filterCriteria['staff'] = $this->getRequestAttributesFaculties($academicUpdateRequest);
        $filterCriteria['groups'] = $this->getRequestAttributesGroups($academicUpdateRequest);
        $filterCriteria['courses'] = $this->getRequestAttributesCourses($academicUpdateRequest);
        $filterCriteria['profile'] = $this->getRequestAttributesProfile($academicUpdateRequest);
        $filterCriteria['static_list'] = $this->getRequestAttributesStaticList($academicUpdateRequest);
        return $filterCriteria;
    }

    /**
     * Gets the static list included as an attribute within an academic update request.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @return StaticListDto
     */
    public function getRequestAttributesStaticList($academicUpdateRequest)
    {
        $studentDto = new StaticListDto();
        $studentDto->setIsAll(false);
        $studentDto->setSelectedStaticIds("");
        $type = $academicUpdateRequest->getSelectStaticList();
        switch ($type) {
            case 'all':
                $studentDto->setIsAll(true);
                break;
            case 'none':
                $studentDto->setSelectedStaticIds("");
                break;
            default:
                $students = $this->academicUpdateRequestRepository->getSelectedStaticListByRequest($academicUpdateRequest);
                $studentDto->setSelectedStaticIds(AcademicUpdateHelper::checkEmpty($students));
                break;
        }

        $this->academicUpdateRequestRepository->getSelectedStudentsByRequest($academicUpdateRequest);
        return $studentDto;
    }

    /**
     * Gets profiles within an AcademicUpdateRequest.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @return array
     */
    private function getRequestAttributesProfile($academicUpdateRequest)
    {
        $profileArray = [];
        $ebiProfile = $this->academicUpdateRequestRepository->getSelectedProfileByRequest($academicUpdateRequest, 'ebi');
        $profileArray['selected_ebi_ids'] = AcademicUpdateHelper::checkEmpty($ebiProfile);
        $ispProfile = $this->academicUpdateRequestRepository->getSelectedProfileByRequest($academicUpdateRequest, 'org');
        $profileArray['selected_isp_ids'] = AcademicUpdateHelper::checkEmpty($ispProfile);
        return $profileArray;
    }

    /**
     * Gets students within an AcademicUpdateRequest.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @return StudentsDto
     */
    private function getRequestAttributesStudent($academicUpdateRequest)
    {
        $studentDto = new StudentsDto();
        $studentDto->setIsAll(false);
        $studentDto->setSelectedStudentIds("");
        $type = $academicUpdateRequest->getSelectStudent();
        switch ($type) {
            case 'all':
                $studentDto->setIsAll(true);
                break;
            case 'none':
                $studentDto->setSelectedStudentIds("");
                break;
            default:
                $students = $this->academicUpdateRequestRepository->getSelectedStudentsByRequest($academicUpdateRequest);
                $studentDto->setSelectedStudentIds(AcademicUpdateHelper::checkEmpty($students));
                break;
        }

        $this->academicUpdateRequestRepository->getSelectedStudentsByRequest($academicUpdateRequest);
        return $studentDto;
    }

    /**
     * Gets faculty within an AcademicUpdateRequest.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @return StaffDto
     */
    private function getRequestAttributesFaculties($academicUpdateRequest)
    {
        $studentDto = new StaffDto();
        $studentDto->setIsAll(false);
        $studentDto->setSelectedStaffIds("");
        $type = $academicUpdateRequest->getSelectFaculty();
        switch ($type) {
            case 'all':
                $studentDto->setIsAll(true);
                break;
            case 'none':
                $studentDto->setSelectedStaffIds("");
                break;
            default:
                $students = $this->academicUpdateRequestRepository->getSelectedFacultyByRequest($academicUpdateRequest);
                $studentDto->setSelectedStaffIds(AcademicUpdateHelper::checkEmpty($students));
                break;
        }

        $this->academicUpdateRequestRepository->getSelectedStudentsByRequest($academicUpdateRequest);
        return $studentDto;
    }

    /**
     * Gets courses within an AcademicUpdateRequest.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @return CoursesDto
     */
    private function getRequestAttributesCourses($academicUpdateRequest)
    {
        $studentDto = new CoursesDto();
        $studentDto->setIsAll(false);
        $studentDto->setSelectedCourseIds("");
        $type = $academicUpdateRequest->getSelectCourse();
        switch ($type) {
            case 'all':
                $studentDto->setIsAll(true);
                break;
            case 'none':
                $studentDto->setSelectedCourseIds("");
                break;
            default:
                $students = $this->academicUpdateRequestRepository->getSelectedCourseByRequest($academicUpdateRequest);
                $studentDto->setSelectedCourseIds(AcademicUpdateHelper::checkEmpty($students));
                break;
        }

        $this->academicUpdateRequestRepository->getSelectedStudentsByRequest($academicUpdateRequest);
        return $studentDto;
    }

    /**
     * Gets groups within an AcademicUpdateRequest.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @return \Synapse\AcademicUpdateBundle\EntityDto\GroupsDto
     */
    private function getRequestAttributesGroups($academicUpdateRequest)
    {
        $studentDto = new \Synapse\AcademicUpdateBundle\EntityDto\GroupsDto();
        $studentDto->setIsAll(false);
        $studentDto->setSelectedGroupIds("");
        $type = $academicUpdateRequest->getSelectGroup();
        switch ($type) {
            case 'all':
                $studentDto->setIsAll(true);
                break;
            case 'none':
                $studentDto->setSelectedGroupIds("");
                break;
            default:
                $students = $this->academicUpdateRequestRepository->getSelectedGroupByRequest($academicUpdateRequest);
                $studentDto->setSelectedGroupIds(AcademicUpdateHelper::checkEmpty($students));
                break;
        }

        $this->academicUpdateRequestRepository->getSelectedStudentsByRequest($academicUpdateRequest);
        return $studentDto;
    }

    /**
     * Gets a list of academic update requests by organization and user.
     *
     * @param string $userType
     * @param int $organizationId
     * @param string $request
     * @param string $filter
     * @param string $viewMode
     * @param int $loggedInUserId
     * @return AcademicUpdateResponseDto $response
     */
    public function getAcademicUpdateRequestList($userType, $organizationId, $request, $filter, $viewMode, $loggedInUserId)
    {
        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException('Organization Not Found');
        }
        $filters['request'] = $request;
        $filters['filter'] = $filter;
        $filters['viewmode'] = $viewMode;
        $currentDateTime = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, SynapseConstant::DEFAULT_DATETIME_FORMAT);
        if ($userType == "coordinator") {
            $isCoordinator = $this->organizationRoleRepository->findOneBy(['organization' => $organizationId, 'person' => $loggedInUserId]);
            if (!$isCoordinator) {
                throw new SynapseValidationException('You do not have coordinator access');
            }
        }
        $response = $this->getAcademicUpdateRequests($userType, $organizationId, $loggedInUserId, $currentDateTime, $filters, $viewMode);
        return $response;
    }

    /**
     * Get AcademicUpdateResponseDto
     *
     * @param string $userType
     * @param int $orgId
     * @param int $loggedInUserId
     * @param DateTime $currentDate
     * @param array $filters
     * @param string $viewmode
     * @return AcademicUpdateResponseDto | string
     */
    protected function getAcademicUpdateRequests($userType, $orgId, $loggedInUserId, $currentDate, $filters, $viewmode = 'json')
    {
        $academicUpdateFaculties = $this->academicUpdateRepository->getAUFacultyAssigned($userType, $orgId, $loggedInUserId);
        $staffList = $this->mapRequestStaffValue($academicUpdateFaculties);
        $academicUpdateList = $this->academicUpdateRepository->getAcademicUpdateRequestsByUser($userType, $orgId, $currentDate, $loggedInUserId, $filters);
        $academicUpdateResponseDto = new AcademicUpdateResponseDto();

        if (count($academicUpdateList)) {
            if ($viewmode == 'json') {
                $academicUpdateResponse = $this->formatAcademicUpdateRequestList($academicUpdateList, $loggedInUserId, $orgId, $staffList, $userType);
                $academicUpdateOpenDetails = $academicUpdateResponse['open'];
                $academicUpdateCloseDetails = $academicUpdateResponse['closed'];

                if ($filters[AcademicUpdateConstant::KEY_REQUEST] == "myopen" || $filters[AcademicUpdateConstant::KEY_REQUEST] == "all") {
                    $academicUpdateResponseDto->setAcademicUpdatesOpen($academicUpdateOpenDetails);
                } else {
                    $academicUpdateOpenDetails = array();
                    $academicUpdateResponseDto->setAcademicUpdatesOpen($academicUpdateOpenDetails);
                }
                if ($filters[AcademicUpdateConstant::KEY_REQUEST] == "myclosed" || $filters[AcademicUpdateConstant::KEY_REQUEST] == "all") {
                    $academicUpdateResponseDto->setAcademicUpdatesClosed($academicUpdateCloseDetails);
                } else {
                    $academicUpdateCloseDetails = array();
                    $academicUpdateResponseDto->setAcademicUpdatesClosed($academicUpdateCloseDetails);
                }
            }
        }

        if ($viewmode == 'csv') {
            $this->getAcademicUpdateDetailsCSV($academicUpdateList, $loggedInUserId, $currentDate, $orgId);
            return "roaster_uploads/{$orgId}-{$loggedInUserId}-{$currentDate}-view-academic-updates-roaster.csv";
        }

        return $academicUpdateResponseDto;
    }

    /**
     * Creates notifications to faculty for either cancelling or reminding them about an academic update request they need to fulfill.
     *
     * @param array $reminderListArray
     * @param Organization $organization
     * @param int $requestId
     * @param int $loggedInUserId
     * @param string $type
     * @return boolean
     */
    public function createAcademicUpdateRequestReminderOrCancellationNotifications($reminderListArray, $organization, $requestId, $loggedInUserId, $type)
    {
        $academicUpdateRequest = $this->academicUpdateRepository->findOneBy(array(
            'academicUpdateRequest' => $requestId,
            'org' => $organization
        ), new SynapseValidationException('Academic Update Not Found'));

        $coordinator = $this->personRepository->find($loggedInUserId, new SynapseValidationException("Coordinator Not Found"));
        $coordinatorName = $coordinator->getLastname() . ', ' . $coordinator->getFirstname();
        foreach ($reminderListArray as $reminderList) {
            $personIdFacultyAssigned = $this->personRepository->find($reminderList['person_id_faculty_assigned']);
            if ($type == 'academic-updates-reminder') {
                $this->alertNotificationsService->createNotification('academic-updates-reminder', 'Academic update reminder from ' . $coordinatorName, $personIdFacultyAssigned, null, null, null, null, $academicUpdateRequest, null, $organization);
            } elseif ($type == 'academic-updates-cancelled') {
                $this->alertNotificationsService->createNotification('academic-updates-cancelled', 'Academic update request closed by ' . $coordinatorName, $personIdFacultyAssigned, null, null, null, null, $academicUpdateRequest, null, $organization);
            }
        }
        return true;
    }

    /**
     * Formats the academic update request list, including grouping open and closed academic update requests.
     *
     * @param array $academicUpdateRequests
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param array $staffList
     * @param string $userType -- "faculty" or "coordinator"
     * @return array
     */
    private function formatAcademicUpdateRequestList($academicUpdateRequests, $loggedInUserId, $organizationId, $staffList, $userType)
    {
        $openAcademicUpdates = [];
        $closedAcademicUpdates = [];

        foreach ($academicUpdateRequests as $academicUpdateRequest) {
            $academicUpdateDetailsResponseDto = new AcademicUpdateDetailsResponseDto();

            $academicUpdateDetailsResponseDto->setRequestId($academicUpdateRequest['requestId']);
            $academicUpdateDetailsResponseDto->setRequestName($academicUpdateRequest['name']);
            $academicUpdateDetailsResponseDto->setRequestDescription($academicUpdateRequest['description']);

            $requestCreated = new \DateTime($academicUpdateRequest['requestCreated']);
            $formattedOrganizationAdjustedRequestCreatedDatetime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $requestCreated, 'm/d/Y');
            $academicUpdateDetailsResponseDto->setRequestCreated($formattedOrganizationAdjustedRequestCreatedDatetime);

            $requestDue = new \DateTime($academicUpdateRequest['requestDue']);
            $formattedOrganizationAdjustedRequestDueDatetime = $this->dateUtilityService->getFormattedDateTimeForOrganization($organizationId, $requestDue, 'm/d/Y');
            $academicUpdateDetailsResponseDto->setRequestDue($formattedOrganizationAdjustedRequestDueDatetime);

            $academicUpdateDetailsResponseDto->setUpdateCompleted($academicUpdateRequest['completedTotal']);
            $academicUpdateDetailsResponseDto->setUpdateTotal($academicUpdateRequest['totalUpdates']);
            $academicUpdateDetailsResponseDto->setStatus($academicUpdateRequest['status']);

            $requestFrom = [];
            $requestFrom['firstname'] = $this->checkCondition($academicUpdateRequest['requesterId'], $loggedInUserId, "me", $academicUpdateRequest['requesterFirst']);
            $requestFrom['lastname'] = $this->checkCondition($academicUpdateRequest['requesterId'], $loggedInUserId, "", $academicUpdateRequest['requesterLast']);
            $academicUpdateDetailsResponseDto->setRequestFrom($requestFrom);

            $facultyDetails = $this->bindStaffDetails($staffList, $academicUpdateRequest, $userType);
            $academicUpdateDetailsResponseDto->setStaff($facultyDetails);

            // An academic update request should be marked as closed in a faculty's view if any of the following is true:
            // 1. The entire academic update request is marked as closed in the database (whether by being fulfilled, or the deadline passing, or the coordinator cancelling it).
            // 2. The deadline has passed.
            // 3. The faculty member has submitted academic updates for all of his/her students included in the request.
            if ($academicUpdateRequest['status'] == 'closed' || $academicUpdateRequest['pastDueDate'] || $academicUpdateRequest['totalUpdates'] == $academicUpdateRequest['completedTotal']) {
                $academicUpdateDetailsResponseDto->setStatus('closed');
                $closedAcademicUpdates[] = $academicUpdateDetailsResponseDto;
            } else {
                $openAcademicUpdates[] = $academicUpdateDetailsResponseDto;
            }
        }

        $academicUpdateRequestList = [];
        $academicUpdateRequestList['open'] = $openAcademicUpdates;
        $academicUpdateRequestList['closed'] = $closedAcademicUpdates;

        return $academicUpdateRequestList;
    }

    /**
     * Get latest academic updates for a specific course and students
     *
     * @param int $courseId
     * @param int $organizationId
     * @param array $studentIds
     * @return array|CourseAdhocAcademicUpdateDTO
     */
    public function getLatestCourseAcademicUpdates($courseId, $organizationId, $studentIds)
    {
        $latestCourseAcademicUpdates = $this->academicUpdateRepository->getLatestAcademicUpdatesForCourse($courseId, $organizationId, $studentIds);
        $courseAdhocAcademicUpdateDTO = [];
        $studentAcademicUpdatesDTOs = [];

        if (!empty($latestCourseAcademicUpdates)) {
            $courseAdhocAcademicUpdateDTO = new CourseAdhocAcademicUpdateDTO();
            // Assign academic updates in to DTO.
            // $latestCourseAcademicUpdates will have single academic update for each students.
            foreach ($latestCourseAcademicUpdates as $latestCourseAcademicUpdate) {
                $courseAdhocAcademicUpdateDTO->setCourseId($latestCourseAcademicUpdate['course_id']);
                $studentAcademicUpdatesDTO = new StudentAcademicUpdatesDTO();
                $studentAcademicUpdatesDTO->setStudentId($latestCourseAcademicUpdate['student_id']);
                $individualAcademicUpdateDTOs = $this->createIndividualAcademicUpdatesDTO($latestCourseAcademicUpdate);
                $studentAcademicUpdatesDTO->setAcademicUpdates($individualAcademicUpdateDTOs);
                $studentAcademicUpdatesDTOs[] = $studentAcademicUpdatesDTO;
            }
            $courseAdhocAcademicUpdateDTO->setStudentsWithAcademicUpdates($studentAcademicUpdatesDTOs);
        }
        return $courseAdhocAcademicUpdateDTO;
    }

    /**
     * Creates the IndividualAcademicUpdateDTO array for all academic updates specific to a student.
     *
     * @param array $individualAcademicUpdate
     * @return IndividualAcademicUpdateDTO[]
     */
    private function createIndividualAcademicUpdatesDTO($individualAcademicUpdate)
    {
        $individualAcademicUpdateDTO = new IndividualAcademicUpdateDTO();
        $individualAcademicUpdateDTO->setFacultyIdSubmitted($individualAcademicUpdate['faculty_id']);
        $submittedDate = new \DateTime($individualAcademicUpdate['date_submitted']);
        $individualAcademicUpdateDTO->setDateSubmitted($submittedDate);
        $individualAcademicUpdateDTO->setFailureRiskLevel($individualAcademicUpdate['failure_risk_level']);
        $individualAcademicUpdateDTO->setInProgressGrade($individualAcademicUpdate['in_progress_grade']);
        $individualAcademicUpdateDTO->setAbsences($individualAcademicUpdate['absences']);
        $individualAcademicUpdateDTO->setComment($individualAcademicUpdate['comment']);
        $individualAcademicUpdateDTO->setReferForAssistance($individualAcademicUpdate['refer_for_assistance']);
        $individualAcademicUpdateDTO->setSendToStudent($individualAcademicUpdate['send_to_student']);
        $individualAcademicUpdateDTOArray[] = $individualAcademicUpdateDTO;
        return $individualAcademicUpdateDTOArray;
    }


    /**
     * This is basic createAcademicRecord function the function will take CoursesStudentsAdhocAcademicUpdateDTO
     * and create multiple academic records. In an attempt to prepare it for to handle flat file changes,
     * The actual creation of academic updates will take a single academic update record and throw errors. Hence the
     * try catch.
     *
     * @param CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO
     * @param Person $loggedInUser
     * @param boolean $isAdhoc
     * @param string $updateType
     * @return array
     */
    public function createAcademicRecord($coursesStudentsAdhocAcademicUpdateDTO, $loggedInUser, $isAdhoc, $updateType)
    {
        $this->successfulData = 0;
        $errors = [];
        $dataProcessingExceptionHandler = null;

        $organizationId =  $loggedInUser->getOrganization()->getId();

        // this will actually loop through each section of the academic record
        // and then send back the academic record errors. These function also set
        // each class level variable successfulData, successfulDataForJob,
        // successfulAcademicUpdateData and successfulStudentData and resetting them each time
        try {
            $this->processCoursesStudentsAdhocAcademicUpdateDTO($coursesStudentsAdhocAcademicUpdateDTO, $loggedInUser);
        } catch (DataProcessingExceptionHandler $dpeh) {
            $errors = $dpeh->getPlainErrors();
            $dataProcessingExceptionHandler = $dpeh;
        }


        // set preparing the records to return
        $this->successfulDataForJob['records'] = $this->successfulCourseData;
        $responseArray = [];
        $responseArray['created_count'] = $this->successfulData;
        $responseArray['created_records'] = $this->successfulDataForJob;
        $responseArray['skipped_count'] = 0;
        $responseArray['skipped_records'] = [];
        $returnArray['data'] = $responseArray;
        $returnArray['errors'] = $errors;


        // if its a service account and there are errors in the request , update the organization error count
        if (is_null($loggedInUser->getExternalId()) && $dataProcessingExceptionHandler) {
            array_walk_recursive($errors, function ($errorValue, $errorKey) {
                $this->errorsForAdhocAcademicUpdates[] = $errorValue;
            });
            $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $this->errorsForAdhocAcademicUpdates);

        }

        // flushing all of the data before launching the job
        $this->academicRecordRepository->flush();

        if ($this->successfulData > 0) {
            $createAcademicUpdateHistoryJob = $this->createAcademicUpdateHistoryJob($loggedInUser, $isAdhoc, false, $updateType);
            $this->jobService->addJobToQueue($loggedInUser->getOrganization()->getId(), 'CreateAcademicUpdateHistoryJob', $createAcademicUpdateHistoryJob, $loggedInUser->getId());
        }

        if (!is_null($loggedInUser->getExternalId()) && $dataProcessingExceptionHandler) {
            $userMessage = $this->buildFailedAdhocAcademicUpdateUserMessage();
            $dataProcessingExceptionHandler->setMessage(json_encode($dataProcessingExceptionHandler->getAllErrors()));
            $dataProcessingExceptionHandler->setUserMessage($userMessage);
            $dataProcessingExceptionHandler->setCode("adhoc_academic_update_error");
            throw $dataProcessingExceptionHandler;
        }

        return $returnArray;
    }

    /**
     * Builds the user message for when academic updates encounter errors.
     *
     * @return string
     */
    private function buildFailedAdhocAcademicUpdateUserMessage()
    {
        if ($this->successfulData == 0) {
            $userMessage = "0 updates were successful. Please retry the request.";
        } else {
            $countUpdates = $this->successfulData;
            $userMessage = $countUpdates . " update(s) succeeded, but there were errors. Please retry the request. If the problem persists, please contact Mapworks Client Services.";
        }

        return $userMessage;
    }

    /**
     * This is the first layer of the academic Update JSON. The goal here is to loop through
     * each course and then call the student function. The student function will call the academic
     * update function. Each layer will pass in the constant (courses here, student in processStudentAcademicUpdatesDTOArray)
     * The lowest layer will throw all errors and pass it up to this foreach loop. After all errors
     * are gathered then we will get the academic updates that did not have errors and send those
     * off to the new academic update job. Then we will take the errors and prep them for each
     * place this can be called (V1 and V2).
     *
     * @param CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAdhocAcademicUpdateDTO
     * @param $loggedInUser
     * @throws DataProcessingExceptionHandler
     */
    private function processCoursesStudentsAdhocAcademicUpdateDTO($coursesStudentsAdhocAcademicUpdateDTO, $loggedInUser)
    {
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        $coursesWithAcademicUpdates = $coursesStudentsAdhocAcademicUpdateDTO->getCoursesWithAcademicUpdates();

        foreach ($coursesWithAcademicUpdates as $coursesWithAcademicUpdate) {
            $courseId = $coursesWithAcademicUpdate->getCourseId();
            try {
                $courseObject = $this->checkingForCourseBasedOnCriteria($courseId, $loggedInUser);
                $this->processStudentAcademicUpdatesDTOArray($coursesWithAcademicUpdate->getStudentsWithAcademicUpdates(), $courseObject, $loggedInUser);
            } catch (DataProcessingExceptionHandler $deph) {

                $dataErrorArray = $deph->getPlainErrors();
                $dataProcessingExceptionHandler->addErrors($dataErrorArray, $courseId, 'required');
            }

            // this will take the successful student data from the
            // student layer and gets it ready to be returned
            if (!empty($this->successfulStudentData)) {
                $studentAcademicUpdateArray['course_id'] = $courseId;
                $studentAcademicUpdateArray['students_with_academic_updates'] = $this->successfulStudentData;
                $this->successfulCourseData[] = $studentAcademicUpdateArray;
                $this->successfulStudentData = [];
            }
        }

        // Throw any errors if thrown from the student layer.
        if ($dataProcessingExceptionHandler->doesErrorHandlerContainError('required')) {
            throw $dataProcessingExceptionHandler;
        }
    }


    /**
     * This is the student layer of the academic JSON.
     * This follows the same pattern and courses and individual academic update
     *
     * @param StudentAcademicUpdatesDTO[] $studentsWithAcademicUpdates
     * @param OrgCourses $courseObject
     * @param Person $loggedInUser
     * @throws DataProcessingExceptionHandler
     */
    private function processStudentAcademicUpdatesDTOArray($studentsWithAcademicUpdates, $courseObject, $loggedInUser)
    {
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($studentsWithAcademicUpdates as $studentsWithAcademicUpdate) {

            $requestedStudentId = $studentsWithAcademicUpdate->getStudentId();
            try {

                $studentObject = $this->checkingForStudentBasedOnCriteria($requestedStudentId, $loggedInUser);
                // getting the student object to check if the student is part of the course
                $this->getStudentCourseFromDatabase($studentObject, $courseObject);

                $academicUpdates = $studentsWithAcademicUpdate->getAcademicUpdates();
                $academicUpdates = $this->sortIndividualAcademicUpdateDTOByDataSubmitted($academicUpdates);
                $this->proccessIndividualAcademicUpdateDTOArray($academicUpdates, $studentObject, $courseObject, $loggedInUser);

            } catch (DataProcessingExceptionHandler $deph) {
                $dataErrorArray = $deph->getPlainErrors();
                $dataProcessingExceptionHandler->addErrors($dataErrorArray, $requestedStudentId, 'required');
            }

            if (!empty($this->successfulAcademicUpdateData)) {
                $successfulStudentAcademicUpdates['student_id'] = $requestedStudentId;
                $successfulStudentAcademicUpdates['academic_updates'] = $this->successfulAcademicUpdateData;
                $this->successfulStudentData[] = $successfulStudentAcademicUpdates;
                $this->successfulAcademicUpdateData = [];
            }
        }

        if ($dataProcessingExceptionHandler->doesErrorHandlerContainError('required')) {
            throw $dataProcessingExceptionHandler;
        }
    }

    /**
     * The final layer of the JSON for Academic update each individual academic update.
     * This follows the same pattern as student and courses.
     *
     * @param IndividualAcademicUpdateDTO[] $academicUpdates
     * @param Person $studentObject
     * @param OrgCourses $courseObject
     * @param Person $loggedInUser
     * @throws array|DataProcessingExceptionHandler|SynapseException
     */
    private function proccessIndividualAcademicUpdateDTOArray($academicUpdates, $studentObject, $courseObject, $loggedInUser)
    {
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        $currentDate = new \DateTime();

        // Unfortunately we do not have a good way to track the academic records they are submitting
        // So to track them, we give the academic record an arbitrary id
        // (the sorted order of date submitted and the order it is handed to us)
        $arbitraryIdForAcademicUpdateBasedOffOfDateSubmitted = 1;
        foreach ($academicUpdates as $academicUpdate) {

            // if date submitted is never set in the json/ we default it to the current date
            if (!$academicUpdate->getDateSubmitted()) {
                $academicUpdate->setDateSubmitted($currentDate);
            }
            try {

                // create the academic record, validate it and persist/flush it
                $academicRecord = $this->createAdhocAcademicRecordObject($academicUpdate, $studentObject, $courseObject, $loggedInUser);
                $academicRecord = $this->validateAcademicRecordObject($academicRecord);
                $this->academicRecordRepository->persist($academicRecord, false);
                $this->academicRecordRepository->flush();

                // prepare this for the AcademicUpdate Need to use class variable
                $this->successfulAcademicUpdateData[] = $academicUpdate->__toArray();
                $this->successfulData++;

                // catch any errors the createAdhocAcademicRecordObject/validateAcademicRecordObject
                // would throw
            } catch (DataProcessingExceptionHandler $deph) {
                $this->academicRecordRepository->clear();
                $dataErrorArray = $deph->getPlainErrors();
                $dataProcessingExceptionHandler->addErrors($dataErrorArray, $arbitraryIdForAcademicUpdateBasedOffOfDateSubmitted, 'required');
            }
            $arbitraryIdForAcademicUpdateBasedOffOfDateSubmitted++;
        }

        if ($dataProcessingExceptionHandler->doesErrorHandlerContainError('required')) {
            throw $dataProcessingExceptionHandler;
        }
    }

    /**
     * creates a single academic record object, will run permission checks
     *
     * @param IndividualAcademicUpdateDTO $academicUpdate
     * @param Person $studentObject
     * @param OrgCourses $courseObject
     * @param Person $loggedInUser
     * @return null|object|AcademicRecord
     */
    public function createAdhocAcademicRecordObject($academicUpdate, $studentObject, $courseObject, $loggedInUser)
    {
        $facultyId = $academicUpdate->getFacultyIdSubmitted();
        $organizationObject = $loggedInUser->getOrganization();
        $organizationId = $organizationObject->getId();
        // checks the faculty's permissions
        $this->checkingForFacultyBasedOnCriteria($facultyId, $loggedInUser);
        $this->loggedInUserChecks($loggedInUser, $studentObject, $courseObject, $organizationId);

        $academicRecordObject = $this->getAcademicRecordObject($courseObject, $studentObject, $organizationObject);
        $academicRecordObject = $this->buildAcademicRecordObject($organizationId, $academicUpdate, $academicRecordObject);

        return $academicRecordObject;
    }

    /**
     * transforms a array representation of CoursesStudentsAdhocAcademicUpdateDTO
     * into the CoursesStudentsAdhocAcademicUpdateDTO object
     *
     * @param array $createdArrayWithInternalIds
     * @return CoursesStudentsAdhocAcademicUpdateDTO
     */
    public function prepareCoursesStudentAdhocAcademicUpdatesDTOForAcademicUpdateJob($createdArrayWithInternalIds)
    {
        $courseAdhocAcademicUpdateDtoForJobArray = [];

        foreach ($createdArrayWithInternalIds['records'] as $createdRecord) {
            $courseAdhocAcademicUpdateDtoForJob = new CourseAdhocAcademicUpdateDTO();
            $courseAdhocAcademicUpdateDtoForJob->setCourseId($createdRecord['course_id']);
            $studentWithAcademicRecordsArray = [];
            foreach ($createdRecord['students_with_academic_updates'] as $studentWithAcademicRecords) {

                $studentWithAcademicUpdateDtoForJob = new StudentAcademicUpdatesDTO();
                $studentWithAcademicUpdateDtoForJob->setStudentId($studentWithAcademicRecords['student_id']);
                $academicUpdateArray = [];
                foreach ($studentWithAcademicRecords['academic_updates'] as $academicUpdate) {

                    $individualAcademicUpdateDTO = new IndividualAcademicUpdateDTO();
                    $individualAcademicUpdateDTO->setfacultyIdSubmitted($academicUpdate['faculty_id_submitted']);
                    $individualAcademicUpdateDTO->setdateSubmitted($academicUpdate['date_submitted']);
                    $individualAcademicUpdateDTO->setfailureRiskLevel($academicUpdate['failure_risk_level']);
                    $individualAcademicUpdateDTO->setinProgressGrade($academicUpdate['in_progress_grade']);
                    $individualAcademicUpdateDTO->setabsences($academicUpdate['absences']);
                    $individualAcademicUpdateDTO->setcomment($academicUpdate['comment']);
                    $individualAcademicUpdateDTO->setreferForAssistance($academicUpdate['refer_for_assistance']);
                    $individualAcademicUpdateDTO->setsendToStudent($academicUpdate['send_to_student']);
                    $individualAcademicUpdateDTO->setacademicUpdateId($academicUpdate['academic_update_id']);
                    $individualAcademicUpdateDTO->setfinalGrade($academicUpdate['final_grade']);
                    $academicUpdateArray[] = $individualAcademicUpdateDTO;
                }
                $studentWithAcademicUpdateDtoForJob->setAcademicUpdates($academicUpdateArray);

                $studentWithAcademicRecordsArray[] = $studentWithAcademicUpdateDtoForJob;
            }
            $courseAdhocAcademicUpdateDtoForJob->setStudentsWithAcademicUpdates($studentWithAcademicRecordsArray);
            $courseAdhocAcademicUpdateDtoForJobArray[] = $courseAdhocAcademicUpdateDtoForJob;
        }
        $courseStudentAdhocAcademicUpdateDtoForJob = new CoursesStudentsAdhocAcademicUpdateDTO();
        $courseStudentAdhocAcademicUpdateDtoForJob->setCoursesWithAcademicUpdates($courseAdhocAcademicUpdateDtoForJobArray);

        return $courseStudentAdhocAcademicUpdateDtoForJob;
    }

    /**
     * creates and run the academic record object validator
     *
     * @param AcademicRecord $academicRecordObject
     * @return AcademicRecord
     * @throws array|DataProcessingExceptionHandler|SynapseException
     */
    public function validateAcademicRecordObject($academicRecordObject)
    {
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        $dataProcessingExceptionHandler = $this->entityValidationService->validateDoctrineEntity($academicRecordObject, $dataProcessingExceptionHandler, "Default", false);
        $containRequiredErrors = $dataProcessingExceptionHandler->doesErrorHandlerContainError("Default");

        if ($containRequiredErrors) {
            throw $dataProcessingExceptionHandler;
        }

        return $academicRecordObject;
    }

    /**
     * checks the database to make sure the student id given is apart
     * of the database and is a student in the organization. throws error
     * if not given
     *
     * @param integer $studentId
     * @param int $organizationId
     * @param array $studentPersonCriteria
     * @return null|object|Person
     */
    private function getStudentFromDatabase($studentId, $organizationId, $studentPersonCriteria)
    {
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        $dataProcessingExceptionHandler->addErrors("Person Id {$studentId} is not valid at the organization.", "student_id", "required");

        $studentPersonObject = $this->personRepository->findOneBy($studentPersonCriteria, $dataProcessingExceptionHandler);
        $dataProcessingExceptionHandler->resetAllErrors();


        $studentName = $studentPersonObject->getFirstname() . " " . $studentPersonObject->getLastname();
        $studentInternalId = $studentPersonObject->getId();

        $studentErrorMessage = " $studentName is not a valid student.";
        $dataProcessingExceptionHandler->addErrors($studentErrorMessage);

        //Verify that the student is in fact a student.
        $this->orgPersonStudentRepository->findOneBy([
            "person" => $studentInternalId,
            "organization" => $organizationId
        ], $dataProcessingExceptionHandler);

        $dataProcessingExceptionHandler->resetAllErrors();
        $dataProcessingExceptionHandler->addErrors('Student ' . $studentId . ' is not a participating student', 'student_id', 'required');

        return $studentPersonObject;
    }

    /**
     * Validates the student ID passed for the academic update.
     *
     * @param int $studentId
     * @param Person $loggedInUser
     * @return null|object|Person
     */
    public function checkingForStudentBasedOnCriteria($studentId, $loggedInUser)
    {
        // If the user is not a Service Account (API Coordinator), find by the internal ID value.
        if (!is_null($loggedInUser->getExternalId())) {
            $studentFindOneByCriteria = ["id" => $studentId, "organization" => $loggedInUser->getOrganization()->getId()];
        } else {
            $studentFindOneByCriteria = ["externalId" => $studentId, "organization" => $loggedInUser->getOrganization()->getId()];
        }
        return $this->getStudentFromDatabase($studentId, $loggedInUser->getOrganization()->getId(), $studentFindOneByCriteria);
    }

    /**
     * makes sure that the course id given is in the database. Throws an error if it doesn't exist
     *
     * @param integer $courseId
     * @param array $courseFindOneByCriteria
     * @return null|object|OrgCourses
     * @throws DataProcessingExceptionHandler
     */
    private function getCourseFromDatabase($courseId, $courseFindOneByCriteria)
    {
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

        $nonExistentCourseMessage = "The requested course ID $courseId does not exist";
        $dataProcessingExceptionHandler->addErrors($nonExistentCourseMessage);
        $courseObject = $this->orgCoursesRepository->findOneBy($courseFindOneByCriteria, $dataProcessingExceptionHandler);

        $dataProcessingExceptionHandler->resetAllErrors();

        return $courseObject;
    }

    /**
     * Validates the course for the academic update
     *
     * @param int $courseId
     * @param Person $loggedInUser
     * @return null|object|OrgCourses
     */
    private function checkingForCourseBasedOnCriteria($courseId, $loggedInUser)
    {
        // If the user is not a Service Account (API Coordinator), find by the internal ID value.
        if (!is_null($loggedInUser->getExternalId())) {
            $courseFindOneByCriteria = ["id" => $courseId, "organization" => $loggedInUser->getOrganization()->getId()];
        } else {
            $courseFindOneByCriteria = ["externalId" => $courseId, "organization" => $loggedInUser->getOrganization()->getId()];
        }
        return $this->getCourseFromDatabase($courseId, $courseFindOneByCriteria);
    }

    /**
     * Checks to see that the student object is currently taking the course object
     *
     * @param Person $studentObject
     * @param OrgCourses $courseObject
     * @return null|OrgCourseStudent
     * @throws DataProcessingExceptionHandler
     */
    public function getStudentCourseFromDatabase($studentObject, $courseObject)
    {
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        $dataProcessingExceptionHandler->addErrors('Student is not participating in the given course', 'student_id', 'required');
        $orgCourseObject = $this->orgCourseStudentRepository->findOneBy(['person' => $studentObject, 'course' => $courseObject], $dataProcessingExceptionHandler);
        return $orgCourseObject;
    }

    /**
     * Runs the database checks for the faculty join on the academic record object
     *
     * @param integer $facultyId
     * @param integer $organizationId
     * @param array $facultyFindOneByCriteria
     * @return null|Person
     * @throws DataProcessingExceptionHandler
     */
    private function getFacultyFromDatabase($facultyId, $organizationId, $facultyFindOneByCriteria)
    {

        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        $dataProcessingExceptionHandler->addErrors("Person ID {$facultyId} is not valid at the organization.", 'faculty_id_submitted', 'required');
        $facultyPersonObject = $this->personRepository->findOneBy($facultyFindOneByCriteria, $dataProcessingExceptionHandler);
        $dataProcessingExceptionHandler->resetAllErrors();

        //
        $facultyInternalId = $facultyPersonObject->getId();
        $facultyName = $facultyPersonObject->getFirstname() . " " . $facultyPersonObject->getLastname();

        $facultyErrorMessage = "$facultyName is not a valid faculty.";
        $dataProcessingExceptionHandler->addErrors($facultyErrorMessage, "faculty_id_submitted", "required");

        //Verify that the faculty is a faculty. This will only throw errors
        $this->orgPersonFacultyRepository->findOneBy([
            "person" => $facultyInternalId,
            "organization" => $organizationId
        ], $dataProcessingExceptionHandler);
        $dataProcessingExceptionHandler->resetAllErrors();

        return $facultyPersonObject;
    }

    /**
     * Does permissions checks for the faculty submitting the academic update(s)
     *
     * @param int $facultyId
     * @param Person $loggedInUser
     * @return null|Person
     */
    private function checkingForFacultyBasedOnCriteria($facultyId, $loggedInUser)
    {
        // If the user is not a Service Account (API Coordinator), find by the internal ID value.
        if (!is_null($loggedInUser->getExternalId())) {
            $facultyFindOneByCriteria = ["id" => $facultyId, "organization" => $loggedInUser->getOrganization()->getId()];
        } else {
            $facultyFindOneByCriteria = ["externalId" => $facultyId, "organization" => $loggedInUser->getOrganization()->getId()];
        }
        $facultyPersonObject = $this->getFacultyFromDatabase($facultyId, $loggedInUser->getOrganization()->getId(), $facultyFindOneByCriteria);
        return $facultyPersonObject;
    }

    /**
     * Validates the logged in user for permissions to submit the academic update.
     * The external ID check is a permission bypass for Service Accounts (API Coordinators)
     *
     * @param Person $loggedInUser
     * @param Person $studentObject
     * @param OrgCourses $courseObject
     * @param integer $organizationId
     * @return boolean
     */
    private function loggedInUserChecks($loggedInUser, $studentObject, $courseObject, $organizationId)
    {
        if (!is_null($loggedInUser->getExternalId())) {
            $studentName = $studentObject->getFirstname() . " " . $studentObject->getLastname();
            $this->rbacManager->checkAccessToStudent($studentObject->getId(), $loggedInUser->getId(), new AccessDeniedException("You do not have access to $studentName."));
            $this->orgPermissionsetService->checkCreateAcademicUpdatePermissionForCourses($courseObject->getId(), $loggedInUser->getId(), $organizationId, new AccessDeniedException("You do not have permission to access this course " . $courseObject->getExternalId()));
        }
        // Do not need to run these checks in V2
        return true;
    }

    /**
     * Fetches the academic record object if there is one for the student & course. If not, one is created and returned.
     *
     * @param OrgCourses $courseObject
     * @param Person $studentPersonObject
     * @param Organization $organizationObject
     * @return null|object|AcademicRecord
     */
    public function getAcademicRecordObject($courseObject, $studentPersonObject, $organizationObject)
    {
        //Check to see if the academic update Record exists.
        $academicRecordObject = $this->academicRecordRepository->findOneBy([
            'organization' => $organizationObject,
            'orgCourses' => $courseObject,
            'personStudent' => $studentPersonObject
        ]);

        // if it doesn't exist create one
        if (!$academicRecordObject) {
            $academicRecordObject = new AcademicRecord();
            $academicRecordObject->setOrganization($organizationObject);
            $academicRecordObject->setPersonStudent($studentPersonObject);
            $academicRecordObject->setOrgCourses($courseObject);
            $this->academicRecordRepository->persist($academicRecordObject, false);
            $this->academicRecordRepository->flushEntity($academicRecordObject);
        }

        return $academicRecordObject;
    }


    /**
     * Validates if request size is allowed for Academic Update
     *
     * @param string $requestJSON
     * @throws AccessDeniedException
     */
    public function isRequestSizeAllowedForAcademicUpdate($requestJSON)
    {
        $coursesWithAcademicUpdatesArray = json_decode($requestJSON, true);
        $keyToCheck = "academic_updates";
        $academicUpdatesCount = $this->apiValidationService->getRequestSize($coursesWithAcademicUpdatesArray, $keyToCheck);

        $limitForPost = $this->ebiConfigService->get(SynapseConstant::POST_PUT_MAX_RECORD_COUNT);
        if ($academicUpdatesCount > $limitForPost) {
            throw new AccessDeniedException("The body of your POST / PUT request has exceeded the maximum number of create / update records. Please make sure your request contains less than $limitForPost records at the base level of the JSON body.");
        }
    }


    /**
     * Triggers the job to update the organization's academic update download file.
     *
     * @param int $organizationId
     */
    public function updateAcademicUpdateDataFile($organizationId)
    {
        $updateAcademicUpdateDataFileJob = new UpdateAcademicUpdateDataFile();
        $updateAcademicUpdateDataFileJob->args = array(
            'organizationId' => $organizationId
        );
        $this->resque->enqueue($updateAcademicUpdateDataFileJob, true);
    }


    /**
     * Full fill the existing academic updates if exists any,method would return true if any of the academic update is updated,
     * if no academic update request foond then it returns false
     *
     * @param integer $courseId
     * @param integer $studentId
     * @param IndividualAcademicUpdateDTO $academicUpdate
     * @param Person $facultyPersonObject
     * @param Person $loggedInUserObject
     * @param boolean $notifyStudent
     * @param boolean $isInternal
     * @return boolean
     */
    public function fulfillOpenAcademicUpdateRequestsForStudentAndCourse($courseId, $studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, $isInternal)
    {
        $this->academicUpdateCreateService = $this->container->get(AcademicUpdateCreateService::SERVICE_KEY); // Circular reference issue

        $organizationObject = $loggedInUserObject->getOrganization();
        $organizationId = $organizationObject->getId();
        $loggedInUserId = $loggedInUserObject->getId();

        //Get the update date. If there is no update date, set the update date to now.
        $updateDate = $academicUpdate->getDateSubmitted();
        $currentDate = new DateTime();
        $currentDateString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        //Check if there are existing academic updates that are open and tied to an open academic update request. If there are, and the user is
        //Submitting an in_progress_grade, update those academic updates with the in_progress_grade, then close those updates under the request.
        $existingAcademicUpdates = $this->academicUpdateRequestRepository->getAcademicUpdatesInOpenRequestsForStudent($courseId, $organizationId, $studentId, $currentDateString);
        $fullFilledAcademicUpdate = $this->fulfillExistingAcademicUpdates($studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, $isInternal, $existingAcademicUpdates, $loggedInUserId, $updateDate, $currentDate, $organizationId);

        return $fullFilledAcademicUpdate;
    }

    /**
     * Fulfills any existing academic updates; if fulfilled, then returns true.
     * If no academic update request found then returns false
     *
     * @param array $existingAcademicUpdates
     * @param integer $studentId
     * @param IndividualAcademicUpdateDTO $academicUpdate
     * @param Person $facultyPersonObject
     * @param Person $loggedInUserObject
     * @param boolean $notifyStudent
     * @param integer $isInternal
     * @return boolean
     */
    public function fulfillSavedAcademicUpdate($existingAcademicUpdates, $studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, $isInternal)
    {

        $this->academicUpdateCreateService = $this->container->get(AcademicUpdateCreateService::SERVICE_KEY); // Circular reference issue

        $organizationObject = $loggedInUserObject->getOrganization();
        $organizationId = $organizationObject->getId();
        $loggedInUserId = $loggedInUserObject->getId();

        //Get the update date. If there is no update date, set the update date to now.
        $updateDate = $academicUpdate->getDateSubmitted();
        $currentDate = new DateTime();

        //Check if there are existing academic updates that are open and tied to an open academic update request. If there are, and the user is
        //Submitting an in_progress_grade, update those academic updates with the in_progress_grade, then close those updates under the request.
        $fulfilledAcademicUpdate = $this->fulfillExistingAcademicUpdates($studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, $isInternal, $existingAcademicUpdates, $loggedInUserId, $updateDate, $currentDate, $organizationId);
        return $fulfilledAcademicUpdate;
    }

    /**
     * Building academic update object and persisting
     *
     * @param IndividualAcademicUpdateDTO $academicUpdate
     * @param Person $facultyPersonObject
     * @param OrgCourses $courseObject
     * @param Person $studentPersonObject
     * @param Person $loggedInUserObject
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param string $updateType
     * @param boolean $isUpload
     * @param boolean $isAdhoc
     * @return AcademicUpdate
     */
    public function buildAcademicUpdateObject($academicUpdate, $facultyPersonObject, $courseObject, $studentPersonObject, $loggedInUserObject, $academicUpdateRequest = null, $updateType = null, $isUpload = false, $isAdhoc = false)
    {
        $organizationObject = $loggedInUserObject->getOrganization();
        $inProgressGrade = $academicUpdate->getInProgressGrade();
        $finalGrade = $academicUpdate->getFinalGrade();
        $sendToStudent = $academicUpdate->getSendToStudent();
        $failureRiskLevel = $academicUpdate->getFailureRiskLevel();
        $absences = $academicUpdate->getAbsences();
        $comment = $academicUpdate->getComment();
        $referForAssistance = $academicUpdate->getReferForAssistance();

        $updateDate = $academicUpdate->getDateSubmitted();

        $academicUpdateObject = new AcademicUpdate();
        $academicUpdateObject->setAcademicUpdateRequest($academicUpdateRequest);
        $academicUpdateObject->setPersonFacultyResponded($facultyPersonObject);
        $academicUpdateObject->setOrg($organizationObject);
        $academicUpdateObject->setOrgCourses($courseObject);
        $academicUpdateObject->setPersonStudent($studentPersonObject);
        $academicUpdateObject->setStatus('closed');
        $academicUpdateObject->setUpdateType($updateType);
        $academicUpdateObject->setIsAdhoc($isAdhoc);
        $academicUpdateObject->setIsUpload($isUpload);

        $academicUpdateObject->setFailureRiskLevel($failureRiskLevel);
        $academicUpdateObject->setGrade($inProgressGrade);
        $academicUpdateObject->setFinalGrade($finalGrade);
        $academicUpdateObject->setAbsence($absences);
        $academicUpdateObject->setComment($comment);
        $academicUpdateObject->setReferForAssistance($referForAssistance);
        $academicUpdateObject->setSendToStudent($sendToStudent);

        $academicUpdateObject->setUpdateDate($updateDate);
        $academicUpdateObject->setRequestDate($updateDate);
        $academicUpdateObject->setDueDate($updateDate);

        $academicUpdateObject->setCreatedAt($updateDate);
        $academicUpdateObject->setCreatedBy($loggedInUserObject);
        $academicUpdateObject->setModifiedAt($updateDate);
        $academicUpdateObject->setModifiedBy($loggedInUserObject);

        $this->academicUpdateRepository->persist($academicUpdateObject);
        return $academicUpdateObject;
    }


    /**
     * Setting up the academic record object. Checks to see if the DTO has valid data
     * in it before it set the Academic Record Object. Does is_int check because
     * 0 == false in PHP
     *
     * @param int $organizationId
     * @param IndividualAcademicUpdateDTO $academicUpdate
     * @param AcademicRecord $academicRecordEntity
     * @return AcademicRecord
     */
    public function buildAcademicRecordObject($organizationId, $academicUpdate, $academicRecordEntity)
    {
        $updateDate = $academicUpdate->getDateSubmitted();
        $updateDateString = $updateDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        $updateDate = $this->dateUtilityService->adjustOrganizationDateTimeStringToUtcDateTimeObject($updateDateString, $organizationId);

        if ($academicUpdate->getInProgressGrade() || is_int($academicUpdate->getInProgressGrade()) ) {
            $academicRecordEntity->setInProgressGrade($academicUpdate->getInProgressGrade());
            $academicRecordEntity->setInProgressGradeUpdateDate($updateDate);
        }

        if ( $academicUpdate->getFailureRiskLevel() || is_int($academicUpdate->getFailureRiskLevel())) {
            $academicRecordEntity->setFailureRiskLevel($academicUpdate->getFailureRiskLevel());
            $academicRecordEntity->setFailureRiskLevelUpdateDate($updateDate);
        }

        if ($academicUpdate->getAbsences() || is_int($academicUpdate->getAbsences())) {
            $academicRecordEntity->setAbsence($academicUpdate->getAbsences());
            $academicRecordEntity->setAbsenceUpdateDate($updateDate);
        }

        if ($academicUpdate->getComment() || is_int($academicUpdate->getComment())) {
            $academicRecordEntity->setComment($academicUpdate->getComment());
            $academicRecordEntity->setCommentUpdateDate($updateDate);
        }

        if ($academicUpdate->getFinalGrade() || is_int($academicUpdate->getFinalGrade())) {
            $academicRecordEntity->setFinalGrade($academicUpdate->getFinalGrade());
            $academicRecordEntity->setFinalGradeUpdateDate($updateDate);
        }
        $academicRecordEntity->setUpdateDate($updateDate);
        return $academicRecordEntity;
    }

    /**
     * Note that this function will only be called after the DTO has been processed elsewhere, hence the lack of validations in this function.
     * TODO: break this function up much like how the academic record functions are broken up.
     *
     * @param CoursesStudentsAdhocAcademicUpdateDTO $coursesStudentsAcademicUpdateDTO
     * @param int $loggedInUserId
     * @param null|string $updateType
     * @param boolean $isUpload
     * @param boolean $isAdhoc
     * @return boolean
     */
    public function createAcademicUpdateHistory($coursesStudentsAcademicUpdateDTO, $loggedInUserId, $updateType = null, $isUpload = false, $isAdhoc = false)
    {
        $loggedInUserObject = $this->personRepository->find($loggedInUserId);
        $isInternal = !empty($loggedInUserObject->getExternalId());
        $organization = $loggedInUserObject->getOrganization();
        $organizationId = $organization->getId();
        //Get the courses that academic updates were submitted on
        $coursesWithAcademicUpdates = $coursesStudentsAcademicUpdateDTO->getCoursesWithAcademicUpdates();
        $studentArray=[];

        //This does not use a DataProcessingExceptionHandler because, at this point in the process, it is assumed
        // that all of the data passed to this function has been validated as correct. If there are still errors, hard exceptions
        // should be thrown. The 500 response code was chosen because, at this point in the code, if bad data is being passed,
        // something is having a major issue up the function chain.
        $validationException = new SynapseValidationException();
        $validationException->setHttpCode(SynapseConstant::INTERNAL_SERVER_ERROR_CODE);

        foreach ($coursesWithAcademicUpdates as $coursesWithAcademicUpdate) {

            $courseId = $coursesWithAcademicUpdate->getCourseId();
            $validationException->setMessage("Course ID $courseId not found.");

            if ($isInternal) {
                $orgCourseObject = $this->orgCoursesRepository->find($courseId, $validationException);
            } else {
                $orgCourseObject = $this->orgCoursesRepository->findOneBy(['courseSectionId' => $courseId, 'organization' => $organizationId], $validationException);
            }
            //Get the students & the academic updates associated with those students.
            $studentsWithAcademicUpdates = $coursesWithAcademicUpdate->getStudentsWithAcademicUpdates();

            //For each of the students with academic updates
            foreach ($studentsWithAcademicUpdates as $studentsWithAcademicUpdate) {

                $studentId = $studentsWithAcademicUpdate->getStudentId();
                $validationException->setMessage("Student ID $studentId not found.");

                if ($isInternal) {
                    $studentPersonObject = $this->personRepository->find($studentId, $validationException);
                } else {
                    $studentPersonObject = $this->personRepository->findOneBy(['externalId' => $studentId, 'organization' => $organizationId], $validationException);
                }

                //Get the list of academic updates the user is submitting for the student.
                $academicUpdates = $studentsWithAcademicUpdate->getAcademicUpdates();

                $academicUpdates = $this->sortIndividualAcademicUpdateDTOByDataSubmitted($academicUpdates);

                foreach ($academicUpdates as $academicUpdate) {

                    $facultyId = $academicUpdate->getFacultyIdSubmitted();

                    $validationException->setMessage("Faculty ID $facultyId not found.");

                    if ($isInternal) {
                        $facultyPersonObject = $this->personRepository->find($facultyId, $validationException);
                    } else {
                        $facultyPersonObject = $this->personRepository->findOneBy(['externalId' => $facultyId, 'organization' => $organizationId], $validationException);
                    }

                    $notifyStudent = $academicUpdate->getSendToStudent();

                    //Adjust the timezone to UTC from the organization's timezone
                    $dateSubmmitted = $academicUpdate->getDateSubmitted();
                    $utcDateSubmitted = $dateSubmmitted->setTimezone(new \DateTimeZone('UTC'));
                    $academicUpdate->setDateSubmitted($utcDateSubmitted);

                    if ($isAdhoc == true) {
                        // creates the new academic update. Sets Academic Update Requests to null as this is the academic update the API is setting the Academic Update Request to null
                        $this->buildAcademicUpdateObject($academicUpdate, $facultyPersonObject, $orgCourseObject, $studentPersonObject, $loggedInUserObject, null, $updateType, $isUpload, $isAdhoc);
                        $this->checkEmailSendToStudent($notifyStudent, [$studentId], $organization);
                    } else {
                        // fulfills a saved academic update
                        $existingAcademicUpdates = $this->academicUpdateRequestRepository->getSavedAcademicUpdateForStudentFacultyCourse($courseId, $loggedInUserObject->getOrganization()->getId(), $studentId);
                        if ($existingAcademicUpdates) {
                            $this->fulfillSavedAcademicUpdate($existingAcademicUpdates, $studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, true);
                        }

                        // fulfills the academic update request for the student and course
                        $this->fulfillOpenAcademicUpdateRequestsForStudentAndCourse($courseId, $studentId, $academicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, true);
                    }
                    $studentArray[] = $studentPersonObject->getId();
                }
            }

            $organizationId = $loggedInUserObject->getOrganization()->getId();
            $this->orgCalcFlagsRiskService->updateStudentRiskFlagsWithInternalIds(array_unique($studentArray), $organizationId);
            $this->updateAcademicUpdateDataFile($organizationId);
        }
        return true;
    }


    /**
     * takes the array of individualAcademicUpdateDTO's and sorts it based off of the dateSubmitted
     * This function sorts everything ascending
     *
     * @param individualAcademicUpdateDTO[] $individualAcademicUpdateDTOArray
     * @return individualAcademicUpdateDTO[]
     */
    public function sortIndividualAcademicUpdateDTOByDataSubmitted($individualAcademicUpdateDTOArray)
    {
        $sortingArray = [];
        foreach ($individualAcademicUpdateDTOArray as $individualAcademicUpdateDTO) {
            $dateSubmitted = $individualAcademicUpdateDTO->getDateSubmitted();
            $dateSubmitted = strtotime(date_format($dateSubmitted, SynapseConstant::DEFAULT_DATETIME_FORMAT));
            $sortingArray[] = $dateSubmitted;
        }
        array_multisort($individualAcademicUpdateDTOArray, $sortingArray);
        return $individualAcademicUpdateDTOArray;
    }

    /**
     * Builds academic Update
     *
     * @param int $studentId
     * @param IndividualAcademicUpdateDTO $newAcademicUpdate
     * @param Person $facultyPersonObject
     * @param Person $loggedInUserObject
     * @param boolean $notifyStudent
     * @param boolean $isInternal
     * @param array $existingAcademicUpdates
     * @param int $loggedInUserId
     * @param DateTime $updateDate
     * @param DateTime $currentDate
     * @param int $organizationId
     * @return boolean
     */
    public function fulfillExistingAcademicUpdates($studentId, $newAcademicUpdate, $facultyPersonObject, $loggedInUserObject, $notifyStudent, $isInternal, $existingAcademicUpdates, $loggedInUserId, $updateDate, $currentDate, $organizationId)
    {
        $fulfilledAcademicUpdate = false;

        $inProgressGrade = $newAcademicUpdate->getInProgressGrade();
        $sendToStudent = $newAcademicUpdate->getSendToStudent();
        $failureRiskLevel = $newAcademicUpdate->getFailureRiskLevel();
        $absences = $newAcademicUpdate->getAbsences();
        $comment = $newAcademicUpdate->getComment();
        $referForAssistance = $newAcademicUpdate->getReferForAssistance();

        foreach ($existingAcademicUpdates as $existingAcademicUpdate) {
            $existingAcademicUpdateId = $existingAcademicUpdate['academic_update_id'];
            $existingAcademicUpdateRequestId = $existingAcademicUpdate['academic_update_request_id'];
            $existingAcademicUpdateObject = $this->academicUpdateRepository->find($existingAcademicUpdateId);
            $academicUpdateRequestObject = $this->academicUpdateRequestRepository->find($existingAcademicUpdateRequestId);
            $hasUpdateAccess = $this->academicUpdateCreateService->canUserUpdateAcademicUpdate($existingAcademicUpdate['academic_update_id'], $loggedInUserId, $academicUpdateRequestObject);
            if (!$isInternal || ($isInternal && $hasUpdateAccess)) {
                $existingAcademicUpdateObject->setGrade($inProgressGrade);
                $existingAcademicUpdateObject->setPersonFacultyResponded($facultyPersonObject);
                $existingAcademicUpdateObject->setUpdateDate($updateDate);
                $existingAcademicUpdateObject->setFailureRiskLevel($failureRiskLevel);
                $existingAcademicUpdateObject->setAbsence($absences);
                $existingAcademicUpdateObject->setComment($comment);
                $existingAcademicUpdateObject->setReferForAssistance($referForAssistance);
                $existingAcademicUpdateObject->setSendToStudent($sendToStudent);
                $existingAcademicUpdateObject->setModifiedAt($currentDate);
                $existingAcademicUpdateObject->setModifiedBy($loggedInUserObject);
                $existingAcademicUpdateObject->setStatus('closed');
                $this->academicUpdateRequestRepository->flush();
            }
            if ($academicUpdateRequestObject) {
                $this->academicUpdateCreateService->checkToCloseAcademicUpdateRequest($notifyStudent, [$studentId], $academicUpdateRequestObject, $organizationId);
                $fulfilledAcademicUpdate = true;
            }
        }
        return $fulfilledAcademicUpdate;
    }

    /**
     * @param Person $loggedInUser
     * @param boolean $isAdhoc
     * @param boolean $isUpload
     * @param string $updateType
     * @return CreateAcademicUpdateHistoryJob
     */
    public function createAcademicUpdateHistoryJob($loggedInUser, $isAdhoc, $isUpload, $updateType)
    {
        // preparing the academic update history job.
        $serializedSuccessfulDataArray = serialize($this->successfulDataForJob);

        $createAcademicUpdateHistoryJob = new CreateAcademicUpdateHistoryJob();
        $jobNumber = uniqid();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();

        // create the AcademicUpdateHistoryJob arguments
        $createAcademicUpdateHistoryJob->args = [];
        $createAcademicUpdateHistoryJob->args['loggedInUserId'] = $loggedInUserId;
        $createAcademicUpdateHistoryJob->args['organizationId'] = $organizationId;
        $createAcademicUpdateHistoryJob->args['coursesStudentsAcademicUpdateDTO'] = $serializedSuccessfulDataArray;
        $createAcademicUpdateHistoryJob->args['isAdhoc'] = $isAdhoc;
        $createAcademicUpdateHistoryJob->args['isUpload'] = $isUpload;
        $createAcademicUpdateHistoryJob->args['updateType'] = $updateType;
        $createAcademicUpdateHistoryJob->args['jobNumber'] = $jobNumber;

        return $createAcademicUpdateHistoryJob;
    }

    /**
     * Prepares the token values email notification for faculties regarding AU cancel.
     *
     * @param array $facultyAUCancelList
     * @param int $organizationId
     * @param string $emailTemplate
     * @param string $emailKey
     * @param array $details
     */
    protected function buildAcademicUpdateRequestReminderOrCancellationEmail($facultyAUCancelList, $organizationId, $emailTemplate, $emailKey, $details)
    {
        foreach ($facultyAUCancelList as $facultyList) {
            $tokenValues = [];
            $dueDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, new DateTime($facultyList['request_due_date']));
            $tokenValues['faculty_name'] = $facultyList['faculty_firstname'];
            if ($details['action'] == 'reminder') {
                $tokenValues['faculty_au_submission_page'] = $details['submissionPage'];
            }
            $tokenValues['request_name'] = $facultyList['request_name'];
            $tokenValues['due_date'] = $dueDate->format(SynapseConstant::DATE_FORMAT);
            $tokenValues['request_description'] = $facultyList['request_description'];
            $tokenValues['requestor_name'] = $facultyList['requester_firstname'] . " " . $facultyList['requester_lastname'];
            $tokenValues['requestor_email'] = $facultyList['requester_email'];
            $tokenValues['student_update_count'] = $facultyList['total_updates'];
            $tokenValues['custom_message'] = $facultyList['request_email_optional_message'];

            // Including sky factor mapworks logo in email template
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            $tokenValues['Skyfactor_Mapworks_logo'] = "";
            if ($systemUrl) {
                $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            $emailDetails['faculty_email'] = $facultyList['faculty_email'];
            $emailDetails['subject'] = $facultyList['request_email_subject'];
            $emailDetails['emailKey'] = $emailKey;
            $emailDetails['orgId'] = $organizationId;

            $this->sendEmailNotification($emailTemplate, $tokenValues, $emailDetails);
        }
    }
}