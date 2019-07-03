<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MapworksToolBundle\Repository\OrgPermissionsetToolRepository;

class OrgPermissionsetToolRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     *
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrgPermissionsetToolRepository
     */
    private $orgPermissionsetToolRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPermissionsetToolRepository = $this->repositoryResolver->getRepository(OrgPermissionsetToolRepository::REPOSITORY_KEY);
    }

    /**
     * @throws  Exception
     */
    public function testGetToolsWithPermissionsetSelection()
    {
        $this->specify('test get tools for permission selection ', function ($expectedResult, $orgPermissionsetId, $organizationId) {
            $toolSelections=$this->orgPermissionsetToolRepository->getToolsWithPermissionsetSelection($orgPermissionsetId, $organizationId);
            $this->assertEquals(count($expectedResult),count($toolSelections));
            verify($toolSelections)->equals($expectedResult);
        }, [
            'examples' => [
                // fetch mapwork tools for given organization_id and permissionsetId where all selection = true
                [
                    [
                        [
                            'tool_id' => 1,
                            'tool_name' => 'Top Issues',
                            'short_code' => 'T-I',
                            'selection' => 1,
                            'can_access_with_aggregate_only_permission' => 1,
                        ],
                        [
                            'tool_id' => 2,
                            'tool_name' => 'Top Issues 2',
                            'short_code' => 'T-II',
                            'selection' => 1,
                            'can_access_with_aggregate_only_permission' => 1,
                        ],
                        [
                            'tool_id' => 3,
                            'tool_name' => 'Top Issues 3',
                            'short_code' => 'T-III',
                            'selection' => 1,
                            'can_access_with_aggregate_only_permission' => 1,
                        ]
                    ],
                    1448, //$orgPermissionsetId
                    204, // organizationId
                ],
                // fetch mapwork tools for given organization_id and permissionsetId where selection true and false both cases
                [
                    [
                        [
                            'tool_id' => 1,
                            'tool_name' => 'Top Issues',
                            'short_code' => 'T-I',
                            'selection' => 1,
                            'can_access_with_aggregate_only_permission' => 1,
                        ],
                        [
                            'tool_id' => 2,
                            'tool_name' => 'Top Issues 2',
                            'short_code' => 'T-II',
                            'selection' => 0,
                            'can_access_with_aggregate_only_permission' => 1,
                        ],
                        [
                            'tool_id' => 3,
                            'tool_name' => 'Top Issues 3',
                            'short_code' => 'T-III',
                            'selection' => 0,
                            'can_access_with_aggregate_only_permission' => 1,
                        ]
                    ],
                    1449,
                    204,
                ],
                // fetch mapwork tools for given organization_id and permissionsetId where all selection = false
                [
                    [
                        [
                            'tool_id' => 1,
                            'tool_name' => 'Top Issues',
                            'short_code' => 'T-I',
                            'selection' => 0,
                            'can_access_with_aggregate_only_permission' => 1,
                        ],
                        [
                            'tool_id' => 2,
                            'tool_name' => 'Top Issues 2',
                            'short_code' => 'T-II',
                            'selection' => 0,
                            'can_access_with_aggregate_only_permission' => 1,
                        ],
                        [
                            'tool_id' => 3,
                            'tool_name' => 'Top Issues 3',
                            'short_code' => 'T-III',
                            'selection' => 0,
                            'can_access_with_aggregate_only_permission' => 1,
                        ]
                    ],
                    1435,
                    204,
                ],

            ]
        ]);
    }
}
