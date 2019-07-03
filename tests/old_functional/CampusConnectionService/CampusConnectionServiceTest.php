<?php
use Codeception\Util\Stub;
use Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto;
use Synapse\CampusConnectionBundle\EntityDto\StudentListDto;

class CampusConnectionServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService
     */
    private $campusConnectionService;

    private $orgId = 1;

    private $invalidOrgId = -2;

    private $studentids;

    private $personStudentId = 6;

    private $invalidPersonStudentId = - 1;
    
    private $personFacultyId = 4;
	
	private $coordinatorId = 1;
    
    private $timezone = "Pacific";

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->campusConnectionService = $this->container->get('campusconnection_service');
        $studentIds = array();
        $studentIds[0] = 6;
        $studentIds[1] = 8;
        $this->studentids = implode($studentIds, ',');
    }

    public function testGetStudentFacultyConnections()
    {
        $facultyConn = $this->campusConnectionService->getStudentFacultyConnections($this->orgId, $this->studentids);
        $this->assertInternalType('array', $facultyConn);
        if (! empty($facultyConn['staff_details'])) {
            foreach ($facultyConn['staff_details'] as $faculty) {
                $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\StudentFacultyConnectionsListDto', $faculty);
                $this->assertNotEmpty($faculty->getId());
                $this->assertNotEmpty($faculty->getFirstname());
                $this->assertNotEmpty($faculty->getLastname());
            }
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentFacultyConnectionsInvalidOrg()
    {
        $facultyConn = $this->campusConnectionService->getStudentFacultyConnections(mt_rand(), $this->studentids);
    }

    public function testGetStudentCampusConnections()
    {
        $connections = $this->campusConnectionService->getStudentCampusConnections($this->orgId, $this->personStudentId);
        $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\StudentCampusConnectionsDto', $connections);
        $this->assertEquals($this->orgId, $connections->getOrganizationId());
        $this->assertNotEmpty($connections->getCampusId());
        $this->assertNotEmpty($connections->getCampusName());
        if (! empty($connections->getCampusConnections())) {
            foreach ($connections->getCampusConnections() as $conn) {
                $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\CampusConnectionsArrayDto', $conn);
                $this->assertNotEmpty($conn->getPersonId());
                $this->assertNotEmpty($conn->getPersonFirstname());
                $this->assertNotEmpty($conn->getPersonLastname());
                $this->assertNotEmpty($conn->getEmail());
            }
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusConnectionsInvalidOrg()
    {
        $connections = $this->campusConnectionService->getStudentCampusConnections($this->invalidOrgId, $this->personStudentId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetStudentCampusConnectionsInvalidStudent()
    {
        $connections = $this->campusConnectionService->getStudentCampusConnections($this->orgId, $this->invalidPersonStudentId);
    }
    
    public function testAssignPrimaryConnection()
    {
        $assignPrimaryDto = $this->createPrimaryconnectionDto();
        $primaryConn = $this->campusConnectionService->assignPrimaryConnection($assignPrimaryDto, $this->timezone, $this->coordinatorId);
        $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto', $primaryConn);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testAssignPrimaryConnectionInvalidOrganization()
    {
        $assignPrimaryDto = $this->createPrimaryconnectionDto();
        $assignPrimaryDto->setOrganizationId($this->invalidOrgId);
        $primaryConn = $this->campusConnectionService->assignPrimaryConnection($assignPrimaryDto, $this->timezone, $this->coordinatorId);
    }
    
    public function testAssignPrimaryConnectionInvalid()
    {
        $assignPrimaryDto = $this->createPrimaryconnectionInvalidDto();
        $primaryConn = $this->campusConnectionService->assignPrimaryConnection($assignPrimaryDto, $this->timezone, $this->coordinatorId);
        $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\AssignPrimaryRequestDto', $primaryConn);
        foreach ($primaryConn->getStudentList() as $list) {
            $this->assertInstanceOf('Synapse\CampusConnectionBundle\EntityDto\StudentListDto', $list);
            $this->assertEquals(false, $list->getIsPrimaryAssigned());
        }
    }
    
    public function testRemovePrimaryStatus()
    {
        $this->campusConnectionService->removePrimaryStatus($this->orgId, $this->personStudentId, $this->coordinatorId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testRemovePrimaryStatusInvalidOrganization()
    {
        $this->campusConnectionService->removePrimaryStatus($this->invalidOrgId, $this->personStudentId, $this->coordinatorId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testRemovePrimaryStatusInvalidStudent()
    {
        $this->campusConnectionService->removePrimaryStatus($this->orgId, $this->invalidPersonStudentId, $this->coordinatorId);
    }
    
    public function createPrimaryconnectionDto()
    {
        $assignPrimaryRequestDto = new AssignPrimaryRequestDto();
        $assignPrimaryRequestDto->setOrganizationId($this->orgId);
        $studentList = new StudentListDto();
    
        $studentList->setStudentId($this->personStudentId);
        $studentList->setStaffId($this->personFacultyId);
    
        $studentArray = array();
        $studentArray[] = $studentList;
    
        $assignPrimaryRequestDto->setStudentList($studentArray);
        return $assignPrimaryRequestDto;
    }
    
    public function createPrimaryconnectionInvalidDto()
    {
        $assignPrimaryRequestDto = new AssignPrimaryRequestDto();
        $assignPrimaryRequestDto->setOrganizationId($this->orgId);
        $studentList = new StudentListDto();
    
        $studentList->setStudentId(0);
        $studentList->setStaffId(0);
    
        $studentArray = array();
        $studentArray[] = $studentList;
    
        $assignPrimaryRequestDto->setStudentList($studentArray);
        return $assignPrimaryRequestDto;
    }
}