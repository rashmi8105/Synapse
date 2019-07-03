<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * 
 * @package Synapse\SurveyBundle\EntityDto
 */
class IssueCreateQuestionsDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $id;
    
    /**
     * type
     *
     * @var integer @JMS\Type("string")
     *
     */
    private $type;
    
    /**
     * $minRange
     * 
     *@var integer @JMS\Type("integer")
     *
     */
    private $minRange;
    
    /**
     * $maxRange
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $maxRange;
    
    /**
     * $startDate
     *
     * @var DateTime @JMS\Type("DateTime<'Y-m-d'>")
     *
     *
     */
    private $startDate;
    
    /**
     * $endDate
     *
     * @var DateTime @JMS\Type("DateTime<'Y-m-d'>")
     *
     *
     */
    private $endDate;
    
    /**
     * options
     * @var Object
     * @JMS\Type("array<Synapse\SurveyBundle\EntityDto\IssueCreateQuesOptionsDto>")
     *
     *
     */
    private $options;
    
    /**
     * $text
     *
     * @var string @JMS\Type("string")
     *
     */
    private $text;
    
    /**
     *
     * @param integer $id
     */
    public function setId($id)
    {
    	$this->id = $id;
    }
    
    /**
     *
     * @return integer
     */
    public function getId()
    {
    	return $this->id;
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
     * @param int $minRange
     */
    public function setMinRange($minRange)
    {
    	$this->minRange = $minRange;
    }
    
    /**
     *
     * @return int
     */
    public function getMinRange()
    {
    	return $this->minRange;
    }
    
    /**
     *
     * @param int $maxRange
     */
    public function setMaxRange($maxRange)
    {
    	$this->maxRange = $maxRange;
    }
    
    /**
     *
     * @return int
     */
    public function getMaxRange()
    {
    	return $this->maxRange;
    }
    
    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
    	$this->startDate = $startDate;
    }
    
    /**
     * @return string
     */
    public function getStartDate()
    {
    	return $this->startDate;
    }
    
    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
    	$this->endDate = $endDate;
    }
    
    /**
     * @return string
     */
    public function getEndDate()
    {
    	return $this->endDate;
    }
    
    /**
     *
     * @param Object $options
     */
    public function setOptions($options)
    {
    	$this->options = $options;
    }
    
    
    
    /**
     * @return Object
     */
    public function getOptions()
    {
    	return $this->options;
    }
    
    /**
     *
     * @param string $text
     */
    public function setText($text)
    {
    	$this->text = $text;
    	return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getText()
    {
    	return $this->text;
    }
}