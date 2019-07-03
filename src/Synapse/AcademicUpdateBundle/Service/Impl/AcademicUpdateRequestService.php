<?php

namespace Synapse\AcademicUpdateBundle\Service\Impl;

use DateTime;
use Aws\ElasticTranscoder\Exception\ValidationException;
use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateAssignedFaculty;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequestGroup;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequestMetadata;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequestStaticList;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequestStudent;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto;
use Synapse\AcademicUpdateBundle\Exception\AcademicUpdateCreateException;
use Synapse\AcademicUpdateBundle\Job\AcademicUpdateRequestJob;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\ReportsBundle\Service\Impl\ReportDrilldownService;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;

/**
 * @DI\Service("academicupdaterequest_service")
 */
class AcademicUpdateRequestService extends AcademicUpdateServiceHelper
{
    const SERVICE_KEY = 'academicupdaterequest_service';

    //Scaffolding

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    /**
     * @var Resque
     */
    private $resque;

    // Member variables
    /**
     * @var array
     */
    private $academicUpdateUnique = [];

    /**
     * @var array
     */
    private $facultiesDetailsArray = [];

    /**
     * @var int
     */
    private $totalUpdates = 0;

    /**
     * @var bool
     */
    private $isAcademicUpdateCreated = false;

    //Services

    /**
     * @var AcademicUpdateCreateService
     */
    private $academicUpdateCreateService;

    /**
     * @var AcademicUpdateService
     */
    private $academicUpdateService;

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

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
    private $emailService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var ReportDrilldownService
     */
    private $reportDrilldownService;

    /**
     * @var Validator
     */
    private $validatorService;

    //Repositories

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
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

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
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var OrgMetadataRepository
     */
    private $orgMetadataRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgStaticListRepository
     */
    private $orgStaticListRepository;

    /**
     * @var OrgStaticListStudentsRepository
     */
    private $orgStaticListStudentsRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    //Class Constants

    CONST NO_STUDENTS_IN_FILTER_MESSAGE = "You either do not have individual access to all of the students in the filter, or the students are all not participating. Please refine your request's filter criteria.";


    /**
     * AcademicUpdateRequestService constructor.
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
     * @throws \Exception
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //Scaffolding
        parent::__construct($repositoryResolver, $logger, $container);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        //Services
        $this->academicUpdateCreateService = $this->container->get(AcademicUpdateCreateService::SERVICE_KEY);
        $this->academicUpdateService = $this->container->get(AcademicUpdateService::SERVICE_KEY);
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->reportDrilldownService = $this->container->get(ReportDrilldownService::SERVICE_KEY);
        $this->validatorService = $this->container->get(SynapseConstant::VALIDATOR);

        //Repositories
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->academicUpdateRequestRepository = $this->repositoryResolver->getRepository(AcademicUpdateRequestRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
        $this->orgCourseRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgMetadataRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgStaticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);
        $this->orgStaticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Initiate an academic update request job
     *
     * TODO:: This function validates the academic update request before passing it off to the job. It is duplicating the validation
     * done on the academic update request object. This function should just create and enqueue the resque job, and all of this
     * validation should be checked for in createAcademicUpdateRequest, and the code removed from this function once the check has been
     * done.
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Person $loggedInPersonObject
     * @return AcademicUpdateCreateDto $academicUpdateCreateDto
     * @throws SynapseValidationException
     */
    public function initiateAcademicUpdateRequestJob(AcademicUpdateCreateDto $academicUpdateCreateDto, $loggedInPersonObject)
    {
        $loggedInUserId = $loggedInPersonObject->getId();
        $organization = $loggedInPersonObject->getOrganization();
        $organizationId = $organization->getId();
        if ($organizationId != $academicUpdateCreateDto->getOrganizationId()) {
            throw new SynapseValidationException('Invalid Organization');
        }
        $isCoordinator = $this->organizationRoleRepository->findOneBy(['organization' => $organizationId, 'person' => $loggedInUserId]);
        if (!$isCoordinator) {
            $this->logger->error("The user attempting to create a request ($loggedInUserId) is not a coordinator.");
            throw new SynapseValidationException('Only coordinators are allowed to create an academic update request, and you do not have coordinator access.');
        }
        $academicUpdateRequest = new AcademicUpdateRequest();

        $currentDate = new \DateTime();
        $academicUpdateRequest->setOrg($organization);
        $academicUpdateRequest->setPerson($loggedInPersonObject);
        $academicUpdateRequest->setUpdateType($this->getUpdateType($academicUpdateCreateDto));
        $academicUpdateRequest->setRequestDate($currentDate);
        $academicUpdateRequest->setName($academicUpdateCreateDto->getRequestName());
        $academicUpdateRequest->setDescription($academicUpdateCreateDto->getRequestDescription());
        $academicUpdateRequest->setStatus('open');
        $dueDate = $academicUpdateCreateDto->getRequestDueDate();

        $dueDate->setTime(23, 59, 59);

        $currentAcademicYearDate = $this->orgAcademicYearRepository->getCountCurrentAcademic($dueDate->format('Y-m-d'), $organization);
        $currentAcademicYearDate = call_user_func_array('array_merge', $currentAcademicYearDate);
        if ($currentAcademicYearDate['oayCount'] <= 0) {
            throw new SynapseValidationException('Invalid due date. Please enter another.');
        }
        $this->validateDueDate($currentDate, $dueDate);
        $academicUpdateRequest->setDueDate($dueDate);
        $academicUpdateRequest->setSelectCourse($this->getSelectedType($academicUpdateCreateDto, 'courses'));
        $academicUpdateRequest->setSelectStudent($this->getSelectedType($academicUpdateCreateDto, 'students'));
        $academicUpdateRequest->setSelectGroup($this->getSelectedType($academicUpdateCreateDto, 'groups'));
        $academicUpdateRequest->setSelectFaculty($this->getSelectedType($academicUpdateCreateDto, 'staff'));
        $academicUpdateRequest->setSelectStaticList($this->getSelectedType($academicUpdateCreateDto, 'staticList'));
        $academicUpdateRequest->setSubject($academicUpdateCreateDto->getRequestEmailSubject());
        $academicUpdateRequest->setEmailOptionalMsg($academicUpdateCreateDto->getRequestEmailOptionalMessage());
        $this->validateAcademicUpdateRequest($academicUpdateRequest);
        $academicUpdateRequestJob = new AcademicUpdateRequestJob;
        $jobNumber = uniqid();
        $academicUpdateRequestJob->args = array('jobNumber' => $jobNumber, 'academicUpdateCreateDto' => serialize($academicUpdateCreateDto), 'userId' => $loggedInUserId);
        $this->resque->enqueue($academicUpdateRequestJob, true);
        return $academicUpdateCreateDto;
    }

    /**
     * Create an Academic Update Request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param int $loggedInUserId
     * @return AcademicUpdateCreateDto
     * @throws SynapseValidationException
     */
    public function createAcademicUpdateRequest(AcademicUpdateCreateDto $academicUpdateCreateDto, $loggedInUserId)
    {
        $loggedInPerson = $this->personRepository->find($loggedInUserId);
        $organization = $loggedInPerson->getOrganization();
        $organizationId = $organization->getId();
        if ($organizationId != $academicUpdateCreateDto->getOrganizationId()) {
            throw new SynapseValidationException('Invalid Organization');
        }
        $isCoordinator = $this->organizationRoleRepository->findOneBy(['organization' => $organizationId, 'person' => $loggedInUserId]);
        if (!$isCoordinator) {
            $this->logger->error("The user attempting to create a request ($loggedInUserId) is not a coordinator.");
            throw new SynapseValidationException('Only coordinators are allowed to create an academic update request, and you do not have coordinator access.');
        }
        $academicUpdateRequest = new AcademicUpdateRequest();

        $currentDate = new \DateTime();
        $academicUpdateRequest->setOrg($organization);
        $academicUpdateRequest->setPerson($loggedInPerson);
        $academicUpdateRequest->setUpdateType($this->getUpdateType($academicUpdateCreateDto));
        $academicUpdateRequest->setRequestDate($currentDate);
        $academicUpdateRequest->setName($academicUpdateCreateDto->getRequestName());
        $academicUpdateRequest->setDescription($academicUpdateCreateDto->getRequestDescription());
        $academicUpdateRequest->setStatus('open');
        $academicUpdateRequestDueDateTimeObject = $academicUpdateCreateDto->getRequestDueDate();
        $academicUpdateRequestDueDateTimeObject->setTime(23, 59, 59);

        //Translating Due Date From Local TimeZone into UTC for database storage
        $academicUpdateRequestUtcDueDateObject = $this->dateUtilityService->adjustOrganizationDateTimeStringToUtcDateTimeObject($academicUpdateRequestDueDateTimeObject->format(SynapseConstant::DEFAULT_DATETIME_FORMAT), $organizationId);

        $currentAcademicYearDate = $this->orgAcademicYearRepository->getCountCurrentAcademic($academicUpdateRequestUtcDueDateObject->format(SynapseConstant::DEFAULT_DATE_FORMAT), $organization);
        $currentAcademicYearDate = call_user_func_array('array_merge', $currentAcademicYearDate);
        if ($currentAcademicYearDate['oayCount'] <= 0) {
            $academicUpdateRequestUtcDueDateString = $academicUpdateRequestUtcDueDateObject->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            $this->logger->error("The due date requested ($academicUpdateRequestUtcDueDateString) does not fall into an existing academic year.");
            throw new SynapseValidationException('Invalid due date. Please enter another.');
        }
        $academicUpdateRequest->setDueDate($academicUpdateRequestUtcDueDateObject);
        $academicUpdateRequest->setSelectCourse($this->getSelectedType($academicUpdateCreateDto, 'courses'));
        $academicUpdateRequest->setSelectStudent($this->getSelectedType($academicUpdateCreateDto, 'students'));
        $academicUpdateRequest->setSelectGroup($this->getSelectedType($academicUpdateCreateDto, 'groups'));
        $academicUpdateRequest->setSelectFaculty($this->getSelectedType($academicUpdateCreateDto, 'staff'));
        $academicUpdateRequest->setSelectStaticList($this->getSelectedType($academicUpdateCreateDto, 'staticList'));
        $academicUpdateRequest->setCreatedBy($loggedInPerson);

        //Email Related data
        $academicUpdateRequest->setSubject($academicUpdateCreateDto->getRequestEmailSubject());
        $academicUpdateRequest->setEmailOptionalMsg($academicUpdateCreateDto->getRequestEmailOptionalMessage());
        $this->validateDueDate($currentDate, $academicUpdateRequestUtcDueDateObject);
        $academicUpdateRequest = $this->academicUpdateRequestRepository->persist($academicUpdateRequest, false);
        $this->determineAcademicUpdateRequestStudentsByStudentFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPerson);
        $this->determineAcademicUpdateRequestStudentsByFacultyFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate);
        $this->determineAcademicUpdateRequestStudentsByCourseFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate);
        $this->determineAcademicUpdateRequestStudentsByGroupFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPerson);
        $this->determineAcademicUpdateRequestStudentsByProfileFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPerson);
        $this->determineAcademicUpdateRequestStudentsByStaticListFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPerson);
        // If there no academic update created,then it will throw an exception
        if (!$this->isAcademicUpdateCreated) {
            $this->logger->error("The filters selected were too restrictive;. The function name" . __FUNCTION__ . ";The serialized academicUpdateCreateDto" . serialize($academicUpdateCreateDto) . ";The logged in user's ID" . $loggedInPerson->getId());
            throw new SynapseValidationException('The filters for the academic update request were too restrictive.');
        }
        $this->academicUpdateRequestRepository->flush();
        $academicUpdateCreateDto->setId($academicUpdateRequest->getId());
        $this->buildAndSendAcademicUpdateRequestAssigneeEmails($academicUpdateRequest, $academicUpdateCreateDto, $loggedInUserId, $organizationId);
        $this->alertNotificationsService->createNotification('academic-updates-completed', $this->totalUpdates . ' Academic Update requests have been successfully sent. View your requests', $loggedInPerson, null, null, null, null, null, null, $organization);
        $this->academicUpdateService->updateAcademicUpdateDataFile($organizationId);
        return $academicUpdateCreateDto;
    }

    /**
     * Get an academic update count for request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Person $loggedInPersonObject
     * @return AcademicUpdateCreateDto
     * @throws ValidationException
     */
    public function getAcademicUpdateCountForRequest(AcademicUpdateCreateDto $academicUpdateCreateDto, $loggedInPersonObject)
    {
        $loggedInUserId = $loggedInPersonObject->getId();
        $organization = $loggedInPersonObject->getOrganization();
        $dueDate = $academicUpdateCreateDto->getRequestDueDate();
        $academicYears = $this->orgAcademicYearRepository->getCountCurrentAcademic($dueDate->format('Y-m-d'), $organization);
        $academicYears = call_user_func_array('array_merge', $academicYears);
        if ($academicYears['oayCount'] <= 0) {
            $dueDateString = $dueDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            $this->logger->error("The due date requested ($dueDateString) does not fall into an existing academic year.");
            throw new SynapseValidationException('Invalid due date. Please enter another');
        }
        $academicUpdateRequest = NULL;
        $currentDate = new \DateTime();
        $this->determineAcademicUpdateRequestStudentsByStudentFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, false);
        $this->determineAcademicUpdateRequestStudentsByFacultyFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, false);
        $this->determineAcademicUpdateRequestStudentsByCourseFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, false);
        $this->determineAcademicUpdateRequestStudentsByGroupFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, false);
        $this->determineAcademicUpdateRequestStudentsByProfileFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, false);
        $this->determineAcademicUpdateRequestStudentsByStaticListFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, false);
        if (!$this->isAcademicUpdateCreated) {
            $this->logger->error("The filters selected were too restrictive;. The function name" . __FUNCTION__ . ";The serialized academicUpdateCreateDto" . serialize($academicUpdateCreateDto) . ";The logged in user's ID" . $loggedInUserId);
            throw new SynapseValidationException('The specified filter criteria will generate no academic updates. Please select less restrictive filter criteria');
        }
        $academicUpdateCreateDto->setUpdateCount($this->totalUpdates);
        return $academicUpdateCreateDto;
    }

    /**
     * Determines the student population for the student filter of creating an academic update request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param DateTime $currentDate
     * @param bool $academicUpdateCreateFlag
     * @param Person $loggedInPersonObject
     */
    private function determineAcademicUpdateRequestStudentsByStudentFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, $academicUpdateCreateFlag = true)
    {
        $selectedStudents = $academicUpdateCreateDto->getStudents();
        if ($selectedStudents->getIsAll()) {
            $organizationId = $organization->getId();
            $studentList = $this->orgCourseStudentRepository->getStudentsInAnyCurrentCourse($organizationId);
            $studentList = array_unique(array_column($studentList, 'student_id'));

        } else {
            // Selected student ids is a string separated by commas, if it's not empty.
            $studentsSelected = $selectedStudents->getSelectedStudentIds();
            if (!empty($studentsSelected)) {
                $studentList = explode(",", $studentsSelected);
                $studentList = array_unique($studentList);
            } else {
                $studentList = [];
            }
        }
        if (count($studentList) > 0) {
            if ($academicUpdateCreateFlag) {
                $this->createAllAcademicUpdatesForRequest($studentList, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject);
            } else {
                $this->updateTotalAcademicUpdateRequestCount($studentList, $currentDate, $loggedInPersonObject, $organization);
            }
        }
    }

    /**
     * Determines the student population for the faculty filter of creating an academic update request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param DateTime $currentDate
     * @param bool $academicUpdateCreateFlag
     */
    private function determineAcademicUpdateRequestStudentsByFacultyFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $academicUpdateCreateFlag = true)
    {
        $selectedStaff = $academicUpdateCreateDto->getStaff();
        if (!$selectedStaff->getIsAll()) {
            $staffSelected = $selectedStaff->getSelectedStaffIds();
            $selectedStaffs = explode(",", $staffSelected);
            if (count($selectedStaffs) > 0) {
                $this->createAcademicUpdateByFaculty($selectedStaffs, $organization, $academicUpdateRequest, $currentDate, $academicUpdateCreateFlag);
            }
        } else {
            $staffList = $this->orgPersonFacultyRepository->getFacultiesByOrganizationCourse($organization->getId(), $currentDate);
            if (count($staffList) > 0) {
                $selectedStaffs = array_unique(array_column($staffList, 'staff_id'));
                $this->createAcademicUpdateByFaculty($selectedStaffs, $organization, $academicUpdateRequest, $currentDate, $academicUpdateCreateFlag);
            }
        }
    }

    /**
     * Determines the student population for the course filter of creating an academic update request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param DateTime $currentDate
     * @param bool $academicUpdateCreateFlag
     */
    private function determineAcademicUpdateRequestStudentsByCourseFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $academicUpdateCreateFlag = true)
    {
        $selectedCourses = $academicUpdateCreateDto->getCourses();
        if (!$selectedCourses->getIsAll()) {
            $coursesSelected = $selectedCourses->getSelectedCourseIds();
            $selectedCourses = explode(",", $coursesSelected);
            if (count($selectedCourses) > 0 && !empty($selectedCourses[0])) {
                $this->createAcademicUpdateByCourse($selectedCourses, $organization, $academicUpdateRequest, $academicUpdateCreateFlag);
            }
        } else {
            if ($academicUpdateCreateFlag) {
                $courseList = $this->orgCourseRepository->getActiveCourseByOrganization($organization, $currentDate);
                $courseColumn = 'id';
            } else {
                //TODO:: Move this datetime string higher in the calling function chain, and convert the other functions using this date from DQL to SQL.
                $currentDateAsString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                $courseList = $this->orgCourseRepository->getAllCoursesEncapsulatingDatetime($organization->getId(), $currentDateAsString);
                $courseColumn = 'org_courses_id';
            }

            if (count($courseList) > 0) {
                $selectedCourses = array_unique(array_column($courseList, $courseColumn));
                $this->createAcademicUpdateByCourse($selectedCourses, $organization, $academicUpdateRequest, $academicUpdateCreateFlag);
            }
        }
    }

    /**
     * Determines the student population for the group filter of creating an academic update request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param DateTime $currentDate
     * @param bool $academicUpdateCreateFlag
     * @param Person $loggedInPersonObject
     */
    private function determineAcademicUpdateRequestStudentsByGroupFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject , $academicUpdateCreateFlag = true)
    {
        $selectedGroups = $academicUpdateCreateDto->getGroups();
        if ($selectedGroups->getIsAll()) {
            $groupsSelected = $this->orgGroupRepository->getGroupByOrganization($organization->getId());
            $groupsSelected = array_unique(array_column($groupsSelected, 'group_id'));
        } else {
            $groupsSelected = $selectedGroups->getSelectedGroupIds();
            $groupsSelected = explode(",", $groupsSelected);
            if ($academicUpdateCreateFlag) {
                if (count($groupsSelected) > 0 && !empty($groupsSelected[0])) {
                    foreach ($groupsSelected as $group) {
                        $academicUpdateRequestGroup = new AcademicUpdateRequestGroup();
                        $academicUpdateRequestGroup->setOrg($organization);
                        $academicUpdateRequestGroup->setAcademicUpdateRequest($academicUpdateRequest);
                        $academicUpdateRequestGroup->setOrgGroup($this->orgGroupRepository->getPersonReferance($group));
                        $this->orgGroupRepository->persist($academicUpdateRequestGroup, false);
                    }
                } else {
                    $groupsSelected = [];
                }
            }
        }
        $organizationAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organization->getId());
        $students = $this->orgGroupStudentsRepository->getNonArchivedStudentsByGroups($groupsSelected, $organizationAcademicYearId);
        if (count($students) > 0) {
            if ($academicUpdateCreateFlag) {
                $this->createAllAcademicUpdatesForRequest($students, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, false);
            } else {
                $this->updateTotalAcademicUpdateRequestCount($students, $currentDate, $loggedInPersonObject, $organization);

            }
        }
    }

    /**
     * Determines the student population for the profile item filter of creating an academic update request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param DateTime $currentDate
     * @param bool $academicUpdateCreateFlag
     * @param Person $loggedInPersonObject
     */
    private function determineAcademicUpdateRequestStudentsByProfileFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject , $academicUpdateCreateFlag = true)
    {
        $studentsArray = [];
        $profileItemStudentsArray = [];
        $ISPstudentsArray = [];
        $organizationId = $organization->getId();

        $profileItems = $academicUpdateCreateDto->getProfileItems();
        if ($profileItems) {

            $ISPItem = $profileItems->getIsps();
            if (count($ISPItem) > 0) {
                $ISPstudentsArray = $this->academicUpdateCreateService->getAllStudentsByProfileItemType('org', $organizationId, $ISPItem);
                if ($academicUpdateCreateFlag) {
                    $this->assignProfileItemsToAcademicUpdateRequest($ISPItem, 'org', $academicUpdateRequest);
                }
            }

            $profileItem = $profileItems->getEbi();
            if (count($profileItem) > 0) {
                $profileItemStudentsArray = $this->academicUpdateCreateService->getAllStudentsByProfileItemType('ebi', $organizationId, $profileItem);
                if ($academicUpdateCreateFlag) {
                    $this->assignProfileItemsToAcademicUpdateRequest($profileItem, 'ebi', $academicUpdateRequest);
                }
            }

            if (!empty($profileItemStudentsArray) || !empty($ISPstudentsArray)) {
                $studentsArray = array_merge($ISPstudentsArray, $profileItemStudentsArray);
            }

            if (count($studentsArray) > 0) {
                $studentsArray = array_unique(array_column($studentsArray, 'person_id'));
                if ($academicUpdateCreateFlag) {
                    $this->createAllAcademicUpdatesForRequest($studentsArray, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, false);
                } else {
                    $this->updateTotalAcademicUpdateRequestCount($studentsArray, $currentDate, $loggedInPersonObject, $organization);
                }
            }
        }
    }

    /**
     * Determines the student population for the group filter of creating an academic update request
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param DateTime $currentDate
     * @param  bool $academicUpdateCreateFlag
     * @param Person $loggedInPerson
     */
    private function determineAcademicUpdateRequestStudentsByStaticListFilter($academicUpdateCreateDto, $organization, $academicUpdateRequest, $currentDate, $loggedInPerson , $academicUpdateCreateFlag = true)
    {
        $organizationId = $organization->getId();
        $selectedStaticLists = $academicUpdateCreateDto->getStaticList();
        if (!$selectedStaticLists->getIsAll()) {
            $staticListSelected = $selectedStaticLists->getSelectedStaticIds();
            $staticListSelected = explode(",", $staticListSelected);
            if (count($staticListSelected) > 0 && !empty($staticListSelected[0])) {
                foreach ($staticListSelected as $staticList) {
                    $academicUpdateStaticList = new AcademicUpdateRequestStaticList();
                    $academicUpdateStaticList->setOrganization($organization);
                    $academicUpdateStaticList->setAcademicUpdateRequest($academicUpdateRequest);
                    $orgStaticList = $this->orgStaticListRepository->getStaticListReferance($staticList);
                    $academicUpdateStaticList->setOrgStaticList($orgStaticList);
                    $academicUpdateStaticList->setCreatedBy($loggedInPerson);
                    $this->orgStaticListRepository->persist($academicUpdateStaticList, false);
                }
            } else {
                $staticListSelected = [];
            }
        } else {
            $staticListSelected = $this->orgStaticListRepository->getAllStaticLists($organizationId, $loggedInPerson);
            $staticListSelected = array_unique(array_column($staticListSelected, 'id'));
        }
        $organizationAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $allStudents = $this->orgStaticListStudentsRepository->getStudentsByList($staticListSelected, $organizationId, $organizationAcademicYearId);

        if (count($allStudents) > 0) {
            $selectedStudents = array_column($allStudents, 'person_id');
            if ($academicUpdateCreateFlag) {
                $this->createAllAcademicUpdatesForRequest($selectedStudents, $organization, $academicUpdateRequest, $currentDate, $loggedInPerson, false);
            } else {
                $this->updateTotalAcademicUpdateRequestCount($selectedStudents, $currentDate, $loggedInPerson, $organization);
            }
        }
    }

    /**
     * Update total academic update request count
     *
     * @param array $individuallyAccessibleParticipants
     * @param DateTime $currentDate
     * @param Person $loggedInPerson
     * @param Organization|null $organization
     */
    private function updateTotalAcademicUpdateRequestCount($individuallyAccessibleParticipants, $currentDate, $loggedInPerson, $organization)
    {
        if (!empty($individuallyAccessibleParticipants)) {
            $loggedInPersonId = $loggedInPerson->getId();
            $organizationId = $organization->getId();
            $individuallyAccessibleParticipants = $this->reportDrilldownService->getIndividuallyAccessibleParticipants($loggedInPersonId, $organizationId, $individuallyAccessibleParticipants, self::NO_STUDENTS_IN_FILTER_MESSAGE);
            $coursesForStudent = $this->orgCourseStudentRepository->getCoursesForStudent($individuallyAccessibleParticipants, $currentDate);
            foreach ($coursesForStudent as $courseForStudent) {
                // Find all the faculties associated with a course
                $this->addAcademicUpdateForRequestCount($courseForStudent['org_courses_id'], $courseForStudent['person_id']);
            }
        }
    }

    /**
     * Add an academic update request count
     *
     * @param int $studentCourse
     * @param int $personStudent
     */
    private function addAcademicUpdateForRequestCount($studentCourse, $personStudent)
    {
        $uniqueStudentCourseComboKey = $studentCourse . "-" . $personStudent;
        if (!in_array($uniqueStudentCourseComboKey, $this->academicUpdateUnique)) {
            $this->academicUpdateUnique[] = $uniqueStudentCourseComboKey;
            $this->totalUpdates++;
            $this->isAcademicUpdateCreated = true;
        }
    }

    /**
     * Creates all academic updates for the request, one for each student-course combination applicable to the filter.
     *
     * @param array $individuallyAccessibleParticipants
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param DateTime $currentDate
     * @param boolean $isStudentsSelected
     * @param Person $loggedInPersonObject
     */
    private function createAllAcademicUpdatesForRequest($individuallyAccessibleParticipants, $organization, $academicUpdateRequest, $currentDate, $loggedInPersonObject, $isStudentsSelected = true)
    {
        $uniqueStudentList = [];
        $loggedInPersonId = $loggedInPersonObject->getId();
        $organizationId = $organization->getId();
        $individuallyAccessibleParticipants = $this->reportDrilldownService->getIndividuallyAccessibleParticipants($loggedInPersonId, $organizationId, $individuallyAccessibleParticipants, self::NO_STUDENTS_IN_FILTER_MESSAGE);
        $studentsAndCourses = $this->orgCourseStudentRepository->getCoursesForStudent($individuallyAccessibleParticipants, $currentDate);
        foreach ($studentsAndCourses as $studentAndCourse) {
            $studentId = $studentAndCourse['person_id'];
            $courseId = $studentAndCourse['org_courses_id'];
            $personObject = $this->personRepository->find($studentId);

            if ($isStudentsSelected && $academicUpdateRequest->getSelectStudent() == 'individual' && !in_array($studentId, $uniqueStudentList)) {
                $academicUpdateRequestStudent = new AcademicUpdateRequestStudent();
                $academicUpdateRequestStudent->setOrg($organization);
                $academicUpdateRequestStudent->setPerson($personObject);
                $academicUpdateRequestStudent->setAcademicUpdateRequest($academicUpdateRequest);
                $this->academicUpdateRepository->persist($academicUpdateRequestStudent, false);
                $uniqueStudentList[] = $studentId;

            }

            // Find all the faculties associated with a course
            $orgCourseFacultyObject = $this->orgCourseFacultyRepository->getFacultiesForCourse($courseId);
            if (!$orgCourseFacultyObject) {
                continue;
            }

            $orgCourseObject = $this->orgCourseRepository->find($courseId);
            $academicUpdate = $this->createIndividualAcademicUpdateForRequest($academicUpdateRequest, $organization, $orgCourseObject, $personObject);
            $this->assignFacultyToIndividualAcademicUpdate($orgCourseFacultyObject, $organization, $academicUpdate);

            unset($personObject);
            unset($orgCourseFacultyObject);
            unset($orgCourseObject);
        }
    }

    /**
     * Validate an academic update request
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @throws SynapseValidationException
     */
    private function validateAcademicUpdateRequest($academicUpdateRequest)
    {
        $errors = $this->validatorService->validate($academicUpdateRequest);

        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {

                $errorsString .= $error->getMessage();
            }
            throw new SynapseValidationException("Academic update request error $errorsString ");
        }
    }

    /**
     * Create an academic update if user selects faculty
     *
     * @param array $selectedFaculty
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param \DateTime $currentDate
     * @param bool $createAcademicUpdateFlag
     * @throws AcademicUpdateCreateException
     */
    private function createAcademicUpdateByFaculty($selectedFaculty, $organization, $academicUpdateRequest, $currentDate, $createAcademicUpdateFlag = true)
    {
        foreach ($selectedFaculty as $selectedStaff) {
            $coursesForStaffArray = $this->orgCourseFacultyRepository->getCoursesForStaff($selectedStaff, $currentDate);
            try {
                $this->checkArrayCount($coursesForStaffArray);
                if ($createAcademicUpdateFlag) {
                    $personStaff = $this->personRepository->find($selectedStaff);
                    $this->addSelectedFaculty($academicUpdateRequest, $organization, $personStaff);
                }
                foreach ($coursesForStaffArray as $courseForStaff) {
                    $students = $this->orgCourseStudentRepository->getStudentsByCourse($courseForStaff['courseId']);
                    try {
                        $this->checkArrayCount($students);
                    } catch (AcademicUpdateCreateException $e) {
                        continue;
                    }
                    if ($createAcademicUpdateFlag) {
                        foreach ($students as $student) {
                            $personObject = $this->personRepository->find($student['studentId']);
                            $orgCourseObject = $this->orgCourseRepository->find($courseForStaff['courseId']);
                            $createdAcademicUpdate = $this->createIndividualAcademicUpdateForRequest($academicUpdateRequest, $organization, $orgCourseObject, $personObject);
                            if (!$createdAcademicUpdate) {
                                $this->academicUpdateRepository->flush();
                                $createdAcademicUpdate = $this->academicUpdateRepository->findOneBy(['academicUpdateRequest' => $academicUpdateRequest, 'orgCourses' => $orgCourseObject, 'personStudent' => $personObject, 'status' => 'open']);
                            }

                            if ($createdAcademicUpdate) {
                                $academicUpdateAssignedFaculty = new AcademicUpdateAssignedFaculty();
                                $academicUpdateAssignedFaculty->setPersonFacultyAssigned($personStaff);
                                $academicUpdateAssignedFaculty->setOrg($organization);
                                $academicUpdateAssignedFaculty->setAcademicUpdate($createdAcademicUpdate);
                                $this->academicUpdateRequestRepository->persist($academicUpdateAssignedFaculty, false);
                                $this->isAcademicUpdateCreated = true;
                                $this->facultiesDetailsArray[] = $selectedStaff;
                            }
                        }
                    } else {
                        // Count the the number of academic updates in the request for the faculty
                        foreach ($students as $student) {
                            $this->addAcademicUpdateForRequestCount($courseForStaff['courseId'], $student['studentId']);
                        }
                    }
                }
            } catch (AcademicUpdateCreateException $academicUpdateCreateException) {
                continue;
            }
        }
    }

    /**
     * Builds and sends an email to all assigned faculty for an academic update request.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param boolean $closedFlag
     */
    private function buildAndSendAcademicUpdateRequestAssigneeEmails($academicUpdateRequest, $academicUpdateCreateDto, $loggedInUserId, $organizationId, $closedFlag = false)
    {
        $personObject = $this->personRepository->getUsersByUserIds($this->facultiesDetailsArray);
        $emailKey = 'Academic_Update_Request_Staff';
        $loggedInPersonObject = $this->personRepository->find($loggedInUserId);
        $loggedInPersonFirstName = $loggedInPersonObject->getFirstname();
        $loggedInPersonLastName = $loggedInPersonObject->getLastname();
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $updateViewUrl = "";
        if ($systemUrl) {
            $updateViewUrl = $systemUrl . AcademicUpdateConstant::AU_VIEW_URL;
        }
        $ebiLang = $this->ebiConfigRepository->findOneBy(['key' => 'Ebi_Lang']);
        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $ebiLang->getValue());
        $emailBodyMessageTemplate = $emailTemplate->getBody();

        $tokenValues['requestor'] = "$loggedInPersonFirstName  $loggedInPersonLastName";
        $tokenValues['requestor_email'] = $loggedInPersonObject->getUsername();
        $tokenValues['requestname'] = $academicUpdateRequest->getName();
        $tokenValues['studentupdate'] = $this->totalUpdates;
        $academicUpdateEmailOptionalMessage = $academicUpdateRequest->getEmailOptionalMsg();

        if (is_null($academicUpdateEmailOptionalMessage)) {
            $tokenValues['optional_message'] = "";
        } else {
            $tokenValues['optional_message'] = $academicUpdateEmailOptionalMessage;
        }

        $tokenValues['description'] = $academicUpdateRequest->getDescription();
        $tokenValues['updateviewurl'] = $updateViewUrl . $academicUpdateRequest->getId();
        if ($closedFlag) {
            $tokenValues['duedate'] = $academicUpdateRequest->getDueDate()->format(SynapseConstant::DATE_FORMAT);
            $subject = "Closed : ";
        } else {
            $tokenValues['duedate'] = $academicUpdateCreateDto->getRequestDueDate()->format(SynapseConstant::DATE_FORMAT);
            $subject = "";
        }
        $facultiesCount = array_count_values($this->facultiesDetailsArray);
        $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
        $responseArray = [];
        foreach ($personObject as $personDetail) {
            $tokenValues['studentupdate'] = $facultiesCount[$personDetail['user_id']];
            $emailBody = $this->emailService->generateEmailMessage($emailBodyMessageTemplate, $tokenValues);
            $responseArray['email_detail'] = [
                'from' => $from,
                'subject' => $subject . $academicUpdateRequest->getSubject(),
                'bcc' => $emailTemplate->getEmailTemplate()->getBccRecipientList(),
                'body' => $emailBody,
                'emailKey' => $emailKey,
                'to' => trim($personDetail['username']),
                'organizationId' => $organizationId
            ];
            $emailServiceInstance = $this->emailService->sendEmailNotification($responseArray['email_detail']);
            $this->emailService->sendEmail($emailServiceInstance);
        }
    }

    /**
     * Submit an academic update for student.
     *
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param Organization $organization
     * @param OrgCourses $studentCourse
     * @param Person $personStudent
     * @return AcademicUpdate | null
     * @throws AcademicUpdateCreateException
     */
    private function createIndividualAcademicUpdateForRequest($academicUpdateRequest, $organization, $studentCourse, $personStudent)
    {
        $academicUpdate = null;
        $combinedCourseIdAndStudentId = $studentCourse->getId() . "-" . $personStudent->getId();
        if (!in_array($combinedCourseIdAndStudentId, $this->academicUpdateUnique)) {
            $academicUpdate = new AcademicUpdate();
            $academicUpdate->setOrg($organization);
            $academicUpdate->setAcademicUpdateRequest($academicUpdateRequest);
            $academicUpdate->setOrgCourses($studentCourse);
            $academicUpdate->setPersonStudent($personStudent);
            $academicUpdate->setUpdateType($academicUpdateRequest->getUpdateType());
            $academicUpdate->setStatus($academicUpdateRequest->getStatus());
            $academicUpdate->setRequestDate($academicUpdateRequest->getRequestDate());
            $academicUpdate->setDueDate($academicUpdateRequest->getDueDate());
            $this->academicUpdateRequestRepository->persist($academicUpdate, false);
            $this->academicUpdateUnique[] = $combinedCourseIdAndStudentId;
            $this->totalUpdates++;
        }
        return $academicUpdate;
    }

    /**
     * Assigns a faculty to an individual academic update within a request
     *
     * @param array $faculties
     * @param Organization $organization
     * @param AcademicUpdate $academicUpdate
     * @throws SynapseValidationException
     */
    private function assignFacultyToIndividualAcademicUpdate($faculties, $organization, $academicUpdate)
    {
        if ($academicUpdate) {
            foreach ($faculties as $faculty) {
                $facultyId = $faculty['facultyId'];
                $personObject = $this->personRepository->find($facultyId);
                if (!$personObject) {
                    throw new SynapseValidationException("Person not found.");
                }
                $academicUpdateAssignedFaculty = new AcademicUpdateAssignedFaculty();
                $academicUpdateAssignedFaculty->setPersonFacultyAssigned($personObject);
                $academicUpdateAssignedFaculty->setOrg($organization);
                $academicUpdateAssignedFaculty->setAcademicUpdate($academicUpdate);
                $this->personRepository->persist($academicUpdateAssignedFaculty, false);
                $this->isAcademicUpdateCreated = true;
                $this->facultiesDetailsArray[] = $facultyId;
            }
        }
    }

    /**
     * Assigns profile items or ISPs to an academic update request
     *
     * @param array $profiles
     * @param string $profileType
     * @param AcademicUpdateRequest $academicUpdateRequest
     */
    private function assignProfileItemsToAcademicUpdateRequest($profiles, $profileType, $academicUpdateRequest)
    {
        foreach ($profiles as $profile) {
            $academicUpdateRequestMetadata = new AcademicUpdateRequestMetadata();
            $academicUpdateRequestMetadata->setOrg($academicUpdateRequest->getOrg());
            if ($profileType == 'org') {
                $academicUpdateRequestMetadata->setOrgMetadata($this->orgMetadataRepository->getOrgMetadataReferance($profile['id']));
            } else {
                $academicUpdateRequestMetadata->setEbiMetadata($this->ebiMetadataRepository->getEbiMetadataReferance($profile['id']));
            }
            $academicUpdateRequestMetadata->setSearchValue($this->getMetadataValues($profile));
            $academicUpdateRequestMetadata->setAcademicUpdateRequest($academicUpdateRequest);
            $this->ebiMetadataRepository->persist($academicUpdateRequestMetadata, false);
        }
    }

    /**
     *Create an academic update by course
     *
     * @param array $selectedCourses
     * @param Organization $organization
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param boolean $academicUpdateCreateFlag
     * @throws AcademicUpdateCreateException
     */
    private function createAcademicUpdateByCourse($selectedCourses, $organization, $academicUpdateRequest, $academicUpdateCreateFlag = true)
    {
        foreach ($selectedCourses as $selectedCourse) {
            // find out the students associated with this course
            $orgCourseObject = $this->orgCourseRepository->find($selectedCourse);
            $studentsArray = $this->orgCourseStudentRepository->getStudentsByCourse($selectedCourse);
            try {
                $this->checkArrayCount($studentsArray);
                $staffs = $this->orgCourseFacultyRepository->getFacultiesForCourse($selectedCourse);
                $this->checkArrayCount($staffs);
                if ($academicUpdateCreateFlag) {
                    $this->addSelectedCourse($academicUpdateRequest, $organization, $orgCourseObject);
                    foreach ($studentsArray as $student) {
                        $personStudent = $this->personRepository->find($student['studentId']);
                        $academicUpdate = $this->createIndividualAcademicUpdateForRequest($academicUpdateRequest, $organization, $orgCourseObject, $personStudent);
                        $this->assignFacultyToIndividualAcademicUpdate($staffs, $organization, $academicUpdate);
                    }
                } else { // Count the the number of academic updates in the request for the course
                    foreach ($studentsArray as $student) {
                        $this->checkArrayCount($staffs);
                        $this->addAcademicUpdateForRequestCount($selectedCourse, $student['studentId']);
                    }
                }
            } catch (AcademicUpdateCreateException $academicUpdateCreateException) {
                continue;
            }
        }
    }

    /**
     * Get select type - example - none, all and individual
     *
     * @param AcademicUpdateCreateDto $academicUpdateCreateDto
     * @param string $type
     * @return string
     * @deprecated  - Remove this method in favor of referencing these methods directly in other methods.
     */
    private function getSelectedType($academicUpdateCreateDto, $type)
    {
        $functionName = 'get' . ucfirst($type);
        $selectedItem = $academicUpdateCreateDto->$functionName();
        if ($selectedItem->getIsAll()) {
            $selectType = 'all';
        } else {
            $functionNameArray = [
                'students' => 'getSelectedStudentIds',
                'staff' => 'getSelectedStaffIds',
                'courses' => 'getSelectedCourseIds',
                'groups' => 'getSelectedGroupIds',
                'staticList' => 'getSelectedStaticIds'
            ];
            $functionName = $functionNameArray[$type];
            if (empty($selectedItem->$functionName())) {
                $selectType = 'none';
            } else {
                $selectType = 'individual';
            }
        }
        return $selectType;
    }

    /**
     * Gets the metadata values for the specified profile item based on its type.
     *
     * @param array $profileItem
     * @return string
     */
    private function getMetadataValues($profileItem)
    {
        $profileItemValues = '';

        $itemDataType = $profileItem['item_data_type'];
        if ($itemDataType == 'S') {
            $institutionSpecificCategoryArray = array_column($profileItem['category_type'], 'value');
            $profileItemValues .= implode(",", $institutionSpecificCategoryArray);
        } elseif ($itemDataType == 'D') {

            $profileItemValues .= $profileItem['start_date'] . "," . $profileItem['end_date'];
        } elseif ($itemDataType == 'N') {
            if ($profileItem['is_single']) {

                $profileItemValues .= $profileItem['single_value'];
            } else {

                $profileItemValues .= $profileItem['min_digits'] . "," . $profileItem['max_digits'];
            }
        } else {
            $profileItemValues = '';
        }

        return $profileItemValues;
    }
} 
