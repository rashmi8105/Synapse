<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class IssueSurveyQuestionsDto
{    
    
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
}