<?php
namespace Synapse\RestBundle\Entity;

use Faker\Provider\cs_CZ\DateTime;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 *
 * 
 * @package Synapse\RestBundle\Entity
 */
class AppointmentsDto
{

    /**
     * Appointment Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $appointmentId;

    /**
     * Person Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * Person Proxy Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personIdProxy;

    /**
     * Organization Id
     * 
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     */
    private $organizationId;

    /**
     * Appointment Details
     * 
     * @var string @JMS\Type("string")
     */
    private $detail;

    /**
     * Details Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $detailId;

    /**
     * Appointment Location
     * 
     * @var string @JMS\Type("string")
     */
    private $location;

    /**
     * Appointment Description
     * 
     * @var string @JMS\Type("string")
     */
    private $description;

    /**
     * Office Hours Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $officeHoursId;

    /**
     * Is Free Standing
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isFreeStanding;

    /**
     * Appointment Type
     * 
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * Attending Student Id
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\AttendeesDto>")
     *      @Assert\NotBlank()
     *     
     */
    private $attendees;

    /**
     * Date and Time Appointment Starts
     * 
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $slotStart;

    /**
     * Date and Time Appointment Ends
     * 
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $slotEnd;
    
     /**
     * Source
     * 
     * @var string @JMS\Type("string")
     */
    private $source;
    
     /**
     * Google Appointment Id
     * 
     * @var string @JMS\Type("string")
     */
    private $googleAppointmentId;
    
    /**
     * Share option used in calendar sharing
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\ShareOptionsDto>")
     */
    private $shareOptions;
    
    /**
     * Activity Log Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $activityLogId;

    /**
     * Returns the appointment Id
     *
     * @return integer
     */
    public function getAppointmentId()
    {
        return $this->appointmentId;
    }

    /**
     * Sets the appointment Id
     *
     * @param integer $appointmentId
     */
    public function setAppointmentId($appointmentId)
    {
        $this->appointmentId = $appointmentId;
    }

    /**
     * Returns the person Id
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Sets the person Id
     *
     * @param integer $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * Returns the proxy person's Id
     *
     * @return integer
     */
    public function getPersonIdProxy()
    {
        return $this->personIdProxy;
    }

    /**
     * Sets the proxy person's Id
     *
     * @param integer $personIdProxy
     */
    public function setPersonIdProxy($personIdProxy)
    {
        $this->personIdProxy = $personIdProxy;
    }

    /**
     * Returns the organization Id
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the organization Id
     *
     * @param integer $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the appointment detail information
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Sets the appointment detail information
     *
     * @param string $detail            
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    /**
     * Returns the appointment Details Id
     *
     * @return integer
     */
    public function getDetailId()
    {
        return $this->detailId;
    }

    /**
     * Sets the appointment details Id
     *
     * @param integer $detailId
     */
    public function setDetailId($detailId)
    {
        $this->detailId = $detailId;
    }

    /**
     * Returns the appointment location information
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets the appointment location information
     *
     * @param string $location            
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Returns the appointment Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the appointment description
     *
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the office hours Id
     *
     * @return integer
     */
    public function getOfficeHoursId()
    {
        return $this->officeHoursId;
    }

    /**
     * Sets the office hours Id
     *
     * @param integer $officeHoursId
     */
    public function setOfficeHoursId($officeHoursId)
    {
        $this->officeHoursId = $officeHoursId;
    }

    /**
     * Returns is free standing boolean value
     *
     * @return boolean
     */
    public function getIsFreeStanding()
    {
        return $this->isFreeStanding;
    }

    /**
     * Sets is free standing boolean value
     *
     * @param boolean $isFreeStanding            
     */
    public function setIsFreeStanding($isFreeStanding)
    {
        $this->isFreeStanding = $isFreeStanding;
    }

    /**
     * Returns appointment type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets appointment type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the students Id who will be attending the appointment
     *
     * @return Object
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * Sets the students Id who will be attending the appointment
     *
     * @param Object $attendees            
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;
    }

    /**
     * Returns the starting time of the appointment
     *
     * @return DateTime
     */
    public function getSlotStart()
    {
        return $this->slotStart;
    }

    /**
     * Sets the starting time of the appointment
     *
     * @param DateTime $slotStart
     */
    public function setSlotStart($slotStart)
    {
        $this->slotStart = $slotStart;
    }

    /**
     * Returns the ending time of the appointment
     *
     * @return DateTime
     */
    public function getSlotEnd()
    {
        return $this->slotEnd;
    }

    /**
     * Sets the ending time of the appointment
     *
     * @param DateTime $slotEnd;
     */
    public function setSlotEnd($slotEnd)
    {
        $this->slotEnd = $slotEnd;
    }

    /**
     * Returns the source value
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the source value
     *
     * @param string $source            
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Returns the google appointment Id
     *
     * @return string
     */
    public function getGoogleAppointmentId()
    {
        return $this->googleAppointmentId;
    }
    
    /**
     * Sets the google appointment Id
     *
     * @param string $googleAppointmentId            
     */
    public function setGoogleAppointmentId($googleAppointmentId)
    {
        $this->googleAppointmentId = $googleAppointmentId;
    }

    /**
     * Returns the shareOptionDto
     *
     * @return Object
     */
    public function getShareOptions()
    {
        return $this->shareOptions;
    }

    
    /**
     * Sets the shareOptionDto
     *
     * @param Object $shareOptions
     */
    public function setShareOptions($shareOptions)
    {
        $this->shareOptions = $shareOptions;
    }

    /**
     * Returns activity log Id
     *
     * @return integer
     */
    public function getActivityLogId()
    {
        return $this->activityLogId;
    }

    /**
     * Sets the activity log Id
     *
     * @param integer $activityLogId
     */
    public function setActivityLogId($activityLogId)
    {
        $this->activityLogId = $activityLogId;
    }
}