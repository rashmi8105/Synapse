<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;


class RiskGroupPersonHistoryRepositoryTest extends Test
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
     * @var RiskGroupPersonHistoryRepository
     */
    private $riskGroupPersonHistoryRepository;


    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->riskGroupPersonHistoryRepository = $this->repositoryResolver->getRepository(RiskGroupPersonHistoryRepository::REPOSITORY_KEY);
    }

    public function testIsStudentInValidRiskGroup()
    {
        $this->specify("Verify the functionality of the method isStudentInValidRiskGroup", function ($personId, $currentDateTime, $expectedResults) {
            $result = $this->riskGroupPersonHistoryRepository->isStudentInValidRiskGroup($personId, $currentDateTime);
            verify($result)->equals($expectedResults);

        }, ["examples" =>
            [
                //  Student Doesn't Exist
                [99999999999, '1999-01-01 00:00:00', false],
                //  Risk Group Assignment Doesn't Exist
                [256416, '1999-01-01 00:00:00', false],
                //  Past Risk Model Date
                [4892330, '2059-01-01 00:00:00', false],
                // Student in Valid Risk Group
                [4892330, '1999-01-01 00:00:00', true],
                // Null Student
                [null, '1999-01-01 00:00:00', false],
                // Null datetime
                [4892330, null, false]
            ]
        ]);
    }




}
