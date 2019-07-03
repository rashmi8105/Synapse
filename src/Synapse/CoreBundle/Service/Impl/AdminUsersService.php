<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\OrganizationRole;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\RoleRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUsersConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\CoreBundle\Util\Constants\UsersConstant;
use Synapse\MultiCampusBundle\EntityDto\UsersDto;
use Synapse\RestBundle\Entity\UserDTO;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("admin_users_service")
 */
class AdminUsersService
{
	const SERVICE_KEY = 'admin_users_service';

    // Scaffolding

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

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
    private $orgService;

    /**
     * @var PasswordService
     */
    private $passwordService;


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
     * @var OrganizationRepository
     */
    private $organizationRepo;

    /**
     * @var OrganizationRoleRepository
     */
    private $orgRoleRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;


    /**
     * AdminUsersService constructor.
     *
     *  @DI\InjectParams({
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
        $this->container = $container;
        $this->logger = $logger;
        $this->repositoryResolver = $repositoryResolver;

        // Services
        $this->ebiConfigService = $this->container->get("ebi_config_service");
        $this->emailService = $this->container->get("email_service");
        $this->orgService = $this->container->get("org_service");
        $this->passwordService = $this->container->get("password_service");

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(UsersConstant::EBICONFIG_REPO);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(UsersConstant::EMAIL_TEMP_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(UsersConstant::PERSON_REPO);

    }


    /**
     *
     * @param UserDTO $userDTO
     */
    public function createAdminUser(UserDTO $userDTO, $loggedInPersonId)
    {
        $this->logger->debug(" Creating User");

        $this->personRepository = $this->repositoryResolver->getRepository(UsersConstant::PERSON_REPO);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_ROLE_REPO);
        $this->roleRepository = $this->repositoryResolver->getRepository(UsersConstant::ROLE_REPO);
        $this->organizationRepo = $this->repositoryResolver->getRepository(UsersConstant::ORG_REPO);
        $this->checkExistingEmail($userDTO->getEmail(), null, true);
        if ($userDTO->getUserType() == 'Mapworks Admin') {
            $role = $this->validateRole($userDTO->getUserType());
            $externalId = "Mapworks" . rand(2, 10);
        }

        if ($userDTO->getUserType() == 'Skyfactor Admin') {
            $role = $this->validateRole($userDTO->getUserType());
            $externalId = "Skyfactor" . rand(2, 10);
        }
        $orgId = $this->organizationRepo->findOneById(-1);
        $this->isObjectExist($orgId, AcademicUsersConstant::ORG_ID_ERROR, AcademicUsersConstant::ORG_ID_ERROR_KEY);

        $this->validateFields($userDTO);

        $person = new Person();
        $person->setFirstname($userDTO->getFirstname());
        $person->setLastname($userDTO->getLastname());
        $person->setUsername($userDTO->getEmail());
        $person->setOrganization($orgId);
        $person->setExternalId($externalId);

        $contact = new Contactinfo();
        $contact->setHomephone($userDTO->getPhone());
        $contact->setPrimaryemail($userDTO->getEmail());
        $person->addContact($contact);

        $this->personRepository->createPerson($person);

        $orgRole = new OrganizationRole();
        $orgRole->setPerson($person);
        $orgRole->setRole($role);
        $orgRole->setOrganization($orgId);
        $this->orgRoleRepository->createCoordinator($orgRole);
        $this->personRepository->flush();
        $this->logger->info("Create User is completed");
        $userDTO->setId($person->getId());
        if ($userDTO->getSendinvite() == true) {
            $this->getSendInvitationEmail($person->getId());
        }
        return $userDTO;
    }

    private function validateRole($roleName)
    {
        $this->roleLangRepository = $this->repositoryResolver->getRepository(UsersConstant::ROLE_LANG_REPO);
        $this->roleRepository = $this->repositoryResolver->getRepository(UsersConstant::ROLE_REPO);

        $roleLang = $this->roleLangRepository->findOneByRoleName($roleName);
        if (!isset($roleLang)) {
            throw new ValidationException([
                UsersConstant::ERROR_ROLE_NOT_FOUND
            ], UsersConstant::ERROR_ROLE_NOT_FOUND, 'role_not_found');
        }
        $role = $this->roleRepository->findOneById($roleLang->getRole()->getId());
        if (!isset($role)) {
            throw new ValidationException([
                UsersConstant::ERROR_ROLE_NOT_FOUND
            ], UsersConstant::ERROR_ROLE_NOT_FOUND, 'role_not_found');
        }
        return $role;
    }

    private function validateFields($userDTO)
    {

        if (empty($userDTO->getFirstname()) || $userDTO->getFirstname() == '') {
            return $this->isObjectExist(null, AcademicUsersConstant::FIRST_NAME_ERROR, AcademicUsersConstant::FIRST_NAME_ERROR_KEY);

        } elseif (strlen($userDTO->getFirstname()) > 100) {
            return $this->isObjectExist(null, AcademicUsersConstant::FIRST_NAME_LIMIT, AcademicUsersConstant::FIRST_NAME_LIMIT_KEY);

        } elseif (empty($userDTO->getLastname()) || $userDTO->getLastname() == '') {
            return $this->isObjectExist(null, AcademicUsersConstant::LAST_NAME_ERROR, AcademicUsersConstant::LAST_NAME_ERROR_KEY);

        } elseif (strlen($userDTO->getLastname()) > 100) {
            return $this->isObjectExist(null, AcademicUsersConstant::LAST_NAME_LIMIT, AcademicUsersConstant::LAST_NAME_LIMIT_KEY);

        } elseif (empty($userDTO->getEmail()) || $userDTO->getEmail() == '') {
            return $this->isObjectExist(null, AcademicUsersConstant::EMAIL_ERROR, AcademicUsersConstant::EMAIL_ERROR_KEY);

        } elseif (!filter_var($userDTO->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return $this->isObjectExist(null, AcademicUsersConstant::INVALID_EMAIL, AcademicUsersConstant::INVALID_EMAIL_KEY);

        }
    }

    public function editAdminUser($userDTO, $userId)
    {

        $this->personRepository = $this->repositoryResolver->getRepository(UsersConstant::PERSON_REPO);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_ROLE_REPO);

        $person = $this->personRepository->find($userId);
        $this->isObjectExist($person, AcademicUsersConstant::INVALID_EMAIL, AcademicUsersConstant::INVALID_EMAIL_KEY);;
        $this->checkExistingEmail($userDTO->getEmail(), $userId, false);

        $role = $this->validateRole($userDTO->getUserType());
        $orgRole = $this->orgRoleRepository->findOneBy(array(
            UsersConstant::FIELD_PERSON => $person
        ));
        if (!$orgRole) {
            $orgrole = new OrganizationRole();
            $orgrole->setPerson($person);
            $orgrole->setRole($role);
            $this->orgRoleRepository->createCoordinator($orgrole);
        } else {
            $orgRole->setRole($role);
        }
        $this->validateFields($userDTO);
        $person->setFirstname($userDTO->getFirstname());
        $person->setLastname($userDTO->getLastname());
        $person->setUsername($userDTO->getEmail());
        $contact = $person->getContacts()->first();
        if ($contact) {
            $contact->setHomephone($userDTO->getPhone());
        }
        $contact->setPrimaryemail($userDTO->getEmail());
        $this->personRepository->flush();
        $this->logger->info("Admin User Updated");
        $userDTO->setId($person->getId());
        return $userDTO;
    }

    public function getAllAdminUsers($pageNo, $offset)
    {
        $this->logger->debug("List all admin users");
        $this->personRepository = $this->repositoryResolver->getRepository(UsersConstant::PERSON_REPO);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_ROLE_REPO);
        $usersList = array();
        $totalUsers = 0;
        $allUsers = $this->orgRoleRepository->getAllAdminUsers();
        $totalUsers = count($allUsers);
        $usersList['count'] = $totalUsers;
        foreach ($allUsers as $user) {
            $usersDto = new UsersDto();
            $usersDto->setUserId($user['admin_id']);
            $usersDto->setFirstName($user['firstname']);
            $usersDto->setLastName($user['lastname']);
            $usersDto->setEmail($this->checkNullResponse($user['primary_email']));
            $usersDto->setRole($user['role_name']);
            $usersDto->setPhone($user['home_phone']);
            $usersDto->setLastLogin($user['last_login']);
            $usersList['users'][] = $usersDto;
        }
        return $usersList;
    }

    /**
     * return Empty Value for null value
     */
    private function checkNullResponse($input)
    {
        return $input ? $input : '';
    }

    public function deleteAdminUser($userId, $loggedInPersonId)
    {
        $this->logger->debug("Deleting a User" . $userId);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(UsersConstant::ORG_ROLE_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(UsersConstant::PERSON_REPO);
        if ($userId == $loggedInPersonId) {
            $this->isObjectExist(null, AcademicUsersConstant::USER_CAN_NOT_DELETE, AcademicUsersConstant::USER_CAN_NOT_DELETE_KEY);
        }
        $person = $this->personRepository->find($userId);
        $this->isObjectExist($person, AcademicUsersConstant::PERSON_NOT_FOUND, AcademicUsersConstant::PERSON_NOT_FOUND_KEY);
        $orgRolePerson = $this->orgRoleRepository->findOneByPerson($userId);
        $this->isObjectExist($orgRolePerson, AcademicUsersConstant::PERSON_NOT_FOUND, AcademicUsersConstant::PERSON_NOT_FOUND_KEY);
        $this->orgRoleRepository->remove($orgRolePerson);
        $this->personRepository->remove($person);
        $this->personRepository->flush();
        $this->logger->info("Admin User Deleted");
        return;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (!($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    public function getSendInvitationEmail($userId)
    {
        $this->logger->debug("Send Invitation to Users");
        $this->personRepository = $this->repositoryResolver->getRepository(UsersConstant::PERSON_REPO);
        $getUserDetails = $this->personRepository->getUsersByUserIds($userId);
        if (empty($getUserDetails)) {
            throw new ValidationException([
                UsersConstant::ERROR_PERSON_NOT_FOUND
            ], UsersConstant::ERROR_PERSON_NOT_FOUND, UsersConstant::ERROR_PERSON_NOT_FOUND_KEY);
        } else {
            return $this->generateEmailInvitationMail($userId, $getUserDetails[0]['user_email']);
        }
        $this->logger->info("Send Invitation to Users");
    }

    /**
     * Generates and send Email Notification to User.
     *
     * @param int $userId
     * @param string $email
     * @return array
     */
    private function generateEmailInvitationMail($userId, $email)
    {
        $person = $this->personRepository->find($userId);
        $expiryHoursConfigEntry = $this->ebiConfigRepository->findOneBy(['key'=>'Coordinator_First_Password_Expiry_Hrs']);
        $token = $this->passwordService->generateTemporaryActivationToken($email);
        $person->setActivationToken($token);
        $expire = 0;
        if ($expiryHoursConfigEntry) {
            // Setting Expire date
            $expire = (int)$expiryHoursConfigEntry->getValue();

            if ($expire) {

                $dateNow = new \DateTime('now');
                $dateNow->setTimezone(new \DateTimeZone('UTC'));
                $dateNow->add(new \DateInterval('P0DT' . $expire . 'H0M0S'));

                $person->setTokenExpiryDate($dateNow);
            }
        }

        // Get Email Template
        $organizationId = $person->getOrganization()->getId();
        $emailKey = 'Welcome_Email_Skyfactor_Admin_User';
        $organizationLang = $this->orgService->getOrganizationDetailsLang($organizationId);

        // pass language param
        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $organizationLang->getLang()->getId());
        if ($emailTemplate) {
            $emailBody = $emailTemplate->getBody();

            $tokenValues = [];
            $tokenValues[UsersConstant::FIELD_FIRSTNAME] = $person->getFirstname();
            $tokenValues[AppointmentsConstant::EMAIL_SKY_LOGO] = "";

            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            if ($systemUrl) {
                $tokenValues[AppointmentsConstant::EMAIL_SKY_LOGO] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }

            $urlPrefix = $this->ebiConfigRepository->findOneBy(['key'=>'Skyfactor_Admin_Activation_URL_Prefix']);
            $supportEmail = $this->ebiConfigRepository->findOneBy(['key'=>'Coordinator_Support_Helpdesk_Email_Address']);

            $tokenValues[UsersConstant::SUPPORT_HELPDESK] = $supportEmail->getValue();
            $tokenValues['activation_token'] = $urlPrefix->getValue() . $token;
            $tokenValues['Coordinator_ResetPwd_URL_Prefix'] = $urlPrefix->getValue() . $token;
            $tokenValues['Reset_Password_Expiry_Hrs'] = $expire;

            $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);

            $sendLinkDate = new \DateTime('now');
            $sendLinkDate->setTimezone(new \DateTimeZone('UTC'));
            $person->setWelcomeEmailSentDate($sendLinkDate);
        }

        $responseArray['email_sent_status'] = true;
        $responseArray['welcome_email_sentDate'] = $sendLinkDate;

        $emailConstant = array(
            'from' => $emailTemplate->getEmailTemplate()->getFromEmailAddress(),
            UsersConstant::FIELD_SUBJECT => $emailTemplate->getSubject(),
            'bcc' => $emailTemplate->getEmailTemplate()->getBccRecipientList(),
            'body' => $emailBody,
            'to' => $email,
            UsersConstant::FIELD_EMAILKEY => $emailKey,
            UsersConstant::FIELD_ORGID => $organizationId
        );

        $this->personRepository->flush();

        $emailInst = $this->emailService->sendEmailNotification($emailConstant);
        $send = $this->emailService->sendEmail($emailInst);

        unset($emailConstant['body']);
        unset($token);

        $responseArray[UsersConstant::EMAIL_ADDRESS] = $emailConstant;
        if ($send) {
            $responseArray[UsersConstant::FIELD_MESSAGE] = "Mail sent successfully to $email";
        } else {
            $responseArray[UsersConstant::FIELD_MESSAGE] = "Mail email sending failed to $email";
        }

        return $responseArray;
    }

    private function checkExistingEmail($email, $userId, $addFlag)
    {

        $this->personRepository = $this->repositoryResolver->getRepository(UsersConstant::PERSON_REPO);
        $personEmail = $this->personRepository->checkExistingEmail($email, $userId, $addFlag);
        if (!empty($personEmail)) {
            $this->isObjectExist(null, AcademicUsersConstant::EMAIL_EXISTS, AcademicUsersConstant::EMAIL_EXISTS_KEY);
        }
    }
}