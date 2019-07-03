<?php
namespace Synapse\StaticListBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\StaticListBundle\Entity\OrgStaticList;
use Synapse\StaticListBundle\EntityDto\StaticListDetailsDto;
use Synapse\StaticListBundle\EntityDto\StaticListDto;
use Synapse\StaticListBundle\EntityDto\StaticListsResponseDto;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\StaticListBundle\Service\StaticListInterface;
use Synapse\StaticListBundle\Util\Constants\StaticListConstant;

/**
 * @DI\Service("staticlist_service")
 */
class StaticListService extends StaticListServiceHelper
{
    const SERVICE_KEY = 'staticlist_service';

    const PAGE_NO = 1;

    const OFFSET = 25;

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     *
     * @var Logger
     */
    private $logger;

    /**
     * @var Manager
     */
    public $rbacManager;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

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
     * @var StaticListStudentsService
     */
    private $staticListStudentsService;


    // Repositories

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgStaticListStudentsRepository
     */
    private $orgStaticListStudentsRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var OrgStaticListRepository
     */
    private $staticListRepository;

    /**
     * @var OrgStaticListStudentsRepository
     */
    private $staticListStudentsRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $studentRepository;

    /**
     * StaticListService Constructor
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
        $this->container = $container;
        $this->logger = $logger;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->repositoryResolver = $repositoryResolver;

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->staticListStudentsService = $this->container->get(StaticListStudentsService::SERVICE_KEY);

        // Repositories
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgStaticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->staticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);
        $this->staticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);
        $this->studentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);

    }

    public function createStaticList($orgId, $faculty, $name = null, $description = '')
    {
        $organization = $this->organizationRepository->findOneById($orgId);
        $this->isObjectExist($organization, StaticListConstant::ORGANIZATION_NOT_FOUND, StaticListConstant::ORGANIZATION_NOT_FOUND_KEY);

        // Block if the person of student type
        $personObj = $this->orgPersonFacultyRepository->findOneBy(array(
            StaticListConstant::PERSON => $faculty,
            StaticListConstant::ORGN => $organization
        ));

        is_null($personObj) ? $this->justThrow(StaticListConstant::NOT_AUTH_PERSON, StaticListConstant::NOT_AUTH_PERSON_KEY) : '';
        $this->checkEmpty($name);
        $this->checkLimitOnlyMax($name, 120, StaticListConstant::STATICLIST_120_CHARS_VALIDATION, StaticListConstant::STATICLIST_120_CHARS_VALIDATION_KEY);
        $this->checkLimitOnlyMax($description, 350, StaticListConstant::STATICLIST_DESC_VALIDATION, StaticListConstant::STATICLIST_DESC_VALIDATION_KEY);

        $staticListEntity = "";
        $staticListEntity = $this->staticListRepository->findOneBy(array(
            'name' => $name,
            StaticListConstant::ORGN => $organization,
            StaticListConstant::PERSON => $faculty
        ));
        ($staticListEntity) ? $this->justThrow(StaticListConstant::STATICLIST_DUPLICATE_VALIDATE, StaticListConstant::STATICLIST_DUPLICATE_VALIDATE_KEY) : "";

        $staticListObj = new OrgStaticList();
        $staticListObj->setOrganization($organization);
        $staticListObj->setPerson($faculty);
        $staticListObj->setName($name);
        $staticListObj->setDescription($description);
        $staticListObj->setCreatedBy($faculty);
        $staticListInstance = $this->staticListRepository->createStaticList($staticListObj);

        $StaticList = new StaticListDto();
        $staticlist_id = ($staticListEntity) ? $staticListEntity->getId() : $staticListInstance->getId();
        $StaticList->setStaticlistId($staticlist_id);
        $StaticList->setStaticlistName($staticListInstance->getName());
        $StaticList->setStaticlistDescription($staticListInstance->getDescription());
        $StaticList->setCreatedAt($staticListInstance->getCreatedAt());
        $StaticList->setModifiedAt($staticListInstance->getModifiedAt());
        $StaticList->setPersonId($staticListInstance->getPerson()
            ->getId());
        // $this->logger->info(" Static list Created ");
        return $StaticList;
    }

    public function updateStaticList($orgId, $faculty, $staticlist_id = null, $name, $description)
    {
        $organization = $this->organizationRepository->findOneById($orgId);
        $this->isObjectExist($organization, StaticListConstant::ORGANIZATION_NOT_FOUND, StaticListConstant::ORGANIZATION_NOT_FOUND_KEY);

        $personObj = $this->orgPersonFacultyRepository->findOneBy(array(
            StaticListConstant::PERSON => $faculty,
            StaticListConstant::ORGN => $organization
        ));
        (!$personObj) ? $this->justThrow(StaticListConstant::NOT_AUTH_PERSON, StaticListConstant::NOT_AUTH_PERSON_KEY) : "";

        $staticListObj = $this->staticListRepository->findOneBy([
            'id' => $staticlist_id
        ]);
        $this->isObjectExist($staticListObj, StaticListConstant::STATICLIST_NOT_FOUND, StaticListConstant::STATICLIST_NOT_FOUND_KEY);

        $staticListExist = $this->staticListRepository->findOneBy([
            'id' => $staticlist_id,
            StaticListConstant::PERSON => $faculty
        ]);

        if (!$staticListExist) {
            throw new AccessDeniedException();
        }

        $this->checkEmpty($name);
        $this->checkLimitOnlyMax($name, 120, StaticListConstant::STATICLIST_120_CHARS_VALIDATION, StaticListConstant::STATICLIST_120_CHARS_VALIDATION_KEY);
        $this->checkLimitOnlyMax($description, 350, StaticListConstant::STATICLIST_DESC_VALIDATION, StaticListConstant::STATICLIST_DESC_VALIDATION_KEY);

        $staticListEntity = "";
        $staticListEntity = $this->staticListRepository->findOneBy(array(
            StaticListConstant::NAME => $name,
            StaticListConstant::PERSON => $faculty
        ));
        ($staticListEntity && $staticListEntity->getId() != $staticlist_id) ? $this->justThrow(StaticListConstant::STATICLIST_DUPLICATE_VALIDATE, StaticListConstant::STATICLIST_DUPLICATE_VALIDATE_KEY) : "";

        $staticListObj->setOrganization($organization);
        $staticListObj->setPerson($faculty);
        (strlen(trim($name)) > 0) ? $staticListObj->setName($name) : "";
        (strlen(trim($description)) > 0) ? $staticListObj->setDescription($description) : $staticListObj->setDescription("");
        $staticListObj->setModifiedBy($faculty);
        $staticListInstance = $this->staticListRepository->update($staticListObj);

        $StaticList = new StaticListDto();
        $StaticList->setStaticlistId($staticListInstance->getId());
        $StaticList->setStaticlistName($staticListInstance->getName());
        $StaticList->setStaticlistDescription($staticListInstance->getDescription());
        $StaticList->setCreatedAt($staticListInstance->getCreatedAt());
        $StaticList->setModifiedAt($staticListInstance->getModifiedAt());
        $StaticList->setPersonId($staticListInstance->getPerson()
            ->getId());

        return $StaticList;
    }


    public function deleteStaticList($orgId, $faculty, $staticListId)
    {
        $this->logger->debug(" Delete Static List for Organization Id" . $orgId . "StaticList Id" . $staticListId);
        $staticListExist = $this->staticListRepository->findOneBy([
            'id' => $staticListId,
            StaticListConstant::PERSON => $faculty
        ]);

        if (!$staticListExist) {
            throw new AccessDeniedException();
        }

        $organization = $this->organizationRepository->findOneById($orgId);
        $this->isObjectExist($organization, StaticListConstant::ORG_NOT_FOUND, StaticListConstant::ORG_NOT_FOUND_KEY);

        $personObj = $this->orgPersonFacultyRepository->findOneBy(array(
            StaticListConstant::PERSON => $faculty,
            StaticListConstant::ORGN => $organization
        ));
        (!$personObj) ? $this->justThrow(StaticListConstant::NOT_AUTH_PERSON, StaticListConstant::NOT_AUTH_PERSON_KEY) : "";

        $staticListObj = $this->staticListRepository->findOneBy([
            'id' => $staticListId,
            StaticListConstant::ORGN => $organization,
            StaticListConstant::PERSON => $faculty
        ]);
        $this->isObjectExist($staticListObj, StaticListConstant::STATICLIST_NOT_FOUND, StaticListConstant::STATICLIST_NOT_FOUND_KEY);

        $this->staticListRepository->remove($staticListObj);
        $this->staticListRepository->flush();
    }

    /**
     * Listing of all static list by organization student
     *
     * @param int $organizationId
     * @param Person $faculty
     * @param integer|null $studentId
     * @param integer|null $pageNumber
     * @param integer|null $recordsPerPage
     * @param string|null $sortBy
     * @return string|StaticListsResponseDto
     * @throws SynapseValidationException
     */
    public function listAllStaticLists($organizationId, $faculty, $studentId = null, $pageNumber = null, $recordsPerPage = null, $sortBy = '')
    {
        if ($studentId) {
            //check for non-participant student permissions
            $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);
        }
        $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);

        $facultyId = $faculty->getId();

        // Block if the person of student type
        $orgPersonFacultyObject = $this->orgPersonFacultyRepository->findOneBy(array(
            'person' => $faculty,
            'organization' => $organizationId
        ));

        if (empty($orgPersonFacultyObject)) {
            throw new SynapseValidationException('You are not authorized to view the list of static lists.');
        }

        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        $recordsPerPage = (int)$recordsPerPage;
        if (!$recordsPerPage) {
            $recordsPerPage = SynapseConstant::DEFAULT_RECORD_COUNT;
        }

        $offset = ($pageNumber * $recordsPerPage) - $recordsPerPage;

        if ($studentId) {
            // Get all the static lists for the faculty that the student is associated with
            $staticListArray = $this->staticListRepository->getStaticListsWithStudentId($facultyId, $studentId, $sortBy, $recordsPerPage, $offset, $currentOrgAcademicYearId);
            $staticListCount = $this->staticListRepository->getCountOfStaticListsWithStudentID($facultyId, $studentId, $organizationId);

        } else {
            // Get all the static list  for the faculty
            $staticListArray = $this->staticListRepository->getStaticListsForFaculty($facultyId, $sortBy, $recordsPerPage, $offset, $currentOrgAcademicYearId);
            $staticListCount = $this->staticListRepository->getCountOfStaticListsForFaculty($facultyId, $organizationId);
        }
        $totalPageCount = ceil($staticListCount / $recordsPerPage);

        $staticListDetails = array();

        $staticListsResponse = new StaticListsResponseDto();
        $staticListsResponse->setTotalRecords($staticListCount);
        $staticListsResponse->setTotalPages($totalPageCount);
        $staticListsResponse->setRecordsPerPage($recordsPerPage);
        $staticListsResponse->setCurrentPage($pageNumber);
        if (count($staticListArray) > 0) {
            foreach ($staticListArray as $staticListData) {
                $staticList = new StaticListDetailsDto();
                $staticListId = $staticListData['id'];
                $staticList->setStaticlistId($staticListId);
                $createdByName = $staticListData['created_by_firstname'] . " " . $staticListData['created_by_lastname'];
                $staticList->setCreatedBy($staticListData['created_by_person_id']);
                $staticList->setCreatedByUserName($createdByName);
                $staticList->setStaticlistName($staticListData['name']);
                $staticList->setStaticlistDescription($staticListData['description']);
                $createdAt = date(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE, strtotime($staticListData['created_at']));
                $modifiedAt = date(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE, strtotime($staticListData['modified_at']));
                $staticList->setCreatedAt($createdAt);
                $staticList->setModifiedAt($modifiedAt);
                $modifiedByName = $staticListData['modified_by_firstname'] . " " . $staticListData['modified_by_lastname'];
                $staticList->setModifiedByUserName($modifiedByName);
                $studentCount = $staticListData['student_count'];
                $staticList->setStudentCount($studentCount);

                $staticListDetails[] = $staticList;
            }
            $staticListsResponse->setStaticlistDetails($staticListDetails);
        }
        return $staticListsResponse;
    }

    /**
     * Shares a static list with the specified faculty
     *
     * @param int $organizationId
     * @param int $staticListSharedWithPersonId
     * @param int $staticListShareId
     * @param Person $loggedInFaculty
     * @return StaticListDto
     * @throws SynapseValidationException
     */
    public function shareStaticList($organizationId, $staticListSharedWithPersonId, $staticListShareId, $loggedInFaculty)
    {
        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new SynapseValidationException('Organization Not Found.');
        }

        $staticListSharedWithPersonObject = $this->personRepository->findOneBy([
            'id' => $staticListSharedWithPersonId,
            'organization' => $organizationId
        ]);
        if (!$staticListSharedWithPersonObject) {
            throw new SynapseValidationException('The person with which the static list is being shared was not found.');
        }

        $personFacultyObject = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $loggedInFaculty,
            'organization' => $organization
        ]);
        if (!$personFacultyObject) {
            throw new SynapseValidationException('Faculty sharing the static list not found.');
        }

        $shareStaticList = $this->staticListRepository->findOneBy([
            'id' => $staticListShareId,
            'person' => $loggedInFaculty,
            'organization' => $organization
        ]);
        if (!$shareStaticList) {
            throw new SynapseValidationException('Static list not found.');
        }

        $sharedStaticListObject = $this->staticListRepository->findOneBy([
            'name' => $shareStaticList->getName(),
            'person' => $staticListSharedWithPersonObject,
            'personIdSharedBy' => $loggedInFaculty,
            'organization' => $organization
        ]);
        if ($sharedStaticListObject) {
            throw new SynapseValidationException('You have already shared this Static List.');
        }

        $name = $loggedInFaculty->getFirstname() . " " . $loggedInFaculty->getLastname();
        $username = $loggedInFaculty->getUsername();
        $event = 'StaticList_Shared';
        $reason = 'Shared static list from ' . $name;

        $name = $shareStaticList->getName();
        $description = $shareStaticList->getDescription();
        $sharedOn = $this->dateUtilityService->getTimezoneAdjustedCurrentDateTimeForOrganization($organizationId);

        $orgStaticListObject = new OrgStaticList();
        $orgStaticListObject->setOrganization($organization);
        $orgStaticListObject->setPerson($staticListSharedWithPersonObject);
        $orgStaticListObject->setName($name);
        $orgStaticListObject->setDescription($description);
        $orgStaticListObject->setPersonIdSharedBy($loggedInFaculty->getId());
        $orgStaticListObject->setSharedOn($sharedOn);
        $orgStaticListObject->setCreatedBy($loggedInFaculty);
        $orgStaticListObject = $this->staticListRepository->createStaticList($orgStaticListObject);
        $this->alertNotificationsService->createNotification($event, $reason, $staticListSharedWithPersonObject, null, null, null, null, null, $orgStaticListObject);

        $staticListStudentsObjectArray = $this->orgStaticListStudentsRepository->findBy([
            'orgStaticList' => $shareStaticList,
            'organization' => $organization
        ]);

        $studentIds = [];
        $staticListId = $orgStaticListObject->getId();

        // Build a list of student IDs to pass to the add function
        foreach ($staticListStudentsObjectArray as $staticListStudentsObject) {
            $studentIds[] = $staticListStudentsObject->getPerson()->getId();
        }

        //If there are students in the static list, add those students to the shared static list
        if (!empty($staticListStudentsObjectArray)) {
            if (count($studentIds) > 1) {
                // Share bulk students into static list
                $this->staticListStudentsService->createBulkJobToAddStudentsToStaticList($organizationId, $staticListSharedWithPersonObject, $staticListId, $studentIds, 'share');
            } else {
                // Share single student into static list
                $this->staticListStudentsService->addStudentToStaticList($organizationId, $staticListSharedWithPersonObject, $staticListId, $studentIds);
            }
        }

        // Prepare the response
        $staticListDtoObject = new StaticListDto();
        $staticListDtoObject->setOrganizationId($organization->getId());
        $staticListDtoObject->setPersonId($staticListSharedWithPersonObject->getId());
        $staticListDtoObject->setStaticlistId($staticListId);
        $staticListDtoObject->setSharedPersonName($name);
        $staticListDtoObject->setSharedPersonEmail($username);
        $staticListDtoObject->setSharedOn($sharedOn);
        $staticListDtoObject->setModifiedAt($orgStaticListObject->getModifiedAt());

        $staticListModifiedBy = $orgStaticListObject->getModifiedBy();
        if ($staticListModifiedBy) {
            $modifiedBy = $staticListModifiedBy->getFirstname() . " " . $staticListModifiedBy->getLastname();
        } else {
            $modifiedBy = "";
        }

        $staticListDtoObject->setModifiedBy($modifiedBy);
        return $staticListDtoObject;
    }

}
