<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Academic Update Student History
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentHistoryDetailsDto
{

    /**
     *
     * @var @JMS\Type("DateTime")
     */
    private $date;

    /**
     * $failureRisk
     *
     * @var string @JMS\Type("string")
     */
    private $failureRisk;

    /**
     * $grade
     *
     * @var string @JMS\Type("string")
     */
    private $grade;

    /**
     * $absences
     *
     * @var integer @JMS\Type("integer")
     */
    private $absences;

    /**
     * $comments
     *
     * @var string @JMS\Type("string")
     */
    private $comments;

    /**
     * $academicAssistRefer
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $academicAssistRefer;

    /**
     * $studentSend
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $studentSend;

    /**
     *
     * @param mixed $date            
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     *
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     *
     * @param string $failureRisk            
     */
    public function setFailureRisk($failureRisk)
    {
        $this->failureRisk = $failureRisk;
    }

    /**
     *
     * @return string
     */
    public function getFailureRisk()
    {
        return $this->failureRisk;
    }

    /**
     *
     * @param string $grade            
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }
    
    /**
     * @param int $absences
     */
    public function setAbsences($absences)
    {
        $this->absences = $absences;
    }
    
    /**
     * @return int
     */
    public function getAbsences()
    {
        return $this->absences;
    }
    
    /**
     * @param string $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
    
    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }
    
    /**
     * @param boolean $academicAssistRefer
     */
    public function setAcademicAssistRefer($academicAssistRefer)
    {
        $this->academicAssistRefer = $academicAssistRefer;
    }
    
    /**
     * @return boolean
     */
    public function getAcademicAssistRefer()
    {
        return $this->academicAssistRefer;
    }
    
    /**
     * @param boolean $studentSend
     */
    public function setStudentSend($studentSend)
    {
        $this->studentSend = $studentSend;
    }
    
    /**
     * @return boolean
     */
    public function getStudentSend()
    {
        return $this->studentSend;
    }
}