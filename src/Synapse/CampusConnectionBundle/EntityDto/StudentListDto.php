<?php
namespace Synapse\CampusConnectionBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student List
 *
 * @package Synapse\CampusConnectionBundle\EntityDto
 */
class StudentListDto
{

    /**
     * Student Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * Id of the faculty assigned to the student
     *
     * @var integer @JMS\Type("integer")
     */
    private $staffId;

    /**
     * Whether this faculty is the primary campus connection
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isPrimaryAssigned;

    /**
     * @param int $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param int $staffId
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;
    }

    /**
     * @return int
     */
    public function getStaffId()
    {
        return $this->staffId;
    }

    /**
     * @param boolean $isPrimaryAssigned
     */
    public function setIsPrimaryAssigned($isPrimaryAssigned)
    {
        $this->isPrimaryAssigned = $isPrimaryAssigned;
    }

    /**
     * @return boolean
     */
    public function getIsPrimaryAssigned()
    {
        return $this->isPrimaryAssigned;
    }
}