<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Academic Update
 *
 * @package Synapse\RestBundle\Entity
 */
class AcademicUpdateDetailsStudentDto
{

    /**
     * ID of an Academic Update Request.
     *
     * @var integer @JMS\Type("integer")
     */
    private $academicUpdateId;

    /**
     * ID of a Student.
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * First name of a Student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentFirstname;

    /**
     * Last name of a Student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentLastname;

    /**
     * The Risk level of a Student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentRisk;

    /**
     * Current grade of a student in a course that lasts multiple terms.
     *
     * @var string @JMS\Type("string")
     */
    private $studentInProgressGrade;
    
    
    /**
     * Final grade for a student in a specified course.
     *
     * @var string @JMS\Type("string")
     */
    private $studentGrade;

    /**
     * Number of absences that a Student has.
     *
     * @var string @JMS\Type("string")
     */
    private $studentAbsences;

    /**
     * Comments associated with a Student's profile.
     *
     * @var string @JMS\Type("string")
     */
    private $studentComments;

    /**
     * Whether a Student has been referred for Academic Assistance or not.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $studentAcademicAssistRefer;

    /**
     * Boolean to indicate whether a student has been notified about an Academic Update Request.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $studentSend;
    
    /**
     * Deprecated and no longer in use.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isBypassed;

    /**
     * Set the ID of an Academic Update Request.
     *
     * @param int $academicUpdateId
     */
    public function setAcademicUpdateId($academicUpdateId)
    {
        $this->academicUpdateId = $academicUpdateId;
    }

    /**
     * Returns the ID of an Academic Update Request.
     *
     * @return int
     */
    public function getAcademicUpdateId()
    {
        return $this->academicUpdateId;
    }

    /**
     * Set the number of Absences a Student has.
     *
     * @param int $studentAbsences
     */
    public function setStudentAbsences($studentAbsences)
    {
        $this->studentAbsences = $studentAbsences;
    }

    /**
     * Returns the number of Absences a Student has.
     *
     * @return int
     */
    public function getStudentAbsences()
    {
        return $this->studentAbsences;
    }

    /**
     * Set whether a Student has been Referred for Academic Assistance or not.
     *
     * @param boolean $studentAcademicAssistRefer
     */
    public function setStudentAcademicAssistRefer($studentAcademicAssistRefer)
    {
        $this->studentAcademicAssistRefer = $studentAcademicAssistRefer;
    }

    /**
     * Returns whether a Student has been Referred for Academic Assistance or not.
     *
     * @return boolean
     */
    public function getStudentAcademicAssistRefer()
    {
        return $this->studentAcademicAssistRefer;
    }

    /**
     * Set the Comments on a Student's profile.
     *
     * @param string $studentComments
     */
    public function setStudentComments($studentComments)
    {
        $this->studentComments = $studentComments;
    }

    /**
     * Returns the Comments set on a Student's profile.
     *
     * @return string
     */
    public function getStudentComments()
    {
        return $this->studentComments;
    }

    /**
     * Set a Student's First Name.
     *
     * @param string $studentFirstname
     */
    public function setStudentFirstname($studentFirstname)
    {
        $this->studentFirstname = $studentFirstname;
    }

    /**
     * Returns a Student's First Name.
     *
     * @return string
     */
    public function getStudentFirstname()
    {
        return $this->studentFirstname;
    }

    /**
     * Set a Student's ID.
     *
     * @param int $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * Returns the ID of a Student.
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Set the current grade for a student in a course that lasts multiple terms.
     *
     * @param string $studentInProgressGrade
     */
    public function setStudentInProgressGrade($studentInProgressGrade)
    {
        $this->studentInProgressGrade = $studentInProgressGrade;
    }

    /**
     * Returns the current grade for a student in a course that lasts multiple terms.
     *
     * @return string
     */
    public function getStudentInProgressGrade()
    {
        return $this->studentInProgressGrade;
    }

    /**
     * Set the Last Name of a Student.
     *
     * @param string $studentLastname
     */
    public function setStudentLastname($studentLastname)
    {
        $this->studentLastname = $studentLastname;
    }

    /**
     * Returns the Last Name of a Student.
     *
     * @return string
     */
    public function getStudentLastname()
    {
        return $this->studentLastname;
    }

    /**
     * Set the Risk level of a Student.
     *
     * @param string $studentRisk
     */
    public function setStudentRisk($studentRisk)
    {
        $this->studentRisk = $studentRisk;
    }

    /**
     * Returns the Risk level of a Student.
     *
     * @return string
     */
    public function getStudentRisk()
    {
        return $this->studentRisk;
    }

    /**
     * Set whether or not a student has been notified about an Academic Update Request.
     *
     * @param boolean $studentSend
     */
    public function setStudentSend($studentSend)
    {
        $this->studentSend = $studentSend;
    }

    /**
     * Returns whether or not a student has been notified about an Academic Update Request.
     *
     * @return boolean
     */
    public function getStudentSend()
    {
        return $this->studentSend;
    }

    /**
     *
     * @deprecated
     * @param boolean $isBypassed
     */
    public function setIsBypassed($isBypassed)
    {
        $this->isBypassed = $isBypassed;
    }
    
    /**
     *
     * @deprecated
     * @return boolean
     */
    public function getIsBypassed()
    {
        return $this->isBypassed;
    }

    /**
     * Set the Final grade for a student in a course.
     *
     * @param string $studentGrade
     */
    public function setStudentGrade($studentGrade)
    {
        $this->studentGrade = $studentGrade;
    }

    /**
     * Returns the Final grade for a student in a course.
     *
     * @return string
     */
    public function getStudentGrade()
    {
        return $this->studentGrade;
    }


}