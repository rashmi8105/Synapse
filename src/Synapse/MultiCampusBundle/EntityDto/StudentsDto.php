<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentsDto
{

	/**
     *  home_campus_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $homeCampusId;
	
	/**
     *  masterId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $masterId;
    /**
     * source
     * 
     * @var array @JMS\Type("array")
     */
    private $source;
	
	   /**
     * target
     * 
     * @var array @JMS\Type("array")
     */
    private $target;

    /**
     *
     * @param integer $homeCampusId            
     */
    public function setHomeCampusId($homeCampusId)
    {
        $this->homeCampusId = $homeCampusId;
    }

    /**
     *
     * @return integer
     */
    public function getHomeCampusId()
    {
        return $this->homeCampusId;
    }

    /**
     *
     * @param integer $masterId            
     */
    public function setMasterId($masterId)
    {
        $this->masterId = $masterId;
    }

    /**
     *
     * @return integer
     */
    public function getMasterId()
    {
        return $this->masterId;
    }

    /**
     *
     * @param array $source            
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     *
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }
	
	
	/**
     *
     * @param array $target            
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     *
     * @return array
     */
    public function getTarget()
    {
        return $this->target;
    }
}