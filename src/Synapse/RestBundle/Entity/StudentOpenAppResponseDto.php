<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class StudentOpenAppResponseDto
{

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $personStudentId;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $personStaffId;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $totalAppointments;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $totalMissedAppointments;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $totalAppointmentsByMe;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $totalSameDayAppointmentsByMe;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\AppointmentListArrayResponseDto>")
     *     
     *     
     */
    private $appointments;

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

    /**
     *
     * @param int $totalAppointments            
     */
    public function setTotalAppointments($totalAppointments)
    {
        $this->totalAppointments = $totalAppointments;
    }

    /**
     *
     * @return int
     */
    public function getTotalAppointments()
    {
        return $this->totalAppointments;
    }

    /**
     *
     * @param int $totalMissedAppointments            
     */
    public function setTotalMissedAppointments($totalMissedAppointments)
    {
        $this->totalMissedAppointments = $totalMissedAppointments;
    }

    /**
     *
     * @return int
     */
    public function getTotalMissedAppointments()
    {
        return $this->totalMissedAppointments;
    }

    /**
     *
     * @param int $totalAppointmentsByMe            
     */
    public function setTotalAppointmentsByMe($totalAppointmentsByMe)
    {
        $this->totalAppointmentsByMe = $totalAppointmentsByMe;
    }

    /**
     *
     * @return int
     */
    public function getTotalAppointmentsByMe()
    {
        return $this->totalAppointmentsByMe;
    }

    /**
     *
     * @param int $totalSameDayAppointmentsByMe            
     */
    public function setTotalSameDayAppointmentsByMe($totalSameDayAppointmentsByMe)
    {
        $this->totalSameDayAppointmentsByMe = $totalSameDayAppointmentsByMe;
    }

    /**
     *
     * @return int
     */
    public function getTotalSameDayAppointmentsByMe()
    {
        return $this->totalSameDayAppointmentsByMe;
    }

    /**
     *
     * @param Object $appointments            
     */
    public function setAppointments($appointments)
    {
        $this->appointments = $appointments;
    }

    /**
     *
     * @return Object
     */
    public function getAppointments()
    {
        return $this->appointments;
    }
}