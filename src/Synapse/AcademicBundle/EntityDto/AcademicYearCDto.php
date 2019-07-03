<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for AcademicYear
 *
 * @package Synapse\AcademicBundle\EntityDto
 */
class AcademicYearCDto
{

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $id;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $organizationId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $name;

    /**
     *
     * @param int $id            
     */
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $yearId;

    /**
     * startDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $startDate;

    /**
     * endDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $endDate;

    /**
     * canDelete
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $canDelete;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $organizationId            
     */
    public function setOrganization($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $yearId            
     */
    public function setYearId($yearId)
    {
        $this->yearId = $yearId;
    }

    /**
     *
     * @return string
     */
    public function getYearId()
    {
        return $this->yearId;
    }

    /**
     *
     * @param mixed $startDate            
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     *
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @param mixed $endDate            
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     *
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     *
     * @param boolean $canDelete            
     */
    public function setCanDelete($canDelete)
    {
        $this->canDelete = $canDelete;
    }

    /**
     *
     * @return boolean
     */
    public function getCanDelete()
    {
        return $this->canDelete;
    }
}   