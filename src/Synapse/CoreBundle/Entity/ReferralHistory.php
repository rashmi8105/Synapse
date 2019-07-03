<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * ReferralHistory
 *
 * @ORM\Table(name="referral_history")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ReferralHistoryRepository")
 * @JMS\ExclusionPolicy("all")
 */
class ReferralHistory
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Referrals
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Referrals")
     * @ORM\JoinColumn(name="referral_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $referral;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", columnDefinition="ENUM('create','update','close','reopen','reassign','interested party')", nullable=false)
     * @JMS\Expose
     */
    private $action;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @JMS\Expose
     */
    private $createdAt;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="person_id_assigned_to", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $personAssignedTo;

    /**
     * @var \Synapse\CoreBundle\Entity\ActivityCategory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     * @ORM\JoinColumn(name="activity_category_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $activityCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=4000, nullable=true)
     * @JMS\Expose
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=1, nullable=true)
     * @JMS\Expose
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_leaving", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $leaving;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_discussed", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $discussed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="referrer_permission", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $referrerPermission;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_high_priority", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $highPriority;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notify_student", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $notifyStudent;

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
     * @var boolean
     *
     * @ORM\Column(name="is_reason_routed", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $reasonRouted;

    /**
     * @var string
     *
     * @ORM\Column(name="user_key", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $userKey;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Referrals
     */
    public function getReferral()
    {
        return $this->referral;
    }

    /**
     * @param Referrals $referral
     */
    public function setReferral($referral)
    {
        $this->referral = $referral;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return Person
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param Person $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Person
     */
    public function getPersonAssignedTo()
    {
        return $this->personAssignedTo;
    }

    /**
     * @param Person $personAssignedTo
     */
    public function setPersonAssignedTo($personAssignedTo)
    {
        $this->personAssignedTo = $personAssignedTo;
    }

    /**
     * @return ActivityCategory
     */
    public function getActivityCategory()
    {
        return $this->activityCategory;
    }

    /**
     * @param ActivityCategory $activityCategory
     */
    public function setActivityCategory($activityCategory)
    {
        $this->activityCategory = $activityCategory;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return boolean
     */
    public function isLeaving()
    {
        return $this->leaving;
    }

    /**
     * @param boolean $leaving
     */
    public function setLeaving($leaving)
    {
        $this->leaving = $leaving;
    }

    /**
     * @return boolean
     */
    public function isDiscussed()
    {
        return $this->discussed;
    }

    /**
     * @param boolean $discussed
     */
    public function setDiscussed($discussed)
    {
        $this->discussed = $discussed;
    }

    /**
     * @return boolean
     */
    public function isReferrerPermission()
    {
        return $this->referrerPermission;
    }

    /**
     * @param boolean $referrerPermission
     */
    public function setReferrerPermission($referrerPermission)
    {
        $this->referrerPermission = $referrerPermission;
    }

    /**
     * @return boolean
     */
    public function isHighPriority()
    {
        return $this->highPriority;
    }

    /**
     * @param boolean $highPriority
     */
    public function setHighPriority($highPriority)
    {
        $this->highPriority = $highPriority;
    }

    /**
     * @return boolean
     */
    public function isNotifyStudent()
    {
        return $this->notifyStudent;
    }

    /**
     * @param boolean $notifyStudent
     */
    public function setNotifyStudent($notifyStudent)
    {
        $this->notifyStudent = $notifyStudent;
    }

    /**
     * @return boolean
     */
    public function isAccessPrivate()
    {
        return $this->accessPrivate;
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
    public function isAccessPublic()
    {
        return $this->accessPublic;
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
    public function isAccessTeam()
    {
        return $this->accessTeam;
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
    public function isReasonRouted()
    {
        return $this->reasonRouted;
    }

    /**
     * @param boolean $reasonRouted
     */
    public function setReasonRouted($reasonRouted)
    {
        $this->reasonRouted = $reasonRouted;
    }

    /**
     * @return string
     */
    public function getUserKey()
    {
        return $this->userKey;
    }

    /**
     * @param string $userKey
     */
    public function setUserKey($userKey)
    {
        $this->userKey = $userKey;
    }

}