<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\ReportsBundle\EntityDto
 */
class AcademicUpdateReportDto
{
    
    /**
     * $studentId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $studentId;
    
    
    /**
     * $studentFirstName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentFirstName;  
      
    
   
    /**
     * $studentLastName
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $studentLastName;

    /**
     * $studentRiskLevel
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $studentRiskLevel;
    
    /**
     * $courseName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $courseName;

    /**
     * $facultyFirstName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $facultyFirstName;  
   
    /**
     * $facultyLastName
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $facultyLastName;
    
    /**
     * $academicUpdateId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $academicUpdateId;
      
    /**
     * $byRequest
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $byRequest;

    /**
     * $failureRisk
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $failureRisk;

    /**
     * $inprogressGrade
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $inprogressGrade;

    /**
     * $absences
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $absences;
    
    /**
     * $createdAt
     *
     * @var datetime @JMS\Type("DateTime<'Y-m-d'>")
     *     
     */
    private $createdAt;
    
    /**
     * $studentIsActive
     *
     * @var boolean @JMS\Type("boolean")
     *
     */
    private $studentIsActive;
    
    /**
     * $riskText
     *
     * @var string @JMS\Type("string")
     *
     */
    private $riskText;
    
    /**
     * $riskImageName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $riskImageName;
    
    /**
     * $comment
     *
     * @var string @JMS\Type("string")
     *
     */
    private $comment;
    
    /**
     * $termId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $termId;
    
    /**
     * $termName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $termName;
    

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
     * @param boolean $byRequest
     */
    public function setByRequest($byRequest)
    {
        $this->byRequest = $byRequest;
    }

    /**
     * @return boolean
     */
    public function getByRequest()
    {
        return $this->byRequest;
    }

    /**
     * @param \Synapse\ReportsBundle\EntityDto\datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \Synapse\ReportsBundle\EntityDto\datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $facultyFirstName
     */
    public function setFacultyFirstName($facultyFirstName)
    {
        $this->facultyFirstName = $facultyFirstName;
    }

    /**
     * @return string
     */
    public function getFacultyFirstName()
    {
        return $this->facultyFirstName;
    }

    /**
     * @param string $facultyLastName
     */
    public function setFacultyLastName($facultyLastName)
    {
        $this->facultyLastName = $facultyLastName;
    }

    /**
     * @return string
     */
    public function getFacultyLastName()
    {
        return $this->facultyLastName;
    }

    /**
     * @param string $failureRisk
     */
    public function setFailureRisk($failureRisk)
    {
        $this->failureRisk = $failureRisk;
    }

    /**
     * @return string
     */
    public function getFailureRisk()
    {
        return $this->failureRisk;
    }

    /**
     * @param string $inprogressGrade
     */
    public function setInprogressGrade($inprogressGrade)
    {
        $this->inprogressGrade = $inprogressGrade;
    }

    /**
     * @return string
     */
    public function getInprogressGrade()
    {
        return $this->inprogressGrade;
    }

    /**
     * @param int $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param string $studentFirstName
     */
    public function setStudentFirstName($studentFirstName)
    {
        $this->studentFirstName = $studentFirstName;
    }

    /**
     * @return string
     */
    public function getStudentFirstName()
    {
        return $this->studentFirstName;
    }

    /**
     * @param string $studentLastName
     */
    public function setStudentLastName($studentLastName)
    {
        $this->studentLastName = $studentLastName;
    }

    /**
     * @return string
     */
    public function getStudentLastName()
    {
        return $this->studentLastName;
    }

    /**
     * @param string $studentRiskLevel
     */
    public function setStudentRiskLevel($studentRiskLevel)
    {
        $this->studentRiskLevel = $studentRiskLevel;
    }

    /**
     * @return string
     */
    public function getStudentRiskLevel()
    {
        return $this->studentRiskLevel;
    }
    
    /**
     * @param string $courseName
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;
    }
    
    /**
     * @return string
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $riskImageName
     */
    public function setRiskImageName($riskImageName)
    {
        $this->riskImageName = $riskImageName;
    }

    /**
     * @return string
     */
    public function getRiskImageName()
    {
        return $this->riskImageName;
    }

    /**
     * @param string $riskText
     */
    public function setRiskText($riskText)
    {
        $this->riskText = $riskText;
    }

    /**
     * @return string
     */
    public function getRiskText()
    {
        return $this->riskText;
    }

    /**
     * @param boolean $studentIsActive
     */
    public function setStudentIsActive($studentIsActive)
    {
        $this->studentIsActive = $studentIsActive;
    }

    /**
     * @return boolean
     */
    public function getStudentIsActive()
    {
        return $this->studentIsActive;
    }

    /**
     * @param int $termId
     */
    public function setTermId($termId)
    {
        $this->termId = $termId;
    }

    /**
     * @return int
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * @param string $termName
     */
    public function setTermName($termName)
    {
        $this->termName = $termName;
    }

    /**
     * @return string
     */
    public function getTermName()
    {
        return $this->termName;
    }
    
    /**
     * @param int $academicUpdateId
     */
    public function setAcademicUpdateId($academicUpdateId)
    {
        $this->academicUpdateId = $academicUpdateId;
    }
    
    /**
     * @return int
     */
    public function getAcademicUpdateId()
    {
        return $this->academicUpdateId;
    }


}