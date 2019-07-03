<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class AcademicTermDto
{

    /**
     * Academic term's organization ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Organization specific academic year ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $academicYearId;

    /**
     * Academic term's name
     *
     * @var string @JMS\Type("string")
     */
    private $name;

    /**
     * Academic term's internal ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $termId;

    /**
     * Academic term's external ID
     *
     * @var string @JMS\Type("string")
     */
    private $termCode;

    /**
     * Academic term's start date
     *
     * @var \Datetime @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $startDate;

    /**
     * Academic term's end date
     *
     * @var \Datetime @JMS\Type("DateTime<'Y-m-d'>")
     */
    private $endDate;

    /**
     * Indicates whether or not an academic term can be deleted
     *
     * @var boolean
     *
     * @JMS\Type("boolean")
     */
    private $canDelete;
    
    /**
     * Indicates whether or not the term is a term that encompasses the current time
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $currentAcademicTermFlag;

    /**
     * returns the academic term's organization Id
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * sets the academic term's organization Id
     *
     * @param integer $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * returns the organization specific academic term's year Id
     *
     * @return integer
     */
    public function getAcademicYearId()
    {
        return $this->academicYearId;
    }

    /**
     * sets the organization specific academic term's year Id
     *
     * @param integer $academicYearId
     */
    public function setAcademicYearId($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    /**
     * returns the academic term's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * sets the academic term's name
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * returns the academic term's term Id
     *
     * @return integer
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * sets the academic term's term Id
     *
     * @param integer $termId            
     */
    public function setTermId($termId)
    {
        $this->termId = $termId;
    }

    /**
     * returns the academic term's code number
     *
     * @return string
     */
    public function getTermCode()
    {
        return $this->termCode;
    }

    /**
     * sets the academic term's code number
     *
     * @param string $termCode            
     */
    public function setTermCode($termCode)
    {
        $this->termCode = $termCode;
    }

    /**
     * returns the academic term's start date
     *
     * @return \Datetime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * sets the academic term's start date
     *
     * @param \Datetime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * returns the academic term's end date
     *
     * @return \Datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * sets the academic term's end date
     *
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * returns canDelete value which indicates whether an academic term can be deleted
     *
     * @return boolean
     */
    public function getCanDelete()
    {
        return $this->canDelete;
    }

    /**
     * sets canDelete value which indicates whether an academic term can be deleted
     *
     * @param boolean $canDelete            
     */
    public function setCanDelete($canDelete)
    {
        $this->canDelete = $canDelete;
    }

    /**
     * returns currentAcademicTermFlag value which indicates whether an academic term is the current academic term
     *
     * @return boolean
     */
    public function getCurrentAcademicTermFlag()
    {
        return $this->currentAcademicTermFlag;
    }

    /**
     * sets currentAcademicTermFlag value which indicates whether an academic term is the current academic term
     *
     * @param boolean $currentAcademicTermFlag
     */
    public function setCurrentAcademicTermFlag($currentAcademicTermFlag)
    {
    	$this->currentAcademicTermFlag = $currentAcademicTermFlag;
    }
}