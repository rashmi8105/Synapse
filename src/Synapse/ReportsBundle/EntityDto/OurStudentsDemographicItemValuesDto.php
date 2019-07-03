<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class OurStudentsDemographicItemValuesDto
{
    /**
     * value
     *
     * @var string @JMS\Type("string")
     */
    private $value;
    
    /**
     * percentage
     *
     * @var string @JMS\Type("string")
     */
    private $percentage;
    
    /**
     * count
     *
     * @var integer @JMS\Type("integer")
     */
    private $count;
    
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
     * @param integer $count
     */
    public function setCount($count)
    {
    	$this->count = $count;
    }
    
    /**
     *
     * @return integer
     */
    public function getCount()
    {
    	return $this->count;
    }
    
    /**
     *
     * @param string $percentage
     */
    public function setPercentage($percentage)
    {
    	$this->percentage = $percentage;
    }
    
    /**
     *
     * @return string
     */
    public function getPercentage()
    {
    	return $this->percentage;
    }
    
}