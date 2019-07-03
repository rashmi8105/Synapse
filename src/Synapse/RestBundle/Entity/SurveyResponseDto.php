<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Survey
 *
 * @package Synapse\RestBundle\Entity
 */
class SurveyResponseDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * survey_name
     *
     * @var string @JMS\Type("string")
     */
    private $surveyName;
        
    /**
     * survey_status
     *
     * @var string @JMS\Type("string")
     */
    private $surveyStatus;
    
    /**
     * year
     *
     * @var string @JMS\Type("string")
     */
    private $year;
    
    /**
     * startDate
     *
     * @var datetime @JMS\Type("DateTime")
     */
    private $startDate;
    
    /**
     * endDate
     *
     * @var datetime @JMS\Type("DateTime")
     */
    private $endDate;
    
    /**
     * cohorts
     * @var Object
     * @JMS\Type("array<Synapse\RestBundle\Entity\SurveyCohortsDto>")
     *
     *
     */
    private $cohorts;
	
	/**
     * survey_icon
     *
     * @var string @JMS\Type("string")
     */
    private $surveyIcon;


    /**
     *
     * @param string $surveyName            
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = $surveyName;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     *
     * @param string $year
     */
    public function setYear($year)
    {
    	$this->year = $year;
    	return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getYear()
    {
    	return $this->year;
    }
    
    /**
     *
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
    	$this->startDate = $startDate;
    	return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getStartDate()
    {
    	return $this->startDate;
    }
    
    /**
     *
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
    	$this->endDate = $endDate;
    	return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getEndDate()
    {
    	return $this->endDate;
    }
    
    /**
     *
     * @param Object $cohorts
     */
    public function setCohorts($cohorts)
    {
    	$this->cohorts = $cohorts;
    	return $this;
    }
    
    /**
     *
     * @return Object
     */
    public function getCohorts()
    {
    	return $this->cohorts;
    }
	
	/**
     *
     * @param string $surveyIcon            
     */
    public function setSurveyIcon($surveyIcon)
    {
        $this->surveyIcon = $surveyIcon;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSurveyIcon()
    {
        return $this->surveyIcon;
    }
        
    /**
     *
     * @param string $surveyStatus
     */
    public function setSurveyStatus($surveyStatus)
    {
    	$this->surveyStatus = $surveyStatus;
    	return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getSurveyStatus()
    {
    	return $this->surveyStatus;
    }
}