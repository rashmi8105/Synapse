<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentReportDto
{

    /**
     *
     * @var array @JMS\Type("array")
     *     
     */
    private $studentInfo;
	
	/**
     * campusInfo
     *
     * @var array @JMS\Type("array")
     */
    private $campusInfo;
		
	/**
     * surveyInfo
     *
	 * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\StudentSurveyInfoDto>")
     */
    private $surveyInfo;
	
	/**
     * reportSections
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\ReportSectionsDto>")
     */
    private $reportSections;
	
	/**
     * takingAction
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\TakingActionDto>")
     */
    private $takingAction;
	
	/**
     * campusConnections
     *
     * @var array @JMS\Type("array<Synapse\CampusConnectionBundle\EntityDto\CampusConnectionsArrayDto>")
     */
    private $campusConnections;
	
	/**
     * campusResources
     *
     * @var array @JMS\Type("array<Synapse\CampusResourceBundle\EntityDto\CampusResourceDto>")
     */
    private $campusResources;
	
	

    /**
     *
     * @param array $studentInfo            
     */
    public function setStudentInfo($studentInfo)
    {
        $this->studentInfo = $studentInfo;
    }

    /**
     *
     * @return array
     */
    public function getStudentInfo()
    {
        return $this->studentInfo;
    }
	
	/**
     *
     * @param array $campusInfo            
     */
    public function setCampusInfo($campusInfo)
    {
        $this->campusInfo = $campusInfo;
    }

    /**
     *
     * @return array
     */
    public function getCampusInfo()
    {
        return $this->campusInfo;
    }
	
	/**
     *
     * @param array $surveyInfo
     */
    public function setSurveyInfo($surveyInfo)
    {
        $this->surveyInfo = $surveyInfo;
    }

    /**
     *
     * @return array
     */
    public function getSurveyInfo()
    {
        return $this->surveyInfo;
    }
	
	/**
     *
     * @param array $reportSections
     */
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
        return $this->reportSections;
    }
	
	/**
     *
     * @param array $takingAction
     */
    public function setTakingAction($takingAction)
    {
        $this->takingAction = $takingAction;
    }

    /**
     *
     * @return array
     */
    public function getTakingAction()
    {
        return $this->takingAction;
    }
	
	/**
     *
     * @param array $campusConnections
     */
    public function setCampusConnections($campusConnections)
    {
        $this->campusConnections = $campusConnections;
    }

    /**
     *
     * @return array
     */
    public function getCampusConnections()
    {
        return $this->campusConnections;
    }
	
	/**
     *
     * @param array $campusResources
     */
    public function setCampusResources($campusResources)
    {
        $this->campusResources = $campusResources;
    }

    /**
     *
     * @return array
     */
    public function getCampusResources()
    {
        return $this->campusResources;
    }
}