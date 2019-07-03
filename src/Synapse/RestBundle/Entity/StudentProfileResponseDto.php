<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Reponser object for Student Related profiles
 */
class StudentProfileResponseDto
{

    /**
     * student_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personStudentId;

    /**
     * staff_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personStaffId;

    /**
     * student_id
     * 
     * @var integer @JMS\Type("array")
     */
    private $profile;

    /**
     *
     * @param int $profile            
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     *
     * @return int
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     *
     * @param int $personStudentId            
     */
    public function setPersonStudentId($personStudentId)
    {
        $this->personStudentId = $personStudentId;
    }

    /**
     *
     * @return int
     */
    public function getPersonStudentId()
    {
        return $this->personStudentId;
    }

    /**
     *
     * @param int $personStaffId            
     */
    public function setPersonStaffId($personStaffId)
    {
        $this->personStaffId = $personStaffId;
    }

    /**
     *
     * @return int
     */
    public function getPersonStaffId()
    {
        return $this->personStaffId;
    }
}