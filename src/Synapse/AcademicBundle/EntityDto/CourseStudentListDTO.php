<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseStudentListDTO
{
    /**
     * Array of course-student pairs.
     *
     * @var CourseStudentsDto[]
     * @JMS\Type("array<Synapse\AcademicBundle\EntityDto\CourseStudentsDto>")
     */
    private $courseStudentList;

    /**
     * @return CourseStudentsDto[]
     */
    public function getCourseStudentList()
    {
        return $this->courseStudentList;
    }

    /**
     * @param CourseStudentsDto[] $courseStudentList
     */
    public function setCourseStudentList($courseStudentList)
    {
        $this->courseStudentList = $courseStudentList;
    }


}