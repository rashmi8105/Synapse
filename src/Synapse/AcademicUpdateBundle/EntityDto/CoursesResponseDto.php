<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Courses
 *
 * @package Synapse\RestBundle\Entity
 */
class CoursesResponseDto
{

    /**
     * total_course_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalCourseCount;

    /**
     * organization_id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * course_details
     *
     * @var string @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\CourseDetailsResponseDto>")
     *     
     */
    private $courseDetails;

    /**
     *
     * @param int $totalCourseCount            
     */
    public function setTotalCourseCount($totalCourseCount)
    {
        $this->totalCourseCount = $totalCourseCount;
    }

    /**
     *
     * @return int
     */
    public function getTotalCourseCount()
    {
        return $this->totalCourseCount;
    }

    /**
     *
     * @param string $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param string $courseDetails       
     */
    public function setCourseDetails($courseDetails)
    {
        $this->courseDetails = $courseDetails;
    }

    /**
     *
     * @return array
     */
    public function getCourseDetails()
    {
        return $this->courseDetails;
    }
}