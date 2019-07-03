<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentsDetailsResponseDto
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
     *     
     */
    private $studentExternalId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentFirstname;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentLastname;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentEmail;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;

    /**
     *
     * @param int $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

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
     * @param string $studentExternalId            
     */
    public function setStudentExternalId($studentExternalId)
    {
        $this->studentExternalId = $studentExternalId;
    }

    /**
     *
     * @return string
     */
    public function getStudentExternalId()
    {
        return $this->studentExternalId;
    }

    /**
     *
     * @param string $studentFirstname            
     */
    public function setStudentFirstname($studentFirstname)
    {
        $this->studentFirstname = $studentFirstname;
    }

    /**
     *
     * @return string
     */
    public function getStudentFirstname()
    {
        return $this->studentFirstname;
    }

    /**
     *
     * @param string $studentLastname            
     */
    public function setStudentLastname($studentLastname)
    {
        $this->studentLastname = $studentLastname;
    }

    /**
     *
     * @return string
     */
    public function getStudentLastname()
    {
        return $this->studentLastname;
    }

    /**
     *
     * @param string $studentEmail            
     */
    public function setStudentEmail($studentEmail)
    {
        $this->studentEmail = $studentEmail;
    }

    /**
     *
     * @return string
     */
    public function getStudentEmail()
    {
        return $this->studentEmail;
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