<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Survey
 *
 * @package Synapse\RestBundle\Entity
 */
class SurveyDto
{

    /**
     * survey_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyId;

    /**
     * survey_name
     *
     * @var string @JMS\Type("string")
     */
    private $surveyName;

    /**
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $surveyStartDate;

    /**
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $surveyEndDate;

    /**
     * cohorts
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\CohortsDto>")
     */
    private $cohorts;

    /**
     * totalCount
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $totalCount;

    /**
     * langId
     *
     * @var string @JMS\Type("integer")
     */
    private $langId;

    /**
     * surveys
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\SurveyResponseDto>")
     */
    private $surveys;

    /**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $date            
     */
    public function setSurveyStartDate($surveyStartDate)
    {
        $this->surveyStartDate = $surveyStartDate;
    }

    /**
     *
     * @return \Synapse\RestBundle\Entity\datetime
     */
    public function getSurveyStartDate()
    {
        return $this->surveyStartDate;
    }

    /**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $date            
     */
    public function setSurveyEndDate($surveyEndDate)
    {
        $this->surveyEndDate = $surveyEndDate;
    }

    /**
     *
     * @return \Synapse\RestBundle\Entity\datetime
     */
    public function getSurveyEndDate()
    {
        return $this->surveyEndDate;
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
     * @param Object $cohorts            
     */
    public function setCohorts($cohorts)
    {
        $this->cohorts = $cohorts;
    }

    /**
     *
     * @param
     *            string integer
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    /**
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     *
     * @param Object $surveys            
     */
    public function setSurveys($surveys)
    {
        $this->surveys = $surveys;
    }

    /**
     *
     * @param Object $surveys            
     */
    public function getSurveys()
    {
        return $this->surveys;
    }

    /**
     *
     * @param integer $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return integer
     */
    public function getLangId()
    {
        return $this->langId;
    }
}