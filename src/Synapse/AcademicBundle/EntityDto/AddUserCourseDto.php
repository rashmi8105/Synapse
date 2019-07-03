<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class AddUserCourseDto
{

    /**
     * Type of user, Can be student or faculty
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * Course internal ID for the course being updated
     *
     * @var integer @JMS\Type("integer")
     */
    private $courseId;

    /**
     * Person internal ID of the person being added to the course
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * Return course type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set course type
     *
     * @param string $type            
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Return course ID
     *
     * @return integer
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * Set course ID
     *
     * @param integer $courseId            
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     * Return person ID
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set person ID
     *
     * @param integer $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }
}