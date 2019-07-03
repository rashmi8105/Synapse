<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\NotesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
require_once 'tests/functional/FunctionalBaseTest.php';

class NotesServiceTest extends FunctionalBaseTest
{
	/**
	 * @var UnitTester
	 */
	protected $tester;
	
	/**
	 * @var Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 * @var \Synapse\CoreBundle\Service\Impl\NotesService
	 */
	private $notesService;
	
	private $organization = 1;
	
	private $invalidNoteId = -1;
	
	private $invalidOrganizationId = -100;
	
	private $userId = 1;
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->notesService = $this->createServiceWithRbacMock('notes_service');
	}
	protected function initializeRbac()
	{
	    // Bootstrap Rbac Authorization.
	    /** @var Manager $rbacMan */
	    $rbacMan = $this->container->get('tinyrbac.manager');
	    $rbacMan->initializeForUser($this->userId);
	}
	
	private function shareOptions(){
		$return = array();
		$sharaOption = new ShareOptionsDto();
		$sharaOption->setPrivateShare(true);
		$sharaOption->setPublicShare(true);
		$sharaOption->setTeamsShare(true);
		$sharaOption->setTeamIds($this->teams());
		return array($sharaOption);
	}
	
	
	private function teams(){
		$return = array();
		$teams = array();
		$team = new TeamIdsDto();
	
		for($i=1; $i<=3; $i++){
			$team->setId($i);
			$team->setIsTeamSelected(true);
			$teams[] = $team;
		}
	
		return $teams;
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
	

	public function testCreateNote()
	{
        //$this->markTestSkipped("Errored");
	    $this->initializeRbac();
	    $notesdto = $this->createNotesDto();		
		$note = $this->notesService->createNote($notesdto);
		$this->assertInstanceOf('Synapse\RestBundle\Entity\NotesDto', $note);
		$this->assertEquals($notesdto->getReasonCategorySubitemId(), $note->getReasonCategorySubitemId());
		$this->assertEquals($notesdto->getReasonCategorySubitem(), $note->getReasonCategorySubitem());
		$this->assertEquals($notesdto->getStaffId(), $note->getStaffId());
		$this->assertEquals($notesdto->getNotesStudentId(), $note->getNotesStudentId());
		$this->assertEquals($notesdto->getShareOptions(), $note->getShareOptions());
		$this->assertEquals($notesdto->getComment(), $note->getComment());
		$this->assertNotNull($note->getNotesUpdatedOn());
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateNoteInvalidStaff()
	{
		$notesdto = $this->createNotesDto();
		$notesdto->setStaffId(-1);		
		$note = $this->notesService->createNote($notesdto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateNoteInvalidReasonCategorySubitem()
	{
		$notesdto = $this->createNotesDto();
		$notesdto->setReasonCategorySubitemId(-1);
	
		$note = $this->notesService->createNote($notesdto);
	}
	
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateNoteInvalidTeams()
	{
        //$this->markTestSkipped("Failed");
		$notesdto = $this->createNotesDto();
		$teams = array();
		$team = new TeamIdsDto();
		$team->setId(-1);
		$team->setIsTeamSelected(true);
		$teams[] = $team;
		$shareOption = $this->shareOptions();
		$shareOptions = $shareOption[0];
		$shareOptions->setTeamIds($teams);
		$shareOption[] = $shareOptions;		
		$notesdto->setShareOptions($shareOption);	
		$note = $this->notesService->createNote($notesdto);
	}
	
	
	public function testEditNote()
	{
       // $this->markTestSkipped("Errored");
		$notesDto = $this->createNotesDto();				
		$newNote = $this->notesService->createNote($notesDto);		
		$notesDto->setNotesId($newNote->getNotesId());		
		$noteEdit = $this->notesService->EditNote($notesDto);	
		$this->assertEquals($newNote->getNotesId(), $noteEdit->getNotesId());
		$this->assertEquals($newNote->getComment(), $noteEdit->getComment());
		$this->assertEquals($newNote->getStaffId(), $noteEdit->getStaffId());
		$this->assertEquals($newNote->getReasonCategorySubitemId(), $noteEdit->getReasonCategorySubitemId());
		$this->assertEquals($newNote->getNotesStudentId(), $noteEdit->getNotesStudentId());
		$this->assertEquals($newNote->getOrganization(), $noteEdit->getOrganization());		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	
	public function testEditNotewithInvalidNoteId()
	{			
		$notesDto = $this->createNotesDto();
		$notesDto->setNotesId($this->invalidNoteId);		
		$result = $this->notesService->EditNote($notesDto);			
		$this->assertInternalType('array', $result,"Note Not Found.");		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	
	public function testEditNotewithInvalidOrganizationId()
	{			
        //$this->markTestSkipped("Failed");
		$notesDto = $this->createNotesDto();		
		$newNote = $this->notesService->createNote($notesDto);		
		$notesDto->setNotesId($newNote->getNotesId());			
		$notesDto->setOrganization($this->invalidOrganizationId);
		$result = $this->notesService->EditNote($notesDto);
		$this->assertInternalType('array', $result,"Organization Not Found.");		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testEditNoteInvalidStaff()
	{
		$notesdto = $this->createNotesDto();
		$notesdto->setStaffId(-1);		
		$result = $this->notesService->editNote($notesdto);
		$this->assertInternalType('array', $result,"Staff Not Found.");	
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	
	public function testViewNoteInvalidNoteId()
	{				
		$result = $this->notesService->getNotes(-1);
		$this->assertInternalType('array', $result,"Note Not Found.");	
	}
	
	public function testViewNote()
	{				
        //$this->markTestSkipped("Errored");
		$notesDto = $this->createNotesDto();		
		$newNote = $this->notesService->createNote($notesDto);				
		$result = $this->notesService->getNotes($newNote->getNotesId());			
		$this->assertEquals($newNote->getNotesStudentId(), $result->getNotesStudentId());	
		$this->assertEquals($newNote->getStaffId(), $result->getStaffId());	
		$this->assertEquals($newNote->getNotesUpdatedOn(), $result->getNotesUpdatedOn());	
		$this->assertEquals($newNote->getReasonCategorySubitemId(), $result->getReasonCategorySubitemId());	
		$this->assertEquals($newNote->getComment(), $result->getComment());
		$this->assertEquals($newNote->getComment(), $result->getComment());		
		$this->assertEquals($newNote->getShareOptions()[0]->getPrivateShare(), $result->getShareOptions()[0]->getPrivateShare());
		
	}
	
	/**
    * @expectedException Synapse\RestBundle\Exception\ValidationException
    */
	public function testDeleteNoteWithInvalidNoteId()
	{
		$note = $this->notesService->deleteNote($this->invalidNoteId);
        $this->assertInternalType('object', $note,"Note Not Found.");
		$this->assertSame('{"errors": ["Note Not Found."],
			"data": [],
			"sideLoaded": []
			}',$officeHour);
	}
	
	public function testDeleteNote()
	{
        //$this->markTestSkipped("Errored");
		$noteDto = $this->createNotesDto();				
		$newNote = $this->notesService->createNote($noteDto);				
		$noteDelet = $this->notesService->deleteNote($newNote->getNotesId());		
	}
}
