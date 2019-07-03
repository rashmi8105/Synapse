<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CampusActivityDto
{

    /**
     * totalRecords
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalRecords;
	
	/**
     * totalPages
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalPages;
	
    /**
     * recordsPerPage
     *
     * @var integer @JMS\Type("integer")
     */
    private $recordsPerPage;
	
	 /**
     * currentPage
     *
     * @var integer @JMS\Type("integer")
     */
    private $currentPage;
	
	/**
     * activityFilterDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $activityFilterDate;
    

    /**
     * abbreviation
     *
     * @var string @JMS\Type("string")
     */
    private $abbreviation;
    
	
	/**
     * activities
     *
     * @var Object @JMS\Type("array<Synapse\ReportsBundle\EntityDto\ActivitiesDto>") 
     *
     */
    private $activities;  

	
	/**
     *
     * @param int $totalRecords            
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    /**
     *
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }
	
	/**
     *
     * @param int $totalPages            
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     *
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }
	
	/**
     *
     * @param int $recordsPerPage            
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
    }

    /**
     *
     * @return int
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     *
     * @param int $currentPage            
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
	
	/**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $activityFilterDate            
     */
    public function setActivityFilterDate($activityFilterDate)
    {
        $this->activityFilterDate = $activityFilterDate;
    }

    /**
     *
     * @return mixed
     */
    public function getActivityFilterDate()
    {
        return $this->activityFilterDate;
    }
	
	/**
     *
     * @param Object $activities            
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;
    }

    /**
     *
     * @return Object
     */
    public function getActivities()
    {
        return $this->activities;
    }
    
    /**
     *
     * @param string $abbreviation
     */
    public function setAbbreviation($abbreviation)
    {
    	$this->abbreviation = $abbreviation;
    }
    
    /**
     *
     * @return string
     */
    public function getAbbreviation()
    {
    	return $this->abbreviation;
    }
    
}