<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\AcademicBundle\EntityDto\AcademicTermDto;
class AcademicTermServiceTest extends \Codeception\TestCase\Test {
	
	/**
	 *
	 * @var UnitTester
	 */
	protected $tester;
	
	/**
	 *
	 * @var Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 *
	 * @var \Synapse\AcademicBundle\Service\Impl\AcademicTermService
	 */
	private $academicTermService;
	private $organization = 1;
	private $invalidOrg = -2;
	private $personId = 1;
	private $invalidPersonId = - 1;
	private $academicYearId = 2;
	private $invalidYearId = - 1;
	private $academicTermId = 1;
	private $invalidTermId = - 1;
	private $start;
	private $end;
	public function _before() {
		$this->container = $this->getModule ( 'Symfony2' )->container;
		$this->academicTermService = $this->container->get ( 'academicterm_service' );
		$this->start = new DateTime ( "now" );
		$this->end = clone $this->start;
		$this->end->add ( new \DateInterval ( 'P1M' ) );
	}
	protected function initializeRbac()
	{
	    // Bootstrap Rbac Authorization.
	    /** @var Manager $rbacMan */
	    $rbacMan = $this->container->get('tinyrbac.manager');
	    $rbacMan->initializeForUser($this->personId);
	}
	private function createAcademicTermDto() {
		$termDto = new AcademicTermDto ();
		$termDto->setOrganizationId ( $this->organization );
		$termDto->setAcademicYearId ( $this->academicYearId );
		$termDto->setName ( "Test Academic Term" );
		$termDto->setTermCode ( substr ( uniqid (), 0, 9 ) );
		$termDto->setStartDate ( $this->start );
		$termDto->setEndDate ( $this->end );
		
		return $termDto;
	}
	public function testCreateAcademicTerm() {
	    $this->initializeRbac();
		$termDto = $this->createAcademicTermDto ();
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
		$this->assertInstanceOf ( "Synapse\AcademicBundle\EntityDto\AcademicTermDto", $term );
		$this->assertEquals ( $this->organization, $term->getOrganizationId () );
		$this->assertEquals ( $this->academicYearId, $term->getAcademicYearId () );
		$this->assertEquals ( "Test Academic Term", $term->getName () );
		$this->assertEquals ( $this->start, $term->getStartDate () );
		$this->assertEquals ( $this->end, $term->getEndDate () );
		$this->assertNotEmpty ( $term->getTermId () );
	}
	public function testGetAcademicTerm() {
	    $this->initializeRbac();
		$termDto = $this->createAcademicTermDto ();
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
		$getTerm = $this->academicTermService->getAcademicTerm ( $this->organization, $this->academicYearId, $term->getTermId (), $this->personId );
		$this->assertInstanceOf ( "Synapse\AcademicBundle\EntityDto\AcademicTermDto", $term );
		$this->assertEquals ( $this->organization, $term->getOrganizationId () );
		$this->assertEquals ( $this->academicYearId, $term->getAcademicYearId () );
		$this->assertEquals ( "Test Academic Term", $term->getName () );
		$this->assertEquals ( $term->getTermId (), $getTerm->getTermId () );
		$this->assertEquals ( $this->start, $term->getStartDate () );
		$this->assertEquals ( $this->end, $term->getEndDate () );
	}
	public function testEditAcademicTerm() {
		$termDto = $this->createAcademicTermDto ();
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
		$termDto->setName ( "Test Academic Term Edited" );
		$termDto->setTermId ( $term->getTermId () );
		$editTerm = $this->academicTermService->editAcademicTerm ( $termDto, $this->personId );
		$this->assertInstanceOf ( "Synapse\AcademicBundle\EntityDto\AcademicTermDto", $term );
		$this->assertEquals ( $this->organization, $editTerm->getOrganizationId () );
		$this->assertEquals ( $this->academicYearId, $editTerm->getAcademicYearId () );
		$this->assertEquals ( "Test Academic Term Edited", $editTerm->getName () );
		$this->assertEquals ( $term->getTermId (), $editTerm->getTermId () );
		$this->assertEquals ( $this->start, $editTerm->getStartDate () );
		$this->assertEquals ( $this->end, $editTerm->getEndDate () );
	}
	public function testDeleteAcademicTerm() {
		$termDto = $this->createAcademicTermDto ();
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
		$deleteTerm = $this->academicTermService->deleteAcademicTerm ( $term->getTermId (), $this->organization, $this->personId );
		$this->assertEquals ( $term->getTermId (), $deleteTerm );
	}
	public function testListAcademicTerms() {
		$termDto = $this->createAcademicTermDto ();
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
		$termList = $this->academicTermService->listAcademicTerms ( $this->organization, $this->academicYearId, $this->personId );
		$this->assertInstanceOf ( "Synapse\AcademicBundle\EntityDto\AcademicTermListResponseDto", $termList );
		$this->assertEquals ( $this->organization, $termList->getOrganizationId () );
		$this->assertEquals ( $this->academicYearId, $termList->getAcademicYearId () );
		$this->assertInternalType ( 'array', $termList->getAcademicTerms () );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateAcademicTermGreaterStartDate() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setStartDate ( $this->end );
		$termDto->setEndDate ( $this->start );
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
  public function testCreateAcademicTermBeyondYear() {
    //$this->markTestSkipped("Failed");
		$termDto = $this->createAcademicTermDto ();
		$termDto->setStartDate ( $this->start->sub ( new \DateInterval ( 'P13M' ) ) );
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testCreateAcademicTermInvalidOrg() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setOrganizationId ( $this->invalidOrg );
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateAcademicTermInvalidYear() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setAcademicYearId ( $this->invalidYearId );
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testGetAcademicTermInvalidOrg() {
		$getTerm = $this->academicTermService->getAcademicTerm ( $this->invalidOrg, $this->academicYearId, $this->academicTermId, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetAcademicTermInvalidYear() {
		$getTerm = $this->academicTermService->getAcademicTerm ( $this->organization, $this->invalidYearId, $this->academicTermId, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetAcademicTermInvalidTerm() {
		$getTerm = $this->academicTermService->getAcademicTerm ( $this->organization, $this->academicYearId, $this->invalidTermId, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testEditAcademicTermInvalidOrg() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setOrganizationId ( $this->invalidOrg );
		$editTerm = $this->academicTermService->editAcademicTerm ( $termDto, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testEditAcademicTermInvalidYear() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setAcademicYearId ( $this->invalidYearId );
		$editTerm = $this->academicTermService->editAcademicTerm ( $termDto, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testEditAcademicTermInvalidTerm() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setTermId ( $this->invalidTermId );
		$editTerm = $this->academicTermService->editAcademicTerm ( $termDto, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testListAcademicTermInvalidOrg() {
		$termList = $this->academicTermService->listAcademicTerms ( $this->invalidOrg, $this->academicYearId, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testListAcademicTermInvalidYear() {
		$termList = $this->academicTermService->listAcademicTerms ( $this->organization, $this->invalidYearId, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testDeleteAcademicTermInvalidOrg() {
		$deleteTerm = $this->academicTermService->deleteAcademicTerm ( $this->academicTermId, $this->invalidOrg, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testDeleteAcademicTermInvalidTerm() {
		$deleteTerm = $this->academicTermService->deleteAcademicTerm ( $this->invalidTermId, $this->organization, $this->personId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testCreateAcademicTermInvalidUser() {
		$termDto = $this->createAcademicTermDto ();
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->invalidPersonId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testGetAcademicTermInvalidUser() {
		$getTerm = $this->academicTermService->getAcademicTerm ( $this->organization, $this->academicYearId, $this->academicTermId, $this->invalidPersonId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testListAcademicTermInvalidUser() {
		$termList = $this->academicTermService->listAcademicTerms ( $this->organization, $this->academicYearId, $this->invalidPersonId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testEditAcademicTermInvalidUser() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setOrganizationId ( $this->invalidOrg );
		$editTerm = $this->academicTermService->editAcademicTerm ( $termDto, $this->invalidPersonId );
	}
	
	/**
	 * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
	 */
	public function testDeleteAcademicTermInvalidUser() {
		$deleteTerm = $this->academicTermService->deleteAcademicTerm ( $this->academicTermId, $this->organization, $this->invalidPersonId );
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testCreateAcademicTermWithSameTermCode() {
		$termDto = $this->createAcademicTermDto ();
		$termDto->setTermCode ( "12345" );
		$term = $this->academicTermService->createAcademicTerm ( $termDto, $this->personId );
		$term = $this->academicTermService->createAcademicTerm($termDto, $this->personId);   	
    }
    
    
}
