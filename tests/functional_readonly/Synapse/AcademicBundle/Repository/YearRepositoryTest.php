<?php
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\YearRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

class YearRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var YearRepository
     */
    private $yearRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->yearRepository = $this->repositoryResolver->getRepository(YearRepository::REPOSITORY_KEY);
    }

    public function testListYearIds()
    {
        $this->specify("Verify the functionality of the method listYearIds", function ($expectedResults) {
            $results = $this->yearRepository->listYearIds();
            foreach ($expectedResults as $expectedResult){
                verify($results)->contains($expectedResult);
            }
        }
        , [
                "examples" => [
                    [
                        // Test 01 - Since this function(listYearIds) doesn't take any argument, so we can't have different test cases.
                        [
                            0 =>
                                [
                                'id' => '201415'
                                ],
                            1 =>
                                [
                                    'id' => '201516'
                                ],
                            2 =>
                                [
                                    'id' => '201617'
                                ],
                            3 =>
                                [
                                    'id' => '201718'
                                ],
                            4 =>
                                [
                                    'id' => '201819'
                                ],
                            5 =>
                                [
                                    'id' => '201920'
                                ]
                        ]
                    ]
                ]
            ]);
    }
}