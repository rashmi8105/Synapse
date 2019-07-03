<?php
namespace Synapse\GroupBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Entity\OrgGroupFaculty;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Entity\PermissionSet;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\GroupService as CoreBundleGroupService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\GroupConstant;
use Synapse\GroupBundle\DTO\GroupFacultyDTO;
use Synapse\GroupBundle\DTO\GroupFacultyListInputDTO;
use Synapse\GroupBundle\DTO\GroupInputDTO;
use Synapse\GroupBundle\DTO\GroupListDto;
use Synapse\GroupBundle\DTO\GroupPersonInputDto;
use Synapse\GroupBundle\DTO\GroupStudentDto;
use Synapse\UploadBundle\Service\Impl\GroupUploadService;


/**
 * @TODO : This class should replace the current Group service. When all methods have been moved out of the old group service class, change the class key here to "group_service".
 * @DI\Service("new_group_service")
 */
class GroupService extends AbstractService
{

    const SERVICE_KEY = 'new_group_service';


    private $jsonEntityMapFromCreateGroups = [
        'groupName' => "group_name",
        'externalId' => "external_id",
    ];

    private $jsonEntityFieldFromCreateGroupFaculty = [
        'orgGroup' => "group_id",
        'person' => "faculty_id",
        'permissionsetName' => "permissionset_name",
        'isInvisible' => "is_invisible"
    ];

    /**
     * This variable is used for the entity field and the json attribute mapping.This is needed when the doctrine entity is validated.
     *
     * @var array
     */
    private $jsonEntityFieldMap = [
        'orgGroup' => 'group_external_id',
        'person' => 'person_external_id'
    ];

    //scaffolding

    /**
     * @var Container
     */
    private $container;

    //Repositories
    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    //Services
    /**
     * @var APIValidationService
     */
    private $apiValidationService;

    /**
     * @var CoreBundleGroupService
     */
    private $coreBundleGroupService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    /**
     * @var GroupUploadService
     */
    private $groupUploadService;

    /**
     * @var IDConversionService
     */
    private $idConversionService;

    /**
     * Group Service Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
     *            })
     *
     * @param $repositoryResolver
     * @param $container
     * @param $logger
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;

        //Repositories
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

        //Services
        $this->apiValidationService = $this->container->get(APIValidationService::SERVICE_KEY);
        $this->coreBundleGroupService = $this->container->get(CoreBundleGroupService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->entityValidationService = $this->container->get(EntityValidationService::SERVICE_KEY);
        $this->groupUploadService = $this->container->get(GroupUploadService::SERVICE_KEY);
        $this->idConversionService = $this->container->get(IDConversionService::SERVICE_KEY);
    }

    /**
     * Gets the list of group faculties
     *
     * @param int $organizationId
     * @param OrgGroup $groupObject
     * @param $isInternal
     * @return GroupFacultyDTO
     */
    public function getFacultyByGroup($organizationId, $groupObject, $isInternal)
    {
        $groupFacultyList = $this->orgGroupFacultyRepository->getGroupStaffList($organizationId, $groupObject->getId(), $isInternal);

        $groupFacultyDTO = new GroupFacultyDTO();
        $groupFacultyDTO->setGroupExternalId($groupObject->getExternalId());
        $groupFacultyDTO->setGroupName($groupObject->getGroupName());
        $groupFacultyDTO->setFacultyList($groupFacultyList);
        return $groupFacultyDTO;
    }

    /**
     * Gets the list of groups based on search text
     *
     * @param integer $organizationId
     * @param  string|null $searchText
     * @param  integer|null $pageNumber
     * @param  integer|null $recordsPerPage
     * @return GroupListDto
     */
    public function getGroupsByOrganization($organizationId, $searchText = null, $pageNumber = null, $recordsPerPage = null)
    {

        $groupCountArray = $this->orgGroupRepository->fetchOrgGroupTotalCount($organizationId, $searchText);
        $groupCount = (int)$groupCountArray['total_groups'];

        if (empty($pageNumber)) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        if (empty($recordsPerPage)) {
            $recordsPerPage = $groupCount;
        } else {
            $recordsPerPage = (int)$recordsPerPage;
        }
        $totalPages = ceil($groupCount / $recordsPerPage);
        $offset = ($pageNumber * $recordsPerPage) - $recordsPerPage;

        $groupList = $this->orgGroupRepository->getGroupsMetaData($organizationId, $searchText, $offset, $recordsPerPage);

        $groupListDto = new GroupListDto();
        $groupListDto->setTotalPages($totalPages);
        $groupListDto->setRecordsPerPage($recordsPerPage);
        $groupListDto->setCurrentPage($pageNumber);
        $groupListDto->setTotalRecords($groupCount);
        $groupListDto->setGroupList($groupList);

        return $groupListDto;
    }


    /**
     * Adds the students from  groups specified
     *
     * @param GroupPersonInputDto $groupPersonInputDto
     * @param int $organizationId
     * @return array
     */
    public function addStudentsToGroup($groupPersonInputDto, $organizationId)
    {

        $groupStudentsArray = $groupPersonInputDto->getGroupPersonList();
        $error = [];
        $createdRecords = [];

        foreach ($groupStudentsArray as $groupStudent) {
            try {
                $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
                $groupExternalId = $groupStudent['group_external_id'];
                $personExternalId = $groupStudent['person_external_id'];

                $dataProcessingExceptionHandler->addErrors("Not a Valid GroupId", "group_external_id");
                $orgGroupEntity = $this->orgGroupRepository->findOneBy([
                    'externalId' => $groupExternalId,
                    'organization' => $organizationId
                ], null, $dataProcessingExceptionHandler);

                $dataProcessingExceptionHandler->resetAllErrors();

                $studentInternalIdArray = $this->idConversionService->convertPersonIds($personExternalId, $organizationId, false);
                $studentInternalId = current($studentInternalIdArray);

                $dataProcessingExceptionHandler->addErrors("Not a Valid Student", "person_external_id");
                $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy([
                    'person' => $studentInternalId,
                    'organization' => $organizationId
                ], $dataProcessingExceptionHandler);

                $dataProcessingExceptionHandler->resetAllErrors();


                $personEntity = $orgPersonStudent->getPerson();
                $organizationEntity = $personEntity->getOrganization();
                $groupStudentEntity = new OrgGroupStudents();
                $groupStudentEntity->setOrgGroup($orgGroupEntity);
                $groupStudentEntity->setPerson($personEntity);
                $groupStudentEntity->setOrganization($organizationEntity);

                $this->entityValidationService->validateDoctrineEntity($groupStudentEntity, $dataProcessingExceptionHandler, "required", true);

                $this->orgGroupStudentsRepository->persist($groupStudentEntity);
                $createdRecords[] = $groupStudent;

            } catch (DataProcessingExceptionHandler $dpeh) {
                $errorArray = current($dpeh->getAllErrors());
                $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $dpeh->getAllErrors());
                $error[] = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($groupStudent, $errorArray, $this->jsonEntityFieldMap);
            }
        }
        return [
            'data' => [
                'created_count' => count($createdRecords),
                'created_records' => $createdRecords,
            ],
            'error' => $error
        ];
    }

    /**
     * Fetch list of students for a group
     *
     * @param OrgGroup $groupObject
     * @return GroupStudentDto
     */
    public function fetchStudentsForGroup($groupObject)
    {
        $groupId = $groupObject->getId();
        $groupName = $groupObject->getGroupName();
        $groupExternalId = $groupObject->getExternalId();
        $organizationId = $groupObject->getOrganization()->getId();
        $studentIds = $this->orgGroupStudentsRepository->listExternalIdsForStudentsInGroup($organizationId, $groupId, true);
        $groupPersonDto = new GroupStudentDto();
        $groupPersonDto->setGroupExternalId($groupExternalId);
        $groupPersonDto->setGroupName($groupName);
        $groupPersonDto->setStudentList($studentIds);
        return $groupPersonDto;
    }

    /*
     * Removing a student from specified Group
     *
     * @param string $groupExternalId
     * @param string $studentExternalId
     * @param integer $organizationId
     * @throws SynapseValidationException
     */
    public function deleteStudentFromGroup($groupExternalId, $studentExternalId, $organizationId)
    {

        $validationError = [];

        if ($groupExternalId == GroupConstant::SYS_GROUP_EXTERNAL_ID) {
            $validationError[] = "Student can not be removed from 'ALL Students' Group";
        } else {
            //checking for valid group
            $orgGroupEntity = $this->orgGroupRepository->findOneBy([
                'externalId' => $groupExternalId,
                'organization' => $organizationId
            ]);

            if ($orgGroupEntity) {

                $studentInternalIdArray = $this->idConversionService->convertPersonIds($studentExternalId, $organizationId, false);
                $studentInternalId = current($studentInternalIdArray);

                //checking for valid student
                $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy([
                    'person' => $studentInternalId,
                    'organization' => $organizationId
                ]);

                if (!$orgPersonStudent) {
                    $validationError[] = "Student is not valid for current organization";

                } else {
                    $personEntity = $orgPersonStudent->getPerson();
                    $groupStudentEntity = $this->orgGroupStudentsRepository->findOneBy([
                        'person' => $personEntity,
                        'orgGroup' => $orgGroupEntity
                    ]);

                    if ($groupStudentEntity) {
                        $this->orgGroupStudentsRepository->delete($groupStudentEntity);
                        $this->groupUploadService->updateDataFile($organizationId);
                    } else {
                        $validationError[] = "Student is not associated with the group";
                    }
                }

            } else {
                $validationError[] = "Group is not valid for current organization";
            }
        }
        if (count($validationError) > 0) {
            $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $validationError);
            throw new SynapseValidationException($validationError[0]);
        }
    }


    /**
     * Delete a faculty from a group
     *
     * @param int $personId
     * @param int $groupId
     * @param int $organizationId
     * @throws SynapseValidationException
     * @return bool
     */
    public function deleteFacultyFromGroup($personId, $groupId, $organizationId)
    {
        $groupFacultyObject = $this->orgGroupFacultyRepository->findOneBy([
            'person' => $personId,
            'orgGroup' => $groupId
        ]);

        if ($groupFacultyObject) {
            $this->orgGroupFacultyRepository->delete($groupFacultyObject);
        } else {
            throw new SynapseValidationException('Faculty is not a member of the group');
        }
        $this->coreBundleGroupService->startUpdateSubGroupList($organizationId);
        return true;
    }

    /**
     * Creates Groups
     *
     * @param GroupInputDTO $groupInputDto
     * @param Organization $organization
     * @return array
     */
    public function createGroups($groupInputDto, $organization)
    {

        $groupList = $groupInputDto->getGroupList();
        $createdGroupArray = [];
        $error = [];
        foreach ($groupList as $group) {

            $externalId = $group->getExternalId();
            $groupName = $group->getGroupName();

            try {
                $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
                // creating an entity
                $orgGroup = new OrgGroup();
                $orgGroup->setOrganization($organization);
                $orgGroup->setExternalId($externalId);
                $orgGroup->setGroupName($groupName);
                $this->entityValidationService->validateDoctrineEntity($orgGroup, $dataProcessingExceptionHandler, null, true);
                $this->orgGroupRepository->persist($orgGroup);
                $group->setMapworksInternalId($orgGroup->getId());
                $createdGroupArray[] = $group;
            } catch (DataProcessingExceptionHandler $dpeh) {

                $errorList = $dpeh->getAllErrors();
                $groupArray['external_id'] = $externalId;
                $groupArray['group_name'] = $groupName;
                $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organization->getId(), $errorList);
                $error[] = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($groupArray, current($errorList), $this->jsonEntityMapFromCreateGroups);

            }
        }
        return [
            'data' => [
                'created_count' => count($createdGroupArray),
                'created_records' => $createdGroupArray
            ],
            'errors' => $error
        ];
    }

    /**
     * Adds a faculties to groups
     *
     * @param int $organizationId
     * @param GroupFacultyListInputDTO $groupFacultyListInputDTO
     * @return array
     */
    public function addFacultiesToGroups($organizationId, $groupFacultyListInputDTO)
    {
        $error = [];
        $createdRecords = [];
        $groupFacultyList = $groupFacultyListInputDTO->getGroupFacultyList();
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($groupFacultyList as $groupFacultyDTO) {
            $dataProcessingExceptionHandler->resetAllErrors();
            try {
                $groupExternalId = $groupFacultyDTO->getGroupId();
                $facultyExternalId = $groupFacultyDTO->getFacultyId();
                $isInvisible = boolval($groupFacultyDTO->getIsInvisible());
                $permissionsetName = $groupFacultyDTO->getPermissionsetName();
                //checking for valid Group
                $dataProcessingExceptionHandler->addErrors("Not a Valid GroupId", "group_id", "required");
                $orgGroupEntity = $this->orgGroupRepository->findOneBy([
                    'externalId' => $groupExternalId,
                    'organization' => $organizationId
                ], null, $dataProcessingExceptionHandler);
                $dataProcessingExceptionHandler->resetAllErrors();
                //checking for valid Person
                $dataProcessingExceptionHandler->addErrors("Not a Valid Person", "faculty_id", "required");
                $orgPerson = $this->personRepository->findOneBy([
                    'externalId' => $facultyExternalId,
                    'organization' => $organizationId
                ], $dataProcessingExceptionHandler, null);
                $dataProcessingExceptionHandler->resetAllErrors();
                //checking for valid Faculty
                $dataProcessingExceptionHandler->addErrors("Not a Valid FacultyID", "faculty_id", "required");
                $orgPersonFaculty = $this->orgPersonFacultyRepository->findOneBy([
                    'person' => $orgPerson,
                    'organization' => $organizationId
                ], $dataProcessingExceptionHandler, null);
                $dataProcessingExceptionHandler->resetAllErrors();

                // if the faculty is  inactive , we should not allow it to be added to the group
                if ($orgPersonFaculty->getStatus() == 0) {
                    $dataProcessingExceptionHandler->addErrors("Faculty Id is not active at the organization", "faculty_id", "required");
                    throw $dataProcessingExceptionHandler;
                }

                //checking for valid permissionset
                $orgPermissionsetObject = $this->orgPermissionsetRepository->findOneBy([
                    'organization' => $organizationId,
                    'permissionsetName' => $permissionsetName
                ]);
                //create new orgGroupFaculty entity and set its value
                $this->createGroupFaculty($orgPersonFaculty, $orgGroupEntity, $orgPermissionsetObject, $isInvisible, $dataProcessingExceptionHandler);
                $createdRecords[] = $groupFacultyDTO;
                $this->coreBundleGroupService->startUpdateSubGroupList($organizationId);
                if (!$orgPermissionsetObject && ($permissionsetName!= null)) {
                    $dataProcessingExceptionHandler->addErrors("Permissionset Name is not valid", "permissionset_name");
                    throw $dataProcessingExceptionHandler;
                }
            } catch (DataProcessingExceptionHandler $dpeh) {
                $groupFaculty['group_id'] = $groupFacultyDTO->getGroupId();
                $groupFaculty['faculty_id'] = $groupFacultyDTO->getFacultyId();
                $groupFaculty['permissionset_name'] = $groupFacultyDTO->getPermissionsetName();
                $groupFaculty['is_invisible'] = $groupFacultyDTO->getIsInvisible();
                $errorArray = current($dpeh->getAllErrors());

                if ($errorArray['type'] == 'required') {
                    $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $dpeh->getAllErrors());
                }
                $error[] = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($groupFaculty, $errorArray, $this->jsonEntityFieldFromCreateGroupFaculty);
            }
        }
        return [
            'data' => [
                'created_count' => count($createdRecords),
                'created_records' => $createdRecords,
            ],
            'errors' => [
                'error_count' => count($error),
                'error_records' => $error
            ]
        ];
    }
    /**
     * Creates Group Faculty entity
     *
     * @param OrgPersonFaculty $orgPersonFaculty
     * @param OrgGroup $orgGroupEntity
     * @param PermissionSet $permissionsetObject
     * @param bool $isInvisible
     * @param DataProcessingExceptionHandler $dataProcessingExceptionHandler
     */
    private function createGroupFaculty($orgPersonFaculty, $orgGroupEntity, $permissionsetObject, $isInvisible, $dataProcessingExceptionHandler)
    {
        $personEntity = $orgPersonFaculty->getPerson();
        $organizationEntity = $orgPersonFaculty->getOrganization();
        $groupFacultyEntity = new OrgGroupFaculty();
        $groupFacultyEntity->setOrgGroup($orgGroupEntity);
        $groupFacultyEntity->setPerson($personEntity);
        $groupFacultyEntity->setOrganization($organizationEntity);
        $groupFacultyEntity->setOrgPermissionset($permissionsetObject);
        $groupFacultyEntity->setIsInvisible($isInvisible);
        $this->entityValidationService->validateDoctrineEntity($groupFacultyEntity, $dataProcessingExceptionHandler, "required", true);
        $this->orgGroupFacultyRepository->persist($groupFacultyEntity);
    }

    /**
     * Update Groups
     *
     * @param GroupInputDTO $groupInputDTO
     * @param Organization $organization
     * @return array
     */
    public function updateGroups($groupInputDTO, $organization)
    {
        $updatedGroupArray = [];
        $skippedGroupArray = [];
        $error = [];
        $groupList = $groupInputDTO->getGroupList();
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($groupList as $orgGroupDTO) {
            $dataProcessingExceptionHandler->resetAllErrors();
            $groupName = $orgGroupDTO->getGroupName();
            $groupExternalId = $orgGroupDTO->getExternalId();
            try {
                $errorMessage = isset($groupExternalId) ? "The group ID provided is an invalid group ID." : "Group ID cannot be empty.";
                $dataProcessingExceptionHandler->addErrors($errorMessage, "external_id");
                //checking for valid group
                $orgGroupEntity = $this->orgGroupRepository->findOneBy([
                    'externalId' => $groupExternalId,
                    'organization' => $organization
                ], null, $dataProcessingExceptionHandler);
                $dataProcessingExceptionHandler->resetAllErrors();
                //skipping if group name is same in database
                if ($orgGroupEntity->getGroupName() == $groupName) {
                    $skippedGroupArray[] = $orgGroupDTO;
                } else {
                    //setting group name in group entity
                    $orgGroupEntity->setGroupName($groupName);
                    //validating group entity
                    $this->entityValidationService->validateDoctrineEntity($orgGroupEntity, $dataProcessingExceptionHandler, null, true);
                    $this->orgGroupRepository->persist($orgGroupEntity);
                    $this->coreBundleGroupService->startUpdateSubGroupList($organization->getId());
                    $orgGroupDTO->setMapworksInternalId($orgGroupEntity->getId());
                    $updatedGroupArray[] = $orgGroupDTO;
                }
            } catch (DataProcessingExceptionHandler $dpeh) {
                $groupArray['group_name'] = $orgGroupDTO->getGroupName();
                $groupArray['external_id'] = $orgGroupDTO->getExternalId();
                $errorList = $dpeh->getAllErrors();
                $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organization->getId(), $errorList);
                $error[] = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($groupArray, current($errorList), $this->jsonEntityMapFromCreateGroups);
            }
        }
        return [
            'data' => [
                'updated_count' => count($updatedGroupArray),
                'updated_records' => $updatedGroupArray,
                'skipped_count' => count($skippedGroupArray),
                'skipped_records' => $skippedGroupArray
            ],
            'errors' => [
                'error_count' => count($error),
                'error_records' => $error
            ]
        ];
    }
}