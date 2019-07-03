<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveysDetailsDto
{
    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $surveyId;
    
    /**
     * surveyName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $surveyName;
    
    /**
     * openDate
     *
     * @var datetime @JMS\Type("DateTime")
     *
     */
    private $openDate;
    
    /**
     * status
     *
     * @var string @JMS\Type("string")
     *
     */
    private $status;
    
    /**
     * status
     *
     * @var string @JMS\Type("string")
     *
     */
    private $year;
    
    /**
     * status
     *
     * @var string @JMS\Type("string")
     *
     */
    private $cohort;
    
    /**
     *
     * @param int $surveyId
     */
    public function setSurveyId($surveyId)
    {
    	$this->surveyId = $surveyId;
    }
    
    /**
     *
     * @return int
     */
    public function getSurveyId()
    {
    	return $this->surveyId;
    }
    
    /**
     *
     * @param string $surveyName
     */
    public function setSurveyName($surveyName)
    {
    	$this->surveyName = $surveyName;
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
     * @param mixed $openDate
     */
    public function setOpenDate($openDate)
    {
    	$this->openDate = $openDate;
    }
    
    /**
     *
     * @return mixed
     */
    public function getOpenDate()
    {
    	return $this->openDate;
    }

    /**
     * @param string $cohort
     */
    public function setCohort($cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return string
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * @param string $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }
    
    
    
}