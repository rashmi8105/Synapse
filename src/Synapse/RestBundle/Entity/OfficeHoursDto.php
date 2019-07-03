<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OfficeHoursDto
{

    /**
     * Internal mapworks ID of the office hour.
     *
     * @JMS\Type("integer")
     */
    private $officeHoursId;

    /**
     * Internal mapworks person ID of the creator of the office hour / series
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $personId;

    /**
     * Internal mapworks person ID of the person proxying in as the creator of the office hour / series
     *
     * @JMS\Type("integer")
     */
    private $personIdProxy;

    /**
     * Internal mapworks person ID of the person proxying in and cancelling the office hour/series as the creator of the office hour / series
     *
     * @JMS\Type("integer")
     */
    private $personIdProxyCancelled;

    /**
     * Internal mapworks organization ID of the creator of the office hour / series
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $organizationId;

    /**
     * Type of office hour being created. 'I' for individual office hours, 'S' for series
     *
     * @JMS\Type("string")
     * @Assert\Choice({"I", "S"})
     */
    private $slotType;

    /**
     * Start date and start time of the office hour / series
     *
     * @var \Datetime
     * @JMS\Type("DateTime")
     */
    private $slotStart;

    /**
     * End date and end time of the office hour / series
     *
     * @var \Datetime
     * @JMS\Type("DateTime")
     */
    private $slotEnd;

    /**
     * Meeting place of the office hour / series
     *
     * @JMS\Type("string")
     * @Assert\NotBlank()
     */
    private $location;

    /**
     * Length of the office hour / individual office hours within a series
     *
     * @JMS\Type("string")
     */
    private $meetingLength;

    /**
     * Internal mapworks appointment ID of the appointment linked to the office hour / individual office hour within a series
     *
     * @JMS\Type("integer")
     */
    private $appointmentId;

    /**
     * Flag to determine whether an individual office hour is cancelled
     *
     * @JMS\Type("boolean")
     */
    private $isCancelled;

    /**
     * OfficeHoursSeries JSON object. See attributes below for details.
     *
     * @JMS\Type("Synapse\RestBundle\Entity\OfficeHoursSeriesDto")
     */
    private $seriesInfo;
    
    /**
     * Flag to indicate whether or not the update converts a single office hour to a series of office hours.
     *
     * @JMS\Type("boolean")
     */
    private $oneToSeries;
    
    /**
     * Calendar-specific ID value of the linked office hour / series in an external calendaring system.
     *
     * @JMS\Type("string")
     */
    private $pcsCalendarId;

    /**
     * Return office location
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set office location
     *
     * @param mixed $location            
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Return meeting length
     *
     * @return mixed
     */
    public function getMeetingLength()
    {
        return $this->meetingLength;
    }

    /**
     * Set meeting length
     *
     * @param mixed $meetingLength            
     */
    public function setMeetingLength($meetingLength)
    {
        $this->meetingLength = $meetingLength;
    }

    /**
     * Return organization Id
     *
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set organization Id
     *
     * @param mixed $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Return person Id
     *
     * @return mixed
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set person Id
     *
     * @param mixed $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * Return proxy person Id
     *
     * @return mixed
     */
    public function getPersonIdProxy()
    {
        return $this->personIdProxy;
    }

    /**
     * Set proxy person Id
     *
     * @param mixed $personIdProxy            
     */
    public function setPersonIdProxy($personIdProxy)
    {
        $this->personIdProxy = $personIdProxy;
    }

    /**
     * Return office hours end time
     *
     * @return \DateTime
     */
    public function getSlotEnd()
    {
        return $this->slotEnd;
    }

    /**
     * Set office hours end time
     *
     * @param mixed $slotEnd            
     * @var \Datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    public function setSlotEnd($slotEnd)
    {
        $this->slotEnd = $slotEnd;
    }

    /**
     * Return office hours start time
     *
     * @return \DateTime
     */
    public function getSlotStart()
    {
        return $this->slotStart;
    }

    /**
     * Set office hours start time
     *
     * @param mixed $slotStart            
     * @var \Datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    public function setSlotStart($slotStart)
    {
        $this->slotStart = $slotStart;
    }

    /**
     * Return office hours type
     *
     * @return mixed
     */
    public function getSlotType()
    {
        return $this->slotType;
    }

    /**
     * Set office hours type
     *
     * @param mixed $slotType            
     */
    public function setSlotType($slotType)
    {
        $this->slotType = $slotType;
    }

    /**
     * Return appointment Id
     *
     * @return integer
     */
    public function getAppointmentId()
    {
        return $this->appointmentId;
    }

    /**
     * Set appointment Id
     *
     * @param integer $appointmentId            
     */
    public function setAppointmentId($appointmentId)
    {
        $this->appointmentId = $appointmentId;
    }

    /**
     * Return office hours Id
     *
     * @return integer
     */
    public function getOfficeHoursId()
    {
        return $this->officeHoursId;
    }

    /**
     * Set office hours Id
     *
     * @param integer $officeHoursId
     */
    public function setOfficeHoursId($officeHoursId)
    {
        $this->officeHoursId = $officeHoursId;
    }

    /**
     * Return office hours series information
     *
     * @return mixed
     */
    public function getSeriesInfo()
    {
        return $this->seriesInfo;
    }

    /**
     * Set office hours series information
     *
     * @param mixed $seriesInfo            
     */
    public function setSeriesInfo($seriesInfo)
    {
        $this->seriesInfo = $seriesInfo;
    }

    /**
     * Return isCanceled
     *
     * @return boolean
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * Set isCanceled
     *
     * @param boolean $isCancelled            
     */
    public function setisCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;
    }

    /**
     * Return proxy person Id who canceled
     *
     * @return mixed
     */
    public function getPersonIdProxyCancelled()
    {
        return $this->personIdProxyCancelled;
    }

    /**
     * Set proxy person Id who canceled
     *
     * @param mixed $personIdProxyCancelled            
     */
    public function setPersonIdProxyCancelled($personIdProxyCancelled)
    {
        $this->personIdProxyCancelled = $personIdProxyCancelled;
    }

    /**
     * return one to series
     *
     * @return mixed
     */
    public function getOneToSeries()
    {
        return $this->oneToSeries;
    }

    /**
     * Set one to series
     *
     * @param mixed $oneToSeries
     */
    public function setOneToSeries($oneToSeries)
    {
        $this->oneToSeries = $oneToSeries;
    }

    /**
     * Return calendar Id
     *
     * @return string
     */
    public function getPcsCalendarId()
    {
        return $this->pcsCalendarId;
    }

    /**
     * Set calendar Id
     *
     * @param string $pcsCalendarId            
     */
    public function setPcsCalendarId($pcsCalendarId)
    {
        $this->pcsCalendarId = $pcsCalendarId;
    }
}