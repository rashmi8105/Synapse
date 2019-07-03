<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\SearchBundle\EntityDto
 */
class SearchDto
{

    /**
     * $personId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $personId;
    
    /**
     * $totalrecords
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalRecords;
    
    
    /**
     * $totalPages
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalPages;
    
    
    /**
     * $recordsPerPage
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $recordsPerPage;
    
    
    /**
     * $currentPage
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $currentPage;

    /**
     * $searchResult
     *
     * @var array @JMS\Type("array<Synapse\SearchBundle\EntityDto\SearchResultListDto>")
     */
    private $searchResult;

    /**
     * searchAttributes
     * 
     * @var array @JMS\Type("array")
     */
    private $searchAttributes;
    
    
    /**
     * question
     *
     * @var string @JMS\Type("string")
     */
    private $question;
    
    public function setTotalRecords($totalRecords){
        
        $this->totalRecords = $totalRecords;
    }

    /**
     *
     * @return integer
     */
    public function getTotalRecords(){

        return $this->totalRecords;
    }

    public function setCurrentPage($currentPage){
    
        $this->currentPage = $currentPage;
    }
    
    public function setTotalPages($totalPages){
    
        $this->totalPages = $totalPages;
    }
    
    
    public function setRecordsPerPage($recordsPerPage){
        
        $this->recordsPerPage = $recordsPerPage;
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
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param array $searchResult
     */
    public function setSearchResult($searchResult)
    {
        $this->searchResult = $searchResult;
    }

    /**
     *
     * @return array
     */
    public function getSearchResult()
    {
        return $this->searchResult;
    }

    /**
     *
     * @param array $searchAttributes            
     */
    public function setSearchAttributes($searchAttributes)
    {
        $this->searchAttributes = $searchAttributes;
    }

    /**
     *
     * @return array
     */
    public function getSearchAttributes()
    {
        return $this->searchAttributes;
    }
    
	/**
     *
     * @param string $question            
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }
}