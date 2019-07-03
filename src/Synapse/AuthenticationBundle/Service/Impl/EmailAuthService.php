<?php
namespace Synapse\AuthenticationBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AccessTokenRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\EmailConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("emailauth_service")
 */
class EmailAuthService extends AbstractService
{
    const SERVICE_KEY = 'emailauth_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    private $rbacManager;


    // Services

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * @var UserManagementService
     */
    private $userManagementService;

    /**
     * @var UtilServiceHelper
     */
    private $utilHelperService;


    // Repositories

    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepository;

    /**
     *
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
     * EmailAuthService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     * })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        // Services
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->tokenService = $this->container->get(TokenService::SERVICE_KEY);
        $this->userManagementService = $this->container->get(UserManagementService::SERVICE_KEY);
        $this->utilHelperService = $this->container->get(UtilServiceHelper::SERVICE_KEY);

        // Repositories
        $this->accessTokenRepository = $this->repositoryResolver->getRepository(AccessTokenRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    /**
     * takes the encoded username and returns the complete url
     *
     * @param string $tokenData
     * @param boolean $isAcademicUpdate
     * @return string $url
     */
    public function emailAuth($tokenData, $isAcademicUpdate = false, $type=false, $id=false)
    {
        $this->logger->debug(" Email Auth Token Data " . $tokenData);
        $this->logger->info(" Email Authentication ");
        $decodedTokenData = base64_decode($tokenData);
        $username = Helper::decrypt($decodedTokenData);

        $baseUrl = $this->ebiConfigService->generateCompleteUrl('Email_Login_Landing_Page');

        if ($username) {
            $personObject = $this->personRepository->findOneBy(['username' => $username]);

            if ($personObject) {
                $accessToken = $this->accessTokenRepository->getAccessToken($personObject->getId());
                if (count($accessToken) == 0) {
                    $this->tokenService->generateToken($personObject->getId(), 1);
                    $accessToken = $this->accessTokenRepository->getAccessToken($personObject->getId());
                }

                $token = $accessToken[0]['token'];

                // TODO: In the case of an academic update we should have a different controller route. This is completely separated logic from standard email login
                if ($isAcademicUpdate) {
                    $orgId = $personObject->getOrganization()->getId();
                    $baseUrl = $this->ebiConfigService->generateCompleteUrl('Student_Course_List_Page', $orgId);
                }

                $returnUrl = $baseUrl . "?access_token=" . $token;
                
                // In Calendar integration, user will be redirected from Google to Mapworks and the corresponding   appointment/office hour
                // should be opened up in pop-up
                $appointmentTypes = ['appointment' => '', 'officehour' => 'I', 'series' => 'S'];
                if (in_array($type, array_keys($appointmentTypes))) {
                    $orgId = $personObject->getOrganization()->getId();
                    $queryParam = (!empty($appointmentTypes[$type])) ? '&slot_type=' . $appointmentTypes[$type] : '';
                    $type = ($type == 'series') ? 'officehour' : $type;                   
                    $baseUrl = $this->ebiConfigService->generateCompleteUrl('Faculty_Appointment_List_Page', $orgId);
                    $returnUrl = $baseUrl . "?access_token=" . $token . '&id=' . $id . '&type=' . $type . $queryParam;
                }

            }
        }

        if (empty($returnUrl)) {
            $returnUrl = $baseUrl;
        }

        return $returnUrl;
    }

    /**
     * Sends login link to students withe the username/email provided
     *
     * @param string $username
     * @throws SynapseValidationException | AccessDeniedException
     */
    public function sendStudentLoginLinkEmail($username)
    {
        $personObject = $this->personRepository->findOneBy(['username' => $username], new SynapseValidationException('The email address provided does not exist within the system'));
        $personStudentObject = $this->orgPersonStudentRepository->findOneBy(['person' => $personObject->getId()], new SynapseValidationException($username . ' does not belong to a valid student at the organization'));
        
        $studentId = $personObject->getId();
        $organizationId = $personObject->getOrganization()->getId();
        if (!$this->userManagementService->isStudentActive($studentId, $organizationId)) {
            throw new AccessDeniedException("This student is a non participant for the current academic year");
        }
        $organizationLanguage = $this->organizationLangRepository->findOneBy(['organization' => $organizationId], new SynapseValidationException('OrganizationLang not found'));
        $organizationLanguageId = $organizationLanguage->getLang()->getId();

        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey('Email_Login_Url', $organizationLanguageId);
        $accessToken = $this->accessTokenRepository->getAccessToken($studentId);
        if (count($accessToken) == 0) {
            $this->tokenService->generateToken($studentId, 1);
        }
        $usernameEncrypt = $this->dataProcessingUtilityService->encrypt($personObject->getUsername(), SynapseConstant::ENCRYPTION_METHOD, SynapseConstant::ENCRYPTION_HASH);
        $usernameEncrypt = base64_encode($usernameEncrypt);

        $systemApiUrl = $this->ebiConfigRepository->findOneBy(['key' => 'System_API_URL']);
        $studentLoginUrl = $systemApiUrl->getValue(). "/api/v1/email/$usernameEncrypt";

        $firstName = $personObject->getFirstname();
        $tokenValues['firstname'] = $firstName;
        $tokenValues['studentUrl'] = $studentLoginUrl;
        $emailArray['username'] = $username;
        $emailArray['emailKey'] = 'Email_Login_Url';
        $emailArray['organizationId'] = $organizationId;
        $emailSentStatus = $this->sendEmailNotification($emailTemplate, $tokenValues, $emailArray);
        return $emailSentStatus;
    }

    /**
     * Send Email Notification to Student User
     *
     * @param object $emailTemplate
     * @param array $tokenValues
     * @param array $emailMessage
     * @return int
     */
    protected function sendEmailNotification($emailTemplate, $tokenValues, $emailMessage)
    {
        $emailResponse = [];
        if ($emailTemplate) {
            $emailBody = $emailTemplate->getBody();
            $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
            $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
            $subject = $emailTemplate->getSubject();
            $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
            $emailResponse['email_detail'] = array(
                'from' => $from,
                'subject' => $subject,
                'bcc' => $bcc,
                'body' => $emailBody,
                'to' => $emailMessage['username'],
                'emailKey' => $emailMessage['emailKey'],
                'organizationId' => $emailMessage['organizationId']
            );
        }
        $emailInstance = $this->emailService->sendEmailNotification($emailResponse['email_detail']);
        $emailSentStatus = $this->emailService->sendEmail($emailInstance);
        return $emailSentStatus;
    }
}