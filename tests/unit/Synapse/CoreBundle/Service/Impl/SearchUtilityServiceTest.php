<?php
namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Synapse\CoreBundle\Service\Utility\SearchUtilityService;

class SearchUtilityServiceTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    public function testMakeSqlQuery()
    {
        $this->specify("Test make SQL query,returns appended query", function ($appendValue, $appendKey, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $searchUtilityService = new SearchUtilityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $searchUtilityService->makeSqlQuery($appendValue, $appendKey);
            $this->assertEquals($result, $expectedResult);


        }, [
                'examples' => [
                    // Example 1: Will return empty query string
                    [
                        null,
                        -1,
                        ""
                    ],
                    // Example 2: Will return appended query string
                    [
                        'departmentId',
                        'dept_code',
                        "dept_code = 'departmentId'"
                    ],
                    // Example 3: Will return appended 'in' query string
                    [
                        'section1,section2,section3,section4',
                        'section_ids',
                        "section_ids in ('section1','section2','section3','section4')"
                    ],
                    // Example 4: Will return empty query string
                    [
                        null,
                        null,
                        ""
                    ]
                ]
            ]
        );
    }


}