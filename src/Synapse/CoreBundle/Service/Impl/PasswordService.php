<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\RestBundle\Entity\CreatePasswordDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("password_service")
 */
class PasswordService extends AbstractService
{
    const SERVICE_KEY = 'password_service';

    const ERROR_ACTIVATION_TOKEN_EXPIRED = "Activation Token Expired.";

    const EBI_CONFIG_KEY_SUPPORT_HELPDESK = "Support_Helpdesk_Email_Address";

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

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
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    // Repositories
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
    private $orgRoleRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     *
     * @param $repositoryResolver
     * @param logger
     * @param encoderFactory
     * @param container
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        $this->encoderFactory = $this->container->get('security.encoder_factory');

        // Services
        $this->ebiConfigService = $this->container->get('ebi_config_service');
        $this->emailService = $this->container->get('email_service');
        $this->loggerHelperService = $this->container->get('loggerhelper_service');
        $this->orgService = $this->container->get('org_service');

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    public function getOrganizationLang($orgId)
    {
        $this->logger->debug("Get Organization Lang by Organization Id " . $orgId);
        return $this->orgService->getOrganizationDetailsLang($orgId);
    }

    public function validateActivationLink($token)
    {
        $this->logger->debug("Validate Activiation  Link  Token" . $token);
        $person = $this->personRepository->findOneByActivationToken($token);
        if (!$person) {
            $this->logger->error("Password Service - validateActivationLink - " . self::ERROR_ACTIVATION_TOKEN_EXPIRED);
            throw new ValidationException([
                self::ERROR_ACTIVATION_TOKEN_EXPIRED
            ], self::ERROR_ACTIVATION_TOKEN_EXPIRED, 'token_expire');
        }
        $dateNow = new \DateTime('now');
        $dateNow->setTimezone(new \DateTimeZone('UTC'));

        $expireDate = $person->getTokenExpiryDate();
        if ($expireDate && $dateNow > $expireDate) {
            $this->logger->error(" Password Service - validateActivationLink - Activation Token Expired");
            throw new ValidationException([
                self::ERROR_ACTIVATION_TOKEN_EXPIRED
            ], self::ERROR_ACTIVATION_TOKEN_EXPIRED, 'token_time_expire');
        }
        $this->logger->info("Validate Activation Link Token");
        return $person;
    }

    private function getEmailKeyForcreatePassword($isFirstTime, $userRole)
    {
        $emailKey = null;
        if ($isFirstTime) {
            $emailKey = 'Welcome_Email_' . $userRole;
        } else {
            $emailKey = 'Sucessful_Password_Reset_' . $userRole;
        }
        return $emailKey;
    }

    /**
     * This method creates password for the user
     *
     * @param CreatePasswordDto $createPasswordDto
     * @return array
     */
    public function createPassword(CreatePasswordDto $createPasswordDto)
    {
        $logContent = $this->loggerHelperService->getLog($createPasswordDto);
        $this->logger->debug(" Creating Password -  " . $logContent);
        $isAccepted = (bool)$createPasswordDto->getIsConfidentialityAccepted();
        if (!$isAccepted) {
            $this->logger->error(" Password Service - Create Password - Confidentiality Statement not accepted");
            throw new ValidationException([
                'Confidentiality Statement not accepted.'
            ], 'Confidentiality Statement not accepted', 'not_accepted');
        } else {
            $token = $createPasswordDto->getToken();
            $password = $createPasswordDto->getPassword();
            $person = $this->personRepository->findOneByActivationToken($token);
            // To Identify the user creating password or resetting password
            $isFirstTime = true;
            if (!$person) {
                $this->logger->error("Password Service - Create Password - " . self::ERROR_ACTIVATION_TOKEN_EXPIRED);
                throw new ValidationException([
                    self::ERROR_ACTIVATION_TOKEN_EXPIRED
                ], self::ERROR_ACTIVATION_TOKEN_EXPIRED, 'token_expire');
            }
            if ($person->getPassword()) {
                $isFirstTime = false;
            }
            $personDetails = $this->personRepository->getPersonDetails($person);
            $email = null;
            if (count($personDetails) > 0) {
                $email = $personDetails[0]['contacts'][0]['primaryEmail'];
            }

            $encoder = $this->encoderFactory->getEncoder($person);
            $encryptPassword = $encoder->encodePassword($password, $person->getSalt());

            $person->setPassword($encryptPassword);
            $person->setActivationToken(null);
            $person->setTokenExpiryDate(null);
            $acceptedDate = new \DateTime('now');
            $acceptedDate->setTimezone(new \DateTimeZone('UTC'));
            $person->setConfidentialityStmtAcceptDate($acceptedDate);
            $organizationLang = $this->getOrganizationLang($person->getOrganization()
                ->getId());
            $this->personRepository->flush();

            $organizationId = $person->getOrganization()->getId();
            $userRole = null;

            $roleArray = $this->getUserRole($organizationId, $person);
            $userRole = trim($roleArray[0]);
            $userRole = ucfirst(strtolower($userRole));
            $emailKey = $this->getEmailKeyForcreatePassword($isFirstTime, $userRole);
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $organizationLang->getLang()
                ->getId());
            $responseArray = array();
            $tokenValues = array();
            if ($systemUrl) {
                $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            if ($emailTemplate) {
                $emailBody = $emailTemplate->getBody();
                $supportEmail = $this->ebiConfigRepository->findOneByKey($userRole . '_' . self::EBI_CONFIG_KEY_SUPPORT_HELPDESK);
                if ($supportEmail) {
                    $tokenValues[self::EBI_CONFIG_KEY_SUPPORT_HELPDESK] = $supportEmail->getValue();
                }

                $tokenValues['firstname'] = $person->getFirstname();
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
            }
            // Sending email notification
            $emailInst = $this->emailService->sendEmailNotification($responseArray['email_detail']);
            $this->emailService->sendEmail($emailInst);

            $responseArray['signin_status'] = true;
            $responseArray['person_id'] = $person->getId();
            $responseArray['person_first_name'] = $person->getFirstname();
            $responseArray['person_last_name'] = $person->getLastname();
            $responseArray['person_type'] = $this->getPersonType($userRole, $roleArray);
            $this->logger->info("Password Created");
            return $responseArray;
        }
    }

    /**
     * Get user role based on organization and person
     *
     * @param int $organizationId
     * @param int $personId
     * @return array
     * @deprecated
     */
    private function getUserRole($organizationId, $personId)
    {
        $roleArray = array();
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($organizationId, $personId);
        if ($isCoordinator) {
            $roleArray[] = "Coordinator";
        } else {
            $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
                'person' => $personId,
                'organization' => $organizationId
            ));
            if ($orgPersonFaculty) {
                $roleArray[] = "Staff";
            }

            $orgPersonStudent = $this->orgPersonStudentRepository->findBy(array(
                'person' => $personId,
                'organization' => $organizationId
            ));
            if ($orgPersonStudent) {
                $roleArray[] = "Student";
            }
        }
        return $roleArray;
    }

    private function getPersonType($userRole, $roleArray)
    {
        $type = "";
        if ($roleArray) {
            $type = implode(",", $roleArray);
        } else {
            $type = $userRole;
        }
        return $type;
    }

    /**
     * Helper method for generating a randomized activation token. Only to be used when creating a link for a user to set (or reset) their password.
     * TODO: Uses MD5 Hashing. NOT SAFE.
     *
     * @param string|null $salt
     * @return string
     */
    public function generateTemporaryActivationToken($salt = null)
    {
        if (is_null($salt)) {
            $token = md5(time());
        } else {
            $token = md5($salt . time() . $salt);
        }
        return $token;
    }
}