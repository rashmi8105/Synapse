<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class AcademicTermListResponseDto
{

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * academicYearId
     *
     * @var integer @JMS\Type("integer")
     */
    private $academicYearId;

    /**
     *
     * @var array @JMS\Type("array<Synapse\AcademicBundle\EntityDto\AcademicTermDto>")
     *     
     */
    private $academicTerms;

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
     * @param integer $academicYearId
     */
    public function setAcademicYearId($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    /**
     *
     * @return integer
     */
    public function getAcademicYearId()
    {
        return $this->academicYearId;
    }

    /**
     *
     * @param array $academicTerms
     */
    public function setAcademicTerms($academicTerms)
    {
        $this->academicTerms = $academicTerms;
    }

    /**
     *
     * @return array
     */
    public function getAcademicTerms()
    {
        return $this->academicTerms;
    }
}