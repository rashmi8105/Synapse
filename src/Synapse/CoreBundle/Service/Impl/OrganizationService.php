<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContext;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CalendarBundle\Job\SwitchOrgCalendarJob;
use Synapse\CalendarBundle\Repository\OrgCorporateGoogleAccessRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\CopyEbiPermissionSet;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\GroupConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\RestBundle\Entity\LogoDto;
use Synapse\RestBundle\Entity\OrganizationDTO;
use Synapse\RestBundle\Entity\OrgStmtUpdateDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;


/**
 * @DI\Service("org_service")
 */
class OrganizationService extends AbstractService
{
    const SERVICE_KEY = 'org_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_LANG_REPO = "SynapseCoreBundle:OrganizationLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    const ORG_NOT_FOUND = "Organization Not Found.";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_FACULTY_REPO = "SynapseCoreBundle:OrgPersonFaculty";


    const FIELD_ORGANIZATION = "organization";

    const CAMPUS_IDS = "campus_ids";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_LIST_REPO = 'SynapseCoreBundle:MetadataListValues';

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    /*
     * @var Manager
     */
    public $rbacManager;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    //Repositories

    /**
     * @var OrgAcademicTermRepository
     */
    private $academicTermRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $academicYearRepository;

    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metaDataListValuesRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationlangRepository;

    /**
     * @var OrgCorporateGoogleAccessRepository
     */
    private $orgCorporateGoogleAccessRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

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
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $orgRoleRepository;

    /**
     * @var OrgStaticListRepository
     */
    private $orgStaticListRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    //Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var JobService
     */
    private $jobService;

    /**
     * @var LanguageMasterService
     */
    private $langService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var UsersService
     */
    private $usersService;

    /**
     * OrganizationService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //Scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->securityContext = $this->container->get(SynapseConstant::SECURITY_CONTEXT_CLASS_KEY);

        //Repositories
        $this->academicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->academicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->metaDataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgCorporateGoogleAccessRepository = $this->repositoryResolver->getRepository(OrgCorporateGoogleAccessRepository::REPOSITORY_KEY);
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgStaticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);


        //Services
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->jobService = $this->container->get(JobService::SERVICE_KEY);
        $this->langService = $this->container->get(LanguageMasterService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
    }

    /**
     * @param $id
     * @return Organization
     * @throws ValidationException
     */
    public function find($id)
    {
        if(is_object($id)){
            $orgId = $id->getId();
        }else{
            $orgId = $id;
        }

        $this->organizationRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $organization = $this->organizationRepository->find($orgId);
        if (! $organization) {
            $this->logger->error(" Organization Service - find - " . self::ORG_NOT_FOUND . $orgId);
            throw new ValidationException([
                self::ORG_NOT_FOUND
            ], self::ORG_NOT_FOUND, self::ORG_NOT_FOUND);
        }
        return $organization;
    }

    /**
     *
     * @param Organization $organization
     * @throws InvalidArgumentException
     * @return Organization organization created
     */
    public function createOrganization(OrganizationDTO $organizationDTO)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($organizationDTO);
        $this->logger->debug(" Creating Organization -  " . $logContent);

        $this->organizationRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(self::ORG_LANG_REPO);
        $language = $this->langService->getLanguageById($organizationDTO->getLangid());
        $organization = new Organization();
        $organization->setSubdomain($organizationDTO->getSubdomain());
        $organization->setTimeZone($organizationDTO->getTimezone());
        $organization->setCampusId($organizationDTO->getCampusId());
        $status = ($organizationDTO->getStatus() == 'Active') ? 'A' : 'I';
        $organization->setStatus($status);
        $organization->setTier(0);
        /**
         * Set is_ldap_saml_enabled true|false
         */
        $organization->setIsLdapSamlEnabled($organizationDTO->getIsLdapSamlEnabled() ? 1 : 0);
        $validator = $this->container->get('validator');
        $errors = $validator->validate($organization);

        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();

            throw new ValidationException([
                $errorsString
            ], $errorsString, 'subdomain_duplicate_error');
        }
        // call create organization
        $this->organizationRepository->createOrganization($organization);
        $organizationLang = new OrganizationLang();
        $organizationLang->setNickName($organizationDTO->getNickName());
        $organizationLang->setOrganizationName($organizationDTO->getName());
        $organizationLang->setLang($language);
        $organizationLang->setOrganization($organization);
        $validator = $this->container->get('validator');
        $errors = $validator->validate($organizationLang);

        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();

            throw new ValidationException([
                $errorsString
            ], $errorsString, 'organizationname_duplicate_error');
        }
        // call create organizationLang
        $this->organizationlangRepository->createOrganizationLang($organizationLang);
        $this->organizationRepository->flush();
        $this->copyEBIPermissions($organization, $organizationDTO->getLangid());
        $this->container->get('group_service')->addSystemGroup(GroupConstant::SYS_GROUP_NAME, GroupConstant::SYS_GROUP_EXTERNAL_ID, $organization);

        $responseArray = array();
        $responseArray['id'] = $organization->getId();
        $responseArray['name'] = $organizationLang->getOrganizationName();
        $responseArray['nick_name'] = $organizationLang->getNickName();
        $responseArray['subdomain'] = $organization->getSubdomain();
        $responseArray['timezone'] = $organization->getTimeZone();
        $responseArray['campus_id'] = $organization->getCampusId();
        $status = ($organization->getStatus() == 'A') ? 'Active' : 'Inactive';
        $responseArray['status'] = $status;
        $responseArray['is_ldap_saml_enabled '] = $organization->getIsLdapSamlEnabled();
        $this->logger->info(">>>> Organization Created");
        return $responseArray;
    }

    public function copyEBIPermissions($organization, $organizationLangId)
    {
        /* Resque job start here */
        $jobNumber = uniqid();
        $job = new CopyEbiPermissionSet();
        $this->resque = $this->container->get('bcc_resque.resque');
        $job->args = array(
            'jobNumber' => $jobNumber,
            'organization' => $organization->getId(),
            'langId' => $organizationLangId
        );
        $this->resque->enqueue($job, true);
        return $organization;
        /* Resque job end here */
    }

    public function getOrganizationDetails($orgID)
    {
        $this->logger->debug(">>>> Getting Organization Details " . $orgID);
        $this->organizationRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $organization = $this->organizationRepository->find($orgID);
        $this->isOrgExists($organization);
        if (is_null($organization->getInactivityTimeout())) {
            $organization->setInactivityTimeout(30);
        }
        $this->logger->info(">>>> Getting Organization Details " );
        return $organization;
    }

    /**
     * Update an organization
     *
     * @param LogoDto $logoDto
     * @return Organization
     */
    public function updateOrganizationDetails(LogoDto $logoDto)
    {
        $organizationId = $logoDto->getOrganizationId();
        $organizationObject = $this->organizationRepository->find($organizationId);
        $organizationObject->setSecondaryColor($logoDto->getSecondaryColor());
        $organizationObject->setPrimaryColor($logoDto->getPrimaryColor());
        $organizationObject->setLogoFileName($logoDto->getLogoFileName());
        $organizationObject->setInactivityTimeout($logoDto->getInactivityTimeout());
        $organizationObject->setAcademicUpdateNotification($logoDto->getAcademicUpdateNotification());
        $organizationObject->setReferForAcademicAssistance($logoDto->getReferForAcademicAssistance());
        $organizationObject->setSendToStudent($logoDto->getSendToStudent());
        if ($logoDto->getSendToStudent()) {
            $organizationObject->setCanViewAbsences($logoDto->getCanViewAbsences());
            $organizationObject->setCanViewInProgressGrade($logoDto->getCanViewInProgressGrade());
            $organizationObject->setCanViewComments($logoDto->getCanViewComments());
        } else {
            $organizationObject->setCanViewAbsences(false);
            $organizationObject->setCanViewInProgressGrade(false);
            $organizationObject->setCanViewComments(false);
        }

        $calendarType = ($logoDto->getCalendarType()) ? $logoDto->getCalendarType() : '';
        $calendarToolName = ($calendarType) ? 'G' : '';

        if ($calendarToolName != $organizationObject->getPcs()) {
            if ($calendarType) {
                $event = "Calendar_Enabled";
                $organizationObject->setPcs('G');
            } else {
                $organizationObject->setPcs(NULL);
                $event = "Calendar_Disabled";
            }

            //sent a notification to all staff members about the calendar sync status
            $job = new SwitchOrgCalendarJob();
            $jobNumber = uniqid();
            $job->args = array(
                'jobNumber' => $jobNumber,
                'event' => $event,
                'organizationId' => $organizationId,
                'type' => 'coordinator',
                'pcsRemove' => $logoDto->getPcsRemove(),
                'personId' => $this->securityContext->getToken()->getUser()->getId()
            );
            $this->jobService->addJobToQueue($organizationId, SynapseConstant::ORG_CALENDAR_JOB, $job, null, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
        }
        $this->organizationRepository->updateOrganization($organizationObject);
        $this->organizationRepository->flush();

        return $organizationObject;
    }

    private function isOrgExists($organizationInstance)
    {
        if (! isset($organizationInstance)) {
            throw new ValidationException([
                self::ORG_NOT_FOUND
            ], self::ORG_NOT_FOUND, self::ORG_NOT_FOUND);
        }
    }

    private function removeOrgLang($orgLangInstance)
    {
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(self::ORG_LANG_REPO);
        if (! is_null($orgLangInstance) && count($orgLangInstance) > 0) {
            foreach ($orgLangInstance as $organizationlang) {
                $this->organizationlangRepository->remove($organizationlang);
            }
        }
    }

    private function removePerson($person, $personFaulty, $contact)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(self::PERSON_REPO);
        $orgPersonFacultyRepository = $this->repositoryResolver->getRepository(self::ORG_PERSON_FACULTY_REPO);

        if ($person) {
            $this->personRepository->remove($person);
            /*
             * Delete Form Org Person faculty
             */
            if (isset($personFaulty)) {
                $orgPersonFacultyRepository->remove($personFaulty);
            }
            if ($contact) {
                $this->contactInfoRepository->remove($contact);
            }
        }
    }

    public function deleteOrganization($id)
    {
        $this->logger->debug(">>>> Deleting Organization " . $id);
        $this->organizationRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(self::ORG_LANG_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(self::PERSON_REPO);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(self::ORG_ROLE_REPO);
        $organizationInstance = $this->organizationRepository->find($id);
        $this->isOrgExists($organizationInstance);
        $orgLangInstance = $this->organizationlangRepository->findBy(array(
            self::FIELD_ORGANIZATION => $organizationInstance
        ));
        $person = $this->personRepository->find($organizationInstance->getId());
        $this->removeOrgLang($orgLangInstance);

        $coordinators = $this->orgRoleRepository->findBy(array(
            self::FIELD_ORGANIZATION => $organizationInstance->getId()
        ));

        $orgPersonFacultyRepository = $this->repositoryResolver->getRepository(self::ORG_PERSON_FACULTY_REPO);
        if (isset($coordinators) && ! is_null($coordinators) && count($coordinators) > 0) {
            throw new ValidationException([
                "Coordinator is associated with the campus. This item cannot be removed."
            ], "Coordinator is associated with the campus. This item cannot be removed.", 'campus_remove_error');
        } else {
            foreach ($coordinators as $coordinator) {
                $person = $coordinator->getPerson();
                $this->orgRoleRepository->remove($coordinator);
                $contact = $person->getContacts()->first();
                $personFaulty = $orgPersonFacultyRepository->findOneBy([
                    'person' => $person
                ]);
                $this->removePerson($person, $personFaulty, $contact);
            }
        }

        /**
         * Checking staffs
         */
        $staffs = $orgPersonFacultyRepository->findBy(array(
            self::FIELD_ORGANIZATION => $organizationInstance->getId()
        ));
        if (isset($staffs)) {
            foreach ($staffs as $staff) {

                $person = $staff->getPerson();
                $contact = $person->getContacts()->first();

                $orgPersonFacultyRepository->remove($staff);
                $this->personRepository->remove($person);
                $this->contactInfoRepository->remove($contact);
            }
        }
        $this->organizationRepository->remove($organizationInstance);
        $this->organizationRepository->flush();
        $this->logger->info(">>>> Deleted Organization ");
        return $id;
    }

    public function getTimezones()
    {
        $this->logger->info(">>>> Getting Timezones");
        $this->metaDataListValuesRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:MetadataListValues");
        return $this->metaDataListValuesRepository->getTimezones();
    }

    public function updateCustomConfidStmt(OrgStmtUpdateDto $orgStmtUpdateDto)
    {
		$logContent = $this->container->get('loggerhelper_service')->getLog($orgStmtUpdateDto);
        $this->logger->debug(" Updating Custom Confidentiality Statement -  " . $logContent);

        $this->organizationRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $organization = $this->organizationRepository->find($orgStmtUpdateDto->getOrganizationId());
        if (! isset($organization)) {
            $this->logger->error(" Organization Service - updateCustomConfidStmt - " . self::ORG_NOT_FOUND);
            throw new ValidationException([
                self::ORG_NOT_FOUND
            ], self::ORG_NOT_FOUND, 'organization_not_found');
        }

        $organization->setCustomConfidentialityStatement($orgStmtUpdateDto->getCustomConfidentialityStatement());
        $this->organizationRepository->flush();
        $this->logger->info(">>>> Updating Custom Confidentiality Statement");
        return $this->getCustomConfidStmt($organization->getId());
    }

    public function getCustomConfidStmt($orgId)
    {
        $this->logger->debug(">>>> Getting Custom Confidentiality Statement" . $orgId);
        $organization = $this->find($orgId);
        $responseArray = array();
        $responseArray['organization_id'] = $organization->getId();
        $responseArray['custom_confidentiality_statement'] = is_null($organization->getCustomConfidentialityStatement()) ? "" : $organization->getCustomConfidentialityStatement();
        $this->logger->info(">>>> Getting Custom Confidentiality Statement");
        return $responseArray;
    }

    /**
     * Get overview for an organization and a coordinator
     *
     * @param int $organizationId
     * @param int $personId
     * @throws SynapseValidationException
     * @return array
     */
    public function getOverview($organizationId, $personId)
    {
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY); //not moved to constructor for circular reference

        $this->rbacManager->checkAccessToOrganization($organizationId);

        $countResult = array();
        $organization = $this->organizationRepository->find($organizationId);
        $timeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);

        if ($organization) {
            $person = $this->personRepository->find($personId);
            if ($person) {
                $role = $this->orgRoleRepository->findOneBy(array(
                    "organization" => $organization,
                    "person" => $person
                ));

                if ($role) {
                    $organizationRoleId = $role->getRole()->getId();
                    if (isset($organizationRoleId)) {
                        $organizationLanguage = $this->organizationlangRepository->findOneBy(array(
                            "organization" => $organization
                        ));
                        $languageId = $organizationLanguage->getLang()->getId();
                        // Getting count of groups
                        $orgGroupCount = $this->organizationRepository->getCount('OrgGroup', $organizationId);
                        // org_permissionset
                        $orgPermissionSetCount = $this->organizationRepository->getCount('OrgPermissionset', $organizationId);
                        // Teams Count
                        $teamsCount = $this->organizationRepository->getCount('Teams', $organizationId);
                        $courseCount = $this->orgCoursesRepository->getCount('OrgCourses', $organizationId);
                        $academicYearCount = $this->orgCoursesRepository->getCount('OrgAcademicYear', $organizationId);
                        $academicTermsCount = $this->orgCoursesRepository->getCount('OrgAcademicTerms', $organizationId);
                        // OrgPersonFaculty
                        $orgPersonFaculty = $this->organizationRepository->getCountAndLastUpdateOfOrgPersonFaculty( $organizationId );
                        if (! empty($orgPersonFaculty)) {
                            $facultyCount = $orgPersonFaculty['faculty_count'];
                            $updateDate = new \DateTime($orgPersonFaculty['modifiedAt']);
                            $updateDate->setTimezone(new \DateTimeZone($timeZone));
                            $facultyUpdateDate = $updateDate;
                        } else {
                            $facultyCount = 0;
                            $facultyUpdateDate = '-/-/-';
                        }
                        $orgPersonStudent = $this->organizationRepository->getCountAndLastUpdateOfOrgPersonStudent( $organizationId );
                        if (! empty($orgPersonStudent)) {
                            $studentCount = $orgPersonStudent['student_count'];
                            $updateDate = new \DateTime($orgPersonStudent['modifiedAt']);
                            $studentUpdateDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, $updateDate);
                        } else {
                            $studentCount = 0;
                            $studentUpdateDate = '-/-/-';
                        }

                        $staticListCount = $this->orgStaticListRepository->getCount('OrgStaticList', $organizationId);
                        $currentAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
                        $participantStudents = '';
                        $activeParticipantStudents = '';
                        if(isset($currentAcademicYearId)){
                            $participantStudents = $this->orgPersonStudentYearRepository->getParticipantAndActiveStudents($organizationId, $currentAcademicYearId);
                            $activeParticipantStudents = $this->orgPersonStudentYearRepository->getParticipantAndActiveStudents($organizationId, $currentAcademicYearId, true);
                        }
                        $countResult['organization_id'] = $organizationId;
                        $countResult['lang_id'] = $languageId;
                        $countResult['students_count'] = $studentCount;
                        $countResult['students_participants_count'] = $participantStudents;
                        $countResult['students_active_participants_count'] = $activeParticipantStudents;
                        $countResult['students_updated_date'] = $studentUpdateDate;
                        $countResult['staff_count'] = $facultyCount;
                        $countResult['staff_updated_date'] = $facultyUpdateDate;
                        $countResult['permissions_count'] = $orgPermissionSetCount;
                        $countResult['groups_count'] = $orgGroupCount;
                        $countResult['teams_count'] = $teamsCount;
                        $countResult['academicyear_count'] = $academicYearCount;
                        $countResult['academicterm_count'] = $academicTermsCount;
                        $countResult['course_count'] = $courseCount;
                        $countResult['staticlist_count'] = $staticListCount;
                        $countResult['current_academic_year'] = isset($currentAcademicYearId) ? true : false;
                        return $countResult;
                    } else {
                        throw new SynapseValidationException('Person does not have coordinator role.');
                    }
                } else {
                    throw new SynapseValidationException('Person not mapped with role.');
                }
            } else {
                throw new SynapseValidationException('Person does not exist.');
            }
        } else {
            throw new SynapseValidationException('Organization Not Found.');
        }
    }

    public function getOrganizationDetailsLang($orgID)
    {
        $this->logger->info(">>>> Getting Organization Details Lang for Organization Id ");
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(self::ORG_LANG_REPO);
        $organization = $this->organizationlangRepository->findOneBy(array(
            'organization' => $orgID
        ));
        return $organization;
    }

    public function getListCampuses()
    {
        $this->logger->info(">>>> Getting List Campuses");
        $cache = $this->container->get('synapse_redis_cache');
        $allCampusIds = $cache->fetch(self::CAMPUS_IDS);

        if (! $allCampusIds) {
            $this->organizationRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
            $campusIds = $this->organizationRepository->getAllCampusIds();
            $allCampusIds = $campusIds[0];
            $cache->save(self::CAMPUS_IDS, $allCampusIds, 7200);
        }
        $this->logger->info(">>>> Getting List Campuses");
        return $allCampusIds;
    }

    public function getOrgnizationByCampusId($campusId)
    {
        $this->logger->debug(">>>> Getting Organization By Campus Id " . $campusId);
        $orgRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $organization = $orgRepository->findOneBy([
            'campusId' => $campusId
        ]);
        if ($organization) {
            return $organization;
        } else {
            $this->logger->error( " Organization Service - getOrgnizationByCampusId - " . self::ORG_NOT_FOUND );
            throw new ValidationException([
                self::ORG_NOT_FOUND
            ], self::ORG_NOT_FOUND, self::ORG_NOT_FOUND);
        }
    }

    /**
     * Generates auth keys for all users in all organizations
     *
     * @return bool;
     */
    public function generateAuthKeysForAllUsersInOrganization()
    {
        $organizations = $this->organizationRepository->findAll();
        // Defined personService and usersService within the function due to circular dependency
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->usersService = $this->container->get(UsersService::SERVICE_KEY);

        foreach ($organizations as $organization) {
            $organizationId = $organization->getId();
            $students = $this->usersService->getUsers($organizationId, "student");
            $staff = $this->usersService->getUsers($organizationId, "faculty");

            // Batch counter after 30 records, the job needs to run the flush command, else
            // the it will slow to a crawl when working with a lot of students
            $batchCount = 0;
            foreach ($students['student'] as $person) {
                $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy(["person" => $person['id']]);
                $orgPersonStudent->setAuthKey($this->personService->generateAuthKey($person['externalid'], 'student'));
                $this->orgPersonStudentRepository->persist($orgPersonStudent);
                if ($batchCount % SynapseConstant::DEFAULT_BATCH_SIZE == 0) {
                    $this->orgPersonStudentRepository->flush();
                }

                $batchCount++;
            }

            // Batch counter after 30 records, the job needs to run the flush command, else
            // the it will slow to a crawl when working with a lot of faculties
            $batchCount = 0;
            foreach ($staff['faculty'] as $person) {
                $orgPersonFaculty = $this->orgPersonFacultyRepository->findOneBy(["person" => $person['id']]);
                $orgPersonFaculty->setAuthKey($this->personService->generateAuthKey($person['externalid'], 'faculty'));
                $this->orgPersonFacultyRepository->persist($orgPersonFaculty);
                if ($batchCount % SynapseConstant::DEFAULT_BATCH_SIZE == 0) {
                    $this->orgPersonFacultyRepository->flush();
                }

                $batchCount++;
            }

            $this->orgPersonStudentRepository->flush();
            $this->orgPersonFacultyRepository->flush();
        }

        return true;
    }
}