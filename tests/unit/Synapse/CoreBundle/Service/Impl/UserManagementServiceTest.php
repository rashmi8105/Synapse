<?php
use Synapse\CoreBundle\Service\Impl\UserManagementService;

class UserManagementServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testIsStudentMemberOfCurrentAcademicYear()
    {
        $this->specify("Validate Archived Students by isStudentMemberOfCurrentAcademicYear", function ($studentId, $orgId, $orgPersonStudentYearId, $ebiConfigValue) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'error'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $orgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $academicYearService = $this->getMock('AcademicYearService', array(
                'findCurrentAcademicYearForOrganization',
                'getCurrentOrgAcademicYearId'
            ));

            $academicYearService->method('getCurrentOrgAcademicYearId')->willReturn(1);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'academicyear_service',
                        $academicYearService
                    ],
                    [
                        'org_service',
                        $orgService
                    ]
                ]);
            $academicYearService->method('findCurrentAcademicYearForOrganization')
                ->willReturn(3);
            $orgService->method('find')
                ->willReturn(9);

            $orgPersonStudentYear = $this->getMock('OrgPersonStudentYear', array(
                'findOneBy'
            ));
            $orgPersonStudent = $this->getMock('OrgPersonStudent', array(
                'findOneBy'
            ));
            $ebiConfigRepository = $this->getMock('EbiConfigRepository', array(
                'findOneBy'
            ));

            $ebiConfigEntity = $this->getMock('EbiConfig', array(
                'getValue'
            ));
            $ebiConfigRepository->method('findOneBy')
                ->willReturn($ebiConfigEntity);

            $ebiConfigEntity->method('getValue')
                ->willReturn($ebiConfigValue);
            $orgPersonStudentYear->method('findOneBy')
                ->willReturn($orgPersonStudentYearId);

            $orgPersonStudent->method('findOneBy')
                ->willReturn($orgPersonStudentYearId);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:OrgPersonStudentYear',
                        $orgPersonStudentYear
                    ],
                    [
                        'SynapseCoreBundle:EbiConfig',
                        $ebiConfigRepository
                    ],
                    [
                        'SynapseCoreBundle:OrgPersonStudent',
                        $orgPersonStudent
                    ],
                ]);


            $userManagementService = new UserManagementService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $studentStatus = $userManagementService->isStudentMemberOfCurrentAcademicYear($studentId, $orgId);
            if ($orgPersonStudentYearId) {
                $this->assertEquals(true, $studentStatus);
            } else {
                $this->assertEquals(false, $studentStatus);
            }
        }, [
            'examples' => [
                [
                    314,
                    2,
                    NULL,
                    1
                ],
                [
                    314,
                    2,
                    2,
                    1
                ],
                [
                    314,
                    2,
                    2,
                    NULL
                ]
            ]
        ]);
    }

}

?>