<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Courses Students Adhoc Academic Updates
 *
 * @package Synapse\RestBundle\Entity
 */
class CoursesStudentsAdhocAcademicUpdateDTO
{
    /**
     * Group of courses that academic updates are being submitted against
     *
     * @var CourseAdhocAcademicUpdateDTO[]
     * @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\CourseAdhocAcademicUpdateDTO>")
     *
     */
    private $coursesWithAcademicUpdates;

    /**
     * @param CourseAdhocAcademicUpdateDTO[] $coursesWithAcademicUpdates
     */
    public function setCoursesWithAcademicUpdates($coursesWithAcademicUpdates)
    {
        $this->coursesWithAcademicUpdates = $coursesWithAcademicUpdates;
    }

    /**
     * @return CourseAdhocAcademicUpdateDTO[]
     */
    public function getCoursesWithAcademicUpdates()
    {
        return $this->coursesWithAcademicUpdates;
    }
}
