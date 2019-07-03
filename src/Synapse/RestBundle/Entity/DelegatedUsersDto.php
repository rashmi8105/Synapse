<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Notes
 *
 * @package Synapse\RestBundle\Entity
 */
class DelegatedUsersDto
{

    /**
     * calendarSharingId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $calendarSharingId;

    /**
     * sharedOn
     * @JMS\Type("DateTime")
     */
    private $sharedOn;

    /**
     * delegatedToPersonId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $delegatedToPersonId;

    /**
     * isSelected
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isSelected;

    /**
     * isDeleted
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isDeleted;

    /**
     *
     * @return boolean
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     *
     * @param boolean $isSelected            
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;
    }

    /**
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     *
     * @param boolean $isDeleted            
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     *
     * @param int $calendarSharingId            
     */
    public function setCalendarSharingId($calendarSharingId)
    {
        $this->calendarSharingId = $calendarSharingId;
    }

    /**
     *
     * @return int
     */
    public function getCalendarSharingId()
    {
        return $this->calendarSharingId;
    }

    /**
     *
     * @param int $sharedOn            
     */
    public function setSharedOn($sharedOn)
    {
        $this->sharedOn = $sharedOn;
    }

    /**
     *
     * @return int
     */
    public function getSharedOn()
    {
        return $this->sharedOn;
    }

    /**
     *
     * @param int $delegatedToPersonId            
     */
    public function setDelegatedToPersonId($delegatedToPersonId)
    {
        $this->delegatedToPersonId = $delegatedToPersonId;
    }

    /**
     *
     * @return int
     */
    public function getDelegatedToPersonId()
    {
        return $this->delegatedToPersonId;
    }
}