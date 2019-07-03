<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\NotesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\CoreBundle\Entity\RelatedActivities;
require_once 'tests/functional/FunctionalBaseTest.php';


class RelatedActivityTest extends FunctionalBaseTest
{

    
    private $personId = 1;
    private $invalidPersonId = -1;
    private $personProxyId = 1;
    private $organizationId = 1;
    private $isFreeStandingFalse = true;
    private $type = "S";
    private $teamId = 1;
    
    /**
     *
     * @var \Synapse\CoreBundle\Service\Impl\RelatedActivities
     */
    private $relatedActivitiesService;
    
    public function _before()
    {
        $this->markTestSkipped('Skipping test due to following exception happening causing it to error out: Symfony\Component\Security\Core\Exception\AccessDeniedException: note');
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->relatedActivitiesService = $this->container->get('relatedactivities_service');
        $this->appointmentsService = $this->container->get('appointments_service');
        $this->notesService = $this->container->get('notes_service');
        $this->activityLogService = $this->container->get('activitylog_service');
        $this->notesServiceM = $this->createServiceWithRbacMock('notes_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rb = $rbacMan->initializeForUser($this->personId);
    }

      
    public function testCreateRelatedActivity()
    {
        $this->initializeRbac();
        $appointmentsDto = $this->createAppointmentsDto();
        $appointments = $this->appointmentsService->create($appointmentsDto);
        $appointmentId = $appointments->getAppointmentId();
        
        $activityLogDto = new ActivityLogDto(); 
        $activityLogDto->setActivityDate(new \DateTime('now'));
        $activityLogDto->setActivityType("A");
     
        $activityLogDto->setAppointments($appointmentId);
        $orgId =$this->organizationId ;
        $activityLogDto->setOrganization($orgId);
        $activityLogDto->setPersonIdFaculty($this->personId);
        $activityLogDto->setPersonIdStudent($this->personId);
        $activityLogDto->setReason("Some reason");
        $actLog = $this->activityLogService->createActivityLog($activityLogDto);
        
        $notesdto = $this->createNotesDto();
        $note = $this->notesServiceM->createNote($notesdto);

        $noteId  = $note->getNotesId();

        
        $relatedActivitiesDto =  new RelatedActivitiesDto();
        $relatedActivitiesDto->setActivityLog($actLog->getId());
        $relatedActivitiesDto->setNote($noteId);
        $relatedActivitiesDto->setOrganization($this->organizationId);
        $related = $this->relatedActivitiesService->createRelatedActivities($relatedActivitiesDto);        
        $this->assertInstanceOf('Synapse\CoreBundle\Entity\RelatedActivities',$related);
        
        $this->assertObjectHasAttribute('organization', $related);
        $this->assertObjectHasAttribute('activityLog', $related);
        $this->assertObjectHasAttribute('contacts', $related);
        $this->assertObjectHasAttribute('note', $related);
        $this->assertEquals($noteId, $related->getNote()->getId());

        if(isset($actLog)){
            $activityLogId = $actLog->getId();
        }else{
            $activityLogId = 0;
        }
        $this->assertEquals($activityLogId, $related->getActivityLog()->getId());

    }
    
    private function teams(){
        $return = array();
        $teams = array();
        $team = new TeamIdsDto();
        $team->setId($this->teamId);
        $team->setIsTeamSelected(true);
        $teams[] = $team;
        return $teams;
    }
    
    private function shareOptions(){
        $return = array();
        $sharaOption = new ShareOptionsDto();
        $sharaOption->setPrivateShare(true);
        $sharaOption->setPublicShare(true);
        $sharaOption->setTeamsShare(false);
        $sharaOption->setTeamIds($this->teams());
        return array($sharaOption);
    }
    
    
    private function createNotesDto()
    {
        $notesDto = new NotesDto();
    
        $notesDto->setNotesStudentId(1);
        $notesDto->setReasonCategorySubitemId(1);
        $notesDto->setOrganization(1);
        $notesDto->setReasonCategorySubitem('Test');
        $notesDto->setComment('Test Note');
        $notesDto->setShareOptions($this->shareOptions());
        $notesDto->setStaffId(1);
        $notesDto->setNotesId(1);
        return $notesDto;
    }
    
    public function createAppointmentsDto(){
        $appDto = new AppointmentsDto();
        $appDto->setPersonId($this->personId);
        $appDto->setPersonIdProxy($this->personProxyId);
        $appDto->setOrganizationId($this->organizationId);
        $appDto->setDetail("Reason details");
        $appDto->setDetailId(1);
        $appDto->setLocation("Stepojevac");
        $appDto->setDescription("dictumst etiam faucibus cursus elementum");
        $appDto->setOfficeHoursId(3);
        $appDto->setIsFreeStanding($this->isFreeStandingFalse);
        $appDto->setType($this->type);
        $appDto->setAttendees($this->createAttendees("create"));
        $appDto->setSlotStart(new \DateTime("+ 3 hour"));
        $appDto->setSlotEnd(new \DateTime("+ 4 hour"));
        return $appDto;
    }
    
    public function createAttendees($action = "create")
    {
        $return = [];
        $attDto = new AttendeesDto();
        $attDto->setStudentId(1);
        if($action == "create")
        {
            $attDto->setIsSelected(true);
            $attDto->setIsAddedNew(true);
        }
        $attDto = new AttendeesDto();
        $attDto->setStudentId(2);
        if($action == "create")
        {
            $attDto->setIsSelected(true);
            $attDto->setIsAddedNew(true);
        }
        $return = $attDto;
        return $return;
    
    
    }
    
    
    
    
}