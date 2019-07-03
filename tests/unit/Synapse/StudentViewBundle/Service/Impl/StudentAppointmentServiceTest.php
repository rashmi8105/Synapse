<?php

use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\CalendarSharingRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\NotificationChannelService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;


class StudentAppointmentServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testCancelStudentAppointment()
    {
        $this->specify("Cancel Appointment by student", function ($studentId, $appointmentId, $timeZoneName) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockRASRepo = $this->getMock('recepientPerson', array('findOneBy', 'getPersonIdFaculty', 'remove', 'flush', 'getParticipantAttendeesForAppointment'));

            $mockEbiConfigRepository = $this->getMock('ebiConfigRepository', array('findOneByKey'));
            $mockEmailTemplateRepository = $this->getMock('emailTemplateLangRepository', array('getEmailTemplateByKey'));
            $mockAppointmentRepository = $this->getMock('Appointments', array('find'));
            $mockCalendarSharingRepository = $this->getMock('CalendarSharingRepository', array('getSelectedProxyUsers'));
            /*
             * Mock Appointment
             */
            $mockAppointment = $this->getMock("Appointments", array('getPerson', 'getOrganization', 'getStartDateTime', 'getEndDateTime', 'getActivityCategory', 'getShortName', 'getGoogleAppointmentId', 'getId', 'getLocation', 'getTitle'));
            $mockAppointmentRepository->expects($this->once())->method('find')->will($this->returnValue($mockAppointment));
            /*
             * Organization Mock
             */
            $mockOrganization = $this->getMock("Organization", array('getId', 'find', 'getTimeZone'));
            $mockAlertNotificationService = $this->getMock('alertnotification', array('createNotification'));
            $personFaculty = $this->getMock('PersonFaculty', array('findPerson', 'getId'));
            $mockRASRepo->method('getPersonIdFaculty')->willReturn($personFaculty);

            $mockPersonService = $this->getMock('Person', array('findPerson'));
            $personStudent = $this->getMock('PersonStudent', array('findPerson', 'getId', 'getLastname', 'getFirstname', 'getLocation'));
            $mockPersonService->method('findPerson')->willReturn($personStudent);

            $mockAppointment->method('getOrganization')->willReturn($mockOrganization);
            $mockEbiConfigService = $this->getMock('EbiConfigService', array('getSystemUrl','generateCompleteUrl'));
            $mockDateUtilityService = $this->getMock('dateUtilityService', ['adjustDateTimeToOrganizationTimezone', 'getTimezoneAdjustedCurrentDateTimeForOrganization']);
			
            $orgService = $this->getMock('OrganizationService', array(
                'find', 'getTimeZone', 'getOrganizationDetailsLang'
            ));
            $mockCalendarIntegrationService = $this->getMock('calendarIntegrationService', array('facultyCalendarSettings', 'syncOneOffEvent'));

            $mockMetaList = $this->getMock('metadataListValues', array(
                'findByListName'
            ));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [AppointmentsRepository::REPOSITORY_KEY, $mockAppointmentRepository],
                    [AppointmentRecepientAndStatusRepository::REPOSITORY_KEY, $mockRASRepo],
                    [CalendarSharingRepository::REPOSITORY_KEY, $mockCalendarSharingRepository],
                    [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository],
                    [EmailTemplateLangRepository::REPOSITORY_KEY, $mockEmailTemplateRepository],
                    [MetadataListValuesRepository::REPOSITORY_KEY, $mockMetaList],
                    [PersonRepository::REPOSITORY_KEY, $mockAppointmentRepository]
                ]);

            $academicYearService = $this->getMock('AcademicYearService', array(
                'findCurrentAcademicYearForOrganization'
            ));

            $mockNotificationChannelService = $this->getMock('NotificationChannelService', array('sendNotificationToAllRegisteredChannels'));
            $mockContainer->method('get')
                ->willReturnMap([
                    [AcademicYearService::SERVICE_KEY, $academicYearService],
                    [AlertNotificationsService::SERVICE_KEY, $mockAlertNotificationService],
                    [CalendarIntegrationService::SERVICE_KEY, $mockCalendarIntegrationService],
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [NotificationChannelService::SERVICE_KEY, $mockNotificationChannelService],
                    [OrganizationService::SERVICE_KEY, $orgService],
                    [PersonService::SERVICE_KEY, $mockPersonService]
                ]);
            
            $mockCalendarSharingRepository->method('getSelectedProxyUsers')->willReturn([]);
            $mockEbiConfigService->expects($this->at(0))->method('getSystemUrl')->willReturn('http://publishing.mapworks.com');
            $mockEbiConfigService->expects($this->at(0))->method('generateCompleteUrl')->willReturn('http://subdomain.mapworks.com');
            $mockDateTimeObject = new \DateTime('now');
            $mockDateUtilityService->method('getTimezoneAdjustedCurrentDateTimeForOrganization')->willReturn($mockDateTimeObject);
            $mockDateUtilityService->method('adjustDateTimeToOrganizationTimezone')->willReturn(new \DateTime('now'));
            /*
             * Current DateTime for an appointment
             */
            $currentNow = $this->getMockBuilder('\DateTime')
                ->setMethods(array(
                    '__construct'
                ))
                ->setConstructorArgs(array(
                    'now',
                    new \DateTimeZone($timeZoneName)
                ))
                ->getMock();
            /*
             * Appointment end date
             */
            $endDate = $currentNow->modify('+1 week');
            $mockAppointment->method('getStartDateTime')->willReturn($currentNow);
            $mockAppointment->method('getEndDateTime')->willReturn($endDate);
            $mockRSAStatus = $this->getMock('appointmentsRAStatusRepository', array(
                'findOneBy', 'getPersonIdFaculty', 'remove', 'flush'
            ));
            $mockRASRepo->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockRSAStatus));
            $mockRepositoryResolver->expects($this->any())->method('findOneBy')->with('')->will($this->returnValue($mockAppointment));
            $mockAlertNotificationService->method('createNotification')->willReturn('');
            $activityMock = $this->getMock('Appointments', array('getActivityCategory', 'getShortName'));
            $mockAppointment->method('getActivityCategory')->willReturn($activityMock);
            $orgnizationLang = $this->getMock('orgnizationLang', array(
                'getOrganizationDetailsLang', 'getLang'
            ));
            $orgService->expects($this->at(0))
                ->method('getOrganizationDetailsLang')
                ->willReturn($orgnizationLang);
            /*
             * OrganizationLang Mock
             */
            $langMock = $this->getMock('\Synapse\CoreBundle\Entity\LanguageMaster', [
                'getId', 'getLang'
            ]);
            $langMock->method('getId')
                ->willReturn(1);

            $orgnizationLang->expects($this->at(0))->method('getLang')
                ->willReturn($langMock);

            $studentAppointmentService = new StudentAppointmentService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $cancelAppointment = $studentAppointmentService->cancelStudentAppointment($studentId, $appointmentId);
            $this->assertEquals($cancelAppointment, $appointmentId);
        }, [
            'examples' => [
                [
                    2916, 131506, 'Canada/Eastern',
                    3822, 105812, 'Asia/Kolkata',
                    3708, 4808286, 'Canada/Mountain'
                ]
            ]
        ]);
    }

}

?>