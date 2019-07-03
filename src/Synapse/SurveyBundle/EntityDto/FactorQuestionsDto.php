<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for FactorQuestions
 *
 * @package Synapse\SurveyBundle\FactorQuestionsDto
 */
class FactorQuestionsDto
{

    /**
     * langId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $langId;

    /**
     * factorId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $factorId;
    
    /**
     * totalcount
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalCount;
    
    /**
     * factorName
     *
     * @var string @JMS\Type("string")    
     *     
     */
    private $factorName;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\SurveyQuestionsDto>")
     *     
     */
    private $surveyQuestions;
    
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

    /**
     *
     * @param int $factorId            
     */
    public function setFactorId($factorId)
    {
        $this->factorId = $factorId;
    }

    /**
     *
     * @return int
     */
    public function getFactorId()
    {
        return $this->factorId;
    }

    /**
     *
     * @param integer $totalCount            
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
     * @param Object $surveyQuestions            
     */
    public function setSurveyQuestions($surveyQuestions)
    {
        $this->surveyQuestions = $surveyQuestions;
    }

    /**
     *
     * @return Object
     */
    public function getSurveyQuestions()
    {
        return $this->surveyQuestions;
    }
    
    /**
     *
     * @param string $factorName            
     */
    public function setFactorName($factorName)
    {
        $this->factorName = $factorName;
    }

    /**
     *
     * @return string
     */
    public function getFactorName()
    {
        return $this->factorName;
    }
}