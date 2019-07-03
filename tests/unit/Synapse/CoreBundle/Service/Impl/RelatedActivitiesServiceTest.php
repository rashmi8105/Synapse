<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Entity\ActivityLog;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Email;
use Synapse\CoreBundle\Entity\Note;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Entity\RelatedActivities;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\ContactsRepository;
use Synapse\CoreBundle\Repository\EmailRepository;
use Synapse\CoreBundle\Repository\NoteRepository;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Repository\RelatedActivitiesRepository;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;

class RelatedActivitiesServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testCreateRelatedActivities()
    {
        $this->specify("Test create related activities", function ($relatedActivitiesDtoData, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array('get'));

            // Mock LoggerHelperService
            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);

            // Mock ActivityLogRepository
            $mockActivityLogRepository = $this->getMock('ActivityLogRepository', ['findById']);
            $mockActivityLog = $this->getMock('Synapse\CoreBundle\Entity\ActivityLog', ['getId']);
            $mockActivityLog->method('getId')->willReturn(2);
            $mockActivityLogRepository->method('findById')->willReturn([$mockActivityLog]);

            // Mock ContactsRepository
            $mockContactsRepository = $this->getMock('ContactsRepository', ['findById']);
            $mockContacts = $this->getMock('Synapse\CoreBundle\Entity\Contacts', ['getId']);
            $mockContacts->method('getId')->willReturn(3);
            $mockContactsRepository->method('findById')->willReturn([$mockContacts]);

            // Mock NoteRepository
            $mockNoteRepository = $this->getMock('NoteRepository', ['findById']);
            $mockNote = $this->getMock('Synapse\CoreBundle\Entity\Note', ['getId']);
            $mockNote->method('getId')->willReturn(4);
            $mockNoteRepository->method('findById')->willReturn([$mockNote]);

            // Mock AppointmentsRepository
            $mockAppointmentsRepository = $this->getMock('AppointmentsRepository', ['findById']);
            $mockAppointments = $this->getMock('Synapse\CoreBundle\Entity\Appointments', ['getId']);
            $mockAppointments->method('getId')->willReturn(5);
            $mockAppointmentsRepository->method('findById')->willReturn([$mockAppointments]);

            // Mock ReferralRepository
            $mockReferralRepository = $this->getMock('ReferralRepository', ['findById']);
            $mockReferrals = $this->getMock('Synapse\CoreBundle\Entity\Appointments', ['getId']);
            $mockReferrals->method('getId')->willReturn(6);
            $mockReferralRepository->method('findById')->willReturn([$mockAppointments]);

            // Mock EmailRepository
            $mockEmailRepository = $this->getMock('EmailRepository', ['findById']);
            $mockEmail = $this->getMock('Synapse\CoreBundle\Entity\Email', ['getId']);
            $mockEmail->method('getId')->willReturn(7);
            $mockEmailRepository->method('findById')->willReturn([$mockAppointments]);

            // Mock RelatedActivitiesRepository
            $mockRelatedActivitiesRepository = $this->getMock('RelatedActivitiesRepository', ['createRelatedActivities', 'flush']);
            $mockRelatedActivitiesRepository->method('createRelatedActivities')->willReturn($this->getRelatedActivitiesResponse($relatedActivitiesDtoData));

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                    [ActivityLogRepository::REPOSITORY_KEY, $mockActivityLogRepository],
                    [ContactsRepository::REPOSITORY_KEY, $mockContactsRepository],
                    [NoteRepository::REPOSITORY_KEY, $mockNoteRepository],
                    [AppointmentsRepository::REPOSITORY_KEY, $mockAppointmentsRepository],
                    [ReferralRepository::REPOSITORY_KEY, $mockReferralRepository],
                    [EmailRepository::REPOSITORY_KEY, $mockEmailRepository],
                    [RelatedActivitiesRepository::REPOSITORY_KEY, $mockRelatedActivitiesRepository],
                ]);

            $mockContainer->method('get')->willReturnMap([
                [LoggerHelperService::SERVICE_KEY,$mockLoggerHelperService],
            ]);

            $relatedActivitiesDto = $this->createRelatedActivitiesDto($relatedActivitiesDtoData);
            $relatedActivitiesService = new RelatedActivitiesService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $relatedActivitiesService->createRelatedActivities($relatedActivitiesDto);
            $this->assertEquals($results, $expectedResult);

        }, ['examples' => [
            // Test01 - Creating related activities for only contact
            [
                [
                    'activity_log_id' => 1,
                    'contact_id' => 112,
                    'note_id' => null,
                    'appointment_id' => null,
                    'appointment_description' => null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => null
                ],
                $this->getRelatedActivitiesResponse([
                    'activity_log_id' => 1,
                    'contact_id' => 112,
                    'note_id' => null,
                    'appointment_id' => null,
                    'appointment_description' =>null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => null
                ])
            ],
            // Test02 - Creating related activities for only note
            [
                [
                    'activity_log_id' => 2,
                    'contact_id' => null,
                    'note_id' => 113,
                    'appointment_id' => null,
                    'appointment_description' => null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => null
                ],
                $this->getRelatedActivitiesResponse([
                    'activity_log_id' => 2,
                    'contact_id' => null,
                    'note_id' => 113,
                    'appointment_id' => null,
                    'appointment_description' =>null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => null
                ])
            ],
            // Test03 - Creating related activities for only appointment
            [
                [
                    'activity_log_id' => 3,
                    'contact_id' => null,
                    'note_id' => null,
                    'appointment_id' => 114,
                    'appointment_description' => null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => null
                ],
                $this->getRelatedActivitiesResponse([
                    'activity_log_id' => 3,
                    'contact_id' => null,
                    'note_id' => null,
                    'appointment_id' => 114,
                    'appointment_description' =>null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => null
                ])
            ],
            // Test04 - Creating related activities for only referral
            [
                [
                    'activity_log_id' => 4,
                    'contact_id' => null,
                    'note_id' => null,
                    'appointment_id' => null,
                    'appointment_description' => null,
                    'referral_id' => 115,
                    'referral_note' => 'Testing referral',
                    'email_id' => null
                ],
                $this->getRelatedActivitiesResponse([
                    'activity_log_id' => 4,
                    'contact_id' => null,
                    'note_id' => null,
                    'appointment_id' => null,
                    'appointment_description' =>null,
                    'referral_id' => 115,
                    'referral_note' => 'Testing referral',
                    'email_id' => null
                ])
            ],
            // Test05 - Creating related activities for only email
            [
                [
                    'activity_log_id' => 5,
                    'contact_id' => null,
                    'note_id' => null,
                    'appointment_id' => null,
                    'appointment_description' => null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => 117
                ],
                $this->getRelatedActivitiesResponse([
                    'activity_log_id' => 5,
                    'contact_id' => null,
                    'note_id' => null,
                    'appointment_id' => null,
                    'appointment_description' =>null,
                    'referral_id' => null,
                    'referral_note' => null,
                    'email_id' => 117
                ])
            ]
        ]
        ]);
    }

    private function createRelatedActivitiesDto($relatedActivitiesDtoData)
    {
        $relatedActivitiesDto = new RelatedActivitiesDto();
        $relatedActivitiesDto->setActivityLog($relatedActivitiesDtoData['activity_log_id']);
        $relatedActivitiesDto->setContacts($relatedActivitiesDtoData['contact_id']);
        $relatedActivitiesDto->setNote($relatedActivitiesDtoData['note_id']);
        $relatedActivitiesDto->setAppointment($relatedActivitiesDtoData['appointment_id']);
        $relatedActivitiesDto->setReferral($relatedActivitiesDtoData['referral_id']);
        $relatedActivitiesDto->setEmail($relatedActivitiesDtoData['email_id']);
        return $relatedActivitiesDto;
    }

    private function getRelatedActivitiesResponse($relatedActivitiesDtoData)
    {
        $relatedActivities = new RelatedActivities();
        $activityLog = new ActivityLog();
        $activityLog->setId($relatedActivitiesDtoData['activity_log_id']);
        $relatedActivities->setActivityLog($activityLog);
        $contacts = new Contacts();
        $contacts->setId($relatedActivitiesDtoData['contact_id']);
        $relatedActivities->setContacts($contacts);
        $note = new Note();
        $note->setId($relatedActivitiesDtoData['note_id']);
        $relatedActivities->setNote($note);
        $appointments = new Appointments();
        $appointments->setDescription($relatedActivitiesDtoData['appointment_description']);
        $relatedActivities->setAppointment($appointments);
        $referral = new Referrals();
        $referral->setNote($relatedActivitiesDtoData['referral_note']);
        $relatedActivities->setReferral($referral);
        $email = new Email();
        $email->setId($relatedActivitiesDtoData['email_id']);
        $relatedActivities->setEmail($email);
        return $relatedActivities;
    }
}