<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Courses
 *
 * @package Synapse\RestBundle\Entity
 */
class CourseDetailsResponseDto
{

    /**
     * course_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $courseId;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $termName;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $courseName;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $courseCode;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $courseSectionNo;

    /**
     *
     * @param int $courseId            
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     *
     * @return int
     */
    public function getCourseId()
    {
        return $this->courseId;
    }
    
    /**
     *
     * @param string $termName
     */
    public function setTermName($termName)
    {
        $this->termName = $termName;
    }
    
    /**
     *
     * @return string
     */
    public function getTermName()
    {
        return $this->termName;
    }

    /**
     *
     * @param string $courseName            
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;
    }

    /**
     *
     * @return string
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     *
     * @param string $courseCode            
     */
    public function setCourseCode($courseCode)
    {
        $this->courseCode = $courseCode;
    }

    /**
     *
     * @return string
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     *
     * @param string $courseSectionNo           
     */
    public function setCourseSectionNo($courseSectionNo)
    {
        $this->courseSectionNo = $courseSectionNo;
    }

    /**
     *
     * @return string
     */
    public function getCourseSectionNo()
    {
        return $this->courseSectionNo;
    }
}