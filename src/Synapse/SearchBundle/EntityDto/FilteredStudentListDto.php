<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

abstract class FilteredStudentListDto
{
    /**
     * @var string @JMS\Type("string")
     */
    private $listTitle;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $totalRecords;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $totalPages;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $recordsPerPage;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $currentPage;

    /**
     * @var array @JMS\Type("array")
     */
    private $searchResult;

    /**
     * @var array @JMS\Type("array")
     */
    private $searchAttributes;

    /**
     * @return string
     */
    public function getListTitle()
    {
        return $this->listTitle;
    }

    /**
     * @param string $listTitle
     */
    public function setListTitle($listTitle)
    {
        $this->listTitle = $listTitle;
    }

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
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

    /**
     * @return array
     */
    public function getSearchResult()
    {
        return $this->searchResult;
    }

    /**
     * @param array $searchResult
     */
    public function setSearchResult($searchResult)
    {
        $this->searchResult = $searchResult;
    }

    /**
     * @return array
     */
    public function getSearchAttributes()
    {
        return $this->searchAttributes;
    }

    /**
     * @param array $searchAttributes
     */
    public function setSearchAttributes($searchAttributes)
    {
        $this->searchAttributes = $searchAttributes;
    }

}