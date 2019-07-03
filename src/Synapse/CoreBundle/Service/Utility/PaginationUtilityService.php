<?php

namespace Synapse\CoreBundle\Service\Utility;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\MathUtilityService;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\DTO\PaginatedSearchResultDTO;


/**
 * Central service for manipulation of pagination values.
 *
 * @DI\Service("pagination_utility_service")
 */
class PaginationUtilityService extends AbstractService
{
    const SERVICE_KEY = 'pagination_utility_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    // Services
    /**
     * @var MathUtilityService
     */
    private $mathUtilityService;

    // Repositories


    /**
     * PaginationUtilityService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @throws \Exception
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->container = $container;

        // Services
        $this->mathUtilityService = $this->container->get(MathUtilityService::SERVICE_KEY);

        // Repository
    }


    /**
     * Sets the current pagination status of the result set
     *
     * @param PaginatedSearchResultDTO $paginatedSearchResultDTO
     * @param $totalRecords
     * @param $recordsPerPage
     * @return PaginatedSearchResultDTO
     */
    public function setResultPaginationDetails($paginatedSearchResultDTO, $totalRecords, $recordsPerPage, $currentPage)
    {
        $totalPages = $this->mathUtilityService->divideAndCeiling($totalRecords, $recordsPerPage);
        $paginatedSearchResultDTO->setTotalPages($totalPages);
        $paginatedSearchResultDTO->setTotalRecords($totalRecords);
        $paginatedSearchResultDTO->setRecordsPerPage($recordsPerPage);
        $paginatedSearchResultDTO->setCurrentPage($currentPage);

        return $paginatedSearchResultDTO;
    }
}
