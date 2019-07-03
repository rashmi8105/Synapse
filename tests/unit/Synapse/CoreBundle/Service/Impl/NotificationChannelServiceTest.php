<?php
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\Service\Impl\CurlService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;


class NotificationChannelServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testRegisterChannel()
    {
        $this->specify("Test Registering a channel", function ($personObject) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgPersonPushNotificationChannelRepository = $this->getMock('orgPersonPushNotificationChannelRepository', ['persist']);

            $mockRepositoryResolver->method('getRepository')->willReturn($mockOrgPersonPushNotificationChannelRepository);


            $notificationChannelService = new NotificationChannelService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $notificationChannelService->registerChannel($personObject);

            $resultArr = explode("-", $result);

            $this->assertEquals($resultArr[0], $personObject->getOrganization()->getId()); // first element of the array should have the organization id
            $this->assertEquals($resultArr[1], $personObject->getId()); // second element of the array should have the person id

        }, [
                'examples' => [
                    // creating channel for the person id 2 and organization id 1
                    [
                        $this->createPersonObject(1, 2)
                    ],
                    // creating channel for the person id 4 and organization id 3
                    [
                        $this->createPersonObject(3, 4)
                    ],
                ]
            ]
        );
    }

    private function createPersonObject($organizationId, $personId)
    {

        $mockPersonEntity = $this->getMock('Person', ['getOrganization', 'getId']);
        $mockOrganization = $this->getMock('Organization', ['getId']);
        $mockOrganization->method('getId')->willReturn($organizationId);
        $mockPersonEntity->method('getOrganization')->willReturn($mockOrganization);
        $mockPersonEntity->method('getId')->willReturn($personId);
        return $mockPersonEntity;

    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testUnregisterChannel()
    {
        $this->specify("Test un  registering  a channel", function ($userId, $organizationId, $channelName, $channelExists = true) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgPersonPushNotificationChannel = $this->getMock('OrgPersonPushNotificationChannel', ['getPerson', 'getOrganization']);
            $mockOrgPersonPushNotificationChannel->method('getPerson')->willReturn(1);
            $mockOrgPersonPushNotificationChannel->method('getOrganization')->willReturn(1);


            $mockOrgPersonPushNotificationChannelRepository = $this->getMock('orgPersonPushNotificationChannelRepository', ['persist', 'findOneBy', 'delete']);

            $mockOrgPersonPushNotificationChannelRepository->method('delete')->willReturn(1);
            if ($channelExists) {
                $mockOrgPersonPushNotificationChannelRepository->method('findOneBy')->willReturn($mockOrgPersonPushNotificationChannel);
            } else {
                $mockOrgPersonPushNotificationChannelRepository->method('findOneBy')->willReturn($channelExists);
            }

            $mockRepositoryResolver->method('getRepository')->willReturn($mockOrgPersonPushNotificationChannelRepository);

            $notificationChannelService = new NotificationChannelService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $notificationChannelService->unRegisterChannel($userId, $organizationId, $channelName);


        }, [
                'examples' => [
                    // Channel would be deleted sucessfully
                    [
                        1, 2, "channelName", true
                    ],
                    // throws synapse validation exception that channel does not exist for the user
                    [
                        3, 4, "channelName", false
                    ],
                ]
            ]
        );
    }

    public function testSendNotificationToAllRegisteredChannels()
    {


        $this->specify("Test un-registering  a channel", function ($user, $channels) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgPersonPushNotificationChannelRepository = $this->getMock('orgPersonPushNotificationChannelRepository', ['persist', 'findOneBy', 'delete', 'getChannelNameForUser']);
            $mockOrgPersonPushNotificationChannelRepository->method('getChannelNameForUser')->willReturn($channels);
            $mockRepositoryResolver->method('getRepository')->willReturn($mockOrgPersonPushNotificationChannelRepository);
            $mockCurlService = $this->getMock('CurlService',['sendCurlRequest']);
            $mockCurlService->method('sendCurlRequest')->willReturn(1);

            $mockEbiConfigService = $this->getMock('EbiConfigService',['get']);
            $mockEbiConfigService->method('get')->willReturnMap(
                [
                    [
                        'PUSH_NOTIFICATION_NUMBER_OF_CHANNELS_PER_REQUEST',99
                    ],
                    [
                        'PUSH_NOTIFICATION_API_TOKEN',"SOMETOKEN"
                    ],
                    [
                        'PUSH_NOTIFICATION_API_URL',"https://ortc-developers-useast1-s0001.realtime.co/sendbatch"
                    ],
                    [
                        'PUSH_NOTIFICATION_API_KEY',"CO6vUS"
                    ]
                ]
            );

            $mockContainer->method('get')->willReturnMap(
                [
                    [
                        CurlService::SERVICE_KEY,
                        $mockCurlService

                    ],
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ]
                ]
            );
            $notificationChannelService = new NotificationChannelService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $notificationChannelService->sendNotificationToAllRegisteredChannels($user, "test");


            $this->assertTrue(TRUE); // This method does not return anything, it just curls a third part url, if it comes to this line , then the function executed successfully

        }, [
                'examples' => [
                    // This would curl the third party api to send message to the channel
                    [
                        $this->createPersonObject(1, 2), ['channel1']
                    ],
                    // This would not hit the third party api as there is no channel
                    [
                        $this->createPersonObject(1, 2), []
                    ],
                ]
            ]
        );

    }


    public function testLogPushNotification()
    {
        $this->specify("Test logging of data for push notification", function ($person, $organization, $channel, $event, $dataPushedToChannel = null, $responseFromPushServer = null) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockPushNotificationLog = $this->getMock('PushNotificationLog', ['getPerson', 'getOrganization', 'getChannelName', 'getEventKey', 'getDataPostedToPushServer', 'getResponseFromPushServer']);
            $mockPushNotificationLog->method('getOrganization')->willReturn($organization);
            $mockPushNotificationLog->method('getPerson')->willReturn($person);
            $mockPushNotificationLog->method('getChannelName')->willReturn($channel);
            $mockPushNotificationLog->method('getEventKey')->willReturn($event);
            $mockPushNotificationLog->method('getDataPostedToPushServer')->willReturn($dataPushedToChannel);
            $mockPushNotificationLog->method('getResponseFromPushServer')->willReturn($responseFromPushServer);


            $mockPushNotificationLogRepository = $this->getMock('PushNotificationLogRepository', ['persist',]);

            $mockPushNotificationLogRepository->method('persist')->willReturn($mockPushNotificationLog);

            $mockRepositoryResolver->method('getRepository')->willReturn($mockPushNotificationLogRepository);

            $notificationChannelService = new NotificationChannelService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $notificationPushLog = $notificationChannelService->logPushNotification($person, $organization, $channel, $event, $dataPushedToChannel, $responseFromPushServer);

            $this->assertEquals($notificationPushLog->getOrganization(), $organization);
            $this->assertEquals($notificationPushLog->getPerson(), $person);
            $this->assertEquals($notificationPushLog->getChannelName(), $channel);
            $this->assertEquals($notificationPushLog->getEventKey(), $event);
            $this->assertEquals($notificationPushLog->getDataPostedToPushServer(), $dataPushedToChannel);
            $this->assertEquals($notificationPushLog->getResponseFromPushServer(), $responseFromPushServer);


        }, [
                'examples' => [
                    // logging data when Channel created successfully
                    [
                        $this->createPersonObject(2, 1), $this->createPersonObject(2, 1)->getOrganization(), "channelName", "channel_created"
                    ],
                    // logging data when some data is being sent to push server
                    [
                        $this->createPersonObject(2, 1), $this->createPersonObject(2, 1)->getOrganization(), "channelName", "push_notification_to_channels", "somedataposted", "server response"
                    ],
                    // logging data when channel is unregistered
                    [
                        $this->createPersonObject(2, 1), $this->createPersonObject(2, 1)->getOrganization(), "channelName", "channel_deleted"
                    ],
                ]
            ]
        );
    }

    public function testGetNotificationChannelConfigurations()
    {
        $this->specify("Test to get the configuration set up values", function ($apiKey, $authDomain, $databaseUrl, $projectId, $storageBucket, $senderId, $applicationKey) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        \Synapse\CoreBundle\Repository\EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ],
                ]);

            $mockEbiConfigRepository->method('findOneBy')->willReturnCallback(function ($inputData) {
                $ebiConfigEntity = new \Synapse\CoreBundle\Entity\EbiConfig();
                switch ($inputData['key']) {
                    case 'PUSH_NOTIFICATION_FIREBASE_API_KEY':
                        $ebiConfigEntity->setValue('https://skyfactor-push-notification.firebaseio.com');
                        break;
                    case 'PUSH_NOTIFICATION_AUTH_DOMAIN':
                        $ebiConfigEntity->setValue('skyfactor-push-notification.firebaseapp.com');
                        break;
                    case 'PUSH_NOTIFICATION_DATABASE_URL':
                        $ebiConfigEntity->setValue('https://skyfactor-push-notification.firebaseio.com');
                        break;
                    case 'PUSH_NOTIFICATION_PROJECT_ID':
                        $ebiConfigEntity->setValue('skyfactor-push-notification');
                        break;
                    case 'PUSH_NOTIFICATION_STORAGE_BUCKET':
                        $ebiConfigEntity->setValue('skyfactor-push-notification.appspot.com');
                        break;
                    case 'PUSH_NOTIFICATION_MESSAGING_SENDER_ID':
                        $ebiConfigEntity->setValue('466332768821');
                        break;
                    case 'PUSH_NOTIFICATION_APPLICATION_KEY':
                        $ebiConfigEntity->setValue('CO6vUS');
                        break;
                }
                return $ebiConfigEntity;
            });

            $notificationChannelService = new NotificationChannelService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $notificationSetupValues = $notificationChannelService->getNotificationChannelConfigurations();

            $this->assertEquals($notificationSetupValues->getApiKey(), $apiKey);
            $this->assertEquals($notificationSetupValues->getAuthDomain(), $authDomain);
            $this->assertEquals($notificationSetupValues->getDatabaseUrl(), $databaseUrl);
            $this->assertEquals($notificationSetupValues->getProjectId(), $projectId);
            $this->assertEquals($notificationSetupValues->getStorageBucket(), $storageBucket);
            $this->assertEquals($notificationSetupValues->getMessagingSenderId(), $senderId);
            $this->assertEquals($notificationSetupValues->getApplicationKey(), $applicationKey);
        }, [
                'examples' => [
                    // test set up data from config table.
                    [
                        'https://skyfactor-push-notification.firebaseio.com',
                        'skyfactor-push-notification.firebaseapp.com',
                        'https://skyfactor-push-notification.firebaseio.com',
                        'skyfactor-push-notification',
                        'skyfactor-push-notification.appspot.com',
                        '466332768821',
                        'CO6vUS'
                    ],
                ]
            ]
        );
    }
}