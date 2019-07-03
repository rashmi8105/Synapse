<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class OurStudentsTop5issueDto
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
     * total_students
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;
    
    /**
     * image
     *
     * @var string @JMS\Type("string")
     */
    private $image;
    
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
     * @param integer $totalStudents
     */
    public function setTotalStudents($totalStudents)
    {
    	$this->totalStudents = $totalStudents;
    }
    
    /**
     *
     * @return integer
     */
    public function getTotalStudents()
    {
    	return $this->totalStudents;
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
     * @param string $image
     */
    public function setImage($image){
        $this->image = $image;
    } 
     
    /**
     * 
     * @return string
     */
    public function getImage(){
        return $this->image;
    }
}