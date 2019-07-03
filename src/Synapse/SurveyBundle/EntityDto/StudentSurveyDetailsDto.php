<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentSurveyDetailsDto
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
     * surveyLastDate
     *
     * @var datetime @JMS\Type("DateTime")
     *
     */
    private $surveyLastDate;
    
    /**
     * status
     *
     * @var string @JMS\Type("string")
     *
     */
    private $status;
    
    /**
     * campusName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $campusName;
    
    /**
     * surveyUrl
     *
     * @var string @JMS\Type("string")
     *
     */
    private $surveyUrl;
    
    /**
     * reportPdf
     *
     * @var string @JMS\Type("string")
     *
     */
    private $reportPdf;
    
    
    /**
     * surveyUrl
     *
     * @var string @JMS\Type("string")
     *
     */
    private $year;
    
    
    /**
     * surveyUrl
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
     * @param mixed $closeDate
     */
    public function setSurveyLastDate($surveyLastDate)
    {
    	$this->surveyLastDate = $surveyLastDate;
    }
    
    /**
     *
     * @return mixed
     */
    public function getSurveyLastDate()
    {
    	return $this->surveyLastDate;
    }
    
    /**
     *
     * @param mixed $campusName
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }
    
    /**
     *
     * @return mixed
     */
    public function getCampusName()
    {
        return $this->campusName;
    }
    
    
    /**
     *
     * @param mixed $surveyUrl
     */
    public function setSurveyUrl($surveyUrl)
    {
        $this->surveyUrl = $surveyUrl;
    }
    
    /**
     *
     * @return mixed
     */
    public function getSurveyUrl()
    {
        return $this->surveyUrl;
    }
    
    /**
     *
     * @param mixed $reportPdf
     */
    public function setReportPdf($reportPdf)
    {
        $this->reportPdf = $reportPdf;
    }
    
    /**
     *
     * @return mixed
     */
    public function getReportPdf()
    {
        return $this->reportPdf;
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