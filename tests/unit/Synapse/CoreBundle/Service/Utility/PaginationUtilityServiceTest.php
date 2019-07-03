<?php

namespace tests\unit\Synapse\CoreBundle\Service\Util;

use Codeception\TestCase\Test;
use Codeception\Specify;
use Synapse\CoreBundle\DTO\PaginatedSearchResultDTO;
use Synapse\CoreBundle\Service\Utility\MathUtilityService;
use Synapse\CoreBundle\Service\Utility\PaginationUtilityService;
use Synapse\AcademicBundle\EntityDto\CourseSearchResultDTO;


class PaginationUtilityServiceTest extends Test
{
    use Specify;


    public function testSetResultPaginationDetails()
    {
        $this->specify("test setting pagination details", function($expectedResults, $totalRecords = null, $recordsPerPage = null, $currentPage = null, $mockPaginatedSearchResultsDTO = null, $mockCeiling = null){
            //Create all mocks necessary for Service class creation
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockMathUtilityService = $this->getMock('mathUtilityService', ['divideAndCeiling']);

            $mockContainer->method('get')->willReturnMap([
                [MathUtilityService::SERVICE_KEY, $mockMathUtilityService]
            ]);

            $mockMathUtilityService->method('divideAndCeiling')->willReturn($mockCeiling);


            $mockPaginationUtilityService = new PaginationUtilityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $mockPaginationUtilityService->setResultPaginationDetails($mockPaginatedSearchResultsDTO, $totalRecords, $recordsPerPage, $currentPage);
            $this->assertEquals($expectedResults, $results);
        }, [
            'examples' => [
                //Valid test case using PaginatedSearchResultDto
                [
                    $this->generatePaginatedSearchResultDTO(new PaginatedSearchResultDTO(), 100, 10, 10, 1),
                    100,
                    10,
                    1,
                    new PaginatedSearchResultDTO(),
                    10
                ],
                //Valid test case using a DTO that inherits from PaginatedSearchResultDTO
                [
                    $this->generatePaginatedSearchResultDTO(new CourseSearchResultDTO(), 100,  10, 10, 1),
                    100,
                    10,
                    1,
                    new CourseSearchResultDTO(),
                    10
                ],
                //Null values
                [
                    $this->generatePaginatedSearchResultDTO(new PaginatedSearchResultDTO()),
                    null,
                    null,
                    null,
                    new PaginatedSearchResultDTO()
                ],
                // Invalid Values
                [
                    $this->generatePaginatedSearchResultDTO(new PaginatedSearchResultDTO(), "Bird", "Giraffe", "Plane", "Superman"),
                    "Bird",
                    "Plane",
                    "Superman",
                    new PaginatedSearchResultDTO(),
                    "Giraffe"
                ],

            ]
        ]);
    }

    /**
     * @param PaginatedSearchResultDTO $paginatedSearchResultDTO
     * @param null $totalRecords
     * @param null $totalPages
     * @param null $recordsPerPage
     * @param null $currentPage
     * @return mixed
     */
    private function generatePaginatedSearchResultDTO($paginatedSearchResultDTO, $totalRecords = null, $totalPages = null, $recordsPerPage = null, $currentPage = null)
    {
        $paginatedSearchResultDTO->setTotalPages($totalPages);
        $paginatedSearchResultDTO->setTotalRecords($totalRecords);
        $paginatedSearchResultDTO->setRecordsPerPage($recordsPerPage);
        $paginatedSearchResultDTO->setCurrentPage($currentPage);

        return $paginatedSearchResultDTO;
    }
}