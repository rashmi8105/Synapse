<?php

use Codeception\TestCase\Test;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionRepository;

class OrgPersonStudentRetentionRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrgPersonStudentRetentionRepository
     */
    private $orgPersonStudentRetentionRepository;


    public function _before(){
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPersonStudentRetentionRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionRepository::REPOSITORY_KEY);
    }

    public function testGetAggregatedCountsOfRetentionDataByStudentList()
    {

        $this->specify("Verify the functionality of the method getAggregatedCountsOfRetentionDataByStudentList", function ($retentionTrackingYear, $organizationId, $studentIds, $expectedResults)
        {
            $results = $this->orgPersonStudentRetentionRepository->getAggregatedCountsOfRetentionDataByStudentList($retentionTrackingYear, $organizationId, $studentIds);
            verify($results)->equals($expectedResults);

        }, ["examples" =>
            [
                // gets aggregated Retention data for students of organization_id  = 59
                ['201516', 59, [4614735], [
                    ["years_from_retention_track" => 0, "beginning_year_numerator_count" => 0, "midyear_numerator_count" => 1, "denominator_count" => 1],
                    ["years_from_retention_track" => 1, "beginning_year_numerator_count" => 1, "midyear_numerator_count" => 1, "denominator_count" => 1],
                    ["years_from_retention_track" => 2, "beginning_year_numerator_count" => 1, "midyear_numerator_count" => 1, "denominator_count" => 1]

                ]],

                // gets aggregated Retention data for students of organization_id  = 14
                ['201516', 14, [406703], []]
            ]
        ]);
    }
}