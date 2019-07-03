<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Tests\Fixture\Validator\Validator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\AuthenticationBundle\Repository\OrgAuthConfigRepository;
use Synapse\CalendarBundle\Job\SwitchOrgCalendarJob;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RestBundle\Entity\OrganizationDTO;

/**
 * @DI\Service("organizationlang_service")
 */
class OrganizationlangService extends AbstractService
{

    const SERVICE_KEY = 'organizationlang_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     * @var Organizationlang repository
     */
    const ORG_LANG_REPO = 'SynapseCoreBundle:OrganizationLang';

    const LAST_UPDATED_DATE = 'institutions_last_updated';

    const ORG_NOT_FOUND = 'Organization Not Found.';

    const SUBDOMAIN_NOT_FOUND = 'Subdomain Not Found.';

    // Scaffolding
    
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var LegacyValidator
     */
    private $validator;

    // Services

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

    /**
     * @var EmailPasswordService
     */
    private $emailPasswordService;

    /**
     * @var JobService
     */
    private $jobService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var PersonService
     */
    private $personService;


    // Repositories

    /**
     * @var OrgAuthConfigRepository
     */
    private $authConfigRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationlangRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * OrganizationLangService Constructor
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);

        // Services
        $this->calendarFactoryService = $this->container->get(CalendarFactoryService::SERVICE_KEY);
        $this->emailPasswordService = $this->container->get(EmailPasswordService::SERVICE_KEY);
        $this->jobService = $this->container->get(JobService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);

        // Repositories
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
    }

    public function getOrganizations()
    {
        $organization = $this->organizationlangRepository->findAll();
        $returnArray = array();
        $returnArray['institutions'] = array();
        if ($organization) {
            $returnArray['institutions_total_count'] = count($organization);
            $returnArray[self::LAST_UPDATED_DATE] = null;

            foreach ($organization as $org) {
                if (is_null($returnArray[self::LAST_UPDATED_DATE])) {
                    $returnArray[self::LAST_UPDATED_DATE] = $org->getModifiedAt();
                } else {
                    if ($org->getModifiedAt() > $returnArray[self::LAST_UPDATED_DATE]) {
                        $returnArray[self::LAST_UPDATED_DATE] = $org->getModifiedAt();
                    }
                }
                $temp = array();
                $temp['id'] = $org->getOrganization()->getId();
                $temp['name'] = $org->getOrganizationName();
                $temp['nick_name'] = $org->getNickName();
                $temp['subdomain'] = $org->getOrganization()->getSubdomain();
                $temp['timezone'] = $org->getOrganization()->getTimeZone();
                array_push($returnArray['institutions'], $temp);
            }
        }
        $this->logger->info(">>>> Get Organizations");
        return $returnArray;
    }

    /**
     * Get Organization Details
     *
     * @param $organizationId
     * @return array
     */
    public function getOrganization($organizationId)
    {
        $organization = $this->organizationlangRepository->findOneBy([
            'organization' => $organizationId
        ]);
        if ($organization) {
            $response = array();
            $response['id'] = $organizationId;
            $response['name'] = $organization->getOrganizationName();
            $response['nick_name'] = $organization->getNickName();
            $response['subdomain'] = $organization->getOrganization()->getSubdomain();
            $response['timezone'] = $organization->getOrganization()->getTimeZone();
            $response['campus_id'] = $organization->getOrganization()->getCampusId();

            // Get Ldap saml true|false
            $organizationLDAPorSAMLEnabled = $organization->getOrganization()->getIsLdapSamlEnabled();
            $isLDAPorSAMLEnabled = $organizationLDAPorSAMLEnabled ? $organizationLDAPorSAMLEnabled : false;
            $response['is_ldap_saml_enabled'] = $isLDAPorSAMLEnabled;
            $organizationStatus = $organization->getOrganization()->getStatus();
            if ($organizationStatus == 'I') {
                $response['status'] = 'Inactive';
            } else {
                $response['status'] = 'Active';
            }

            // Get the status of calendar sync - true|false
            $calendarSync = $organization->getOrganization()->getCalendarSync() ? true : false;
            $response['calendar_sync'] = $calendarSync;

            // Number of users who enabled calendar sync
            $numberOfUsersEnabledCalendar = $this->calendarFactoryService->getCountOfCalendarSyncUsers($organizationId);
            $response['calendar_sync_users'] = $numberOfUsersEnabledCalendar;

        } else {
            throw new SynapseValidationException('Organization Not Found.');
        }
        return $response;
    }


    public function getLdapLoginDetails($subdomain)
    {

        $this->logger->info(">>>> Get Ldap Login by subdomain");
        $this->authConfigRepository = $this->repositoryResolver->getRepository('SynapseAuthenticationBundle:OrgAuthConfig');
        $organization = $this->organizationRepository->findOneBy([
            'subdomain' => $subdomain
        ]);
        if ($organization) {
            $response = new OrganizationDTO();
            $ldapSaml = $organization->getIsLdapSamlEnabled() ? $organization->getIsLdapSamlEnabled() : false;
            $authConfig = $this->authConfigRepository->findOneByOrganization($organization);
            if ($ldapSaml && $authConfig) {
                if ($authConfig->getSamlStudentEnabled() || $authConfig->getSamlStaffEnabled()) {
                    $ldapSaml = 'saml';
                }
            }
            $response->setIsLdapSamlEnabled($ldapSaml);
            $response->setSubdomain($organization->getSubdomain());
            $response->setId($organization->getId());
        } else {
            $this->logger->error("Organizationlang Service - getLdapLoginDetails - " . self::SUBDOMAIN_NOT_FOUND . $subdomain);
            throw new ValidationException([
                self::SUBDOMAIN_NOT_FOUND
            ], self::SUBDOMAIN_NOT_FOUND, 'subdomain_not_found');
        }
        return $response;
    }

    /**
     * Update the organization metadata.
     *
     * @param OrganizationDTO $organizationDTO
     * @return array
     * @throws SynapseValidationException
     */
    public function updateOrganization($organizationDTO)
    {
        $organizationId = $organizationDTO->getId();
        $organization = $this->organizationRepository->find($organizationId);

        if ($organization) {
            $organization->setSubdomain($organizationDTO->getSubdomain());
            $organization->setTimeZone($organizationDTO->getTimezone());
            $organization->setCampusId($organizationDTO->getCampusId());

            $status = ($organizationDTO->getStatus() == 'Active') ? 'A' : 'I';
            $organization->setStatus($status);

            $ldapValue = null;
            $isLDAPorSAMLenabled = $organizationDTO->getIsLdapSamlEnabled();
            if (!is_null($isLDAPorSAMLenabled)) {
                $ldapValue = 1;
            } else {
                $ldapValue = 0;
            }
            $organization->setIsLdapSamlEnabled($ldapValue);

            $isCalendarSyncEnabled = !empty($organizationDTO->getCalendarSync()) ? 1 : 0;
            $isPcsRemove = $organizationDTO->getPcsRemove();

            // Remove external events by SwitchOrgCalendarJob if sync is disabled by admin

            if (!$isCalendarSyncEnabled && $isCalendarSyncEnabled != $organization->getCalendarSync()) {
                $organization->setPcs(NULL);
                $job = new SwitchOrgCalendarJob();
                $jobNumber = uniqid();
                $job->args = array(
                    'jobNumber' => $jobNumber,
                    'organizationId' => $organizationId,
                    'type' => 'admin',
                    'pcsRemove' => $isPcsRemove
                );
                $this->jobService->addJobToQueue($organizationId, SwitchOrgCalendarJob::JOB_KEY, $job, null, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
            }
            $organization->setCalendarSync($isCalendarSyncEnabled);
            $campusErrors = $this->validator->validate($organization);
            if (count($campusErrors) > 0) {
                $errorsString = $campusErrors[0]->getMessage();
                throw new SynapseValidationException($errorsString);
            }
        } else {
            throw new SynapseValidationException('The organization could not be found.');
        }

        //Get the organization lang object, and set its new name and nickname values.
        $organizationLang = $this->organizationlangRepository->findOneBy(['organization' => $organization]);
        $organizationLang->setOrganizationName($organizationDTO->getName());
        $organizationLang->setNickName($organizationDTO->getNickName());


        //Validate the organization lang object,
        $errors = $this->validator->validate($organizationLang);
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            throw new SynapseValidationException($errorsString);
        }

        //If the isSendLink attribute is available, set invitation link emails to coordinators.
        if ($organizationDTO->getIsSendLink()) {
            $persons = $this->personService->getCoordinator($organizationId, '');

            if ($persons['coordinators'] && count($persons['coordinators']) > 0) {
                foreach ($persons['coordinators'] as $person) {
                    $this->emailPasswordService->sendEmailWithCoordinatorInvitationLink($organizationId, $person['id']);
                }
            }
        }

        $this->organizationlangRepository->flush();
        return $this->getOrganization($organizationId);
    }

}
