<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveyScoreDto
{

    /**
     * id of the survey that a score belongs to
     *
     * @var string @JMS\Type("string")
     */
    private $surveyId; 
	
	/**
     * score for a particular element
     *
     * @var string @JMS\Type("string")
     */
    private $elementScore; 
	
	/**
     * color(risk) corresponding with an element
     *
     * @var string @JMS\Type("string")
     */
    private $elementColor;
	
	/**
     * text describing an element
     *
     * @var string @JMS\Type("string")
     */
    private $elementText; 

	/**
     * @param string $surveyId
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     * @return string
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }
	
	/**
     * @param string $elementScore
     */
    public function setElementScore($elementScore)
    {
        $this->elementScore = $elementScore;
    }

    /**
     * @return string
     */
    public function getElementScore()
    {
        return $this->elementScore;
    }
	
	/**
     * @param string $elementColor
     */
    public function setElementColor($elementColor)
    {
        $this->elementColor = $elementColor;
    }

    /**
     * @return string
     */
    public function getElementColor()
    {
        return $this->elementColor;
    }
	
	/**
     * @param string $elementText
     */
    public function setElementText($elementText)
    {
        $this->elementText = $elementText;
    }

    /**
     * @return string
     */
    public function getElementText()
    {
        return $this->elementText;
    }
}