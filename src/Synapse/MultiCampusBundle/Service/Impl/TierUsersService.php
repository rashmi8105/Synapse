<?php
namespace Synapse\MultiCampusBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\OrganizationRole;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\MultiCampusBundle\Entity\OrgUsers;
use Synapse\MultiCampusBundle\EntityDto\PromoteUserDto;
use Synapse\MultiCampusBundle\EntityDto\RoleDto;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\TilesDto;
use Synapse\MultiCampusBundle\EntityDto\UsersDto;
use Synapse\MultiCampusBundle\Service\TierUsersServiceInterface;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("tier_users_service")
 */
class TierUsersService extends TierHelperService implements TierUsersServiceInterface
{

    const SERVICE_KEY = 'tier_users_service';


    /**
     * @var  ContactInfoRepository
     */
     private $contactInfoRepository;
    /**
     *
     * @var TierUsersRepository
     */
    private $tierUsersRepository;


    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //Repository
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
    }

    public function listTierUsers($tierlevel, $tierId)
    {
        $this->logger->debug("List Primary Tier User| List Secondary Tier User for Tier Id - " . $tierId . "Tier Level -" . $tierlevel);
        return $tier = ($tierlevel == TierConstant::PRIMARY_TIER) ? $this->listPrimaryTierUser($tierId) : $this->listSecondaryTierUser($tierId);
    }

    public function deleteTierUser($personid, $tierlevel, $tierId)
    {
        $this->logger->debug("Delete Primary Tier User| Delete Secondary Tier User for Person Id - " . $personid . "Tier Level -" . $tierlevel . "Tier Id -" . $tierId);
        return $tier = ($tierlevel == TierConstant::PRIMARY_TIER) ? $this->deletePrimaryTierUser($personid, $tierId) : $this->deleteSecondaryTierUser($personid, $tierId);
    }

    public function deleteTierAndCampusUser($campusId, $userId, $tierlevel, $tierId, $loggedInUserId = NULL)
    {
        if (! empty($campusId)) {
            
            $deleteUser = $this->container->get('person_service')->deleteCoordinator($campusId, $userId);
        } elseif (! empty($tierlevel) && ! empty($tierId)) {
            
            $deleteUser = $this->deleteTierUser($userId, $tierlevel, $tierId);
        } else {
            
            $deleteUser = $this->container->get('users_service')->deleteUser($userId, $loggedInUserId);
        }
        return $deleteUser;
    }

    public function listExistingUsers($tierId, $tierlevel)
    {
        $this->logger->debug("List Existing Users -  Tier Level -" . $tierlevel . "Tier Id -" . $tierId);
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        if ($tierlevel == TierConstant::PRIMARY_TIER) {
            $this->validateTier($tierId, $tierlevel);
            $secondaryTiers = $this->campusRepository->listCampuses($tierId, '2');
            $secondaryTierIds = array_column($secondaryTiers, TierConstant::ORGID);
            $campuses = $this->campusRepository->listCampuses($secondaryTierIds, '3');
            $campus = array_column($campuses, TierConstant::ORGID);
            $campusIds = array_merge($secondaryTierIds, $campus);
        } elseif ($tierlevel == TierConstant::SECONDARY_TIER) {
            $this->validateTier($tierId, $tierlevel);
            $secondaryTier = $this->campusRepository->find($tierId);
            $primaryTier[] = $secondaryTier->getParentOrganizationId();
            $campuses = $this->campusRepository->listCampuses($tierId, '3');
            $campus = array_column($campuses, TierConstant::ORGID);
            $campusIds = array_merge($primaryTier, $campus);
        } else {
            $secondaryTier = $this->campusRepository->find($tierId);
            $parentId[] = $secondaryTier->getParentOrganizationId();
            $parentId[] = $secondaryTier->getId();
            $campuses = $this->campusRepository->listCampuses($tierId, '3');
            $campus = array_column($campuses, TierConstant::ORGID);
            $campusIds = array_merge($parentId, $campus);
        }
        $usersList = array();
        if (! empty($campusIds)) {
            $campusIds = implode(',', $campusIds);
            $users = $this->campusRepository->listCampusUsers($campusIds);
            if (! empty($users)) {
                foreach ($users as $user) {
                    $person = $user['id'];
                    $campus = $user['organization_id'];
                    $userType = $this->getUserType($person, $campus);
                    $userRole = $this->getUserRole($person, $campus);
                    $usersDto = new UsersDto();
                    $usersDto->setUserId($person);
                    $usersDto->setCampusId($campus);
                    $usersDto->setFirstName($user['firstname']);
                    $usersDto->setLastName($user['lastname']);
                    $usersDto->setTitle($user['title']);
                    $usersDto->setEmail($user['primary_email']);
                    $usersDto->setExternalId($user['external_id']);
                    $usersDto->setUserType($userType);
                    $usersDto->setRole($userRole);
                    $permissions = $this->permission($campus, $person);
                    $usersDto->setPermissions($permissions);
                    $usersList[] = $usersDto;
                }
            }
        }
        return $usersList;
    }

    public function updateCoordinatorRole($userId, RoleDto $roleDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($roleDto);
        $this->logger->debug("Update Coordinator Role " . $logContent . "User Id - " . $userId);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_ROLE_REPO);
        $campusId = $roleDto->getCampusId();
        $orgRole = $this->orgRoleRepository->getUserCoordinatorRole($campusId, $userId);
        if (empty($orgRole)) {
            throw new ValidationException([
                "Coordinator not found"
            ], "Coordinator not found", "coordinator_not_found");
        }
        $role = $this->validateRole($roleDto->getRoleId());
        $orgrole = new OrganizationRole();
        $orgRole->setRole($role);
        $this->orgRoleRepository->flush();
    }

    public function promoteUserToTierUser(PromoteUserDto $promoteDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($promoteDto);
        $this->logger->debug("Update Coordinator Role " . $logContent);
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $userId = $promoteDto->getUserId();
        $tierId = $promoteDto->getTierId();
        if ($promoteDto->getTierLevel() == TierConstant::PRIMARY_TIER) {
            $tierLevel = 1;
        }
        if ($promoteDto->getTierLevel() == TierConstant::SECONDARY_TIER) {
            $tierLevel = 2;
        }
        $tierUser = $this->tierUsersRepository->findBy([
            TierConstant::FIELD_ORGANIZATION => $tierId,
            TierConstant::PERSON_FIELD => $userId
        ]);
        if ($tierUser) {
            throw new ValidationException([
                "User already exists"
            ], "User already exists", "user_already_exists");
        }
        $person = $this->personRepository->find($userId);
        if (empty($person)) {
            throw new ValidationException([
                "Person not found"
            ], "Person not found", "person_not_found");
        }
        $campus = $this->campusRepository->findOneBy([
            'id' => $tierId,
            'tier' => $tierLevel
        ]);
        if (empty($campus)) {
            throw new ValidationException([
                "Campus not found"
            ], "Campus not found", "campus_not_found");
        }
        $orgUsers = new OrgUsers();
        $orgUsers->setOrganization($campus);
        $orgUsers->setPerson($person);
        $this->tierUsersRepository->create($orgUsers);
        $this->tierUsersRepository->flush();
    }

    private function validateRole($roleId)
    {
        $this->roleRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Role');
        $role = $this->roleRepository->find($roleId);
        if (! isset($role)) {
            throw new ValidationException([
                "Role Not Found"
            ], "Role Not Found", 'role_not_found');
        }
        return $role;
    }

    /**
     * @param $tierId
     * @param $tier_level
     * @deprecated
     */
    private function validateTier($tierId, $tier_level)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $tierLevel = ($tier_level == TierConstant::PRIMARY_TIER) ? 1 : 2;
        $tierDetails = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => $tierLevel
        ));
        if (empty($tierDetails)) {
            throw new ValidationException([
                "Invalid Parent tier"
            ], "Invalid Parent tier", 'invalid_parent_tier_error');
        }
    }

    public function permission($orgId, $personId)
    {
        $this->logger->debug(" Permission for Organization Id - " . $orgId . "Person Id -" . $personId);
        $this->OrgGroupFaculty = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $this->OrgCourseFaculty = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');
        $groupPermissions = $this->OrgGroupFaculty->findBy([
            TierConstant::FIELD_ORGANIZATION => $orgId,
            TierConstant::PERSON_FIELD => $personId
        ]);
        $coursePermission = $this->OrgCourseFaculty->findBy([
            TierConstant::FIELD_ORGANIZATION => $orgId,
            TierConstant::PERSON_FIELD => $personId
        ]);
        $personPermission = array_merge($groupPermissions, $coursePermission);
        $permissions = array();
        if (! empty($personPermission)) {
            $permissions = $this->getFacultyPermissions($personPermission);
        }
        return $permissions;
    }

    private function getUserType($person, $campus)
    {
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_ROLE_REPO);
        $this->orgPersonFacultyRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_FACULTY_REPO);
        $this->roleLangRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:RoleLang');
        $isCoordinator = $this->orgRoleRepository->findBy(array(
            TierConstant::FIELD_ORGANIZATION => $campus,
            TierConstant::PERSON_FIELD => $person
        ));
        $isFaculty = $this->orgPersonFacultyRepo->findBy(array(
            TierConstant::FIELD_ORGANIZATION => $campus,
            TierConstant::PERSON_FIELD => $person
        ));
        $type = '';
        if ($isCoordinator) {
            $type = 'Coordinator';
        }
        if ($isFaculty) {
            $type = 'Faculty';
        }
        return $type;
    }

    public function getUserRole($person, $campus)
    {
        $this->logger->debug(" Get User Role ");
        $roleName = '';
        $this->roleLangRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:RoleLang');
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_ROLE_REPO);
        $isCoordinator = $this->orgRoleRepository->findBy(array(
            TierConstant::FIELD_ORGANIZATION => $campus,
            TierConstant::PERSON_FIELD => $person
        ));
        if ($isCoordinator) {
            $coordinator = $isCoordinator[0];
            $roles = $this->roleLangRepository->findBy(array(
                'role' => $coordinator->getRole()
                    ->getId()
            ));
            if (isset($roles)) {
                foreach ($roles as $role) {
                    $roleName = $role->getRolename();
                }
            }
        }
        return $roleName;
    }

    public function listPrimaryTierCoordinators($campusId, $filter = '')
    {
        $this->logger->debug("List Primary Tier Coordinators - " . $campusId . "Filter -" . $filter);
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);

        $campuses = $this->campusRepository->find($campusId);
        
        $secondaryParent = $campuses->getParentOrganizationId();
        $secondaryRow = $this->campusRepository->find($secondaryParent);
        $primaryParent = $secondaryRow->getParentOrganizationId();
        $secondaryTiers = $this->campusRepository->listCampuses($primaryParent, '2');
        $secondaryTierIds = array_column($secondaryTiers, TierConstant::ORGID);
        $parent = $secondaryTierIds;
        $parent[] = $primaryParent;
        $campuses = $this->campusRepository->listCampuses($secondaryTierIds, '3');
        $campus = array_column($campuses, TierConstant::ORGID);
        $campus = array_merge($parent, $campus);
        $usersList = '';
        if (! empty($campus)) {
            $campusList = implode(',', $campus);
            $users = $this->campusRepository->listCampusUsers($campusList);
            if (! empty($users)) {
                foreach ($users as $user) {
                    $person = $user['id'];
                    $campus = $user['organization_id'];
                    $userType = $this->getUserType($person, $campus);
                    $userRole = $this->getUserRole($person, $campus);
                    if ($userRole != '' && $campus == $campusId) {
                        continue;
                    } else {
                        $usersDto = new UsersDto();
                        $usersDto->setUserId($person);
                        $usersDto->setCampusId($campus);
                        $usersDto->setFirstName($user['firstname']);
                        $usersDto->setLastName($user['lastname']);
                        $usersDto->setTitle($user['title']);
                        $usersDto->setEmail($user['primary_email']);
                        $usersDto->setExternalId($user['external_id']);
                        $usersDto->setUserType($userType);
                        $usersDto->setRole($userRole);
                        $permissions = $this->permission($campus, $person);
                        $usersDto->setPermissions($permissions);
                        $usersList[] = $usersDto;
                    }
                }
            }
        }
        return $usersList;
    }

    public function listCampusCoordinatorsAction($campusId)
    {
        $this->logger->debug("List Campus Coordinators for Campus Id- " . $campusId);
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_ROLE_REPO);
        $secondary = $this->campusRepository->find($campusId);
        $tierId = $secondary->getParentOrganizationId();
        $listCampuses = $this->campusRepository->listCampuses($tierId, '3');
        $campus = array_column($listCampuses, TierConstant::ORGID);
        $coordinators = $this->orgRoleRepository->getPrimaryTierCoordinators($campus);
        $usersList = array();
        if (! empty($coordinators)) {
            foreach ($coordinators as $coordinator) {
                $person = $coordinator->getPerson();
                $contactInfo = $this->contactInfoRepository->getCoalescedContactInfo($person->getContacts());
                $campus = $coordinator->getOrganization()->getId();
                $userRole = $this->getUserRole($person, $campus);
                $usersDto = new UsersDto();
                $usersDto->setUserId($person->getId());
                $usersDto->setCampusId($campus);
                $parent = call_user_func_array('array_merge', $this->campusRepository->getHierarchyOrder($campus));
                $usersDto->setFirstName($person->getFirstname());
                $usersDto->setLastName($person->getLastname());
                $usersDto->setEmail($contactInfo->getPrimaryEmail());
                $usersDto->setExternalId($person->getExternalId());
                $usersDto->setRole($userRole);
                $phoneNumber = $contactInfo->getPrimaryMobile();
                if(!$phoneNumber)
                {
                   $phoneNumber = $contactInfo->getHomePhone(); 
                }
                $usersDto->setPhone($phoneNumber);
                $usersDto->setPrimaryTierName($parent['primaryName']);
                $usersDto->setSecondaryTierName($parent['secondaryName']);
                $usersDto->setCampusName($parent['campusName']);
                $usersList[] = $usersDto;
            }
        }
        return $usersList;
    }

    private function findTierUserPerson($id)
    {
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $person = $this->tierUsersRepository->find($id);
        if (! $person) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }
        return $person;
    }

    public function listActiveCampusTiersforUser($loggedUser)
    {
        $this->logger->info("List Active Campus Tiers for User");
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_ROLE_REPO);
        $this->orgPersonFacultyRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_FACULTY_REPO);
        $this->orgPersonStudentRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_STUDENT_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $loggedinUsers = '';
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $person = $this->personRepository->find($loggedUser);
        $multiCampusPerson = $this->personRepository->findBy([
            'username' => $person->getUsername()
        ]);
        if ($multiCampusPerson) {
            foreach ($multiCampusPerson as $personObj) {
                $loggedinUsers[] = $personObj->getId();
            }
        }
        $orgArray[] = $this->userOrganization($this->tierUsersRepository, $loggedinUsers);
        $orgArray[] = $this->userOrganization($this->orgPersonStudentRepo, $loggedinUsers);
        $orgArray[] = $this->userOrganization($this->orgPersonFacultyRepo, $loggedinUsers);
        $orgArray[] = $this->userOrganization($this->orgRoleRepository, $loggedinUsers);
        $organization = call_user_func_array('array_merge', $orgArray);
        $org = array_unique($organization);
        $role = array();
        
        $tilesArray = array();
        $tierDto = new TierDto();
        $tierDto->setPersonId($loggedUser);
        $person = $this->container->get('person_service')->findPerson($loggedUser);
        $tierDto->setExternalId($person->getExternalId());
        $tilesArray = $this->getTiles($org, $loggedUser);
        $tierDto->setTiles($tilesArray);
        return $tierDto;
    }

    private function getTiles($org, $loggedUser)
    {
        $tilesArray = array();
        foreach ($org as $organization) {
            $roles = array();
            $campusType = '';
            $orgPersonStudent = $this->checkPersonRole($loggedUser, $organization, $this->orgPersonStudentRepo);
            if ($orgPersonStudent != NULL) {
                $roles[] = 'student';
            }
            $orgPersonFaculty = $this->checkPersonRole($loggedUser, $organization, $this->orgPersonFacultyRepo);
            if ($orgPersonFaculty != NULL) {
                $roles[] = 'faculty';
            }
            $orgCoordinator = $this->checkPersonRole($loggedUser, $organization, $this->orgRoleRepository);
            if ($orgCoordinator != NULL) {
                $roles[] = 'coordinator';
            }
            
            $tilesDto = new TilesDto();
            $tierDet = $this->tierDetailsRepository->findOneBy(array(
                TierConstant::FIELD_ORGANIZATION => $organization
            ));
            
            $tilesDto->setOrganizationId($tierDet->getOrganization()
                ->getId());
            $tilesDto->setName($tierDet->getOrganizationName());
            $tilesDto->setLogo($tierDet->getOrganization()
                ->getLogoFileName());
            $tilesDto->setUrl($tierDet->getOrganization()
                ->getSubdomain());
            if ($tierDet->getOrganization()->getTier() == '3') {
                $campusType = 'hierarchy';
                $tilesDto->setIsHierarchyCampus(true);
            }
            if ($tierDet->getOrganization()->getTier() == '0') {
                $campusType = 'solo';
                $tilesDto->setIsHierarchyCampus(false);
            }
            if ($campusType != '') {
                $tilesDto->setType('campus');
                foreach ($roles as $role) {
                    $roleArray = array();
                    $roleDto = new RoleDto();
                    $roleDto->setRole($role);
                    $roleArray[] = $roleDto;
                }
                $tilesDto->setRoles($roleArray);
            } elseif ($tierDet->getOrganization()->getTier() == '1' || $tierDet->getOrganization()->getTier() == '2') {
                $tilesDto->setType('tier');
                $tilesDto->setId($tierDet->getOrganization()
                    ->getId());
            }
            
            $tilesArray[] = $tilesDto;
        }
        return $tilesArray;
    }

    private function userOrganization($repo, $loggedUser)
    {
        $userOrganization = $repo->findByPerson($loggedUser);
        $orgArray = array();
        foreach ($userOrganization as $tierUserDet) {
            $orgArray[] = $tierUserDet->getOrganization()->getId();
        }
        return $orgArray;
    }

    private function checkPersonRole($loggedUser, $org, $repo)
    {
        $personRole = $repo->findOneBy(array(
            TierConstant::PERSON_FIELD => $loggedUser,
            TierConstant::FIELD_ORGANIZATION => $org
        ));
        
        return $personRole;
    }
}