<?php
use Codeception\Util\Stub;
use Synapse\StudentBulkActionsBundle\EntityDto\BulkStudentsDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class StudentBulkActionTest extends \Codeception\TestCase\Test
{

    private $studentbulkactionService;

    private $orgId = 1;

    private $coordinatorId = 1;

    private $studentArray = [
        [
            "student_id" => 6,
            "student_firstname" => "firstname",
            "student_lastname" => "lastname"
        ],
        [
            "student_id" => 8,
            "student_firstname" => "firstname",
            "student_lastname" => "lastname"
        ]
    ];

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->studentbulkactionService = $this->container->get('studentbulkactions_service');
    }

    private function createBulkStudentsDto($type = 'N')
    {
        $bulk = new BulkStudentsDto();
        $bulk->setOrganizationId($this->orgId);
        $bulk->setType($type);
        $bulk->setStudents($this->studentArray);
        return $bulk;
    }

    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /**
         * @var Manager $rbacMan
         */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->coordinatorId);
    }

    public function testCreateBulkNotes()
    {
        $this->initializeRbac();
        $type = "N";
        $bulkActionDto = $this->createBulkStudentsDto($type);
        $bulkAction = $this->studentbulkactionService->getStudentPermissions($bulkActionDto, $type, $this->coordinatorId);
        $this->assertEquals($this->coordinatorId, $bulkAction->getPersonStaffId());
        $this->assertEquals($this->orgId, $bulkAction->getOrganizationId());
    }

    public function testCreateBulkReferrals()
    {
        $this->initializeRbac();
        $type = "R";
        $bulkActionDto = $this->createBulkStudentsDto($type);
        $bulkAction = $this->studentbulkactionService->getStudentPermissions($bulkActionDto, $type, $this->coordinatorId);
        $this->assertEquals($this->coordinatorId, $bulkAction->getPersonStaffId());
        $this->assertEquals($this->orgId, $bulkAction->getOrganizationId());
    }

    public function testCreateBulkAppointment()
    {
        $this->initializeRbac();
        $type = "A";
        $bulkActionDto = $this->createBulkStudentsDto($type);
        $bulkAction = $this->studentbulkactionService->getStudentPermissions($bulkActionDto, $type, $this->coordinatorId);
        $this->assertEquals($this->coordinatorId, $bulkAction->getPersonStaffId());
        $this->assertEquals($this->orgId, $bulkAction->getOrganizationId());
    }

    public function testCreateBulkContact()
    {
        $this->initializeRbac();
        $type = "C";
        $bulkActionDto = $this->createBulkStudentsDto($type);
        $bulkAction = $this->studentbulkactionService->getStudentPermissions($bulkActionDto, $type, $this->coordinatorId);
        $this->assertEquals($this->coordinatorId, $bulkAction->getPersonStaffId());
        $this->assertEquals($this->orgId, $bulkAction->getOrganizationId());
    }

    public function testCreateBulkEmail()
    {
        $this->initializeRbac();
        $type = "E";
        $bulkActionDto = $this->createBulkStudentsDto($type);
        $bulkAction = $this->studentbulkactionService->getStudentPermissions($bulkActionDto, $type, $this->coordinatorId);
        $this->assertEquals($this->coordinatorId, $bulkAction->getPersonStaffId());
        $this->assertEquals($this->orgId, $bulkAction->getOrganizationId());
    }
}
