<?php

namespace Synapse\CalendarBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;

class GoogleFormatServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testGetGoogleSyncData()
    {
        $this->specify("Verify the functionality of the method getGoogleSyncData", function ($type)
        {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockAppointmentRecipientAndStatusRepository = $this->getMock('AppointmentRecepientAndStatusRepository', array('getParticipantAttendeesForAppointment', 'getAppointmentFaculty'));
			
			$academicYearService = $this->getMock('AcademicYearService', array(
                'findCurrentAcademicYearForOrganization'
            ));
			
			$mockContainer->method('get')
                ->willReturnMap([                    
                    ['academicyear_service', $academicYearService],
                ]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:AppointmentRecepientAndStatus', $mockAppointmentRecipientAndStatusRepository]
                ]);

            $mockOrganization = $this->getMock('Organization', array('getId'));
            $mockOrganization->method('getId')->willReturn(203);

            $mockPerson = $this->getMock('Person', array('getId', 'getOrganization', 'getFirstname', 'getLastname'));
            $mockPerson->method('getId')->willReturn(1);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);
            $mockPerson->method('getFirstname')->willReturn('test');
            $mockPerson->method('getLastname')->willReturn('test');

            $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', array('getId', 'getPerson', 'getGoogleEmailId'));
            $mockOrgPersonFaculty->method('getPerson')->willReturn($mockPerson);
            $mockOrgPersonFaculty->method('getGoogleEmailId')->willReturn('test');

            $mockAppointments = $this->getMock('Appointments', array('getId', 'getLocation', 'getStartDateTime', 'getEndDateTime', 'getTitle', 'getSlotStart', 'getSlotEnd'));
            $mockAppointments->method('getId')->willReturn(1);
            $mockAppointments->method('getLocation')->willReturn('test');
            $mockAppointments->method('getStartDateTime')->willReturn('test');
            $mockAppointments->method('getEndDateTime')->willReturn('test');
            $mockAppointments->method('getTitle')->willReturn('test');
            $mockAppointments->method('getSlotStart')->willReturn('test');
            $mockAppointments->method('getSlotEnd')->willReturn('test');

            $googleFormatService = new GoogleFormatService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $googleFormatService->getGoogleSyncData($mockAppointments, $mockOrgPersonFaculty, $type);
            $this->assertNotEmpty($result);
            $this->assertEquals('array', gettype($result));

        }, [
            'examples' => [
                ['appointment'],
                ['officehour'],
                ['officehourseries']
            ]
        ]);
    }

    public function testGenerateRedirectionURL()
    {
        $this->specify("Verify the functionality of the method generateRedirectionURL", function ($personId, $id, $type)
        {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $mockPersonRepository = $this->getMock('PersonRepository', array('find', 'getUsername', 'getOrganization'));

            $mockEbiConfigService = $this->getMock('EbiConfigService', array('get', 'generateCompleteUrl'));
            $mockUtilServiceHelper = $this->getMock('UtilServiceHelper', array('encrypt'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:Person', $mockPersonRepository]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    ['ebi_config_service', $mockEbiConfigService],
                    ['util_service', $mockUtilServiceHelper]
                ]);

            $mockEbiConfigService->method('get')->willReturn('test');
            $mockUtilServiceHelper->method('encrypt')->willReturn('test');

            $mockOrganization = $this->getMock('Organization', array('getId', 'getIsLdapSamlEnabled'));
            $mockOrganization->method('getId')->willReturn(203);
            $mockOrganization->method('getIsLdapSamlEnabled')->willReturn(0);

            $mockPerson = $this->getMock('Person', array('getId', 'getUsername', 'getOrganization'));
            $mockPerson->method('getId')->willReturn(1);
            $mockPerson->method('getUsername')->willReturn('test');
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);

            $mockPersonRepository->method('find')->willReturn($mockPerson);

            $googleFormatService = new GoogleFormatService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $googleFormatService->generateRedirectionURL($personId, $id, $type);
            $this->assertNotEmpty($result);
            $this->assertEquals('string', gettype($result));

        }, [
            'examples' => [
                [4891668, 1, 'appointment'],
                [4901835, 1, 'appointment']
            ]
        ]);
    }

    public function testFormatDataToSync()
    {
        $this->specify("Verify the functionality of the method FormatDataToSync", function ($personId, $eventType, $action, $gmailId, $location, $startDate, $endDate, $subject, $expectedResults) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));

            $calendarSettings['googleClientId'] = $gmailId;
            $mockAppointment = $this->getMock('AppointmentsRepository', array('getOrganization', 'getId', 'getGoogleAppointmentId', 'getLocation', 'getSlotStart', 'getSlotEnd', 'getStartDateTime', 'getEndDateTime', 'getTitle'));
            $mockOrganization = $this->getMock('OrganizationRepository', array('getId'));
            $mockAppointmentRecipientAndStatusRepository = $this->getMock('AppointmentRecepientAndStatusRepository', array('getParticipantAttendeesForAppointment', 'getAppointmentFaculty'));
            $mockPerson = $this->getMock('PersonRepository', array('getId', 'getOrganization', 'getLastname', 'getFirstname'));

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [AppointmentsRepository::REPOSITORY_KEY, $mockAppointment],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganization],
                    [AppointmentRecepientAndStatusRepository::REPOSITORY_KEY, $mockAppointmentRecipientAndStatusRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPerson],
                ]
            );
            $organizationId = 203;
            $mockPerson->expects($this->any())->method('getId')->willReturn($personId);
            $mockAppointment->expects($this->any())->method('getLocation')->willReturn($location);
            $mockAppointment->expects($this->any())->method('getSlotStart')->willReturn($startDate);
            $mockAppointment->expects($this->any())->method('getSlotEnd')->willReturn($endDate);
            $mockAppointment->expects($this->any())->method('getStartDateTime')->willReturn($startDate);
            $mockAppointment->expects($this->any())->method('getEndDateTime')->willReturn($endDate);
            $mockAppointment->expects($this->any())->method('getTitle')->willReturn($subject);

            $academicYearService = $this->getMock('AcademicYearService', array(
                'findCurrentAcademicYearForOrganization'
            ));
            $mockContainer->method('get')
                ->willReturnMap([
                    [AcademicYearService::SERVICE_KEY, $academicYearService],
                ]);

            $googleFormatService = new GoogleFormatService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $googleFormatService->formatDataToSync($eventType, $action, $calendarSettings, $mockAppointment, $mockPerson, $organizationId);
            $this->assertNotEmpty($result);
            $this->assertEquals('array', gettype($result));
            $this->assertEquals($result, $expectedResults);

        }, [
            'examples' => [
                [4878827, 'officehour', 'create', 'Dallas@gmail.com', 'Mapworks', '2016-12-24 10:00:00', '2016-12-24 10:30:00', 'Student Meeting', ['eventType' => 'officehour',
                    'personId' => 4878827,
                    'orgId' => 203,
                    'crudType' => 'create',
                    'calendarSettings' => [
                        'googleClientId' => 'Dallas@gmail.com'
                    ],
                    'primaryAttendee' => [
                        'email' => 'Dallas@gmail.com',
                        'displayName' => ' '
                    ],
                    'mafInputDto' => [
                        'location' => 'Mapworks',
                        'startTime' => '2016-12-24 10:00:00',
                        'endTime' => '2016-12-24 10:30:00',
                    ]
                ]
                ],
                [4878830, 'appointment', 'update', 'Clayton@gmail.com', 'Skyfactor', '2016-12-23 10:00:00', '2016-12-23 10:30:00', 'Student Meeting', ['eventType' => 'appointment',
                    'personId' => 4878830,
                    'orgId' => 203,
                    'crudType' => 'update',
                    'calendarSettings' => [
                        'googleClientId' => 'Clayton@gmail.com'
                    ],
                    'primaryAttendee' => [
                        'email' => 'Clayton@gmail.com',
                        'displayName' => ' '
                    ],
                    'mafInputDto' => [
                        'location' => 'Skyfactor',
                        'startTime' => '2016-12-23 10:00:00',
                        'endTime' => '2016-12-23 10:30:00',
                        'subject' => 'Student Meeting'
                    ],
                    'attendees' => [],
                ]
                ]
            ]
        ]);
    }

}