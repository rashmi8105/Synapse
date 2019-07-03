<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Note;
use Synapse\CoreBundle\Entity\NoteTeams;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\job\BulkNoteJob;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\NoteRepository;
use Synapse\CoreBundle\Repository\NoteTeamsRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\PermissionConstInterface;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Entity\NotesDto;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("notes_service")
 */
class NotesService extends AbstractService implements PermissionConstInterface
{

    const SERVICE_KEY = 'notes_service';

    const REASON_CATEGORY_NOT_FOUND = 'Reason category not found.';

    const ORG_LANG_NOT_FOUND = 'Organization language not found.';

    const NOTE_NOT_FOUND = 'Note Not Found.';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const NOTE_TEAM_REPO = "SynapseCoreBundle:NoteTeams";

    const ERROR_ORG_NOT_FOUND_KEY = "organization_language_not_found";

    const ERROR_NOTE_NOT_FOUND_KEY = "Note_not_found";


    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    // Services

    /**
     * @var ActivityLogService
     */
    private $activityLogService;

    /**
     * @var FeatureService
     */
    private $featureService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var RelatedActivitiesService
     */
    private $relatedActivitiesService;

    // Repositories

    /**
     * @var  ActivityCategoryRepository
     */
    private $activityCategoryRepository;

    /**
     * @var ActivityCategoryLangRepository
     */
    private $activityCategoryLangRepository;

    /**
     * @var FeatureMasterLangRepository
     */
    private $featureMasterLangRepository;

    /**
     * @var NoteRepository
     */
    private $noteRepository;

    /**
     * @var NoteTeamsRepository
     */
    private $noteTeamsRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionsetFeaturesRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var TeamsRepository
     */
    private $teamsRepository;

    /**
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;


    /**
     * NotesService constructor.
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
	 *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //scafolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        // Repositories
        $this->activityCategoryLangRepository = $this->repositoryResolver->getRepository(ActivityCategoryLangRepository::REPOSITORY_KEY);
        $this->activityCategoryRepository = $this->repositoryResolver->getRepository(ActivityCategoryRepository::REPOSITORY_KEY);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(FeatureMasterLangRepository::REPOSITORY_KEY);
        $this->noteRepository = $this->repositoryResolver->getRepository(NoteRepository::REPOSITORY_KEY);
        $this->noteTeamsRepository = $this->repositoryResolver->getRepository(NoteTeamsRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(OrgPermissionsetFeaturesRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamMembersRepository::REPOSITORY_KEY);

        // Services
        $this->activityLogService = $this->container->get(ActivityLogService::SERVICE_KEY);
        $this->featureService = $this->container->get(FeatureService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->relatedActivitiesService = $this->container->get(RelatedActivitiesService::SERVICE_KEY);
    }

    /**
     * Used for creating note for students
     * @param NotesDto $notesDto
     * @param bool $isJob
     * @return NotesDto
     */
    public function createNote(NotesDto $notesDto, $isJob = false)
    {
        //Create notes for multiple students
        $personStudentIds = $notesDto->getNotesStudentId();
        $personStudentIds = explode(',', $personStudentIds);

        $staffId = $notesDto->getStaffId();

        $this->rbacManager->assertPermissionToEngageWithStudents($personStudentIds, $staffId);
        if ($isJob || (count($personStudentIds) == 1)) {
            $this->createNotes( $notesDto);
        } else {
            $this->scheduleJob( $notesDto);
        }
        return $notesDto;
    }

    private function scheduleJob(NotesDto $notesDto) {
        
    	$job = new BulkNoteJob();
    
    	$jobNumber = uniqid();
    
    	$resque = $this->container->get('bcc_resque.resque');
    	$job->args = array(
    			'jobNumber' => $jobNumber,
    			'noteDto' => serialize($notesDto)
    	);
    	    	
        $resque->enqueue($job, true);
    }

    /**
     * Creates note for students
     * @param NotesDto $notesDto
     */
    private function createNotes(NotesDto $notesDto)
    {
        $personStudentIds = $notesDto->getNotesStudentId();
        $personStudentIds = explode(',', $personStudentIds);

        $personStaff = $this->personService->findPerson($notesDto->getStaffId());
        $organization = $this->orgService->find($personStaff->getOrganization());

        $activityCategory = $this->activityCategoryRepository->find($notesDto->getReasonCategorySubitemId());

        $this->isExists($activityCategory, 'reason_category_not_found', self::REASON_CATEGORY_NOT_FOUND);
        $orgLang = $this->orgService->getOrganizationDetailsLang($personStaff->getOrganization());
        $this->isExists($orgLang, self::ERROR_ORG_NOT_FOUND_KEY, self::ORG_LANG_NOT_FOUND);

        $actCatName = $this->activityCategoryLangRepository->findOneBy(array(
            'activityCategoryId' => $activityCategory,
            'language' => $orgLang
        ));

        $shareOptionPermission = $this->getShareOptionPermission($notesDto, PermissionConstInterface::ASSET_NOTES);

        $dateTime = new \DateTime('now');
        $noteDateTime = $dateTime->setTimezone(new \DateTimeZone('UTC'));

        $teamShare = $notesDto->getShareOptions()[0]->getTeamsShare();
        $teamsArray = $notesDto->getShareOptions()[0]->getTeamIds();
        if ($actCatName) {
            $notesDto->setReasonCategorySubitem($actCatName->getDescription());
        }

        $reasonText = $activityCategory->getShortName();

        $isJob = count($personStudentIds) > 1 ? true : false;

        foreach ($personStudentIds as $personStudentId) {
            $personStudent = $this->personService->findPerson($personStudentId);
            $feature = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Notes']);
            $featureAccess = $this->featureService->verifyFacultyAccessToStudentForFeature($notesDto->getStaffId(), $organization->getId(), $personStudentId, $shareOptionPermission, $feature->getId());
            if (!$featureAccess) {
                if ($isJob) {
                    continue;
                } else {
                    $this->logger->error("Notes Service - Create Note - Do not have permission to create note for student -" . $personStudentId);
                    throw new AccessDeniedException('You do not have permission to create a note');
                }
            }

            $note = new Note();
            $note->setActivityCategory($activityCategory);
            $note->setPersonIdStudent($personStudent);
            $note->setPersonIdFaculty($personStaff);
            $note->setOrganization($organization);
            $note->setNote($notesDto->getComment());
            $note->setNoteDate($noteDateTime);
            $note->setAccessPrivate($notesDto->getShareOptions()[0]
                ->getPrivateShare());
            $note->setAccessPublic($notesDto->getShareOptions()[0]
                ->getPublicShare());
            $note->setAccessTeam($teamShare);
            $note = $this->noteRepository->createNote($note);
            $this->isExists($note, 'note_not_created', 'Note not created.');
            if ($teamShare && $note) {
                $this->addTeam($note, $teamsArray);
            }
            $this->noteRepository->flush();
            $notesDto->setNotesId($note->getId());
            $notesDto->setNotesUpdatedOn($note->getModifiedAt());
            $activityLogDto = new ActivityLogDto();
            $activityLogDto->setActivityDate($noteDateTime);
            $activityLogDto->setActivityType("N");
            $noteId = $notesDto->getNotesId();
            $activityLogDto->setNote($noteId);
            $orgId = $note->getOrganization()->getId();
            $activityLogDto->setOrganization($orgId);
            $facultyId = $personStaff->getId();
            $activityLogDto->setPersonIdFaculty($facultyId);
            $studentId = $personStudent->getId();
            $activityLogDto->setPersonIdStudent($studentId);
            $activityLogDto->setReason($reasonText);
            $this->activityLogService->createActivityLog($activityLogDto);
            $activityLogId = $notesDto->getActivityLogId();
            if (isset($activityLogId)) {
                $relatedActivitiesDto = new RelatedActivitiesDto();
                $relatedActivitiesDto->setActivityLog($activityLogId);
                $relatedActivitiesDto->setNote($noteId);
                $relatedActivitiesDto->setOrganization($orgId);
                $this->relatedActivitiesService->createRelatedActivities($relatedActivitiesDto);
            }
        } // end of for each student

        $this->noteRepository->flush();

        /*
         * After finishing bulk action send notification to logged in person
         */
        if ($isJob) {
            $alertService = $this->container->get('alertNotifications_service');
            $alertService->createNotification('bulk-action-completed', count($personStudentIds) . ' notes have been created successfully.', $personStaff, null, null, null, null, null, null, null, null, null, $note);
        }
    }

    private function addTeam($note, $teamsArray)
    {
        $this->noteTeamsRepository = $this->repositoryResolver->getRepository(self::NOTE_TEAM_REPO);
        $this->teamsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Teams");
        $noteTeams = '';
        foreach ($teamsArray as $team) {
            if ($team->getIsTeamSelected()) {
                $noteTeam = new NoteTeams();
                $team = $this->teamsRepository->find($team->getId());
                $this->isExists($team, 'team_not_found', 'Team not found.');
                $noteTeam->setNoteId($note);
                $noteTeam->setTeamsId($team);
                $noteTeams = $this->noteTeamsRepository->createNoteTeams($noteTeam);
            }
        }
        return $noteTeams;
    }

    /**
     * Edit a note
     * @param NotesDto $notesDto
     * @return NotesDto
     */
    public function editNote(NotesDto $notesDto)
    {
        $note = $this->noteRepository->find($notesDto->getNotesId());
        $this->isExists($note, self::ERROR_NOTE_NOT_FOUND_KEY, self::NOTE_NOT_FOUND);
        $shareOptionPermississon = $this->getShareOptionPermission($notesDto, PermissionConstInterface::ASSET_NOTES);
        if (!$this->rbacManager->hasAssetAccess([$shareOptionPermississon], $note)) {
            $student = $notesDto->getNotesStudentId();
            $this->logger->error("Notes Service - Edit Note - Do not have permission to edit note for student -".$student);
            throw new AccessDeniedException(self::NOTE_VIEW_EXCEPTION);
        }
        $personStaff = $this->personService->findPerson($notesDto->getStaffId());
        $this->isExists($personStaff, 'Staff_not_found', 'Staff Not Found.');
        $personStudent = $this->personService->findPerson($notesDto->getNotesStudentId());
        $this->isExists($personStudent, 'Student_not_found', 'Student Not Found.');
        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents([$notesDto->getNotesStudentId()]);
        $organization = $this->orgService->find($notesDto->getOrganization());
        $activityCategory = $this->activityCategoryRepository->find($notesDto->getReasonCategorySubitemId());
        $this->isExists($activityCategory, 'reason_category_not_found', self::REASON_CATEGORY_NOT_FOUND);
        $orgLang = $this->orgService->getOrganizationDetailsLang($personStaff->getOrganization());
        $this->isExists($orgLang, self::ERROR_ORG_NOT_FOUND_KEY, self::ORG_LANG_NOT_FOUND);
        $note->setActivityCategory($activityCategory);
        $personStudent = $this->personService->findPerson($notesDto->getNotesStudentId());
        $note->setPersonIdStudent($personStudent);
        $note->setPersonIdFaculty($personStaff);
        $note->setOrganization($organization);
        $note->setNote($notesDto->getComment());
        $note->setAccessPrivate($notesDto->getShareOptions()[0]
            ->getPrivateShare());
        $note->setAccessPublic($notesDto->getShareOptions()[0]
            ->getPublicShare());
        $teamShare = $notesDto->getShareOptions()[0]->getTeamsShare();
        $note->setAccessTeam($teamShare);
        $teamsArray = $notesDto->getShareOptions()[0]->getTeamIds();
        $this->isExists($note, 'note_not_updated', 'Not able to update a Note.');
        $noteTeams = $this->noteRepository->getNoteTeamIds($notesDto->getNotesId());
        $teamIds = array_map('current', $noteTeams);
        $newTeamArray = array();
        foreach ($teamsArray as $teamArray) {
            if ($teamArray->getIsTeamSelected()) {
                if (! in_array($teamArray->getId(), $teamIds)) {
                    $newTeamArray[] = $teamArray;
                }
            } else {
                if (in_array($teamArray->getId(), $teamIds)) {
                    $notesTeam = $this->noteTeamsRepository->findBy([
                        'noteId' => $notesDto->getNotesId(),
                        'teamsId' => $teamArray->getId()
                    ]);
                    if (! empty($notesTeam[0])) {
                        $this->noteTeamsRepository->remove($notesTeam[0]);
                    }
                }
            }
        }
        if ($teamShare && $note && $newTeamArray) {
            $this->addTeam($note, $newTeamArray);
        }
        $this->noteRepository->flush();
        return $notesDto;
    }

    /**
     * Delete a note
     * @param int $noteId
     * @return int
     */
    public function deleteNote($noteId)
    {
        $noteRec = $this->noteRepository->find($noteId);
        $personIdStudent = $noteRec->getPersonIdStudent()->getId();
        // check if the student is part of current academic year
        $this->rbacManager->assertPermissionToEngageWithStudents([$personIdStudent]);
        $this->isExists($noteRec, self::ERROR_NOTE_NOT_FOUND_KEY, self::NOTE_NOT_FOUND);
        if (!$this->rbacManager->hasAssetAccess([self::PERM_NOTES_PUBLIC_CREATE, self::PERM_NOTES_PRIVATE_CREATE, self::PERM_NOTES_TEAMS_CREATE], $noteRec)) {
            $this->logger->error("Notes Service - Delete Note - Do not have permission to delete this note -".$noteId);
            throw new AccessDeniedException(self::NOTE_VIEW_EXCEPTION);
        }
        $notesTeam = $this->noteTeamsRepository->findBy([
            'noteId' => $noteId
        ]);
        if (isset($notesTeam)) {
            foreach ($notesTeam as $noteTeam) {
                $this->noteTeamsRepository->remove($noteTeam);
            }
        }
        
        $this->noteRepository->remove($noteRec);
        $this->activityLogService->deleteActivityLogByType($noteId, 'N');
        $this->noteRepository->flush();
        return $noteId;
    }


    /**
     * Get Note by noteId
     *
     * @param int $noteId
     * @return NotesDto
     */
    public function getNotes($noteId)
    {
        $notes = $this->noteRepository->find($noteId);

        $this->isExists($notes, self::ERROR_NOTE_NOT_FOUND_KEY, self::NOTE_NOT_FOUND);

        $studentId = $notes->getPersonIdStudent()->getId();

        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        // find the access level for this particular note id, then check for permission for that only
        if ($notes->getAccessPublic()) {
            $checkAssetAccess = self::PERM_NOTES_PUBLIC_VIEW;
        } elseif ($notes->getAccessPrivate()) {
            $checkAssetAccess = self::PERM_NOTES_PRIVATE_VIEW;
        } else {
            $checkAssetAccess = self::PERM_NOTES_TEAMS_VIEW;
        }

        //check if notes has is team accessed
        if (!$this->rbacManager->hasAssetAccess([$checkAssetAccess], $notes)) {
            $this->logger->error("Notes Service - View Note - Do not have permission to view this note -" . $noteId);
            throw new AccessDeniedException(self::NOTE_VIEW_EXCEPTION);
        }

        $activityCategory = $this->activityCategoryRepository->find($notes->getActivityCategory());
        $orgLangDetails = $this->orgService->getOrganizationDetailsLang($notes->getOrganization()
            ->getId());
        $orgLang = $orgLangDetails->getLang();
        $this->isExists($orgLang, self::ERROR_ORG_NOT_FOUND_KEY, self::ORG_LANG_NOT_FOUND);
        $actCatName = $this->activityCategoryLangRepository->findOneBy(array(
            'activityCategoryId' => $activityCategory,
            'language' => $orgLang
        ));
        $notesDto = new NotesDto();
        $notesDto->setNotesId($notes->getId());
        $notesDto->setNotesStudentId($notes->getPersonIdStudent()
            ->getId());
        $facultyId = $notes->getPersonIdFaculty()->getId();
        $notesDto->setStaffId($facultyId);
        $notesDto->setOrganization($notes->getOrganization()
            ->getId());
        $notesDto->setComment($notes->getNote());
        $notesDto->setNotesUpdatedOn($notes->getModifiedAt());
        if ($actCatName) {
            $notesDto->setReasonCategorySubitemId($notes->getActivityCategory()
                ->getId());
            $notesDto->setReasonCategorySubitem($actCatName->getDescription());
        }
        $teamNote = [];
        $teamDtoData = [];
        $shareOptionsDto = new ShareOptionsDto();
        $noteTeams = $this->noteRepository->getNoteTeamIds($noteId);
        foreach ($noteTeams as $noteTeam) {
            $teamNote[] = $noteTeam['teams_id'];
        }

        $teams = $this->teamMembersRepository->getTeams($facultyId);
        $teamShare = $notes->getAccessTeam();
        if ($teamShare && !empty($teams)) {
            foreach ($teams as $team) {
                $teamId = $team['team_id'];
                $teamDto = new TeamIdsDto();
                $teamDto->setId($teamId);
                $teamDto->setTeamName($team['team_name']);
                if (in_array($teamId, $teamNote)) {
                    $teamDto->setIsTeamSelected(true);
                } else {
                    $teamDto->setIsTeamSelected(false);
                }
                $teamDtoData[] = $teamDto;
            }
            $shareOptionsDto->setTeamIds($teamDtoData);
        }
        $shareOptionsDto->setPrivateShare($notes->getAccessPrivate());
        $shareOptionsDto->setPublicShare($notes->getAccessPublic());
        $shareOptionsDto->setTeamsShare($teamShare);
        $shareOptionsDtoResponse[] = $shareOptionsDto;
        $notesDto->setShareOptions($shareOptionsDtoResponse);
        return $notesDto;
    }

    private function isExists($object, $key, $error)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $error
            ], $error, $key);
        }
    }
}