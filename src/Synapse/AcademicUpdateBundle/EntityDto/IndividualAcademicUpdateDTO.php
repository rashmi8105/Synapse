<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\SynapseConstant;

/**
 * Individual students academic updates
 *
 * @package Synapse\RestBundle\Entity
 */
class IndividualAcademicUpdateDTO
{
    /**
     * Faculty ID who has submitted the academic updates.
     *
     * @var string @JMS\Type("string")
     */
    private $facultyIdSubmitted;

    /**
     * Academic update submitted date.
     *
     * @var \Datetime @JMS\Type("DateTime")
     */
    private $dateSubmitted;

    /**
     * Failure risk level for a student.
     *
     * @var string @JMS\Type("string")
     */
    private $failureRiskLevel;

    /**
     * In progress grade for the student in the course.
     *
     * @var string @JMS\Type("string")
     */
    private $inProgressGrade;

    /**
     * Number of classes missed by the student.
     *
     * @var integer @JMS\Type("integer")
     */
    private $absences;

    /**
     * Faculty comment specific to the academic update
     *
     * @var string @JMS\Type("string")
     */
    private $comment;

    /**
     * For referring the student for any assistance if required.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $referForAssistance;

    /**
     * Send email notification to student regarding the academic update.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $sendToStudent;

    /**
     * Mapworks internal ID of the academic update.
     *
     * @var int @JMS\Type("integer")
     */
    private $academicUpdateId;

    /**
     * Final grade for the student in the course.
     *
     * @var string @JMS\Type("string")
     */
    private $finalGrade;

    /**
     *
     * @return int
     */
    public function getAcademicUpdateId()
    {
        return $this->academicUpdateId;
    }

    /**
     *
     * @param int $academicUpdateId
     */
    public function setAcademicUpdateId($academicUpdateId)
    {
        $this->academicUpdateId = $academicUpdateId;
    }

    /**
     *
     * @return string
     */
    public function getFacultyIdSubmitted()
    {
        return $this->facultyIdSubmitted;
    }

    /**
     *
     * @param string $facultyIdSubmitted
     */
    public function setFacultyIdSubmitted($facultyIdSubmitted)
    {
        $this->facultyIdSubmitted = $facultyIdSubmitted;
    }

    /**
     * @return \DateTime
     */
    public function getDateSubmitted()
    {
        return $this->dateSubmitted;
    }

    /**
     * @param \DateTime $dateSubmitted
     */
    public function setDateSubmitted($dateSubmitted)
    {
        $this->dateSubmitted = $dateSubmitted;
    }

    /**
     * @return string
     */
    public function getFailureRiskLevel()
    {
        return $this->failureRiskLevel;
    }

    /**
     * @param string $failureRiskLevel
     */
    public function setFailureRiskLevel($failureRiskLevel)
    {
        $this->failureRiskLevel = $failureRiskLevel;
    }

    /**
     * @return string
     */
    public function getInProgressGrade()
    {
        return $this->inProgressGrade;
    }

    /**
     * @param string $inProgressGrade
     */
    public function setInProgressGrade($inProgressGrade)
    {
        $this->inProgressGrade = $inProgressGrade;
    }

    /**
     *
     * @return int
     */
    public function getAbsences()
    {
        return $this->absences;
    }

    /**
     *
     * @param int $absences
     */
    public function setAbsences($absences)
    {
        $this->absences = $absences;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return boolean
     */
    public function getReferForAssistance()
    {
        return $this->referForAssistance;
    }

    /**
     * @param boolean $referForAssistance
     */
    public function setReferForAssistance($referForAssistance)
    {
        $this->referForAssistance = $referForAssistance;
    }

    /**
     * @return boolean
     */
    public function getSendToStudent()
    {
        return $this->sendToStudent;
    }

    /**
     * @param boolean $sendToStudent
     */
    public function setSendToStudent($sendToStudent)
    {
        $this->sendToStudent = $sendToStudent;
    }

    /**
     * @return string
     */
    public function getFinalGrade()
    {
        return $this->finalGrade;
    }

    /**
     * @param string $finalGrade
     */
    public function setFinalGrade($finalGrade)
    {
        $this->finalGrade = $finalGrade;
    }


    /**
     * returns an array representation fo the IndividualUpdateDTOObject
     * This will return an array in the format ["important_value" => "value"]
     *
     * @return array
     */
    public function __toArray() {
        $objectVariablesArray = get_object_vars($this);
        $IndividualAcademicUpdateDTOArray = [];

        // adds "_" to each capital letter for each property
        foreach ($objectVariablesArray as $key => $value) {
            $property = strtolower(preg_replace(SynapseConstant::REGEX_STRING_FIND_UPPERCASE, SynapseConstant::REGEX_UNDERSCORE_VARIABLE, $key));
            $IndividualAcademicUpdateDTOArray[$property] = $value;
        }
        return $IndividualAcademicUpdateDTOArray;
    }

}