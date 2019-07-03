<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class AcademicYearListResponseDto
{

    /**
     * organization
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     *
     * @var array @JMS\Type("array<Synapse\AcademicBundle\EntityDto\AcademicYearDto>")
     *     
     */
    private $academicYears;

    /**
     *
     * @param integer $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param array $academicYears
     */
    public function setAcademicYears($academicYears)
    {
        $this->academicYears = $academicYears;
    }

    /**
     *
     * @return array
     */
    public function getAcademicYears()
    {
        return $this->academicYears;
    }
}