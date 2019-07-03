<?php

namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Students Academic Updates
 *
 * @package Synapse\RestBundle\Entity
 */
class CourseAdhocAcademicUpdateDTO
{
    /**
     * Course ID
     *
     * @var string @JMS\Type("string")
     */
    private $courseId;

    /**
     * Students grouped with their academic updates.
     *
     * @var StudentAcademicUpdatesDTO[]
     * @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\StudentAcademicUpdatesDTO>")
     *
     */
    private $studentsWithAcademicUpdates;

    /**
     * @param string $courseId
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     * @return string
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * @param StudentAcademicUpdatesDTO[] $studentsWithAcademicUpdates
     */
    public function setStudentsWithAcademicUpdates($studentsWithAcademicUpdates)
    {
        $this->studentsWithAcademicUpdates = $studentsWithAcademicUpdates;
    }

    /**
     * @return StudentAcademicUpdatesDTO[]
     */
    public function getStudentsWithAcademicUpdates()
    {
        return $this->studentsWithAcademicUpdates;
    }
}
