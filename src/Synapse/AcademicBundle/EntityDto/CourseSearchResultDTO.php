<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\DTO\PaginatedSearchResultDTO;

class CourseSearchResultDTO extends PaginatedSearchResultDTO
{
    /**
     * Array of courses in Mapworks.
     *
     * @var array
     * @JMS\Type("array")
     */
    private $courseList;

    /**
     * @return array
     */
    public function getCourseList()
    {
        return $this->courseList;
    }

    /**
     * @param array $courseList
     */
    public function setCourseList($courseList)
    {
        $this->courseList = $courseList;
    }
}