<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseFacultyListDTO
{
    /**
     * Array of course-faculty pairings.
     *
     * @var CourseFacultyDTO[]
     *
     *      @JMS\Type("array<Synapse\AcademicBundle\EntityDto\CourseFacultyDTO>")
     */
    private $courseFacultyList;

    /**
     * @param CourseFacultyDTO[] $courseFacultyList
     */
    public function setCourseFacultyList($courseFacultyList)
    {
        $this->courseFacultyList = $courseFacultyList;
    }

    /**
     * @return CourseFacultyDTO[]
     */
    public function getCourseFacultyList()
    {
        return $this->courseFacultyList;
    }
}