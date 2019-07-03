<?php

/**
 * Class OrgSearchRepositoryTest
 */

use Codeception\TestCase\Test;

class OrgSearchRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\SearchBundle\Repository\OrgSearchRepository
     */
    private $orgSearchRepository;

    private $orgId = 203;

    public function testGetRiskIntentData()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->orgSearchRepository = $this->repositoryResolver->getRepository('SynapseSearchBundle:OrgSearch');
        });

        $this->specify("Verify the functionality of the method getRiskIntentData", function($facultyId, $studentIds, $expectedResultsSize, $riskFlag, $intentFlag){

            $results = $this->orgSearchRepository->getRiskIntentData($facultyId, $studentIds);

            verify(count($results))->equals($expectedResultsSize);
            for($i = 0; $i < count($results); $i++){
                verify($results[$i]['intent_flag'])->notEmpty();
                verify($results[$i]['intent_flag'])->equals($intentFlag);
                verify($results[$i]['risk_flag'])->notEmpty();
                verify($results[$i]['risk_flag'])->equals($riskFlag);
            }
        }, ["examples"=>
            [
                [4878750,'4878808,4878809,4878810,4878811,4878812,4878813,4878814,4878815,4878816,4878817',10,1,1],
                [4883175,'4878808,4878809,4878810,4878811,4878812,4878813,4878814,4878815,4878816,4878817',0,0,0],
                [4878751,'4878808,4878809,4878810,4878811,4878812,4878813,4878814,4878815,4878816,4878817',10,1,1],
            ]
        ]);
    }
}