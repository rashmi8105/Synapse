<?php
namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;

class ActivityCategoryServiceTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    public function testGetActivityCategory()
    {
        $this->specify("Get reason categories", function ($activityParents, $allActivityChild, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);

            $mockActivityCategoryRepository = $this->getMock('ActivityCategoryRepository', ['getActivityCategoryList']);
            $mockActivityCategoryRepository->expects($this->at(0))->method('getActivityCategoryList')->willReturn($activityParents);
            $mockActivityCategoryRepository->expects($this->at(1))->method('getActivityCategoryList')->willReturn($allActivityChild);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                ]
            );


            $academicTermService = new ActivityCategoryService($mockRepositoryResolver, $mockLogger);
            $result = $academicTermService->getActivityCategory();

            $this->assertEquals($result, $expectedResult);

        }, [
            'examples' => [
                // Example1: Get reason categories
                [
                    [
                        [
                            'group_item_key' => 1,
                            'group_item_value' => 'test parent description1',
                        ],
                        [
                            'group_item_key' => 2,
                            'group_item_value' => 'test parent description2',
                        ]
                    ],
                    [
                        [
                            'subitem_key' => 1,
                            'subitem_value' => 'test child description1',
                            'parent' => 1,
                        ],
                        [
                            'subitem_key' => 2,
                            'subitem_value' => 'test child description2',
                            'parent' => 1,
                        ]
                    ],
                    [
                        'category_groups' => [
                            ['group_item_key' => 1,
                                'group_item_value' => 'test parent description1',
                                'subitems' => [
                                    [
                                        'subitem_key' => 1,
                                        'subitem_value' => 'test child description1'
                                    ],
                                    [
                                        'subitem_key' => 2,
                                        'subitem_value' => 'test child description2'
                                    ]
                                ]
                            ],
                            ['group_item_key' => 2,
                                'group_item_value' => 'test parent description2',
                                'subitems' => [
                                    [
                                        'subitem_key' => 1,
                                        'subitem_value' => 'test child description1'
                                    ],
                                    [
                                        'subitem_key' => 2,
                                        'subitem_value' => 'test child description2'
                                    ]
                                ]
                            ]
                        ]
                    ]

                ],
                // Example2: Will return nothing when parents reasons categories aren't found
                [
                    [],
                    [
                        [
                            'subitem_key' => 1,
                            'subitem_value' => 'test child description1',
                            'parent' => 1,
                        ],
                        [
                            'subitem_key' => 2,
                            'subitem_value' => 'test child description2',
                            'parent' => 1,
                        ]
                    ],
                    []

                ],
                // Example3: Will return only parents reasons categories without child reasons category
                [
                    [
                        [
                            'group_item_key' => 1,
                            'group_item_value' => 'test parent description1',
                        ],
                        [
                            'group_item_key' => 2,
                            'group_item_value' => 'test parent description2',
                        ]
                    ],
                    [],
                    [
                        'category_groups' => [
                            ['group_item_key' => 1,
                                'group_item_value' => 'test parent description1',
                                'subitems' => []
                            ],
                            ['group_item_key' => 2,
                                'group_item_value' => 'test parent description2',
                                'subitems' => []
                            ]
                        ]
                    ]

                ],
                // Example4: Checking about null data
                [
                    [],
                    [],
                    []

                ],
            ]
        ]);
    }

}