<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\ReportsBundle\EntityDto
 */
class ElementDto
{

    /**
     * id of the report that an element is a part of
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * id of the factor that is related to a report's section
     * 
     * @var integer @JMS\Type("integer")
     */
    private $factorId ;
	
	/**
     * id of the survey question that is related to the element
     * 
     * @var integer @JMS\Type("integer")
     */
    private $surveyQuestionId ;
    
    /**
     * id of the report section that an element is within
     * 
     * @var integer @JMS\Type("integer")
     */
    private $sectionId;
    
    /**
     * id of an element
     * 
     * @var integer @JMS\Type("integer")
     */
    private $elementId;
        
	/**
     * name of an element's section
     *
     * @var string @JMS\Type("string")
     */
    private $sectionName; 
    
    /**
     * name of an element
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(min = 1,
     *      max = 25,
     *      minMessage = "Element Name must be at least {{ limit }} characters long",
     *      maxMessage = "Element Name cannot be longer than {{ limit }} characters long"
     *      )
     */
    private $elementName; 

    /**
     * content's of an element's bucket
     *
     * @var string @JMS\Type("string")
     */
    private $bucketText; 
	
	/**
     * file name for an element's icon
     *
     * @var string @JMS\Type("string")
     */
    private $elementIcon; 
    
    /**
     * name of an element's icon
     *
     * @var string @JMS\Type("string")
     */
    private $elementIconName; 
	
	/**
     * $source_type 
     *
     * @var string @JMS\Type("string")
     */
    private $sourceType;
	
	/**
     * object representation of the scores that a report's element has
     *
     * @var SurveyScoreDto[] @JMS\Type("array<Synapse\ReportsBundle\EntityDto\SurveyScoreDto>")
     */
    private $elementScores; 

	/**
     * bucket object storing element data
     *
     * @var ElementBucketDto[] @JMS\Type("array<Synapse\ReportsBundle\EntityDto\ElementBucketDto>")
     */
    private $buckets; 	
    
     /**
     * boolean representing whether an element's image has been changed
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $imageChanges;
    
    
    /**
     * @param int $reportId
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * @return int
     */
    public function getReportId()
    {
        return $this->reportId;
    }
    
    /**
     * @param int $elementId
     */
    public function setElementId($elementId)
    {
        $this->elementId = $elementId;
    }

    /**
     * @return int
     */
    public function getElementId()
    {
        return $this->elementId;
    }
   
   
    /**
     * @param int $sectionId
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }
    
    /**
     * @param string $elementName
     */
    public function setElementName($elementName)
    {
        $this->elementName = $elementName;
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }
    
    /**
     * @param string $bucketText
     */
    public function setBucketText($bucketText)
    {
        $this->bucketText = $bucketText;
    }

    /**
     * @return string
     */
    public function getBucketText()
    {
        return $this->bucketText;
    }
	
	/**
     * @param string $elementIcon
     */
    public function setElementIcon($elementIcon)
    {
        $this->elementIcon = $elementIcon;
    }

    /**
     * @return string
     */
    public function getElementIcon()
    {
        return $this->elementIcon;
    }
	
	/**
     * @param array $elementScores
     */
    public function setElementScores($elementScores)
    {
        $this->elementScores = $elementScores;
    }

    /**
     * @return SurveyScoreDto[]
     */
    public function getElementScores()
    {
        return $this->elementScores;
    }
	
	/**
     * @param array $buckets
     */
    public function setBuckets($buckets)
    {
        $this->buckets = $buckets;
    }

    /**
     * @return ElementBucketDto[]
     */
    public function getBuckets()
    {
        return $this->buckets;
    }
	
	/**
     * @param string $sectionName
     */
    public function setSectionName($sectionName)
    {
        $this->sectionName = $sectionName;
    }

    /**
     * @return string
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }
	
	/**
     * @param string $sourceType
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType ;
    }
	
	 /**
     * @param int $factorId
     */
    public function setFactorId($factorId)
    {
        $this->factorId = $factorId;
    }

    /**
     * @return int
     */
    public function getFactorId()
    {
        return $this->factorId ;
    }
	
	 /**
     * @param int $surveyQuestionId
     */
    public function setSurveyQuestionId($surveyQuestionId)
    {
        $this->surveyQuestionId = $surveyQuestionId;
    }

    /**
     * @return int
     */
    public function getSurveyQuestionId()
    {
        return $this->surveyQuestionId ;
    }
    
    /**
     * @param string $elementIconName
     */
    public function setElementIconName($elementIconName)
    {
        $this->elementIconName = $elementIconName;
    }

    /**
     * @return string
     */
    public function getElementIconName()
    {
        return $this->elementIconName;
    }
    
    /**
     * @param boolean $imageChanges
     */
    public function setImageChanges($imageChanges)
    {
        $this->imageChanges = $imageChanges;
    }

    /**
     * @return boolean
     */
    public function getImageChanges()
    {
        return $this->imageChanges;
    }
}