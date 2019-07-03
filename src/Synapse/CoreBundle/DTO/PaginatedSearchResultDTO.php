<?php

namespace Synapse\CoreBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PaginatedSearchResultDTO
 *
 * @package Synapse\CoreBundle\DTO
 */
Class PaginatedSearchResultDTO
{

    /**
     * Total number of pages in paginated result set.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalPages;

    /**
     *  Total number of records in the result set.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalRecords;

    /**
     * Records per page of the paginated result set.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $recordsPerPage;

    /**
     * Current page number of the paginated result set
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $currentPage;

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     * @param int $totalRecords
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    /**
     * @return int
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * @param int $recordsPerPage
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

}