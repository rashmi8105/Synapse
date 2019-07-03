<?php

class OrgPermissionsetFeaturesRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionsetFeaturesRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionsetFeatures');
    }

    /**
     * This will check to see if the query returns the correct permissions
     * for each feature given an intersection for certain features
     */
    public function testGetUnionOfAllPermittedFeaturesBasedOffOfPermissionSetIds()
    {
        $this->specify("Test to get all of a group's generational information ", function ($permissionSetArray, $organizationId, $specificFeature, $expectedResults) {
            $returnValues = $this->orgPermissionsetFeaturesRepository->getFeaturePermissions($permissionSetArray, $organizationId, $specificFeature);

            $this->assertEquals($returnValues, $expectedResults);

        }, ["examples" =>
            [
                //Get email permissions for the permission set ID
                [
                    [1411],
                    203,
                    7,
                    [

                        'feature_id' => '7',
                        'private_create' => '1',
                        'teams_create' => '1',
                        'public_create' => '1',
                        'public_view' => '1',
                        'teams_view' => '1',
                        'reason_referrals_private_create' => null,
                        'reason_referrals_teams_create' => null,
                        'reason_referrals_public_create' => null,
                        'reason_referrals_teams_view' => null,
                        'reason_referrals_public_view' => null,
                    ]

                ],
                //Get referral permissions for the permission set ID. This permission set has no direct referral permissions.
                [
                    [583],
                    99,
                    1,
                    [
                        'feature_id' => '1',
                        'private_create' => '0',
                        'teams_create' => '0',
                        'public_create' => '0',
                        'public_view' => '0',
                        'teams_view' => '0',
                        'reason_referrals_private_create' => '0',
                        'reason_referrals_teams_create' => '0',
                        'reason_referrals_public_create' => '1',
                        'reason_referrals_teams_view' => '0',
                        'reason_referrals_public_view' => '1',
                    ]
                ],
                //Get referrals permissions for the permission set ID. This permission set has direct & reason routed permissions.
                [
                    [127],
                    23,
                    1,
                    [
                        'feature_id' => '1',
                        'private_create' => '1',
                        'teams_create' => '0',
                        'public_create' => '1',
                        'public_view' => '1',
                        'teams_view' => '0',
                        'reason_referrals_private_create' => '1',
                        'reason_referrals_teams_create' => '0',
                        'reason_referrals_public_create' => '1',
                        'reason_referrals_teams_view' => '0',
                        'reason_referrals_public_view' => '1',

                    ]
                ]
            ]
        ]);
    }

    public function testGetStudentsForFeature()
    {

        $this->specify("Verify the functionality of the method getStudentsForFeature", function ($orgId, $facultyId, $featureName, $requestedStudentIds, $orgAcademicYearId,  $expectedResults) {

            $results = $this->orgPermissionsetFeaturesRepository->getStudentsForFeature($orgId, $facultyId, $featureName,  $requestedStudentIds, $orgAcademicYearId);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                [
                    196,
                    4614715,
                    "Referrals",
                    [4627110, 4627112, 4627113, 4627114, 4627115, 4627116, 4627117, 4874487, 9999999],
                    98,

                    [
                        0 => [
                            'student_id' => '4627110',
                            'firstname' => 'Scott',
                            'lastname' => 'Alexander'
                        ],
                        1 => [
                            'student_id' => '4627112',
                            'firstname' => 'Mohamed',
                            'lastname' => 'Wallace'
                        ],
                        2 => [
                            'student_id' => '4627113',
                            'firstname' => 'Colby',
                            'lastname' => 'Griffin'
                        ],
                        3 => [
                            'student_id' => '4627114',
                            'firstname' => 'Danny',
                            'lastname' => 'West'
                        ],
                        4 => [
                            'student_id' => '4627115',
                            'firstname' => 'Leonel',
                            'lastname' => 'Cole'
                        ],
                        5 => [
                            'student_id' => '4627116',
                            'firstname' => 'Kayson',
                            'lastname' => 'Hayes'
                        ],
                        6 => [
                            'student_id' => '4627117',
                            'firstname' => 'Warren',
                            'lastname' => 'Chavez'
                        ],
                        7 => [
                            'student_id' => '4874487',
                            'firstname' => 'Macey',
                            'lastname' => 'Hogan'
                        ]
                    ]
                ],
                [
                    196,
                    4614715,
                    "Notes",
                    [4627110, 4627112, 4627113, 4627114, 4627115, 4627116, 4627117, 4874487, 9999999],
                    98,
                    [
                        0 => [
                            'student_id' => '4627110',
                            'firstname' => 'Scott',
                            'lastname' => 'Alexander'
                        ],
                        1 => [
                            'student_id' => '4627112',
                            'firstname' => 'Mohamed',
                            'lastname' => 'Wallace'
                        ],
                        2 => [
                            'student_id' => '4627113',
                            'firstname' => 'Colby',
                            'lastname' => 'Griffin'
                        ],
                        3 => [
                            'student_id' => '4627114',
                            'firstname' => 'Danny',
                            'lastname' => 'West'
                        ],
                        4 => [
                            'student_id' => '4627115',
                            'firstname' => 'Leonel',
                            'lastname' => 'Cole'
                        ],
                        5 => [
                            'student_id' => '4627116',
                            'firstname' => 'Kayson',
                            'lastname' => 'Hayes'
                        ],
                        6 => [
                            'student_id' => '4627117',
                            'firstname' => 'Warren',
                            'lastname' => 'Chavez'
                        ],
                        7 => [
                            'student_id' => '4874487',
                            'firstname' => 'Macey',
                            'lastname' => 'Hogan'
                        ]
                    ]
                ],
                [
                    196,
                    4614715,
                    "Primary Campus Connection Referral Routing",
                    [4627110, 4627112, 4627113, 4627114, 4627115, 4627116, 4627117, 4874487, 9999999],
                    98,
                    []
                ],
                [
                    196,
                    4614715,
                    "Notes",
                    [4627110, 4627112, 4627113, 4627114, 4627115, 4627116, 4627117, 4874487, 9999999],
                    -1,
                    []
                ]
            ]
        ]);
    }

}
