<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class ReportRunningStatusDto
{

    /**
     * id of a running status report
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;
	
	 /**
     * datetime that a running status report is created
     *
     * @var \DateTime @JMS\Type("DateTime")
     */
    private $createdAt;
	
	/**
     * id of the report that a running status report is referencing
     *
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * id of the organization that owns the report and its information
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;
	
	/**
     * id of the person that created the report
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;
	
	/**
     * tells whether a report has been viewed before
     *
     * @var string @JMS\Type("string")
     */
    private $isViewed;
	
	/**
     * custom title for a report
     *
     * @var string @JMS\Type("string")
     */
    private $reportCustomTitle;
	
	/**
     * short-code identifying a report
     *
     * @var string @JMS\Type("string")
     */
    private $shortCode;
	
	/**
     * json containing the report's data
     *
     * @var array @JMS\Type("array")
     */
    private $responseJson;
	
	/**
     * filters that apply to a report
     *
     * @var array @JMS\Type("array")
     */
    private $searchAttributes;
    
    
    /**
     * sections within a report
     *
     * @var array @JMS\Type("array")
     */
    private $reportSections;
    
	
	/**
     * current status of a report
     *
     * @var string @JMS\Type("string")
     */
    private $status;
    
    
    /**
     * list of past activity upon a report
     *
     * @var array @JMS\Type("array")
     */
    private $activityReport;

    /**
     * used ONLY for compare reports, filters that are required, as to correspond with another report
     *
     * @var array @JMS\Type("array")
     */
    private $mandatoryFilters;
    
	
	/**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
	
	/**
     *
     * @param int $reportId            
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     *
     * @return int
     */
    public function getReportId()
    {
        return $this->reportId;
    }
	
	/**
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }
	
	/**
     *
     * @param int $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }
	
	/**
     *
     * @param string $isViewed            
     */
    public function setIsViewed($isViewed)
    {
        $this->isViewed = $isViewed;
    }

    /**
     *
     * @return string
     */
    public function getIsViewed()
    {
        return $this->isViewed;
    }
	
	/**
     *
     * @param string $reportCustomTitle            
     */
    public function setReportCustomTitle($reportCustomTitle)
    {
        $this->reportCustomTitle = $reportCustomTitle;
    }

    /**
     *
     * @return string
     */
    public function getReportCustomTitle()
    {
        return $this->reportCustomTitle;
    }
	
	/**
     *
     * @param string $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
	
	/**
     *
     * @param array $responseJson            
     */
    public function setResponseJson($responseJson)
    {
        $this->responseJson = $responseJson;
    }

    /**
     *
     * @return array
     */
    public function getResponseJson()
    {
        return $this->responseJson;
    }
	
	/**
     *
     * @param array $filterAttributes            
     */
    public function setSearchAttributes($filterAttributes)
    {
        $this->searchAttributes = $filterAttributes;
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
     * @param Object $savedSearches
     */
    public function setActivityReport($activityReport)
    {
        $this->activityReport = $activityReport;
    }
    
    /**
     *
     * @return Object
     */
    public function getActivityReport()
    {
        return $this->activityReport;
    }
	

    

    public function setReportSections($reportSections)
    {
        $this->reportSections = $reportSections;
    }
    
    /**
     *
     * @return array
     */
    public function getReportSections()
    {
        return $this->reportSections ;
    }
	
	/**
     *
     * @param string $shortCode            
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
    }

    /**
     *
     * @return string
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }

    /**
     *
     * @param array $mandatoryFilters
     */
    public function setMandatoryFilters($mandatoryFilters)
    {
        $this->mandatoryFilters = $mandatoryFilters;
    }

    /**
     *
     * @return array
     */
    public function getMandatoryFilters()
    {
        return $this->mandatoryFilters;
    }
    
}