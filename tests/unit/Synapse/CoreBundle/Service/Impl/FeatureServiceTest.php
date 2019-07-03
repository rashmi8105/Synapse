<?php

use Synapse\CoreBundle\Service\Impl\FeatureService;

class FeatureServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testVerifyFacultyAccessToStudentForFeature()
    {

        $this->specify("Test to make sure that the person logged in is the person sending the email", function (
            $expectedResult,
            $shareOptionPermission,
            $orgGroupFacultyQueryReturns,
            $orgPermissionsetFeaturesQueryReturns
        ) {
            // Inititializing repository to be mocked

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockOrgPermissionsetFeaturesRepository = $this->getMock('OrgPermissionsetFeatures', array(
                'getFeaturePermissions'
            ));
            $mockOrgGroupFacultyRepository = $this->getMock('OrgGroupFaculty', array(
                'getPermissionsByFacultyStudent'
            ));
            $mockOrgGroupFacultyRepository->expects($this->any())->method('getPermissionsByFacultyStudent')->willReturn($orgGroupFacultyQueryReturns);
            $mockOrgPermissionsetFeaturesRepository->expects($this->any())->method('getFeaturePermissions')->willReturn($orgPermissionsetFeaturesQueryReturns);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:OrgGroupFaculty',
                        $mockOrgGroupFacultyRepository
                    ],
                    [
                        'SynapseCoreBundle:OrgPermissionsetFeatures',
                        $mockOrgPermissionsetFeaturesRepository
                    ]]);

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array('get'));


            // these are injected and set to blank
            $mockLangService = $this->getMock('LanguageMasterService', array());

            $mockActCategoryService = $this->getMock('ActivityCategoryService', array());

            $mockrbacManager = $this->getMock('Manager', array());
            $mockOrgService = $this->getMock('OrganizationService', array());
            $mockPersonService = $this->getMock('PersonService', array());

            $featureService = new FeatureService($mockRepositoryResolver, $mockLogger, $mockLangService, $mockOrgService, $mockPersonService, $mockrbacManager, $mockActCategoryService, $mockContainer);
            $actualResult = $featureService->verifyFacultyAccessToStudentForFeature(1, 1, 1, $shareOptionPermission, 1);

            $this->assertEquals($expectedResult, $actualResult);
        }, [
            'examples' => [
                [
                    true,
                    'email-public-create',
                    [
                        [
                            // returns at least one permissionset
                            'org_permissionset_id' => 1
                        ]
                    ],
                    [
                        'feature_id' => '1',
                        'private_create' => '1',
                        'team_create' => '1',
                        'public_create' => '1',
                        'public_view' => '1',
                        'team_view' => '1',
                    ]
                ],
                [
                    false,
                    'email-public-create',
                    [
                        [
                            'org_permissionset_id' => 1
                        ]
                    ],
                    [
                        'feature_id' => '1',
                        'private_create' => '0',
                        'team_create' => '1',
                        'public_create' => '0',
                        'public_view' => '1',
                        'team_view' => '1',
                    ]
                ],
                [
                    false,
                    'email-public-create',
                    [
                        // simulating that there are no permissionsets
                        // between faculty and student
                    ],
                    [
                        // showing that even if somehow the next repository function
                        // broke and displayed this
                        'feature_id' => '1',
                        'private_create' => '1',
                        'team_create' => '1',
                        'public_create' => '1',
                        'public_view' => '1',
                        'team_view' => '1',
                    ]
                ],

            ]
        ]);
    }
}