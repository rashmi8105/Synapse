<?php

class PermissionServiceTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;
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
     * @var \Synapse\CoreBundle\Service\Impl\PermissionService
     */
    private $permissionService;

    /**
     *
     * @var \Synapse\CoreBundle\Service\Impl\PersonService
     */
    private $personService;

    /**
     *
     * @var \Synapse\CoreBundle\Service\Impl\OrganizationService
     */
    private $orgService;

    /**
     * {@inheritDoc}
     */
    private $organization = 1;
    private $invalidOrganization = -2;
    private $userId = 1;
    private $invalidUserId = -1;

    public function _before()
    {
    }

    public function testStudentsByOrganization()
    {
        $this->markTestSkipped("Errored");
        $result = $this->permissionService->searchForStudents($this->organization, $this->userId, false);
        $this->assertInternalType('array', $result);
        $this->assertEquals($this->organization, $result ['organization_id']);
        $this->assertNotEquals(0, $result ['organization_id']);
        $this->assertInternalType('array', $result ['users']);
    }

    public function testGetStudentsByOrganization()
    {
		$this->markTestSkipped("Errored");
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->permissionService = $this->container->get('permission_service');
            $this->orgService = $this->container->get('org_service');
            $this->personService = $this->container->get('person_service');
        });

        $this->specify("Verify that the permissions service handles all search criteria correctly when using the front end search box", function ($organizationId, $userId, $appointmentPermission, $userType, $searchText, $expectedResultsSize) {

            $results = $this->permissionService->searchForStudents($organizationId, $userId, $appointmentPermission, $userType, $searchText);
            $this->assertEquals($organizationId, $results['organization_id']);
            $this->assertEquals($expectedResultsSize, count($results['users']));
        }, ['examples' => [
            [1, 2, false, 'faculty', 'user', 0],
            [1, 2, false, 'faculty', 'd', 0],
            [1, 2, false, 'faculty', '@', 0],
            [1, 2, false, 'faculty', 'smith', 0],
            [1, 2, false, 'faculty', 'art', 0],
            [1, 2, false, 'faculty', '\') or p.id in (select id from person) ##', 0],
            [1, 2, true, 'faculty', 'user', 0],
            [1, 2, true, 'faculty', 'd', 0],
            [1, 2, true, 'faculty', '@', 0],
            [1, 2, true, 'faculty', 'smith', 0],
            [1, 2, true, 'faculty', 'art', 0],
            [1, 2, true, 'faculty', '\') or p.id in (select id from person) ##', 0],
            [1, 2, false, 'coordinator', 'user', 16],
            [1, 2, false, 'coordinator', '\') or p.id in (select id from person) ##', 0],
            [1, 2, false, 'coordinator', 'd', 17],
            [1, 2, false, 'coordinator', '@', 3],
            [1, 2, false, 'coordinator', 'smith', 0],
            [1, 2, false, 'coordinator', 'art', 1]
        ]
        ]);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetPersonInvalid()
    {
        $this->markTestSkipped("Failed");
        $personInst = $this->personService->find($this->invalidUserId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetOrganizationInvalid()
    {
        $this->markTestSkipped("Failed");
        $organization = $this->orgService->find($this->invalidOrganization);
    }
}
