<?php

namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CalendarBundle\Service\Impl\CronofyCalendarService;
use Synapse\CalendarBundle\Service\Impl\CronofyWrapperService;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;

class AppointmentsServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $personId = 1;

    private $organizationId = 1;


    public function testCheckExternalCalendarForAppointmentConflict()
    {
        $this->specify("Test function checkExternalCalendarForAppointmentConflict", function ($startDateTime, $endDateTime, $freeBusyEvents, $expectedOutput) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            // Repository Mocks
            $mockOrgPersonFacultyRepository = $this->getMock("OrgPersonFacultyRepository", ['findOneBy']);
            $mockAppointmentsRepository = $this->getMock("AppointmentsRepository", ['isOverlappingAppointments']);

            // Service Mocks
            $mockCronofyWrapperService = $this->getMock("CronofyWrapperService", ["getFreeBusyEventsForConflictValidation"]);
            $mockCronofyCalendarService = $this->getMock("CronofyCalendarService", ["getHandleResponse"]);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgPersonFacultyRepository::REPOSITORY_KEY,
                        $mockOrgPersonFacultyRepository
                    ],
                    [
                        AppointmentsRepository::REPOSITORY_KEY,
                        $mockAppointmentsRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        CronofyWrapperService::SERVICE_KEY,
                        $mockCronofyWrapperService
                    ],
                    [
                        CronofyCalendarService::SERVICE_KEY,
                        $mockCronofyCalendarService
                    ]
                ]);

            $mockOrgPersonFacultyRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($this->getOrgPersonFacultyEntity()));


            $mockBusyEvents = $this->getMock('CronofyCalendarService', array('getFreeBusyEvents', 'firstPage'));
            $events = [
                "pages" =>
                    [
                        "current" => 1,
                        "total" => 1
                    ],

                "free_busy" => $freeBusyEvents
            ];
            $mockBusyEvents->firstPage = $events;
            $mockCronofyWrapperService->method('getFreeBusyEventsForConflictValidation')->willReturn($mockBusyEvents);

            $appointmentsService = new AppointmentsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $appointmentsService->checkExternalCalendarForAppointmentConflict($this->createAppointmentsDto($startDateTime, $endDateTime), $this->personId, $this->organizationId);

            $this->assertInternalType("boolean", $results);
            $this->assertEquals($results, $expectedOutput);

        }, [
            'examples' => [
                [
                    "2017-01-27T10:00:00+0530",
                    "2017-01-27T11:00:00+0530",
                    [[
                        "calendar_id" => "cal_V6gV6WjRXRUOAL7r_5iNV3MeIW@71Xmq@fU3G2A",
                        "start" =>
                            [
                                "time" => "2017-01-27T10:00:00+05:30",
                                "tzid" => "Asia/Kolkata"
                            ],

                        "end" =>
                            [
                                "time" => "2017-01-27T11:00:00+05:30",
                                "tzid" => "Asia/Kolkata"
                            ],

                        "free_busy_status" => "busy"
                    ]],
                    true
                ],
                [
                    "2017-01-31T10:00:00+0530",
                    "2017-01-31T11:00:00+0530",
                    [],
                    false
                ]
            ]
        ]);
    }

    private function createAppointmentsDto($startDateTime, $endDateTime)
    {
        $appointmentsDto = new AppointmentsDto();
        $appointmentsDto->setSlotStart(new \DateTime($startDateTime));
        $appointmentsDto->setSlotEnd(new \DateTime($endDateTime));
        return $appointmentsDto;
    }

    private function getOrgPersonFacultyEntity()
    {
        $orgPersonFaculty = new OrgPersonFaculty();
        $orgPersonFaculty->setPcsToMafIsActive("y");
        return $orgPersonFaculty;
    }


    public function testCancelStudentAppointments()
    {

        $this->specify("Test cancel Student appointments", function ($studentId, $currentDate, $appointmentsArray, $expectedResult) {
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


            $mockOrgPersonStudentYearRepositoryMock = $this->getMock('OrgPersonStudentYearRepository', ['findOneBy']);
            $mockOrgPersonStudentYearRepositoryMock->method('findOneBy')->willReturn(1);


            $mockAcademicYearService = $this->getMock("AcademicYearService", ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);


            $mockAppointmentsAndRecipientRepository = $this->getMock('AppointmentRecipientAndStatusRepository', ['getStudentsUpcomingAppointments']);
            $mockAppointmentsAndRecipientRepository->method('getStudentsUpcomingAppointments')->willReturn($appointmentsArray);

            $mockStudentAppointmentService = $this->getMock('StudentAppointmentService', ['cancelStudentAppointment']);
            $mockStudentAppointmentService->method('cancelStudentAppointment')->willReturnCallback(function ($studentId, $appointmentId) {
                return $appointmentId;
            });


            $mockJobService = $this->getMock('JobService', ['addJobToQueue']);
            $mockJobService->method('addJobToQueue')->willReturn(1);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        AppointmentRecepientAndStatusRepository::REPOSITORY_KEY,
                        $mockAppointmentsAndRecipientRepository

                    ],
                    [
                        OrgPersonStudentYearRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentYearRepositoryMock
                    ]

                ]);

            $mockContainer->method('get')->willReturnMap(
                [
                    [
                        StudentAppointmentService::SERVICE_KEY,
                        $mockStudentAppointmentService
                    ]
                ]);

            $studentAppointmentService = new AppointmentsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $studentAppointmentService->cancelStudentAppointments($studentId, $currentDate);
            $this->assertEquals($result, $expectedResult);
        }, [

                'examples' => [
                    // no appointments ro be cancelled
                    [
                        1, "2016-10-10", [], []
                    ],
                    //  two appointments to be cancelled
                    [
                        1, "2016-10-10", [

                        [
                            'appointment_id' => 1,
                            'location' => "somewhere"
                        ],
                        [
                            'appointment_id' => 2,
                            'location' => "somewhere"
                        ],
                    ], [1, 2]
                    ]
                ]
            ]
        );

    }


}