<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentsResponseDto
{

    /**
     * total_students_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudentsCount;

    /**
     * organization_id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * student_details
     *
     * @var string @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\StudentsDetailsResponseDto>")
     *     
     */
    private $studentDetails;

    /**
     *
     * @param int $totalStudentsCount            
     */
    public function setTotalStudentsCount($totalStudentsCount)
    {
        $this->totalStudentsCount = $totalStudentsCount;
    }

    /**
     *
     * @return int
     */
    public function getTotalStudentsCount()
    {
        return $this->totalStudentsCount;
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
     * @param string $studentDetails            
     */
    public function setStudentDetails($studentDetails)
    {
        $this->studentDetails = $studentDetails;
    }

    /**
     *
     * @return array
     */
    public function getStudentDetails()
    {
        return $this->studentDetails;
    }
}