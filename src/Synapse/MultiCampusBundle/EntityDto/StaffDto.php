<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class StaffDto
{

    /**
     * person_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * master_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $masterId;

    /**
     * is_by_passed
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isByPassed;

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
     * @param integer $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
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
     * @param boolean $isByPassed            
     */
    public function setIsByPassed($isByPassed)
    {
        $this->isByPassed = $isByPassed;
    }

    /**
     *
     * @return boolean
     */
    public function getIsByPassed()
    {
        return $this->isByPassed;
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