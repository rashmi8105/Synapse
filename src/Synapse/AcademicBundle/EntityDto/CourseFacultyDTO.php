<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseFacultyDTO
{
    /**
     * Id of the course at the organization
     *
     * @var string
     * @JMS\Type("string")
     */
    private $courseId;

    /**
     * ID of the faculty at the organization
     *
     * @var string
     * @JMS\Type("string")
     */
    private $facultyId;

    /**
     * Permissionset name at the organization
     *
     * @var string
     * @JMS\Type("string")
     */
    private $permissionsetName;

    /**
     *
     * @param string $permissionsetName
     */
    public function setPermissionsetName($permissionsetName)
    {
        $this->permissionsetName = $permissionsetName;
    }

    /**
     *
     * @return string
     */
    public function getPermissionsetName()
    {
        return $this->permissionsetName;
    }

    /**
     *
     * @param string $courseId
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     *
     * @return string
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     *
     * @param string $facultyId
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
    }

    /**
     *
     * @return string
     */
    public function getFacultyId()
    {
        return $this->facultyId;
    }

}