<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseListDTO
{
    /**
     * Courses within the list
     *
     * @var CourseDTO[]
     * @JMS\Type("array<Synapse\AcademicBundle\EntityDto\CourseDTO>")
     */
    private $courseList;

    /**
     * @param CourseDTO[] $courseList
     */
    public function setCourseList($courseList)
    {
        $this->courseList = $courseList;
    }

    /**
     * @return CourseDTO[]
     */
    public function getCourseList()
    {
        return $this->courseList;
    }
}