<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Exception\RestException;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("email_password_service")
 */
class EmailPasswordService extends AbstractService
{

    const SERVICE_KEY = "email_password_service";

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    // Services
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
     * @var PasswordService
     */
    private $passwordService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var RoleService
     */
    private $roleService;

    // Repositories
    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * EmailPasswordService constructor.
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

        // Services
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->passwordService = $this->container->get(PasswordService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->roleService = $this->container->get(RoleService::SERVICE_KEY);

        // Repositories
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Generate invitation email for users.
     *
     * TODO: This method probably shouldn't be called in LDAP/SAML instances. We probably need a new sendEmailWithInvitation for SSO instances. And a new email template.
     * TODO: Add a precondition upstream that ensures this isn't called in the case of LDAP/SAML
     *
     * @param int $facultyId
     * @param string $facultyEmailAddress
     * @return array
     * @throws RestException
     */
    public function sendEmailWithInvitationLink($facultyId, $facultyEmailAddress)
    {
        $personObject = $this->personRepository->find($facultyId);

        $organizationForFacultyMember = $personObject->getOrganization();
        $organizationId = $organizationForFacultyMember->getId();

        $personRoles = $this->roleService->getRolesForUser($facultyId);

        $passwordExpirationEbiConfigObject = null;
        $urlPrefixEbiConfigObject = null;
        $supportEmailEbiConfigObject = null;

        if ($personRoles['coordinator']) {
            $passwordExpirationEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_First_Password_Expiry_Hrs']);
            $urlPrefixEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_Activation_URL_Prefix']);
            $supportEmailEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_Support_Helpdesk_Email_Address']);
        } elseif ($personRoles['faculty']) {
            $passwordExpirationEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Staff_First_Password_Expiry_Hrs']);
            $urlPrefixEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Staff_Activation_URL_Prefix']);
            $supportEmailEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Staff_Support_Helpdesk_Email_Address']);
        }

        if (!$passwordExpirationEbiConfigObject || !$urlPrefixEbiConfigObject || !$supportEmailEbiConfigObject) {
            $userMessage = 'There was an error retrieving appropriate configuration for email. Please contact support if this error continues.';
            $developerMessage = "There was an error retrieving the EBI config entries. Please check ebi_config for the keys needed for creating faculty/coordinator emails";
            throw new RestException([$userMessage], $userMessage, 'no_email_template', 500, $developerMessage);
        }

        $activationToken = $this->passwordService->generateTemporaryActivationToken($facultyEmailAddress);
        $personObject->setActivationToken($activationToken);

        $hoursUntilTokenExpiration = 0;
        if ($passwordExpirationEbiConfigObject) {
            $hoursUntilTokenExpiration = (int)$passwordExpirationEbiConfigObject->getValue();

            if ($hoursUntilTokenExpiration) {
                $currentDate = new \DateTime('now');
                $tokenExpirationDate = $currentDate->add(new \DateInterval('PT' . $hoursUntilTokenExpiration . 'H'));
                $personObject->setTokenExpiryDate($tokenExpirationDate);
            }
        }

        $this->personRepository->flush();

        $emailTemplateKey = 'Welcome_To_Mapworks';

        $emailTemplateObject = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailTemplateKey]);
        $emailTemplateLangObject = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplateObject]);

        if (!$emailTemplateLangObject) {
            $userMessage = 'There was an error retrieving the email template. Please contact support if this error continues.';
            $developerMessage = "There was an error retrieving the email template. Please check ebi_config to see if the key $emailTemplateKey exists";
            throw new RestException([$userMessage], $userMessage, 'no_email_template', 500, $developerMessage);
        }


        // Token values get used for parsing a really long HTML string and plugging values into special tokens in that HTML
        $tokenValues = [];
        $tokenValues['$$firstname$$'] = $personObject->getFirstname();

        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        if ($systemUrl) {
            $tokenValues['$$Skyfactor_Mapworks_logo$$'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        } else {
            $tokenValues['$$Skyfactor_Mapworks_logo$$'] = "";
        }

        $tokenValues['$$Support_Helpdesk_Email_Address$$'] = $supportEmailEbiConfigObject->getValue();
        $tokenValues['$$activation_token$$'] = $urlPrefixEbiConfigObject->getValue() . $activationToken;
        $tokenValues['$$Reset_Password_Expiry_Hrs$$'] = $hoursUntilTokenExpiration;

        $emailBody = $emailTemplateLangObject->getBody();
        // Convert the email string template and token values into a long HTML string
        $emailBody = strtr($emailBody, $tokenValues);
        $welcomeEmailSentDate = new \DateTime();
        $personObject->setWelcomeEmailSentDate($welcomeEmailSentDate);

        $responseArray['email_sent_status'] = true;
        $responseArray['welcome_email_sentDate'] = $welcomeEmailSentDate;

        // Formatted for API response JSON, to convert into a DTO simply for sending an email
        $emailContent = [
            'from' => $emailTemplateObject->getFromEmailAddress(),
            'subject' => $emailTemplateLangObject->getSubject(),
            'bcc' => $emailTemplateObject->getBccRecipientList(),
            'body' => $emailBody,
            'to' => $facultyEmailAddress,
            'emailKey' => $emailTemplateKey,
            'organizationId' => $organizationId
        ];

        $this->personRepository->flush();

        // TODO: Remove awful translation code that creates a DTO object solely for the purpose of being used for sending an email
        $emailNotificationDto = $this->emailService->sendEmailNotification($emailContent);

        // Send the email based on the values in the DTO
        try {
            $this->emailService->sendEmail($emailNotificationDto);

            // Get rid of the email body because it's a big string, and the activation token for security redundancy
            unset($emailContent['body']);
            unset($activationToken);

            // Because "obviously" the properties used for sending the email have to be different than those returned to the API response
            $responseArray['email_detail'] = $emailContent;
            $responseArray['message'] = "Mail sent successfully to $facultyEmailAddress";
            $responseArray['email_detail'] = $emailContent;
            return $responseArray;
        } catch (ValidationException $ex) {
            throw $ex;
        }
    }


    /**
     * Generate invitation email for users.
     *
     * TODO: Add a precondition upstream that ensures this isn't called in the case of LDAP/SAML
     *
     * @param string $facultyEmailAddress
     * @return array
     * @throws RestException
     */
    public function sendEmailWithResetPasswordLink($facultyEmailAddress)
    {
        $facultyPersonObject = $this->personRepository->findOneBy(['username' => $facultyEmailAddress]);

        if (!$facultyPersonObject) {
            $userMessage = 'There was an error retrieving your user information. Please contact support if this error continues.';
            $developerMessage = "There was an error retrieving the person with username $facultyEmailAddress. Please check person table for this username";
            throw new RestException([$userMessage], $userMessage, 'person_id_not_found', 500, $developerMessage);
        }

        $organizationForFacultyMember = $facultyPersonObject->getOrganization();
        $organizationId = $organizationForFacultyMember->getId();
        $facultyId = $facultyPersonObject->getId();

        $personRoles = $this->roleService->getRolesForUser($facultyId);

        $passwordExpirationEbiConfigEntry = null;
        $urlPrefixEbiConfigObject = null;
        $supportEmailEbiConfigObject = null;
        $emailTemplateKey = null;

        if ($personRoles['coordinator']) {
            $passwordExpirationEbiConfigEntry = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_Reset_Password_Expiry_Hrs']);
            $urlPrefixEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_ResetPwd_URL_Prefix']);
            $supportEmailEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_Support_Helpdesk_Email_Address']);
            $emailTemplateKey = 'Forgot_Password_Coordinator';
        } elseif ($personRoles['faculty']) {
            $passwordExpirationEbiConfigEntry = $this->ebiConfigRepository->findOneBy(['key' => 'Staff_First_Password_Expiry_Hrs']);
            $urlPrefixEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Staff_ResetPwd_URL_Prefix']);
            $supportEmailEbiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Staff_Support_Helpdesk_Email_Address']);
            $emailTemplateKey = 'Forgot_Password_Staff';
        } else {
            $userMessage = "You are not a faculty or coordinator at this institution.";
            $developerMessage = "Person ID: $facultyId is not a faculty or coordinator. ";
            throw new RestException([$userMessage], $userMessage, 'invalid_role', 500, $developerMessage);
        }

        if (!$passwordExpirationEbiConfigEntry || !$urlPrefixEbiConfigObject || !$supportEmailEbiConfigObject) {
            $userMessage = 'There was an error retrieving appropriate configuration for email. Please contact support if this error continues.';
            $developerMessage = "There was an error retrieving the EBI config entries. Please check ebi_config for the keys needed for creating faculty/coordinator emails";
            throw new RestException([$userMessage], $userMessage, 'no_email_template', 500, $developerMessage);
        }

        $hoursUntilTokenExpiration = 0;

        $resetToken = $this->passwordService->generateTemporaryActivationToken($facultyEmailAddress);
        $facultyPersonObject->setActivationToken($resetToken);

        if ($passwordExpirationEbiConfigEntry) {
            $hoursUntilTokenExpiration = (int)$passwordExpirationEbiConfigEntry->getValue();

            if ($hoursUntilTokenExpiration) {
                $currentDate = new \DateTime('now');
                $tokenExpirationDate = $currentDate->add(new \DateInterval('PT' . $hoursUntilTokenExpiration . 'H'));
                $facultyPersonObject->setTokenExpiryDate($tokenExpirationDate);
            }
        }

        $this->personRepository->flush();

        $emailTemplateObject = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailTemplateKey]);
        $emailTemplateLangObject = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplateObject]);

        if (!$emailTemplateLangObject) {
            $userMessage = 'There was an error retrieving the email template. Please contact support if this error continues.';
            $developerMessage = "There was an error retrieving the email template. Please check ebi_config to see if the key $emailTemplateKey exists";
            throw new RestException([$userMessage], $userMessage, 'no_email_template', 500, $developerMessage);
        }


        // Token values get used for parsing a really long HTML string and plugging values into special tokens in that HTML
        $tokenValues = [];
        $tokenValues['$$firstname$$'] = $facultyPersonObject->getFirstname();

        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        if ($systemUrl) {
            $tokenValues['$$Skyfactor_Mapworks_logo$$'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        } else {
            $tokenValues['$$Skyfactor_Mapworks_logo$$'] = "";
        }

        $tokenValues['$$Support_Helpdesk_Email_Address$$'] = $supportEmailEbiConfigObject->getValue();
        $tokenValues['$$activation_token$$'] = $urlPrefixEbiConfigObject->getValue() . $resetToken;
        $tokenValues['$$Reset_Password_Expiry_Hrs$$'] = $hoursUntilTokenExpiration;

        $emailBody = $emailTemplateLangObject->getBody();
        // Convert the email string template and token values into a long HTML string
        $emailBody = strtr($emailBody, $tokenValues);
        $welcomeEmailSentDate = new \DateTime();
        $facultyPersonObject->setWelcomeEmailSentDate($welcomeEmailSentDate);

        $responseArray['email_sent_status'] = true;
        $responseArray['welcome_email_sentDate'] = $welcomeEmailSentDate;

        // Formatted for API response JSON, to convert into a DTO simply for sending an email
        $emailContent = [
            'from' => $emailTemplateObject->getFromEmailAddress(),
            'subject' => $emailTemplateLangObject->getSubject(),
            'bcc' => $emailTemplateObject->getBccRecipientList(),
            'body' => $emailBody,
            'to' => $facultyEmailAddress,
            'emailKey' => $emailTemplateKey,
            'organizationId' => $organizationId
        ];

        $this->personRepository->flush();

        // TODO: Remove awful translation code that creates a DTO object solely for the purpose of being used for sending an email
        $emailNotificationDto = $this->emailService->sendEmailNotification($emailContent);

        // Send the email based on the values in the DTO
        try {
            $this->emailService->sendEmail($emailNotificationDto);

            // Get rid of the email body because it's a big string, and the activation token for security redundancy
            unset($emailContent['body']);
            unset($activationToken);

            // Because "obviously" the properties used for sending the email have to be different than those returned to the API response
            $responseArray['email_detail'] = $emailContent;
            $responseArray['message'] = "Mail sent successfully to $facultyEmailAddress";
            $responseArray['email_detail'] = $emailContent;
            return $responseArray;
        } catch (ValidationException $ex) {
            throw $ex;
        }
    }

}