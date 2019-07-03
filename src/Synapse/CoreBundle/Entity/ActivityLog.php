<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;

/**
 * ActivityLog
 *
 * @ORM\Table(name="activity_log", indexes={@ORM\Index(name="fk_activity_referrals1_idx", columns={"referrals_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ActivityLogRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ActivityLog extends BaseEntity implements OwnableAssetEntityInterface
{
    public static $TYPES = [
        'R' => 'Referral',
        'A' => 'Appointment',
        'N' => 'Note',
        'L' => 'Login',
        'C' => 'Contact',
        'E' => 'Email',
    ];

    /**
     *
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="person_id_faculty", referencedColumnName="id")
     * })
     */
    private $personIdFaculty;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     * })
     */
    private $personIdStudent;

    /**
     *
     * @var string
     * @ORM\Column(name="activity_type", type="string", length=1, nullable=true)
     * @JMS\Expose
     */
    private $activityType;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="activity_date", type="datetime", nullable=true)
     */
    private $activityDate;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Referrals
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Referrals")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="referrals_id", referencedColumnName="id")
     * })
     */
    private $referrals;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Appointments
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Appointments")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="appointments_id", referencedColumnName="id")
     * })
     */
    private $appointments;

    /**
     *
     * @var string
     * @ORM\Column(name="reason", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $reason;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Note
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Note")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $note;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Contacts
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Contacts")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contacts_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $contacts;
    
    /**
     *
     * @var \Synapse\CoreBundle\Entity\Email
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Email")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $email;

    /**
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return ActivityLog
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
     * Set personIdFaculty
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdFaculty
     * @return ActivityLog
     */
    public function setPersonIdFaculty(\Synapse\CoreBundle\Entity\Person $personIdFaculty = null)
    {
        $this->personIdFaculty = $personIdFaculty;
        return $this;
    }

    /**
     * Get personIdFaculty
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdFaculty()
    {
        return $this->personIdFaculty;
    }

    /**
     * Set personIdStudent
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdStudent   
     * @return ActivityLog
     */
    public function setPersonIdStudent(\Synapse\CoreBundle\Entity\Person $personIdStudent = null)
    {
        $this->personIdStudent = $personIdStudent;
        return $this;
    }

    /**
     * Get personIdStudent
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdStudent()
    {
        return $this->personIdStudent;
    }

    /**
     * Set activityType
     *
     * @param string $activityType
     * @return ActivityLog
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
        return $this;
    }

    /**
     * Get activityType
     *
     * @return string
     */
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     * Set activityDate
     *
     * @param \DateTime $activityDate
     * @return ActivityLog
     */
    public function setActivityDate($activityDate)
    {
        $this->activityDate = $activityDate;
        return $this;
    }

    /**
     * Get activityDate
     *
     * @return \DateTime
     */
    public function getActivityDate()
    {
        return $this->activityDate;
    }

    /**
     * Set referrals
     *
     * @param \Synapse\CoreBundle\Entity\Referrals $referrals 
     * @return ActivityLog
     */
    public function setReferrals(\Synapse\CoreBundle\Entity\Referrals $referrals = null)
    {
        $this->referrals = $referrals;
        return $this;
    }

    /**
     * Get referrals
     *
     * @return \Synapse\CoreBundle\Entity\Referrals
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * Set appointments
     *
     * @param \Synapse\CoreBundle\Entity\Appointments $appointments
     * @return ActivityLog
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
     * Set reason
     *
     * @param string $reason
     * @return ActivityLog
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Note $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Note
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Contacts $contacts
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;
    }
    
    /**
     * @return \Synapse\CoreBundle\Entity\Contacts
    */
    public function getContacts()
    {
        return $this->contacts;
    }
    
    /**
     *
     * @param \Synapse\CoreBundle\Entity\Email $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    /**
     * @return \Synapse\CoreBundle\Entity\Email
     */
    public function getEmail()
    {
        return $this->email;
    }
}
