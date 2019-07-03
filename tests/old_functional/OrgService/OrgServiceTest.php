<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\LogoDto;
use Synapse\RestBundle\Entity\OrganizationDTO;
use Synapse\RestBundle\Entity\OrgStmtUpdateDto;
use Proxies\__CG__\Synapse\CoreBundle\Entity\Organization;

class OrgServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Service\OrganizationService
     */
    private $organizationService;
    
    private $personIdForCoordinator = 1;
    
    private $invalidOrg = -20;
    
    private $org = 1;
    
    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->organizationService = $this->container
        ->get('org_service');
    }    
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personIdForCoordinator);
    }
    public function testCreateOrganization()
    {
    	
    	$orgDto = $this->createOrg();
    	$organization = $this->organizationService->createOrganization($orgDto);
    	$this->assertEquals($orgDto->getName(), $organization['name']);
    	$this->assertEquals($orgDto->getNickName(), $organization['nick_name']);
    	$this->assertArrayHasKey('id', $organization);
    	$this->assertArrayHasKey('subdomain', $organization);
    	$this->assertArrayHasKey('timezone', $organization);
    	$this->assertInternalType('array', $organization);
    	 
    }
	public function testDeleteOrganization(){
	    
	    
		$orgDto = $this->createOrg();
		$organization = $this->organizationService->createOrganization($orgDto);
		
		$deleted = $this->organizationService->deleteOrganization($organization['id']);
		$this->assertEquals($organization['id'], $deleted);
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testDeleteOrganizationInvalid()
	{
		$deleted = $this->organizationService->deleteOrganization($this->invalidOrg);
	}
	
	
	public function testGetOrganizationDetails(){
		$orgDto = $this->createOrg();
		$organization = $this->organizationService->createOrganization($orgDto);
		$organizationDetails = $this->organizationService->getOrganizationDetails($organization['id']);
		$this->assertEquals($organization['id'], $organizationDetails->getId());
	}   
	
	
    public function testGetTimezones(){    	
    	$timezone = $this->organizationService->getTimezones();
    	$this->assertArrayHasKey('timezone_name', $timezone[0]);
    	$this->assertArrayHasKey('timezone', $timezone[0]);
    }
    
    
    public function testUpdateOrganizationDetails()
    {
    	$orgDto = $this->createOrg();
    	$organization = $this->organizationService->createOrganization($orgDto);
    	
    	$logoDto = $this->createLogoDto();
    	$logoDto->setOrganizationId($organization['id']);
    	$org = $this->organizationService->updateOrganizationDetails($logoDto);
    	$this->assertInstanceOf('Synapse\CoreBundle\Entity\Organization', $org);
    	$this->assertEquals($organization['id'], $org->getId());
    	$this->assertEquals('red', $org->getPrimaryColor());
    	$this->assertEquals('yellow', $org->getSecondaryColor());
    }
    
    
    public function testUpdateCustomConfidStmt(){
    	$orgStmtUpdateDto = new OrgStmtUpdateDto();    	
    	$orgStmtUpdateDto->setOrganizationId($this->org);
    	$organization = $this->organizationService->updateCustomConfidStmt($orgStmtUpdateDto);
    	$this->assertEquals($this->org, $organization['organization_id']);
    	$this->assertArrayHasKey("organization_id", $organization);
    	$this->assertArrayHasKey("custom_confidentiality_statement", $organization);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    
    public function testUpdateCustomConfidStmtInvalidOrg()
    {
    	$orgStmtUpdateDto = new OrgStmtUpdateDto();
    	$orgStmtUpdateDto->setOrganizationId($this->invalidOrg);
    	$organization = $this->organizationService->updateCustomConfidStmt($orgStmtUpdateDto);
    }
    
    
    public function testGetCustomConfidStmt(){
    	$orgDto = new OrganizationDTO();
    	$orgDto->setId($this->org);
    	$organization = $this->organizationService->getCustomConfidStmt($orgDto->getId());
    	$this->assertEquals($this->org, $organization['organization_id']);
    	$this->assertArrayHasKey('custom_confidentiality_statement',$organization);
    }
    
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetOverviewInvalidPerson(){
        $this->initializeRbac();
    	$orgDto = new OrganizationDTO();
    	$orgDto->setId($this->org);
    	$personId = -1;
    	$organization = $this->organizationService->getOverview($orgDto->getId(),$personId);    	
    }
    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testGetOverviewInvalidOrganization(){
    	$orgDto = new OrganizationDTO();
    	$orgDto->setId($this->invalidOrg);
    	$personId = $this->personIdForCoordinator;
    	$organization = $this->organizationService->getOverview($orgDto->getId(),$personId);    	
    }
    public function testGetOverview()
    {
        $this->initializeRbac();
        $orgDto = new OrganizationDTO();
    	$orgDto->setId($this->org);	
    	$organization = $this->organizationService->getOverview($orgDto->getId(),$this->personIdForCoordinator);
    	
    	$this->assertEquals($this->org, $organization['organization_id']);
    	$this->assertArrayHasKey('students_count', $organization);
    	$this->assertArrayHasKey('staff_count', $organization);
    	$this->assertArrayHasKey('permissions_count', $organization);
    	$this->assertArrayHasKey('groups_count', $organization);
    	$this->assertArrayHasKey('teams_count', $organization);
    }
    
    
    public function testGetOrganizationDetailsLang()
    {
    	$organization = $this->organizationService->getOrganizationDetailsLang($this->org);
    	$this->assertInstanceOf('Synapse\CoreBundle\Entity\OrganizationLang', $organization);
    	$this->assertEquals($this->org, $organization->getOrganization()->getId());
    }
    
    //setter methods------------------------
    public function createLogoDto()
    {
    	$logoDto = new LogoDto();
    	$logoDto->setOrganizationId($this->org);
    	$logoDto->setPrimaryColor("red");
    	$logoDto->setSecondaryColor("yellow");
    
    	return $logoDto;
    }
    public function createOrg(){
    	$orgDto = new OrganizationDTO();
    	$orgDto->setLangid($this->org);
    	/*
    	 * Special charect not allowed in Name. change functional_test to functionaltest
    	 */
    	$orgDto->setName("functionaltest");
    	$orgDto->setNickName("nick_name");
    
    	return $orgDto;
    }
    /**
     * {@inheritDoc}
     */
    protected function _after()
    {
    
    }
    
}