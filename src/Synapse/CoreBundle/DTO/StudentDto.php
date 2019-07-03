<?php

namespace Synapse\CoreBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StudentDto
 *
 * @package Synapse\CoreBundle\DTO
 */
class StudentDto
{
    /**
     * @var int @JMS\Type("integer")
     */
    private $studentId;

    /**
     * @var string @JMS\Type("string")
     */
    private $firstname;

    /**
     * @var string @JMS\Type("string")
     */
    private $lastname;

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
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
}