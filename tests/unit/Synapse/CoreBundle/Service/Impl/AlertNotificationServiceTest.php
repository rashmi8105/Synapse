<?php

use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\CoreBundle\Entity\ActivityCategory;
use Synapse\CoreBundle\Entity\AlertNotifications;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AlertNotificationsRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\SearchBundle\Entity\OrgSearch;
use Synapse\StaticListBundle\Entity\OrgStaticList;
use Synapse\CoreBundle\Entity\Person;

class AlertNotificationsServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testUpdateNotificationViewStatus()
    {

        $this->specify("update Notification View Status", function ($alertNotificationId, $loggedInUserId, $expectedResult) {
            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            //Mock Repositories
            $mockAlertNotificationsRepository = $this->getMock('AlertNotificationsRepository', ['findOneBy', 'flush']);
            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [AlertNotificationsRepository::REPOSITORY_KEY, $mockAlertNotificationsRepository]
                ]
            );

            //Initialized with empty object
            $mockPersonObject = $this->getMock('Person', ['getOrganization']);
            $mockPersonObject->method('getOrganization')->willReturn('');

            $mockAlertNotificationObject = NULL;
            if (ctype_digit($alertNotificationId)) {
                $mockAlertNotificationObject = $this->createAlertNotifications($alertNotificationId);
                $mockAlertNotificationsRepository->method('findOneBy')->willReturn($mockAlertNotificationObject);
            } else {
                $mockAlertNotificationsRepository->method('findOneBy')->willThrowException($expectedResult);
            }


            $notificationService = new AlertNotificationsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $result = $notificationService->updateNotificationReadStatus($alertNotificationId, $loggedInUserId);
                $this->assertEquals($expectedResult, $result);
            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult->getMessage(), $e->getMessage());
            }

        }, ['examples' =>
            [
                // updating alert notification for id 5565
                [5565, $this->createPerson(), 5565],

                // The alert notification object wasn't found, because the alert notification doesn't exist.
                ['invalid', $this->createPerson(), new SynapseValidationException('Alert notification not found')],

                // The alert notification ID is not valid.
                [-1, $this->createPerson(), new SynapseValidationException('Alert notification not found')],

            ]]);
    }


    public function testCreateNotification()
    {
        $this->specify("create notification", function ($event, $reason, $referralObject, $appointmentObject, $orgSearchObject, $courseUploadFileName, $academicUpdateObject, $orgStaticListObject, $reportRunningStatusObject) {

            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            //Mock Repositories
            $alertNotifications = $this->getAlertNotifications($event, $reason, $referralObject, $appointmentObject, $orgSearchObject, $courseUploadFileName, $academicUpdateObject, $orgStaticListObject, $reportRunningStatusObject);
            $mockAlertNotificationsRepository = $this->getMock('AlertNotificationsRepository', ['create', 'flush']);
            $mockAlertNotificationsRepository->method('create')->willReturn($alertNotifications);

            // Mock Services
            $mockNotificationChannelService = $this->getMock('NotificationChannelService', ['sendNotificationToAllRegisteredChannels']);

            // Scaffolding for Repository
            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [AlertNotificationsRepository::REPOSITORY_KEY, $mockAlertNotificationsRepository]
                ]
            );

            // Scaffolding for service
            $mockContainer->method('get')->willReturnMap(
                [
                    [NotificationChannelService::SERVICE_KEY, $mockNotificationChannelService]
                ]
            );

            $alertNotificationsService = new AlertNotificationsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $alertNotificationsObject = $alertNotificationsService->createNotification($alertNotifications->getEvent(), $alertNotifications->getReason(), $alertNotifications->getPerson(), $alertNotifications->getReferrals(), $alertNotifications->getAppointments(), $alertNotifications->getOrgSearch(), $alertNotifications->getOrgCourseUploadFile(), $alertNotifications->getAcademicUpdate(), $alertNotifications->getOrgStaticList(), $alertNotifications->getOrganization(), true, $alertNotifications->getReportsRunningStatus(), null, null, null, null);
            $this->assertInternalType("object", $alertNotificationsObject);
            $this->assertInstanceOf('Synapse\CoreBundle\Entity\AlertNotifications', $alertNotificationsObject);

            // For referrals
            if ($alertNotifications->getReferrals()) {

                $actualResult = $alertNotifications->getReferrals();
                $expectedResult = $alertNotificationsObject->getReferrals();
                $this->assertEquals($actualResult->getStatus(), $expectedResult->getStatus());
                $this->assertEquals($actualResult->getPersonIdFaculty(), $expectedResult->getPersonFaculty());
                $this->assertEquals($actualResult->getPersonAssignedTo(), $expectedResult->getPersonAssignedTo());
                $this->assertEquals($actualResult->getPersonStudent(), $expectedResult->getPersonStudent());
                $this->assertEquals($actualResult, $expectedResult);
            }

            // For Appointments
            if ($alertNotifications->getAppointments()) {

                $actualResult = $alertNotifications->getAppointments();
                $expectedResult = $alertNotificationsObject->getAppointments();

                $this->assertEquals($actualResult->getPerson(), $expectedResult->getPerson());
                $this->assertEquals($actualResult->getActivityCategory(), $expectedResult->getActivityCategory());
                $this->assertEquals($actualResult->getLocation(), $expectedResult->getLocation());
                $this->assertEquals($actualResult->getStartDateTime(), $expectedResult->getStartDateTime());
                $this->assertEquals($actualResult->getEndDateTime(), $expectedResult->getEndDateTime());
                $this->assertEquals($actualResult->getAccessPublic(), $expectedResult->getAccessPublic());
                $this->assertEquals($actualResult, $expectedResult);
            }

            // For OrgSearch
            if ($alertNotifications->getOrgSearch()) {

                $actualResult = $alertNotifications->getOrgSearch();
                $expectedResult = $alertNotificationsObject->getOrgSearch();

                $this->assertEquals($actualResult->getPerson(), $expectedResult->getPerson());
                $this->assertEquals($actualResult->getName(), $expectedResult->getName());
                $this->assertEquals($actualResult->getQuery(), $expectedResult->getQuery());
                $this->assertEquals($actualResult->getJson(), $expectedResult->getJson());
                $this->assertEquals($actualResult->getEditedByMe(), $expectedResult->getEditedByMe());
                $this->assertEquals($actualResult->getFromSharedtab(), $expectedResult->getFromSharedtab());
                $this->assertEquals($actualResult, $expectedResult);
            }

            // For AcademicUpdate
            if ($alertNotifications->getAcademicUpdate()) {

                $actualResult = $alertNotifications->getAcademicUpdate();
                $expectedResult = $alertNotificationsObject->getAcademicUpdate();

                $this->assertEquals($actualResult->getOrg(), $expectedResult->getOrg());
                $this->assertEquals($actualResult->getUpdateType(), $expectedResult->getUpdateType());
                $this->assertEquals($actualResult->getStatus(), $expectedResult->getStatus());
                $this->assertEquals($actualResult->getRequestDate(), $expectedResult->getRequestDate());
                $this->assertEquals($actualResult->getDueDate(), $expectedResult->getDueDate());
                $this->assertEquals($actualResult->getIsAdhoc(), $expectedResult->getIsAdhoc());
                $this->assertEquals($actualResult, $expectedResult);
            }

            // For OrgStaticList
            if ($alertNotifications->getOrgStaticList()) {

                $actualResult = $alertNotifications->getOrgStaticList();
                $expectedResult = $alertNotificationsObject->getOrgStaticList();

                $this->assertEquals($actualResult->getOrganization(), $expectedResult->getOrganization());
                $this->assertEquals($actualResult->getPerson(), $expectedResult->getPerson());
                $this->assertEquals($actualResult->getName(), $expectedResult->getName());
                $this->assertEquals($actualResult->getDescription(), $expectedResult->getDescription());
                $this->assertEquals($actualResult->getPersonIdSharedBy(), $expectedResult->getPersonIdSharedBy());
                $this->assertEquals($actualResult->getSharedOn(), $expectedResult->getSharedOn());
                $this->assertEquals($actualResult, $expectedResult);
            }

            // For ReportsRunningStatus
            if ($alertNotifications->getReportsRunningStatus()) {

                $actualResult = $alertNotifications->getReportsRunningStatus();
                $expectedResult = $alertNotificationsObject->getReportsRunningStatus();

                $this->assertEquals($actualResult->getOrganization(), $expectedResult->getOrganization());
                $this->assertEquals($actualResult->getPerson(), $expectedResult->getPerson());
                $this->assertEquals($actualResult->getStatus(), $expectedResult->getStatus());
                $this->assertEquals($actualResult->getIsViewed(), $expectedResult->getIsViewed());
                $this->assertEquals($actualResult->getResponseJson(), $expectedResult->getResponseJson());
                $this->assertEquals($actualResult, $expectedResult);
            }

        }, ['examples' =>
            [
                // Notification for Referrals
                [
                    'referral_create_current_assignee',
                    'Personal Issues',
                    $this->getReferralInstance(),
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
                // Notification for Appointments
                [
                    'Appointment_Created',
                    'Personal Issues',
                    null,
                    $this->getAppointmentInstance(),
                    null,
                    null,
                    null,
                    null,
                    null
                ],
                // Notification for OrgSearch
                [
                    'SHARED_SEARCH',
                    'Personal Issues',
                    null,
                    null,
                    $this->getOrgSearchInstance(),
                    null,
                    null,
                    null,
                    null
                ],
                // Notification for AcademicUpdate
                [
                    'Academic_Update',
                    'You have received an academic update for one or more of your courses click here to review your update.',
                    null,
                    null,
                    null,
                    null,
                    $this->getAcademicUpdateInstance(),
                    null,
                    null
                ],
                // Notification for OrgStaticList
                [
                    'bulk-action-completed',
                    '1 students have been added to the static list',
                    null,
                    null,
                    null,
                    null,
                    null,
                    $this->getOrgStaticListInstance(),
                    null
                ],
                // Notification for ReportsRunningStatus
                [
                    'Reports running status',
                    'created',
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $this->getReportsRunningStatusInstance()
                ],
            ]
        ]);
    }


    private function getAlertNotifications($event, $reason, $referralObject = null, $appointmentObject = null, $orgSearchObject = null, $courseUploadFileName = null, $academicUpdateObject = null, $orgStaticListObject = null, $reportRunningStatusObject = null)
    {
        $alertNotification = new AlertNotifications();

        $alertNotification->setReason($event);
        $alertNotification->setEvent($reason);

        $alertNotification->setPerson($this->getPersonInstance());
        $alertNotification->setOrganization($this->getOrganizationInstance());
        $alertNotification->setReferrals($referralObject);
        $alertNotification->setAppointments($appointmentObject);
        $alertNotification->setOrgSearch($orgSearchObject);
        $alertNotification->setOrgCourseUploadFile($courseUploadFileName);
        $alertNotification->setAcademicUpdate($academicUpdateObject);
        $alertNotification->setOrgStaticList($orgStaticListObject);
        $alertNotification->setReportsRunningStatus($reportRunningStatusObject);

        $alertNotification->setIsRead(false);

        return $alertNotification;
    }


    private function getPersonInstance($id = 1)
    {
        $person = new Person();
        $person->setId($id);

        return $person;
    }

    private function getOrganizationInstance($campusId = 2)
    {
        $organization = new Organization();
        $organization->setCampusId($campusId);
        return $organization;
    }

    private function getReferralInstance()
    {
        $referrals = new Referrals();
        $referrals->setOrganization($this->getOrganizationInstance());
        $referrals->setStatus('O');
        $referrals->setPersonIdFaculty($this->getPersonInstance(1));
        $referrals->setPersonAssignedTo($this->getPersonInstance(2));
        $referrals->setPersonStudent($this->getPersonInstance(6));
        $referrals->setActivityCategory($this->getActivityCategory());
        return $referrals;
    }

    private function getActivityCategory()
    {
        $activityCategory = new ActivityCategory();
        $activityCategory->setId(19);

        return $activityCategory;
    }

    private function getAppointmentInstance()
    {
        $appointments = new Appointments();
        $appointments->setOrganization($this->getOrganizationInstance());
        $appointments->setPerson($this->getPersonInstance(1));
        $appointments->setTitle('Title1');
        $appointments->setActivityCategory($this->getActivityCategory());
        $appointments->setLocation('India');
        $appointments->setDescription('Test Appointment');
        $startTime = new \DateTime("+2 hour");
        $endTime = $startTime->modify("+30 minutes");
        $appointments->setStartDateTime($startTime);
        $appointments->setEndDateTime($endTime);
        $appointments->setSource('S');
        $appointments->setAccessPublic(true);
        return $appointments;
    }


    private function getOrgSearchInstance()
    {
        $orgSearch = new OrgSearch();
        $orgSearch->setOrganization($this->getOrganizationInstance());
        $orgSearch->setPerson($this->getPersonInstance(1));
        $orgSearch->setName('Test OrgSearch');
        $orgSearch->setQuery('Test OrgSearch Query');
        $orgSearch->setJson(json_encode([]));
        $orgSearch->setEditedByMe(true);
        $orgSearch->setFromSharedtab(1);
        return $orgSearch;
    }


    private function getAcademicUpdateInstance()
    {
        $academicUpdate = new AcademicUpdate();
        $academicUpdate->setOrg($this->getOrganizationInstance());
        $academicUpdate->setUpdateType('adhoc');
        $academicUpdate->setStatus('closed');
        $currentDate = new \DateTime();
        $academicUpdate->setRequestDate($currentDate);
        $academicUpdate->setDueDate($currentDate);
        $academicUpdate->setIsAdhoc(true);
        return $academicUpdate;
    }

    private function getOrgStaticListInstance()
    {
        $orgStaticList = new OrgStaticList();
        $orgStaticList->setOrganization($this->getOrganizationInstance());
        $orgStaticList->setPerson($this->getPersonInstance(1));
        $orgStaticList->setName('Test Name1');
        $orgStaticList->setDescription('Test Description1');
        $orgStaticList->setPersonIdSharedBy(2);
        $currentDate = new \DateTime();
        $orgStaticList->setSharedOn($currentDate);
        return $orgStaticList;
    }

    private function getReportsRunningStatusInstance()
    {
        $reportsRunningStatus = new ReportsRunningStatus();
        $reportsRunningStatus->setOrganization($this->getOrganizationInstance());
        $reportsRunningStatus->setPerson($this->getPersonInstance(1));
        $reportsRunningStatus->setStatus('C');
        $reportsRunningStatus->setIsViewed('Y');
        $reportsRunningStatus->setResponseJson(json_encode([]));
        return $reportsRunningStatus;
    }


    public function testUpdateAllUnseenNotificationsAsSeenForUser()
    {
        $this->specify("test updateAllUnseenNotificationsAsSeenForUser", function ($expectedResult, $personId = null) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockAlertNotificationsRepository = $this->getMock('alertNotificationsRepository', ['updateAllUnseenNotificationsAsSeenForUser']);

            if (is_int($personId)) {
                $mockAlertNotificationsRepository->method('updateAllUnseenNotificationsAsSeenForUser')->willReturn(true);
            } else {
                $mockAlertNotificationsRepository->method('updateAllUnseenNotificationsAsSeenForUser')->willThrowException(new SynapseDatabaseException());
            }

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [AlertNotificationsRepository::REPOSITORY_KEY, $mockAlertNotificationsRepository]
            ]);


            $alertNotificationService = new AlertNotificationsService($mockRepositoryResolver, $mockLogger, $mockContainer);

            try {
                $functionResult = $alertNotificationService->updateAllUnseenNotificationsAsSeenForUser($personId);
                $this->assertEquals($expectedResult, $functionResult);
            } catch (Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }



        }, ['examples' => [
            //Valid person ID
            [
                true,
                12345
            ],
            //No person ID passed, will throw exception
            [
                "A database error has occurred. Please review system logs for more information. You can override this message when throwing SynapseDatabaseException to get more useful details if needed."
            ],
            //Invalid person ID passed, will throw exception
            [
                "A database error has occurred. Please review system logs for more information. You can override this message when throwing SynapseDatabaseException to get more useful details if needed.",
                "I am most definitely not an integer"
            ]
        ]]);
    }

    public function testUpdateAllUnreadNotificationsAsReadForUser()
    {
        $this->specify("test updateAllUnreadNotificationsAsReadForUser", function($expectedResult, $personId = null){
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockAlertNotificationsRepository = $this->getMock('alertNotificationsRepository', ['updateAllUnreadNotificationsAsReadForUser']);

            if (is_int($personId)) {
                $mockAlertNotificationsRepository->method('updateAllUnreadNotificationsAsReadForUser')->willReturn(true);
            } else {
                $mockAlertNotificationsRepository->method('updateAllUnreadNotificationsAsReadForUser')->willThrowException(new SynapseDatabaseException());
            }

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [AlertNotificationsRepository::REPOSITORY_KEY, $mockAlertNotificationsRepository]
            ]);


            $alertNotificationService = new AlertNotificationsService($mockRepositoryResolver, $mockLogger, $mockContainer);

            try {
                $functionResult = $alertNotificationService->updateAllUnreadNotificationsAsReadForUser($personId);
                $this->assertEquals($expectedResult, $functionResult);
            } catch (Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        },['examples' => [
            //Valid person ID
            [
                true,
                12345
            ],
            //No person ID passed, will throw exception
            [
                "A database error has occurred. Please review system logs for more information. You can override this message when throwing SynapseDatabaseException to get more useful details if needed."
            ],
            //Invalid person ID passed, will throw exception
            [
                "A database error has occurred. Please review system logs for more information. You can override this message when throwing SynapseDatabaseException to get more useful details if needed.",
                "I am most definitely not an integer"
            ]
        ]]);
    }

    private function createPerson()
    {
        $person = new Person();
        $organization = new Organization();
        $person->setOrganization($organization);
        $person->setId(1);

        return $person;
    }

    private function createAlertNotifications($id)
    {
        $alertNotification = $this->getMock('alertNotification', ['setIsSeen', 'setIsRead', 'getId']);
        $alertNotification->method('setIsSeen')->willReturn(true);
        $alertNotification->method('setIsRead')->willReturn(true);
        $alertNotification->method('getId')->willReturn($id);

        return $alertNotification;
    }
}
