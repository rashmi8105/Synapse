<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\OrganizationRole;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\UnauthorizedException;
use Synapse\CoreBundle\Repository\AuthCodeRepository;
use Synapse\CoreBundle\Repository\ClientRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RefreshTokenRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Entity\CoordinatorDTO;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\LoggedInUserDetailsResponseDto;
use Synapse\RestBundle\Entity\MyAccountDto;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("person_service")
 */
class PersonService extends PersonHelperService
{

    const SERVICE_KEY = 'person_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

    //Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EbiUserService
     */
    private $ebiUserService;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrganizationlangService
     */
    private $organizationLangService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var UtilServiceHelper
     */
    private $utilServiceHelper;


    //Repositories

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
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

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
     * @var RefreshTokenRepository
     */
    private $refreshTokenRepository;

    /**
     * @var RoleLangRepository
     */
    private $roleLangRepository;

    /**
     * PersonService Constructor
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
        //scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->encoderFactory = $this->container->get('security.encoder_factory');

        //services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->ebiUserService = $this->container->get(EbiUserService::SERVICE_KEY);
        $this->entityService = $this->container->get(EntityService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->utilServiceHelper = $this->container->get(UtilServiceHelper::SERVICE_KEY);

        //Repositories
        $this->authCodeRepository = $this->repositoryResolver->getRepository(AuthCodeRepository::REPOSITORY_KEY);
        $this->clientRepository = $this->repositoryResolver->getRepository(ClientRepository::REPOSITORY_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->refreshTokenRepository = $this->repositoryResolver->getRepository(RefreshTokenRepository::REPOSITORY_KEY);
        $this->roleLangRepository = $this->repositoryResolver->getRepository(RoleLangRepository::REPOSITORY_KEY);
    }

    /**
     * @deprecated - Person creation is being consolidated within the person bundle. Please look there for this functionality
     * Creates New Person
     */
    public function createPerson(PersonDTO $personDTO)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($personDTO);
        $this->logger->debug(" Creating Person -  " . $logContent);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $response = array();
        $person = $this->getPersonConv($personDTO);
        $contactInfo = $this->getContactInfoConv($personDTO);
        $person->addContact($contactInfo);
        $personInstance = $this->personRepository->createPerson($person);
        $response[PersonConstant::FIELD_PERSON] = $personInstance;
        $people = $this->cache->fetch("organization.{$person->getOrganization()->getId()}.people");
        $people = $people ? $people : [];
        $people[$person->getId()] = $person->getExternalId();
        $this->cache->save("organization.{$person->getOrganization()->getId()}.people", $people);
        $this->personRepository->flush();
        $this->logger->info("Created Person Successfully ");
        return $response;
    }

    /**
     * @deprecated - Person creation is being consolidated within the person bundle. Please look there for this functionality
     * Creates New Person Without DTO
     */
    public function createPersonRaw(Person $person, Contactinfo $contactInfo)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $response = array();
        $person->addContact($contactInfo);
        $personInstance = $this->personRepository->createPerson($person);
        $response[PersonConstant::FIELD_PERSON] = $personInstance;
        $response['contactInfo'] = $contactInfo;
        $people = $this->cache->fetch("organization.{$person->getOrganization()->getId()}.people");
        $people = $people ? $people : [];
        $people[$person->getId()] = $person->getExternalId();
        $this->cache->save("organization.{$person->getOrganization()->getId()}.people", $people);
        return $personInstance;
    }

    public function getPersonConv(PersonDTO $personDTO)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($personDTO);
        $this->logger->debug(" Get Person Conv -  " . $logContent);
        $person = new Person();
        $personID = $personDTO->getPersonId();
        if (isset($personID)) {
            $person->setId($personDTO->getPersonId());
            $person->setActivationToken($personDTO->getActivationToken());
        } else {
            $activationToken = mt_rand();
            $person->setActivationToken($activationToken);
        }

        $person->setFirstName($personDTO->getFirstName());
        $person->setLastName($personDTO->getLastName());
        $person->setTitle($personDTO->getTitle());
        $person->setDateOfBirth($personDTO->getDateOfBirth());
        $person->setExternalId($personDTO->getExternalId());
        $person->setUsername($personDTO->getUsername());
        $person->setOrganization($this->orgService->find($personDTO->getOrganization()));

        $person->setConfidentialityStmtAcceptDate($personDTO->getConfidentialityStmtAcceptDate());

        return $person;
    }

    public function getContactInfoConv(PersonDTO $personDTO)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($personDTO);
        $this->logger->debug(" Get Contact Info Conv -  " . $logContent);
        $contactInfo = new ContactInfo();

        $contactInfo->setAddress1($personDTO->getAddress1());
        $contactInfo->setAddress2($personDTO->getAddress2());
        $contactInfo->setAlternateEmail($personDTO->getAlternateEmail());
        $contactInfo->setAlternateMobile($personDTO->getAlternateMobile());
        $contactInfo->setAlternateMobileProvider($personDTO->getAlternateMobileProvider());
        $contactInfo->setCity($personDTO->getCity());
        $contactInfo->setCountry($personDTO->getCountry());
        $contactInfo->setHomePhone($personDTO->getHomePhone());
        $contactInfo->setOfficePhone($personDTO->getOfficePhone());
        $contactInfo->setPrimaryEmail($personDTO->getPrimaryEmail());
        $contactInfo->setPrimaryMobile($personDTO->getPrimaryMobile());
        $contactInfo->setPrimaryMobileProvider($personDTO->getPrimaryMobileProvider());
        $contactInfo->setState($personDTO->getState());
        $contactInfo->setZip($personDTO->getZip());

        return $contactInfo;
    }

    /**
     * Get All Roles
     */
    public function getRoles($langid)
    {
        $this->logger->debug("Get All Roles by LangId " . $langid);
        $this->orgRoleRepo = $this->repositoryResolver->getRepository(PersonConstant::ORG_ROLE_REPO);
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(PersonConstant::LANGUAGE_MASTER_REPO);
        $lang = $this->languageMasterRepository->find($langid);
        if (!isset($lang)) {
            return new Error("validation_error", 'Language not found ');
        }
        $roles = $this->orgRoleRepo->getCoordinatorRolesByLangId($lang);

        if (!isset($roles)) {
            $this->logger->error(" Person Service - getRoles - Role  Not Found ");
            return new Error("validation_error", "Role Not Found");
        }
        $reponseArray = array();
        if (count($roles) > 0) {
            foreach ($roles as $role) {
                $roleTemp = array();

                $roleTemp[PersonConstant::FIELD_ROLEID] = $role[PersonConstant::ID];
                $roleTemp['coordinator_type'] = $role[PersonConstant::ROLE_NAME];
                array_push($reponseArray, $roleTemp);
            }
        }
        return $reponseArray;
    }

    /**
     * Get coordinators list
     *
     * @param int $organizationId
     * @param string $filter
     * @return array
     * @throws \Exception
     */
    public function getCoordinator($organizationId, $filter)
    {
        $this->logger->debug("Get Coordinator List for Organization Id " . $organizationId);
        $coordinators = $this->organizationRoleRepository->getCoordinators($organizationId);
        $this->isPersonFound($coordinators);

        $returnSet = [
            'last_updated' => null,
            'coordinators' => []
        ];
        $lastUpdated = null;
        // @var OrganizationRole $coordinator
        foreach ($coordinators as $coordinator) {
            if (!$lastUpdated || $lastUpdated < $coordinator->getModifiedAt()) {
                $lastUpdated = $coordinator->getModifiedAt();
            }

            $person = $coordinator->getPerson();
            $emailSentDate = $person->getWelcomeEmailSentDate() ?: false;
            if ($emailSentDate && $lastUpdated < $emailSentDate) {
                $lastUpdated = $emailSentDate;
            }

            $contactInfo = $this->contactInfoRepository->getCoalescedContactInfo($person->getContacts());
            $isMobile = true;
            if (!($phoneNumber = $contactInfo->getPrimaryMobile())) {
                $phoneNumber = $contactInfo->getHomePhone();
                $isMobile = false;
            }

            $role = $coordinator->getRole();

            // ESPRJ-5366 Looked into adding a null check to $role. However, if $role is null, and we null-check, then
            // we will skip over the "continue" statement and assume that whoever this coordinator is has primary coordinator assigned.
            // Instead I will add more logging statements that might help identify who the coordinator is that has no role.
            if ($role) {
                // If Filler set primary will return only primary coordinator
                if ($filter == 'primary' && strtolower($role->getName()) != 'primary coordinator') {
                    continue;
                }
            } else {
                $coordinatorPerson = $coordinator->getPerson();
                if ($coordinatorPerson) {
                    $coordinatorPersonId = $coordinatorPerson->getId();
                    if ($coordinatorPersonId) {
                        throw new \Exception("Attempted to determine the coordinator role for person ID: $coordinatorPersonId and failed. Please investigate.");
                    }
                }
                throw new \Exception("Attempted to determine a coord role for an unknown coordinator person ID. Please investigate");
            }

            $personDetails = [
                'id' => $person->getId(),
                'firstname' => $person->getFirstname(),
                'lastname' => $person->getLastname(),
                'welcome_email_sentDate' => $person->getWelcomeEmailSentDate(),
                'title' => $person->getTitle(),
                'email' => $person->getUsername(),
                'phone' => $phoneNumber,
                'ismobile' => $isMobile,
                'role' => $role->getName(),
                'roleid' => $role->getId()
            ];

            $returnSet['coordinators'][] = $personDetails;
        }

        $returnSet['last_updated'] = $lastUpdated;
        $this->logger->info("Get Coordinator List for Organization Id ");
        return $returnSet;
    }

    /**
     * @param $organizationid
     * @param $personid
     * @return array
     */
    public function getCoordinatorById($organizationid, $personid)
    {
        $this->roleLangRepository = $this->repositoryResolver->getRepository(PersonConstant::ROLE_LANG_REPO);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(PersonConstant::ORG_ROLE_REPO);
        $coordinatorContact = array();
        $coordinators = $this->organizationRoleRepository->findBy(array(
            PersonConstant::FIELD_ORGANIZATION => $organizationid,
            PersonConstant::FIELD_PERSON => $personid
        ));
        if (!isset($coordinators)) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        } else {
            foreach ($coordinators as $coordinator) {
                $info = array();
                $info['id'] = $coordinator->getPerson()->getId();
                $info[PersonConstant::FIELD_FIRSTNAME] = $coordinator->getPerson()->getFirstname();
                $info[PersonConstant::FIELD_LASTNAME] = $coordinator->getPerson()->getLastname();
                $info[PersonConstant::FILED_TITLE] = $coordinator->getPerson()->getTitle();
                $contacts = $coordinator->getPerson()->getContacts();
                if (isset($contacts)) {
                    foreach ($contacts as $contact) {
                        $info[PersonConstant::FIELD_EMAIL] = $contact->getPrimaryEmail();
                        if (is_null($contact->getPrimaryMobile())) {
                            $info[PersonConstant::FIELD_PHONE] = $contact->getHomePhone();
                            $info[PersonConstant::FIELD_ISMOBILE] = false;
                        } else {
                            $info[PersonConstant::FIELD_PHONE] = $contact->getPrimaryMobile();
                            $info[PersonConstant::FIELD_ISMOBILE] = true;
                        }
                    }
                }
                $roles = $this->roleLangRepository->findBy(array(
                    'role' => $coordinator->getRole()
                        ->getId()
                ));
                if (isset($roles)) {
                    foreach ($roles as $role) {
                        $info['role'] = $role->getRolename();
                        $info[PersonConstant::FIELD_ROLEID] = $role->getRole()->getId();
                    }
                }
                array_push($coordinatorContact, $info);
            }
        }

        return $coordinatorContact;
    }

    /**
     * Gets the details for the logged in user
     *
     * @param Person $person
     * @return LoggedInUserDetailsResponseDto
     */
    public function getLoggedInUserDetails(Person $person)
    {
        $organization = $person->getOrganization();
        $organizationId = $organization->getId();
        $coordinatorDetails = $this->getCoordinatorById($organizationId, $person->getId());
        $isCoordinator = (isset($coordinatorDetails) && !empty($coordinatorDetails)) ? true : false;
        $loggedUser = new LoggedInUserDetailsResponseDto();
        $personId = $person->getId();
        $role = [];
        $adminDetails = $this->getSkyFactorUserDetails($person->getId());
        if (!empty($adminDetails)) {
            $roleName = $adminDetails['roleName'];
        }
        if (isset($roleName)) {
            $role[] = $roleName;
        } elseif ($isCoordinator) {
            $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
                'person' => $person,
                'organization' => $organization
            ));
            if ($orgPersonFaculty && (is_null($orgPersonFaculty[0]->getStatus()) || $orgPersonFaculty[0]->getStatus() == 1)) {
                $role[] = 'Coordinator';
            }
        } else {
            $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
                'person' => $person,
                'organization' => $organization
            ));
            if ($orgPersonFaculty && (is_null($orgPersonFaculty[0]->getStatus()) || $orgPersonFaculty[0]->getStatus() == 1)) {
                $role[] = 'Staff';
            }
        }
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        $currentOrgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        $orgPersonStudentYear = $this->orgPersonStudentYearRepository->findOneBy(array(
            'person' => $person,
            'organization' => $organization,
            'orgAcademicYear' => $currentOrgAcademicYearId
        ));

        if ($orgPersonStudentYear && $orgPersonStudentYear->getIsActive()) {
            $role[] = "Student";
        }
        $role = implode(",", $role);
        $loggedUser->setType($role);
        $loggedUser->setId($personId);
        $contacts = $person->getContacts();
        $loggedUser->setFirstname($person->getFirstName());
        $loggedUser->setLastname($person->getLastName());
        if (!empty($contacts)) {
            $loggedUser->setEmail($contacts[0]->getPrimaryEmail());
            $loggedUser->setMobile($contacts[0]->getPrimaryMobile());
        }

        $loggedUser->setOrganizationId($organizationId);
        $organization = $this->orgService->getOrganizationDetailsLang($organizationId);
        if ($organization) {
            $loggedUser->setOrganizationName($organization->getOrganizationName());
            $loggedUser->setLangId($organization->getLang()
                ->getId());
            $loggedUser->setLangCode($organization->getLang()
                ->getLangcode());
        }
        $checkProxy = $this->container->get('appointments_service')->checkIfActAsProxy($personId);
        $loggedUser->setCanActAsProxy($checkProxy);
        $externalId = ($person->getExternalId()) ? $person->getExternalId() : '';
        $loggedUser->setExternalId($externalId);
        $academicUpdateNotification = ($organization->getOrganization()->getAcademicUpdateNotification()) ? $organization->getOrganization()->getAcademicUpdateNotification() : false;
        $loggedUser->setAcademicUpdateNotification($academicUpdateNotification);
        $referForAcademicAssistance = ($organization->getOrganization()->getReferForAcademicAssistance()) ? $organization->getOrganization()->getReferForAcademicAssistance() : false;
        $loggedUser->setReferForAcademicAssistance($referForAcademicAssistance);
        $sendToStudent = ($organization->getOrganization()->getSendToStudent()) ? $organization->getOrganization()->getSendToStudent() : false;
        $loggedUser->setSendToStudent($sendToStudent);

        return $loggedUser;
    }

    /**
     * Getting all Primary Coordinators of an Organization
     * TODO: Remove "Primary coordinator" from the parameters that are required. The name of the method tells us this. OR
     * TODO: Change the name of the method to match its purpose
     *
     * @param integer $orgId
     * @param integer $langId
     * @param string $type ,
     *            description: $type = "Primary coordinator"
     * @return OrganizationRole[]
     */
    public function getAllPrimaryCoordinators($orgId, $langId = 1, $type = 'Primary coordinator')
    {
        $this->languageMasterRepository = $this->repositoryResolver->getRepository(PersonConstant::LANGUAGE_MASTER_REPO);
        $this->roleLangRepository = $this->repositoryResolver->getRepository(PersonConstant::ROLE_LANG_REPO);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(PersonConstant::ORG_ROLE_REPO);
        $lang = $this->languageMasterRepository->find($langId);
        $roleLang = $this->roleLangRepository->findOneBy(array(
            'lang' => $lang,
            'roleName' => $type
        ));
        $coordinators = $this->organizationRoleRepository->findBy(array(
            PersonConstant::FIELD_ORGANIZATION => $orgId,
            'role' => $roleLang->getRole()
        ));
        return $coordinators;
    }


    /**
     * Gets the person ID of the first primary coordinator for the organization
     *
     * @param int $organizationId
     * @return int
     */
    public function getFirstPrimaryCoordinatorPersonId($organizationId)
    {
        $primaryCoordinatorId = $this->organizationRoleRepository->findFirstPrimaryCoordinatorIdAlphabetically($organizationId);
        return $primaryCoordinatorId;
    }


    /**
     * Gets the person object of the first primary coordinator for the organization
     *
     * @param int $organizationId
     * @return Person
     */
    public function getFirstPrimaryCoordinatorPerson($organizationId)
    {
        $primaryCoordinatorId = $this->getFirstPrimaryCoordinatorPersonId($organizationId);
        $primaryCoordinatorObject = $this->personRepository->find($primaryCoordinatorId);
        return $primaryCoordinatorObject;
    }


    /**
     *
     * @param unknown $organizationid
     * @param unknown $personid
     */
    public function deleteCoordinator($organizationid, $personid)
    {
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(PersonConstant::ORG_ROLE_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $coordinator = $this->organizationRoleRepository->findOneBy(array(
            PersonConstant::FIELD_ORGANIZATION => $organizationid,
            PersonConstant::FIELD_PERSON => $personid
        ));
        if (!isset($coordinator)) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        } else {

            $this->organizationRoleRepository->remove($coordinator);
        }
        $this->personRepository->flush();
        return;
    }

    public function getPerson($id)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_FACULTY_REPO);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_STUDENT);
        $personDetails = $this->personRepository->find($id);
        $responseArray = array();
        if (!$personDetails) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        } else {
            $personDetail = $this->personRepository->getPersonDetails($personDetails);
            if (count($personDetail) > 0) {
                $contacts = $personDetail[0]['contacts'][0];
                $isCoordinator = $this->getCoordinatorById($personDetails->getOrganization()
                    ->getId(), $id);
                if ($isCoordinator && count($isCoordinator) > 0) {
                    $responseArray['person_type'] = $isCoordinator[0]['role'];
                } else {
                    $roleArray = array();
                    $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
                        PersonConstant::FIELD_PERSON => $personDetails,
                        PersonConstant::FIELD_ORGANIZATION => $personDetails->getOrganization()
                    ));
                    if ($orgPersonFaculty) {
                        $roleArray[] = PersonConstant::STAFF;
                    }

                    $orgPersonStudent = $this->orgPersonStudentRepository->findBy(array(
                        PersonConstant::FIELD_PERSON => $personDetails,
                        PersonConstant::FIELD_ORGANIZATION => $personDetails->getOrganization()
                    ));
                    if ($orgPersonStudent) {
                        $roleArray[] = PersonConstant::STUDENT;
                    }

                    $role = implode(",", $roleArray);
                    $responseArray['person_type'] = $role;
                }

                $responseArray['person_id'] = $personDetails->getId();
                $responseArray['person_first_name'] = $personDetails->getFirstname();
                $responseArray['person_last_name'] = $personDetails->getLastName();
                $responseArray[PersonConstant::FILED_TITLE] = $personDetails->getTitle();
                $responseArray['person_email'] = $contacts['primaryEmail'];
                if (isset($contacts['primaryMobile'])) {
                    $responseArray['person_mobile'] = $contacts['primaryMobile'];
                    $responseArray['is_mobile'] = true;
                } else {
                    $responseArray['person_mobile'] = $contacts['homePhone'];
                    $responseArray['is_mobile'] = false;
                }
                $responseArray['organization_id'] = $personDetails->getOrganization()->getId();
            } else {
                throw new ValidationException([
                    PersonConstant::ERROR_PERSON_NOT_FOUND
                ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
            }
        }
        return $responseArray;
    }

    /**
     * This method updates my account details
     *
     * @param MyAccountDto $myAccountDto
     * @throws SynapseValidationException
     * @return array
     */
    public function updateMyAccount(MyAccountDto $myAccountDto)
    {
        $personId = $myAccountDto->getPersonId();
        $person = $this->personRepository->find($personId);
        $responseArray = array();
        $this->isPersonFound($person);
        $personDetail = $this->personRepository->getPersonDetails($person);
        $contacts = $personDetail[0]['contacts'][0];
        $email = $contacts['primaryEmail'];
        $userRole = null;
        $isCoordinator = $this->organizationRoleRepository->findOneBy([
            'organization' => $person->getOrganization()->getId(),
            'person' => $person->getId()]);
        $userRole = $this->getUserRole($isCoordinator, $person);
        $userRole = trim($userRole);
        $userRole = ucfirst(strtolower($userRole));
        $emailKey = 'MyAccount_Updated_Staff';
        $organizationId = $person->getOrganization()->getId();
        $organizationLang = $this->orgService->getOrganizationDetailsLang($organizationId);
        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $organizationLang->getLang()
            ->getId());
        if ($myAccountDto->getPassword()) {
            if (!$this->dataProcessingUtilityService->validatePasswordStrength($myAccountDto->getPassword())) {
                throw new SynapseValidationException(["The password does not meet the password policy requirements."], 'The password does not meet the password policy requirements', 'password_policy');
            }
            $encoder = $this->encoderFactory->getEncoder($person);
            $encryptPassword = $encoder->encodePassword($myAccountDto->getPassword(), $person->getSalt());
            $person->setPassword($encryptPassword);
        }
        if ($myAccountDto->getIsMobileChanged()) {
            $contact = $this->contactInfoRepository->find($contacts['id']);
            $this->isContactExist($contact);
            $contact->setPrimaryMobile($myAccountDto->getPersonMobile());
        }
        $this->personRepository->flush();
        if ($emailTemplate) {
            $tokenValues = array();
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            if ($systemUrl) {
                $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            $emailBody = $emailTemplate->getBody();
            $supportEmail = $this->ebiConfigRepository->findOneByKey($userRole . '_Support_Helpdesk_Email_Address');
            if ($supportEmail) {
                $tokenValues['Support_Helpdesk_Email_Address'] = $supportEmail->getValue();
            }
            $tokenValues['firstname'] = $person->getFirstname();
            $updateFields = "";
            if ($myAccountDto->getIsMobileChanged()) {
                $updateFields .= "&bull; &nbsp; Mobile phone <br/>";
            }
            if ($myAccountDto->getPassword()) {
                $updateFields .= "&bull; &nbsp; Password <br/>";
            }
            $tokenValues['Updated_MyAccount_fields'] = $updateFields;
            $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
            $responseArray['email_detail'] = array(
                'from' => $emailTemplate->getEmailTemplate()->getFromEmailAddress(),
                'subject' => $emailTemplate->getSubject(),
                'bcc' => $emailTemplate->getEmailTemplate()->getBccRecipientList(),
                'body' => $emailBody,
                'to' => $email,
                'emailKey' => $emailKey,
                'organizationId' => $organizationId
            );
            $emailInst = $this->emailService->sendEmailNotification($responseArray['email_detail']);
            $send = $this->emailService->sendEmail($emailInst);
            $responseArray['message'] = $this->isMailSend($send, $email);
        }

        $responseArray = array_merge($responseArray, $this->getPerson($personId));
        return $responseArray;
    }

    public function createCoordinator(CoordinatorDTO $coordinatorDTO)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($coordinatorDTO);
        $this->logger->debug(" Creating Coordinator -  " . $logContent);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $this->orgRepository = $this->repositoryResolver->getRepository(PersonConstant::ORG_REPO);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(PersonConstant::ORG_ROLE_REPO);
        $this->roleRepository = $this->repositoryResolver->getRepository(PersonConstant::ROLE_REPO);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_FACULTY_REPO);
        $organization = $this->orgRepository->find($coordinatorDTO->getOrganizationid());
        if (!isset($organization)) {
            $this->logger->error("Person Service - createCoordinator - " . PersonConstant::ORG_NOT_FOUND);
            throw new ValidationException([
                PersonConstant::ORG_NOT_FOUND
            ], PersonConstant::ORG_NOT_FOUND, 'org_not_found');
        }

        $role = $this->roleRepository->find($coordinatorDTO->getRoleid());
        if (!isset($role)) {
            $this->logger->error("Person Service - createCoordinator - " . PersonConstant::ERROR_ROLE_NOT_FOUND);
            throw new ValidationException([
                PersonConstant::ERROR_ROLE_NOT_FOUND
            ], PersonConstant::ERROR_ROLE_NOT_FOUND, 'role_not_found');
        }

        $person = new Person();
        $person->setFirstname($coordinatorDTO->getFirstname());
        $person->setLastname($coordinatorDTO->getLastname());
        $person->setUsername($coordinatorDTO->getEmail());
        $person->setTitle($coordinatorDTO->getTitle());
        $person->setOrganization($organization);
        $person->setExternalId('C' . uniqid());
        $contact = new Contactinfo();
        if ($coordinatorDTO->getIsmobile()) {
            $contact->setPrimarymobile($coordinatorDTO->getPhone());
        } else {
            $contact->setHomephone($coordinatorDTO->getPhone());
        }
        $contact->setPrimaryemail($coordinatorDTO->getEmail());
        $validator = $this->container->get('validator');
        $errors = $validator->validate($contact);
        $this->isDuplicate($errors);
        $person->addContact($contact);
        /**
         * Adding coordinator as staff also
         */
        $this->personRepository->createPerson($person);

        $orgFaculty = new OrgPersonFaculty();
        $orgFaculty->setPerson($person);
        $orgFaculty->setOrganization($organization);
        $this->orgPersonFacultyRepository->persist($orgFaculty);

        $orgRole = new OrganizationRole();
        $orgRole->setOrganization($organization);
        $orgRole->setPerson($person);
        $orgRole->setRole($role);
        $this->organizationRoleRepository->createCoordinator($orgRole);
        $this->personRepository->flush();
        $coordinatorDTO->setId($person->getId());

        //$facultyUploadService = $this->container->get('faculty_upload_service');
        //$facultyUploadService->updateDataFile($organization->getId());

        $people = $this->cache->fetch("organization.{$organization->getId()}.people");
        $people = $people ? $people : [];
        $people[$person->getId()] = $person->getExternalId();
        $this->cache->save("organization.{$organization->getId()}.people", $people);

        return $coordinatorDTO;
    }

    public function updateCoordinator(CoordinatorDTO $coordinatorDTO)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($coordinatorDTO);
        $this->logger->debug(" Updating Coordinator -  " . $logContent);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(PersonConstant::ORG_ROLE_REPO);
        $this->roleRepository = $this->repositoryResolver->getRepository(PersonConstant::ROLE_REPO);
        $this->orgRepository = $this->repositoryResolver->getRepository(PersonConstant::ORG_REPO);
        $person = $this->personRepository->find($coordinatorDTO->getId());
        if (!isset($person)) {
            $this->logger->error(PersonConstant::ERROR_PERSON_NOT_FOUND);
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }
        $organization = $this->orgRepository->find($coordinatorDTO->getOrganizationid());
        if (!isset($organization)) {
            $this->logger->error("Person Service - updateCoordinator - " . PersonConstant::ORG_NOT_FOUND);
            throw new ValidationException([
                PersonConstant::ORG_NOT_FOUND
            ], PersonConstant::ORG_NOT_FOUND, 'org_not_found');
        }
        $role = $this->roleRepository->find($coordinatorDTO->getRoleid());
        if (!isset($role)) {
            $this->logger->error("Person Service - updateCoordinator - " . PersonConstant::ERROR_ROLE_NOT_FOUND);
            throw new ValidationException([
                PersonConstant::ERROR_ROLE_NOT_FOUND
            ], PersonConstant::ERROR_ROLE_NOT_FOUND, 'role_not_found');
        }

        $person->setFirstname($coordinatorDTO->getFirstname());
        $person->setLastname($coordinatorDTO->getLastname());
        $person->setUsername($coordinatorDTO->getEmail());
        $person->setTitle($coordinatorDTO->getTitle());
        $contact = $person->getContacts()->first();
        if ($contact) {
            if ($coordinatorDTO->getIsmobile()) {
                $contact->setPrimarymobile($coordinatorDTO->getPhone());
            } else {
                $contact->setHomephone($coordinatorDTO->getPhone());
            }
            $contact->setPrimaryemail($coordinatorDTO->getEmail());
            $validator = $this->container->get('validator');
            $errors = $validator->validate($contact);
            $this->isDuplicate($errors);
        }

        $orgRole = $this->organizationRoleRepository->findOneBy(array(
            PersonConstant::FIELD_ORGANIZATION => $person->getOrganization(),
            PersonConstant::FIELD_PERSON => $person
        ));
        if (!$orgRole) {
            $orgrole = new OrganizationRole();
            $orgrole->setOrganization($organization);
            $orgrole->setPerson($person);
            $orgrole->setRole($role);
            $this->organizationRoleRepository->createCoordinator($orgrole);
        } else {
            $orgRole->setRole($role);
        }

        $this->personRepository->flush();
        $coordinatorDTO->setId($person->getId());
        //$facultyUploadService = $this->container->get('faculty_upload_service');
        //$facultyUploadService->updateDataFile($organization->getId());
        return $coordinatorDTO;
    }

    public function getPersonPermission($loggedUserId, $userRole, $orgFeatures)
    {
        $personPermission = [];

        /*
         * if (stristr($userRole, 'coordinator')) { $personPermission = $orgFeatures; } else {
         */
        $features = $this->container->get('orgpermissionset_service')->getFeaturesBlockPermission($loggedUserId);
        foreach ($features['features'] as $feature) {
            $featureName = str_replace(' ', '_', strtolower($feature['name']));
            $featureName_share = $featureName . '_share';
            $access = $this->getUserAccess($feature);
            $personPermission[$featureName] = $access;
            unset($feature['id'], $feature['name']);
            $personPermission[$featureName_share] = $feature;
        }
        // }
        return $personPermission;
    }

    public function getUserFromToken($token)
    {
        return $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO)->getUserIdFromRefreshToken($token);
    }

    public function find($id)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $person = $this->personRepository->find($id);
        if (!$person) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, 'Person_not_found');
        }
        return $person;
    }

    public function findByUsername($email)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $person = $this->personRepository->findOneByUsername($email);
        return $person;
    }

    public function findByAuthUsername($username)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $person = $this->personRepository->findOneByAuthUsername($username);
        return $person;
    }

    public function findByOrgAuthUsername($orgId, $username)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $person = $this->personRepository->findOneBy(['organization' => $orgId, 'authUsername' => $username]);
        return $person;
    }

    public function sendPersonEmailActivation($personID)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        return $this->personRepository->sendPersonEmailActivation($personID);
    }

    /**
     * Updates Existing Person
     */
    public function updatePerson(Person $person)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        return $this->personRepository->update($person);
    }

    /**
     * Updates Existing Persons
     * @param Person[] $persons
     * @return bool
     */
    public function updatePersons($persons)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);

        try {
            foreach ($persons as $person) {
                $this->personRepository->update($person);
            }
        } catch (ValidationException $e) {
            return false;
        }
    }

    public function getRoleById($id)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        return $this->personRepository->getRoleById($id);
    }

    /**
     * @param $id
     * @return Person
     */
    public function findPerson($id)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        $person = $this->personRepository->find($id);
        if (!$person) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }
        return $person;
    }

    /**
     * Get person by external ID
     */
    public function findOneByExternalId($externalId)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        return $this->personRepository->findOneByExternalId($externalId);
    }

    /**
     * Get person by external ID in org
     */
    public function findOneByExternalIdOrg($externalId, $organizationId)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        return $this->personRepository->findOneBy([
            'externalId' => $externalId,
            PersonConstant::FIELD_ORGANIZATION => $organizationId
        ]);
    }

    /**
     * Get person by external IDs
     */
    public function findByExternalId($externalIds)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        return $this->personRepository->findByExternalId($externalIds);
    }

    public function getUserType($user)
    {
        $request = Request::createFromGlobals();
        $switchUser = $request->headers->get('switch-user');
        if ($switchUser != null) {
            $user = $this->container->get('proxy_user');
            if (! $user) {
                throw new AccessDeniedException();
            }
        }
        $isCoordinator = $this->getCoordinatorById($user->getOrganization()
            ->getId(), $user->getId());
        return $this->getUserRole($isCoordinator, $user);
    }


    /**
     * @param int $personId
     * @return string
     */
    public function getAuthUsername($personId)
    {
        $personRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_REPO);
        /** @var Person $person */
        $person = $personRepository->findOneBy([
            'id' => $personId
        ]);

        if (!$person) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }

        return $person->getAuthUsername();
    }

    /**
     * Gets details of the skyfactor user
     *
     * @param $userId
     * @return array
     */
    public function getSkyFactorUserDetails($userId)
    {
        $this->logger->debug(">>>>Is Skyfactor User" . $userId);
        $details = [];
        $skyFactorUser = $this->personRepository->findBy(array(
            'id' => $userId,
            'organization' => -1
        ));
        if (!empty($skyFactorUser)) {
            $orgRole = $this->organizationRoleRepository->findOneByPerson($skyFactorUser[0]->getId());
            $roleDetails = $this->roleLangRepository->findOneByRole($orgRole->getRole()->getId());
            if ($roleDetails->getRoleName() == 'Skyfactor Admin' || $roleDetails->getRoleName() == 'Mapworks Admin') {
                $details = ["roleName" => $roleDetails->getRoleName()];
            }
        }
        return $details;
    }

    public function getPrimryCoordinatorSortedByName($orgId, $type) {

        $orgLang = $this->orgService->getOrganizationDetailsLang($orgId);
        $orgLangId = $orgLang->getLang()->getId();
        $primaryCoordinators = $this->getAllPrimaryCoordinators($orgId, $orgLangId, $type);
        $firstName = "";
        $lastName = "";
        $userId = "";
        $userDetails = [];
        $coordinatorArr = [];
        $coordinatorName = "";
        if($primaryCoordinators){
            foreach ($primaryCoordinators as $coordinator){
                $firstName = $coordinator->getPerson()->getFirstname();
                $lastName = $coordinator->getPerson()->getLastname();
                $userId = $coordinator->getPerson()->getId();
                $userDetails[] = array("fname"=>$firstName,"lname"=>$lastName,"id"=>$userId);
            }
        }
        $coordinatorArr = $this->dataProcessingUtilityService->sortMultiDimensionalArray($userDetails, 'fname,lname', 'ASC');
        if(count($coordinatorArr)>0){
            $firstName = $coordinatorArr[0]['fname'];
            $lastName = $coordinatorArr[0]['lname'];
            $coordinatorName = $lastName.','.$firstName;
        }
        return $coordinatorName;
    }

    /**
     * Used to generate the authentication key for the person using default encryption patterns
     *
     * @param int $externalId - The external ID of the person to get an auth key for
     * @param string $entityName - The object name to use as part of the encrypted key
     * @return string Base 64 encoded encrypted auth key for the given person
     */
    public function generateAuthKey($externalId, $entityName)
    {
        return base64_encode(
            Helper::encrypt(
                $externalId . '::' . strtolower($entityName)
            )
        );
    }

    /**
     * Verify existence of primary email
     *
     * @param string $email
     * @return bool
     */
    public function primaryEmailExists($email)
    {
        $contact = $this->contactInfoRepository->findOneBy(['primaryEmail' => $email]);
        $result = ($contact) ? true : false;
        return $result;
    }


    /**
     * Gets the person object using different authentication variables used during the generation of token
     *
     * TODO::Move this function to a authentication related service ie. TokenService, ClientService, etc..
     *
     * @param array $inputData -  The request data sent while login
     * @return Person
     * @throws UnauthorizedException
     */
    public function getPersonFromAuthenticationVariables($inputData)
    {

        $authenticationType = $inputData['grant_type'];
        $devErrorMessage = '';
        $userErrorMessage = '';

        switch ($authenticationType) {

            case "refresh_token" :
                $refreshToken = $inputData['refresh_token'];
                $refreshTokenObject = $this->refreshTokenRepository->findOneBy(['token' => $refreshToken]);
                if ($refreshTokenObject) {
                    $personId = $refreshTokenObject->getUser()->getId();
                    $personObject = $this->personRepository->findOneBy(['id' => $personId]);
                } else {
                    $personObject = null;
                    $userErrorMessage = "Your refresh token, client ID, or client secret is invalid. Please try your request again with valid credentials.";
                    $devErrorMessage = __FUNCTION__ . " Refresh Token: $refreshToken";
                }

                break;
            case  "password" :
                $personObject = $this->personRepository->findOneBy(['username' => $inputData['username']]);
                if (!$personObject && isset($inputData['campusSubdomain'])) {
                    $this->organizationLangService = $this->container->get(OrganizationlangService::SERVICE_KEY); // added here for circular  reference
                    $organizationDetail = $this->organizationLangService->getLdapLoginDetails($inputData['campusSubdomain']);
                    $personObject = $this->personRepository->findOneBy(['organization' => $organizationDetail->getId(), 'authUsername' => $inputData['username']]);
                }
                break;

            case "authorization_code" :
                $authCode = $inputData['code'];
                $authCodeObject = $this->authCodeRepository->findOneBy(['token' => $authCode]);
                if ($authCodeObject) {
                    $serviceAccountId = $authCodeObject->getUser()->getId();
                    $personObject = $this->personRepository->findOneBy(['id' => $serviceAccountId]);
                } else {
                    $personObject = null;
                    $userErrorMessage = "Your authorization code, client ID, or client secret is invalid. Please try your request again with valid credentials";
                    $devErrorMessage = __FUNCTION__ . " Authorization Code: $authCode";
                }
                break;

            case "client_credentials":
                $prependedClientIDArray = explode('_', $inputData['client_id']);
                $clientObjectId = $prependedClientIDArray[0];
                $clientID = $prependedClientIDArray[1];
                $clientSecret = $inputData['client_secret'];
                $clientObject = $this->clientRepository->findOneBy([ 'id' => $clientObjectId, 'randomId' => $clientID, 'secret' => $clientSecret]);
                if ($clientObject) {
                    $personObject = $clientObject->getPerson();
                } else {
                    $personObject = null;
                    $userErrorMessage = "Your client ID or client secret is invalid. Please try your request again with valid credentials";
                    $devErrorMessage = __FUNCTION__ . " Client Internal ID: $clientObjectId Client ID: $clientID Client Secret: $clientSecret";
                }
                break;
            default :
                $devErrorMessage = __FUNCTION__ . " Invalid grant type provided: $authenticationType";
                $userErrorMessage = "You have provided an unsupported grant_type. Please try your request again with a valid grant type.";
                throw new UnauthorizedException($devErrorMessage, $userErrorMessage, 'invalid_grant', 401);
                break;
        }

        if (!$personObject) {
            throw new UnauthorizedException($devErrorMessage, $userErrorMessage, 'invalid_grant', 401);
        } else {
            return $personObject;
        }
    }

}