<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Faculty
 *
 * @package Synapse\RestBundle\Entity
 */
class FacultiesResponseDto
{

    /**
     * total_staff_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStaffCount;

    /**
     * organization_id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * staff_details
     *
     * @var string @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\FacultiesDetailsResponseDto>")
     *     
     */
    private $staffDetails;

    /**
     *
     * @param int $totalStaffCount            
     */
    public function setTotalStaffCount($totalStaffCount)
    {
        $this->totalStaffCount = $totalStaffCount;
    }

    /**
     *
     * @return int
     */
    public function getTotalStaffCount()
    {
        return $this->totalStaffCount;
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
    public function setStaffDetails($staffDetails)
    {
        $this->staffDetails = $staffDetails;
    }

    /**
     *
     * @return array
     */
    public function getStaffDetails()
    {
        return $this->staffDetails;
    }
}