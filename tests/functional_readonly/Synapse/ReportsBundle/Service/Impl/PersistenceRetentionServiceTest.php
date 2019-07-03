<?php

use Codeception\TestCase\Test;
use Synapse\ReportsBundle\Service\Impl\PersistenceRetentionService;
use \Symfony\Component\DependencyInjection\Container;

class PersistenceRetentionServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;


    /**
     * @var PersistenceRetentionService
     */
    private $persistenceRetentionService;


    public function _before()
    {
        $this->markTestSkipped("AbstractService's repository resolver is not getting instantiated, causing this test to fail. Refreshing cache locally does not fix the issue.");
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->persistenceRetentionService = $this->container->get(PersistenceRetentionService::SERVICE_KEY);
    }


    public function testGetMeaningfulRetentionVariables()
    {
        $this->specify("Verify the functionality of the method getMeaningfulRetentionVariables", function ($retentionTrackingYearId, $yearLimit, $expectedResult) {
            $result = $this->persistenceRetentionService->getMeaningfulRetentionVariables($retentionTrackingYearId, $yearLimit);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
            [
                [
                    // the return value would have array of retention variables, mapped to the number of years from retention tracking year
                    201516, 202021, [
                        0 => [
                            1 => "Retained to Midyear Year 1" // mid year retentions should have array index as 1
                        ],
                        1 => [
                            0 => "Retained to Start of Year 2",
                            1 => "Retained to Midyear Year 2"
                        ],

                        2 => [
                            0 => "Retained to Start of Year 3",
                            1 => "Retained to Midyear Year 3"
                        ],

                        3 => [
                            0 => "Retained to Start of Year 4",
                            1 => "Retained to Midyear Year 4"
                        ]
                    ]
                ]
            ]
        ]);
    }
}