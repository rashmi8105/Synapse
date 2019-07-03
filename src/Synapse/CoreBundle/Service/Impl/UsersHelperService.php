<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\InactiveFacultyJob;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RoleRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\CoreBundle\Util\Constants\UsersConstant;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\SecondaryTiersDto;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\UsersDto;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\CoreBundle\job\NonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob;
use Synapse\RestBundle\Entity\CoordinatorDTO;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;

/**
 * @DI\Service("users_helper_service")
 * @deprecated
 */
class UsersHelperService extends AbstractService
{
    const SERVICE_KEY = 'users_helper_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

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
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var JobService
     */
    private $jobService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var ReferralService
     */
    private $referralService;

    /**
     * @var StudentAppointmentService
     */
    private $studentAppointmentService;

    /**
     * @var UtilServiceHelper
     */
    private $utilHelperService;

    // Repositories
    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;

    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionSetService;

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

    /**
     * @var RoleRepository
     */
    private $roleRepository;


    /**
     * UsersHelperService constructor.
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
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->jobService =  $this->container->get(JobService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->orgPermissionSetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->referralService = $this->container->get(ReferralService::SERVICE_KEY);
        $this->studentAppointmentService = $this->container->get(StudentAppointmentService::SERVICE_KEY);
        $this->utilHelperService = $this->container->get(UtilServiceHelper::SERVICE_KEY);

        //Repositories
        $this->appointmentRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Get organizations user type by person Id
     *
     * @param int $personId
     * @param int $campusId
     * @return string
     */
    public function getUserType($personId, $campusId)
    {

        $isCoordinator = $this->organizationRoleRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $isFaculty = $this->orgPersonFacultyRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $isStudent = $this->orgPersonStudentRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $type = '';
        if ($isCoordinator) {
            $type = UsersConstant::FILTER_COORDINATOR;
        } elseif ($isFaculty) {
            $type = UsersConstant::FACULTY_STAFF;
        } elseif ($isStudent) {
            $type = UsersConstant::FILTER_STUDENT;
        }
        return $type;
    }

    public function tierUsersBinding($tierUsers)
    {
        $usersArray = [];
        foreach ($tierUsers as $tierPrimaryUser) {
            $contacts = $tierPrimaryUser->getPerson()->getContacts();
            $userDto = new UsersDto();
            $userDto->setUserId($tierPrimaryUser->getPerson()
                ->getId());
            $userDto->setFirstName($tierPrimaryUser->getPerson()
                ->getFirstName());
            $userDto->setLastName($tierPrimaryUser->getPerson()
                ->getLastName());
            $userDto->setTitle($tierPrimaryUser->getPerson()
                ->getTitle());
            if (isset($contacts)) {
                $userDto->setEmail($this->checkNullResponse($contacts[0]->getPrimaryEmail()));
                if ($contacts[0]->getPrimaryMobile()) {
                    $userDto->setPhone($this->checkNullResponse($contacts[0]->getPrimaryMobile()));
                } else {
                    $userDto->setPhone($this->checkNullResponse($contacts[0]->getHomePhone()));
                }
            }
            $usersArray[] = $userDto;
        }
        return $usersArray;
    }

    public function getStudentsList($campusIds, $filter)
    {
        $usersList = '';
        if (!empty($campusIds)) {
            foreach ($campusIds as $campusId) {
                $studentsInstitution[$campusId] = call_user_func_array('array_merge', $this->campusRepository->getHierarchyOrder($campusId));
            }
            $campus_id = implode(',', $campusIds);

            $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
            $students = $this->campusRepository->listHierarchyStudents($campus_id, $filter);
            foreach ($students as $student) {
                $usersDto = new UsersDto();
                $usersDto->setUserId($student[UsersConstant::FIELD_ID]);
                $usersDto->setFirstName($student[UsersConstant::FIELD_FIRSTNAME]);
                $usersDto->setLastName($student[UsersConstant::FIELD_LASTNAME]);
                $usersDto->setEmail($this->checkNullResponse($student[UsersConstant::PRIMARY_EMAIL]));
                $usersDto->setExternalId($this->checkNullResponse($student[UsersConstant::EXTERNAL_ID]));
                $campusDto = new CampusDto();
                if (isset($studentsInstitution[$student[UsersConstant::ORGANIZATION_ID]])) {
                    $campusDto->setPrimaryTierId($studentsInstitution[$student[UsersConstant::ORGANIZATION_ID]]['primaryId']);
                    $campusDto->setPrimaryTierName($studentsInstitution[$student[UsersConstant::ORGANIZATION_ID]]['primaryName']);
                    $campusDto->setSecondaryTierId($studentsInstitution[$student[UsersConstant::ORGANIZATION_ID]]['secondaryId']);
                    $campusDto->setSecondaryTierName($studentsInstitution[$student[UsersConstant::ORGANIZATION_ID]]['secondaryName']);
                    $campusDto->setCampusId($studentsInstitution[$student[UsersConstant::ORGANIZATION_ID]]['campusId']);
                    $campusDto->setCampusName($studentsInstitution[$student[UsersConstant::ORGANIZATION_ID]]['campusName']);
                }
                $usersDto->setInstitutions($campusDto);
                $usersList[] = $usersDto;
            }
        }
        return $usersList;
    }

    /**
     * return Empty Value for null value
     */
    public function checkNullResponse($input)
    {
        return $input ? $input : '';
    }

    public function validateRole($roleId)
    {
        $this->roleRepository = $this->repositoryResolver->getRepository(UsersConstant::ROLE_REPO);
        $role = $this->roleRepository->find($roleId);
        if (!isset($role)) {
            throw new ValidationException([
                UsersConstant::ERROR_ROLE_NOT_FOUND
            ], UsersConstant::ERROR_ROLE_NOT_FOUND, 'role_not_found');
        }
        return $role;
    }

    /**
     * Check $orgId is available in Organazation Table
     */
    public function validateOrganization($orgId)
    {
        $this->organizationRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_REPO);
        $organization = $this->organizationRepository->find($orgId);
        if (!$organization) {
            throw new ValidationException([
                UsersConstant::ORGANIZATION_NOT_FOUND
            ], UsersConstant::ORGANIZATION_NOT_FOUND, UsersConstant::ORGANIZATION_NOT_FOUND_CODE);
        }
        return $organization;
    }

    public function isPersonFound($users)
    {
        if (!isset($users)) {
            throw new ValidationException([
                UsersConstant::ERROR_PERSON_NOT_FOUND
            ], UsersConstant::ERROR_PERSON_NOT_FOUND, UsersConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }
    }

    public function isPersonLocked($users)
    {
        if ($users->getIsLocked() == 'y') {
            throw new ValidationException([
                UsersConstant::ERROR_PERSON_LOCKED
            ], UsersConstant::ERROR_PERSON_LOCKED, UsersConstant::ERROR_PERSON_LOCKED_KEY);
        }
    }

    public function isDuplicate($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                $errorsString .= $error->getMessage();
            }
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'metakey_duplicate_Error');
        }
    }

    /**
     * gets the list of coordinators for the organization
     *
     * @param integer $organizationId
     * @return array
     */
    public function getCoordinator($organizationId)
    {
        $coordinators = $this->organizationRoleRepository->getCoordinatorsArray($organizationId);
        $lastUpdated = null;
        foreach ($coordinators as $coordinatorIndex => $coordinator) {

            $coordinatorModified = new \DateTime($coordinator['modified_at']);
            if (!$lastUpdated || $lastUpdated < $coordinatorModified) {
                $lastUpdated = $coordinatorModified;
            }
            if (isset($coordinator['welcome_email_sent_date']) && !empty($coordinator['welcome_email_sent_date'])) {
                $welcomeEmailDate = $coordinators[$coordinatorIndex]['welcome_email_sentDate'] = new \DateTime($coordinator['welcome_email_sent_date']);
                if ($welcomeEmailDate && $lastUpdated < $welcomeEmailDate) {
                    $lastUpdated = $welcomeEmailDate;
                }

                //un setting  the variable  welcome_email_sent_date as the Frontend uses welcome_email_sentDate
                unset($coordinators[$coordinatorIndex]['welcome_email_sent_date']);

            }
            $isMobile = true;
            if (empty($coordinator['primary_mobile'])) {
                $phoneNumber = $coordinator['home_phone'];
                $isMobile = false;
            } else {
                $phoneNumber = $coordinator['primary_mobile'];
            }
            $coordinators[$coordinatorIndex]['phone'] = $phoneNumber;
            $coordinators[$coordinatorIndex]['ismobile'] = $isMobile;

            //get the permission Templates
            $permissionTemplates = $this->getUserPermissionTemplates($coordinator['id']);

            if (!empty($permissionTemplates)) {
                $permissionTemplateNameArray = array_column($permissionTemplates, 'permission_template_name');
                $permissionNames = implode(",", $permissionTemplateNameArray);
            } else {
                $permissionNames = "";
            }
            $coordinators[$coordinatorIndex]['permission_template_names'] = $permissionNames;

            // unset the values in the array which are not needed any more
            unset($coordinators[$coordinatorIndex]['home_phone']);
            unset($coordinators[$coordinatorIndex]['primary_mobile']);
            unset($coordinators[$coordinatorIndex]['modified_at']);
        }

        $returnSet['coordinators'] = $coordinators;
        $returnSet['last_updated'] = $lastUpdated;
        return $returnSet;
    }

    /**
     * Get the service accounts for the organization
     *
     * @param integer $organizationId
     * @return array
     */
    public function getServiceAccounts($organizationId){

        $serviceAccounts = $this->organizationRoleRepository->getServiceAccountsForOrganization($organizationId);

        foreach($serviceAccounts  as $serviceAccountIndex => $serviceAccount){
            $serviceAccounts[$serviceAccountIndex]['key_creation_date'] =  new \DateTime($serviceAccount['modified_at']);
            unset($serviceAccounts[$serviceAccountIndex]['modified_at']);

        }
        return ['service_accounts' => $serviceAccounts ];
    }

    public function secondaryTierUserDashboard($tierId)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $this->orgUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $tierSecondaryInfos = $this->tierRepository->findOneBy(array(
            'tier' => '2',
            UsersConstant::FIELD_ID => $tierId
        ));
        if (!isset($tierSecondaryInfos)) {
            throw new ValidationException([
                UsersConstant::ERROR_NOT_TIER
            ], UsersConstant::ERROR_NOT_TIER, 'not_tier');
        }

        $tierPrimaryDet = $this->tierDetailsRepository->findOneBy(array(
            UsersConstant::ORGANIZATION => $tierSecondaryInfos->getParentOrganizationId()
        ));

        $tierDto = new TierDto();

        $listTierDto = new SecondaryTiersDto();
        $listTierDto->setPrimaryTierId($tierPrimaryDet->getOrganization()
            ->getId());
        $listTierDto->setPrimaryTierName($tierPrimaryDet->getOrganizationName());

        $tierInfodet = $this->tierDetailsRepository->findOneBy(array(
            UsersConstant::ORGANIZATION => $tierId
        ));
        $tierCampusInfo = $this->tierRepository->findBy(array(
            'tier' => '3',
            TierConstant::PARENT_ORG_ID => $tierId
        ));

        if (!empty($tierInfodet)) {
            $usersArray = [];
            $tierUsers = $this->tierUsersRepository->findBy([
                TierConstant::FIELD_ORGANIZATION => $tierInfodet->getOrganization()
                    ->getId()
            ]);
            $campuses = $this->tierRepository->listCampuses($tierId, '3');
            $campusIds = array_column($campuses, UsersConstant::ORG_ID);
            $secondaryTierId = $tierInfodet->getOrganization()->getId();
            $tierDto->setSecondaryTierId($secondaryTierId);
            $tierDto->setSecondaryTierName($tierInfodet->getOrganizationName());
            $tierDto->setDescription($tierInfodet->getDescription());
            $users = $this->orgUsersRepository->usersCount($secondaryTierId);
            $campusCoordinators = $this->organizationRoleRepository->getPrimaryTierCoordinators($campusIds);
            $tierDto->setTotalSecondaryTierUsers(count($users));
            $tierDto->setTotalCampus(count($tierCampusInfo));
            $tierDto->setTotalCoordinators(count($campusCoordinators));
            if (!empty($tierUsers)) {
                $usersArray = $this->tierUsersBinding($tierUsers);
                $tierDto->setUsers($usersArray);
            }

            $campusArray = [];
            if (!empty($tierCampusInfo)) {
                foreach ($tierCampusInfo as $tierCampus) {
                    $tierCampusInfodet = $this->tierDetailsRepository->findOneBy(array(
                        UsersConstant::ORGANIZATION => $tierCampus->getId()
                    ));
                    $campusDto = new CampusDto();
                    $campusDto->setId($tierCampusInfodet->getOrganization()
                        ->getId());
                    $campusDto->setCampusName($tierCampusInfodet->getOrganizationName());
                    $getUsers = $this->getCoordinator($tierCampusInfodet->getOrganization()
                        ->getId());
                    $coordinatorArray = [];
                    foreach ($getUsers[UsersConstant::FIELD_COORDINATORS] as $coordinator) {
                        $coordinatorDto = new CoordinatorDTO();
                        $coordinatorDto->setId($coordinator[UsersConstant::FIELD_ID]);
                        $coordinatorDto->setFirstname($coordinator[UsersConstant::FIELD_FIRSTNAME]);
                        $coordinatorDto->setLastname($coordinator[UsersConstant::FIELD_LASTNAME]);
                        $coordinatorDto->setTitle($coordinator[UsersConstant::FILED_TITLE]);
                        $coordinatorDto->setEmail($coordinator[UsersConstant::FIELD_EMAIL]);
                        $coordinatorDto->setPhone($coordinator[UsersConstant::FIELD_PHONE]);
                        $coordinatorArray[] = $coordinatorDto;
                    }
                    $campusDto->setCoordinators($coordinatorArray);

                    $campusArray[] = $campusDto;
                }
                $tierDto->setCampuses($campusArray);
            }
            $tierDtoArray[] = $tierDto;
        } else {
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $listTierDto->setSecondaryTiers($tierDtoArray);
        return $listTierDto;
    }

    public function primaryTierUserDashboard($tierId)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $this->orgUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $tierPrimaryInfos = $this->tierRepository->findBy(array(
            'tier' => '2',
            TierConstant::PARENT_ORG_ID => $tierId
        ));
        $tierPrimaryInfodet = $this->tierDetailsRepository->findOneBy(array(
            UsersConstant::ORGANIZATION => $tierId
        ));
        if (!isset($tierPrimaryInfos)) {
            throw new ValidationException([
                UsersConstant::ERROR_NOT_TIER
            ], UsersConstant::ERROR_NOT_TIER, 'not_tier');
        }
        $tierDtoArray = [];
        foreach ($tierPrimaryInfos as $tierInfo) {
            $tierDto = new TierDto();
            $listTierDto = new SecondaryTiersDto();
            $listTierDto->setPrimaryTierId($tierId);
            $listTierDto->setPrimaryTierName($tierPrimaryInfodet->getOrganizationName());
            $tierInfodet = $this->tierDetailsRepository->findOneBy(array(
                UsersConstant::ORGANIZATION => $tierInfo->getId()
            ));
            $tierCampusInfo = $this->tierRepository->findBy(array(
                'tier' => '3',
                TierConstant::PARENT_ORG_ID => $tierInfo->getId()
            ));
            if (!empty($tierInfodet)) {
                $usersArray = [];
                $tierUsers = $this->tierUsersRepository->findBy([
                    TierConstant::FIELD_ORGANIZATION => $tierInfodet->getOrganization()
                        ->getId()
                ]);
                $campuses = $this->tierRepository->listCampuses($tierInfo->getId(), '3');
                $campusIds = array_column($campuses, UsersConstant::ORG_ID);
                $secondaryTierId = $tierInfodet->getOrganization()->getId();
                $tierDto->setSecondaryTierId($secondaryTierId);
                $tierDto->setSecondaryTierName($tierInfodet->getOrganizationName());
                $tierDto->setDescription($tierInfodet->getDescription());
                $users = $this->orgUsersRepository->usersCount($secondaryTierId);
                $campusCoordinators = $this->organizationRoleRepository->getPrimaryTierCoordinators($campusIds);
                $tierDto->setTotalSecondaryTierUsers($users);
                $tierDto->setTotalCampus(count($tierCampusInfo));
                $tierDto->setTotalCoordinators(count($campusCoordinators));
                if (!empty($tierUsers)) {
                    $usersArray = $this->tierUsersBinding($tierUsers);
                    $tierDto->setUsers($usersArray);
                }
                $campusArray = [];
                if (!empty($tierCampusInfo)) {
                    foreach ($tierCampusInfo as $tierCampus) {
                        $tierCampusInfodet = $this->tierDetailsRepository->findOneBy(array(
                            UsersConstant::ORGANIZATION => $tierCampus->getId()
                        ));
                        $campusDto = new CampusDto();
                        $campusDto->setId($tierCampusInfodet->getOrganization()
                            ->getId());
                        $campusDto->setCampusName($tierCampusInfodet->getOrganizationName());
                        $getUsers = $this->getCoordinator($tierCampusInfodet->getOrganization()
                            ->getId());
                        $coordinatorArray = [];
                        foreach ($getUsers[UsersConstant::FIELD_COORDINATORS] as $coordinator) {
                            $coordinatorDto = new CoordinatorDTO();
                            $coordinatorDto->setId($coordinator[UsersConstant::FIELD_ID]);
                            $coordinatorDto->setFirstname($coordinator[UsersConstant::FIELD_FIRSTNAME]);
                            $coordinatorDto->setLastname($coordinator[UsersConstant::FIELD_LASTNAME]);
                            $coordinatorDto->setTitle($coordinator[UsersConstant::FILED_TITLE]);
                            $coordinatorDto->setEmail($coordinator[UsersConstant::FIELD_EMAIL]);
                            $coordinatorDto->setPhone($coordinator[UsersConstant::FIELD_PHONE]);
                            $coordinatorArray[] = $coordinatorDto;
                        }
                        $campusDto->setCoordinators($coordinatorArray);
                        $campusArray[] = $campusDto;
                    }
                    $tierDto->setCampuses($campusArray);
                }
                $tierDtoArray[] = $tierDto;
            } else {
                throw new ValidationException([
                    TierConstant::TIER_NOT_FOUND
                ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            }
            $listTierDto->setSecondaryTiers($tierDtoArray);
        }
        return $listTierDto;
    }

    /**
     * Validate external id using external id and organization
     *
     * @param string $externalId
     * @param Organization $organization
     * @param int|null $userId
     * @param Person|null $entity
     * @param string|null $userType
     * @param string|null $userName
     *
     * @return Person|null
     * @throws SynapseValidationException
     */
    public function validateExternalId($externalId, $organization, $userId = null, $entity = null, $userType = null, $userName = null)
    {
        // Validate person email and external id
        $this->validateEmailAndExternalId($externalId, $userType, $userName, $organization, $userId);

        if (empty($entity)) {
            $personObject = $this->personRepository->findOneBy([
                'externalId' => $externalId,
                'organization' => $organization
            ]);

            if (!empty($personObject)) {
                $existingUserId = $personObject->getId();

                // Check for Creating Organization userType exist or not
                $existingUserType = $this->getUserTypeAsArray($existingUserId, $organization->getId());
                if (in_array($userType, $existingUserType) && $personObject->getUsername() == $userName && $existingUserId != $userId) {
                    throw new SynapseValidationException("User already exists in the system");
                } elseif ($personObject->getUsername() != $userName && $existingUserId != $userId) {
                    throw new SynapseValidationException("User already exists in the system");
                } else {
                    return $personObject;
                }
            }
        } else {

            $organizationId = $organization->getId();
            $personArray = $this->personRepository->validateEntityExtId($externalId, $organizationId);
            if (count($personArray) > 0) {
                throw new SynapseValidationException("User already exists in the system");
            }
        }
    }

    /**
     * Get permissionset for user
     *
     * @param int $loggedUserId
     * @return array
     */
    public function getUserPermissionTemplates($loggedUserId)
    {
        $permissionTemplates = $this->orgPermissionSetService->getPermissionSetsByUser($loggedUserId);
        $permissionTemplate = array();
        foreach ($permissionTemplates['permission_templates'] as $permission) {
            $permissionArray = array();
            $permissionArray['permission_template_id'] = $permission->getPermissionTemplateId();
            $permissionArray['permission_template_name'] = $permission->getPermissionTemplateName();
            $permissionTemplate[] = $permissionArray;
        }
        return $permissionTemplate;
    }

    /**
     * Update a specific user's active status
     *
     * @param int $userId - user id of the user being marked active / inactive
     * @param int $organizationId - organization id of the user being marked active/inactive
     * @param string $userType - "faculty" or "student", type of user that is being marked active/inactive
     * @param int $status - "0" (inactive) or "1" (active)
     * @return null|object
     */
    public function updateStatus($userId, $organizationId, $userType, $status)
    {
        $userDetails = null;
        if ($userType == 'faculty') {
            //User is a faculty member
            $userDetails = $this->orgPersonFacultyRepository->findOneBy(array(
                'organization' => $organizationId,
                'person' => $userId
            ));
            if ($userDetails) {
                //The faculty member exists
                if ($status != $userDetails->getStatus()) {
                    // Faculty's active status has changed. Start the job to update the user's active status
                    if ($status == 0) {
                        $job = new InactiveFacultyJob();

                        $jobNumber = uniqid();
                        $userIdArray = [];
                        $userIdArray[] = $userId;
                        $job->args = array(
                            'jobNumber' => $jobNumber,
                            'orgId' => $organizationId,
                            'facultyIds' => $userIdArray
                        );
                        $this->resque->enqueue($job, true);
                    }
                    $userDetails->setStatus($status);
                }

            }
        }
        if ($userType == 'student') {
            $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);
            $userDetails = $this->orgPersonStudentYearRepository->findOneBy(array(
                'organization' => $organizationId,
                'person' => $userId,
                'orgAcademicYear' => $orgAcademicYearId
            ));
            if ($userDetails) {
                $userDetails->setIsActive($status);
            }
        }
        return $userDetails;
    }

    /**
     * Updates the student to non-participating in Mapworks, and cancels outstanding appointments for that student.
     * Also sends referral related emails
     *
     * @param int $studentId
     * @param int $organizationId
     * @param int $isParticipating
     * @param int $loggedInUserId
     * @return OrgPersonStudentYear
     * @throws SynapseValidationException
     */
    public function updateStudentAsNonParticipating($studentId, $organizationId, $isParticipating, $loggedInUserId)
    {
        $currentDate = new \DateTime();
        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);

        $orgPersonStudentYearObject = $this->orgPersonStudentYearRepository->findOneBy(array(
            'organization' => $organizationId,
            'person' => $studentId,
            'orgAcademicYear' => $orgAcademicYearId
        ));



        if ($orgPersonStudentYearObject && ($isParticipating == 0)) {


            $orgPersonStudentYearObject->setDeletedAt($currentDate);
            $nonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob = new NonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob();
            $jobNumber = uniqid();

            $nonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob->args = array(
                'studentId' => $studentId,
                'currentDate' => $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT),
                'organizationId' => $organizationId,
                'jobNumber' => $jobNumber
            );

            $this->jobService->addJobToQueue($organizationId, NonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob::JOB_KEY, $nonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob, $loggedInUserId);
        }

        return $orgPersonStudentYearObject;
    }





    public function checkIsValue($isValue, $message, $key)
    {
        if (!$isValue) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    public function getUserTypeAsArray($personId, $campusId)
    {
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_PERSON_FACULTY_REPO);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_PERSON_STUDENT_REPO);

        $isCoordinator = $this->organizationRoleRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $isFaculty = $this->orgPersonFacultyRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $isStudent = $this->orgPersonStudentRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $typeArray = array();
        if ($isCoordinator) {
            $typeArray[] = UsersConstant::FILTER_COORDINATOR;
        }
        if ($isFaculty) {
            $typeArray[] = UsersConstant::FILTER_FACULTY;
        }
        if ($isStudent) {
            $typeArray[] = UsersConstant::FILTER_STUDENT;
        }
        return $typeArray;
    }

    public function getUserOrgId($personId, $campusId)
    {
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_PERSON_FACULTY_REPO);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_PERSON_STUDENT_REPO);

        $coordinatorOrgId = $this->organizationRoleRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $facultyOrgId = $this->orgPersonFacultyRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $studentOrgId = $this->orgPersonStudentRepository->findOneBy(array(
            UsersConstant::FIELD_ORGANIZATION => $campusId,
            UsersConstant::FIELD_PERSON => $personId
        ));
        $orgId = array();
        if ($coordinatorOrgId) {
            $orgId[] = $coordinatorOrgId->getOrganization()->getId();
        }
        if ($facultyOrgId) {
            $orgId[] = $facultyOrgId->getOrganization()->getId();
        }
        if ($studentOrgId) {
            $orgId[] = $studentOrgId->getOrganization()->getId();
        }
        return $orgId;
    }

    /**
     * Validate Email and External Id for a person
     *
     * @param string $externalId
     * @param string $userType
     * @param string $userName
     * @param Organization $organization
     * @param int|null $userId
     * @throws SynapseValidationException
     * @return null
     *
     * @deprecated - This can be replaced with a call to validator->validate() on a person object.
     *
     */
    private function validateEmailAndExternalId($externalId, $userType, $userName, $organization, $userId)
    {
        if ($externalId == '#clear') {
            throw new SynapseValidationException("The provided External ID is not allowed.");
        }

        $personObject = null;
        if ($userId) {
            $personObject = $this->personRepository->find($userId);
        }

        // Find person by User name and organization
        $userNamePerson = $this->personRepository->findOneBy([
            'username' => $userName,
            'organization' => $organization
        ]);

        // Validate if the username is valid
        $this->checkPersonObject($personObject, $userNamePerson, $userType, "email");

        // Find person by external id and organization
        $externalIdPerson = $this->personRepository->findOneBy([
            'externalId' => $externalId,
            'organization' => $organization
        ]);

        // Validate if the externalId is valid
        $this->checkPersonObject($personObject, $externalIdPerson, $userType, "ID");

    }


    /**
     * @param Person $personObject
     * @param Person $columnBasedPersonObject
     * @param string $userType
     * @param string $column
     * @throws SynapseValidationException
     */
    private function checkPersonObject($personObject, $columnBasedPersonObject, $userType, $column)
    {
        // This would get called when we are editing a person
        if ($personObject && $columnBasedPersonObject && $personObject->getId() != $columnBasedPersonObject->getId()) {
            throw new SynapseValidationException(ucfirst($userType) . " {$column} already exists.");
        }

        // This would get called when we are adding a new person
        if (!$personObject && $columnBasedPersonObject) {
            throw new SynapseValidationException(ucfirst($userType) . " {$column} already exists.");
        }
    }

}
