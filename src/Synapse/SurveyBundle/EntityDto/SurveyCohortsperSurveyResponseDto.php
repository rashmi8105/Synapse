<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Survey Cohort List per Survey
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveyCohortsperSurveyResponseDto
{

    /**
     * totalCount
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $totalCount;

    /**
     * langId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $langId;

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
     * @var integer @JMS\Type("string")
     *     
     */
    private $surveyName;

    /**
     * academicYear
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $academicYear;


    /**
     * startDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $startDate;

    /**
     * endDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $endDate;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\SurveysCohortsListperSurveyResponseDto>")
     *     
     */
    private $surveyCohortsperSurvey;
   
    /**
     *
     * @param int $totalCount            
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    /**
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }


    /**
     *
     * @param int $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
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
     * @param int $academicYear            
     */
    public function setAcademicYear($academicYear)
    {
        $this->academicYear = $academicYear;
    }

    /**
     *
     * @return int
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     *
     * @param string $startDate            
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
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
     * @param Object $surveyCohortsperSurvey           
     */
    public function setSurveyCohortsperSurvey($surveyCohortsperSurvey)
    {
        $this->surveyCohortsperSurvey = $surveyCohortsperSurvey;
    }

    /**
     *
     * @return Object
    */
    public function getSurveyCohortsperSurvey()
    {
        return $this->surveyCohortsperSurvey;
   }

}
