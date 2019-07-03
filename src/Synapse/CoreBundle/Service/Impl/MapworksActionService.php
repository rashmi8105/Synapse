<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\AlertNotificationReferral;
use Synapse\CoreBundle\Entity\MapworksAction;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ReferralHistory;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AlertNotificationReferralRepository;
use Synapse\CoreBundle\Repository\MapworksActionRepository;
use Synapse\CoreBundle\Repository\MapworksActionVariableRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;

/**
 * @DI\Service("mapworks_action_service")
 */
class MapworksActionService extends AbstractService
{
    const SERVICE_KEY = 'mapworks_action_service';

    //scaffolding

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     * @var EmailService
     */
    private $emailService;

    // Repositories

    /**
     * @var AlertNotificationReferralRepository
     */
    private $alertNotificationReferralRepository;

    /**
     * @var MapworksActionRepository
     */
    private $mapworksActionRepository;

    /**
     * @var MapworksActionVariableRepository
     */
    private $mapworksActionVariableRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
     * MapworksActionService Construct
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
        $this->container = $container;
        //Services
        $this->alertNotificationService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        //Repositories
        $this->alertNotificationReferralRepository = $this->repositoryResolver->getRepository(AlertNotificationReferralRepository::REPOSITORY_KEY);
        $this->mapworksActionRepository = $this->repositoryResolver->getRepository(MapworksActionRepository::REPOSITORY_KEY);
        $this->mapworksActionVariableRepository = $this->repositoryResolver->getRepository(MapworksActionVariableRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Sends an email and/or alert notification to the user.
     *
     * @param int $organizationId
     * @param string $action
     * @param string $recipientType
     * @param string $eventType
     * @param int $recipientId
     * @param string $notificationReason
     * @param null| Referrals $activityObject
     * @param null | ReferralHistory $referralHistory
     * @param null | array $tokenValues
     * @return bool
     * @throws SynapseValidationException
     */
    public function sendCommunicationBasedOnMapworksAction($organizationId, $action, $recipientType, $eventType, $recipientId, $notificationReason, $activityObject = null, $referralHistory = null, $tokenValues = null)
    {
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $mapworksActionObject = $this->mapworksActionRepository->findOneBy([
            'action' => $action,
            'recipientType' => $recipientType,
            'eventType' => $eventType
        ]);
        if (!$mapworksActionObject) {
            throw new SynapseValidationException("There is no valid mapworks action that can result in a communication");
        }
        $receivesNotification = $mapworksActionObject->getReceivesNotification();
        $receivesEmail = $mapworksActionObject->getReceivesEmail();
        $eventKey = $mapworksActionObject->getEventKey();
        $allCommunicationsSentSuccessfully = false;
        $emailSuccessfullySent = false;
        $notificationSuccessfullySent = false;
        $recipient = $this->personRepository->find($recipientId);
        if ($recipient) {
            $recipientEmail = $recipient->getUsername();
        } else {
            throw new SynapseValidationException("Email Recipient Not Found");
        }
        // send email notification
        if ($receivesEmail) {
            $emailResponse = $this->emailService->buildEmailResponse($organizationId, $recipientEmail, $tokenValues, $mapworksActionObject);
            $emailObject = $this->emailService->sendEmailNotification($emailResponse);
            $emailSuccessfullySent = $this->emailService->sendEmail($emailObject);
        }
        // Send alert notification
        if ($receivesNotification) {
            $organization = $this->organizationRepository->find($organizationId);
            $alertNotification = $this->alertNotificationService->createNotification($eventKey, $notificationReason, $recipient, $activityObject, null, null, null, null, null, $organization);
            if ($referralHistory && $referralHistory !== '') {
                $alertNotificationReferral = new AlertNotificationReferral();
                $alertNotificationReferral->setAlertNotification($alertNotification);
                $alertNotificationReferral->setReferralHistory($referralHistory);
                $bodyText = $this->buildCommunicationBodyFromVariables($mapworksActionObject->getNotificationBodyText(), $tokenValues, $mapworksActionObject);
                $hoverText = $this->buildCommunicationBodyFromVariables($mapworksActionObject->getNotificationHoverText(), $tokenValues, $mapworksActionObject);
                $alertNotificationReferral->setNotificationBodyText($bodyText);
                $alertNotificationReferral->setNotificationHoverText($hoverText);
                $this->alertNotificationReferralRepository->persist($alertNotificationReferral);
            }
            if ($alertNotification) {
                $notificationSuccessfullySent = true;
            }
        }
        if (($receivesNotification == $notificationSuccessfullySent) && ($receivesEmail == $emailSuccessfullySent)) {
            $allCommunicationsSentSuccessfully = true;
        }
        return $allCommunicationsSentSuccessfully;
    }

    /**
     * Builds the token variables array for the person
     *
     * @param string $recipientType
     * @param Person $person
     * @return array
     */
    public function getTokenVariablesFromPerson($recipientType, $person)
    {
        $tokenVariables = [];
        if ($person) {
            $tokenVariables['$$' . $recipientType . '_first_name$$'] = $person->getFirstname();
            $tokenVariables['$$' . $recipientType . '_last_name$$'] = $person->getLastname();
            $tokenVariables['$$' . $recipientType . '_email_address$$'] = $person->getUsername();
            $tokenVariables['$$' . $recipientType . '_title$$'] = $person->getTitle();
        }
        return $tokenVariables;
    }

    /**
     * Builds the body of a Mapworks communication from the token values.
     *
     * @param string $textBody
     * @param array $tokenValues
     * @param MapworksAction $mapworksAction
     * @return string
     */
    public function buildCommunicationBodyFromVariables($textBody, $tokenValues, $mapworksAction)
    {

        $mapworksActionVariables = $this->mapworksActionVariableRepository->findBy(['mapworksAction' => $mapworksAction]);
        $filteredTokenValues = [];
        foreach ($mapworksActionVariables as $mapworksActionVariable) {
            if ($mapworksActionVariable->getMapworksActionVariableDescription()) {
                $communicationVariable = $mapworksActionVariable->getMapworksActionVariableDescription()->getVariable();
                $filteredTokenValues[$communicationVariable] = $tokenValues[$communicationVariable];
            }
        }
        $completedTextBody = strtr($textBody, $filteredTokenValues);
        return $completedTextBody;
    }
}