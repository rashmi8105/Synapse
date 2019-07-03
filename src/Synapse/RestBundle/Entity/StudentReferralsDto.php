<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentReferralsDto
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
     * total_referrals_count
     * 
     * @var integer @JMS\Type("integer")
     */
    private $totalReferralsCount;

    /**
     * total_open_referrals_count
     * 
     * @var integer @JMS\Type("integer")
     */
    private $totalOpenReferralsCount;

    /**
     * total_open_referrals_assigned_to_me
     * 
     * @var integer @JMS\Type("integer")
     */
    private $totalOpenReferralsAssignedToMe;

    /**
     * referrals
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\StudentOpenReferralsDto>")
     */
    private $referrals;

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
    public function getTotalReferralsCount()
    {
        return $this->totalReferralsCount;
    }

    /**
     *
     * @param string $totalReferralsCount            
     */
    public function setTotalReferralsCount($totalReferralsCount)
    {
        $this->totalReferralsCount = $totalReferralsCount;
    }

    /**
     *
     * @return int
     */
    public function getTotalOpenReferralsCount()
    {
        return $this->totalOpenReferralsCount;
    }

    /**
     *
     * @param string $totalOpenReferralsCount            
     */
    public function setTotalOpenReferralsCount($totalOpenReferralsCount)
    {
        $this->totalOpenReferralsCount = $totalOpenReferralsCount;
    }

    /**
     *
     * @return int
     */
    public function getTotalOpenReferralsAssignedToMe()
    {
        return $this->totalOpenReferralsAssignedToMe;
    }

    /**
     *
     * @param string $totalOpenReferralsAssignedToMe            
     */
    public function setTotalOpenReferralsAssignedToMe($totalOpenReferralsAssignedToMe)
    {
        $this->totalOpenReferralsAssignedToMe = $totalOpenReferralsAssignedToMe;
    }

    /**
     *
     * @param Object $referrals            
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;
    }
}