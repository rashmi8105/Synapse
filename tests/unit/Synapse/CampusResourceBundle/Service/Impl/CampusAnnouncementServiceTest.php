<?php

use Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementLangRepository;
use Synapse\CampusResourceBundle\Service\Impl\CampusAnnouncementService;
use Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementList;
use Synapse\CampusResourceBundle\EntityDto\SystemMessage;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\AlertNotifications;
use Synapse\CampusResourceBundle\Entity\OrgAnnouncements;
use Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementRepository;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;

class CampusAnnouncementServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testListBannerOrgAnnouncements()
    {
        $this->specify("", function ($expectedResult, $announcements = null, $personId = null, $organizationId = null) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockOrgCampusAnnouncementLangRepository = $this->getMock('orgAnnouncementRepository', ['listBannerOrgAnnouncements']);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrgCampusAnnouncementLangRepository::REPOSITORY_KEY, $mockOrgCampusAnnouncementLangRepository]
                ]
            );

            $mockOrgCampusAnnouncementLangRepository->method('listBannerOrgAnnouncements')->willReturn($announcements);

            $campusAnnouncementService = new CampusAnnouncementService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $functionResults = $campusAnnouncementService->listBannerOrgAnnouncements($personId, $organizationId);

            $this->assertEquals($expectedResult, $functionResults);

        }, [
                'examples' =>
                    [
                        //Data present for campus announcements
                        [
                            $this->createCampusAnnouncementListDTO($this->generateTestDataForOrgAnnouncements(), 1, 1),
                            $this->generateTestDataForOrgAnnouncements(),
                            1,
                            1
                        ],
                        //No data present
                        [
                            $this->createCampusAnnouncementListDTO([], 1, 1),
                            [],
                            1,
                            1
                        ],
                        //Invalid person ID
                        [
                            $this->createCampusAnnouncementListDTO($this->generateTestDataForOrgAnnouncements(), '', 1),
                            $this->generateTestDataForOrgAnnouncements(),
                            '',
                            1
                        ],
                        //Invalid org ID
                        [
                            $this->createCampusAnnouncementListDTO($this->generateTestDataForOrgAnnouncements(), '', ''),
                            $this->generateTestDataForOrgAnnouncements(),
                            '',
                            ''
                        ]
                    ]
            ]
        );

    }

    public function testMarkOrgAnnouncementAsRead()
    {
        $this->specify("", function ($expectedResult, $orgAnnouncement = null, $person = null, $orgAnnouncementId = null, $displayType = null) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockOrgCampusAnnouncementLangRepository = $this->getMock('orgAnnouncementRepository', ['find']);
            $mockAlertNotificationsRepository = $this->getMock('alertNotificationsRepository', ['persist']);


            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrgCampusAnnouncementRepository::REPOSITORY_KEY, $mockOrgCampusAnnouncementLangRepository],
                    [\Synapse\CoreBundle\Repository\AlertNotificationsRepository::REPOSITORY_KEY, $mockAlertNotificationsRepository]
                ]
            );

            if (is_string($expectedResult)) {
                $mockOrgCampusAnnouncementLangRepository->method('find')->willThrowException(new SynapseValidationException($expectedResult));
            } else {
                $mockOrgCampusAnnouncementLangRepository->method('find')->willReturn($orgAnnouncement);
            }


            $mockAlertNotificationsRepository->method('persist')->willReturn('');


            $campusAnnouncementService = new CampusAnnouncementService($mockRepositoryResolver, $mockLogger, $mockContainer);

            try {
                $functionResults = $campusAnnouncementService->markOrgAnnouncementAsRead($person, $orgAnnouncementId, $displayType);
                $this->assertEquals($expectedResult, $functionResults);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        }, [
                'examples' =>
                    [
                        //Valid data
                        [
                            $this->createAlertNotification(),
                            $this->createOrgAnnouncements(),
                            $this->createPerson(),
                            1,
                            'banner'
                        ],
                        // No valid parameters passed
                        [
                            "Logged in user not found"
                        ],
                        //valid logged in user, org announcements not found
                        [
                            "Organization announcement was not found.",
                            null,
                            $this->createPerson(),
                        ],
                        //invalid organnouncement ID passed
                        [
                            "Organization announcement was not found.",
                            null,
                            $this->createPerson(),
                            "STUFF"
                        ]
                    ]
            ]
        );
    }

    private function createCampusAnnouncementListDTO($systemMessageData, $personId, $orgId)
    {
        $campusAnnouncementListDTO = new CampusAnnouncementList();
        $campusAnnouncementListDTO->setOrganizationId($orgId);
        $campusAnnouncementListDTO->setPersonId($personId);

        $systemMessageDTOArray = [];
        foreach ($systemMessageData as $systemMessage) {
            $systemMessageDTO = new SystemMessage();
            $systemMessageDTO->setId($systemMessage['org_announcements_id']);
            $systemMessageDTO->setMessage($systemMessage['message']);
            $systemMessageDTO->setEndDateTime($systemMessage['stop_datetime']);
            $systemMessageDTO->setMessageDuration($systemMessage['message_duration']);
            $systemMessageDTO->setMessageType($systemMessage['display_type']);
            $systemMessageDTO->setStartDateTime($systemMessage['start_datetime']);

            $systemMessageDTOArray[] = $systemMessageDTO;
        }

        $campusAnnouncementListDTO->setSystemMessage($systemMessageDTOArray);

        return $campusAnnouncementListDTO;
    }


    private function generateTestDataForOrgAnnouncements($index = null)
    {
        $testData = [
            [
                'org_announcements_id' => 1,
                'message' => "Please see your supervisors about the emails you received.",
                'stop_datetime' => '2017-01-01 00:00:00',
                'message_duration' => 'month',
                'display_type' => 'banner',
                'start_datetime' => '2016-12-01 00:00:00'
            ],
            [
                'org_announcements_id' => 2,
                'message' => "Please see your team members about the emails you received.",
                'stop_datetime' => '2017-01-01 00:00:00',
                'message_duration' => 'month',
                'display_type' => 'banner',
                'start_datetime' => '2016-12-01 00:00:00'
            ],
            [
                'org_announcements_id' => 3,
                'message' => "Please see your friends about the emails you received.",
                'stop_datetime' => '2017-01-01 00:00:00',
                'message_duration' => 'month',
                'display_type' => 'banner',
                'start_datetime' => '2016-12-01 00:00:00'
            ],
            [
                'org_announcements_id' => 4,
                'message' => "Please see your minions about the emails you received.",
                'stop_datetime' => '2017-01-01 00:00:00',
                'message_duration' => 'month',
                'display_type' => 'banner',
                'start_datetime' => '2016-12-01 00:00:00'
            ],
            [
                'org_announcements_id' => 5,
                'message' => "Please see your frenemies about the emails you received.",
                'stop_datetime' => '2017-01-01 00:00:00',
                'message_duration' => 'month',
                'display_type' => 'banner',
                'start_datetime' => '2016-12-01 00:00:00'
            ],
        ];

        if (!is_null($index)) {
            return [$testData[$index]];
        } else {
            return $testData;
        }
    }

    private function createPerson()
    {
        $person = new Person();
        $organization = new Organization();
        $person->setOrganization($organization);
        $person->setId(1);

        return $person;
    }

    private function createAlertNotification($event = 'banner')
    {
         $notification = new AlertNotifications();
         $person = $this->createPerson();
         $notification->setPerson($person);
         $notification->setEvent($event);
         $notification->setOrganization($person->getOrganization());
         $notification->setOrgAnnouncements($this->createOrgAnnouncements());
         $notification->setIsRead(true);
         $notification->setIsSeen(true);

         return $notification;
    }

    private function createOrgAnnouncements()
    {
        $orgAnnouncement = new OrgAnnouncements();
        $orgAnnouncement->setDisplayType('banner');
        return $orgAnnouncement;
    }
}