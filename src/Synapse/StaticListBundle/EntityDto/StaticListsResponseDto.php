<?php
namespace Synapse\StaticListBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StaticListsResponseDto
{    
    /**
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalRecords;
    
    /**
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalPages;
    
    /**
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $recordsPerPage;
    
    /**
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $currentPage;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\StaticListBundle\EntityDto\StaticListDto>")
     *     
     */
    private $staticLists;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\StaticListBundle\EntityDto\StaticListDetailsDto>")
     *     
     */
    private $staticListDetails;

    /**
     *
     * @param Object $staticlistDetails            
     */
    public function setStaticLists($staticLists)
    {
        $this->staticLists = $staticLists;
    }

    /**
     *
     * @return Object
     */
    public function getStaticLists()
    {
        return $this->staticLists;
    }

    /**
     *
     * @param Object $staticListDetails            
     */
    public function setStaticListDetails($staticListDetails)
    {
        $this->staticListDetails = $staticListDetails;
    }

    /**
     *
     * @return Object
     */
    public function getStaticListDetails()
    {
        return $this->staticListDetails;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
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
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
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
    public function getTotalPages()
    {
        return $this->totalPages;
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
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }


}