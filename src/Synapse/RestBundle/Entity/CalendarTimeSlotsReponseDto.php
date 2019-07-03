<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class CalendarTimeSlotsReponseDto
{

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $appointmentId;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $organizationId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $campusName;

    /**
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $slotStart;

    /**
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $slotEnd;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $appointmentTimeZone;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $personId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $personFirstName;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $personLastName;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $personTitle;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $location;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $reason;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $description;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $reasonId;

    /**
     * @JMS\Type("string")
     *
     * @var integer
     */
    private $slotType;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $officeHoursId;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $isSlotCancelled;

    /**
     * attendees student id
     *
     * @var array @JMS\Type("array<Synapse\RestBundle\Entity\AttendeesDto>")
     *     
     *     
     */
    private $attendees;
    
    /**
     * @JMS\Type("string")
     */
    private $pcsCalendarId;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $isConflictedFlag;

    /**
     * @var integer
     * @JMS\Type("integer")
     */
    private $managedPersonId;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $managedPersonFirstName;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $managedPersonLastName;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $isAllDayEvent;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $isMultiDayEvent;

    /**
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $allDaySlotStart;

    /**
     * @JMS\Type("DateTime")
     *
     * @var DateTime
     */
    private $allDaySlotEnd;

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
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param string $campusName            
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }

    /**
     *
     * @return string
     */
    public function getCampusName()
    {
        return $this->campusName;
    }

    /**
     *
     * @param int $slotType            
     */
    public function setSlotType($slotType)
    {
        $this->slotType = $slotType;
    }

    /**
     *
     * @return int
     */
    public function getSlotType()
    {
        return $this->slotType;
    }

    /**
     *
     * @param \DateTime $slotStart
     */
    public function setSlotStart($slotStart)
    {
        $this->slotStart = $slotStart;
    }

    /**
     *
     * @return \DateTime
     */
    public function getSlotStart()
    {
        return $this->slotStart;
    }

    /**
     *
     * @param int $reasonId            
     */
    public function setReasonId($reasonId)
    {
        $this->reasonId = $reasonId;
    }

    /**
     *
     * @return int
     */
    public function getReasonId()
    {
        return $this->reasonId;
    }

    /**
     *
     * @param array $attendees
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;
    }

    /**
     *
     * @return array
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     *
     * @param boolean $isSlotCancelled            
     */
    public function setIsSlotCancelled($isSlotCancelled)
    {
        $this->isSlotCancelled = $isSlotCancelled;
    }

    /**
     *
     * @return boolean
     */
    public function getIsSlotCancelled()
    {
        return $this->isSlotCancelled;
    }

    /**
     *
     * @param string $location            
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     *
     * @param int $officeHoursId            
     */
    public function setOfficeHoursId($officeHoursId)
    {
        $this->officeHoursId = $officeHoursId;
    }

    /**
     *
     * @return int
     */
    public function getOfficeHoursId()
    {
        return $this->officeHoursId;
    }

    /**
     *
     * @param string $reason            
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
    
    /**
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param \DateTime $slotEnd
     */
    public function setSlotEnd($slotEnd)
    {
        $this->slotEnd = $slotEnd;
    }

    /**
     *
     * @return \DateTime
     */
    public function getSlotEnd()
    {
        return $this->slotEnd;
    }
    
    /**
     *
     * @param string $appointmentTimeZone
     */
    public function setAppointmentTimeZone($appointmentTimeZone)
    {
        $this->appointmentTimeZone = $appointmentTimeZone;
    }
    
    /**
     *
     * @return string
     */
    public function getAppointmentTimeZone()
    {
        return $this->appointmentTimeZone;
    }

    /**
     *
     * @param int $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param string $personFirstName            
     */
    public function setPersonFirstName($personFirstName)
    {
        $this->personFirstName = $personFirstName;
    }

    /**
     *
     * @return string
     */
    public function getPersonFirstName()
    {
        return $this->personFirstName;
    }

    /**
     *
     * @param string $personLastName            
     */
    public function setPersonLastName($personLastName)
    {
        $this->personLastName = $personLastName;
    }

    /**
     *
     * @return string
     */
    public function getPersonLastName()
    {
        return $this->personLastName;
    }

    /**
     *
     * @param string $personTitle            
     */
    public function setPersonTitle($personTitle)
    {
        $this->personTitle = $personTitle;
    }

    /**
     *
     * @return string
     */
    public function getPersonTitle()
    {
        return $this->personTitle;
    }
    
    /**
     *
     * @param string $pcsCalendarId            
     */
    public function setPcsCalendarId($pcsCalendarId)
    {
        $this->pcsCalendarId = $pcsCalendarId;
    }

    /**
     *
     * @return string
     */
    public function getPcsCalendarId()
    {
        return $this->pcsCalendarId;
    }

    /**
     *
     * @param boolean $isConflictedFlag
     */
    public function setIsConflictedFlag($isConflictedFlag)
    {
        $this->isConflictedFlag = $isConflictedFlag;
    }

    /**
     *
     * @return boolean
     */
    public function getIsConflictedFlag()
    {
        return $this->isConflictedFlag;
    }

    /**
     *
     * @param int $managedPersonId
     */
    public function setManagedPersonId($managedPersonId) {
        $this->managedPersonId = $managedPersonId;
    }

    /**
     *
     * @return int
     */
    public function getManagedPersonId()
    {
        return $this->managedPersonId;
    }

    /**
     *
     * @param string $managedPersonFirstName
     */
    public function setManagedPersonFirstName($managedPersonFirstName) {
        $this->managedPersonFirstName = $managedPersonFirstName;
    }

    /**
     *
     * @return string
     */
    public function getManagedPersonFirstName()
    {
        return $this->managedPersonFirstName;
    }

    /**
     *
     * @param string $managedPersonLastName
     */
    public function setManagedPersonLastName($managedPersonLastName) {
        $this->managedPersonLastName = $managedPersonLastName;
    }

    /**
     *
     * @return string
     */
    public function getManagedPersonLastName()
    {
        return $this->managedPersonLastName;
    }

    /**
     *
     * @param boolean $isAllDayEvent
     */
    public function setIsAllDayEvent($isAllDayEvent)
    {
        $this->isAllDayEvent = $isAllDayEvent;
    }

    /**
     *
     * @return boolean
     */
    public function getIsAllDayEvent()
    {
        return $this->isAllDayEvent;
    }

    /**
     *
     * @param boolean $isMultiDayEvent
     */
    public function setIsMultiDayEvent($isMultiDayEvent)
    {
        $this->isMultiDayEvent = $isMultiDayEvent;
    }

    /**
     *
     * @return boolean
     */
    public function getIsMultiDayEvent()
    {
        return $this->isMultiDayEvent;
    }

    /**
     *
     * @param \DateTime $allDaySlotStart
     */
    public function setAllDaySlotStart($allDaySlotStart)
    {
        $this->allDaySlotStart = $allDaySlotStart;
    }

    /**
     *
     * @return \DateTime
     */
    public function getAllDaySlotStart()
    {
        return $this->allDaySlotStart;
    }

    /**
     *
     * @param \DateTime $allDaySlotEnd
     */
    public function setAllDaySlotEnd($allDaySlotEnd)
    {
        $this->allDaySlotEnd = $allDaySlotEnd;
    }

    /**
     *
     * @return \DateTime
     */
    public function getAllDaySlotEnd()
    {
        return $this->allDaySlotEnd;
    }
}