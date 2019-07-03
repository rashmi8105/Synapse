<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Faculty
 *
 * @package Synapse\RestBundle\Entity
 */
class FacultyDto
{
    /**
     * faculy_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $facultyId;
    
    /**
     * faculty_first_name
     *
     * @var string @JMS\Type("string")
     *
     */
    private $facultyFirstName;
    
    /**
     * faculty_last_name
     *
     * @var string @JMS\Type("string")
     */
    private $facultyLastName;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $facultyStatus;
    
    /**
     *
     * @return int
     */
    public function getFacultyId()
    {
        return $this->facultyId;
    }
    
    /**
     *
     * @param string $facultyId
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
    }
    
    /**
     *
     * @return string
     */
    public function getFacultyFirstName()
    {
        return $this->facultyFirstName;
    }
    
    /**
     *
     * @param string $facultyFirstName
     */
    public function setFacultyFirstName($facultyFirstName)
    {
        $this->facultyFirstName = $facultyFirstName;
    }    
    /**
     *
     * @return string
     */
    public function getFacultyLastName()
    {
        return $this->facultyLastName;
    }
    
    /**
     *
     * @param string $facultyLastName
     */
    public function setFacultyLastName($facultyLastName)
    {
        $this->facultyLastName = $facultyLastName;
    }
    
    /**
     *
     * @param string $facultyStatus
     */
    public function setFacultyStatus($facultyStatus)
    {
        $this->facultyStatus = $facultyStatus;
    }
    
    /**
     *
     * @return string
     */
    public function getFacultyStatus()
    {
        return $this->facultyStatus;
    }
    
}