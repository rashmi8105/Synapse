<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * 
 * @package Synapse\RestBundle\Entity
 */
class SurveyCohortsDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $id;
    
    /**
     * name
     *
     * @var integer @JMS\Type("string")
     *
     */
    private $name;
    
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
    
}