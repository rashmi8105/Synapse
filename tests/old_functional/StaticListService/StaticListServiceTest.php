<?php
use Codeception\Util\Stub;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;

//use Synapse\AcademicBundle\EntityDto\FacultyPermissionDto;

class StaticListServiceTest extends \Codeception\TestCase\Test
{

    private $orgId = 1;
    private $studentId = 8;
    private $staticListId = 2;
    private $name = "First Generation Students";
    private $description = "First Generation Students Description";

    private $invalidStaticListId = -1;
    private $invalidStudentId = -1;
    private $invalidOrgId = -1;
    
    private $faculId = 1;
    private $person_service = "";
    
    private $studentAddId = 8;
    private $studentAddExternalId = "external_id-8";
    

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->staticListService = $this->container->get('staticlist_service');
        $this->person_service = $this->container->get('person_service');
        $this->staticListStudentService = $this->container->get('staticliststudent_service');
    }

    protected function initializeRbac()
    {
    	// Bootstrap Rbac Authorization.
    	/** @var Manager $rbacMan */
    	$rbacMan = $this->container->get('tinyrbac.manager');
    	$rbacMan->initializeForUser($this->faculId);
    }

    public function testCreateStaticList()
    {
        $personFaculty = $this->person_service->find($this->faculId);
        $createStaticList = $this->staticListService->createStaticList($this->orgId, $personFaculty, $this->name, $this->description);
    }

    public function testDeleteStaticList()
    {
        $personFaculty = $this->person_service->find($this->faculId);
        $createStaticList = $this->staticListService->createStaticList($this->orgId, $personFaculty, $this->name, $this->description);
        $deleteStaticList = $this->staticListService->deleteStaticList($this->orgId, $personFaculty, $createStaticList->getStaticlistId());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testDeleteStaticListInvalidStaticId()
    {
        $staticList = $this->staticListService->deleteStaticList($this->orgId, $this->studentId, $this->invalidStaticListId);
        $this->assertSame('{"errors": ["Staticlist Not Found"],
            "data": [],
            "sideLoaded": []
            }', $staticList);
    }

    public function testUpdateStaticList()
    {
        $personFaculty = $this->person_service->find($this->faculId);
        $createStaticList = $this->staticListService->createStaticList($this->orgId, $personFaculty, $this->name, $this->description);
        $deleteStaticList = $this->staticListService->updateStaticList($this->orgId, $personFaculty, $createStaticList->getStaticlistId(), $this->name, $this->description);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateStaticListInvalidStaticId()
    {
        $staticList = $this->staticListService->updateStaticList($this->orgId, $this->studentId, $this->invalidStaticListId, $this->name, $this->description);
        $this->assertSame('{"errors": ["Staticlist Not Found"],
            "data": [],
            "sideLoaded": []
            }', $staticList);
    }


    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    /* service method getStaticList($orgId=null) not used
    public function testGetAllStaticListInvalidOrganizationId()
    {
        $staticList = $this->staticListService->getStaticList($this->invalidOrgId);
        $this->assertSame('{"errors": ["Organization Not Found"],
            "data": [],
            "sideLoaded": []
            }', $staticList);
    }
    */
    
    public function testUpdateStaticListAddStudent(){
        $this->initializeRbac();
    	$personFaculty = $this->person_service->find($this->faculId);
    	$createStaticList = $this->staticListService->createStaticList($this->orgId, $personFaculty, $this->name, $this->description);
    	$studentAddToStaticList = $this->staticListStudentService->addOrRemoveStudent($this->orgId, $personFaculty, $this->studentAddExternalId, $createStaticList->getStaticlistId(), 'add');
    }
    
}