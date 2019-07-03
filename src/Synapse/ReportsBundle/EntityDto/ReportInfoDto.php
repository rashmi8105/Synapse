<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class ReportInfoDto
{

    /**
     * reportId
     *
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * reportInstanceId
     *
     * @var integer @JMS\Type("integer")
     */
    private $reportInstanceId;
	
	/**
     * reportName
     *
     * @var string @JMS\Type("string")
     */
    private $reportName;
	
	/**
     * status
     *
     * @var string @JMS\Type("string")
     */
    private $status;
	
	/**
     * reportDescription
     *
     * @var string @JMS\Type("string")
     */
    private $reportDescription;
	
	/**
     * generatedOn
     *
     * @var datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $generatedOn;
	
    /**
     *
     * @var Object @JMS\Type("array<Synapse\ReportsBundle\EntityDto\StudentReportDto>")
     *     
     */
    private $studentReport;
	
	 /**
     * reportDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $reportDate;
	
	/**
     * totalStudents
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;
	
	/**
     * short_code 
     *
     * @var string @JMS\Type("string")
     */
    private $shortCode;
	
	/**
     * reportDisable  
     *
     * @var string @JMS\Type("string")
     */
    private $reportDisable ;
	
	/**
     * reportFilter 
     *
     * @var array @JMS\Type("array")
     *     
     */
    private $reportFilter;
	
	/**
     * report_by 
     *
     * @var array @JMS\Type("array")
     *     
     */
    private $reportBy;
	
	
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
     * @param int $reportInstanceId            
     */
    public function setReportInstanceId($reportInstanceId)
    {
        $this->reportInstanceId = $reportInstanceId;
    }

    /**
     *
     * @return int
     */
    public function getReportInstanceId()
    {
        return $this->reportInstanceId;
    }
	
	/**
     *
     * @param mixed $generatedOn            
     */
    public function setGeneratedOn($generatedOn)
    {
        $this->generatedOn = $generatedOn;
    }

    /**
     *
     * @return mixed
     */
    public function getGeneratedOn()
    {
        return $this->generatedOn;
    }

    /**
     *
     * @param Object $studentReport            
     */
    public function setStudentReport($studentReport)
    {
        $this->studentReport = $studentReport;
    }

    /**
     *
     * @return Object
     */
    public function getStudentReport()
    {
        return $this->studentReport;
    }
	
	/**
     *
     * @param string $reportName            
     */
    public function setReportName($reportName)
    {
        $this->reportName = $reportName;
    }

    /**
     *
     * @return string
     */
    public function getReportName()
    {
        return $this->reportName;
    }	
	
	/**
     *
     * @param string $reportDescription            
     */
    public function setReportDescription($reportDescription)
    {
        $this->reportDescription = $reportDescription;
    }

    /**
     *
     * @return string
     */
    public function getReportDescription()
    {
        return $this->reportDescription;
    }	
	
	/**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $date            
     */
    public function setReportDate($reportDate)
    {
        $this->reportDate = $reportDate;
    }

    /**
     *
     * @return mixed
     */
    public function getReportDate()
    {
        return $this->reportDate;
    }
	
	/**
     *
     * @param int $totalStudents            
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }

    /**
     *
     * @return int
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
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
     * @param string $reportDisable            
     */
    public function setReportDisable($reportDisable)
    {
        $this->reportDisable = $reportDisable;
    }

    /**
     *
     * @return string
     */
    public function getReportDisable()
    {
        return $this->reportDisable;
    }	
	
	/**
     *
     * @return array
     */
    public function getReportFilter()
    {
        return $this->reportFilter;
    }

    /**
     *
     * @param array $reportFilter            
     */
    public function setReportFilter($reportFilter)
    {
        $this->reportFilter = $reportFilter;
    }
	/**
     *
     * @return array
     */
    public function getReportBy()
    {
        return $this->reportBy;
    }

    /**
     *
     * @param array $reportBy            
     */
    public function setReportBy($reportBy)
    {
        $this->reportBy = $reportBy;
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
} 