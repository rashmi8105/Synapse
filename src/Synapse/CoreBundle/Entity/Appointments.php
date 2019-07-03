<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * Appointments
 * @ORM\Table(name="Appointments",indexes={@ORM\Index(name="fk_appointments_organization1_idx",columns={"organization_id"}),@ORM\Index(name="fk_appointments_person2_idx",columns={"person_id"}),@ORM\Index(name="fk_appointments_activity_category1_idx", columns={"activity_category_id"}),@ORM\Index(name="fk_appointments_person1_idx", columns={"person_id_proxy"}),@ORM\Index(name="start_date_time_idx", columns={"start_date_time"}),@ORM\Index(name="end_date_time_idx", columns={"end_date_time"})});
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AppointmentsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Appointments extends BaseEntity implements OwnableAssetEntityInterface
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;

    /**
     *
     * @var string @ORM\Column(name="type", type="string", length=1, nullable=true)
     */
    private $type;

    /**
     *
     * @var string @ORM\Column(name="location", type="string", length=45, nullable=true)
     */
    private $location;

    /**
     *
     * @var string @ORM\Column(name="title", type="string", length=1000, nullable=true)
     */
    private $title;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=5000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="start_date_time", type="datetime", nullable=true)
     */
    private $startDateTime;

    /**
     * @ORM\Column(name="end_date_time", type="datetime", nullable=true)
     */
    private $endDateTime;

    /**
     *
     * @var string @ORM\Column(name="attendees", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $attendees;

    /**
     *
     * @var string @ORM\Column(name="occurrence_id", type="string", length=255, nullable=true)
     */
    private $occurrence;

    /**
     *
     * @var string @ORM\Column(name="master_occurrence_id", type="string", length=255, nullable=true)
     */
    private $masterOccurrence;

    /**
     *
     * @var boolean @ORM\Column(name="match_status", type="boolean", length=1, nullable=true)
     *     
     */
    private $matchStatus;

    /**
     * @ORM\Column(name="last_synced", type="datetime", nullable=true)
     */
    private $lastSynced;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\ActivityCategory @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="activity_category_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $activityCategory;

    /**
     *
     * @var boolean @ORM\Column(name="is_free_standing", type="boolean", length=1, nullable=true)
     *     
     */
    private $isFreeStanding;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_proxy", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $personIdProxy;
    
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
     * @var string @ORM\Column(name="exchange_master_appointment_id", type="string", length=100, nullable=true)
     */
    private $exchangeMasterAppointmentId;
    
    /**
     *
     * @var string @ORM\Column(name="google_appointment_id", type="string", length=100, nullable=true)
     */
    private $googleAppointmentId;
    
    /**
     *
     * @var string @ORM\Column(name="google_master_appointment_id", type="string", length=100, nullable=true)
     */
    private $googleMasterAppointmentId;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="access_private", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $accessPrivate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="access_public", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $accessPublic;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="access_team", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $accessTeam;    

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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return Appointments
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
     * @return Appointments
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     * Get person
     *
     * @return Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set type
     *
     * @param string $type            
     * @return Appointments
     */
    public function setType($type)
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set location
     *
     * @param string $location            
     * @return Appointments
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
     * Set title
     *
     * @param string $title            
     * @return Appointments
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description            
     * @return Appointments
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startDateTime
     *
     * @param \DateTime $startDateTime            
     * @return Appointments
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }

    /**
     * Get startDateTime
     *
     * @return \Datetime
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * Set endDateTime
     *
     * @param \DateTime $endDateTime            
     * @return Appointments
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
        return $this;
    }

    /**
     * Get endDateTime
     *
     * @return \Datetime
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     * Set attendees
     *
     * @param string $attendees            
     * @return Appointments
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;
        
        return $this;
    }

    /**
     * Get attendees
     *
     * @return string
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * Set occurrence
     *
     * @param string $occurrence            
     * @return Appointments
     */
    public function setOccurrence($occurrence)
    {
        $this->occurrence = $occurrence;
        
        return $this;
    }

    /**
     * Get occurrence
     *
     * @return string
     */
    public function getOccurrence()
    {
        return $this->occurrence;
    }

    /**
     * Set masterOccurrence
     *
     * @param string $masterOccurrence            
     * @return Appointments
     */
    public function setMasterOccurrence($masterOccurrence)
    {
        $this->masterOccurrence = $masterOccurrence;
        
        return $this;
    }

    /**
     * Get masterOccurrence
     *
     * @return string
     */
    public function getMasterOccurrence()
    {
        return $this->masterOccurrence;
    }

    /**
     *
     * @param boolean $matchStatus            
     */
    public function setMatchStatus($matchStatus)
    {
        $this->matchStatus = $matchStatus;
    }

    /**
     *
     * @return boolean
     */
    public function getMatchStatus()
    {
        return $this->matchStatus;
    }

    /**
     * Set lastSynced
     *
     * @param \DateTime $lastSynced            
     * @return Appointments
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
     * Set activityCategory
     *
     * @param \Synapse\CoreBundle\Entity\ActivityCategory $activityCategory            
     * @return Appointments
     */
    public function setActivityCategory(\Synapse\CoreBundle\Entity\ActivityCategory $activityCategory = null)
    {
        $this->activityCategory = $activityCategory;
        
        return $this;
    }

    /**
     * Get activityCategory
     *
     * @return \Synapse\CoreBundle\Entity\ActivityCategory
     */
    public function getActivityCategory()
    {
        return $this->activityCategory;
    }

    /**
     *
     * @param boolean $isFreeStanding            
     */
    public function setIsFreeStanding($isFreeStanding)
    {
        $this->isFreeStanding = $isFreeStanding;
    }

    /**
     *
     * @return boolean
     */
    public function getIsFreeStanding()
    {
        return $this->isFreeStanding;
    }

    /**
     * Set personIdProxy
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdProxy            
     * @return Appointments
     */
    public function setPersonIdProxy(\Synapse\CoreBundle\Entity\Person $personIdProxy = null)
    {
        $this->personIdProxy = $personIdProxy;
        
        return $this;
    }

    /**
     * Get personIdProxy
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdProxy()
    {
        return $this->personIdProxy;
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
     * @return Appointments
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
     * Set exchangeMasterAppointmentId
     *
     * @param string $exchangeMasterAppointmentId
     * @return Appointments
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
     * Set googleAppointmentId
     *
     * @param string $googleAppointmentId
     * @return Appointments
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
     * Set googleMasterAppointmentId
     *
     * @param string $googleMasterAppointmentId
     * @return Appointments
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
     * @param boolean $accessPrivate
     */
    public function setAccessPrivate($accessPrivate)
    {
        $this->accessPrivate = $accessPrivate;
    }
    
    /**
     * @return boolean
     */
    public function getAccessPrivate()
    {
        return $this->accessPrivate;
    }
    
    /**
     * @param boolean $accessPublic
     */
    public function setAccessPublic($accessPublic)
    {
        $this->accessPublic = $accessPublic;
    }
    
    /**
     * @return boolean
     */
    public function getAccessPublic()
    {
        return $this->accessPublic;
    }
    
    /**
     * @param boolean $accessTeam
     */
    public function setAccessTeam($accessTeam)
    {
        $this->accessTeam = $accessTeam;
    }
    
    /**
     * @return boolean
     */
    public function getAccessTeam()
    {
        return $this->accessTeam;
    }
    
    /**
     * @param \Synapse\CoreBundle\Entity\Person $person
     */
    public function setPersonIdFaculty(Person $person = null)
    {
        $this->person = $person;
    }
    
    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdFaculty()
    {
        return $this->person;
    }
    
    /**
     * @param \Synapse\CoreBundle\Entity\Person $person
     */
    public function setPersonIdStudent(Person $person = null)
    {
        $this->personIdStudent = $person;
    }
    
    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdStudent()
    {
        return $this->person;
    }
}
