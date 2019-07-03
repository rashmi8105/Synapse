<?php
namespace Synapse\CalendarBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use FOS\OAuthServerBundle\Entity\ClientManager;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CalendarBundle\Entity\OrgCorporateGoogleAccess;
use Synapse\CalendarBundle\Entity\OrgCronofyCalendar;
use Synapse\CalendarBundle\EntityDto\SyncFacultySettingsDto;
use Synapse\CalendarBundle\Job\InitialSyncJob;
use Synapse\CalendarBundle\Job\RecurrentEventJob;
use Synapse\CalendarBundle\Job\RemoveEventJob;
use Synapse\CalendarBundle\Repository\OrgCorporateGoogleAccessRepository;
use Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AccessTokenRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CalendarConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("calendarintegration_service")
 */
class CalendarIntegrationService extends AbstractService
{
    const SERVICE_KEY = 'calendarintegration_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ClientManager
     */
    private $clientManager;

    /**
     * @var Resque
     */
    private $resque;

    // Private variables

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

    /**
     * @var CronofyWrapperService
     */
    private $cronofyWrapperService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var JobService
     */
    private $jobService;

    /**
     * @var MapworksActionService
     */
    private $mapworksActionService;

    // Repositories

    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgCorporateGoogleAccessRepository
     */
    private $orgCorporateGoogleAccessRepository;

    /**
     * @var OrgCronofyCalendarRepository
     */
    private $orgCronofyCalendarRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * CalendarIntegrationService constructor.
     *
     *      @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
     *      })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Container $container
     * @param Logger $logger
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;

        $this->clientManager = $this->container->get(SynapseConstant::CLIENT_MANAGER_CLASS_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Service Initialization
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->calendarFactoryService = $this->container->get(CalendarFactoryService::SERVICE_KEY);
        $this->cronofyWrapperService = $this->container->get(CronofyWrapperService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->jobService = $this->container->get(JobService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);

        // Repository Initialization
        $this->accessTokenRepository = $this->repositoryResolver->getRepository(AccessTokenRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgCorporateGoogleAccessRepository = $this->repositoryResolver->getRepository(OrgCorporateGoogleAccessRepository::REPOSITORY_KEY);
        $this->orgCronofyCalendarRepository = $this->repositoryResolver->getRepository(OrgCronofyCalendarRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Update Sync status for a faculty
     *
     * @param SyncFacultySettingsDto $facultySettings
     * @param null|int $loggedInPersonId
     *
     * @return SyncFacultySettingsDto
     */
    public function updateFacultySyncStatus(SyncFacultySettingsDto $facultySettings, $loggedInPersonId = NULL)
    {
        $personId = $facultySettings->getPersonId();
        $organizationId = $facultySettings->getOrganizationId();
        $personObj = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $personId,
            'organization' => $organizationId
        ]);
        if ($personObj) {
            if ($facultySettings->getCalendarType() == 'google') {
                if ($facultySettings->getMafToPcs()) {
                    $mafToPcs = 'y';
                } else {
                    $mafToPcs = 'n';
                }
                if ($facultySettings->getSyncOption()) {
                    $syncStatus = 1;
                } else {
                    $syncStatus =0;
                }
                if ($facultySettings->getPcsRemove()) {
                    $pcsRemove = true;
                } else {
                    $pcsRemove = false;
                }

                // Resque Job to remove the Google events which has already synced in Google
                if ($mafToPcs == 'n') {
                    $jobNumber = uniqid();
                    $job = new RemoveEventJob();
                    $job->args = array(
                        'jobNumber' => $jobNumber,
                        'organizationId' => $organizationId,
                        'personId' => $personId,
                        'removeMafToPcs' => $pcsRemove,
                        'syncStatus' => $syncStatus,
                        'type' => 'faculty'
                    );
                    $this->jobService->addJobToQueue($organizationId, RemoveEventJob::JOB_KEY, $job, $personId, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
                    $currentNow = new \DateTime('now');
                    $personObj->setGoogleSyncDisabledTime($currentNow);
                }
                //Resque Job to sync appointments, office hours and series in Google
                if ($mafToPcs == 'y') {
                    $jobNumber = uniqid();
                    $job = new InitialSyncJob();
                    $job->args = array(
                        'jobNumber' => $jobNumber,
                        'personId' => $personId,
                        'organizationId' => $organizationId,
                        'loggedInPersonId' => $loggedInPersonId
                    );
                    $this->jobService->addJobToQueue($organizationId, InitialSyncJob::JOB_KEY, $job, $personId, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
                }
                if ($facultySettings->getPcsToMaf()) {
                    $pcsToMaf = 'y';
                } else {
                    $pcsToMaf = 'n';
                }
                $personObj->setPcsToMafIsActive($pcsToMaf);
                $personObj->setMafToPcsIsActive($mafToPcs);
                $this->calendarFactoryService->updateSyncStatus($syncStatus, $personObj);
                $this->orgPersonFacultyRepository->flush();
            }
        }
        return $facultySettings;
    }

    /**
     * Whenever the appointments, officehours, series are created
     * in mapworks, this function will be called to verify the sync status of that faculty in order to sync with Google.
     *
     * @param int $orgId
     * @param int $facultyId
     * @return array
     */
    public function facultyCalendarSettings($orgId, $facultyId)
    {
        $facultySetting = array();
        $facultySetting[CalendarConstant::KEY_CAMPUS_STATUS] = false;
        $facultySetting[CalendarConstant::KEY_CAMPUS_SETTINGS] = '';
        $facultySetting[CalendarConstant::KEY_FACULTY_MAFTOPCS] = "n";
        $facultySetting[CalendarConstant::KEY_FACULTY_PCSTOMAF] = "n";
        $facultySetting[CalendarConstant::KEY_GOOGLE_CLIENT] = null;
        $organization = $this->organizationRepository->find($orgId);
        if ($organization && $organization->getPcs() != null) {
            $facultySetting[CalendarConstant::KEY_CAMPUS_STATUS] = true;
            $facultySetting[CalendarConstant::KEY_CAMPUS_SETTINGS] = $organization->getPcs();
            $orgFaculty = $this->orgPersonFacultyRepository->findOneBy(array(
                CalendarConstant::PERSON => $facultyId,
                CalendarConstant::ORGANIZATION => $orgId
            ));
            if ($orgFaculty) {
                $facultySetting['googleClientId'] = $orgFaculty->getGoogleEmailId();
                $facultySetting['google_sync_status'] = $orgFaculty->getGoogleSyncStatus();
                $facultySetting[CalendarConstant::KEY_FACULTY_MAFTOPCS] = $orgFaculty->getMafToPcsIsActive();
                $facultySetting[CalendarConstant::KEY_FACULTY_PCSTOMAF] = $orgFaculty->getPcsToMafIsActive();
            }
        }
        $this->logger->info(" Fetched the faculty calendar settings ");
        return $facultySetting;
    }

    /**
     * Get Google sync status for a faculty
     *
     * @param int $organizationId
     * @param int $facultyId
     * @return null|SyncFacultySettingsDto
     * @throws SynapseValidationException
     */
    public function getGoogleSyncStatus($organizationId, $facultyId)
    {
        $filterParameters = [
            'person' => $facultyId,
            'organization' => $organizationId
        ];
        $personObject = $this->orgPersonFacultyRepository->findOneBy($filterParameters);
        if (empty($personObject)) {
            throw new SynapseValidationException('Person Not Found');
        }
        $cronofyCalendar = $this->orgCronofyCalendarRepository->findOneBy($filterParameters);
        if (empty($personObject->getGoogleSyncStatus()) && (!isset($cronofyCalendar) || empty($cronofyCalendar->getStatus()))) {
            $syncFacultySettingsDto = NULL;
        } else {
            $syncFacultySettingsDto = new SyncFacultySettingsDto();
            $syncFacultySettingsDto->setPersonId($personObject->getPerson()->getId());
            $syncFacultySettingsDto->setOrganizationId($personObject->getOrganization()->getId());
            if ($personObject->getPcsToMafIsActive() == 'y') {
                $pcsToMaf = true;
            } else {
                $pcsToMaf = false;
            }
            if ($personObject->getMafToPcsIsActive() == 'y') {
                $mafToPcs = true;
            } else {
                $mafToPcs = false;
            }
            $syncFacultySettingsDto->setPcsToMaf($pcsToMaf);
            $syncFacultySettingsDto->setMafToPcs($mafToPcs);
            if ($personObject->getGoogleSyncStatus() || $cronofyCalendar->getStatus()) {
                $syncStatus = true;
            } else {
                $syncStatus = false;
            }
            $syncFacultySettingsDto->setSyncOption($syncStatus);
            if ($cronofyCalendar) {
                $providerName = $this->cronofyWrapperService->getCalendarProviderName($cronofyCalendar->getCronofyProvider());
                $syncFacultySettingsDto->setProviderName($providerName);
                $syncFacultySettingsDto->setEmailId($cronofyCalendar->getCronofyProfile());
            }
        }
        return $syncFacultySettingsDto;
    }

    /**
     * Create Google OAuth redirect URL, this redirect URL will be show the OAuth screen where the
     * faculty can grant the access to enable Google calendar
     *
     * @param int $personId
     * @param int $organizationId
     * @param string $accessToken
     * @param string $type
     * @return string
     */
    public function enableOAuth($personId, $organizationId, $accessToken, $type)
    {
        $this->clientId = $this->ebiConfigService->get('Google_Client_Id');
        $this->clientSecret = $this->ebiConfigService->get('Google_Client_Secret');
        $client = new \Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->setAccessType("offline");
        $client->setApprovalPrompt('force');
        $client->addScope(CalendarConstant::GOOGLE_SCOPE_PROFILE);
        $client->addScope(CalendarConstant::GOOGLE_SCOPE_CALENDAR);
        $client->addScope(CalendarConstant::GOOGLE_SCOPE_EMAIL);
        $client->setApplicationName("Mapworks Appointment Sync");
        $this->validateAccessToken($accessToken, $personId);
        $clientToken = $this->clientManager->createClient();
        $oneTimeToken = $clientToken->getRandomId();
        if ($type == 'organization') {
            $orgCorporateAccess = $this->orgCorporateGoogleAccessRepository->findOneBy([
                'organization' => $organizationId
            ]);
            $organization = $this->organizationRepository->find($organizationId);
            $person = $this->personRepository->find($personId);
            if (empty($orgCorporateAccess)) {
                $orgCorporateAccess = new OrgCorporateGoogleAccess();
                $orgCorporateAccess->setOrganization($organization);
                $orgCorporateAccess->setPerson($person);
                $orgCorporateAccess->setOauthOneTimeToken($oneTimeToken);
                $this->orgCorporateGoogleAccessRepository->create($orgCorporateAccess);
            } else {
                $orgCorporateAccess->setPerson($person);
                $orgCorporateAccess->setOauthOneTimeToken($oneTimeToken);
            }
            $this->orgCorporateGoogleAccessRepository->flush();
        } else {
            $personObj = $this->orgPersonFacultyRepository->findOneBy([
                'person' => $personId,
                'organization' => $organizationId
            ]);
            $personObj->setOauthOneTimeToken($oneTimeToken);
            $this->orgPersonFacultyRepository->flush();
        }
        $state = $accessToken . '&&queryString&&' . $oneTimeToken . '&&queryString&&' . $type;
        $client->setState($state);
        $redirectURL = $this->ebiConfigService->get('System_API_URL') . 'api/v1/oauth/google';
        $client->setRedirectUri($redirectURL);
        $authUrl = $client->createAuthUrl();
        return $authUrl;
    }

    /**
     * Validate Google access token,
     *
     * @param string $token
     * @param int $personId
     * @throws ValidationException
     * @return null|string
     */
    private function validateAccessToken($token, $personId)
    {
        $this->logger->debug("Validate AccessToken" . $token);
        $accessToken = $this->accessTokenRepository->findOneBy(['token' => $token, 'user' => $personId]);
        if (!$accessToken) {
            $this->logger->error("Invalid Access Token");
            throw new ValidationException([
                'Invalid Access Token.'
            ], 'Invalid Access Token', 'invalid_token');
        }
        $expireDate = $this->accessTokenRepository->getAccessTokenExpireTime($personId, $token);
        if ($expireDate && time() > $expireDate) {
            $this->logger->error("Access Token Expired");
            throw new ValidationException([
                'Access Token Expired'
            ], 'Access Token Expired', 'token_time_expire');
        }
        return $accessToken;
    }

    /**
     * If the faculty has granted access, then OAuth screen will redirected to Mapworks, where we have to store that
     * AccessToken and RefreshToken.
     *
     * @param string $state
     * @param string $code
     * @param string $error
     * @return string
     */
    public function processOauthToken($state, $code, $error = '')
    {
        $googleState = explode('&&queryString&&', $state);
        $accessToken = $googleState[0];
        $oneTimeToken = $googleState[1];
        $type = $googleState[2];
        if ($error) {
            $systemURL = $this->cancelOAuth($type, $oneTimeToken);
        } else {
            $clientId = $this->ebiConfigService->get('Google_Client_Id');
            $clientSecret = $this->ebiConfigService->get('Google_Client_Secret');
            $client = new \Google_Client();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setAccessType("offline");
            $client->setApprovalPrompt('force');
            $client->addScope(CalendarConstant::GOOGLE_SCOPE_PROFILE);
            $client->addScope(CalendarConstant::GOOGLE_SCOPE_CALENDAR);
            $client->addScope(CalendarConstant::GOOGLE_SCOPE_EMAIL);
            $redirectURL = $this->ebiConfigService->get('System_API_URL') . 'api/v1/oauth/google';
            $client->setRedirectUri($redirectURL);
            $client->authenticate($code);
            $token = $client->getAccessToken();
            $client->setAccessToken($token);
            $refreshToken = $client->getRefreshToken();
            $googlePlus = new \Google_Service_Plus($client);
            $person = $googlePlus->people->get('me');
            $email = $person['emails'][0]['value'];
            if ($type == 'organization') {
                $systemURL = $this->processOauthForOrganization($accessToken, $oneTimeToken, $token, $refreshToken);
            } else {
                $systemURL = $this->processOauthForFaculty($accessToken, $oneTimeToken, $token, $refreshToken, $email, $type);
            }
        }
        return $systemURL;
    }

    /**
     * Process Google OAuth in organization level and store the details in Mapwork table
     *
     * @param string $accessToken
     * @param string $oneTimeToken
     * @param string $token
     * @param string $refreshToken
     * @return string
     * @throws ValidationException
     */
    public function processOauthForOrganization($accessToken, $oneTimeToken, $token, $refreshToken)
    {
        $oAuthCredentials = $this->orgCorporateGoogleAccessRepository->findOneBy([
            'oauthOneTimeToken' => $oneTimeToken
        ]);
        if (empty($oAuthCredentials)) {
            throw new ValidationException([
                'Invalid Access Token.'
            ], 'Invalid Access Token', 'invalid_token');
        }
        $personId = $oAuthCredentials->getPerson()->getId();
        $systemURL = $this->ebiConfigService->getSystemUrl() . '#/configDone';
        $this->validateAccessToken($accessToken, $personId);
        $oAuthCredentials->setOauthCalAccessToken($token);
        $oAuthCredentials->setStatus(1);
        $oAuthCredentials->setOauthCalRefreshToken($refreshToken);
        $oAuthCredentials->setOauthOneTimeToken(NULL);
        $this->orgCorporateGoogleAccessRepository->flush();
        return $systemURL;
    }

    /***
     * Process Google OAuth in faculty level and store the details in Mapwork table
     *
     * @param string $accessToken
     * @param string $oneTimeToken
     * @param string $token
     * @param string $refreshToken
     * @param string $email
     * @param string $type
     * @return string
     * @throws ValidationException
     */
    public function processOauthForFaculty($accessToken, $oneTimeToken, $token, $refreshToken, $email, $type)
    {
        $oAuthCredentials = $this->orgPersonFacultyRepository->findOneBy([
            'oauthOneTimeToken' => $oneTimeToken
        ]);
        if (empty($oAuthCredentials)) {
            throw new ValidationException([
                'Invalid Access Token.'
            ], 'Invalid Access Token', 'invalid_token');
        }
        $personId = $oAuthCredentials->getPerson()->getId();
        $organizationId = $oAuthCredentials->getPerson()->getOrganization()->getId();
        $systemURL = $this->ebiConfigService->getSystemUrl() . '#/configDone';
        $oAuthCredentials->setOauthCalAccessToken($token);
        $this->validateAccessToken($accessToken, $personId);
        $oAuthCredentials->setGoogleEmailId($email);
        if ($type == 'maftopcs') {
            $oAuthCredentials->setMafToPcsIsActive('y');
            $oAuthCredentials->setPcsToMafIsActive('n');
        } else if ($type == 'pcstomaf') {
            $oAuthCredentials->setPcsToMafIsActive('y');
            $oAuthCredentials->setMafToPcsIsActive('n');
        } else {
            $oAuthCredentials->setMafToPcsIsActive('y');
            $oAuthCredentials->setPcsToMafIsActive('y');
        }
        $oAuthCredentials->setGoogleSyncStatus(1);
        $oAuthCredentials->setOauthCalRefreshToken($refreshToken);
        $oAuthCredentials->setOauthOneTimeToken(NULL);
        $this->orgPersonFacultyRepository->flush();

        // If the sync is enabled then all the future appointments, office hours and series should be synced to Google.
        // This job will do that.
        if ($type != 'pcstomaf') {
            $jobNumber = uniqid();
            $job = new InitialSyncJob();
            $job->args = array(
                'jobNumber' => $jobNumber,
                'personId' => $personId,
                'organizationId' => $organizationId
            );
            $this->resque->enqueue($job, true);
        }
        return $systemURL;
    }


    /**
     * Send Notification to all faculties in the organization when the calendar sync is enabled or disabled.
     *
     * @param int $organizationId
     * @param string $event
     * @param string $calendarType
     */
    public function sendSyncNotificationToFaculties($organizationId, $event, $calendarType)
    {
        $facultyList = $this->orgPersonFacultyRepository->findBy([
            'organization' => $organizationId
        ]);
        $organization = $this->organizationRepository->find($organizationId);
        if (!empty($facultyList)) {
            foreach ($facultyList as $faculty) {
                $faculty->setGoogleEmailId(NULL);
                $faculty->setOauthOneTimeToken(NULL);
                $faculty->setOauthCalAccessToken(NULL);
                $faculty->setOauthCalRefreshToken(NULL);
                $faculty->setGoogleSyncStatus(0);
                $faculty->setPcsToMafIsActive('n');
                $faculty->setMafToPcsIsActive('n');
                $person = $faculty->getPerson();
                $this->alertNotificationService->createNotification($event, $calendarType, $person, null, null, null, null, null, null, $organization);
            }
            $this->orgPersonFacultyRepository->flush();
        }
    }

    /**
     * Function to return the whether the corporate access enabled for the organization
     *
     * @param int $orgId
     * @return boolean
     */
    public function getGoogleCorporateAccess($orgId)
    {
        $status = NULL;
        $orgCorporateAccess = $this->orgCorporateGoogleAccessRepository->findOneBy([
            'organization' => $orgId
        ]);
        if (!empty($orgCorporateAccess)) {
            $status = $orgCorporateAccess->getStatus();
        }
        return $status;
    }

    /**
     * If the user denied the permission, then clear onetime token and redirect them back to mapworks
     *
     * @param string $type
     * @param string $oneTimeToken
     * @return string
     */
    private function cancelOAuth($type, $oneTimeToken)
    {
        if ($type == 'organization') {
            $oAuthCredentials = $this->orgCorporateGoogleAccessRepository->findOneBy([
                'oauthOneTimeToken' => $oneTimeToken
            ]);
            $oAuthCredentials->setOauthOneTimeToken(NULL);
            $this->orgCorporateGoogleAccessRepository->flush();
        } else {
            $oAuthCredentials = $this->orgPersonFacultyRepository->findOneBy([
                'oauthOneTimeToken' => $oneTimeToken
            ]);
            $oAuthCredentials->setOauthOneTimeToken(NULL);
            $this->orgPersonFacultyRepository->flush();
        }
        $systemURL = $this->ebiConfigService->getSystemUrl() . '#/configDone?error=true';
        return $systemURL;
    }

    /**
     * Get faculties google email address
     *
     * @param int $organizationId
     * @param int $facultyId
     * @return string
     */
    public function getFaultyGoogleEmail($organizationId, $facultyId)
    {
        $facultyGoogleEmail = '';
        $personFaculty = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $facultyId,
            'organization' => $organizationId
        ]);
        if (!empty($personFaculty)) {
            $facultyGoogleEmail = $personFaculty->getGoogleEmailId();
        }
        return $facultyGoogleEmail;
    }

    /**
     * Find the redirection URL where the user has to provide the authentication for cronofy
     *
     * @param int $personId
     * @param int $organizationId
     * @param string $accessToken
     * @param string $type
     * @param boolean $isProxyUser
     * @return string
     */
    public function getAuthorizationURL($personId, $organizationId, $accessToken, $type, $isProxyUser)
    {
        $clientToken = $this->clientManager->createClient();
        $oneTimeToken = $clientToken->getRandomId();
        $orgCronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy([
            'person' => $personId,
            'organization' => $organizationId
        ]);
        if (empty($orgCronofyCalendarObject)) {
            $organization = $this->organizationRepository->find($organizationId);
            $person = $this->personRepository->find($personId);
            $orgCronofyCalendarObject = new OrgCronofyCalendar();
            $orgCronofyCalendarObject->setCronofyOneTimeToken($oneTimeToken);
            $orgCronofyCalendarObject->setPerson($person);
            $orgCronofyCalendarObject->setOrganization($organization);
            $this->orgCronofyCalendarRepository->create($orgCronofyCalendarObject);
        } else {
            $orgCronofyCalendarObject->setCronofyOneTimeToken($oneTimeToken);
        }
        $this->orgCronofyCalendarRepository->flush();
        $authorizationURL = $this->cronofyWrapperService->getAuthorizationURL($accessToken, $oneTimeToken, $type, $isProxyUser);
        return $authorizationURL;
    }

    /**
     * Once the user grant the access then the details should be stored in mapwork database
     *
     * @param string $cronofyStateParameter
     * @param string $oauthAccessCode
     * @param string $cronofyError
     * @return string
     * @throws SynapseValidationException
     */
    public function processCronofyToken($cronofyStateParameter, $oauthAccessCode, $cronofyError)
    {
        $queryString = '';
        $cronofyStateParameter = explode('-queryString-', $cronofyStateParameter);
        $mafAccessToken = $cronofyStateParameter[0];
        $oneTimeToken = $cronofyStateParameter[1];
        $syncType = $cronofyStateParameter[2];
        $isProxyUser = $cronofyStateParameter[3];
        if (empty($cronofyError)) {
            $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy([
                'cronofyOneTimeToken' => $oneTimeToken
            ]);
            if (empty($cronofyCalendarObject)) {
                throw new SynapseValidationException('Invalid Access Token.');
            }
            $personId = $cronofyCalendarObject->getPerson()->getId();
            $organizationId = $cronofyCalendarObject->getOrganization()->getId();
            if ($isProxyUser) {
                $accessToken = $this->accessTokenRepository->findOneBy(['token' => $mafAccessToken]);
                if (!$accessToken) {
                    $this->logger->error("Invalid Access Token");
                    throw new SynapseValidationException('Invalid Access Token.');
                }
            } else {
                $this->validateAccessToken($mafAccessToken, $personId);
            }
            $token = $this->cronofyWrapperService->requestToken($oauthAccessCode);
            $accessToken = $token['access_token'];
            $refreshToken = $token['refresh_token'];
            $existingEmailId = $cronofyCalendarObject->getCronofyProfile();
            $existingProvider = $cronofyCalendarObject->getCronofyProvider();
            $cronofyCalendarObject->setStatus(1);
            $cronofyCalendarObject->setCronofyOneTimeToken(NULL);
            $cronofyCalendarObject->setCronofyCalAccessToken($accessToken);
            $cronofyCalendarObject->setCronofyCalRefreshToken($refreshToken);
            $profileName = $token['linking_profile']['profile_name'];
            $providerName = $token['linking_profile']['provider_name'];
            $calendarId = $this->cronofyWrapperService->getCalendarId($accessToken, $refreshToken, $profileName, $cronofyCalendarObject, $providerName);

            $cronofyCalendarObject->setCronofyProvider($providerName);
            $cronofyCalendarObject->setCronofyProfile($profileName);
            $cronofyCalendarObject->setCronofyCalendar($calendarId);
            $oAuthCredentials = $this->orgPersonFacultyRepository->findOneBy([
                'person' => $personId,
                'organization' => $organizationId
            ]);
            if ($syncType == 'maftopcs') {
                $oAuthCredentials->setMafToPcsIsActive('y');
                $oAuthCredentials->setPcsToMafIsActive('n');
            } else if ($syncType == 'pcstomaf') {
                $oAuthCredentials->setPcsToMafIsActive('y');
                $oAuthCredentials->setMafToPcsIsActive('n');
            } else {
                $oAuthCredentials->setMafToPcsIsActive('y');
                $oAuthCredentials->setPcsToMafIsActive('y');
            }
            $oAuthCredentials->setGoogleSyncStatus(0);
            $oAuthCredentials->setOauthCalRefreshToken(NULL);
            $oAuthCredentials->setOauthOneTimeToken(NULL);

            // Once the connection is established create channel to receive push notification
            $channelId = $this->cronofyWrapperService->createChannel($personId, $calendarId, $accessToken, $refreshToken, $cronofyCalendarObject);
            $cronofyCalendarObject->setCronofyChannel($channelId);
            $this->cronofyWrapperService->updateCronofyHistory($personId, $organizationId, 'Calendar Connected', $profileName, $providerName);

            // If the sync is enabled then all the future appointments, office hours and series should be synced to Google.
            // This job will do that.
            if ($syncType != 'pcstomaf') {
                $jobNumber = uniqid();
                $job = new InitialSyncJob();
                $job->args = array(
                    'jobNumber' => $jobNumber,
                    'personId' => $personId,
                    'organizationId' => $organizationId
                );
                $this->jobService->addJobToQueue($organizationId, InitialSyncJob::JOB_KEY, $job, $personId, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
            }
        } else {
            $cronofyCalendarObject = $this->orgCronofyCalendarRepository->findOneBy([
                'cronofyOneTimeToken' => $oneTimeToken
            ]);
            if ($cronofyCalendarObject) {
                $cronofyCalendarObject->setCronofyOneTimeToken(NULL);
                $personId = $cronofyCalendarObject->getPerson()->getId();
                $organizationId = $cronofyCalendarObject->getOrganization()->getId();
                $this->cronofyWrapperService->updateCronofyHistory($personId, $organizationId, 'Connection Error');
            }
            $queryString .= '?error=true';
        }
        $this->orgCronofyCalendarRepository->flush();
        $systemURL = $this->ebiConfigService->getSystemUrl() . '#/configDone' . $queryString;
        return $systemURL;
    }

    /**
     * Function to validate the users to be redirected to Google or Cronofy
     *
     * @param int $loggedUserId
     * @param int $organizationId
     * @param string $accessToken
     * @param string $type
     * @param boolean $isProxyUser
     * @return string
     */
    public function redirectToOauth($loggedUserId, $organizationId, $accessToken, $type, $isProxyUser = false)
    {
        $calendarType = $this->ebiConfigService->get('calendar_type');
        if ($calendarType == 'cronofy') {
            $oAuthUrl['redirect_url'] = $this->getAuthorizationURL($loggedUserId, $organizationId, $accessToken, $type, $isProxyUser);
        } else {
            $oAuthUrl['redirect_url'] = $this->enableOAuth($loggedUserId, $organizationId, $accessToken, $type);
        }
        return $oAuthUrl;
    }

    /**
     * Sync Appointments/Office hours (one-off events) into external calendar.
     *
     * @param int $organizationId
     * @param int $personId
     * @param int $mapworksEventId
     * @param string $eventType (appointment/office_hour)
     * @param string $actionType - (create/update/delete)
     * @param string $externalCalendarEventId
     * @param int $officeHourId
     * @return boolean
     * @throws \Exception
     */
    public function syncOneOffEvent($organizationId, $personId, $mapworksEventId, $eventType, $actionType, $externalCalendarEventId = NULL, $officeHourId = NULL)
    {
        $personCalendarSettings = $this->facultyCalendarSettings($organizationId, $personId);
        if ($personCalendarSettings['facultyMAFTOPCS'] == 'y') {
            $personCronofyObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId]);
            if ($personCronofyObject && $personCronofyObject->getStatus()) {
                $organizationTimeZone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);
                $currentTime = new \DateTime('now');
                try {
                    if ($actionType == 'delete') {
                        $this->cronofyWrapperService->deleteOneOffEvent($personCronofyObject, $mapworksEventId, $eventType, $currentTime, $externalCalendarEventId, $officeHourId, $organizationTimeZone);
                    } else {
                        $this->cronofyWrapperService->syncOneOffEvent($personCronofyObject, $mapworksEventId, $eventType, $actionType, $currentTime, $organizationTimeZone);
                    }
                } catch (\Exception $exception) {
                    $tokenValues['$$event_id$$'] = $mapworksEventId;
                    $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'event_sync_failed', 'creator', 'calendar_sync', $personId, "A calendar sync error occurred", NULL, NULL, $tokenValues);
                }
            }
        }
        return true;
    }

    /**
     * Sync office hour series slots to external calendar.
     *
     * @param int $officeHoursSeriesId
     * @param int $organizationId
     * @param int $personId
     * @param string $action - (create/update/delete)
     * @return boolean
     */
    public function syncOfficeHourSeries($officeHoursSeriesId, $organizationId, $personId, $action)
    {
        $personCalendarSettings = $this->facultyCalendarSettings($organizationId, $personId);
        if ($personCalendarSettings['facultyMAFTOPCS'] == 'y') {
            $personCronofyObject = $this->orgCronofyCalendarRepository->findOneBy(['person' => $personId]);
            if ($personCronofyObject && $personCronofyObject->getStatus()) {
                $job = new RecurrentEventJob();
                $jobNumber = uniqid();
                $job->args = array(
                    'jobNumber' => $jobNumber,
                    'officeHourSeriesId' => $officeHoursSeriesId,
                    'personId' => $personId,
                    'organizationId' => $organizationId,
                    'action' => $action
                );
                $this->jobService->addJobToQueue($organizationId, SynapseConstant::JOB_KEY_RECURRENT_EVENT, $job, $personId, SynapseConstant::RESQUE_JOB_CALENDAR_ERROR);
            }
        }
        return true;
    }
}
