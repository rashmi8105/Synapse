<?php
namespace tests\unit\Synapse\RestBundle\Utility;

use Codeception\Specify;
use Codeception\Test\Unit;
use Synapse\RestBundle\Utility\RestUtilityService;


class RestUtilityServiceTest extends Unit
{
    use Specify;

    public function testGetSortColumnAndDirection()
    {
        $this->specify("", function($sortBy, $expectedResult) {
            $restUtilityService = new RestUtilityService();
            $arrayReturned = $restUtilityService->getSortColumnAndDirection($sortBy);

            verify($arrayReturned)->equals($expectedResult);

        }, ['examples' => [
            ['', [null, null]],
            ['column1', ['column1', 'ASC']],
            ['+column1', ['column1', 'ASC']],
            ['-column1', ['column1', 'DESC']]
        ]]);
    }
}