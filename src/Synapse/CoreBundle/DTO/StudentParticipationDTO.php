<?php

namespace Synapse\CoreBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StudentDto
 *
 * @package Synapse\CoreBundle\DTO
 */
class StudentParticipationDTO
{
    /**
     * Id of the student that is being updated
     *
     * @var int @JMS\Type("integer")
     */
    private $studentId;

    /**
     * The participation status to which the student will be set.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isParticipant;

    /**
     * The active status to which the student will be set.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isActive;

    /**
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param int $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * @return boolean
     */
    public function getIsParticipant()
    {
        return $this->isParticipant;
    }

    /**
     * @param boolean $isParticipant
     */
    public function setIsParticipant($isParticipant)
    {
        $this->isParticipant = $isParticipant;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }
}