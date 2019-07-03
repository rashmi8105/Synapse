<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class OurStudentsReportSectionsDto
{
    /**
     * title
     *
     * @var string @JMS\Type("string")
     */
    private $title;
    
    /**
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\OurStudentsReportElementsDto>")
     *
     */
    private $elements;
    
    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
    	$this->title = $title;
    }
    
    /**
     *
     * @return string
     */
    public function getTitle()
    {
    	return $this->title;
    }
    
    /**
     *
     * @param array $elements
     */
    public function setElements($elements)
    {
    	$this->elements = $elements;
    }
    
    /**
     *
     * @return array
     */
    public function getElements()
    {
    	return $this->elements;
    }
    
}
	 