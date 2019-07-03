<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\MapworksAction;
use Synapse\CoreBundle\Entity\NotificationLog;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\EmailDto;
use Synapse\RestBundle\Entity\EmailNotificationDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("email_service")
 */
class EmailService extends AbstractService
{
    const SERVICE_KEY = 'email_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

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
     * @var MapworksActionService
     */
    private $mapworksActionService;

    // Repositories

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * EmailService constructor.
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
        $this->container = $container;

        // Scaffolding
        $this->swiftMailer = $this->container->get(SynapseConstant::MAILER_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);

        // Repositories
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository =$this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    /**
     * Construct and send an email. Will return 1 on success and 0 on failure to send the email.
     *
     * @param EmailNotificationDto $emailNotificationDto
     * @param bool $includeNameInFromFlag - TRUE will show the first and last names of the faculty when the email is received (ie. FROM "John Doe" noreply@mapworks.com). FALSE will exclude this value (ie. FROM noreply@mapworks.com)
     * @param Person $personFaculty - the person sending the email; used for the 'From' FirstName LastName
     * @param bool $sendToNonParticipant - False will not send mails to the non-participant students
     * @return int
     * @throws ValidationException
     */
    public function sendEmail(EmailNotificationDto $emailNotificationDto, $includeNameInFromFlag = false, $personFaculty = null, $sendToNonParticipant = false)
    {
        $organizationId = $emailNotificationDto->getOrganizationId();
        $organization = $this->organizationRepository->find($organizationId);

        $to = trim($emailNotificationDto->getRecipientList());
        $personIds = $this->personRepository->getPersonIdsUsingUsernames($to);

        if ((!$sendToNonParticipant)) {
            $facultyArray = [];
            $studentArray = [];

            // identify if its a student or faculty
            foreach ($personIds as $personId) {
                $isFaculty = $this->orgPersonFacultyRepository->findOneBy(['person' => $personId]);
                if ($isFaculty) {
                    $facultyArray[] = $personId;
                } else {
                    $studentArray[] = $personId;
                }
            }
            $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
            $participantStudentIds = [];
            if (!empty($studentArray)) {
                $participantStudentIds = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($studentArray, $organizationId, $currentAcademicYear['org_academic_year_id']);
            }
            $personIds = array_merge($facultyArray, $participantStudentIds);
        }

        $personDetails = $this->personRepository->getPersonNames($personIds);
        $personUserNames = array_column($personDetails, 'username');

        if (!empty($personUserNames)) {
            $cc = $emailNotificationDto->getCcList();
            $bcc = $emailNotificationDto->getBccList();
            $replyTo = $emailNotificationDto->getReplyTo();
            $subject = $emailNotificationDto->getSubject();
            $from = $emailNotificationDto->getFromAddress();
            $emailRecipients = $personUserNames;
            $body = $emailNotificationDto->getBody();

            $message = \Swift_Message::newInstance()->setContentType("text/html")
                ->setSubject($subject)
                ->setTo($emailRecipients)
                ->setBody($body);

            if ($includeNameInFromFlag && $personFaculty) {
                $facultyName = $personFaculty->getFirstname() . ' ' . $personFaculty->getLastname();
                $message->setFrom([$from => $facultyName]);
            } else {
                $message->setFrom($from);
            }

            if ($bcc) {
                $message->setBcc(explode(",", $bcc));
            }
            if ($cc) {
                $message->setCc(explode(",", $cc));
            }
            if ($replyTo) {
                $message->setReplyTo($replyTo);
            }

            $send = $this->swiftMailer->send($message);

            if ($send) {
                $notificationLogObject = new NotificationLog();
                $notificationLogObject->setRecipientList($emailNotificationDto->getRecipientList());
                $notificationLogObject->setEmailKey($emailNotificationDto->getEmailKey());
                $notificationLogObject->setCcList($cc);
                $notificationLogObject->setBccList($bcc);
                $notificationLogObject->setBody($emailNotificationDto->getBody());
                $notificationLogObject->setSubject($emailNotificationDto->getSubject());
                $notificationLogObject->setOrganization($organization);
                $notificationLogObject->setNoOfRetries($emailNotificationDto->getNoOfRetries());
                $notificationLogObject->setStatus('Y');
                $notificationLogObject->setServerResponse($emailNotificationDto->getServerResponse());

                $sentDateTime = new \DateTime('now');
                $sentDateTime->setTimezone(new \DateTimeZone('UTC'));
                $notificationLogObject->setSentDate($sentDateTime);

                $this->organizationRepository->persist($notificationLogObject);
                $this->organizationRepository->flush();
            } else {
                $this->logger->error("Email Service - sendEmail -  Mail send fail");
                throw new ValidationException([
                    'Mail send fail.'
                ], 'Mail send fail.', 'mail_send_fail');
            }
        } else {
            $this->logger->error("Email Service - sendEmail -  Provide recipient list to send mail");
            throw new ValidationException([
                'Provide recipient list to send mail.'
            ], 'Provide recipient list to send mail.', 'provide_recipient_list_to_send_mail');
        }
        $this->logger->info(">>>> Email Sent");
        return $send;
    }

    public function sendEmailNotification($params = array())
    {
        $this->logger->info (">>>>Sending Email Notification ");
        $notificationDto = new EmailNotificationDto();
        
        $notificationDto->setSubject($params['subject']);
        $notificationDto->setFromAddress($params['from']);
        $notificationDto->setBccList($params['bcc']);
        $notificationDto->setBody($params['body']);
        $notificationDto->setRecipientList($params['to']);
        $notificationDto->setEmailKey($params['emailKey']);
        $notificationDto->setOrganizationId($params['organizationId']);
        if (isset($params['replyTo'])) {
            $notificationDto->setReplyTo($params['replyTo']);
        }
        
        return $notificationDto;
    }


    /**
     * Given a person object and an email DTO, verifies the user and the email information provided in the DTO match
     *
     * @param Person $user
     * @param EmailDto $emailDto
     * @throws AccessDeniedException
     */
    public function verifyThatThePersonLoggedInIsThePersonSendingTheEmail($user, $emailDto)
    {
        // These checks are to make sure that the user
        // is not trying to change the email so it will come in
        // as someone else. Currently this breaks sending emails as a proxy user.
        $personStaffId = $emailDto->getPersonStaffId();

        // In the EmailDto, we have technical debt that personStaffName really always contains the user's email address
        $organizationId = $emailDto->getOrganizationId();

        // get the logged in user information
        $loggedInUserId = $user->getId();
        $loggedInUserOrganization = $user->getOrganization()->getId();
        if ($loggedInUserId != $personStaffId) {
            throw new AccessDeniedException('You do not have permission to create email as someone else');
        }
        if ($loggedInUserOrganization != $organizationId) {
            throw new AccessDeniedException('You do not have permission to send an email to a different organization');
        }
    }

    /**
     * Function to generate email message using email templates and values
     *
     * @param string $message
     * @param array $tokenValues
     * @return string
     */
    public function generateEmailMessage($message, $tokenValues)
    {
        preg_match_all('/\\$\$(.*?)\$\$/', $message, $tokenArrays);
        $tokenArray = $tokenArrays[0];
        $tokenKeys = $tokenArrays[1];
        for ($tokenCount = 0; $tokenCount < count($tokenArray); $tokenCount++) {
            if (isset($tokenValues[$tokenKeys[$tokenCount]])) {
                $replaceArray = [$tokenArray[$tokenCount] => $tokenValues[$tokenKeys[$tokenCount]]];
                $message = strtr($message, $replaceArray);
            }
        }
        return $message;
    }

    /**
     * Send Email Notification to Person on Upload Completion
     *
     * @param Person $person
     * @param int $organizationId
     * @param string $emailKey
     * @param int $errorCount
     * @param string|null $uploadFile
     * @throws SynapseValidationException
     */
    public function sendUploadCompletionEmail($person, $organizationId, $emailKey, $errorCount, $uploadFile = null)
    {
        $downloadFailedLogFile = "";

        if ($errorCount > 0) {
            $uploadEmailText = 'Click <a class="external-link" href="DOWNLOAD_URL" target="_blank" style="color: rgb(41, 114, 155);text-decoration: underline;">here </a>to download error file .';
            $downloadFailedLogFile = str_replace('DOWNLOAD_URL', $uploadFile, $uploadEmailText);

        }
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        $userId = $person->getId();
        $tokenValues = [];
        $tokenValues['user_first_name'] = $person->getFirstname();
        $tokenValues['download_failed_log_file'] = $downloadFailedLogFile;
        $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;

        $emailTemplate = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailKey]);
        if ($emailTemplate) {
            $emailTemplate = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplate]);
        } else {
            throw new SynapseValidationException("Email template for key $emailKey not found");
        }

        if ($userId && $userId != null) {
            if ($emailTemplate) {
                $emailBody = $emailTemplate->getBody();
                $email = $person->getUsername();

                $emailBody = $this->generateEmailMessage($emailBody, $tokenValues);

                $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
                $subject = $emailTemplate->getSubject();
                $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
                $responseArray = array(
                    'from' => $from,
                    'subject' => $subject,
                    'bcc' => $bcc,
                    'body' => $emailBody,
                    'to' => $email,
                    'emailKey' => $emailKey,
                    'organizationId' => $organizationId
                );
                $emailInstance = $this->sendEmailNotification($responseArray);
                $this->sendEmail($emailInstance);
            }

        }
    }

    /**
     * Translate tokens with the associated values.
     *
     * @param int $organizationId
     * @param string $recipientEmailAddress
     * @param array $tokenValues
     * @param MapworksAction $mapworksAction
     * @return array
     */
    public function buildEmailResponse($organizationId, $recipientEmailAddress, $tokenValues, $mapworksAction)
    {
        $emailResponse = [];
        $emailTemplate = $mapworksAction->getEmailTemplate();
        if ($emailTemplate) {
            $emailKey = $emailTemplate->getEmailKey();
            $emailTemplateLangObject = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplate]);
        } else {
            throw new SynapseValidationException("Email template not found");
        }
        if ($emailTemplateLangObject) {
            $emailBody = $emailTemplateLangObject->getBody();
            $completelyBuiltEmailBody = $this->mapworksActionService->buildCommunicationBodyFromVariables($emailBody, $tokenValues, $mapworksAction);
            $emailTemplate = $emailTemplateLangObject->getEmailTemplate();
            $fromEmailAddress = $emailTemplate->getFromEmailAddress();
            $bccRecipientList = $emailTemplate->getBccRecipientList();
            $emailSubject = $emailTemplateLangObject->getSubject();

            if (isset($tokenValues['$$coordinator_email_address$$'])) {
                $replyToEmailAddress = $tokenValues['$$coordinator_email_address$$'];
            } else {
                $replyToEmailAddress = $fromEmailAddress;
            }
            $emailResponse = array(
                'from' => $fromEmailAddress,
                'subject' => $emailSubject,
                'bcc' => $bccRecipientList,
                'body' => $completelyBuiltEmailBody,
                'to' => $recipientEmailAddress,
                'emailKey' => $emailKey,
                'organizationId' => $organizationId,
                'replyTo' => $replyToEmailAddress
            );
        }
        return $emailResponse;
    }
}