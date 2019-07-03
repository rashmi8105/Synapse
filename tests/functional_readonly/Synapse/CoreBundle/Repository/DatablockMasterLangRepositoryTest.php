<?php


use Codeception\TestCase\Test;
use Synapse\CoreBundle\Repository\DatablockMasterLangRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Symfony\Component\DependencyInjection\Container;


class DatablockMasterLangRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var DatablockMasterLangRepository
     */
    private $datablockMasterLangRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->datablockMasterLangRepository = $this->repositoryResolver->getRepository(DatablockMasterLangRepository::REPOSITORY_KEY);
    }

    public function testGetDatablocks()
    {
        $this->specify("Verify the functionality of the method getDatablocks", function ($orgId, $expectedCount, $expectedResultSet) {
            $results = $this->datablockMasterLangRepository->getDatablocks($orgId);
            verify(count($results))->equals($expectedCount);
            foreach ($expectedResultSet as $expectedResult) {
                verify($results)->contains($expectedResult);
            }
        }, ["examples" =>
            [
                [
                    'profile',
                    18,
                    [
                        [
                            'datablock_id' => 12,
                            'datablock_name' => 'BasicStudentInfo',
                            'profile_item_count' => 1
                        ],
                        [
                            'datablock_id' => 13,
                            'datablock_name' => 'Demographic',
                            'profile_item_count' => 7
                        ],
                        [
                            'datablock_id' => 14,
                            'datablock_name' => 'Admissions',
                            'profile_item_count' => 5
                        ],
                        [
                            'datablock_id' => 15,
                            'datablock_name' => 'Academic Record',
                            'profile_item_count' => 3
                        ],
                        [
                            'datablock_id' => 16,
                            'datablock_name' => 'Demographic Record',
                            'profile_item_count' => 4
                        ],
                        [
                            'datablock_id' => 17,
                            'datablock_name' => 'Academic Record-PreYear',
                            'profile_item_count' => 3
                        ],
                        [
                            'datablock_id' => 18,
                            'datablock_name' => 'Academic Record-Start',
                            'profile_item_count' => 3
                        ],
                        [
                            'datablock_id' => 19,
                            'datablock_name' => 'Academic Record-Mid',
                            'profile_item_count' => 1
                        ],
                        [
                            'datablock_id' => 20,
                            'datablock_name' => 'Academic Record-End',
                            'profile_item_count' => 6
                        ],
                        [
                            'datablock_id' => 21,
                            'datablock_name' => 'Admissions-Dates',
                            'profile_item_count' => 4
                        ],
                        [
                            'datablock_id' => 22,
                            'datablock_name' => 'Admissions-HS',
                            'profile_item_count' => 4
                        ],
                        [
                            'datablock_id' => 23,
                            'datablock_name' => 'Admissions-Tests',
                            'profile_item_count' => 22
                        ],
                        [
                            'datablock_id' => 24,
                            'datablock_name' => 'FinancialAid',
                            'profile_item_count' => 5
                        ],
                        [
                            'datablock_id' => 25,
                            'datablock_name' => 'Contact',
                            'profile_item_count' => 5
                        ],
                        [
                            'datablock_id' => 28,
                            'datablock_name' => 'Institutional Research',
                            'profile_item_count' => 3
                        ],
                        [
                            'datablock_id' => 29,
                            'datablock_name' => 'Functional',
                            'profile_item_count' => 0
                        ],
                    ]
                ]
            ]
        ]);
    }
}