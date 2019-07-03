<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class AppointmentListArrayResponseDto
{

    /**
     * appointment_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $appointmentId;

    /**
     * start_date
     * 
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $startDate;

    /**
     * end_date
     * 
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $endDate;

    /**
     *
     * @param int $appointmentId            
     */
    public function setAppointmentId($appointmentId)
    {
        $this->appointmentId = $appointmentId;
    }

    /**
     *
     * @return int
     */
    public function getAppointmentId()
    {
        return $this->appointmentId;
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
}