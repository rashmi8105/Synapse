<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for
 * 
 * @package Synapse\SurveyBundle\EntityDto
 */
class IssueCreateQuesOptionsDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $id;
    
    /**
     * value
     *
     * @var string @JMS\Type("string")
     *
     */
    private $value;
    
    /**
     * text
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
     * @param string $value
     */
    public function setValue($value)
    {
    	$this->value = $value;
    }
    
    /**
     *
     * @return string
     */
    public function getValue()
    {
    	return $this->value;
    }
    
    /**
     *
     * @param string $text
     */
    public function setText($text)
    {
    	$this->text = $text;
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