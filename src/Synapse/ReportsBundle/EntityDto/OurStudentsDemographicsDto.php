<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class OurStudentsDemographicsDto
{
    /**
     * block_name
     *
     * @var string @JMS\Type("string")
     */
    private $blockName;
    
    /**
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\OurStudentsDemographicItemsDto>")
     *
     */
    private $items;
    
    /**
     *
     * @param string $blockName
     */
    public function setBlockName($blockName)
    {
    	$this->blockName = $blockName;
    }
    
    /**
     *
     * @return string
     */
    public function getBlockName()
    {
    	return $this->blockName;
    }
    
    /**
     *
     * @param array $items
     */
    public function setItems($items)
    {
    	$this->items = $items;
    }
    
    /**
     *
     * @return array
     */
    public function getItems()
    {
    	return $this->items;
    }
        
}