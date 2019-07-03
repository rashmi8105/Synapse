<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Students Academic Updates
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentAcademicUpdatesDTO
{
    /**
     * Student ID
     *
     * @var string @JMS\Type("string")
     */
    private $studentId;

    /**
     * Academic updates for the student in the course
     *
     * @var IndividualAcademicUpdateDTO[]
     * @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\IndividualAcademicUpdateDTO>")
     *
     */
    private $academicUpdates;

    /**
     * @param string $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param IndividualAcademicUpdateDTO[] $academicUpdates
     */
    public function setAcademicUpdates($academicUpdates)
    {
        $this->academicUpdates = $academicUpdates;
    }

    /**
     * @return IndividualAcademicUpdateDTO[]
     */
    public function getAcademicUpdates()
    {
        return $this->academicUpdates;
    }
}