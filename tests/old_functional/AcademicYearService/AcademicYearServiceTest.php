<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\AcademicBundle\EntityDto\AcademicYearDto;

class AcademicYearServiceTest extends \Codeception\TestCase\Test
{

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
     * @var \Synapse\AcademicBundle\Service\Impl\AcademicYearService
     */
    private $academicYearService;

    private $organization = 1;

    private $invalidOrg = -2;

    private $personId = 1;

    private $name = "Academic Year 2015";

    private $invalidYearId = - 1;

    private $start;

    private $end;

    private $yearId = "202122";

    private $academicYearId = 18;

    private $invalidPersonId = - 1;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->academicYearService = $this->container->get('academicyear_service');
        $this->start = new DateTime("now");
        // TOCONFIRM - year overlab added 12M from now to avaoid overlab
        $this->start->add(new \DateInterval('P10Y1M'));
        $this->end = clone $this->start;
        $this->end->add(new \DateInterval('P10Y11M'));
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }
    private function createAcademicYearDto()
    {
        $yearDto = new AcademicYearDto();
        $yearDto->setOrganization($this->organization);
        $yearDto->setName($this->name);
        $yearDto->setYearId($this->yearId);
        $yearDto->setStartDate($this->start);
        $yearDto->setEndDate($this->end);

        return $yearDto;
    }

    public function testCreateAcademicyear()
    {
        $this->initializeRbac();
        $yearDto = $this->createAcademicYearDto();
        $year = $this->academicYearService->createAcademicyear($yearDto, $this->personId);
        $this->assertInstanceOf("Synapse\AcademicBundle\EntityDto\AcademicYearDto", $year);
        $this->assertEquals($this->organization, $year->getOrganization());
        $this->assertEquals($this->yearId, $year->getYearId());
        $this->assertEquals($this->name, $year->getName());
        $this->assertEquals($this->start, $year->getStartDate());
        $this->assertEquals($this->end, $year->getEndDate());
        
        $deleteYear = $this->academicYearService->deleteAcademicYear($this->organization, $year->getId(), $this->personId);
    }

    public function testGetAcademicYear()
    {
        $this->initializeRbac();
        $yearDto = $this->createAcademicYearDto();
        $year = $this->academicYearService->createAcademicyear($yearDto, $this->personId);
        $getYear = $this->academicYearService->getAcademicYear($this->organization, $year->getId(), $this->personId);
        $this->assertInstanceOf("Synapse\AcademicBundle\EntityDto\AcademicYearDto", $year);
        $this->assertEquals($this->organization, $year->getOrganization());
        $this->assertEquals($this->yearId, $getYear->getYearId());
        $this->assertEquals($this->name, $year->getName());
        $this->assertEquals($this->start, $year->getStartDate());
        $this->assertEquals($this->end, $year->getEndDate());
        
        $deleteYear = $this->academicYearService->deleteAcademicYear($this->organization, $year->getId(), $this->personId);
    }

    public function testEditAcademicYear()
    {
        $yearDto = $this->createAcademicYearDto();
        $year = $this->academicYearService->createAcademicyear($yearDto, $this->personId);
        $yearDto->setName("Test Academic Year Edited");
        $yearDto->setYearId($year->getYearId());
        $edityear = $this->academicYearService->editAcademicYear($yearDto, $this->personId);
        $this->assertInstanceOf("Synapse\AcademicBundle\EntityDto\AcademicYearDto", $year);
        $this->assertEquals($this->organization, $edityear->getOrganization());
        $this->assertEquals($this->yearId, $edityear->getYearId());
        $this->assertEquals("Test Academic Year Edited", $edityear->getName());
        $this->assertEquals($this->start, $edityear->getStartDate());
        $this->assertEquals($this->end, $edityear->getEndDate());
        
        $deleteYear = $this->academicYearService->deleteAcademicYear($this->organization, $year->getId(), $this->personId);
    }

    public function testDeleteAcademicYear()
    {
        $yearDto = $this->createAcademicYearDto();
        $year = $this->academicYearService->createAcademicyear($yearDto, $this->personId);
        $deleteYear = $this->academicYearService->deleteAcademicYear($this->organization, $year->getId(), $this->personId);
    }


    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateAcademicYearGreaterStartDate()
    {
        $yearDto = $this->createAcademicYearDto();
        $yearDto->setStartDate($this->end);
        $yearDto->setEndDate($this->start);
        $year = $this->academicYearService->createAcademicyear($yearDto, $this->personId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testCreateAcademicYearInvalidOrg()
    {
        $yearDto = $this->createAcademicYearDto();
        $yearDto->setOrganization($this->invalidOrg);
        $year = $this->academicYearService->createAcademicyear($yearDto, $this->personId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testGetAcademicYearInvalidOrg()
    {
        $getYear = $this->academicYearService->getAcademicYear($this->invalidOrg, $this->academicYearId, $this->personId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetAcademicYearInvalidYear()
    {
        $getYear = $this->academicYearService->getAcademicYear($this->organization, $this->invalidYearId, $this->personId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testEditAcademicYearInvalidOrg()
    {
        $yearDto = $this->createAcademicYearDto();
        $yearDto->setOrganization($this->invalidOrg);
        $edityear = $this->academicYearService->editAcademicYear($yearDto, $this->personId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditAcademicYearInvalidYear()
    {
        $yearDto = $this->createAcademicYearDto();
        $yearDto->setYearId($this->invalidYearId);
        $edityear = $this->academicYearService->editAcademicYear($yearDto, $this->personId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testDeleteAcademicYearInvalidOrg()
    {
        $deleteYear = $this->academicYearService->deleteAcademicYear($this->invalidOrg, $this->academicYearId, $this->personId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteAcademicYearInvalidYear()
    {
        $deleteYear = $this->academicYearService->deleteAcademicYear($this->organization, $this->invalidYearId, $this->personId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testEditAcademicYearInvalidUser()
    {
        $yearDto = $this->createAcademicYearDto();
        $yearDto->setOrganization($this->invalidOrg);
        $editYear = $this->academicYearService->editAcademicYear($yearDto, $this->invalidPersonId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testDeleteAcademicYearInvalidUser()
    {
        $deleteYear = $this->academicYearService->deleteAcademicYear($this->organization, $this->invalidYearId, $this->invalidPersonId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testCreateAcademicYearInvalidUser()
    {
        $yearDto = $this->createAcademicYearDto();
        $year = $this->academicYearService->createAcademicyear($yearDto, $this->invalidPersonId);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testGetAcademicYearInvalidUser()
    {
        //$this->markTestSkipped("Failed");
        $getYear = $this->academicYearService->getAcademicYear($this->organization, $this->invalidYearId, $this->invalidPersonId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateAcademicYearInvalidYearId()
    {
    	$yearDto = $this->createAcademicYearDto();
    	$yearDto->setYearId($this->invalidYearId);
    	$term = $this->academicYearService->createAcademicyear($yearDto, $this->personId);
    }

    public function testListYear()
    {
    	$yearList = $this->academicYearService->listYear($this->organization);
    	$this->assertInternalType('array', $yearList);
    }

    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testLisYearInvalidOrg()
    {
    	$yearList = $this->academicYearService->listYear($this->invalidOrg);
    }
}