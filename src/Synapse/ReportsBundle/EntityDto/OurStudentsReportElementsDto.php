<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class OurStudentsReportElementsDto
{
    /**
     * name
     *
     * @var string @JMS\Type("string")
     */
    private $name;
    
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
     * @param string $name
     */
    public function setName($name)
    {
    	$this->name = $name;
    }
    
    /**
     *
     * @return string
     */
    public function getName()
    {
    	return $this->name;
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
}
	 