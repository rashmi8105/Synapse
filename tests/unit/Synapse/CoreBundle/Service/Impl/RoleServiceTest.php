<?php
use Synapse\CoreBundle\Service\Impl\RoleService;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
class RoleServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testGetRolesForUser()
    {
        $this->specify("Test to get roles for user", function ($personId, $expectedResult) {


            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationRoleRepository->method('findOneBy')->willReturnCallback(function ($criteriaArray) {

                    if ($criteriaArray['person'] == 1) {
                        return $this->getMock('OrganizationRole', ['getId']);
                    } else {
                       return null;
                    }

                });

            // Mock OrgPersonFacultyRepository
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy']);
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturnCallback(function ($criteriaArray) {

                    if ($criteriaArray['person'] == 2 || $criteriaArray['person'] == 1 ) {
                        return $this->getMock('OrgPersonFaculty', ['getId']);
                    } else {
                        return null;
                    }

                });
            // Mock OrgPersonStudentRepository
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturnCallback(function ($criteriaArray) {

                    if ($criteriaArray['person'] == 3) {
                        return $this->getMock('OrgPersonStudent', ['getId']);
                    } else {
                        return null;
                    }

                });
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository],
                    [OrgPersonStudentRepository::REPOSITORY_KEY, $mockOrgPersonStudentRepository],
                ]);

            try {
                $roleService = new RoleService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $roleService->getRolesForUser($personId);
                 $this->assertEquals($results, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals('Coordinator not found!', $e->getMessage());
            }

        }, [
            'examples' => [
                // Test 01 - passing invalid person id will return false for coordinator, faculty and student
                [
                    -1,
                    ['coordinator' => false,'faculty' => false, 'student' => false]
                ],
                // Test 02 - passing person id as 1 will return true for coordinator, faculty and false for student
                [
                    1,
                    ['coordinator' => true,'faculty' => true, 'student' => false]
                ],
                // Test 03 - passing person id as 2 will return false for coordinator, student and true for faculty
                [
                    2,
                    ['coordinator' => false,'faculty' => true, 'student' => false]
                ],
                // Test 04 - passing person id as 3 will return false for coordinator, faculty and true for student
                [
                    3,
                    ['coordinator' => false,'faculty' => false, 'student' => true]
                ]
            ]
        ]);
    }

    public function testHasCoordinatorOmniscience()
    {
        $this->specify("Test to hasCoordinatorOmniscience", function ($personId, $expectedResult) {


            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            // Mock EbiConfigRepository
            $mockEbiConfigRepository= $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockEbiConfig = $this->getMock('EbiConfig',['getId','getValue']);
            $mockEbiConfig->method('getValue')->willReturn(1);
            $mockEbiConfigRepository->method('findOneBy')->willReturn($mockEbiConfig);

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $organizationRole = $this->getMock('OrganizationRole',['getId']);
            if($personId > 0) {
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn($organizationRole);
            }
            else{
                $mockOrganizationRoleRepository->method('findOneBy')->willReturn(null);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                ]);

                $roleService = new RoleService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $roleService->hasCoordinatorOmniscience($personId);
                $this->assertEquals($results, $expectedResult);
        }, [
            'examples' => [

                // Test 01 - passing invalid person id will return person has not coordinator omniscience.
                [
                    -1,
                    false
                ],
                // Test 02 - passing valid person id will return person has coordinator omniscience.
                [
                    1,
                    true
                ],
                // Test 01 - passing person id as null will return person has not coordinator omniscience.
                [
                    null,
                    false
                ],
            ]
        ]);
    }
}