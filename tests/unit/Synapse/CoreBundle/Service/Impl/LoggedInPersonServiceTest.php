<?php
namespace Synapse\CoreBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\CoreBundle\Entity\EbiConfig;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\MultiCampusBundle\Repository\OrgUsersRepository;
use Synapse\RestBundle\Entity\OrgPermissionSetDto;

class LoggedInPersonServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testGetIsMultiCampusUser()
    {
        $this->specify("Test getIsMultiCampusUser", function ($loggedInUserId,$organizationId,$studentFacultyOrganizationId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'findBy', 'getConflictPersonsByRole']);
            $mockPerson = $this->getMock('Person', ['getId', 'getUsername', 'getExternalId']);
            $mockPerson->method('getId')->willReturn($loggedInUserId);
            $mockPerson->method('getUsername')->willReturn('Test@mailinator.com');
            $mockPerson->method('getExternalId')->willReturn('EX123');
            $mockPersonRepository->method('find')->willReturn($mockPerson);
            $mockPersonRepository->method('findBy')->willReturn($mockPerson);
            $mockPersonRepository->method('getConflictPersonsByRole')->willReturn([['studentOrg' => $studentFacultyOrganizationId], ['facultyOrg' => $studentFacultyOrganizationId]]);

            // Mock OrgUsersRepository
            $mockOrgUsersRepository = $this->getMock('OrgUsersRepository', ['findBy']);
            $mockOrgUsers = $this->getMock('OrgUsers', ['getId', 'getOrganization']);
            $mockOrgUsers->method('getId')->willReturn(1);
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($organizationId);
            $mockOrgUsers->method('getOrganization')->willReturn($mockOrganization);
            $mockOrgUsersRepository->method('findBy')->willReturn([$mockOrgUsers]);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [PersonRepository::REPOSITORY_KEY,$mockPersonRepository],
                    [OrgUsersRepository::REPOSITORY_KEY,$mockOrgUsersRepository],
                ]);

            $loggedInPersonService = new LoggedInPersonService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $loggedInPersonService->getIsMulticampusUser($loggedInUserId);
            $this->assertEquals($expectedResult, $results);
            // var_dump($results);die;

        }, [
            'examples' => [
                // Test01 - If logged in user id and student faculty organization id are not same, will return multi campus user as true
                [1, 2, 3, true],
                // Test02 - If logged in user id and student faculty organization id are same, will return multi campus user as empty string
                [1, 2, 2, ''],
            ]
        ]);
    }

    public function testGetUserTierType()
    {
        $this->specify("Test getUserTierType", function ($loggedInUserId, $tierType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock OrgUsersRepository
            $mockOrgUsersRepository = $this->getMock('OrgUsersRepository', ['findBy']);
            $mockOrgUsers = $this->getMock('OrgUsers', ['getOrganization']);
            $mockOrganization = $this->getMock('Organization', ['getTier']);
            $mockOrganization->method('getTier')->willReturn($tierType);
            $mockOrgUsers->method('getOrganization')->willReturn($mockOrganization);
            $mockOrgUsersRepository->method('findBy')->willReturn([$mockOrgUsers]);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgUsersRepository::REPOSITORY_KEY,$mockOrgUsersRepository],
                ]);

            $loggedInPersonService = new LoggedInPersonService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $loggedInPersonService->getUserTierType($loggedInUserId);
            $this->assertEquals($expectedResult, $results);

        }, [
            'examples' => [
                // Test01 - Tier type as 1, will return user tier type as primary
                [1, 1, 'primary'],
                // Test01 - Tier type as 2, will return user tier type as secondary
                [1, 2, 'secondary'],
                // Test03 - Invalid tier type will return user tier type as an empty
                [1, 3, ''],
            ]
        ]);
    }

    public function testGetUserPermissionTemplates()
    {
        $this->specify("Test getUserPermissionTemplates", function ($loggedInUserId, $permissionTemplateId, $permissionTemplateName, $userType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock orgPermissionSetService
            $mockOrgPermissionSetService = $this->getMock('OrgPermissionSetService', ['getPermissionSetsByUser']);
            if ($userType == 'Staff') {
                $mockOrgPermissionSetService->method('getPermissionSetsByUser')->willReturn($this->getPermissionSetsByUser($permissionTemplateId, $permissionTemplateName));
            } else {
                $mockOrgPermissionSetService->method('getPermissionSetsByUser')->willReturn(null);
            }

            $mockContainer->method('get')
                ->willReturnMap([
                    [OrgPermissionsetService::SERVICE_KEY,$mockOrgPermissionSetService],
                ]);

            $loggedInPersonService = new LoggedInPersonService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $loggedInPersonService->getUserPermissionTemplates($loggedInUserId, $userType);
            $this->assertEquals($expectedResult, $results);

        }, [
            'examples' => [
                // Test01 - If user type as Staff, will return user permission templates details array
                [1, 2, 'Test', 'Staff', $this->getPermissionSetsByUserResponse(2, 'Test', 'Staff')],
                // Test02 - If user type as Coordinator, will return user permission templates array with only user type
                [1, 2, 'Test', 'Coordinator', $this->getPermissionSetsByUserResponse(null, null, 'Coordinator')],
                // Test03 - If user type as Student, will return user permission templates array with only user type
                [1, 2, 'Test', 'Student', $this->getPermissionSetsByUserResponse(null, null, 'Student')],
                // Test04 - If user type as null|invalid, will return user permission templates array with default user type as student
                [1, 2, 'Test', null, $this->getPermissionSetsByUserResponse(null, null, 'Student')],
            ]
        ]);
    }

    private function getPermissionSetsByUser($permissionTemplateId, $permissionTemplateName)
    {
        $permissionTemplates = [];
        $orgPermissionSetDto = new OrgPermissionSetDto();
        $orgPermissionSetDto->setPermissionTemplateId($permissionTemplateId);
        $orgPermissionSetDto->setPermissionTemplateName($permissionTemplateName);
        $permissionTemplates['permission_templates'][] = $orgPermissionSetDto;
        return $permissionTemplates;
    }

    private function getPermissionSetsByUserResponse($permissionTemplateId, $permissionTemplateName, $userType)
    {

        $permissionTemplates['permission'] = $userType;
        $permissionTemplates['templates'] = [];

        if (isset($permissionTemplateId) && isset($permissionTemplateName)) {
            $permissionTemplates['templates'] = [
                0 => [
                    'permission_template_id' => $permissionTemplateId,
                    'permission_template_name' => $permissionTemplateName
                ]
            ];
        }
        return $permissionTemplates;
    }

    public function testGetOrgPersonCourseTabPermission()
    {
        $this->specify("Test getOrgPersonCourseTabPermission", function ($loggedInUserId, $organizationId, $userType, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock OrgCoursesRepository
            $mockOrgCourseRepository = $this->getMock('OrgCoursesRepository', ['getCoursesForFaculty']);
            if ($userType == 'Coordinator' || $userType == 'Staff') {
                $mockOrgCourseRepository->method('getCoursesForFaculty')->willReturn(['college_code' => 'OXF01', 'dept_code' => 'CS01']);
            } else {
                $mockOrgCourseRepository->method('getCoursesForFaculty')->willReturn([]);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgCoursesRepository::REPOSITORY_KEY,$mockOrgCourseRepository],
                ]);

            $loggedInPersonService = new LoggedInPersonService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $loggedInPersonService->getOrgPersonCourseTabPermission($loggedInUserId, $organizationId, $userType);
            $this->assertEquals($expectedResult, $results);

        }, [
            'examples' => [
                // Test01 - If user type as Staff, will return organization person course tab permission as true
                [1, 2, 'Staff', true],
                // Test02 - If user type as Coordinator, will return organization person course tab permission as true
                [1, 2, 'Coordinator', true],
                // Test03 - For any other user type except Staff and Coordinator, will return organization person course tab permission as false
                [1, 2, 'Student', false],
            ]
        ]);
    }

    public function testGetPrivacyPolicy()
    {
        $this->specify("Test getPrivacyPolicy", function ($loggedInUserId, $organizationId, $userType, $isAccepted, $IsPrivacyPolicyAcceptedDate, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock OrgPersonFacultyRepository
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['findOneBy']);

            if ($userType == 'Coordinator' || $userType == 'Staff') {
                $mockOrgPersonFaculty = $this->getMock('OrgPersonFaculty', ['getId', 'getIsPrivacyPolicyAccepted', 'getPrivacyPolicyAcceptedDate']);
                $mockOrgPersonFaculty->method('getIsPrivacyPolicyAccepted')->willReturn($isAccepted);
                if ($IsPrivacyPolicyAcceptedDate) {
                    $mockOrgPersonFaculty->method('getPrivacyPolicyAcceptedDate')->willReturn(new \DateTime('2017-06-06 11:00:00'));
                } else {
                    $mockOrgPersonFaculty->method('getPrivacyPolicyAcceptedDate')->willReturn(null);
                }
                $mockOrgPersonFacultyRepository->method('findOneBy')->willReturn($mockOrgPersonFaculty);
            }

            // Mock OrgPersonStudentRepository
            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', ['findOneBy']);
            if ($userType == 'Student') {
                $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', ['getId', 'getIsPrivacyPolicyAccepted', 'getPrivacyPolicyAcceptedDate']);
                $mockOrgPersonStudent->method('getIsPrivacyPolicyAccepted')->willReturn($isAccepted);
                if ($IsPrivacyPolicyAcceptedDate) {
                    $mockOrgPersonStudent->method('getPrivacyPolicyAcceptedDate')->willReturn(new \DateTime('2017-06-06 11:00:00'));
                } else {
                    $mockOrgPersonStudent->method('getPrivacyPolicyAcceptedDate')->willReturn(null);
                }
                $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            }

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [OrgPersonFacultyRepository::REPOSITORY_KEY,$mockOrgPersonFacultyRepository],
                    [OrgPersonStudentRepository::REPOSITORY_KEY,$mockOrgPersonStudentRepository],
                ]);

            $loggedInPersonService = new LoggedInPersonService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $loggedInPersonService->getPrivacyPolicy($loggedInUserId, $organizationId, $userType);
            $this->assertEquals($expectedResult, $results);

        }, [
            'examples' => [
                // Test01 - If user type as Coordinator and is_accepted as y, will return privacy policy details array
                [1, 2, 'Coordinator', 'y', true, ['is_accepted' => true, 'accepted_date' => new \DateTime('2017-06-06 11:00:00')]],
                // Test02 - If user type as Coordinator and is_accepted as n, will return privacy policy details array
                [1, 2, 'Coordinator', 'n', true, ['is_accepted' => false, 'accepted_date' => new \DateTime('2017-06-06 11:00:00')]],
                // Test03 - If user type as Staff and is_accepted as n, will return privacy policy details array
                [1, 2, 'Staff', 'n', true, ['is_accepted' => false, 'accepted_date' => new \DateTime('2017-06-06 11:00:00')]],
                // Test04 - If user type as Staff and is_accepted as y, will return privacy policy details array
                [1, 2, 'Staff', 'y', true, ['is_accepted' => true, 'accepted_date' => new \DateTime('2017-06-06 11:00:00')]],
                // Test05 - If user type as Student and is_accepted as y, will return privacy policy details array
                [1, 2, 'Student', 'y', true, ['is_accepted' => true, 'accepted_date' => new \DateTime('2017-06-06 11:00:00')]],
                // Test06 - If user type as Student and is_accepted as n, will return privacy policy details array
                [1, 2, 'Student', 'n', true, ['is_accepted' => false, 'accepted_date' => new \DateTime('2017-06-06 11:00:00')]],
                // Test07 - If IsPrivacyPolicyAcceptedDate flag is false, will return privacy policy details array with accepted_date as null
                [1, 2, 'Student', 'n', false, ['is_accepted' => false, 'accepted_date' => null]]
            ]
        ]);
    }
}