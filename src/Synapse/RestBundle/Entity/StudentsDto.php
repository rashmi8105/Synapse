<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentsDto
{

    /**
     * student_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * student_first_name
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $studentFirstName;

    /**
     * student_last_name
     * 
     * @var string @JMS\Type("string")
     */
    private $studentLastName;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;

    /**
     * @JMS\Type("array")
     *
     * @var array
     */
    private $staticListDetails;
    

    /**
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     *
     * @param string $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
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
     * @return string
     */
    public function getStudentLastName()
    {
        return $this->studentLastName;
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

    /**
     *
     * @param array $studentStatus
     */
    public function setStaticListDetails($staticListDetails)
    {
        $this->staticListDetails = $staticListDetails;
    }
    
    /**
     *
     * @return array
     */
    public function getStaticListDetails()
    {
        return $this->staticListDetails;
    }

}