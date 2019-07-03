<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use Doctrine\Common\Cache\RedisCache;
use FOS\OAuthServerBundle\Entity\ClientManager;
use FOS\OAuthServerBundle\Util\Random;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Validator;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\DTO\StudentParticipationDTO;
use Synapse\CoreBundle\Entity\AuthCode;
use Synapse\CoreBundle\Entity\Client;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationRole;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\BulkInviteJob;
use Synapse\CoreBundle\Repository\AccessTokenRepository;
use Synapse\CoreBundle\Repository\AuthCodeRepository;
use Synapse\CoreBundle\Repository\ClientRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;
use Synapse\CoreBundle\Repository\RoleRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\CoreBundle\Util\Constants\UsersConstant;
use Synapse\DataBundle\DAO\InformationSchemaDAO;
use Synapse\MultiCampusBundle\Entity\OrgUsers;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\PermissionDto;
use Synapse\MultiCampusBundle\EntityDto\SecondaryTiersDto;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\UsersDto;
use Synapse\MultiCampusBundle\Repository\OrgUsersRepository;
use Synapse\MultiCampusBundle\Service\Impl\TierUsersService;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Entity\UserDTO;
use Synapse\RestBundle\Entity\UserListDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\DAO\PredefinedSearchDAO;
use Synapse\UploadBundle\Service\Impl\FacultyUploadService;
use Synapse\UploadBundle\Service\Impl\SynapseUploadService;

/**
 * @DI\Service("users_service")
 */
class UsersService extends AbstractService
{
	const SERVICE_KEY = 'users_service';

    // Scaffolding
    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var ClientManager
     */
    private $clientManager;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var Validator
     */
    private $validator;

    // Services
    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var EmailPasswordService
     */
    private $emailPasswordService;

    /**
     * @var FacultyUploadService
     */
    private $facultyUploadService;

    /**
     * @var GroupService
     */
    private $groupService;

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
     * @var ReferralService
     */
    private $referralService;

    /**
     * @var SynapseUploadService
     */
    private $synapseUploadService;

    /**
     * @var TierUsersService
     * @deprecated
     */
    private $tierUsersService;

    /**
     * @var UsersHelperService
     */
    private $usersHelperService;



    //Dao

    /**
     * @var InformationSchemaDAO
     */
    private $informationSchemaDao;

    /**
     * @var PredefinedSearchDAO
     */
    private $predefinedSearchDAO;

    // Repositories

    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepository;

    /**
     * @var AuthCodeRepository
     */
    private $authCodeRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentRepository;

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
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrgUsersRepository
     * @deprecated
     */
    private $orgUsersRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var RoleLangRepository
     */
    private $roleLangRepository;


    /**
     * UsersService constructor.
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
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->clientManager = $this->container->get(SynapseConstant::CLIENT_MANAGER_CLASS_KEY);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->securityContext = $this->container->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY);
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);

        //Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->emailPasswordService = $this->container->get(EmailPasswordService::SERVICE_KEY);
        $this->facultyUploadService = $this->container->get(FacultyUploadService::SERVICE_KEY);
        $this->groupService = $this->container->get(GroupService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->referralService = $this->container->get(ReferralService::SERVICE_KEY);
        $this->synapseUploadService = $this->container->get(SynapseUploadService::SERVICE_KEY);
        $this->tierUsersService = $this->container->get(TierUsersService::SERVICE_KEY);
        $this->usersHelperService = $this->container->get(UsersHelperService::SERVICE_KEY);

        //dao

        $this->informationSchemaDao = $this->container->get(InformationSchemaDAO::DAO_KEY);
        $this->predefinedSearchDAO = $this->container->get(PredefinedSearchDAO::DAO_KEY);

        //Repositories
        $this->accessTokenRepository = $this->repositoryResolver->getRepository(AccessTokenRepository::REPOSITORY_KEY);
        $this->authCodeRepository = $this->repositoryResolver->getRepository(AuthCodeRepository::REPOSITORY_KEY);
        $this->clientRepository = $this->repositoryResolver->getRepository(ClientRepository::REPOSITORY_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgGroupStudentRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->orgUsersRepository = $this->repositoryResolver->getRepository(OrgUsersRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->roleLangRepository = $this->repositoryResolver->getRepository(RoleLangRepository::REPOSITORY_KEY);
        $this->roleRepository = $this->repositoryResolver->getRepository(RoleRepository::REPOSITORY_KEY);
    }

    /**
     * Create tier user
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function createTierUser(UserDTO $userDTO)
    {
        return $tier = $userDTO->getTierLevel() == TierConstant::PRIMARY_TIER ? $this->createPrimaryTierUser($userDTO) : $this->createSecondaryTierUser($userDTO);
    }

    /**
     * Update tier user
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function updateTierUser(UserDTO $userDTO)
    {
        return $tier = $userDTO->getTierLevel() == TierConstant::PRIMARY_TIER ? $this->updatePrimaryTierUser($userDTO) : $this->updateSecondaryTierUser($userDTO);
    }

    /**
     * creates a person if one does not exist from the User DTO
     *
     * @param UserDTO $userDTO
     * @param Organization $organization
     * @throws SynapseValidationException
     * @return Person
     */
    public function createPersonFromUserDTO(UserDTO $userDTO, $organization)
    {
        $userEmail = $userDTO->getEmail();
        $userPhone = $userDTO->getPhone();
        $userExternalId = $userDTO->getExternalid();
        $person = $this->usersHelperService->validateExternalId($userExternalId, $organization, NULL, NULL, $userDTO->getUserType(), $userEmail);
        if (!$person) {
            $person = new Person();
            $person->setFirstname($userDTO->getFirstname());
            $person->setLastname($userDTO->getLastname());
            $person->setUsername($userEmail);
            $person->setTitle($userDTO->getTitle());
            $person->setOrganization($organization);
            $person->setExternalId($userExternalId);
            $contact = new ContactInfo();
            if ($userDTO->getIsmobile()) {
                $contact->setPrimaryMobile($userPhone);
            } else {
                $contact->setHomePhone($userPhone);
            }
            $contact->setPrimaryEmail($userEmail);

            $contactErrors = $this->validator->validate($contact);
            if (count($contactErrors) > 0) {
                $errorsString = $contactErrors[0]->getMessage();
                throw new SynapseValidationException($errorsString);
            }
            $personErrors = $this->validator->validate($person);
            if (count($personErrors) > 0) {
                $errorsString = $personErrors[0]->getMessage();
                throw new SynapseValidationException($errorsString);
            }
            $person->addContact($contact);

            $this->personRepository->createPerson($person);
        }
        return $person;
    }

    /**
     * Creates a student from the the information given from the UserDTO
     * Calls the createPersonFromUserDTO
     *
     * @param UserDTO $userDTO
     * @param Organization $organization
     * @throws SynapseValidationException
     * @return Person
     */
    public function createStudentFromUserDTO(UserDTO $userDTO, $organization)
    {
        $organizationId = $organization->getId();
        $status = $userDTO->getIsActive();
        $participatingStatus = $userDTO->getParticipating();
        $currentAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicYear($organizationId);

        if ($participatingStatus == 1) {
            if (!$currentAcademicYear) {
                throw new SynapseValidationException("Adding a student as participant or active requires the current date be within an Academic Year");
            }
        } else {
            if ($status == 1) {
                throw new SynapseValidationException("Cannot mark a student as active and not participating. Please mark as participating and active, or mark as not participating and inactive.");
            }
        }

        $person = $this->createPersonFromUserDTO($userDTO, $organization);
        $orgPersonStudent = new OrgPersonStudent();
        $orgPersonStudent->setPerson($person);
        $orgPersonStudent->setOrganization($organization);

        $orgPersonStudent->setStatus($status);
        $orgPersonStudent->setAuthKey($this->personService->generateAuthKey($person->getExternalId(), 'student'));
        $this->orgPersonStudentRepository->persist($orgPersonStudent);

        // Adding to System Group

        $this->groupService->addStudentSystemGroup($organization, $person);

        if ($participatingStatus == 1) {
            // Adding the student to the participating/IsActive field

            $currentAcademicYear = $this->orgAcademicYearRepository->findOneBy(['id' => $currentAcademicYear['org_academic_year_id']]);
            $orgPersonStudentYear = new OrgPersonStudentYear();
            $orgPersonStudentYear->setIsActive($status);
            $orgPersonStudentYear->setOrgAcademicYear($currentAcademicYear);
            $orgPersonStudentYear->setPerson($person);
            $orgPersonStudentYear->setOrganization($organization);

            $this->orgPersonStudentYearRepository->persist($orgPersonStudentYear);
        }

        return $person;
    }

    /**
     * Creates a faculty from the the information given from the UserDTO
     * Calls the createPersonFromUserDTO
     *
     * @param UserDTO $userDTO
     * @param Organization $organization
     * @return Person
     */
    public function createFacultyFromUserDTO(UserDTO $userDTO, $organization)
    {
        $person = $this->createPersonFromUserDTO($userDTO, $organization);

        $orgPersonFaculty = new OrgPersonFaculty();
        $orgPersonFaculty->setPerson($person);
        $orgPersonFaculty->setOrganization($organization);
        $status = $userDTO->getIsActive();
        $orgPersonFaculty->setStatus($status);
        $orgPersonFaculty->setAuthKey($this->personService->generateAuthKey($person->getExternalId(), "faculty"));
        $this->orgPersonFacultyRepository->persist($orgPersonFaculty);

        return $person;
    }

    /**
     * Creates a student from the the information given from the UserDTO
     * Calls the createFacultyFromUserDTO
     *
     * @param UserDTO $userDTO
     * @param Organization $organization
     * @return null|object|Person
     */
    public function createCoordinatorFromUserDTO(UserDTO $userDTO, $organization)
    {
        $role = $this->usersHelperService->validateRole($userDTO->getRoleid());

        $person = $this->createFacultyFromUserDTO($userDTO, $organization);
        // Enabling data dump file for coordinators
        $organizationId = $organization->getId();
        $this->synapseUploadService->updateDataFile($organizationId, "Faculty");

        $orgRole = new OrganizationRole();
        $orgRole->setOrganization($organization);
        $orgRole->setPerson($person);
        $orgRole->setRole($role);
        $this->organizationRoleRepository->createCoordinator($orgRole);
        $this->personRepository->flush();
        $this->logger->info("Create User is completed");

        return $person;
    }


    /**
     * Method to create service account for a user
     *
     * @param Organization $organization
     * @param UserDTO $userDTO
     * @return UserDTO
     * @throws SynapseValidationException
     */
    public function createServiceAccount(UserDTO $userDTO, $organization)
    {
        $serviceAccountRoleObject = $this->roleRepository->find($userDTO->getRoleid());

        // check character length for  service account name - more tha 45 character will throw exception
        $lastName = trim($userDTO->getLastname());
        $lastNameColumnLength = $this->informationSchemaDao->getCharacterLengthForColumnsInTable('person', ['lastname']);
        $columnLength = $lastNameColumnLength[0]['length'];

        if (strlen($lastName) > $columnLength) {
            throw new SynapseValidationException(" Service Account name length cannot be greater than $columnLength characters");
        } else if ($lastName == "") {
            throw new SynapseValidationException(" Service Account name cannot be empty");
        }

        // check if the service account name already exists for the organization
        $serviceAccount = $this->personRepository->findOneBy([
            'lastname' => $lastName,
            'organization' => $organization->getId(),
            'externalId' => null // This is to ensure that we are only validating uniqueness  of service accounts  , as they will not be having external ids
        ]);

        if ($serviceAccount) {
            throw new SynapseValidationException("Service Account with the same name already exists for the organization");
        }

        $person = new Person();
        $person->setLastname($lastName);
        $person->setOrganization($organization);
        $serviceAccountPersonObject = $this->personRepository->persist($person, false); // persist = false, as we don't want multiple db hits, but can do it in one database hit


        $organizationRoleObject = new OrganizationRole();
        $organizationRoleObject->setOrganization($organization);
        $organizationRoleObject->setPerson($serviceAccountPersonObject);
        $organizationRoleObject->setRole($serviceAccountRoleObject);
        $this->organizationRoleRepository->persist($organizationRoleObject, false);

        $client = $this->clientManager->createClient();
        $clientObject = new Client();

        $clientRandomId = $client->getRandomId();
        $clientAllowedGrantTypes = $client->getAllowedGrantTypes();
        $clientSecret = $client->getSecret();
        $clientRedirectUri = $client->getRedirectUris();

        $clientObject->setRandomId($clientRandomId);
        $clientObject->setPerson($serviceAccountPersonObject);
        $clientObject->setOrganization($organization);
        $clientObject->setAllowedGrantTypes($clientAllowedGrantTypes);
        $clientObject->setRedirectUris($clientRedirectUri);
        $clientObject->setSecret($clientSecret);
        $this->clientManager->updateClient($clientObject);

        $authorizationCode = Random::generateToken(); // generating the AuthCode
        $authCodeObject = new AuthCode();
        $authCodeObject->setClient($clientObject);
        $authCodeObject->setUser($serviceAccountPersonObject);
        $authCodeObject->setToken($authorizationCode);
        $authCodeObject->setOrganization($organization);
        $authCodeObject->setRedirectUri(serialize($clientRedirectUri));
        $authCodeObject->setExpiresAt(0); // setting it to 0 so that the auth code does not expire
        $this->authCodeRepository->persist($authCodeObject);

        $userDTO->setId($serviceAccountPersonObject->getId());
        $userDTO->setAuthCode($authorizationCode);
        $userDTO->setClientId($clientObject->getId() . "_" . $clientRandomId);
        $userDTO->setClientSecret($clientSecret);
        return $userDTO;
    }

    /**
     * Create a new user / service account
     * @deprecated - Person creation is being consolidated within the person bundle. Please look there for this functionality
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function createUser(UserDTO $userDTO)
    {
        $organization = $this->usersHelperService->validateOrganization($userDTO->getCampusId());
        $organizationId = $organization->getId();
        $roleId = $userDTO->getRoleid();
        $userType = $userDTO->getUserType();
        $serviceAccountRoleObject = $this->roleLangRepository->findOneBy(['roleName' => SynapseConstant::SERVICE_ACCOUNT_ROLE_NAME]);
        if ($serviceAccountRoleObject) {
            $serviceAccountRoleId = $serviceAccountRoleObject->getRole()->getId();
        }
        if (!empty($serviceAccountRoleId) && $roleId == $serviceAccountRoleId) {
            $userDTO = $this->createServiceAccount($userDTO, $organization);
        } else {
            if ($userType == "student") {
                $person = $this->createStudentFromUserDTO($userDTO, $organization);
            } else {
                if ($userType == "coordinator") {
                    $person = $this->createCoordinatorFromUserDTO($userDTO, $organization);
                } else {
                    $person = $this->createFacultyFromUserDTO($userDTO, $organization);
                }
            }
            $people = $this->cache->fetch("organization.{$organizationId}.people");
            $people = $people ? $people : [];

            $personId = $person->getId();
            $people[$personId] = $person->getExternalId();
            $this->cache->save("organization.{$organizationId}.people", $people);

            // refresh cache
            $loggedInUserId = $this->securityContext->getToken()->getUser()->getId();
            $this->rbacManager->refreshPermissionCache($loggedInUserId);
            $userDTO->setId($personId);
        }
        return $userDTO;
    }

    /**
     * Update an existing user
     *
     * @deprecated - Person update logic is being consolidated in PersonBundle. Please search there for this functionality
     *
     * @param UserDTO $userDTO
     * @param int $userId
     * @param int $organizationId
     * @param int $loggedInPersonId
     * @throws SynapseValidationException
     * @return UserDTO
     */
    public function updateUser(UserDTO $userDTO, $userId, $organizationId, $loggedInPersonId)
    {
        $campusId = $userDTO->getCampusId();
        $externalId = $userDTO->getExternalid();
        $userType = $userDTO->getUserType();
        $roleId = $userDTO->getRoleid();
        $email = $userDTO->getEmail();
        $googleEmailId = $userDTO->getGoogleEmailId();
        $currentActiveStatus = $userDTO->getIsActive();
        $userPhone = $userDTO->getPhone();

        if ($userType == 'switch-campus') {
            return $this->securityContext->getToken()
                ->getUser()
                ->getOrganization()
                ->getId();
        }

        $person = $this->personRepository->find($userId, new SynapseValidationException('Person Not Found.'));
        $personId = $person->getId();

        $organizationObject = $this->usersHelperService->validateOrganization($campusId);

        if ($organizationId != -1 && $person->getOrganization()->getId() != $campusId ) {
            throw new SynapseValidationException('Person does not not belong to this organization');
        }


        if (empty($externalId) && empty($userType) && !empty($roleId) && !empty($campusId)) {
            $userDTO->setId($personId);
            return $this->promoteFacultyToCoordinator($userDTO);
        } else {
            if (strtolower($userType) != strtolower(SynapseConstant::SERVICE_ACCOUNT_ROLE_NAME)) {
                $this->usersHelperService->validateExternalId($externalId, $organizationObject, $userId, NULL, $userType, $email);
            }
            if ($userType == 'coordinator') {
                $role = $this->usersHelperService->validateRole($roleId);
                $organizationRoleObject = $this->organizationRoleRepository->findOneBy(array(
                    'organization' => $person->getOrganization(),
                    'person' => $person
                ));
                if (!$organizationRoleObject) {
                    $newOrganizationRoleObject = new OrganizationRole();
                    $newOrganizationRoleObject->setOrganization($organizationObject);
                    $newOrganizationRoleObject->setPerson($person);
                    $newOrganizationRoleObject->setRole($role);
                    $this->organizationRoleRepository->createCoordinator($newOrganizationRoleObject);
                } else {
                    $organizationRoleObject->setRole($role);
                }
            }

            if (!empty($googleEmailId)) {
                $orgPersonFaculty = $this->orgPersonFacultyRepository->findOneBy(['person' => $person, 'organization' => $organizationObject]);
                if (!empty($orgPersonFaculty)) {
                    $orgPersonFaculty->setGoogleEmailId($googleEmailId);
                }
            }
            $person->setFirstname($userDTO->getFirstname());
            $person->setLastname($userDTO->getLastname());
            $person->setUsername($email);
            $person->setTitle($userDTO->getTitle());
            $person->setExternalId($externalId);
            $person->setOrganization($organizationObject);
            $person->setAuthUsername($userDTO->getLdapUsername());
            $contact = $person->getContacts()->first();
            if ($contact) {
                if ($userDTO->getIsmobile()) {
                    $contact->setHomephone(NULL);
                    $contact->setPrimarymobile($userPhone);
                } else {
                    $contact->setPrimarymobile(NULL);
                    $contact->setHomephone($userPhone);
                }
                if ($contact->getAlternateemail() == ' ') {
                    $contact->setAlternateemail(NULL);
                }
                $contact->setPrimaryemail($email);
                $contactErrors = $this->validator->validate($contact);
                if (count($contactErrors) > 0) {
                    $errorsString = $contactErrors[0]->getMessage();
                    throw new SynapseValidationException($errorsString);
                }
            }

            $isParticipating = $userDTO->getParticipating();

            if (isset($isParticipating) && ($isParticipating == 0) && ($userType == 'student')) {
                if ($currentActiveStatus == 1) {
                    throw new SynapseValidationException("Cannot mark a student as active and not participating. Please mark as participating and active, or mark as not participating and inactive.");
                } else {
                    $this->usersHelperService->updateStudentAsNonParticipating($userId, $campusId, $isParticipating, $loggedInPersonId);
                }
            } else if ($isParticipating == 1 && $userType == 'student') {
                // make the student as participant
                $currentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);
                $orgPersonStudentYear = $this->orgPersonStudentYearRepository->findOneBy(array(
                    'organization' => $organizationId,
                    'person' => $userId,
                    'orgAcademicYear' => $currentAcademicYearId
                ));
                $existingIsActiveStatus = '';
                if ($orgPersonStudentYear) {
                    $existingIsActiveStatus = $orgPersonStudentYear->getIsActive();
                }
                // validating current value with existing value, if there is no change then the below code should not be executed.
                if (($existingIsActiveStatus != $currentActiveStatus) || empty($orgPersonStudentYear)) {
                    if ($orgPersonStudentYear) {
                        // If there is a participant record, then update the status
                        $orgPersonStudentYear->setIsActive($currentActiveStatus);
                    } else {
                        // If there is no participant record, create a new one.
                        $orgAcademicYearObject = $this->orgAcademicYearRepository->find($currentAcademicYearId);
                        $orgPersonStudentYearObject = new OrgPersonStudentYear();
                        $orgPersonStudentYearObject->setIsActive($currentActiveStatus);
                        $orgPersonStudentYearObject->setOrgAcademicYear($orgAcademicYearObject);
                        $orgPersonStudentYearObject->setPerson($person);
                        $orgPersonStudentYearObject->setOrganization($organizationObject);
                        $this->orgPersonStudentYearRepository->persist($orgPersonStudentYearObject);
                    }
                    if ($currentActiveStatus == 1) {
                        $loggedInUser = $this->personRepository->find($loggedInPersonId);
                        $this->referralService->sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate($userId, $organizationId, 'student_made_participant', $loggedInUser);
                    }
                }
            } else if ($currentActiveStatus != NULL && $userType != 'student') {
                $this->usersHelperService->updateStatus($userId, $campusId, $userType, $currentActiveStatus);
            }

            $this->personRepository->flush();
            $userDTO->setId($person->getId());
            return $userDTO;
        }
    }

    /**
     * Get list of users matching specified search text.
     *
     * @param $tierCampusId
     * @param $campusId
     * @param $type
     * @param $tierlevel
     * @param $tierId
     * @param $list
     * @param string $searchText
     * @param string $page
     * @param string $offset
     * @param string $exclude
     * @param bool $checkAccessToOrg
     * @return array|\Synapse\CoreBundle\Entity\Person[]|TierDto
     */
    public function getUsersList($tierCampusId, $campusId, $type, $tierlevel, $tierId, $list, $searchText = '', $page = '', $offset = '', $exclude = '', $checkAccessToOrg = true)
    {
        if ($checkAccessToOrg) {
            $this->rbacManager->checkAccessToOrganization($campusId);
        }
        $this->logger->debug("Get User list  based on Campus Tier level Tier Id" . $tierCampusId . "campusId" . $campusId . "Type " . $type . "Tier Level " . $tierlevel);
        if ($campusId != "" && $type != "") {
            if ($type == "userlist") {
                $getListResp = $this->getAllUserList($campusId, $list);
            } else {
                $getListResp = $this->getUsers($campusId, $type, $searchText, $page, $offset, $exclude);
            }
        } elseif ($tierCampusId != "" && $tierlevel != "") {
            $getListResp = $this->tierUsersService->listExistingUsers($tierCampusId, $tierlevel);
        } elseif ($tierlevel != "" && $tierId != "") {
            $getListResp = $this->tierUsersService->listTierUsers($tierlevel, $tierId);
        } else {
            $this->logger->error("Users Service - getUsersList - Invalid Users");
            throw new ValidationException([
                UsersConstant::ERROR_INVALID_PARAMS
            ], UsersConstant::ERROR_INVALID_PARAMS, UsersConstant::ERROR_INVALID_PARAMS_KEY);
        }
        $this->logger->info("Getting the User list");
        return $getListResp;
    }

    /**
     * Get all users in an organization
     * TODO:: Based on traversing the frontend code and admin site code, this function is not used except for
     * TODO:: anything except populating the "Add existing faculty as coordinator" list. Other usages of this
     * TODO:: function all link back to deprecated multicampus functionality. This function needs to be removed. 
     *
     * @param integer $campusId
     * @param string $list
     * @return UserDTO
     */
    public function getAllUserList($campusId, $list)
    {
        $organization = $this->organizationRepository->find($campusId);
        $allStudents = [];
        $allFaculties = [];
        $coordinatorArray = [];
        if (strtolower($list) == 'all') {
            $allFaculties = $this->personRepository->getDumpByOrganizationByType($campusId, 'faculty', $coordinatorArray);
            $allStudents = $this->personRepository->getDumpByOrganizationByType($campusId, 'student', $coordinatorArray);
        } else {
            if (strtolower($list) == 'staff') {
                $coordinatorsRS = $this->organizationRoleRepository->findBy(array(
                    'organization' => $organization
                ));
                $coordinatorArray = [];
                foreach ($coordinatorsRS as $coordinator) {
                    $coordinatorArray[] = $coordinator->getPerson()->getId();
                }
                $allFaculties = $this->personRepository->getDumpByOrganizationByType($campusId, 'faculty', $coordinatorArray);
            }
        }
        $usersList = array();
        if (strtolower($list) == 'all') {
            $coordinators = $this->organizationRoleRepository->getCoordinators($campusId);
            foreach ($coordinators as $coordinator) {
                $person = $coordinator->getPerson();
                $contactInfo = $this->contactInfoRepository->getCoalescedContactInfo($person->getContacts());
                $role = $coordinator->getRole();
                $primaryEmail = $this->usersHelperService->checkNullResponse($contactInfo->getPrimaryEmail());
                $personArray['id'] = $person->getId();
                $personArray['title'] = $person->getTitle();
                $personArray['firstname'] = $person->getFirstname();
                $personArray['lastname'] = $person->getLastname();
                $personArray['externalId'] = $person->getExternalId();
                $usersList[] = $this->createUsersDto($personArray, $campusId, $primaryEmail, 'coordinator', $role->getName(), null);
            }
        }

        if (!empty($allFaculties)) {

            // Get All faculty IDS

            $getFacultyPermissions = $this->personRepository->getPermissionsByUserIds($this->getFacultyIds($allFaculties), $campusId);
            $facultysPermissions = $this->getFacultyPermissions($getFacultyPermissions);
            foreach ($allFaculties as $staffFaculty) {
                $person = $staffFaculty['person'];
                $contacts = $person['contacts'];

                if (count($contacts) > 0) {
                    end($contacts);
                    $key = key($contacts);
                    $contact = $contacts[$key];
                    $primaryEmail = $this->usersHelperService->checkNullResponse($contact['primaryEmail']);
                } else {
                    $primaryEmail = '';
                }
                $personId = $person['id'];
                if (isset($facultysPermissions[$personId])){
                    $permissions = $this->getUserPermissionDto($facultysPermissions[$personId]);
                }else{
                    $permissions = [];
                }
                $usersList[] = $this->createUsersDto($person, $campusId, $primaryEmail, ucfirst('Staff/Faculty'), '', $permissions);

            }
        }
        if (!empty($allStudents)) {
            foreach ($allStudents as $student) {
                $person = $student['person'];
                $contacts = $person['contacts'];
                if (count($contacts) > 0) {
                    end($contacts);
                    $key = key($contacts);
                    $contact = $contacts[$key];
                    $primaryEmail = $this->usersHelperService->checkNullResponse($contact['primaryEmail']);
                } else {
                    $primaryEmail = '';
                }
                $usersList[] = $this->createUsersDto($person, $campusId, $primaryEmail, ucfirst('student'), '', null);
            }
        }
        return $usersList;
    }

    /**
     * Create userDto
     *
     * @param array $person
     * @param integer $campusId
     * @param string $primaryEmail
     * @param string $userType
     * @param string $role
     * @param array $permissions
     * @return UserDTO
     */
    private function createUsersDto($person, $campusId, $primaryEmail, $userType, $role, $permissions){
        $usersDto = new UsersDto();
        $usersDto->setUserId($person['id']);
        $usersDto->setCampusId($campusId);
        $usersDto->setTitle($person['title']);
        $usersDto->setFirstName($person['firstname']);
        $usersDto->setLastName($person['lastname']);
        $usersDto->setEmail($primaryEmail);
        $usersDto->setExternalId($person['externalId']);
        $usersDto->setUserType($userType);
        $usersDto->setRole($role);
        $usersDto->setPermissions($permissions);
        return $usersDto;
    }

    /**
     * Get permissions for faculty
     *
     * @param array $permissions
     * @return array
     */
    private function getFacultyPermissions($permissions)
    {
        $facultyPermissions = array();
        if (!empty($permissions)) {
            foreach ($permissions as $facultyPermission) {
                $groupPersonId = $facultyPermission['group_person_id'];
                $coursePersonId = $facultyPermission['course_person_id'];
                $permissionId = $facultyPermission['permission_id'];
                $permissionName = $facultyPermission['permission_name'];
                if (!empty($coursePersonId)) {
                    if (!array_key_exists($coursePersonId, $facultyPermissions)) {
                        $facultyPermissions[$coursePersonId] = array($permissionId => $permissionName);
                    } else {
                        if (!array_key_exists($permissionId, $facultyPermissions[$coursePersonId])) {
                            $facultyPermissions[$coursePersonId] = $facultyPermissions[$coursePersonId] + array($permissionId => $permissionName);
                        }
                    }
                }
                if (!empty($groupPersonId)) {
                    if (!array_key_exists($groupPersonId, $facultyPermissions)) {
                        $facultyPermissions[$groupPersonId] = array($permissionId => $permissionName);
                    } else {
                        if (!array_key_exists($permissionId, $facultyPermissions[$groupPersonId])) {
                            $facultyPermissions[$groupPersonId] = $facultyPermissions[$groupPersonId] + array($permissionId => $permissionName);
                        }
                    }
                }
            }
        }
        return $facultyPermissions;

    }

    /**
     * Extract faculty id values from an array of faculty
     *
     * @param array $allFacultys
     * @return array
     */
    private function getFacultyIds($allFacultys)
    {
        $facultyIdArr = array();
        if (!empty($allFacultys)) {
            foreach ($allFacultys as $staffFaculty) {
                $person = $staffFaculty[UsersConstant::FIELD_PERSON];
                if ($person[UsersConstant::FIELD_ID]) {
                    if (!in_array($person[UsersConstant::FIELD_ID], $facultyIdArr)) {
                        $facultyIdArr[] = $person[UsersConstant::FIELD_ID];
                    }
                }
            }
        }
        return $facultyIdArr;
    }

    /**
     * Create a PermissionDTO for the specified user permissions
     *
     * @param array $userPermissions
     * @return array
     */
    public function getUserPermissionDto($userPermissions)
    {
        $permissions = array();
        if (!empty($userPermissions)) {
            foreach ($userPermissions as $permissionId => $permissionName) {
                $permissionsDto = new PermissionDto();
                $permissionsDto->setPermissionId($permissionId);
                $permissionsDto->setPermissionName($permissionName);
                $permissions[] = $permissionsDto;
            }
        }
        return $permissions;
    }

    /**
     * @param $organizationId
     * @param $userType
     * @param null|string $exclude - Person id to be excluded from search
     * @param null|string $searchText - search based on $searchText
     * @param null|string $participantFilter - (all, participants, non-participants)
     * @param null|string $sortBy - order the results
     * @param null|int $pageNumber
     * @param null|int $limit
     * @param null|bool $checkAccessToOrganization
     * @param null|string $activeFilter
     * @return array
     */
    public function getUsers($organizationId, $userType, $exclude = NULL, $searchText = NULL, $participantFilter = NULL, $sortBy = NULL, $pageNumber = NULL, $limit = NULL, $checkAccessToOrganization = NULL , $activeFilter =  NUll)
    {
        switch ($userType) {
            case "coordinator" :
                $users = $this->usersHelperService->getCoordinator($organizationId);
                break;
            case "service_accounts" :
                $users = $this->usersHelperService->getServiceAccounts($organizationId);
                break;
            case "faculty":
            case "student":

                $users = $this->listOrganizationUsersByType($organizationId, $userType, $exclude, $searchText, $participantFilter, $sortBy, $pageNumber, $limit, $checkAccessToOrganization, $activeFilter);
                break;
            default:
                throw new SynapseValidationException("Invalid Type");
                break;

        }
        return $users;
    }

    /**
     * Get student hierarchy for organization and specified filter.
     *
     * @param int $campusId
     * @param $filter
     * @return array|string
     */
    public function getHierarchyStudents($campusId, $filter)
    {
        $this->logger->debug("Get Hierarchy Students based on campusId" . $campusId . "and filter" . $filter);
        $campus = $this->organizationRepository->find($campusId);
        $secondaryParentId = $campus->getParentOrganizationId();
        $campuses = $this->organizationRepository->listCampuses($secondaryParentId, '3');
        $secondaryCampuses = array_column($campuses, UsersConstant::ORG_ID);
        if (($key = array_search($campusId, $secondaryCampuses)) !== false) {
            unset($secondaryCampuses[$key]);
        }
        $studentList = $this->usersHelperService->getStudentsList($secondaryCampuses, $filter);
        $this->logger->info("Get Hierarchy Student List");
        return $studentList;
    }

    /**
     * Send invitation email to user
     *
     * @param int $organizationId
     * @param int $personId
     * @return array|null
     */
    public function sendInvitation($organizationId, $personId)
    {
        $userDetails = $this->personRepository->getUsersByUserIds($personId);
        $this->usersHelperService->validateOrganization($organizationId);

        if (empty($userDetails)) {
            throw new SynapseValidationException("Person requested was not found.");
        } else {
            return $this->emailPasswordService->sendEmailWithInvitationLink($personId, $userDetails[0]['username']);
        }
    }

    /**
     * Promotes a faculty user to a coordinator role
     *
     * @param UserDTO $userDTO
     * @throws SynapseValidationException
     * @return UserDTO
     */
    public function promoteFacultyToCoordinator(UserDTO $userDTO)
    {
        $userId = $userDTO->getId();
        $campusId = $userDTO->getCampusId();
        $person = $this->personRepository->find($userId, new SynapseValidationException('Person Not Found.'));
        $organization = $this->usersHelperService->validateOrganization($campusId);
        $role = $this->usersHelperService->validateRole($userDTO->getRoleid());

        $organizationRole = $this->organizationRoleRepository->findOneBy(array(
            'organization' => $campusId,
            'person' => $userId
        ));

        if ($organizationRole) {
            $organizationRole->setRole($role);
        } else {
            $organizationCoordinator = new OrganizationRole();
            $organizationCoordinator->setOrganization($organization);
            $organizationCoordinator->setPerson($person);
            $organizationCoordinator->setRole($role);
            $this->organizationRoleRepository->createCoordinator($organizationCoordinator);
        }
        $this->organizationRoleRepository->flush();

        return $userDTO;
    }

    /**
     * Get a User
     *
     * @param int $userId
     * @param int $organizationId
     * @param string $userType
     * @throws SynapseValidationException
     * @return array
     */
    public function getUser($userId, $organizationId, $userType = '')
    {
        $userDetails = $this->personRepository->getUsersByUserIds($userId);

        $userContact = array();
        $type = null;

        if (empty($userDetails)) {
            throw new SynapseValidationException("Person Not Found.");
        } else {
            foreach ($userDetails as $userDetail) {
                $isMobile = true;
                if (!($phoneNumber = $userDetail['primary_mobile'])) {
                    $phoneNumber = $userDetail['home_phone'];
                    $isMobile = false;
                }

                $coordinators = $faculty = $student = null;
                if ($userType == 'student') {
                    $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);
                    $student = $this->orgPersonStudentYearRepository->findOneBy(array(
                        "organization" => $organizationId,
                        "person" => $userId,
                        "orgAcademicYear" => $orgAcademicYearId
                    ));
                } else {
                    $coordinators = $this->organizationRoleRepository->findBy(array(
                        "organization" => $organizationId,
                        "person" => $userId
                    ));

                    if (empty($coordinators)) {
                        $faculty = $this->orgPersonFacultyRepository->findOneBy(array(
                            "organization" => $organizationId,
                            "person" => $userId
                        ));
                    }
                }

                $googleEmailId = NULL;
                $roleId = NULL;
                $roleName = NULL;
                $isParticipant = 0;
                if ($coordinators) {
                    $type = "coordinator";
                    $coordinator = $coordinators[0];
                    $roles = $this->roleLangRepository->findBy(array(
                        'role' => $coordinator->getRole()
                            ->getId()
                    ));
                    if (isset($roles)) {
                        foreach ($roles as $role) {
                            $roleName = $role->getRolename();
                            $roleId = $role->getRole()->getId();
                        }
                    }
                    $isActive = 1;
                    $orgPersonFaculty = $this->orgPersonFacultyRepository->findOneBy(array(
                        "organization" => $organizationId,
                        "person" => $userId
                    ));
                    $googleEmailId = $orgPersonFaculty->getGoogleEmailId();
                } elseif ($faculty) {
                    $type = "faculty";
                    $isActive = ($faculty->getStatus() === '0') ? 0 : 1;
                    $googleEmailId = $faculty->getGoogleEmailId();
                } else {
                    if ($student) {
                        $type = "student";
                        $isActive = ($student->getIsActive()) ? 1 : 0;
                        $isParticipant = is_object($student) ? 1 : 0;
                    } else {
                        $isActive = '';
                    }
                }

                $info = [
                    'id' => $userDetail['user_id'],
                    'firstname' => $userDetail['user_firstname'],
                    'lastname' => $userDetail['user_lastname'],
                    'title' => $this->usersHelperService->checkNullResponse($userDetail['title']),
                    'email' => $userDetail['username'],
                    'externalid' => $this->usersHelperService->checkNullResponse($userDetail['student_id']),
                    'welcome_email_sentDate' => $this->usersHelperService->checkNullResponse($userDetail['email_sent_date']),
                    'is_active' => $isActive,
                    'phone' => $this->usersHelperService->checkNullResponse($phoneNumber),
                    'ismobile' => $isMobile,
                    'user_type' => $type,
                    'role' => $roleName,
                    'roleid' => $roleId,
                    'ldap_username' => $userDetail['auth_username'],
                    'google_email_id' => $googleEmailId
                ];

                if ($type == 'student') {
                    $info['is_participating'] = $isParticipant;
                }

                array_push($userContact, $info);
            }
        }
        return $userContact;
    }

    /**
     * Delete an existing user
     *
     * @param int $userId
     * @param int $organizationId
     * @param string $type
     * @return bool
     */
    public function deleteUser($userId, $organizationId, $type)
    {
        $person = $this->personRepository->find($userId, new SynapseValidationException('Person Not Found.'));
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException('Organization ID Not Found'));

        if ($type == 'coordinator') {
            $coordinator = $this->organizationRoleRepository->findOneBy([
                'organization' => $organization->getId(),
                'person' => $person
            ], new SynapseValidationException('Coordinator Role not found'));

            $this->organizationRoleRepository->remove($coordinator);
            $this->personRepository->flush();

        } elseif ($type == 'faculty') {
            if ($person->getIsLocked() == 'y') {
                throw new SynapseValidationException('We are unable to delete users who have activity or academic data associated with their Mapworks account.');
            }
            $faculty = $this->orgPersonFacultyRepository->findOneBy([
                'organization' => $organization->getId(),
                'person' => $person
            ], new SynapseValidationException('Faculty Role not found'));

            $this->orgPersonFacultyRepository->remove($faculty);
            $this->personRepository->flush();

        } elseif ($type == 'student') {
            if ($person->getIsLocked() == 'y') {
                throw new SynapseValidationException('We are unable to delete users who have activity or academic data associated with their Mapworks account.');
            }
            $student = $this->orgPersonStudentRepository->findOneBy([
                'organization' => $organization->getId(),
                'person' => $person
            ], new SynapseValidationException('Student Role not found'));

            $this->groupService->removeStudentSystemGroup($student);
            $this->orgPersonStudentRepository->remove($student);
            $this->orgGroupStudentRepository->deleteBulkStudentEnrolledGroups($userId, $organizationId);
            $this->personRepository->flush();

        } else {
            throw new SynapseValidationException('Invalid Type');
        }

        $personRoles = $this->personService->getPerson($person->getId());

        if (empty($personRoles['person_type'])) {
            $loggedInUserId = $this->securityContext->getToken()
                ->getUser()
                ->getId();
            $deleteCode = rand(1000, 9999);

            // Delete cache entry
            $cachedUsers = $this->cache->fetch("organization.{$organization->getId()}.people");
            unset($cachedUsers[$person->getId()]);
            $this->cache->save("organization.{$organization->getId()}.people", $cachedUsers);

            // Delete contacts
            foreach ($person->getContacts() as $contact) {
                $contact->setPrimaryEmail($loggedInUserId . '-' . $deleteCode . '-' . $contact->getPrimaryEmail());
                $this->contactInfoRepository->update($contact);
                $this->contactInfoRepository->remove($contact);
                $this->contactInfoRepository->flush();
            }
            // Rename user
            $person->setExternalId($loggedInUserId . '-' . $deleteCode . '-' . $person->getExternalId());
            $person->setUsername($loggedInUserId . '-' . $deleteCode . '-' . $person->getUsername());
            // Update and flush
            $this->personRepository->update($person);
            // Delete and flush
            $this->personRepository->remove($person);
            $this->personRepository->flush();
        }
        return true;
    }

    /**
     * Create a primary tier user
     *
     * @param UserDTO $userDTO
     * @throws SynapseValidationException
     * @return UserDTO
     */
    public function createPrimaryTierUser(UserDTO $userDTO)
    {
        $personEmail = $userDTO->getEmail();
        $personPhone = $userDTO->getPhone();
        $personFacultyId = $userDTO->getFacultyId();
        $tier = $this->organizationRepository->find($userDTO->getTierId());

        if ((!$tier) || ($tier->getTier() != 1)) {
            throw new SynapseValidationException("Tier Not Found.");
        }

        $person = $this->usersHelperService->validateExternalId($personFacultyId, $tier, NULL, NULL, 'faculty', $personEmail);
        if (!$person) {
            $person = new Person();
            $person->setFirstname($userDTO->getFirstname());
            $person->setLastname($userDTO->getLastname());
            $person->setUsername($personEmail);
            $person->setTitle($userDTO->getTitle());
            $person->setOrganization($tier);
            $person->setExternalId($personFacultyId);
            $contact = new ContactInfo();
            if ($userDTO->getIsmobile()) {
                $contact->setPrimaryMobile($personPhone);
            } else {
                $contact->setHomePhone($personPhone);
            }
            $contact->setPrimaryEmail($personEmail);
            $contactErrors = $this->validator->validate($contact);
            if (count($contactErrors) > 0) {
                $errorsString = $contactErrors[0]->getMessage();
                throw new SynapseValidationException($errorsString);
            }
            $person->addContact($contact);
            $this->personRepository->createPerson($person);
        }
        $organizationUsers = new OrgUsers();
        $organizationUsers->setPerson($person);
        $organizationUsers->setOrganization($tier);
        $this->orgUsersRepository->persist($organizationUsers);

        // Adding tier user as staff also
        $orgFaculty = new OrgPersonFaculty();
        $orgFaculty->setPerson($person);
        $orgFaculty->setOrganization($tier);
        $this->orgPersonFacultyRepository->persist($orgFaculty);

        $this->personRepository->flush();
        $userDTO->setId($person->getId());
        $people = $this->cache->fetch("organization.{$tier->getId()}.people");
        $people = $people ? $people : [];
        $people[$person->getId()] = $person->getExternalId();
        $this->cache->save("organization.{$tier->getId()}.people", $people);
        return $userDTO;
    }

    /**
     * Create a secondary tier user
     *
     * @param UserDTO $userDTO
     * @throws SynapseValidationException
     * @return UserDTO
     */
    public function createSecondaryTierUser(UserDTO $userDTO)
    {
        $userEmail = $userDTO->getEmail();
        $userPhone = $userDTO->getPhone();
        $userFacultyId = $userDTO->getFacultyId();
        $tier = $this->organizationRepository->find($userDTO->getTierId());

        if ((!$tier) || ($tier->getTier() != 2)) {
            throw new SynapseValidationException("Tier Not Found.");
        }

        $person = $this->usersHelperService->validateExternalId($userFacultyId, $tier, NULL, NULL, 'faculty', $userEmail);
        if (!$person) {
            $person = new Person();
            $person->setFirstname($userDTO->getFirstname());
            $person->setLastname($userDTO->getLastname());
            $person->setUsername($userEmail);
            $person->setTitle($userDTO->getTitle());
            $person->setOrganization($tier);
            $person->setExternalId($userFacultyId);
            $contact = new ContactInfo();
            if ($userDTO->getIsmobile()) {
                $contact->setPrimaryMobile($userPhone);
            } else {
                $contact->setHomePhone($userPhone);
            }
            $contact->setPrimaryEmail($userEmail);
            $contactErrors = $this->validator->validate($contact);
            if (count($contactErrors) > 0) {
                $errorsString = $contactErrors[0]->getMessage();
                throw new SynapseValidationException($errorsString);
            }
            $person->addContact($contact);
            $this->personRepository->createPerson($person);
        }
        $orgUsers = new OrgUsers();
        $orgUsers->setPerson($person);
        $orgUsers->setOrganization($tier);
        $this->orgUsersRepository->persist($orgUsers);

        // Adding user as staff also
        $organizationFaculty = new OrgPersonFaculty();
        $organizationFaculty->setPerson($person);
        $organizationFaculty->setOrganization($tier);
        $this->orgPersonFacultyRepository->persist($organizationFaculty);
        $userDTO->setId($person->getId());

        $people = $this->cache->fetch("organization.{$tier->getId()}.people");
        $people = $people ? $people : [];
        $people[$person->getId()] = $person->getExternalId();
        $this->cache->save("organization.{$tier->getId()}.people", $people);
        return $userDTO;
    }

    /**
     * Update a primary tier user
     *
     * @param UserDTO $userDTO
     * @throws SynapseValidationException
     * @return UserDTO
     */
    public function updatePrimaryTierUser(UserDTO $userDTO)
    {
        $this->logger->debug(" Updating Primar Tier User  -  " . $this->loggerHelperService->getLog($userDTO));

        $person = $this->personRepository->find($userDTO->getId(), new SynapseValidationException('Person Not Found.'));
        $tier = $this->organizationRepository->find($userDTO->getTierId());

        if ((!$tier) || ($tier->getTier() != 1)) {
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_PRIMARY_TIER_NOT_FOUND, TierConstant::ERROR_PRIMARY_TIER_NOT_FOUND_KEY);
        }

        $this->usersHelperService->validateExternalId($userDTO->getFacultyId(), $tier, $userDTO->getId(), NULL, UsersConstant::FILTER_FACULTY, $userDTO->getEmail());

        $person->setFirstname($userDTO->getFirstname());
        $person->setLastname($userDTO->getLastname());
        $person->setUsername($userDTO->getEmail());
        $person->setExternalId($userDTO->getFacultyId());
        $person->setTitle($userDTO->getTitle());
        $contact = $person->getContacts()->first();
        if ($contact) {
            if ($userDTO->getIsmobile()) {
                $contact->setPrimaryMobile($userDTO->getPhone());
            } else {
                $contact->setHomePhone($userDTO->getPhone());
            }
            $contact->setPrimaryemail($userDTO->getEmail());
            $contactErrors = $this->validator->validate($contact);
            if (count($contactErrors) > 0) {
                $errorsString = $contactErrors[0]->getMessage();
                throw new SynapseValidationException($errorsString);
            }
        }

        $this->personRepository->flush();
        $this->logger->info("Primary Tier User updated");
        $userDTO->setId($person->getId());
        return $userDTO;
    }

    /**
     * Update a secondary tier user
     *
     * @param UserDTO $userDTO
     * @throws SynapseValidationException
     * @return UserDTO
     */
    public function updateSecondaryTierUser(UserDTO $userDTO)
    {
        $this->logger->debug(" Updating Secondary Tier User  -  " . $this->loggerHelperService->getLog($userDTO));

        $person = $this->personRepository->find($userDTO->getId(), new SynapseValidationException('Person Not Found.'));

        $tier = $this->organizationRepository->find($userDTO->getTierId());

        if ((!$tier) || ($tier->getTier() != 2)) {
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_SECONDARY_TIER_NOT_FOUND, TierConstant::ERROR_SECONDARY_TIER_NOT_FOUND_KEY);
        }

        $this->usersHelperService->validateExternalId($userDTO->getFacultyId(), $tier, $userDTO->getId(), NULL, UsersConstant::FILTER_FACULTY, $userDTO->getEmail());

        $person->setFirstname($userDTO->getFirstname());
        $person->setLastname($userDTO->getLastname());
        $person->setUsername($userDTO->getEmail());
        $person->setExternalId($userDTO->getFacultyId());
        $person->setTitle($userDTO->getTitle());
        $contact = $person->getContacts()->first();
        if ($contact) {
            if ($userDTO->getIsmobile()) {
                $contact->setPrimaryMobile($userDTO->getPhone());
            } else {
                $contact->setHomePhone($userDTO->getPhone());
            }
            $contact->setPrimaryemail($userDTO->getEmail());
            $contactErrors = $this->validator->validate($contact);
            if (count($contactErrors) > 0) {
                $errorsString = $contactErrors[0]->getMessage();
                throw new SynapseValidationException($errorsString);
            }
        }
        $this->personRepository->flush();
        $this->logger->info("Updated Secondary Tier User");
        $userDTO->setId($person->getId());
        return $userDTO;
    }

    /**
     * List user dashboard for tier.
     *
     * @param $loggedUser
     * @return SecondaryTiersDto
     */
    public function listTierUserDashboard($loggedUser)
    {
        $this->logger->info("list Tier Dashboard");

        $tierUserInfo = $this->orgUsersRepository->findOneBy(array(
            'person' => $loggedUser
        ));
        if (!isset($tierUserInfo)) {
            throw new ValidationException([
                UsersConstant::ERROR_NOT_TIER_USER
            ], UsersConstant::ERROR_NOT_TIER_USER, 'not_tier_user');
        }

        $tierId = $tierUserInfo->getOrganization()->getId();
        $tierlevel = $tierUserInfo->getOrganization()->getTier();
        $tierUserDashboard = ($tierlevel == '1') ? $this->usersHelperService->primaryTierUserDashboard($tierId) : $this->usersHelperService->secondaryTierUserDashboard($tierId);
        $this->logger->info("list Tier Dashboard for loggedin User");
        return $tierUserDashboard;
    }

    /**
     * Send bulk user invite emails
     *
     * @param int $campusId
     * @param string $type
     * @return array
     */
    public function bulkUserInvite($campusId, $type)
    {
        $this->usersHelperService->validateOrganization($campusId);

        // Removed Academic Year check
        if ($type == 'coordinator') {
            $getNonLoggedInUserList = $this->organizationRoleRepository->getNonLoggedInCoordinatorCount($campusId);
        } else {
            $getNonLoggedInUserList = $this->orgPersonFacultyRepository->getNonLoggedInFacultyCount($campusId);

        }

        $noOfUserLinkSend = $getNonLoggedInUserList;
        $jobNumber = uniqid();
        $job = new BulkInviteJob();
        $job->args = array(
            'jobNumber' => $jobNumber,
            'organization' => $campusId,
            'type' => $type
        );
        $this->resque->enqueue($job, true);

        $bulkUserResp = [
            'no_of_users_invited' => $noOfUserLinkSend
        ];
        return $bulkUserResp;
    }

    /**
     * List information based on a tier ID
     *
     * @param int $tierId
     * @throws SynapseValidationException
     * @return SecondaryTiersDto
     */
    public function listPrimaryTierDetails($tierId)
    {
        $tierPrimaryInfos = $this->organizationRepository->findBy(array(
            'tier' => '2',
            'parentOrganizationId' => $tierId
        ));
        $tierPrimaryInfodet = $this->organizationLangRepository->findOneBy(array(
            'organization' => $tierId
        ));

        if (!isset($tierPrimaryInfos)) {
            throw new SynapseValidationException('Not a Tier');
        }
        $tierDtoArray = [];
        $listTierDto = null;
        foreach ($tierPrimaryInfos as $tierInfo) {
            $tierDto = new TierDto();
            $listTierDto = new SecondaryTiersDto();
            $listTierDto->setPrimaryTierId($tierId);
            $listTierDto->setPrimaryTierName($tierPrimaryInfodet->getOrganizationName());
            $tierInfodet = $this->organizationLangRepository->findOneBy(array(
                'organization' => $tierInfo->getId()
            ));
            $tierCampusInfo = $this->organizationRepository->findBy(array(
                'tier' => '3',
                'parentOrganizationId' => $tierInfo->getId()
            ));
            if (!empty($tierInfodet)) {
                $secondaryTierId = $tierInfodet->getOrganization()->getId();
                $tierDto->setSecondaryTierId($secondaryTierId);
                $tierDto->setSecondaryTierName($tierInfodet->getOrganizationName());
                $tierDto->setDescription($tierInfodet->getDescription());
                $tierDto->setTotalCampus(count($tierCampusInfo));
                $campusArray = [];
                if (!empty($tierCampusInfo)) {
                    foreach ($tierCampusInfo as $tierCampus) {
                        $tierCampusInfodet = $this->organizationLangRepository->findOneBy(array(
                            'organization' => $tierCampus->getId()
                        ));
                        $campusDto = new CampusDto();
                        $campusDto->setId($tierCampusInfodet->getOrganization()
                            ->getId());
                        $campusDto->setCampusName($tierCampusInfodet->getOrganizationName());
                        $campusArray[] = $campusDto;
                    }
                    $tierDto->setCampuses($campusArray);
                }
                $tierDtoArray[] = $tierDto;
            } else {
                throw new SynapseValidationException("Tier Not Found.");
            }
            $listTierDto->setSecondaryTiers($tierDtoArray);
        }
        return $listTierDto;
    }

    /**
     * List User Details by Primary Tier id
     *
     * @param int $primaryTierId
     * @param int $secondaryTierId
     * @param int $campusId
     * @throws SynapseValidationException
     * @return array $responseArray
     */
    public function listUserDetails($primaryTierId, $secondaryTierId, $campusId)
    {
        $tierPrimaryInfos = $this->organizationRepository->findOneBy(array(
            'tier' => '1',
            'id' => $primaryTierId
        ));
        if (!isset($tierPrimaryInfos)) {
            throw new SynapseValidationException('Not a Tier');
        }
        if (!empty($campusId)) {
            $responseArray = $this->campusUserlist($campusId);
        } elseif (!empty($secondaryTierId)) {
            $responseArray = $this->secondaryTierCampusUserList($secondaryTierId);
        } else {
            $tierPrimaryInfodet = $this->organizationLangRepository->findOneBy(array(
                'organization' => $primaryTierId
            ));
            $responseArray = array();
            $primaryuserList = $this->getAllUserList($primaryTierId, 'all');
            if (!empty($primaryuserList)) {
                foreach ($primaryuserList as $userList) {
                    $primaryUserTempArray = array();
                    $primaryUserTempArray['id'] = $userList->getUserId();
                    $primaryUserTempArray['campus_id'] = $userList->getCampusId();
                    $primaryUserTempArray['firstname'] = $userList->getFirstName();
                    $primaryUserTempArray['lastname'] = $userList->getLastName();
                    $primaryUserTempArray['email'] = $userList->getEmail();
                    $primaryUserTempArray['role'] = $userList->getRole();
                    $primaryUserTempArray['user_type'] = $userList->getUserType();
                    $primaryUserTempArray['external_id'] = $userList->getExternalId();
                    $primaryUserTempArray['tier_level'] = 'primary';
                    $primaryUserTempArray['primary_tier_name'] = $tierPrimaryInfodet->getOrganizationName();
                    array_push($responseArray, $primaryUserTempArray);
                }
            }
            $secondaryTierIds = $this->organizationRepository->listCampuses($primaryTierId, '2');
            $secondaryOrganizationIds = array_column($secondaryTierIds, 'orgId');
            $userArray = array();
            $campusArray = array();
            foreach ($secondaryOrganizationIds as $secondaryOrganization) {
                $seconInfo = $this->organizationRepository->getTierLevelOrder($secondaryOrganization);
                $secondaryInfo = call_user_func_array('array_merge', $seconInfo);
                $userList = $this->getAllUserList($secondaryOrganization, 'all');
                foreach ($userList as $userList) {
                    $tempArray = array();
                    $tempArray['id'] = $userList->getUserId();
                    $tempArray['campus_id'] = $userList->getCampusId();
                    $tempArray['firstname'] = $userList->getFirstName();
                    $tempArray['lastname'] = $userList->getLastName();
                    $tempArray['email'] = $userList->getEmail();
                    $tempArray['role'] = $userList->getRole();
                    $tempArray['user_type'] = $userList->getUserType();
                    $tempArray['external_id'] = $userList->getExternalId();
                    $tempArray['tier_level'] = 'secondary';
                    $tempArray['primary_tier_name'] = $secondaryInfo['primaryName'];
                    $tempArray['secondary_tier_name'] = $secondaryInfo['secondaryName'];
                    array_push($userArray, $tempArray);
                }

                $campusIds = $this->organizationRepository->listCampuses($secondaryOrganization, '3');
                $campuses = array_column($campusIds, 'orgId');

                foreach ($campuses as $campus) {
                    $campusInfos = $this->organizationRepository->getHierarchyOrder($campus);
                    $campusInfo = call_user_func_array('array_merge', $campusInfos);
                    $campusLists = $this->getAllUserList($campus, 'all');
                    foreach ($campusLists as $campusList) {
                        $campusListArray = array();
                        $campusListArray['id'] = $campusList->getUserId();
                        $campusListArray['campus_id'] = $campusList->getCampusId();
                        $campusListArray['firstname'] = $campusList->getFirstName();
                        $campusListArray['lastname'] = $campusList->getLastName();
                        $campusListArray['email'] = $campusList->getEmail();
                        $campusListArray['role'] = $campusList->getRole();
                        $campusListArray['user_type'] = $campusList->getUserType();
                        $campusListArray['external_id'] = $campusList->getExternalId();
                        $campusListArray['tier_level'] = 'campus';
                        $campusListArray['primary_tier_name'] = $campusInfo['primaryName'];
                        $campusListArray['secondary_tier_name'] = $campusInfo['secondaryName'];
                        $campusListArray['campus_name'] = $campusInfo['campusName'];
                        array_push($campusArray, $campusListArray);
                    }
                }
                $responseArray = array_merge($responseArray, $userArray, $campusArray);
            }
        }
        return $responseArray;
    }

    /**
     * Gets the user list for the admin site user search.
     *
     * @param int $organizationId
     * @param string $searchText
     * @param int $pageNumber
     * @param int $recordCount
     * @return array $header
     * @deprecated - Please use PersonService::getMapworksPersons()
     */
    public function getAdminSiteUserSearchResult($organizationId, $searchText, $pageNumber, $recordCount)
    {
        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }

        $recordCount = (int)$recordCount;
        if (!$recordCount) {
            $recordCount = SynapseConstant::DEFAULT_RECORD_COUNT;
        }

        $startPoint = ($pageNumber * $recordCount) - $recordCount;
        $startPoint = (int)$startPoint;

        //Get the current academic year Id and the users for that organization. If a current academic year ID is not present, the search will only return faculty and coordinators.
        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $users = $this->personRepository->getUsersBySearchText($organizationId, $orgAcademicYearId, $searchText, $startPoint, $recordCount);
        $totalRecords = count($this->personRepository->getUsersBySearchText($organizationId, $orgAcademicYearId, $searchText));
        $totalPageCount = ceil($totalRecords / $recordCount);

        $response = [];
        $response['total_records'] = $totalRecords;
        $response['total_pages'] = $totalPageCount;
        $response['records_per_page'] = $recordCount;
        $response['current_page'] = $pageNumber;
        $response['user_list'] = $users;

        return $response;
    }

    /**
     * List user campus
     *
     * @param integer $campusId
     * @throws SynapseValidationException
     * @return array
     */
    public function campusUserlist($campusId)
    {
        $responseArray = array();
        if (!empty($campusId)) {
            $campusInfos = $this->organizationRepository->getHierarchyOrder($campusId);
            $campusInfo = call_user_func_array('array_merge', $campusInfos);
            $campusLists = $this->getAllUserList($campusId, 'all');

            foreach ($campusLists as $campusList) {
                $campusListArray = array();
                $campusListArray['id'] = $campusList->getUserId();
                $campusListArray['campus_id'] = $campusList->getCampusId();
                $campusListArray['firstname'] = $campusList->getFirstName();
                $campusListArray['lastname'] = $campusList->getLastName();
                $campusListArray['email'] = $campusList->getEmail();
                $campusListArray['role'] = $campusList->getUserType();
                $campusListArray['tier_level'] = 'campus';
                $campusListArray['primary_tier_name'] = $campusInfo['primaryName'];
                $campusListArray['secondary_tier_name'] = $campusInfo['secondaryName'];
                $campusListArray['campus_name'] = $campusInfo['campusName'];
                array_push($responseArray, $campusListArray);
            }
        } else {

            throw new SynapseValidationException("CampusId Not Found");
        }
        return $responseArray;
    }

    /**
     * List secondary tier campus for the user
     *
     * @param integer $secondaryTierId
     * @return mixed
     */
    public function secondaryTierCampusUserList($secondaryTierId)
    {
        $campusArray = array();
        $userArray = array();
        $seconInfo = $this->organizationRepository->getTierLevelOrder($secondaryTierId);
        $secondaryInfo = call_user_func_array('array_merge', $seconInfo);
        $userListObj = $this->getAllUserList($secondaryTierId, 'all');
        foreach ($userListObj as $userList) {
            $userListArray = array();
            $userListArray['id'] = $userList->getUserId();
            $userListArray['campus_id'] = $userList->getCampusId();
            $userListArray['firstname'] = $userList->getFirstName();
            $userListArray['lastname'] = $userList->getLastName();
            $userListArray['email'] = $userList->getEmail();
            $userListArray['role'] = $userList->getUserType();
            $userListArray['tier_level'] = 'secondary';
            $userListArray['primary_tier_name'] = $secondaryInfo['primaryName'];
            $userListArray['secondary_tier_name'] = $secondaryInfo['secondaryName'];
            array_push($userArray, $userListArray);
        }
        $campusIds = $this->organizationRepository->listCampuses($secondaryTierId, '3');
        $campuses = array_column($campusIds, 'orgId');
        foreach ($campuses as $campus) {
            $campusInfos = $this->organizationRepository->getHierarchyOrder($campus);
            $campusInfo = call_user_func_array('array_merge', $campusInfos);
            $campusLists = $this->getAllUserList($campus, 'all');
            foreach ($campusLists as $campusList) {
                $campusListArray = array();
                $campusListArray['id'] = $campusList->getUserId();
                $campusListArray['campus_id'] = $campusList->getCampusId();
                $campusListArray['firstname'] = $campusList->getFirstName();
                $campusListArray['lastname'] = $campusList->getLastName();
                $campusListArray['email'] = $campusList->getEmail();
                $campusListArray['role'] = $campusList->getUserType();
                $campusListArray['tier_level'] = 'campus';
                $campusListArray['primary_tier_name'] = $campusInfo['primaryName'];
                $campusListArray['secondary_tier_name'] = $campusInfo['secondaryName'];
                $campusListArray['campus_name'] = $campusInfo['campusName'];
                array_push($campusArray, $campusListArray);
            }
        }
        $responseArray = array_merge($userArray, $campusArray);
        return $responseArray;
    }


    /**
     * Deletes a service account
     *
     * @param integer $serviceAccountId
     * @param integer $organizationId
     * @return void
     * @throws SynapseValidationException
     */
    public function deleteServiceAccount($serviceAccountId, $organizationId)
    {

        $organizationRoleObject = $this->organizationRoleRepository->findOneBy(['person' => $serviceAccountId, 'organization' => $organizationId]);
        if ($organizationRoleObject) {
            $serviceAccountRoleObject = $this->roleLangRepository->findOneBy(['roleName' => SynapseConstant::SERVICE_ACCOUNT_ROLE_NAME]);
            if ($serviceAccountRoleObject) {
                $serviceAccountRoleId = $serviceAccountRoleObject->getRole()->getId();
            } else {
                throw new SynapseValidationException('Service account role does not exist.');
            }
            $roleId = $organizationRoleObject->getRole()->getId();
            if ($roleId != $serviceAccountRoleId) {
                throw new SynapseValidationException('The requested user is not a service account.');
            }

            // deleting the auth code
            $authCodeObject = $this->authCodeRepository->findOneBy(['user' => $serviceAccountId]);
            if ($authCodeObject) {
                $this->authCodeRepository->delete($authCodeObject, false); // false , as we don't want to hit the database multiple times
            }

            $clientObject = $this->clientRepository->findOneBy(['person' => $serviceAccountId]);
            if ($clientObject) {
                $this->clientRepository->delete($clientObject, false); // false , as we don't want to hit the database multiple times
            }

            $serviceAccountObject = $organizationRoleObject->getPerson();
            $this->organizationRoleRepository->delete($organizationRoleObject, false); // false , as we don't want to hit the database multiple times
            $this->personRepository->delete($serviceAccountObject, true); // All the deleting will happen here
            $this->accessTokenRepository->invalidateAccessTokensForUser($serviceAccountId); //  This will invalidate all the access token created for the service account

        } else {
            throw new SynapseValidationException("There was an error retrieving the service account. Please contact Mapworks Client Support.");
        }

    }

    /**
     * Updates Student Participation status
     *
     * @param integer $loggedInUserId
     * @param integer $organizationId
     * @param StudentParticipationDTO $studentParticipationDTO
     * @throws AccessDeniedException|SynapseValidationException
     * @return StudentParticipationDTO
     */
    public function updateStudentParticipation($loggedInUserId, $organizationId, $studentParticipationDTO)
    {
        $studentId = $studentParticipationDTO->getStudentId();
        $student = $this->personRepository->find($studentId);
        $loggedInUser = $this->personRepository->find($loggedInUserId);

        // Validating loggedInUser is Coordinator
        $isCoordinator = $this->personService->getCoordinatorById($organizationId, $loggedInUserId);
        if (!$isCoordinator) {
            throw new SynapseValidationException('Only coordinators are allowed to change the participation status and you do not have coordinator access.');
        }

        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);
        $orgAcademicYearObject = $this->orgAcademicYearRepository->find($orgAcademicYearId);
        $organization = $this->organizationRepository->find($organizationId);
        $orgPersonStudentYearObject = $this->orgPersonStudentYearRepository->findOneBy(['orgAcademicYear' => $orgAcademicYearId, 'person' => $studentId]);
        $newIsActiveStatus = $studentParticipationDTO->getIsActive();
        $newIsParticipantStatus = $studentParticipationDTO->getIsParticipant();
        $currentDateTime = new \DateTime();
        $isParticipant = 1;

        if ($orgPersonStudentYearObject) {
            $existingIsActiveStatus = $orgPersonStudentYearObject->getIsActive();

            //Throwing an exception in case making the student an active participant who is already an active participant in the current year.
            if ($existingIsActiveStatus && $newIsParticipantStatus && $newIsActiveStatus) {
                throw new SynapseValidationException("The student is already an active participant in the current year.");

                //Throwing an exception in case making a student inactive participant where the student is already an inactive participant in the current year.
            } else if (!$existingIsActiveStatus && $newIsParticipantStatus && !$newIsActiveStatus) {
                throw new SynapseValidationException("The student is already an inactive participant in the current year.");

                //Throwing an exception in case making a student active nonparticipant
            } else if (!$newIsParticipantStatus && $newIsActiveStatus) {
                throw new SynapseValidationException("Active non-participant is not a valid state for a student");

                //Making a participant student an inactive nonparticipant in the current year
            } else if (!$newIsParticipantStatus && !$newIsActiveStatus) {
                $orgPersonStudentYearObject->setDeletedAt($currentDateTime);
                $orgPersonStudentYearObject->setDeletedBy($loggedInUser);
                $isParticipant = 0;
            }
            $orgPersonStudentYearObject->setIsActive($newIsActiveStatus);
            $orgPersonStudentYearObject->setModifiedAt($currentDateTime);
            $orgPersonStudentYearObject->setModifiedBy($loggedInUser);

        } else {
            //Creating a new active/inactive participant student for the current academic year
            if ($newIsParticipantStatus) {
                $orgPersonStudentYearObject = new OrgPersonStudentYear();
                $orgPersonStudentYearObject->setCreatedBy($loggedInUser);
                $orgPersonStudentYearObject->setOrganization($organization);
                $orgPersonStudentYearObject->setOrgAcademicYear($orgAcademicYearObject);
                $orgPersonStudentYearObject->setPerson($student);
                $orgPersonStudentYearObject->setModifiedBy($loggedInUser);
                $orgPersonStudentYearObject->setCreatedAt($currentDateTime);
                $orgPersonStudentYearObject->setModifiedAt($currentDateTime);
                $orgPersonStudentYearObject->setIsActive($newIsActiveStatus);
                $this->referralService->sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate($studentId, $organizationId, 'student_made_participant', $loggedInUser);
            } else {
                //Throwing an exception in case creating an active non-participant student in the current year
                if ($newIsActiveStatus) {
                    $message = "Active non-participant is not a valid state for a student.";

                //Throwing an exception in case creating an active non-participant student in the current year
                } else {
                    $message = "The student is already a non-participant in the current year.";
                }
                throw new SynapseValidationException($message);
            }
        }
        if ($isParticipant == 0) {
            $this->usersHelperService->updateStudentAsNonParticipating($studentId, $organizationId, $isParticipant, $loggedInUserId);
        }
        $this->orgPersonStudentYearRepository->persist($orgPersonStudentYearObject);
        return $studentParticipationDTO;
    }

    /**
     * Get faculty or student list based on user type and search text
     *
     * @param int $organizationId
     * @param string $userType - faculty, student
     * @param null|string $exclude - Person id to be excluded from search
     * @param null|string $searchText - search based on $searchText
     * @param null|string $participantFilter - (all, participants, non-participants)
     * @param null|string $sortBy - order the results
     * @param null|int $pageNumber
     * @param null|int $limit
     * @param null|bool $checkAccessToOrganization
     * @param null|string $activeFilter
     * @throws SynapseValidationException
     * @return UserListDto
     */
    public function listOrganizationUsersByType($organizationId, $userType, $exclude = NULL, $searchText = NULL, $participantFilter = NULL, $sortBy = NULL, $pageNumber = NULL, $limit = NULL, $checkAccessToOrganization = NULL, $activeFilter =  NULL)
    {
        if ($checkAccessToOrganization) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }

        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new SynapseValidationException('Organization Not Found.');
        }

        $pageNumber = (int)$pageNumber;
        if (!$pageNumber) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }

        $limit = (int)$limit;
        if (!$limit) {
            $limit = SynapseConstant::DEFAULT_RECORD_COUNT;
        }
        $startPoint = ($pageNumber * $limit) - $limit;

        $personIdsToExclude = [];
        if (strtolower(trim($exclude)) == 'coordinator') {
            $coordinators = $this->organizationRoleRepository->findBy(['organization' => $organization]);
            foreach ($coordinators as $coordinator) {
                $personIdsToExclude[] = $coordinator->getPerson()->getId();
            }
        }
        // get the current academicYear
        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);

        if ($userType == 'student') {
            $users = $this->personRepository->getOrganizationStudentsBySearchText($organizationId, $orgAcademicYearId, $personIdsToExclude, $searchText, $participantFilter, $sortBy, $limit, $startPoint);
            $totalRecordsCount = $this->personRepository->getOrganizationStudentCountBySearchText($organizationId, $orgAcademicYearId, $personIdsToExclude, $searchText, $participantFilter);
        } else {
            $users = $this->personRepository->getOrganizationFacultiesBySearchText($organizationId, $searchText, $personIdsToExclude,$activeFilter, $startPoint, $limit);
            $totalRecordsCount = $this->personRepository->getOrganizationFacultyCountBySearchText($organizationId, $searchText, $personIdsToExclude, $activeFilter);
        }
        $lastUpdated = null;
        $userListDto = new UserListDto();

        foreach ($users as $user) {
            if (!$lastUpdated || $lastUpdated < $user['modified_at']) {
                $lastUpdated = $user['modified_at'];
            }

            $emailSentDate = $user['welcome_email_sent_date'] ?: false;
            if ($emailSentDate && $lastUpdated < $emailSentDate) {
                $lastUpdated = $emailSentDate;
            }

            if (!empty($user['primary_mobile'])) {
                $phoneNumber = $user['primary_mobile'];
                $isMobile = true;
            } else if (!empty($user['home_phone'])) {
                $phoneNumber = $user['home_phone'];
                $isMobile = false;
            } else {
                $phoneNumber = false;
                $isMobile = false;
            }

            $userDto = new UserDTO();
            $userDto->setId($user['id']);
            $userDto->setFirstname($user['firstname']);
            $userDto->setLastname($user['lastname']);
            $userDto->setTitle($user['title']);
            $userDto->setEmail($user['primary_email']);
            $userDto->setExternalid($user['external_id']);

            $isActive = ($user['status']) === '0' ? 0 : 1;
            $userDto->setIsActive($isActive);

            // set the participant status when user_type is 'student'
            if ($userType == 'student') {
                $userDto->setParticipantStatus($user['participant']);
            }
            $userDto->setPhone($phoneNumber);
            $userDto->setIsmobile($isMobile);
            $userDto->setWelcomeEmailSentDate($user['welcome_email_sent_date']);

            $usersDtoArray[] = $userDto;
            if ($userType == 'student') {
                $userListDto->setStudent($usersDtoArray);
            } else {
                $userListDto->setFaculty($usersDtoArray);
            }
        }

        $totalPageCount = ceil($totalRecordsCount / $limit);
        $userListDto->setLastUpdated($lastUpdated);
        $userListDto->setTotalPages((int)$totalPageCount);
        $userListDto->setTotalRecords((int)$totalRecordsCount);
        $userListDto->setRecordsPerPage((int)$limit);
        $userListDto->setCurrentPage((int)$pageNumber);
        return $userListDto;
    }

}

