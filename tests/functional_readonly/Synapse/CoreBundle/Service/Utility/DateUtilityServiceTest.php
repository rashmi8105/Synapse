<?php

use Codeception\TestCase\Test;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;

class DateUtilityServiceTest extends Test
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
     * @var \Synapse\CoreBundle\Service\Utility\DateUtilityService
     */
    private $dateUtilityService;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
    }

    public function testGetDaylightSavingsTimeOffsetAdjustment()
    {
        $this->specify('testGetDaylightSavingsTimeOffsetAdjustment', function($datetimezone, $startDate, $endDate, $expectedResults){

            $functionResults = $this->dateUtilityService->getDaylightSavingsTimeOffsetAdjustment($datetimezone, $startDate, $endDate);
            verify($functionResults)->equals($expectedResults);

        }, ['examples' =>
            [
                //Gap overlaps Daylight Savings Time
                [
                    new DateTimeZone('US/Central'),
                    new DateTime('2017-03-10 00:00:00'),
                    new DateTime('2017-03-15 00:00:00'),
                    -1
                ],
                //Gap overlaps Daylight Savings Time
                [
                    new DateTimeZone('US/Central'),
                    new DateTime('2017-08-10 00:00:00'),
                    new DateTime('2017-12-15 00:00:00'),
                    1
                ],
                //Timezone does not observe Daylight Savings Time
                [
                    new DateTimeZone('US/Arizona'),
                    new DateTime('2017-03-10 00:00:00'),
                    new DateTime('2017-03-11 00:00:00'),
                    0
                ]
            ]
        ]);
    }

    public function testGetFirstDayOfWeekForDatetime()
    {
        $this->specify('test getFirstDayOfWeekForDatetime', function($datetime, $expectedResults){
            $functionResults = $this->dateUtilityService->getFirstDayOfWeekForDatetime($datetime);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            [
                new DateTime('2017-03-10 00:00:00'),
                new DateTime('2017-03-05 00:00:00')
            ],
            [
                new DateTime('2017-10-10 00:00:00'),
                new DateTime('2017-10-08 00:00:00')
            ]
        ]]);
    }
}