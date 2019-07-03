<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * OfficeHours
 *
 * @ORM\Table(name="office_hours", indexes={@ORM\Index(name="fk_office_hours_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_office_hours_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_office_hours_person2_idx", columns={"person_id_proxy_created"}), @ORM\Index(name="fk_office_hours_office_hours_series1_idx", columns={"office_hours_series_id"}), @ORM\Index(name="fk_office_hours_appointments1_idx", columns={"appointments_id"}), @ORM\Index(name="fk_office_hours_person3_idx", columns={"person_id_proxy_cancelled"}), @ORM\Index(name="exchange_appointment_id_idx", columns={"exchange_appointment_id","last_synced"}), @ORM\Index(name="google_appointment_id_idx", columns={"google_appointment_id","last_synced"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OfficeHoursRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"slotStart", "slotEnd","organization","person"},message="Office Hour already exists.")
 */
class OfficeHours extends BaseEntity
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
     * @ORM\Column(name="slot_type", type="string", length=1, nullable=true)
     */
    private $slotType;

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
     * @var boolean
     *
     * @ORM\Column(name="is_cancelled", type="boolean", nullable=true)
     */
    private $isCancelled;

    

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
     * @ORM\ManyToOne(targetEntity="Person" )
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
     *   @ORM\JoinColumn(name="person_id_proxy_created", referencedColumnName="id")
     * })
     */
    private $personProxyCreated;

    /**
     * @var \OfficeHoursSeries
     *
     * @ORM\ManyToOne(targetEntity="OfficeHoursSeries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="office_hours_series_id", referencedColumnName="id")
     * })
     */
    private $officeHoursSeries;

    /**
     * @var \Appointments
     *
     * @ORM\ManyToOne(targetEntity="Appointments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="appointments_id", referencedColumnName="id")
     * })
     */
    private $appointments;

    /**
     * @var \Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_proxy_cancelled", referencedColumnName="id")
     * })
     */
    private $personProxyCancelled;
    
    /**
     *
     * @var string @ORM\Column(name="source", type="string", columnDefinition="enum('S', 'G', 'E')", options={"default":"S"}, precision=0, scale=0, nullable=false, unique=false)
     */
    private $source;
    
    /**
     *
     * @var string @ORM\Column(name="exchange_appointment_id", type="string", length=100, nullable=true)
     */
    private $exchangeAppointmentId;
    
    /**
     *
     * @var string @ORM\Column(name="google_appointment_id", type="string", length=100, nullable=true)
     */
    private $googleAppointmentId;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_synced", type="datetime", nullable=true)
     */
    private $lastSynced;

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
     * @return OfficeHours
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set slotType
     *
     * @param string $slotType
     * @return OfficeHours
     */
    public function setSlotType($slotType)
    {
        $this->slotType = $slotType;

        return $this;
    }

    /**
     * Get slotType
     *
     * @return string 
     */
    public function getSlotType()
    {
        return $this->slotType;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return OfficeHours
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
     * @return OfficeHours
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
     * @return OfficeHours
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
     * @return OfficeHours
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
     * @return OfficeHours
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
     * Set isCancelled
     *
     * @param boolean $isCancelled
     * @return OfficeHours
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get isCancelled
     *
     * @return boolean 
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }

    

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OfficeHours
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
     * @return OfficeHours
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
     * Set personProxyCreated
     *
     * @param \Synapse\CoreBundle\Entity\Person $personProxyCreated
     * @return OfficeHours
     */
    public function setPersonProxyCreated(\Synapse\CoreBundle\Entity\Person $personProxyCreated = null)
    {
        $this->personProxyCreated = $personProxyCreated;

        return $this;
    }

    /**
     * Get personProxyCreated
     *
     * @return \Synapse\CoreBundle\Entity\Person 
     */
    public function getPersonProxyCreated()
    {
        return $this->personProxyCreated;
    }

    /**
     * Set officeHoursSeries
     *
     * @param \Synapse\CoreBundle\Entity\OfficeHoursSeries $officeHoursSeries
     * @return OfficeHours
     */
    public function setOfficeHoursSeries(\Synapse\CoreBundle\Entity\OfficeHoursSeries $officeHoursSeries = null)
    {
        $this->officeHoursSeries = $officeHoursSeries;

        return $this;
    }

    /**
     * Get officeHoursSeries
     *
     * @return \Synapse\CoreBundle\Entity\OfficeHoursSeries 
     */
    public function getOfficeHoursSeries()
    {
        return $this->officeHoursSeries;
    }

    /**
     * Set appointments
     *
     * @param \Synapse\CoreBundle\Entity\Appointments $appointments
     * @return OfficeHours
     */
    public function setAppointments(\Synapse\CoreBundle\Entity\Appointments $appointments = null)
    {
        $this->appointments = $appointments;

        return $this;
    }

    /**
     * Get appointments
     *
     * @return \Synapse\CoreBundle\Entity\Appointments 
     */
    public function getAppointments()
    {
        return $this->appointments;
    }

    /**
     * Set personProxyCancelled
     *
     * @param \Synapse\CoreBundle\Entity\Person $personProxyCancelled
     * @return OfficeHours
     */
    public function setPersonProxyCancelled(\Synapse\CoreBundle\Entity\Person $personProxyCancelled = null)
    {
        $this->personProxyCancelled = $personProxyCancelled;

        return $this;
    }

    /**
     * Get personProxyCancelled
     *
     * @return \Synapse\CoreBundle\Entity\Person 
     */
    public function getPersonProxyCancelled()
    {
        return $this->personProxyCancelled;
    }

    /**
     * Set source
     *
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }
    
    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * Set exchangeAppointmentId
     *
     * @param string $exchangeAppointmentId
     * @return OfficeHours
     */
    public function setExchangeAppointmentId($exchangeAppointmentId)
    {
        $this->exchangeAppointmentId = $exchangeAppointmentId;
    
        return $this;
    }
    
    /**
     * Get exchangeAppointmentId
     *
     * @return string
     */
    public function getExchangeAppointmentId()
    {
        return $this->exchangeAppointmentId;
    }
    
    /**
     * Set googleAppointmentId
     *
     * @param string $googleAppointmentId
     * @return OfficeHours
     */
    public function setGoogleAppointmentId($googleAppointmentId)
    {
        $this->googleAppointmentId = $googleAppointmentId;
    
        return $this;
    }
    
    /**
     * Get googleAppointmentId
     *
     * @return string
     */
    public function getGoogleAppointmentId()
    {
        return $this->googleAppointmentId;
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

}