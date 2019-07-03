<?php
namespace Synapse\StaticListBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\BulkStaticListJob;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\RestBundle\Entity\TotalStudentsListDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\Impl\StudentListService;
use Synapse\StaticListBundle\Entity\OrgStaticListStudents;
use Synapse\StaticListBundle\EntityDto\StaticListDetailsDto;
use Synapse\StaticListBundle\EntityDto\StaticListDto;
use Synapse\StaticListBundle\Job\StaticListCSVJob;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\StaticListBundle\Util\Constants\StaticListConstant;

/**
 * @DI\Service("staticliststudent_service")
 */
class StaticListStudentsService extends StaticListServiceHelper
{

    const SERVICE_KEY = 'staticliststudent_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var Resque
     */
    private $resque;


    // Services

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
     * @var StudentListService
     */
    private $studentListService;


    // Repositories

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;


    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

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

    /**
     * StaticListStudentsService constructor.
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
        // Scaffolding
        // parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;
        $this->repositoryResolver = $repositoryResolver;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);


        // Services
        $this->academicYearService =  $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService =  $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->studentListService = $this->container->get(StudentListService::SERVICE_KEY);

        // Repositories
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->orgStaticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);
        $this->orgStaticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Add/Share single student with a static list
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param int $staticListId
     * @param int $studentId
     * @return array
     * @throws SynapseValidationException
     */
    public function addStudentToStaticList($organizationId, $faculty, $staticListId, $studentId)
    {
        $existingStudentArray = [];
        $staticListStudents = "";
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $staticListObject = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'person' => $faculty
        ]);
        if (!$staticListObject) {
            throw new AccessDeniedException();
        }

        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException('Organization not found.');
        }

        // Other than coordinator and faculty no one is allowed to share
        $orgPersonFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $faculty,
            'organization' => $organization
        ]);
        if (empty($orgPersonFacultyObject)) {
            throw new SynapseValidationException('You are not authorized person!.');
        }


        $orgStaticList = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'organization' => $organization,
            'person' => $faculty
        ]);
        if (empty($orgStaticList)) {
            throw new SynapseValidationException('Static List Not Found.');
        }

        $personObjectOfStudent = $this->personRepository->findOneBy([
            'id' => $studentId,
            'organization' => $organization
        ]);

        if ($personObjectOfStudent) {
            $this->checkStudentAccess($personObjectOfStudent->getId());
        }

        $orgPersonStudentObject = $this->orgPersonStudentRepository->findOneBy([
            'person' => $personObjectOfStudent,
            'organization' => $organization
        ]);

        if (empty($orgPersonStudentObject)) {
            throw new SynapseValidationException($studentId . ' - Student Not Found.');
        }
        $staticListStudentsObject = $this->orgStaticListStudentsRepository->findOneBy([
            'organization' => $organization,
            'orgStaticList' => $orgStaticList,
            'person' => $orgPersonStudentObject->getPerson()
        ]);

        if ($staticListStudentsObject) {
            throw new SynapseValidationException($orgPersonStudentObject->getPerson()->getExternalId() . ' - This Student has already been added to the Static List ' . $orgStaticList->getName(), 'You_have_already_added_the_student_to_the_Static_List.');
        }

        if (!$staticListStudentsObject) {

            $staticListStudentsObject = new OrgStaticListStudents();
            $staticListStudentsObject->setOrganization($organization);
            $staticListStudentsObject->setPerson($orgPersonStudentObject->getPerson());
            $staticListStudentsObject->setOrgStaticList($orgStaticList);
            $staticListStudents = $this->orgStaticListStudentsRepository->addStudentToStaticList($staticListStudentsObject);
            $this->orgStaticListStudentsRepository->flush();

        } else {
            $existingStudentArray[$staticListId][] = $staticListStudentsObject->getPerson()->getExternalId();
        }
        $staticListResponseArray = [$staticListStudents, $existingStudentArray];

        return $staticListResponseArray;
    }

    /**
     * Creates Bulk job to add students to a static list
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param int $staticListId
     * @param array $studentIds
     * @param string $action - "add"|"share"
     * @return bool
     * @throws SynapseValidationException
     */
    public function createBulkJobToAddStudentsToStaticList($organizationId, $faculty, $staticListId, $studentIds, $action = 'add')
    {
        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException('Organization not found.');
        }

        $orgStaticList = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'organization' => $organization,
            'person' => $faculty
        ]);
        if(empty($orgStaticList)){
            throw new SynapseValidationException("Static list id is not found");
        }

        // Other than coordinator and faculty no one is allowed to share
        $orgPersonFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $faculty,
            'organization' => $organization
        ]);
        if (empty($orgPersonFacultyObject)) {
            throw new SynapseValidationException('You are not authorized person!.');
        }

        // call job
        $job = new BulkStaticListJob();
        $jobNumber = uniqid();
        $job->args = [
            'jobNumber' => $jobNumber,
            'organizationId' => $organizationId,
            'faculty' => $faculty->getId(),
            'studentIds' => implode(",", $studentIds),
            'staticListId' => $staticListId,
            'action' => $action
        ];
        $this->resque->enqueue($job, true);

        return true;
    }

    /**
     * Add Students to Static List
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param int $staticListId
     * @param string $studentIds
     * @return boolean
     */
    public function addStudentsToStaticList($organizationId, $faculty, $staticListId, $studentIds)
    {
        $studentsAddedCount = 0;
        $studentIds = explode(',', $studentIds);
        $organization = $this->organizationRepository->find($organizationId);

        $orgStaticList = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'organization' => $organization,
            'person' => $faculty
        ]);

        foreach ($studentIds as $studentId) {
            $personObjectOfStudent = $this->personRepository->findOneBy([
                'id' => $studentId,
                'organization' => $organization
            ]);

            if ($personObjectOfStudent) {
                if (!$this->rbacManager->checkAccessToStudent($studentId)) {
                    // if this process is being ran in a job and
                    // you are trying to get a student you don't
                    // have access to into your static list, then
                    // silently fail
                    continue;
                }
            }

            $orgPersonStudentObject = $this->orgPersonStudentRepository->findOneBy([
                'person' => $personObjectOfStudent,
                'organization' => $organization
            ]);
            if (empty($orgPersonStudentObject)) {
                // Skip if student not found
                continue;
            }

            $staticListStudentsObject = $this->orgStaticListStudentsRepository->findOneBy([
                'organization' => $organization,
                'orgStaticList' => $orgStaticList,
                'person' => $orgPersonStudentObject->getPerson()
            ]);

            if (!$staticListStudentsObject) {

                $staticListStudentsObject = new OrgStaticListStudents();
                $staticListStudentsObject->setOrganization($organization);
                $staticListStudentsObject->setPerson($orgPersonStudentObject->getPerson());
                $staticListStudentsObject->setOrgStaticList($orgStaticList);
                $this->orgStaticListStudentsRepository->addStudentToStaticList($staticListStudentsObject);
                $studentsAddedCount++;
            }
        }
        // After finishing bulk action send notification to logged in person
        if ( $studentsAddedCount > 0) {
            $this->alertNotificationsService->createNotification('bulk-action-completed', $studentsAddedCount . ' students have been added to the static list ' . $orgStaticList->getName(), $faculty, null, null, null, null, null, null, null, null, null, null, null, $staticListStudentsObject);
        }

        return true;
    }

    /**
     * Remove a student from static list
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param int $staticListId
     * @param int $studentId
     * @return array
     * @throws SynapseValidationException
     */
    public function removeStudentFromStaticList($organizationId, $faculty, $staticListId, $studentId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $staticListObject = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'person' => $faculty
        ]);
        if (!$staticListObject) {
            throw new AccessDeniedException();
        }

        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException('Organization not found.');
        }

        // Other than coordinator and faculty no one is allowed to share
        $orgPersonFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $faculty,
            'organization' => $organization
        ]);
        if (empty($orgPersonFacultyObject)) {
            throw new SynapseValidationException('You are not authorized person!.');
        }


        $orgStaticList = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'organization' => $organization,
            'person' => $faculty
        ]);
        if (empty($orgStaticList)) {
            throw new SynapseValidationException('Static List Not Found.');
        }

        $personStudentObject = $this->personRepository->findOneBy([
            'id' => $studentId,
            'organization' => $organization
        ]);

        if ($personStudentObject) {
            $this->checkStudentAccess($personStudentObject->getId());
        }

        $orgPersonStudentObject = $this->orgPersonStudentRepository->findOneBy([
            'person' => $personStudentObject,
            'organization' => $organization
        ]);
        if (empty($orgPersonStudentObject)) {
            throw new SynapseValidationException('Student Not Found');
        }
        $staticListStudentsObject = $this->orgStaticListStudentsRepository->findOneBy([
            'organization' => $organization,
            'orgStaticList' => $orgStaticList,
            'person' => $orgPersonStudentObject->getPerson()
        ]);

        if (empty($staticListStudentsObject)) {
            throw new SynapseValidationException("Record doesn't exist");
        }
        $staticListStudents = $this->orgStaticListStudentsRepository->delete($staticListStudentsObject);
        $staticListResponseArray = [$staticListStudents];
        return $staticListResponseArray;

    }

    /**
     * Creates Bulk Job to remove students from static list
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param int $staticListId
     * @param array $studentIds
     * @param string $action - "remove"
     * @throws SynapseValidationException
     * @return bool
     */
    public function createBulkJobToRemoveStudentsFromStaticList($organizationId, $faculty, $staticListId, $studentIds, $action = 'remove')
    {
        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException('Organization not found.');
        }

        $orgStaticList = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'organization' => $organization,
            'person' => $faculty
        ]);
        if (empty($orgStaticList)) {
            throw new SynapseValidationException('Static list id not found.');
        }

        // Other than coordinator and faculty no one is allowed to share
        $orgPersonFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $faculty,
            'organization' => $organization
        ]);
        if (empty($orgPersonFacultyObject)) {
            throw new SynapseValidationException('You are not authorized person!.');
        }

        $job = new BulkStaticListJob();
        $jobNumber = uniqid();
        $job->args = [
            'jobNumber' => $jobNumber,
            'organizationId' => $organizationId,
            'faculty' => $faculty->getId(),
            'studentIds' => implode(",", $studentIds),
            'staticListId' => $staticListId,
            'action' => $action
        ];
        $this->resque->enqueue($job, true);

        return true;

    }

    /**
     * Removes students from static list
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param int $staticListId
     * @param string $studentIds
     * @return boolean
     */
    public function removeStudentsFromStaticList($organizationId, $faculty, $staticListId, $studentIds)
    {
        $studentsRemovedCount = 0;
        $studentIds = explode(',', $studentIds);
        $organization = $this->organizationRepository->find($organizationId);

        $orgStaticList = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'organization' => $organization,
            'person' => $faculty
        ]);

        foreach ($studentIds as $studentId) {
            $personObjectOfStudent = $this->personRepository->findOneBy([
                'id' => $studentId,
                'organization' => $organization
            ]);

            if ($personObjectOfStudent) {
                if (!$this->rbacManager->checkAccessToStudent($studentId)) {
                    // if this process is being ran in a job and
                    // you are trying to get a student you don't
                    // have access to into your static list, then
                    // silently fail
                    continue;
                }
            }

            $orgPersonStudentObject = $this->orgPersonStudentRepository->findOneBy([
                'person' => $personObjectOfStudent,
                'organization' => $organization
            ]);

            if (empty($orgPersonStudentObject)) {
                // Skip if student not found
                continue;
            }

            $staticListStudentsObject = $this->orgStaticListStudentsRepository->findOneBy([
                'organization' => $organization,
                'orgStaticList' => $orgStaticList,
                'person' => $orgPersonStudentObject->getPerson()
            ]);

            if (empty($staticListStudentsObject)) {
                // Skip if record doesn't exist
                continue;
            }
            
            $this->orgStaticListStudentsRepository->delete($staticListStudentsObject);
            $studentsRemovedCount++;
        }
        // After finishing bulk action send notification to logged in person
        if ( $studentsRemovedCount > 0) {
            $this->alertNotificationsService->createNotification('bulk-action-completed', $studentsRemovedCount . ' students have been removed from the static list ' . $orgStaticList->getName(), $faculty, null, null, null, null, null, null, null, null, null, null, null, $staticListStudentsObject);
        }
        return true;

    }


    /**
     * get static list details w.r.t static list id
     *
     * @param integer $staticListId
     * @param Person $loggedInUser
     * @param integer $pageNumber
     * @param integer $recordsPerPage
     * @param string $sortBy
     * @param bool $isCSV
     * @param bool $isJob
     * @return string | PersonDTO
     * @throws AccessDeniedException
     */
    public function viewStaticListDetails($staticListId, $loggedInUser, $pageNumber, $recordsPerPage, $sortBy = '', $isCSV = false, $isJob = false)
    {
        $personDto = new PersonDTO();
        $organization = $loggedInUser->getOrganization();
        $organizationId = $organization->getId();

        $staticListObject = $this->orgStaticListRepository->findOneBy([
            'id' => $staticListId,
            'person' => $loggedInUser
        ]);
        $loggedInUserId = $loggedInUser->getId();

        if ($staticListObject) {
            if ($isCSV) {
                // export CSV job Implementation
                return $this->generateCSVForStaticList($organizationId, $staticListId, $loggedInUserId);
            }
            // Get Pagination Details
            $pageNumber = (int)$pageNumber;
            if (!$pageNumber) {
                $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
            }
            $recordsPerPage = (int)$recordsPerPage;
            if (!$recordsPerPage) {
                $recordsPerPage = SynapseConstant::DEFAULT_RECORD_COUNT;
            }

            $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
            $currentOrgAcademicYearId = $currentAcademicYear['org_academic_year_id'];

            $studentIds = $this->orgStaticListStudentsRepository->getStaticListStudents($staticListId, $loggedInUserId, $currentOrgAcademicYearId);
            $studentIds = array_column($studentIds, 'student_id');

            if ($isJob) {
                $recordsPerPage = count($studentIds); //  if its a job we should be fetching all the students
                $pageNumber = 1; //  the page number would be 1 as we are getting all the students.
            }
            $studentsDetail = $this->studentListService->getStudentListWithMetadata($studentIds, $loggedInUserId, $organizationId, $sortBy, $pageNumber, $recordsPerPage);

            $totalStudents = $studentsDetail['total_records'];

            $riskCountRows = $this->personRepository->getAggregateRiskCountsWithPermissionCheck($studentIds, $loggedInUserId);


            if (!empty($riskCountRows) && $totalStudents > 0) {
                $finalRiskArray = [];
                $finalRiskCount = 0;
                foreach ($riskCountRows AS $riskRow) {
                    $finalRiskArray[$riskRow['risk_text']] = (int)$riskRow['color_count'];
                    $finalRiskCount += (int)$riskRow['color_count'];
                }
                $red1 = round(($finalRiskArray['red'] / $finalRiskCount) * 100, 2);
                $red2 = round(($finalRiskArray['red2'] / $finalRiskCount) * 100, 2);
                $yellow = round(($finalRiskArray['yellow'] / $finalRiskCount) * 100, 2);
                $green = round(($finalRiskArray['green'] / $finalRiskCount) * 100, 2);
                $gray = round(($finalRiskArray['gray'] / $finalRiskCount) * 100, 2);
                $personDto->setRed1($red1);
                $personDto->setRed2($red2);
                $personDto->setYellow($yellow);
                $personDto->setGreen($green);
                $personDto->setGray($gray);
            } else {
                $personDto->setRed1(0);
                $personDto->setRed2(0);
                $personDto->setYellow(0);
                $personDto->setGreen(0);
                $personDto->setGray(0);
            }


            $staticListStudentDtoArray = [];
            foreach ($studentsDetail['search_result'] as $studentDetail) {
                $totalStudentsListDto = new TotalStudentsListDto();
                $totalStudentsListDto->setStudentId($studentDetail['student_id']);
                $totalStudentsListDto->setStudentFirstName($studentDetail['student_first_name']);
                $totalStudentsListDto->setStudentLastName($studentDetail['student_last_name']);
                $totalStudentsListDto->setExternalId($studentDetail['external_id']);
                $totalStudentsListDto->setStudentRiskStatus($studentDetail['student_risk_status']);
                $totalStudentsListDto->setStudentRiskImageName($studentDetail['student_risk_image_name']);
                $totalStudentsListDto->setStudentIntentToLeaveText($studentDetail['student_intent_to_leave']);
                $totalStudentsListDto->setStudentClasslevel($studentDetail['student_classlevel']);
                $totalStudentsListDto->setStudentLogins($studentDetail['student_logins']);
                if (isset($studentDetail['last_activity']) && trim($studentDetail['last_activity']) != "") {
                    $lastActivity = $studentDetail['last_activity_date'] . " - " . $studentDetail['last_activity'];
                } else {
                    $lastActivity = "";
                }
                $totalStudentsListDto->setLastActivity($lastActivity);
                $totalStudentsListDto->setStudentIntentToLeaveImageName($studentDetail['student_intent_to_leave_image_name']);
                $totalStudentsListDto->setStudentStatus($studentDetail['student_status']);
                $totalStudentsListDto->setPrimaryEmail($studentDetail['student_primary_email']);

                $staticListStudentDtoArray[] = $totalStudentsListDto;
            }


            //returns all risk level in array
            $personDto->setStaticlistName($staticListObject->getName());
            $personDto->setStaticlistDescription($staticListObject->getDescription());
            $personDto->setTotalStudentsList($staticListStudentDtoArray);
            $personDto->setTotalStudents($studentsDetail['total_records']);
            $personDto->setTotalRecords($studentsDetail['total_records']);
            $personDto->setTotalPages($studentsDetail['total_pages']);
            $personDto->setRecordsPerPage($recordsPerPage);
            $personDto->setCurrentPage($pageNumber);

        } else {
            throw new AccessDeniedException();
        }
        return $personDto;
    }

    /**
     * Method to initiate job to generate csv for static list
     *
     * @param int $organizationId
     * @param int $staticListId
     * @param int $loggedInUserId
     * @return string
     */
    private function generateCSVForStaticList($organizationId, $staticListId, $loggedInUserId)
    {
        $currentDateTime = new \DateTime('now');
        $currentDateTimeObject = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $currentDateTime);
        $currentDateTime = $currentDateTimeObject->format(SynapseConstant::DATETIME_FORMAT_CSV);

        // Creates the job for CSV generation
        $jobNumber = uniqid();
        $job = new StaticListCSVJob();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'staticListId' => $staticListId,
            'loggedUserId' => $loggedInUserId,
            'orgId' => $organizationId,
            'currentDateTime' => $currentDateTime
        );

        // Puts the created job in job-queue
        $this->resque->enqueue($job, true);
        return SynapseConstant::DOWNLOAD_IN_PROGRESS_MESSAGE;
    }

    /**
     * Add students to static List.
     *
     * @param int $organizationId
     * @param int $studentId
     * @param array $staticLists
     * @param Person $faculty
     * @return StaticListDetailsDto
     * @throws SynapseValidationException
     */
     public function addStudentToStaticLists($organizationId, $studentId, $staticLists, $faculty)
    {
        $staticListDetailsDto = new StaticListDetailsDto();
        $staticListArray = [];

        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException("Organization Not Found.");
        }
        $listIds = [];
        $existingStudentsArray = array();
        if ($staticLists) {
            foreach ($staticLists as $key => $staticListId) {
                $staticListId = $staticListId['static_list_id'];
                $staticListDto = new StaticListDto();
                $staticListDto->setStaticlistId($staticListId);
                $staticListArray[] = $staticListDto;
                list($data, $existingStudents) = $this->addStudentToStaticList($organization->getId(), $faculty, $staticListId, $studentId);
                $existingStudentsArray[] = $existingStudents;
            }
            foreach ($existingStudentsArray as $existingStudent) {
                foreach ($existingStudent as $staticKey => $student) {
                    $listIds[] = $staticKey;
                }
            }
            $listIds = implode(',', $listIds);
            if (!empty($listIds)) {
                throw new SynapseValidationException("This Student has already been added to the Static List ");
            }
        }
        $staticListDetailsDto->setStudentId($studentId);
        $staticListDetailsDto->setStaticLists($staticListArray);
        return $staticListDetailsDto;
    }

    /**
     * Removes students from static Lists.
     *
     * @param StaticListDto $staticListDto
     * @param Person $faculty
     */
    public function removeStudentsFromStaticLists($staticListDto, $faculty)
    {
        $organizationId = $staticListDto->getOrganizationId();
        $this->rbacManager->checkAccessToOrganization($organizationId);
        // Other than coordinator and faculty/staff no one is allowed to remove
        $personInstance = $this->orgPersonFacultyRepository->findOneBy(array(
            StaticListConstant::PERSON => $faculty,
            StaticListConstant::ORGN => $organizationId
        ));
        (!$personInstance) ? $this->justThrow(StaticListConstant::NOT_AUTH_PERSON, StaticListConstant::NOT_AUTH_PERSON_KEY) : "";

        $staticListId = $staticListDto->getStaticlistId();
        $organization = $this->organizationRepository->findOneById($organizationId);
        $this->isObjectExist($organization, StaticListConstant::ORG_NOT_FOUND, StaticListConstant::ORG_NOT_FOUND_KEY);

        $existingStaticListInstance = $this->orgStaticListRepository->findOneBy(array(
            'id' => $staticListId,
            StaticListConstant::PERSON => $faculty
        ));
        $this->isObjectExist($existingStaticListInstance, StaticListConstant::STATICLIST_NOT_FOUND, StaticListConstant::STATICLIST_NOT_FOUND_KEY);
        foreach ($staticListDto->getStaticListDetails() as $staticListDetails) {
            $this->checkStudentAccess($staticListDetails['student_id']);
            $existingStaticListStudentInstance = $this->orgStaticListStudentsRepository->findOneBy([
                StaticListConstant::PERSON => $staticListDetails['student_id'],
                StaticListConstant::ORGN => $organization,
                'orgStaticList' => $existingStaticListInstance
            ]);

            if (isset($existingStaticListStudentInstance)) {
                $this->orgStaticListStudentsRepository->delete($existingStaticListStudentInstance);

            } else {
                //continue;
                throw new AccessDeniedException();
            }
        }
    }

    public function checkStudentAccess($studentId)
    {
        $hasStudentAccess = $this->rbacManager->checkAccessToStudent($studentId);
        if (!$hasStudentAccess) {
            throw new AccessDeniedException('You do not have access to this student:' . $studentId);
        }
    }

    /**
     * Add/Remove students in static List.
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param array $students
     * @param int $staticListId
     * @param string $actionType
     * @return StaticListDetailsDto
     * @throws SynapseValidationException
     */
    public function updateStudentsInStaticList($organizationId, $faculty, $students, $staticListId, $actionType = 'remove')
    {
        $staticListDetailsDto = new StaticListDetailsDto();

        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException("Organization Not Found.");
        }

        if ($students) {
            foreach ($students as $key => $studentId) {
                $studentId = $studentId['student_id'];
                $staticListDto = new StaticListDto();
                $staticListDto->setStudentId($studentId);
                $studentsArray[] = $staticListDto;

                // This makes sure all students are attempted to be
                // added/removed, even if the user does not access
                // to one or more the the students.
                try {
                    if( $actionType != 'remove' ) {
                        $this->addStudentToStaticList($organizationId, $faculty, $staticListId,$studentId);
                    } else{
                        $this->removeStudentFromStaticList($organizationId, $faculty, $staticListId,$studentId);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $staticListDetailsDto->setStaticListId($studentId);
        $staticListDetailsDto->setStudentDetails($studentsArray);
        return $staticListDetailsDto;
    }

    /**
     * Get count of static list students based on $staticListId and $organizationId
     *
     * @param int $staticListId
     * @param int $organizationId
     * @return array
     * @throws SynapseValidationException
     */
    public function getStaticListStudentsCount($staticListId, $organizationId)
    {
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            return $this->orgStaticListStudentsRepository->getStaticListStudentsCount($staticListId, $currentAcademicYear['org_academic_year_id']);
        } else {
            throw new SynapseValidationException("Academic year is not active");
        }
    }

    /**
     * Build TotalStudentsListDTO
     *
     * @param array $row
     * @param array $searchKeys
     * @return TotalStudentsListDto
     */
    public function buildTotalStudentsListDto(Array $row, $searchKeys)
    {
        $totalStudentsListDto = new TotalStudentsListDto();
        $totalStudentsListDto->setStudentId($row['id']);
        $totalStudentsListDto->setStudentFirstName($row['firstname']);
        $totalStudentsListDto->setStudentLastName($row['lastname']);
        $totalStudentsListDto->setExternalId($row['external_id']);
        $totalStudentsListDto->setStudentRiskStatus($row['risk_text']);
        $riskLevel = (isset($row['risk_level'])) ? $row['risk_level'] : "";
        $totalStudentsListDto->setStudentRiskLevel($riskLevel);
        $intentToLeave = (isset($row['intent_text'])) ? $row['intent_text'] : "";
        $totalStudentsListDto->setStudentIntentToLeaveText($intentToLeave);

        $classLevel = '';
        if ($row['class_level']) {
            $classLevel = $searchKeys['[CLASS_LEVELS]'][$row['class_level']];
            if (!isset($classLevel)) {
                $classLevel = '';
            }
        }

        $totalStudentsListDto->setStudentClasslevel($classLevel);

        $loginCount = (isset($row['login_cnt'])) ? $row['login_cnt'] : "";
        $totalStudentsListDto->setStudentLogins($loginCount);
        $lastActivity = (isset($row['last_activity'])) ? $row['last_activity'] : "";
        $totalStudentsListDto->setLastActivity($lastActivity);
        $riskImageName = (isset($row['risk_imagename'])) ? $row['risk_imagename'] : "risk-level-icon-gray.png";
        $totalStudentsListDto->setStudentRiskImageName($riskImageName);
        $riskStatus = (isset($row['risk_text'])) ? $row['risk_text'] : "gray";
        $totalStudentsListDto->setStudentRiskStatus($riskStatus);
        $intentToLeaveImageName = (isset($row['risk_imagename'])) ? $row['risk_imagename'] : "";
        $totalStudentsListDto->setStudentIntentToLeaveImageName($intentToLeaveImageName);
        $studentStatusObject = $this->orgPersonStudentRepository->findOneBy(['person' => $row['id']]);

        $studentStatus = $studentStatusObject->getStatus();
        $totalStudentsListDto->setStudentStatus($studentStatus);
        $totalStudentsListDto->setPrimaryEmail($row['primary_email']);

        (!empty($row['primary_campus_conn'])) ? $totalStudentsListDto->setHasPrimaryConnection(true) : $totalStudentsListDto->setHasPrimaryConnection(false);
        return $totalStudentsListDto;
    }
}
