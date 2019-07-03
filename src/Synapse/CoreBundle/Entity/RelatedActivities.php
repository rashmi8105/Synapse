<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * RelatedActivities
 *
 * @ORM\Table(name="related_activities", indexes={@ORM\Index(name="fk_activities_related_activity_log1_idx",columns={"activity_log_id"}),@ORM\Index(name="fk_activities_related_organization1_idx",columns={"organization_id"}),@ORM\Index(name="fk_activities_related_contacts1_idx",columns={"contacts_id"}),@ORM\Index(name="fk_activities_related_note1_idx",columns={"note_id"}) })
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\RelatedActivitiesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class RelatedActivities extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\ActivityLog
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityLog")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="activity_log_id", referencedColumnName="id")
     * })
     */
    private $activityLog;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     *
     * @JMS\Expose
     */
    private $createdOn;

    /**
     * @var \Synapse\CoreBundle\Entity\Contacts
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Contacts")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contacts_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $contacts;

    /**
     * @var \Synapse\CoreBundle\Entity\Note
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Note")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $note;
    
    
    /**
     * @var \Synapse\CoreBundle\Entity\Appointments
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Appointments")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $appointment;
    
    /**
     * @var \Synapse\CoreBundle\Entity\Referrals
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Referrals")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="referral_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $referral;
    
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
     * @return RelatedActivities
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return RelatedActivities
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
     * Set activityLog
     *
     * @param \Synapse\CoreBundle\Entity\ActivityLog $activityLog
     * @return RelatedActivities
     */
    public function setActivityLog(\Synapse\CoreBundle\Entity\ActivityLog $activityLog = null)
    {
        $this->activityLog = $activityLog;

        return $this;
    }
    /**
     * Get activityLog
     *
     * @return \Synapse\CoreBundle\Entity\ActivityLog
     */
    public function getActivityLog()
    {
        return $this->activityLog;
    }

    /**
     * @param \Datetime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return \Datetime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
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
     * @param \Synapse\CoreBundle\Entity\Note $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }
    /**
     * @return \Synapse\CoreBundle\Entity\Note
     */
    public function getNote()
    {
        return $this->note;
    }
    
    /**
     * @param \Synapse\CoreBundle\Entity\Appointments $appointment
     */
    public function setAppointment($appointment)
    {
        $this->appointment = $appointment;
    }
    /**
     * @return \Synapse\CoreBundle\Entity\Appointments
     */
    public function getAppointment()
    {
        return $this->appointment;
    }
    
    
    /**
     * @return \Synapse\CoreBundle\Entity\Referrals
     */
    public function setReferral($referral)
    {
        $this->referral = $referral;
    }
    /**
     * @return \Synapse\CoreBundle\Entity\Referrals
     */
    public function getReferrals()
    {
        return $this->referral;
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