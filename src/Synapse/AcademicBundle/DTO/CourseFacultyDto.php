<?php
namespace Synapse\AcademicBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Course Faculty DTO
 */
class CourseFacultyDto
{
    /**
     * External Id of the course at the organization.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $courseExternalId;

    /**
     * Array of faculty in the specified course at the organization. Includes, for each faculty, the faculty's external ID and their permissionset name for the course.
     *
     * @var array
     * @JMS\Type("array")
     */
    private $facultyList;

    /**
     * @return string
     */
    public function getCourseExternalId()
    {
        return $this->courseExternalId;
    }

    /**
     * @param string $courseExternalId
     */
    public function setCourseExternalId($courseExternalId)
    {
        $this->courseExternalId = $courseExternalId;
    }

    /**
     * @return array
     */
    public function getFacultyList()
    {
        return $this->facultyList;
    }

    /**
     * @param array $facultyList
     */
    public function setFacultyList($facultyList)
    {
        $this->facultyList = $facultyList;
    }
}
