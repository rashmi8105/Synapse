<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class ActivitiesDto
{

    /**
     * activityId
     *
     * @var integer @JMS\Type("integer")
     */
    private $activityId;
    
    
    /**
     * studentId
     *
     * @var string @JMS\Type("integer")
     *
     */
    private $studentId;
	
	/**
     * activityType
     *
     * @var string @JMS\Type("string")
     */
    private $activityType;
	
    /**
     * activityStatus
     *
     * @var string @JMS\Type("string")
     */
    private $activityStatus;
	
	/**
     * activityCreatedBy
     *
     * @var string @JMS\Type("string")
     */
    private $activityCreatedBy;
	
	/**
     * activityCreatedOn
     *
     * @var dateTime @JMS\Type("DateTime")
     */
    private $activityCreatedOn;
	
	/**
     * studentName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $studentName;
	
	/**
     * activityDetails
     *
     * @var string @JMS\Type("string") 
     *
     */
    private $activityDetails;  
	
	
	/**
     *
     * @param int $activityId            
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
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
     * @param mixed $reportInstanceId            
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
    }

    /**
     *
     * @return mixed
     */
    public function getActivityType()
    {
        return $this->activityType;
    }
	
	/**
     *
     * @param mixed $activityStatus            
     */
    public function setActivityStatus($activityStatus)
    {
        $this->activityStatus = $activityStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getActivityStatus()
    {
        return $this->activityStatus;
    }

    /**
     *
     * @param mixed $activityCreatedBy            
     */
    public function setActivityCreatedBy($activityCreatedBy)
    {
        $this->activityCreatedBy = $activityCreatedBy;
    }

    /**
     *
     * @return mixed
     */
    public function getActivityCreatedBy()
    {
        return $this->activityCreatedBy;
    }
	
	/**
     *
     * @param mixed $activityCreatedOn            
     */
    public function setActivityCreatedOn($activityCreatedOn)
    {
        $this->activityCreatedOn = $activityCreatedOn;
    }

    /**
     *
     * @return mixed
     */
    public function getActivityCreatedOn()
    {
        return $this->activityCreatedOn;
    }
	
	/**
     *
     * @param mixed $studentName            
     */
    public function setStudentName($studentName)
    {
        $this->studentName = $studentName;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentName()
    {
        return $this->studentName;
    }
	
	/**
     *
     * @param mixed $activityDetails            
     */
    public function setActivityDetails($activityDetails)
    {
        $this->activityDetails = $activityDetails;
    }

    /**
     *
     * @return mixed
     */
    public function getActivityDetails()
    {
        return $this->activityDetails;
    }
    
    /**
     *
     * @param mixed $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }
    
    /**
     *
     * @return mixed
     */
    public function getStudentId()
    {
        return $this->studentId;
    }
    
}