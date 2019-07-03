<?php

use Codeception\TestCase\Test;
use Synapse\ReportsBundle\Service\Impl\ReportsService;

class ReportsServiceTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var ReportsService
     */
    private $reportsService;


    public function _before()
    {
        $this->markTestSkipped("AbstractService's repository resolver is not getting instantiated, causing this test to fail. Refreshing cache locally does not fix the issue.");
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->reportsService = $this->container->get(ReportsService::SERVICE_KEY);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testGetRetentionTrackYears()
    {
        $this->specify("Verify the functionality of the method getRetentionTrackYears", function ($organizationId, $reportId, $expectedResult) {
            $results = $this->reportsService->getRetentionTrackYears($organizationId, $reportId);
            verify($results)->equals($expectedResult);

        }, ["examples" =>
            [

                // Current academic year
                [59, null, [184, 194, 94, 204]],
                // Throws Synapse Validation exception. No academic years for the organization
                [4, null, []],
                // No retention tracking groups
                [20, null, [
                    'message' => "Retention tracks have not been selected for any students. The report will not contain any meaningful data."
                ]],
                // NO retention tracking group for the year provided
                [20, 16, [
                    'message' => "There are no students in the retention track for 2015-2016.  The report will not include Intent to Leave and Persistence, Persistence and Retention by Risk, or Top Factors with Correlation to Persistence and Retention, which depend on having students in the retention track."
                ]
                ]
            ]

        ]);
    }

}
