<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestGroupRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Entity\OrgGroupFaculty;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgGroupTreeRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\GroupConstant;
use Synapse\RestBundle\Entity\OrgGroupDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\EntityDto\GroupsArrayDto;
use Synapse\SearchBundle\EntityDto\GroupsDto;
use Synapse\UploadBundle\Job\CreateSubGroupListJob;

/**
 * @DI\Service("group_service")
 */
class GroupService extends GroupHelperService
{

    const SERVICE_KEY = 'group_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var DI\Validator
     */
    private $validator;

    //Services
    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var PermissionSetService
     */
    private $permissionsetService;

    /**
     *
     * @var PersonService
     */
    private $personService;

    //Repositories
    /**
     * @var AcademicUpdateRequestGroupRepository
     */
    private $academicUpdateRequestGroupRepository;

    /**
     *
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     *
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     *
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * GroupService Construct
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

        // Scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);

        //services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->permissionsetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);

        // repositories
        $this->academicUpdateRequestGroupRepository = $this->repositoryResolver->getRepository(AcademicUpdateRequestGroupRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgGroupTreeRepository = $this->repositoryResolver->getRepository(OrgGroupTreeRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository =  $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
    }

    /**
     * Creates Group.
     *
     * @param OrgGroupDto $orgGroupDto
     * @return OrgGroupDto
     */
    public function createGroup(OrgGroupDto $orgGroupDto)
    {
        $logContent = $this->loggerHelperService->getLog($orgGroupDto);
        $this->logger->debug(" Creating Group " . $logContent);

        $groupName = $orgGroupDto->getGroupName();

        $this->rbacManager->checkAccessToOrganization($orgGroupDto->getOrganizationId());

        $orgId = $orgGroupDto->getOrganizationId();

        // throw validation error if group name already exists in organization
        $this->validateGroupNameIsNotDuplicate($groupName, $orgId);

        $parentGroupId = is_null($orgGroupDto->getParentGroupId()) ? 0 : $orgGroupDto->getParentGroupId();
        $organization = $this->organizationService->find($orgId);


        // If the parent group ID is greater than 0, then check if it exists
        $parentGroup = $this->assertValidParentGroup($parentGroupId);

        // Setting values to OrgGroup
        $this->systemGroupCheck($groupName);
        $orgGroup = new OrgGroup();
        $orgGroup->setGroupName($groupName);
        $orgGroup->setOrganization($organization);
        if ($parentGroup) {
            $orgGroup->setParentGroup($parentGroup);
        }
        $orgGroup->setExternalId($orgGroupDto->getExternalId());

        $errors = $this->validator->validate($orgGroup);
        $this->validateOrgGroup($errors);

        // pass to persist and call createGroup()
        $this->orgGroupRepository->createGroup($orgGroup);

        // Assign staff to group
        $staffLists = $orgGroupDto->getStaffList();

        $staffListIds = $this->getStaffListId($staffLists, $orgGroup, $organization);
        $this->orgGroupRepository->flush();
        foreach ($staffListIds as $key => $val) {
            $staffLists[$key][GroupConstant::GROUP_STAFFID] = $val->getId();
        }
        $orgGroupDto->setStaffList($staffLists);
        $orgGroupDto->setGroupId($orgGroup->getId());
        $this->startUpdateSubGroupList($orgGroupDto->getOrganizationId());
        $this->logger->info(">>>> Create Group");
        return $orgGroupDto;
    }

    /**
     *  Check parent group ID exists.
     *
     * @param int $parentGroupId
     * @return OrgGroup
     * @throws ValidationException
     */
    private function assertValidParentGroup($parentGroupId)
    {
        $parentGroup = null;
        if ($parentGroupId > 0) {
            $parentGroup = $this->orgGroupRepository->find($parentGroupId);
            if (! isset($parentGroup)) {
                throw new ValidationException([
                    'Parent Group Not Found.'
                ], 'Parent Group Not Found.', 'parent_group_not_found');
            }
        }
        return $parentGroup;
    }

    /**
     * Returns the Group Staff Ids created.
     *
     * @param array $staffLists
     * @param OrgGroup $orgGroup
     * @param Organization $organization
     * @throws SynapseValidationException
     * @return array
     */
    private function getStaffListId($staffLists, $orgGroup, $organization)
    {
        $staffListIds = [];
        if (count($staffLists) > 0) {
            foreach ($staffLists as $staffList) {
                $orgGroupFaculty = new OrgGroupFaculty();
                $staffId = isset($staffList['staff_id']) ? $staffList['staff_id'] : 0;
                $orgPermissionSetId = isset($staffList['staff_permissionset_id']) ? $staffList['staff_permissionset_id'] : 0;
                $isInvisible = isset($staffList['staff_is_invisible']) ? $staffList['staff_is_invisible'] : 0;

                $facultyValidationException =  new SynapseValidationException("$staffId Invalid Faculty for the organization");
                $personFacultyObject = $this->orgPersonFacultyRepository->findOneBy(['person' => $staffId], $facultyValidationException);
                $person =  $personFacultyObject->getPerson();
                $facultyStatus = $personFacultyObject->getStatus();
                if($facultyStatus == 0){
                    throw  new SynapseValidationException("$staffId faculty is inactive at the organization");
                }

                if ($orgPermissionSetId > 0) {
                    $orgPermissionSetObject = $this->permissionsetService->find($orgPermissionSetId);
                    $orgGroupFaculty->setOrgPermissionset($orgPermissionSetObject);
                }

                $orgGroupFaculty->setPerson($person);
                $orgGroupFaculty->setOrgGroup($orgGroup);
                $orgGroupFaculty->setOrganization($organization);

                $orgGroupFaculty->setIsInvisible($isInvisible);
                $staffListIds[] = $this->orgGroupFacultyRepository->createGroupFaculty($orgGroupFaculty);
            }
        }
        return $staffListIds;
    }

    /**
     * Returns all sub groups with respect to the group id.
     *
     * @param int $orgId
     * @param int $groupId
     * @param array $treeGroupResult
     * @return array
     */
    public function getAllSubGroups($orgId, $groupId, $treeGroupResult)
    {
        $this->logger->debug(">>>> Get All sub Groups for OrganizationdId" . $orgId . "Group Id" . $groupId );

        $group = $this->orgGroupRepository->getGroupDetails($orgId, $groupId);
        if (! isset($group)) {
            throw new ValidationException([
                GroupConstant::ERROR_GROUP_NOT_FOUND
            ], GroupConstant::ERROR_GROUP_NOT_FOUND, GroupConstant::ERROR_GROUP_NOT_FOUND_KEY);
        }

        $subGroups = [];
        if (count($treeGroupResult) > 0) {
            $groupsArray = array();
            foreach ($treeGroupResult as $groupResult) {
                $groupArray = array();
                $groupArray[GroupConstant::GROUP_ID] = $groupResult[GroupConstant::GROUP_ID];

                $groupArray[GroupConstant::PARENT_ID] = is_null($groupResult[GroupConstant::PARENT_ID]) ? 0 : $groupResult[GroupConstant::PARENT_ID];
                $groupArray[GroupConstant::GROUP_NAME] = $groupResult[GroupConstant::GROUP_NAME];

                array_push($groupsArray, $groupArray);
            }
            $subGroups = $this->buildTree($groupsArray, $group->getId());
        }

        $responseArray = array();
        $responseArray[GroupConstant::ORGANIZATION_ID] = $orgId;
        $responseArray[GroupConstant::GROUP_ID] = $group->getId();
        $responseArray[GroupConstant::GROUP_NAME] = $group->getGroupName();

        $responseArray[GroupConstant::SUBGROUPS] = $subGroups;
        $this->logger->info(">>>> Get All sub Groups for OrganizationdId");
        return $responseArray;
    }

    /**
     * When passing in $organization ID, validates that the group exists, and is a member of the given organization.
     * Otherwise, validates that the current group ID exists.
     *
     * @param int $groupId
     * @param int|null $organizationId
     * @throws SynapseValidationException
     * @return OrgGroup
     */
    private function validateGroup($groupId, $organizationId = null)
    {
        if ($organizationId) {
            $group = $this->orgGroupRepository->findOneBy(array(
                "organization" => $organizationId,
                "id" => $groupId
            ));
        } else {
            $group = $this->orgGroupRepository->find($groupId);
        }

        if (!isset($group)) {
            throw new SynapseValidationException('Group Not Found.');
        }
        return $group;
    }

    /**
     * Get group details by organizationID and groupID
     *
     * @param int $organizationId
     * @param int $groupId
     * @return array
     */
    public function getGroupById($organizationId, $groupId)
    {
        $startTime = $this->milliseconds();
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $this->logger->debug(">>>> Get Group By Id for Organization Id" . $organizationId . "Group Id" . $groupId);
        $organization = $this->organizationService->find($organizationId);
        $this->validateGroup($groupId, $organization);

        $groupInfo = $this->orgGroupRepository->fetchGroupInfo($organizationId, $groupId);

        $responseArray = array();
        $responseArray['organization_id'] = $organizationId;
        $responseArray['group_id'] = $groupId;
        $responseArray['group_name'] = $groupInfo['group_name'];
        $responseArray['external_id'] = is_null($groupInfo['external_id']) ? "" : $groupInfo['external_id'];
        $responseArray['parent_group_id'] = ($groupInfo['parent_id']) ? $groupInfo['parent_id'] : 0;
        $responseArray['parent_group_name'] = ($groupInfo['parent_name']) ? $groupInfo['parent_name'] : "";
        $responseArray['students_count'] = (int)$this->orgGroupStudentsRepository->countStudentsForGroup($organizationId, $groupId);
        $responseArray['staff_list'] = $this->orgGroupFacultyRepository->getGroupStaffList($organizationId, $groupId);

        $tempStaffListArray = array();
        foreach ($responseArray['staff_list'] as $staffList) {
            $staffList['staff_is_invisible'] = filter_var($staffList['staff_is_invisible'], FILTER_VALIDATE_BOOLEAN);
            $tempStaffListArray[] = $staffList;
        }

        $responseArray['staff_list'] = $tempStaffListArray;
        $responseArray['subgroups'] = [];
        $responseArray['time_taken'] = $this->milliseconds() - $startTime;

        $this->logger->info(">>>> Get Group By Id for Organization Id");

        return $responseArray;
    }

    /**
     * Deletes a group
     *
     * @param int $organizationId
     * @param int $orgGroupId
     * @param bool $isInternal
     * @return int
     * @throws SynapseValidationException
     */
    public function deleteGroup($organizationId, $orgGroupId, $isInternal = true)
    {
        if($isInternal) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }

        // check AU referenced
        $groupExists = $this->academicUpdateRequestGroupRepository->isAUExistsForGroup($orgGroupId);
        if (count($groupExists) > 0) {
            throw new SynapseValidationException('Academic Update attached with this group.');
        }

        //check for subgroups exist
        $parentGroupWithSubgroup = $this->orgGroupTreeRepository->findAllDescendantGroups($orgGroupId);

        if (count($parentGroupWithSubgroup) > 1) { // count > 1 as first element of the array will always be the parent group itself
            throw new SynapseValidationException('Group can’t be deleted because it has subgroups below it.');
        }else{
            $this->deleteGroupStaffStudent($orgGroupId);
        }

        $this->startUpdateSubGroupList($organizationId);

        return $orgGroupId;
    }

    /**
     * removes the group as well as the faculty-group students-group relationship with the group
     *
     * @param integer $groupId
     * @return void
     */
    private function deleteGroupStaffStudent($groupId)
    {
        $group = $this->orgGroupRepository->find($groupId);
        if ($group) {
            if($group->getExternalId() == GroupConstant::SYS_GROUP_EXTERNAL_ID){
                throw new SynapseValidationException('ALL Student Group can not be deleted.');
            }

            $allStaff = $this->orgGroupFacultyRepository->findBy(array(
                'orgGroup' => $group
            ));
            if ($allStaff) {
                $this->removeStaffStudent($allStaff, 'staff');
            }

            $allStudent = $this->orgGroupStudentsRepository->findBy(array(
                'orgGroup' => $group
            ));
            if ($allStudent) {
                $this->removeStaffStudent($allStudent, 'student');
            }
            $this->orgGroupRepository->delete($group); //  Removing the  group will also update the org_group_tree table
        }

    }

    /**
     * Remove staff and students from the groups.
     *
     * @param array $allPerson
     * @param string $personType
     */
    private function removeStaffStudent($allPerson, $personType)
    {
        if ($personType == GroupConstant::STAFF) {
            $orgPersonGroupRepository = $this->orgGroupFacultyRepository;
        } else {
            $orgPersonGroupRepository = $this->orgGroupStudentsRepository;
        }
        foreach ($allPerson as $person) {
            $orgPersonGroupRepository->remove($person);
        }
    }

    private function milliseconds() {

    	$mt = explode(' ', microtime());
    	return $mt[1] * 1000 + round($mt[0] * 1000);
    }

    /**
     * Get List of groups based on organization. If rootGroupsOnly is true, returns only groups that have no parent
     *
     * @param int $organizationId
     * @param bool $rootGroupsOnly
     * @return array
     */
    public function getGroupList($organizationId, $rootGroupsOnly = false)
    {
        $startTime = $this->milliseconds();

        $groupList = $this->orgGroupRepository->fetchListOfGroups($organizationId, $rootGroupsOnly);

        // Gets the total group count.
        $groupsTotalCount = $this->orgGroupRepository->fetchOrgGroupTotalCount($organizationId);
        // Gets the group summary last modified date.
        $groupSummaryLastModifiedDate = $this->orgGroupRepository->fetchGroupSummaryLastModifiedDate($organizationId);

        $responseArray = array();
        $responseArray['organization_id'] = $organizationId;
        $responseArray['time_taken'] = $this->milliseconds() - $startTime;

        if($groupsTotalCount != null){
            $responseArray['total_groups'] = $groupsTotalCount['total_groups'];
        }else{
            $responseArray['total_groups'] = '';
        }

        if(!empty($groupSummaryLastModifiedDate['last_updated'])){
            $responseArray['last_updated'] = new \DateTime($groupSummaryLastModifiedDate['last_updated'], new \DateTimeZone('UTC'));
        }else{
            $responseArray['last_updated'] = '';
        }

        $responseArray['groups'] =  $groupList;

        return $responseArray;
    }

    /**
     *  Edit Group Details.
     * @deprecated  "Group creation is being consolidated within the GroupBundle. Please look there for this functionality"
     *
     * @param OrgGroupDto $orgGroupDto
     * @return OrgGroupDto
     */
    public function editGroup(OrgGroupDto $orgGroupDto)
    {
        $logContent = $this->loggerHelperService->getLog($orgGroupDto);
        $this->logger->debug("Editing Group " . $logContent);

        $groupName = $orgGroupDto->getGroupName();
        $orgId = $orgGroupDto->getOrganizationId();

        $this->rbacManager->checkAccessToOrganization($orgId);

        $organization = $this->organizationService->find($orgId);

        $groupId = is_null($orgGroupDto->getGroupId()) ? 0 : $orgGroupDto->getGroupId();

        // check if group exists or not
        $orgGroup = $this->validateGroup($groupId);

        // edit the group
        $oldGroupName = $orgGroup->getGroupName();
        if ($oldGroupName != $groupName) {

            $this->validateGroupNameIsNotDuplicate($groupName, $orgId);
            $this->systemGroupCheck($groupName);
        }

        $orgGroup->setGroupName($groupName);
        $orgGroup->setExternalId($orgGroupDto->getExternalId());
        $errors = $this->validator->validate($orgGroup);
        $this->validateOrgGroup($errors);
        $staffLists = $orgGroupDto->getStaffList();

        if (count($staffLists) > 0) {
            foreach ($staffLists as $staffList) {

                $staffIsRemove = (int)isset($staffList['staff_is_remove']) ? $staffList['staff_is_remove'] : 0;
                $groupStaffId = (int)isset($staffList[GroupConstant::GROUP_STAFFID]) ? $staffList[GroupConstant::GROUP_STAFFID] : 0;
                $orgPermissionSetId = isset($staffList[GroupConstant::STAFF_PERMISSIONSET_ID]) ? $staffList[GroupConstant::STAFF_PERMISSIONSET_ID] : 0;

                // Remove staff from group
                if ($staffIsRemove == 1) {
                    $orgGroupFaculty = $this->orgGroupFacultyRepository->find($groupStaffId);
                    $this->validateEmpty($orgGroupFaculty, GroupConstant::ERROR_PERSON_NOT_FOUND, GroupConstant::ERROR_PERSON_NOT_FOUND_KEY);
                    $this->orgGroupFacultyRepository->remove($orgGroupFaculty);
                } else {
                    $this->createUpdateStaff($groupStaffId, $staffList, $orgPermissionSetId, $orgGroup, $organization);
                }
            }
        }

        $this->orgGroupRepository->flush();
        $this->startUpdateSubGroupList($orgGroupDto->getOrganizationId());
        $this->logger->info(">>>> Edit Group ");
        return $orgGroupDto;
    }

    /**
     * @param int $groupStaffId
     * @param array $staffList
     * @param int $orgPermissionSetId
     * @param OrgGroup $orgGroup
     * @param Organization $organization
     * @throws SynapseValidationException
     */
    private function createUpdateStaff($groupStaffId, $staffList, $orgPermissionSetId, $orgGroup, $organization)
    {
        $orgGroupFacultyId = isset($staffList['staff_id']) ? $staffList['staff_id'] : 0;
        $facultyValidationException =  new SynapseValidationException("$orgGroupFacultyId Invalid Faculty for the organization");
        $personFacultyObject = $this->orgPersonFacultyRepository->findOneBy(['person' => $orgGroupFacultyId], $facultyValidationException);
        $person =  $personFacultyObject->getPerson();
        $facultyStatus = $personFacultyObject->getStatus();
        if($facultyStatus == 0){
            throw  new SynapseValidationException("$orgGroupFacultyId faculty is inactive at the organization");
        }

        $isInvisible = isset($staffList['staff_is_invisible']) ? $staffList['staff_is_invisible'] : 0;
        if ($groupStaffId > 0) {
            // Update Staff
            $orgGroupFaculty = $this->orgGroupFacultyRepository->find($groupStaffId);
            $this->validateEmpty($orgGroupFaculty, 'Person Not Found.', 'Person Not Found.');

            if ($orgPermissionSetId > 0) {
                $orgPermissionSetObject = $this->permissionsetService->find($orgPermissionSetId);
                $orgGroupFaculty->setOrgPermissionset($orgPermissionSetObject);
            } else {
                $orgGroupFaculty->setOrgPermissionset(NULL);
            }

            $orgGroupFaculty->setPerson($person);
            $orgGroupFaculty->setOrgGroup($orgGroup);
            $orgGroupFaculty->setIsInvisible($isInvisible);
        } else {
            // Create Staff
            $orgGroupFaculty = new OrgGroupFaculty();
            $orgPermissionSetId = isset($staffList['staff_permissionset_id']) ? $staffList['staff_permissionset_id'] : 0;
            if ($orgPermissionSetId > 0) {
                $orgPermissionSetObject = $this->permissionsetService->find($orgPermissionSetId);
                $orgGroupFaculty->setOrgPermissionset($orgPermissionSetObject);
            }

            $orgGroupFaculty->setPerson($person);
            $orgGroupFaculty->setOrgGroup($orgGroup);
            $orgGroupFaculty->setOrganization($organization);
            $orgGroupFaculty->setIsInvisible($isInvisible);
            $this->orgGroupFacultyRepository->createGroupFaculty($orgGroupFaculty);
        }
    }

    private function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element[GroupConstant::PARENT_ID] == $parentId) {
                $children = $this->buildTree($elements, $element[GroupConstant::GROUP_ID]);
                if ($children) {
                    $element[GroupConstant::SUBGROUPS] = $children;
                } else {
                    $element[GroupConstant::SUBGROUPS] = array();
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function getListGroups($loggedUserId)
    {
        $this->logger->debug(">>>> Get List Groups for loggerUserId" );
        $this->rbacManager = $this->container->get('tinyrbac.manager');

        $this->rbacManager->refreshPermissionCache($loggedUserId);
        $accessMap = $this->rbacManager->getAccessMap($loggedUserId);
        $groups = $accessMap['groups'];

        $groupsArray = [];
        $groupsArrayDto = new GroupsArrayDto();

        if (! empty($groups)) {
        
            // Takes the array and sorts it as string values, ignoring the case of the string, 
            // and then sorting numbers in natural order (1,2,...10).
            asort($groups, SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL);
            foreach ($groups as $key => $value) {
                $groupsDto = new GroupsDto();
                $groupsDto->setGroupId($key);
                $groupsDto->setGroupName($value);
                $groupsArray[] = $groupsDto;
            }
            $groupsArrayDto->setGroups($groupsArray);
        }
        $this->logger->info(">>>> Get List Groups for loggerUserId" );
        return $groupsArrayDto;
    }

    /**
     * Creates System Group.
     *
     * @param string $groupName
     * @param string $externalId
     * @param Organization $organization
     * @return OrgGroup
     */
    public function addSystemGroup($groupName, $externalId, $organization)
    {
        $this->logger->debug(">>>> add Student System Group " . $groupName . "External Id" . $externalId );
        $orgGroup = $this->orgGroupRepository->findOneBy([
            'groupName' => $groupName,
            'organization' => $organization
        ]);
        if (! $orgGroup) {
            $orgGroup = new OrgGroup();

            $orgGroup->setGroupName($groupName);
            $orgGroup->setExternalId($externalId);
            $orgGroup->setOrganization($organization);
            $this->orgGroupRepository->createGroup($orgGroup);
            $this->orgGroupRepository->flush();
        }
        $this->logger->info(">>>> add Student System Group ");
        return $orgGroup;
    }

    /**
     * Remove Student from system group.
     *
     * @param OrgPersonStudent $orgPersonStudent
     */
    public function removeStudentSystemGroup($orgPersonStudent)
    {
        $organization = $orgPersonStudent->getOrganization();
        $student = $orgPersonStudent->getPerson();
        $orgGroup = $this->orgGroupRepository->findOneBy([
            'groupName' => GroupConstant::SYS_GROUP_NAME,
            'organization' => $organization
        ]);
        if ($orgGroup) {
            $orgGroupStudent = $this->orgGroupStudentsRepository->findOneBy([
                'orgGroup' => $orgGroup,
                'person' => $student
            ]);
            if ($orgGroupStudent) {
                $this->orgGroupStudentsRepository->remove($orgGroupStudent);
            }
        }
        $this->logger->info(">>>>Remove Student System Group");

    }

    /**
     * Add Student to System Group.
     *
     * @param Organization $organization
     * @param Person $person
     */
    public function addStudentSystemGroup($organization, $person)
    {
        $systemGroup = $this->addSystemGroup(GroupConstant::SYS_GROUP_NAME, GroupConstant::SYS_GROUP_EXTERNAL_ID, $organization);

        $orgGroupStudents = new OrgGroupStudents();
        $orgGroupStudents->setOrganization($organization);
        $orgGroupStudents->setPerson($person);
        $orgGroupStudents->setOrgGroup($systemGroup);
        $this->orgGroupStudentsRepository->persist($orgGroupStudents);
        $this->logger->info(">>>> add Student System Group organization");
    }

    public function startUpdateSubGroupList($orgId)
    {
        $this->logger->debug(">>>> Start Update Sub Group List for OrganizationId" . $orgId);
        $jobNumber = uniqid();
        $job = new CreateSubGroupListJob();
        $resque = $this->container->get('bcc_resque.resque');
        $job->args = array(

            'jobNumber' => $jobNumber,
            'orgId' => $orgId
        );

        $resque->enqueue($job, true);
    }

    public function buildTreeRefactored($groups){

        $reArr = array();
        foreach($groups as $group){
            if($group['parent_id'] == 0 || !is_numeric($group['parent_id'])){
                $reArr[$group['group_id']] = $group;
                $reArr[$group['group_id']]['parent_id'] = 0;
                $reArr[$group['group_id']]['parent_group_name'] = "false";
                //$reArr[$group['group_id']]['subgroups'] [] = null ;
            }
        }
        foreach($groups as $group){
            if($group['parent_id'] != 0  && is_numeric($group['parent_id']) && isset($reArr[$group['parent_id']])){
                $reArr[$group['parent_id']]['subgroups'][] = $group;
            }
        }

        foreach($reArr as $arr){
            if(!isset($arr['subgroups'])){
                $arr['subgroups'][] = null ;
            }
            $finalArr[] = $arr;
        }
        return $finalArr;
    }

    private function getProcessedGroupListRefactored($organization, $treeGroupresult, $grp, $lastUpdated)
    {
        $groupArray = $grp;
        $groupArray[GroupConstant::CREATEDAT] = new \DateTime($grp[GroupConstant::CREATEDAT]);
        $groupArray[GroupConstant::MODIFIEDAT] = new \DateTime($grp[GroupConstant::MODIFIEDAT]);
        $lastUpdated = (is_null($lastUpdated)) ? $groupArray[GroupConstant::MODIFIEDAT] : (($groupArray[GroupConstant::MODIFIEDAT] > $lastUpdated) ? $groupArray[GroupConstant::MODIFIEDAT] : $lastUpdated);
        $groupArray['lastUpdated'] = $lastUpdated;
        return $groupArray;
    }

    private function systemGroupCheck($groupname)
    {
        if(GroupConstant::SYS_GROUP_NAME == $groupname)
        {
            throw new ValidationException([
                    'Group Name Reserved for System Group.'
                ], 'Group Name Reserved for System Group.', 'system_group_error');
        }
    }

    /**
     * If the given group name is duplicated anywhere (regardless of case)
     * in the given organization, throw a validation error.
     *
     * @param string $groupName
     * @param int $orgId
     * @return null
     * @throws ValidationException
     */
    private function validateGroupNameIsNotDuplicate($groupName, $orgId){

        // get all groups currently in organization
        $organizationGroupData = $this->orgGroupRepository->getGroupByOrganization($orgId);
        $otherGroupsInOrganization = array_column($organizationGroupData, 'groupName');

        // make all other group names lowercase so detecting duplicates is case insensitive
        $otherGroupsInOrganization = array_map('strtolower', $otherGroupsInOrganization);

        // if group name is already in the organization, throw a validation exception with error code group_already_exists
        if (in_array(strtolower($groupName), $otherGroupsInOrganization)) {
            throw new ValidationException([
                'Group name already exists in organization.'
            ], 'Group name already exists in organization.', 'group_already_exists');
        }
    }

}