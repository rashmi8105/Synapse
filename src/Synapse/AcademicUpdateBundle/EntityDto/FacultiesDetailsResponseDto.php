<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Faculties
 *
 * @package Synapse\RestBundle\Entity
 */
class FacultiesDetailsResponseDto
{

    /**
     * staff_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $staffId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $staffFirstname;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $staffLastname;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $staffEmail;

    /**
     *
     * @var string @JMS\Type("string")
     *
     */
    private $staffExternalId;

    /**
     *
     * @param int $staffId            
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;
    }

    /**
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->staffId;
    }

    /**
     *
     * @param string $staffFirstname            
     */
    public function setStaffFirstname($staffFirstname)
    {
        $this->staffFirstname = $staffFirstname;
    }

    /**
     *
     * @return string
     */
    public function getStaffFirstname()
    {
        return $this->staffFirstname;
    }

    /**
     *
     * @param string $staffLastname            
     */
    public function setStaffLastname($staffLastname)
    {
        $this->staffLastname = $staffLastname;
    }

    /**
     *
     * @return string
     */
    public function getStaffLastname()
    {
        return $this->staffLastname;
    }

    /**
     *
     * @param string $staffEmail            
     */
    public function setStaffEmail($staffEmail)
    {
        $this->staffEmail = $staffEmail;
    }

    /**
     *
     * @return string
     */
    public function getStaffEmail()
    {
        return $this->staffEmail;
    }

    /**
     *
     * @param string $staffExternalId
     */
    public function setStaffExternalId($staffExternalId)
    {
        $this->staffExternalId = $staffExternalId;
    }

    /**
     *
     * @return string
     */
    public function getStaffExternalId()
    {
        return $this->staffExternalId;
    }

}