<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\NoteRepository;
use Synapse\CoreBundle\Repository\NoteTeamsRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\NotesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamsDto;

class NotesServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testCreateNotes()
    {
        $this->specify("Test create notes", function ($isJob, $isOrganizationLangAvailable, $haveAccessForNotes, $isNotesCreated, $notesData, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockAlertNotificationService = $this->getMock('AlertNotificationsService', ['createNotification']);
            $mockActivityLogService = $this->getMock('activityLogService', ['createActivityLog']);
            $mockFeatureService = $this->getMock('FeatureService', ['verifyFacultyAccessToStudentForFeature']);
            $mockManagerService = $this->getMock('Manager', ['assertPermissionToEngageWithStudents']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['find', 'getOrganizationDetailsLang']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockRelatedActivitiesService = $this->getMock('RelatedActivitiesService', ['createRelatedActivities']);
            $mockResque = $this->getMock('resque', ['enqueue']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [ActivityLogService::SERVICE_KEY, $mockActivityLogService],
                    [AlertNotificationsService::SERVICE_KEY, $mockAlertNotificationService],
                    [FeatureService::SERVICE_KEY, $mockFeatureService],
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService],
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [RelatedActivitiesService::SERVICE_KEY, $mockRelatedActivitiesService],
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque]
                ]);

            $mockActivityCategoryRepository = $this->getMock('ActivityCategoryRepository', ['find', 'getShortName']);
            $mockActivityCategoryLangRepository = $this->getMock('ActivityCategoryLangRepository', ['findOneBy']);
            $mockFeatureMasterLangRepository = $this->getMock('FeatureMasterLangRepository', ['findOneBy']);
            $mockNotesRepository = $this->getMock('NoteRepository', ['createNote', 'flush']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                    [ActivityCategoryLangRepository::REPOSITORY_KEY, $mockActivityCategoryLangRepository],
                    [FeatureMasterLangRepository::REPOSITORY_KEY, $mockFeatureMasterLangRepository],
                    [NoteRepository::REPOSITORY_KEY, $mockNotesRepository]
                ]);

            if ($notesData['reasonCategoryItem']) {
                $mockActivityCategory = $this->getMock('ActivityCategory', ['getShortName']);
                $mockActivityCategoryRepository->method('find')->willReturn($mockActivityCategory);
            }
            if ($isOrganizationLangAvailable) {
                $mockOrganizationLangService = $this->getMock('OrganizationLang', ['getLang', 'getId']);
                $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLangService);
            }
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganizationService->method('find')->willReturn($mockOrganization);
            $mockPersonService->method('findPerson')->willReturn(new Person());
            $mockFeatureService->method('verifyFacultyAccessToStudentForFeature')->willReturn($haveAccessForNotes);

            $mockFeatureMasterLang = $this->getMock('FeatureMasterLang', ['getId']);
            $mockFeatureMasterLangRepository->method('findOneBy')->willReturn($mockFeatureMasterLang);
            if ($isNotesCreated) {
                $mockNotes = $this->getMock('Notes', ['getId', 'getOrganization', 'getModifiedAt']);
                $mockOrganization = $this->getMock('Organization', ['getId']);
                $mockNotesRepository->method('createNote')->willReturn($mockNotes);
                $mockNotes->method('getOrganization')->willReturn($mockOrganization);
            }
            $notesDto = $this->createNotesDTO($notesData);
            try {
                $notesService = new NotesService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $notesService->createNote($notesDto, $isJob);
                $this->assertEquals($results, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // Passing $isJob as true to execute it through job.
                [
                    true,
                    true,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    $this->createNotesDTO([
                            'reasonCategoryItem' => 12,
                            'studentId' => '456734, 99878',
                            'organizationId' => 203,
                            'activityId' => 18,
                            'comment' => 'notes'
                        ]
                    )
                ],
                // Create notes for more than one student which should be executed through job
                [
                    false,
                    true,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => '456734, 84552, 56421',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    $this->createNotesDTO([
                            'reasonCategoryItem' => 12,
                            'studentId' => '456734, 84552, 56421',
                            'organizationId' => 203,
                            'activityId' => 18,
                            'comment' => 'notes'
                        ]
                    )
                ],
                // Create Notes where reason category is not found.
                [
                    false,
                    false,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => NULL,
                        'studentId' => 456734,
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],

                    'Reason category not found.'
                ],
                // create notes where when organization language set up was not done.
                [
                    false,
                    false,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => 456734,
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],

                    'Organization language not found.'
                ],
                // Faculty created notes when he has no permission for that.
                [
                    false,
                    true,
                    false,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => 456734,
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    'You do not have permission to create a note'
                ],
                // Notes creation failed with error.
                [
                    false,
                    true,
                    true,
                    false,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => 456734,
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    'Note not created.'
                ],
                // create a notes.
                [
                    false,
                    true,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => 456734,
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    $this->createNotesDTO([
                            'reasonCategoryItem' => 12,
                            'studentId' => 456734,
                            'organizationId' => 203,
                            'activityId' => 18,
                            'comment' => 'notes'
                        ]
                    )
                ],
            ]
        ]);
    }

    public function testEditNotes()
    {
        $this->specify("Test to edit notes", function ($noteId, $hasPermission, $isStaffAvailable, $isOrganizationLangAvailable, $notesData, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockManagerService = $this->getMock('Manager', ['hasAssetAccess', 'assertPermissionToEngageWithStudents']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['find', 'getOrganizationDetailsLang']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService],
                    [PersonService::SERVICE_KEY, $mockPersonService]
                ]);

            $mockActivityCategoryRepository = $this->getMock('ActivityCategoryRepository', ['find']);
            $mockNotesRepository = $this->getMock('NoteRepository', ['find', 'flush', 'getNoteTeamIds']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                    [NoteRepository::REPOSITORY_KEY, $mockNotesRepository]
                ]);

            if ($noteId) {
                $mockNotes = $this->getMock('Notes', array('getId', 'getOrganization', 'setActivityCategory', 'setPersonIdStudent', 'setPersonIdFaculty', 'setOrganization', 'setNote',
                    'setAccessPrivate', 'setAccessPublic', 'setAccessTeam'));
                $mockNotesRepository->method('find')->willReturn($mockNotes);
            }
            $mockNotesRepository->method('getNoteTeamIds')->willReturn([]);
            $mockManagerService->method('hasAssetAccess')->willReturn($hasPermission);
            $notesDto = $this->createNotesDTO($notesData);
            if ($isStaffAvailable) {
                $mockStaff = $this->getMock('Person', ['getId', 'getOrganization']);
                $mockPersonService->method('findPerson')->willReturn($mockStaff);
            }
            if ($notesDto->getReasonCategorySubitemId()) {
                $mockActivityCategoryRepository->method('find')->willReturn($notesDto->getReasonCategorySubitemId());
            }
            if ($isOrganizationLangAvailable) {
                $mockOrganizationLangService = $this->getMock('OrganizationLang', ['getLang', 'getId']);
                $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLangService);
            }
            try {
                $notesService = new NotesService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $notesService->editNote($notesDto);
                $this->assertEquals($results, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // Passing invalid note id should throw exception.
                [
                    '',
                    true,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    'Note Not Found.'
                ],
                // Faculty has no permission which throws an exception.
                [
                    84,
                    false,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    'note'
                ],
                // Faculty not found
                [
                    45,
                    true,
                    false,
                    true,
                    [
                        'reasonCategoryItem' => 12,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    'Staff Not Found.'
                ],
                // Reason category not found for editing the notes.
                [
                    56,
                    true,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => false,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    'Reason category not found.'
                ],
                // Exception while editing notes that Organization lang not found.
                [
                    125,
                    true,
                    true,
                    false,
                    [
                        'reasonCategoryItem' => 15,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    'Organization language not found.'
                ],
                // Edit a notes.
                [
                    458,
                    true,
                    true,
                    true,
                    [
                        'reasonCategoryItem' => 15,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ],
                    $this->createNotesDTO([
                        'reasonCategoryItem' => 15,
                        'studentId' => '456734, 99878',
                        'organizationId' => 203,
                        'activityId' => 18,
                        'comment' => 'notes'
                    ])
                ],
            ]
        ]);

    }

    public function testDeleteNotes()
    {
        $this->specify("Test to delete notes", function ($noteId, $hasPermission, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockActivityLogService = $this->getMock('activityLogService', ['deleteActivityLogByType']);
            $mockManagerService = $this->getMock('Manager', ['hasAssetAccess', 'assertPermissionToEngageWithStudents']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [ActivityLogService::SERVICE_KEY, $mockActivityLogService],
                    [Manager::SERVICE_KEY, $mockManagerService]
                ]);

            $mockNotesRepository = $this->getMock('NoteRepository', ['find', 'flush', 'remove']);
            $mockNoteTeamsRepository = $this->getMock('NoteTeamsRepository', ['findBy']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [NoteTeamsRepository::REPOSITORY_KEY, $mockNoteTeamsRepository],
                    [NoteRepository::REPOSITORY_KEY, $mockNotesRepository]
                ]);

            $mockNotes = $this->getMock('Notes', ['getId', 'getOrganization', 'getPersonIdStudent']);
            $mockNotesRepository->method('find')->willReturn($mockNotes);
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
            $mockNotes->method('getPersonIdStudent')->willReturn($mockPerson);
            $mockManagerService->method('hasAssetAccess')->willReturn($hasPermission);

            try {
                $notesService = new NotesService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $notesService->deleteNote($noteId);
                $this->assertEquals($results, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // passing invalid note id
                [
                    '',
                    true,
                    ''
                ],
                // No permission to delete a note should throw exception
                [
                    '',
                    false,
                    'note'
                ],
                // Delete a notes
                [
                    154,
                    true,
                    154
                ],
            ]
        ]);

    }

    public function testGetNotes()
    {
        $this->specify("Test to get notes", function ($noteId, $isNoteAvailable, $hasPermission, $isOrganizationLangAvailable, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockActivityLogService = $this->getMock('activityLogService', ['deleteActivityLogByType']);
            $mockManagerService = $this->getMock('Manager', ['hasAssetAccess', 'assertPermissionToEngageWithStudents']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['find', 'getOrganizationDetailsLang']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [ActivityLogService::SERVICE_KEY, $mockActivityLogService],
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService]
                ]);

            $mockActivityCategoryRepository = $this->getMock('ActivityCategoryRepository', ['find']);
            $mockActivityCategoryLangRepository = $this->getMock('ActivityCategoryLangRepository', ['findOneBy']);
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getTeams']);
            $mockNotesRepository = $this->getMock('NoteRepository', ['find', 'flush', 'remove', 'getNoteTeamIds']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ActivityCategoryLangRepository::REPOSITORY_KEY, $mockActivityCategoryLangRepository],
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                    [NoteRepository::REPOSITORY_KEY, $mockNotesRepository],
                    [TeamMembersRepository::REPOSITORY_KEY, $mockTeamMembersRepository
                    ]
                ]);

            $mockNotes = $this->getMock('Notes', ['getId', 'getPersonIdStudent', 'getAccessPublic', 'getAccessPrivate', 'getActivityCategory', 'getOrganization', 'getPersonIdFaculty',
                'getNote', 'getModifiedAt', 'getAccessTeam']);
            if ($isNoteAvailable) {
                $mockNotesRepository->method('find')->willReturn($mockNotes);
            }
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockNotes->method('getPersonIdStudent')->willReturn($mockPerson);
            $mockNotes->method('getPersonIdFaculty')->willReturn($mockPerson);
            $mockNotes->method('getOrganization')->willReturn($mockOrganization);
            $mockNotes->method('getId')->willReturn($noteId);

            $mockOrganizationLangService = $this->getMock('OrganizationLang', ['getLang', 'getId']);
            $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLangService);
            $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
            if ($isOrganizationLangAvailable) {
                $mockOrganizationLangService->method('getLang')->willReturn($mockLanguageMaster);
            }
            $mockNotesRepository->method('getNoteTeamIds')->willReturn([]);
            $mockManagerService->method('hasAssetAccess')->willReturn($hasPermission);
            try {
                $notesService = new NotesService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $notesService->getNotes($noteId);
                $this->assertEquals($results->getNotesId(), $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // Passing invalid note id.
                [
                    'invalid id',
                    false,
                    true,
                    true,
                    'Note Not Found.'
                ],
                // Passing empty note id.
                [
                    'invalid id',
                    false,
                    true,
                    true,
                    'Note Not Found.'
                ],
                // No permission to view the notes.
                [
                    452,
                    true,
                    false,
                    true,
                    'note'
                ],
                // Organization language not found.
                [
                    452,
                    true,
                    true,
                    false,
                    'Organization language not found.'
                ],
                // view notes where the id is 452
                [
                    452,
                    true,
                    true,
                    true,
                    452,
                ],
            ]
        ]);

    }

    /**
     * Creates notes DTO
     *
     * @param array $notes
     * @return NotesDto
     */
    private function createNotesDTO($notes)
    {
        $reasonCategoryItem = $notes['reasonCategoryItem'];
        $studentId = $notes['studentId'];
        $organizationId = $notes['organizationId'];
        $activityId = $notes['activityId'];
        $comment = $notes['comment'];
        $notesDto = new NotesDto();
        $notesDto->setReasonCategorySubitemId($reasonCategoryItem);
        $notesDto->setNotesStudentId($studentId);
        $notesDto->setOrganization($organizationId);
        $notesDto->setActivityLogId($activityId);
        $notesDto->setStaffId(2345);
        if (isset($notes['notesId'])) {
            $notesDto->setNotesId($notes['notesId']);
        }
        $notesDto->setComment($comment);
        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPublicShare(false);
        $shareOptionsDto->setPrivateShare(false);
        $shareOptionsDto->setTeamsShare(false);
        $teamsDto = new TeamsDto();
        $teamsDto->setTeamId(1);
        $shareOptionsDto->setTeamIds($teamsDto);
        $shareOptionsDtoResponse[] = $shareOptionsDto;
        $notesDto->setShareOptions($shareOptionsDtoResponse);
        return $notesDto;
    }
}