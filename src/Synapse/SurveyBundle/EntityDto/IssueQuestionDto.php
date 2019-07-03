<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class IssueQuestionDto
{
    /**
     * type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $type;
    
    /**
     * total_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalCount;
    
    /**
     * survey_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyId;
    
    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\IssueSurveyQuestionsDto>")
     *     
     */
    private $surveyQuestions;
    
    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\IssueSurveyQuestionsArrayDto>")
     *     
     */
    private $questions;
    
      
    
    /**
     *
     * @param Object $questions            
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;
    }

    /**
     *
     * @return Object
     */
    public function getQuestions()
    {
        return $this->questions;
    }
    
    /**
     *
     * @param string $type            
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * @param integer $surveyId            
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     *
     * @return integer
     */
    public function getSurveyId()
    {
        return $this->surveyId;
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
}