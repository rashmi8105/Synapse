<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentContactsDto
{

    /**
     * person_student_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personStudentId;

    /**
     * person_staff_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personStaffId;

    /**
     * total_contacts
     * 
     * @var integer @JMS\Type("integer")
     */
    private $totalContacts;

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
     * @param string $personStudentId            
     */
    public function setPersonStudentId($personStudentId)
    {
        $this->personStudentId = $personStudentId;
    }

    /**
     *
     * @return int
     */
    public function getPersonStaffId()
    {
        return $this->personStaffId;
    }

    /**
     *
     * @param string $personStaffId            
     */
    public function setPersonStaffId($personStaffId)
    {
        $this->personStaffId = $personStaffId;
    }

    /**
     *
     * @return int
     */
    public function getTotalContacts()
    {
        return $this->totalContacts;
    }

    /**
     *
     * @param int $totalContacts            
     */
    public function setTotalContacts($totalContacts)
    {
        $this->totalContacts = $totalContacts;
    }
}