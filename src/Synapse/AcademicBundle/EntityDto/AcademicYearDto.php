<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Data Transfer Object for AcademicYear
 *
 * @package Synapse\AcademicBundle\EntityDto
 */
class AcademicYearDto
{

    /**
     * Organization's internal academic year ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Academic year's internal organization ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Name of academic year
     *
     * @var string @JMS\Type("string")
     */
    private $name;
    
    /**
     * Year ID associated with the organization's academic year
     *
     * @var string @JMS\Type("string")
     */
    private $yearId;

    /**
     * Start date of the academic year
     *
     * @var DateTime @JMS\Type("DateTime<'Y-m-d'>")
     *     
     */
    private $startDate;

    /**
     * End date of the academic year
     *
     * @var DateTime @JMS\Type("DateTime<'Y-m-d'>")
     *     
     */
    private $endDate;

    /**
     * Indicates whether a user can delete the specified academic year
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $canDelete;

    /**
     * Indicates whether the academic year is the current academic year
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isCurrentYear;

    /**
     *
     * @var array @JMS\Type("array<Synapse\AcademicBundle\EntityDto\AcademicTermDto>")
     *
     */
    private $academicTerms;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the academic year's Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the organization Id
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organizationId;
    }

    /**
     * Sets the organization Id
     *
     * @param int $organizationId
     */
    public function setOrganization($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the academic years name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the academic year's name
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the academic year Id
     *
     * @return string
     */
    public function getYearId()
    {
        return $this->yearId;
    }

    /**
     * Sets the academic year Id
     *
     * @param string $yearId            
     */
    public function setYearId($yearId)
    {
        $this->yearId = $yearId;
    }

    /**
     * Returns the academic year's start date
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the academic year's start date
     *
     * @param DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Returns the academic year's end date
     *
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the academic year's end date
     *
     * @param DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * Returns the boolean value can delete
     *
     * @return boolean
     */
    public function getCanDelete()
    {
        return $this->canDelete;
    }

    /**
     * Sets the boolean value can delete
     *
     * @param boolean $canDelete            
     */
    public function setCanDelete($canDelete)
    {
        $this->canDelete = $canDelete;
    }

    /**
     * returns the boolean value is current year
     *
     * @return boolean
     */
    public function getIsCurrentYear()
    {
        return $this->isCurrentYear;
    }

    /**
     * Sets the boolean value is current year
     *
     * @param boolean $isCurrentYear
     */
    public function setIsCurrentYear($isCurrentYear)
    {
    	$this->isCurrentYear = $isCurrentYear;
    }


    /**
     *
     * @return array
     */
    public function getAcademicTerms()
    {
        return $this->academicTerms;
    }

    /**
     *
     * @param array $academicTerms
     */
    public function setAcademicTerms($academicTerms)
    {
        $this->academicTerms = $academicTerms;
    }
}