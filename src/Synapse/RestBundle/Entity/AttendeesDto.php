<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\RestBundle\Entity
 */
class AttendeesDto
{

    /**
     * student_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $studentFirstName;

    /**
     *
     * @var string @JMS\Type("string")
     */
    private $studentLastName;

    /**
     * is_selected
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isSelected;

    /**
     * is_added_new
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isAddedNew;

    /**
     * isAttended
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isAttended;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;
    
    /**
     *
     * @param mixed $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentId()
    {
        return $this->studentId;
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
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     *
     * @param boolean $isAddedNew            
     */
    public function setIsAddedNew($isAddedNew)
    {
        $this->isAddedNew = $isAddedNew;
    }

    /**
     *
     * @return boolean
     */
    public function getIsAddedNew()
    {
        return $this->isAddedNew;
    }

    /**
     * Set is_attended
     *
     * boolean is_attended
     */
    public function setIsAttended($isAttended)
    {
        $this->isAttended = $isAttended;
        
        return $this;
    }

    /**
     * Get isAttended
     *
     * @return boolean
     */
    public function getIsAttended()
    {
        return $this->isAttended;
    }

    /**
     *
     * @param string $studentFirstName            
     */
    public function setStudentFirstName($studentFirstName)
    {
        $this->studentFirstName = $studentFirstName;
    }

    /**
     *
     * @return string
     */
    public function getStudentFirstName()
    {
        return $this->studentFirstName;
    }

    /**
     *
     * @param string $studentLastName            
     */
    public function setStudentLastName($studentLastName)
    {
        $this->studentLastName = $studentLastName;
    }

    /**
     *
     * @return string
     */
    public function getStudentLastName()
    {
        return $this->studentLastName;
    }
    
    /**
     *
     * @param string $studentStatus
     */
    public function setStudentStatus($studentStatus)
    {
        $this->studentStatus = $studentStatus;
    }
    
    /**
     *
     * @return string
     */
    public function getStudentStatus()
    {
        return $this->studentStatus;
    }
    
}