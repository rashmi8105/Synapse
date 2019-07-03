<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OfficeHoursSeries
 *
 * @ORM\Table(name="office_hours_series", indexes={@ORM\Index(name="fk_office_hours_series_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_office_hours_series_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_office_hours_series_person2_idx", columns={"person_id_proxy"}), @ORM\Index(name="exchange_master_appointment_id_idx", columns={"exchange_master_appointment_id","last_synced"}), @ORM\Index(name="google_master_appointment_id_idx", columns={"google_master_appointment_id","last_synced"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OfficeHoursSeries extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="days", type="string", length=7, nullable=true)
     */
    private $days;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=45, nullable=true)
     */
    private $location;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="slot_start", type="datetime", nullable=true)
     */
    private $slotStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="slot_end", type="datetime", nullable=true)
     */
    private $slotEnd;

    /**
     * @var integer
     *
     * @ORM\Column(name="meeting_length", type="integer", nullable=true)
     */
    private $meetingLength;

    /**
     * @var string
     *
     * @ORM\Column(name="standing_instructions", type="string", length=255, nullable=true)
     */
    private $standingInstructions;

    /**
     * @var string
     *
     * @ORM\Column(name="repeat_pattern", type="string", length=3, nullable=true)
     */
    private $repeatPattern;

    /**
     * @var integer
     *
     * @ORM\Column(name="repeat_every", type="integer", nullable=true)
     */
    private $repeatEvery;

    /**
     * @var string
     *
     * @ORM\Column(name="repetition_range", type="string", length=1, nullable=true)
     */
    private $repetitionRange;

    /**
     * @var integer
     *
     * @ORM\Column(name="repetition_occurrence", type="integer", nullable=true)
     */
    private $repetitionOccurrence;

   

    /**
     * @var \Organization
     *
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var \Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     */
    private $person;

    /**
     * @var \Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_proxy", referencedColumnName="id")
     * })
     */
    private $personProxy;

    /**
     * @var integer
     *
     * @ORM\Column(name="repeat_monthly_on", type="integer", nullable=true)
     */
    private $repeatMonthlyOn ;

    /**
     *
     * @var string @ORM\Column(name="exchange_master_appointment_id", type="string", length=100, nullable=true)
     */
    private $exchangeMasterAppointmentId;
    
    /**
     *
     * @var string @ORM\Column(name="google_master_appointment_id", type="string", length=100, nullable=true)
     */
    private $googleMasterAppointmentId;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_synced", type="datetime", nullable=true)
     */
    private $lastSynced;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="include_stat_sun", type="integer", nullable=true)
     */
    private $includeSatSun ;
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set id
     *
     * @return OfficeHoursSeries
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * Set days
     *
     * @param string $days
     * @return OfficeHoursSeries
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Get days
     *
     * @return string 
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return OfficeHoursSeries
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set slotStart
     *
     * @param \DateTime $slotStart
     * @return OfficeHoursSeries
     */
    public function setSlotStart($slotStart)
    {
        $this->slotStart = $slotStart;

        return $this;
    }

    /**
     * Get slotStart
     *
     * @return \DateTime 
     */
    public function getSlotStart()
    {
        return $this->slotStart;
    }

    /**
     * Set slotEnd
     *
     * @param \DateTime $slotEnd
     * @return OfficeHoursSeries
     */
    public function setSlotEnd($slotEnd)
    {
        $this->slotEnd = $slotEnd;

        return $this;
    }

    /**
     * Get slotEnd
     *
     * @return \DateTime 
     */
    public function getSlotEnd()
    {
        return $this->slotEnd;
    }

    /**
     * Set meetingLength
     *
     * @param integer $meetingLength
     * @return OfficeHoursSeries
     */
    public function setMeetingLength($meetingLength)
    {
        $this->meetingLength = $meetingLength;

        return $this;
    }

    /**
     * Get meetingLength
     *
     * @return integer 
     */
    public function getMeetingLength()
    {
        return $this->meetingLength;
    }

    /**
     * Set standingInstructions
     *
     * @param string $standingInstructions
     * @return OfficeHoursSeries
     */
    public function setStandingInstructions($standingInstructions)
    {
        $this->standingInstructions = $standingInstructions;

        return $this;
    }

    /**
     * Get standingInstructions
     *
     * @return string 
     */
    public function getStandingInstructions()
    {
        return $this->standingInstructions;
    }

    /**
     * Set repeatPattern
     *
     * @param string $repeatPattern
     * @return OfficeHoursSeries
     */
    public function setRepeatPattern($repeatPattern)
    {
        $this->repeatPattern = $repeatPattern;

        return $this;
    }

    /**
     * Get repeatPattern
     *
     * @return string 
     */
    public function getRepeatPattern()
    {
        return $this->repeatPattern;
    }

    /**
     * Set repeatEvery
     *
     * @param integer $repeatEvery
     * @return OfficeHoursSeries
     */
    public function setRepeatEvery($repeatEvery)
    {
        $this->repeatEvery = $repeatEvery;

        return $this;
    }

    /**
     * Get repeatEvery
     *
     * @return integer 
     */
    public function getRepeatEvery()
    {
        return $this->repeatEvery;
    }

    /**
     * Set repetitionRange
     *
     * @param string $repetitionRange
     * @return OfficeHoursSeries
     */
    public function setRepetitionRange($repetitionRange)
    {
        $this->repetitionRange = $repetitionRange;

        return $this;
    }

    /**
     * Get repetitionRange
     *
     * @return string 
     */
    public function getRepetitionRange()
    {
        return $this->repetitionRange;
    }

    /**
     * Set repetitionOccurrence
     *
     * @param integer $repetitionOccurrence
     * @return OfficeHoursSeries
     */
    public function setRepetitionOccurrence($repetitionOccurrence)
    {
        $this->repetitionOccurrence = $repetitionOccurrence;

        return $this;
    }

    /**
     * Get repetitionOccurrence
     *
     * @return integer 
     */
    public function getRepetitionOccurrence()
    {
        return $this->repetitionOccurrence;
    }

   
    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OfficeHoursSeries
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return OfficeHoursSeries
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set personProxy
     *
     * @param \Synapse\CoreBundle\Entity\Person $personProxy
     * @return OfficeHoursSeries
     */
    public function setPersonProxy(\Synapse\CoreBundle\Entity\Person $personProxy = null)
    {
        $this->personProxy = $personProxy;

        return $this;
    }

    /**
     * Get personProxy
     *
     * @return \Synapse\CoreBundle\Entity\Person 
     */
    public function getPersonProxy()
    {
        return $this->personProxy;
    }

    /**
     * @param int $repeatMonthlyOn
     */
    public function setRepeatMonthlyOn($repeatMonthlyOn)
    {
        $this->repeatMonthlyOn = $repeatMonthlyOn;
    }

    /**
     * @return int
     */
    public function getRepeatMonthlyOn()
    {
        return $this->repeatMonthlyOn;
    }
    
    /**
     * Set exchangeMasterAppointmentId
     *
     * @param string $exchangeMasterAppointmentId
     * @return OfficeHours
     */
    public function setExchangeMasterAppointmentId($exchangeMasterAppointmentId)
    {
        $this->exchangeMasterAppointmentId = $exchangeMasterAppointmentId;
    
        return $this;
    }
    
    /**
     * Get exchangeMasterAppointmentId
     *
     * @return string
     */
    public function getExchangeMasterAppointmentId()
    {
        return $this->exchangeMasterAppointmentId;
    }
    
    /**
     * Set googleMasterAppointmentId
     *
     * @param string $googleMasterAppointmentId
     * @return OfficeHours
     */
    public function setGoogleMasterAppointmentId($googleMasterAppointmentId)
    {
        $this->googleMasterAppointmentId = $googleMasterAppointmentId;
    
        return $this;
    }
    
    /**
     * Get googleMasterAppointmentId
     *
     * @return string
     */
    public function getGoogleMasterAppointmentId()
    {
        return $this->googleMasterAppointmentId;
    }
    
    /**
     * Set lastSynced
     *
     * @param \DateTime $lastSynced
     * @return OfficeHours
     */
    public function setLastSynced($lastSynced)
    {
        $this->lastSynced = $lastSynced;
    
        return $this;
    }
    
    /**
     * Get lastSynced
     *
     * @return \DateTime
     */
    public function getLastSynced()
    {
        return $this->lastSynced;
    }

    /**
     * @param int $includeSatSun
     */
    public function setIncludeSatSun($includeSatSun)
    {
        $this->includeSatSun = $includeSatSun;
    }

    /**
     * @return int
     */
    public function getIncludeSatSun()
    {
        return $this->includeSatSun;
    }


}