<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class TakingActionDto
{

    /**
     * elementId
     *
     * @var integer @JMS\Type("integer")
     */
    private $elementId;
	
	/**
     * elementName
     *
     * @var string @JMS\Type("string")
     */
    private $elementName;
    /**
     * elementIcon
     *
     * @var string @JMS\Type("string")
     */
    private $elementIcon;
	
	
	/**
     * elementColor
     *
     * @var string @JMS\Type("string")
     */
    private $elementColor;
	
    /**
     * elementScore
     *
     * @var string @JMS\Type("string")
     */
    private $elementScore;
	
	/**
     * elementText
     *
     * @var string @JMS\Type("string")
     */
    private $elementText;
	
	
    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     */
    private $surveyId;
	
	/**
     *
     * @param int $elementId            
     */
    public function setElementId($elementId)
    {
        $this->elementId = $elementId;
    }

    /**
     *
     * @return int
     */
    public function getElementId()
    {
        return $this->elementId;
    }
	
	/**
     *
     * @param string $elementName            
     */
    public function setElementName($elementName)
    {
        $this->elementName = $elementName;
    }

    /**
     *
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }

    /**
     *
     * @param string $elementIcon            
     */
    public function setElementIcon($elementIcon)
    {
        $this->elementIcon = $elementIcon;
    }

    /**
     *
     * @return string
     */
    public function getElementIcon()
    {
        return $this->elementIcon;
    }
	
	/**
     *
     * @param string $elementColor            
     */
    public function setElementColor($elementColor)
    {
        $this->elementColor = $elementColor;
    }

    /**
     *
     * @return string
     */
    public function getElementColor()
    {
        return $this->elementColor;
    }
	
	/**
     *
     * @param string $elementScore            
     */
    public function setElementScore($elementScore)
    {
        $this->elementScore = $elementScore;
    }

    /**
     *
     * @return string
     */
    public function getElementScore()
    {
        return $this->elementScore;
    }
	/**
     *
     * @param string $elementText            
     */
    public function setElementText($elementText)
    {
        $this->elementText = $elementText;
    }

    /**
     *
     * @return string
     */
    public function getElementText()
    {
        return $this->elementText;
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
}