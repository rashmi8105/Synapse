<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonPushNotificationChannel;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\PushNotificationLog;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\OrgPersonPushNotificationChannelRepository;
use Synapse\CoreBundle\Repository\PushNotificationLogRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\RestBundle\Entity\NotificationChannelSetupDto;


/**
 * @DI\Service("notification_channel_service")
 */
class NotificationChannelService extends AbstractService
{

    const SERVICE_KEY = 'notification_channel_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    protected $rbacManager;

    //service

    /**
     * @var CurlService
     */
    private $curlService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;


    //Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrgPersonPushNotificationChannelRepository
     */
    private $orgPersonPushNotificationChannelRepository;

    /**
     * @var PushNotificationLogRepository
     */
    private $pushNotificationLogRepository;

    /**
     * NotificationChannelService constructor.
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
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        //Services
        $this->curlService = $this->container->get(CurlService::SERVICE_KEY);
        $this->ebiConfigService =  $this->container->get(EbiConfigService::SERVICE_KEY);

        //Repository
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->orgPersonPushNotificationChannelRepository = $this->repositoryResolver->getRepository(OrgPersonPushNotificationChannelRepository::REPOSITORY_KEY);
        $this->pushNotificationLogRepository = $this->repositoryResolver->getRepository(PushNotificationLogRepository::REPOSITORY_KEY);
    }


    /**
     * Creates the channel name and saves it in the database for the logged in user
     *
     * @param Person $loggedInUserObject
     * @return string
     */
    public function registerChannel($loggedInUserObject)
    {
        $organization = $loggedInUserObject->getOrganization();
        $organizationId = $organization->getId();
        $loggedInUserId = $loggedInUserObject->getId();
        $guid = $this->GUID();
        $channelName = $organizationId . "-" . $loggedInUserId . "-" . $guid;
        $orgPersonPushNotificationChannel = new OrgPersonPushNotificationChannel();
        $orgPersonPushNotificationChannel->setPerson($loggedInUserObject);
        $orgPersonPushNotificationChannel->setOrganization($organization);
        $orgPersonPushNotificationChannel->setChannelName($channelName);
        $this->orgPersonPushNotificationChannelRepository->persist($orgPersonPushNotificationChannel);
        $this->logPushNotification($loggedInUserObject, $organization, $channelName, 'channel_created');
        return $orgPersonPushNotificationChannel->getChannelName();
    }

    /**
     * Create GUID
     *
     * @return string
     */
    private function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    /**
     * Sends notification to all the channels for the User
     *
     * @param Person $user
     * @param string $notificationType
     * @return void
     */
    public function sendNotificationToAllRegisteredChannels($user, $notificationType)
    {
        $userId = $user->getId();
        $organization = $user->getOrganization();
        $organizationId = $organization->getId();
        $channelList = $this->orgPersonPushNotificationChannelRepository->getChannelNameForUser($userId, $organizationId);
        $numberOfChannels =  count($channelList);


        $pushNotificationApplicationKey = $this->ebiConfigService->get('PUSH_NOTIFICATION_API_KEY');
        $pushNotificationApplicationToken = $this->ebiConfigService->get('PUSH_NOTIFICATION_API_TOKEN');
        $pushNotificationApplicationApiUrl = $this->ebiConfigService->get('PUSH_NOTIFICATION_API_URL');
        $pushNotificationNumberOfChannelsPerRequest =  $this->ebiConfigService->get('PUSH_NOTIFICATION_NUMBER_OF_CHANNELS_PER_REQUEST');

        if ($numberOfChannels > 0) {
            // if the number of channels are more than 100 , push notifications to 99 channels per batch
            $channelsArray =  array_chunk($channelList, $pushNotificationNumberOfChannelsPerRequest);
            foreach($channelsArray as $channels ){

                $channelText = implode(",", $channels);
                $curlPostData = [
                    'AK' => $pushNotificationApplicationKey,
                    'AT' => $pushNotificationApplicationToken,
                    'M' => $notificationType,
                    'C' => $channelText
                ];
                $postFields = "";
                foreach ($curlPostData as $postKey => $postValue) {
                    $postFields .= $postKey . '=' . $postValue . '&';
                }
                $countPostFields =  count($curlPostData);
              //  $curlResponse = $this->curlService->sendCurlRequest($pushNotificationApplicationApiUrl ,$postFields, $countPostFields);
                //foreach ($channels as $channel) {
                   // $this->logPushNotification($user, $organization, $channel, 'push_notification_to_channels', $postFields, $curlResponse);
                //}
            }
        }
    }

    /**
     * un register the user channel
     *
     * @param integer $userId
     * @param integer $organizationId
     * @param string $channelName
     * @return void
     * @throws SynapseValidationException
     */
    public function unRegisterChannel($userId, $organizationId, $channelName)
    {

        $channelObject = $this->orgPersonPushNotificationChannelRepository->findOneBy([
            'person' => $userId,
            'organization' => $organizationId,
            'channelName' => $channelName
        ]);
        if ($channelObject) {
            $this->orgPersonPushNotificationChannelRepository->delete($channelObject);
            $this->logPushNotification($channelObject->getPerson(), $channelObject->getOrganization(), $channelName, 'channel_deleted');
        } else {
            throw new SynapseValidationException("No Channel with name $channelName was found for the user");
        }
    }

    /**
     * Used for logging the activities with the push notification server
     *
     * @param Person $person
     * @param Organization $organization
     * @param string $channel
     * @param string $event
     * @param null|string $dataPushedToChannel
     * @param null|string $responseFromPushServer
     * @return PushNotificationLog
     */
    public function logPushNotification($person, $organization, $channel, $event, $dataPushedToChannel = null, $responseFromPushServer = null)
    {
        $pushNotificationLog = new PushNotificationLog();
        $pushNotificationLog->setPerson($person);
        $pushNotificationLog->setOrganization($organization);
        $pushNotificationLog->setChannelName($channel);
        $pushNotificationLog->setEventKey($event);
        $pushNotificationLog->setDataPostedToPushServer($dataPushedToChannel);
        $pushNotificationLog->setResponseFromPushServer($responseFromPushServer);
        $pushNotificationLogObject = $this->pushNotificationLogRepository->persist($pushNotificationLog);

        return $pushNotificationLogObject;
    }

    /**
     * Return push notification server - set up values.
     *
     * @return NotificationChannelSetupDto
     */
    public function getNotificationChannelConfigurations()
    {
        $pushNotificationAPIKey = $this->ebiConfigRepository->findOneBy(['key' => 'PUSH_NOTIFICATION_FIREBASE_API_KEY']);
        $pushNotificationAuthDomain = $this->ebiConfigRepository->findOneBy(['key' => 'PUSH_NOTIFICATION_AUTH_DOMAIN']);
        $pushNotificationDataBaseURL = $this->ebiConfigRepository->findOneBy(['key' => 'PUSH_NOTIFICATION_DATABASE_URL']);
        $pushNotificationProjectId = $this->ebiConfigRepository->findOneBy(['key' => 'PUSH_NOTIFICATION_PROJECT_ID']);
        $pushNotificationStorageBucket = $this->ebiConfigRepository->findOneBy(['key' => 'PUSH_NOTIFICATION_STORAGE_BUCKET']);
        $pushNotificationSenderId = $this->ebiConfigRepository->findOneBy(['key' => 'PUSH_NOTIFICATION_MESSAGING_SENDER_ID']);
        $pushNotificationApplicationKey = $this->ebiConfigRepository->findOneBy(['key' => 'PUSH_NOTIFICATION_APPLICATION_KEY']);

        $notificationChannelSetupDto = new NotificationChannelSetupDto();
        $notificationChannelSetupDto->setApiKey($pushNotificationAPIKey->getValue());
        $notificationChannelSetupDto->setAuthDomain($pushNotificationAuthDomain->getValue());
        $notificationChannelSetupDto->setDatabaseUrl($pushNotificationDataBaseURL->getValue());
        $notificationChannelSetupDto->setProjectId($pushNotificationProjectId->getValue());
        $notificationChannelSetupDto->setStorageBucket($pushNotificationStorageBucket->getValue());
        $notificationChannelSetupDto->setMessagingSenderId($pushNotificationSenderId->getValue());
        $notificationChannelSetupDto->setApplicationKey($pushNotificationApplicationKey->getValue());
        return $notificationChannelSetupDto;
    }

}