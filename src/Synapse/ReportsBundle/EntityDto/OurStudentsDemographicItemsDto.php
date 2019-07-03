<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class OurStudentsDemographicItemsDto
{
    /**
     * name
     *
     * @var string @JMS\Type("string")
     */
    private $name;
    
    /**
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\OurStudentsDemographicItemValuesDto>")
     *
     */
    private $values;
    
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
     * @param array $values
     */
    public function setValues($values)
    {
    	$this->values = $values;
    }
    
    /**
     *
     * @return array
     */
    public function getValues()
    {
    	return $this->values;
    }
}