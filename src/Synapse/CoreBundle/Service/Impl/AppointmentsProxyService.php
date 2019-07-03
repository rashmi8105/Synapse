<?php
namespace Synapse\CoreBundle\Service\Impl;

use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CalendarBundle\Service\Impl\CalendarWrapperService;
use Synapse\CoreBundle\Entity\CalendarSharing;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\CalendarSharingRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\CalendarSharingDto;
use Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto;
use Synapse\RestBundle\Exception\ValidationException;


/**
 * @DI\Service("appointmentsProxy_service")
 */
class AppointmentsProxyService extends AbstractService
{

    const SERVICE_KEY = 'appointmentsProxy_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CALENDAR_SHARE_REPO = "SynapseCoreBundle:CalendarSharing";

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
     * @var Manager
     */
    protected $rbacManager;

    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var CalendarFactoryService;
     */
    private $calendarFactoryService;

    /**
     * @var CalendarIntegrationService
     */
    private $calendarIntegrationService;

    /**
     * @var CalendarWrapperService
     */
    private $calendarWrapperService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var MapworksActionService
     */
    private $mapworksActionService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var PersonService
     */
    private $personService;


    // Repositories

    /**
     * @var AppointmentsRepository
     */
    private $appointmentsRepository;

    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;

    /**
     * @var CalendarSharingRepository
     */
    private $calendarSharingRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var OfficeHoursRepository
     */
    private $officehoursRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * AppointmentsProxyService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     * })
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
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->calendarFactoryService = $this->container->get(CalendarFactoryService::SERVICE_KEY);
        $this->calendarIntegrationService = $this->container->get(CalendarIntegrationService::SERVICE_KEY);
        $this->calendarWrapperService = $this->container->get(CalendarWrapperService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);

        // Repositories
        $this->appointmentsRepository = $this->repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->appointmentRecipientAndStatusRepository = $this->repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->calendarSharingRepository = $this->repositoryResolver->getRepository(CalendarSharingRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->officehoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    public function createDelegateUser(CalendarSharingDto $calendarSharingDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($calendarSharingDto);
        $this->logger->debug("Creating Delegate User " . $logContent);
        $this->rbacManager->checkAccessToOrganization($calendarSharingDto->getOrganizationId());

        $delegatesArray = $calendarSharingDto->getDelegatedUsers();
        if (count($delegatesArray > 0)) {
            $manageDelegates = $this->addRemoveDelegate($calendarSharingDto, $delegatesArray);
        }
        $this->logger->info(">>>> Creating Delegate User");
        return $calendarSharingDto;
    }

    /**
     * Creating/Editing/Deleting delegate users from array
     *
     * @param CalendarSharingDto $calendarSharingDto
     * @param array $delegatesUser
     * @return null
     * @throws AccessDeniedException
     */
    private function addRemoveDelegate($calendarSharingDto, $delegatesUser)
    {
        $organizationId = $calendarSharingDto->getOrganizationId();
        $personId = $calendarSharingDto->getPersonId();
        $person = $this->personService->find($personId);
        $organization = $this->organizationService->find($organizationId);
        if ($organizationId != $person->getOrganization()->getId()) {
            $this->logger->debug("Passed Organization and person oganization doesnt match");
            throw new AccessDeniedException();
        }

        foreach ($delegatesUser as $delegates) {
            $calendarSharing = new CalendarSharing();

            $calendarSharing->setPersonIdSharedby($person);

            $calendarSharing->setOrganization($organization);
            $delegateToPerson = $this->personService->find($delegates->getDelegatedToPersonId());
            $calendarSharing->setPersonIdSharedto($delegateToPerson);
            if ($organizationId != $delegateToPerson->getOrganization()->getId()) {
                $this->logger->debug("Passed Organization and delegate person oganization doesnt match");
                throw new AccessDeniedException();
            }

            $checkDelegateUser = $this->calendarSharingRepository->findBy(array(
                'organization' => $organizationId,
                'personIdSharedby' => $personId,
                'personIdSharedto' => $delegates->getDelegatedToPersonId()
            ));
            $sharedOn = new \DateTime('now');
            $calendarSharing->setSharedOn($sharedOn);

            $isSelected = $delegates->getIsSelected();
            $calendarSharing->setIsSelected($isSelected);
            $tokenValues = array();
            $tokenValues['fullname'] = $delegateToPerson->getFirstname();
            $tokenValues['delegater_name'] = $person->getFirstname() . ' ' . $person->getLastname() . "'s";
            // Including sky factor mapworks logo in email template
            $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
            $tokenValues['Skyfactor_Mapworks_logo'] = "";
            if ($systemUrl) {
                $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            $wasSelected = false;
            if ($checkDelegateUser) {
                $wasSelected = $checkDelegateUser[0]->getIsSelected();
            }
            if ($delegates->getIsDeleted()) {
                if ($checkDelegateUser) {
                    // deleteDelegateUser
                    $this->calendarSharingRepository->deleteDelegateUser($checkDelegateUser[0]);
                    // If it was selected but deleted from list
                    if ($wasSelected) {
                        $this->sendToStudents($delegateToPerson, $organizationId, "Remove_Delegate", $tokenValues);
                    }
                }
            } else {
                if ($checkDelegateUser) {
                    // updateDelegateUser
                    $checkDelegateUser[0]->setIsSelected($isSelected);
                    $this->calendarSharingRepository->updateDelegateUser($checkDelegateUser[0]);
                    if ($isSelected) {
                        // created TRUE
                        if (!$wasSelected) {
                            $this->sendToStudents($delegateToPerson, $organizationId, "Add_Delegate", $tokenValues);
                        }
                    } else {
                        // created False
                        $this->sendToStudents($delegateToPerson, $organizationId, "Remove_Delegate", $tokenValues);
                    }
                } else {
                    // createDelegateUser
                    $this->calendarSharingRepository->createDelegateUser($calendarSharing);
                    // Send Email
                    $this->sendToStudents($delegateToPerson, $organizationId, "Add_Delegate", $tokenValues);
                }
            }

            $this->calendarSharingRepository->flush();

            if ($checkDelegateUser) {
                $delegates->setCalendarSharingId($checkDelegateUser[0]->getId());
                $delegates->setSharedOn($checkDelegateUser[0]->getSharedOn());
            } else {
                $delegates->setCalendarSharingId($calendarSharing->getId());
                $delegates->setSharedOn($calendarSharing->getSharedOn());
            }
        }
    }

    public function listManagedUsers($orgId, $proxyUserId)
    {
        $this->logger->debug(">>>> List Managed Users" . "Organization Id" . $orgId . "proxyUserId" . $proxyUserId);
        $this->orgService = $this->container->get(AppointmentsConstant::ORG_SERVICE);
        $this->calandarSharingRepository = $this->repositoryResolver->getRepository(self::CALENDAR_SHARE_REPO);
        $this->orgService->find($orgId);
        $this->person = $this->container->get(AppointmentsConstant::PERSON_SERVICE);
        $this->person->find($proxyUserId);
        $responseArray = array();
        $resultUsers = $this->calandarSharingRepository->listManagedUser($proxyUserId);
        $newResultSet = array();
        foreach ($resultUsers as $user) {
            $personObj = $this->person->find($user['managed_person_id']);
            $contacts = $personObj->getContacts();
            if (empty($contacts)) {
                $email = "";
            } else {
                $email = $contacts[0]->getPrimaryEmail();
            }
            $user['managed_person_email'] = $email;
            array_push($newResultSet, $user);
        }
        $responseArray[AppointmentsConstant::ORGANIZATIONID] = $orgId;
        $responseArray['person_id_proxy'] = $proxyUserId;
        $responseArray['managed_users'] = $newResultSet;
        $this->logger->info(">>>> List Managed Users");
        return $responseArray;
    }

    public function listSelectedProxyUsers($orgId, $userId)
    {
        $this->logger->debug(">>>> List Selected ProxyUsers" . "Organization Id" . $orgId . "User Id" . $userId);
        $this->orgService = $this->container->get(AppointmentsConstant::ORG_SERVICE);
        $this->calandarSharingRepository = $this->repositoryResolver->getRepository(self::CALENDAR_SHARE_REPO);
        $responseArray = array();
        $resultUsers = $this->calandarSharingRepository->getSelectedProxyUsers($userId);
        $responseArray[AppointmentsConstant::ORGANIZATIONID] = $orgId;
        $responseArray[AppointmentsConstant::PERSON_ID] = $userId;
        $responseArray['delegated_users'] = $resultUsers;
        $this->logger->info(">>>> List Selected ProxyUsers");
        return $responseArray;
    }

    /**
     * Get appointments of the managed person by proxy person id - delegate access
     *
     * @param int $proxyPersonId
     * @param string $frequency
     * @param int $managedPersonId
     * @return array
     */
    public function listProxyAppointments($proxyPersonId, $frequency, $managedPersonId)
    {
        $currentNow = new \DateTime('now');
        $currentDate = $currentNow->format('Y-m-d');
        $currentDateTime = $currentNow->format('Y-m-d H:i:s');

        $proxyPerson = $this->personRepository->find($proxyPersonId);
        $organization = $proxyPerson->getOrganization();
        $this->isObjectExist($proxyPerson, 'Person Not Found.', 'Person_not_found');
        $this->isObjectExist($organization, 'Organization Not Found.', 'organization_not_found');

        $dateRange = $this->dateUtilityService->getDateRange($frequency, $currentDate);

        $fromDate = $dateRange['from_date'];
        $toDate = $dateRange['to_date'];

        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organization->getId());
        $proxyUserAppointments = $this->officehoursRepository->getProxyUsersAppointments($proxyPersonId, $fromDate, $toDate, $frequency, $managedPersonId, $currentDateTime, $orgAcademicYearId);

        $responseArray = [];
        $calendar = [];
        $pcsCalendarId = [];
        $pcsEvents = [];
        if ($proxyUserAppointments) {
            $responseArray['person_id_proxy'] = $proxyPerson->getId();
            $responseArray['organization_id'] = $organization->getId();

            foreach ($proxyUserAppointments as $proxyAppointment) {

                $calendarSlotDto = new CalendarTimeSlotsReponseDto();
                $calendarSlotDto->setAppointmentId($this->isNullValue($proxyAppointment['appointments_id'], "0"));
                if ($proxyAppointment['office_hours_id'] > 0) {
                    $slotStart = $proxyAppointment['slot_start'];
                    $slotEnd = $proxyAppointment['slot_end'];
                    $proxyAppointmentSlotType = $this->isNullValue($proxyAppointment['slot_type'], "");
                } else {
                    $slotStart = $proxyAppointment['start_date_time'];
                    $slotEnd = $proxyAppointment['end_date_time'];
                    $proxyAppointmentSlotType = $this->isNullValue($proxyAppointment['type'], "");
                }
                $startAppointmentDatetime = new \DateTime($slotStart);
                $endAppointmentDatetime = new \DateTime($slotEnd);

                $calendarSlotDto->setSlotStart($startAppointmentDatetime);
                $calendarSlotDto->setSlotEnd($endAppointmentDatetime);

                $personManaged = $this->personRepository->find($proxyAppointment['person_id']);

                $calendarSlotDto->setManagedPersonId($proxyAppointment['person_id']);
                $calendarSlotDto->setManagedPersonFirstName($personManaged->getFirstname());
                $calendarSlotDto->setManagedPersonLastName($personManaged->getLastname());

                $calendarSlotDto->setLocation($this->isNullValue($proxyAppointment['location'], ""));
                $calendarSlotDto->setReason($this->isNullValue($proxyAppointment['title'], ""));
                $calendarSlotDto->setReasonId($this->isNullValue($proxyAppointment['activity_category_id'], ""));
                $calendarSlotDto->setSlotType($proxyAppointmentSlotType);
                $calendarSlotDto->setOfficeHoursId($this->isNullValue($proxyAppointment['office_hours_id'], ""));
                $calendarSlotDto->setIsSlotCancelled($this->isNullValue($proxyAppointment['is_cancelled'], ""));
                $calendarSlotDto->setPcsCalendarId($proxyAppointment['google_appointment_id']);
                $calendarSlotDto->setIsConflictedFlag(false);

                $pcsCalendarId[] = $proxyAppointment['google_appointment_id'];
                if ($proxyAppointment['appointments_id']) {
                    $appointmentEntity = $this->appointmentsRepository->find($proxyAppointment['appointments_id']);
                    $calendarSlotDto->setLocation(is_null($proxyAppointment['app_loc']) ? $proxyAppointment['location'] : $proxyAppointment['app_loc']);
                    if (isset($appointmentEntity)) {
                        $attendees = $this->appointmentRecipientAndStatusRepository->findBy([
                            'appointments' => $appointmentEntity
                        ]);

                        $attendeesList = [];
                        foreach ($attendees as $attendee) {
                            $attendeeDto = new AttendeesDto();
                            $attendeeDto->setStudentId($attendee->getPersonIdStudent()->getId());
                            $attendeeDto->setStudentFirstName($attendee->getPersonIdStudent()->getFirstname());
                            $attendeeDto->setStudentLastName($attendee->getPersonIdStudent()->getLastname());
                            $attendeesList[] = $attendeeDto;
                        }
                        $calendarSlotDto->setAttendees($attendeesList);
                    }
                }
                $calendar[] = $calendarSlotDto;

            }
        }

        $managedPersonIds = explode(',', $managedPersonId);
        if (!empty($managedPersonIds)) {
            foreach ($managedPersonIds as $personId) {
                $organizationId = $organization->getId();
                $calendarSettings = $this->calendarIntegrationService->facultyCalendarSettings($organizationId, $personId);
                if ($calendarSettings['facultyPCSTOMAF'] == "y") {
                    $googleSyncStatus = $calendarSettings['google_sync_status'];
                    $pcsEvents = $this->calendarFactoryService->getBusyEvents($personId, $organizationId, $pcsCalendarId, $fromDate, $toDate, $googleSyncStatus);
                    $calendar = array_merge($pcsEvents, $calendar);
                }
            }
        }

        if (!empty($calendar)) {
            // Checking conflict event, setting isConflictedFlag and sorting the calendar variable.
            $calenderCount = count($calendar);
            for ($indexOne = 0; $indexOne < $calenderCount; $indexOne++) {
                $event1 = $calendar[$indexOne];
                for ($indexTwo = 1; $indexTwo < $calenderCount; $indexTwo++) {
                    $event2 = $calendar[$indexTwo];
                    //skip setting conflict if both index are same
                    if ($indexTwo != $indexOne) {
                        //setting conflicts in all scenario
                        if (($event1->getSlotStart() > $event2->getSlotStart()) && ($event1->getSlotStart() < $event2->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if (($event1->getSlotEnd() > $event2->getSlotStart()) && ($event1->getSlotEnd() < $event2->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if (($event2->getSlotStart() > $event1->getSlotStart()) && ($event2->getSlotStart() < $event1->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if (($event2->getSlotEnd() > $event1->getSlotStart()) && ($event2->getSlotEnd() < $event1->getSlotEnd())) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }

                        if ($event1->getSlotStart() == $event2->getSlotStart()) {
                            $event1->setIsConflictedFlag(true);
                            $event2->setIsConflictedFlag(true);
                        }
                    }
                }
            }

            // Perform sorting
            usort($calendar, function ($event1, $event2) {
                $event1StartDate = $event1->getSlotStart();
                $event1EndDate = $event1->getSlotEnd();
                $event1TimeDifference = strtotime($event1EndDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT)) - strtotime($event1StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));

                $event2StartDate = $event2->getSlotStart();
                $event2EndDate = $event2->getSlotEnd();
                $event2TimeDifference = strtotime($event2EndDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT)) - strtotime($event2StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));

                $formattedEvent1Date = $event1StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                $formattedEvent2Date = $event2StartDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

                // If more than one event are having equal time difference and their start time is same then show the    external event first
                if ($event1TimeDifference == $event2TimeDifference && $formattedEvent1Date == $formattedEvent2Date && ($event1->getSlotType() == 'B')) {
                    $showAsFirstEvent = 1;
                } else if (($event1TimeDifference > $event2TimeDifference) && ($event1StartDate == $event2StartDate)) {
                    // More than one event has same start time then longest event should be displayed first.
                    $showAsFirstEvent = 1;
                } else {
                    $showAsFirstEvent = 0;
                }
                $moveThisEventToTop = ($event1StartDate < $event2StartDate || $showAsFirstEvent == 1) ? -1 : 1;
                return $moveThisEventToTop;
            });

            $responseArray['person_id_proxy'] = $proxyPerson->getId();
            $responseArray['organization_id'] = $organization->getId();
            $responseArray['calendar_time_slots'] = $calendar;
        }
        return $responseArray;
    }

    private function isNullValue($value, $returnValue)
    {
        return ($value != null && $value) ? $value : $returnValue;
    }

    /**
     * Send Email Notification to Person Student
     *
     * @param Person $personStudent
     * @param int $organizationId
     * @param string $emailKey
     * @param array $tokenValues
     * @return null
     * @throws SynapseValidationException
     */
    public function sendToStudents($personStudent, $organizationId, $emailKey, $tokenValues)
    {
        $this->logger->debug(">>>> Send Email for Person Student" . " Organization ID " . $organizationId);

        $studentContactEmail = $personStudent->getUsername();
        if (!is_null($studentContactEmail)) {
            $emailTemplateObject = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailKey]);
            if ($emailTemplateObject) {
                $emailTemplateLangObject = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplateObject]);
            } else {
                throw new SynapseValidationException("Email template for key $emailKey not found");
            }

            if ($emailTemplateLangObject) {
                $emailResponse = [];

                $emailBody = $emailTemplateLangObject->getBody();
                $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);

                $bcc = $emailTemplateLangObject->getEmailTemplate()->getBccRecipientList();
                $subject = $emailTemplateLangObject->getSubject();
                $from = $emailTemplateLangObject->getEmailTemplate()->getFromEmailAddress();
                $emailResponse['email_detail'] = array(
                    'from' => $from,
                    'subject' => $subject,
                    'bcc' => $bcc,
                    'body' => $emailBody,
                    'to' => $studentContactEmail,
                    'emailKey' => $emailKey,
                    'organizationId' => $organizationId
                );
            }

            $emailInstance = $this->emailService->sendEmailNotification($emailResponse['email_detail']);
            $this->emailService->sendEmail($emailInstance);


        }
        $this->logger->info(">>>> Send Email for Person Student");
    }

    private function isObjectExist($object, $message, $key)
    {
        if (!isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }
}