<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Academic Update History
 *
 * @package Synapse\RestBundle\Entity
 */
class AcademicUpdateHistoryDto
{

    /**
     * organization_id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * student_id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $studentId;

    /**
     * course_id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $courseId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $courseName;
    
    /**
     * academic_update_history
     *
     * @var string @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\StudentHistoryDetailsDto>")
     *
     */
    private $academicUpdateHistory;
    
    /**
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }
    
    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }
    
    /**
     *
     * @param int $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }
    
    /**
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }
    
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
     * @param array $academicUpdateHistory
     */
    public function setAcademicUpdateHistory($academicUpdateHistory)
    {
        $this->academicUpdateHistory = $academicUpdateHistory;
    }
    
    /**
     *
     * @return array
     */
    public function getAcademicUpdateHistory()
    {
        return $this->academicUpdateHistory;
    }
}