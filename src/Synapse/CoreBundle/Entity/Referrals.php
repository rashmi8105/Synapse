<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;

/**
 * Referral
 *
 * @ORM\Table(name="referrals")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ReferralRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Referrals extends BaseEntity implements OwnableAssetEntityInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=4000, nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("comment")
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=1, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="is_leaving", type="boolean", nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("student_indicated_to_leave")
     */
    private $isLeaving;

    /**
     * @var string
     *
     * @ORM\Column(name="is_discussed", type="boolean", nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("issue_discussed_with_student")
     */
    private $isDiscussed;

    /**
     * @var string
     *
     * @ORM\Column(name="referrer_permission", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $referrerPermission;

    /**
     * @var string
     *
     * @ORM\Column(name="is_high_priority", type="boolean", nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("high_priority_concern")
     */
    private $isHighPriority;

    /**
     * @var string
     *
     * @ORM\Column(name="notify_student", type="boolean", nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("issue_revealed_to_student")
     */
    private $notifyStudent;

    /**
     * @var string
     *
     * @ORM\Column(name="access_private", type="boolean", nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("private_share")
     */
    private $accessPrivate;

    /**
     * @var string
     *
     * @ORM\Column(name="access_public", type="boolean", nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("public_share")
     */
    private $accessPublic;

    /**
     * @var string
     *
     * @ORM\Column(name="access_team", type="boolean", nullable=true)
     * @JMS\Expose
     * @JMS\SerializedName("teams_share")
     */
    private $accessTeam;

    /**
     * @var string
     *
     * @ORM\Column(name="referral_date", type="datetime", nullable=true)
     * @JMS\Expose
     */
    private $referralDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     * @JMS\SerializedName("referral_id")
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_faculty", referencedColumnName="id")
     * })
     */
    public $personFaculty;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     * })
     */
    public $personStudent;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_assigned_to", referencedColumnName="id")
     * })
     */
    private $personAssignedTo;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\ActivityCategory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_category_id", referencedColumnName="id")
     * })
     */
    private $activityCategory;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reason_routed", type="boolean", nullable=true)
     * @JMS\Expose     
     */
    private $isReasonRouted;
    
    /**
     * @var string
     *
     * @ORM\Column(name="user_key", type="string", length=100, nullable=true)
     */
    private $userKey;
    
    /**
     * Set note
     *
     * @param string $note
     * @return Referrals
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Referrals
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isLeaving
     *
     * @param string $isLeaving
     * @return Referrals
     */
    public function setIsLeaving($isLeaving)
    {
        $this->isLeaving = $isLeaving;

        return $this;
    }

    /**
     * Get isLeaving
     *
     * @return string
     */
    public function getIsLeaving()
    {
        return $this->isLeaving;
    }

    /**
     * Set isDiscussed
     *
     * @param string $isDiscussed
     * @return Referrals
     */
    public function setIsDiscussed($isDiscussed)
    {
        $this->isDiscussed = $isDiscussed;

        return $this;
    }

    /**
     * Get isDiscussed
     *
     * @return string
     */
    public function getIsDiscussed()
    {
        return $this->isDiscussed;
    }

    /**
     * Set referrerPermission
     *
     * @param string $referrerPermission
     * @return Referrals
     */
    public function setReferrerPermission($referrerPermission)
    {
        $this->referrerPermission = $referrerPermission;

        return $this;
    }

    /**
     * Get referrerPermission
     *
     * @return string
     */
    public function getReferrerPermission()
    {
        return $this->referrerPermission;
    }

    /**
     * Set isHighPriority
     *
     * @param string $isHighPriority
     * @return Referrals
     */
    public function setIsHighPriority($isHighPriority)
    {
        $this->isHighPriority = $isHighPriority;

        return $this;
    }

    /**
     * Get isHighPriority
     *
     * @return string
     */
    public function getIsHighPriority()
    {
        return $this->isHighPriority;
    }

    /**
     * Set notifyStudent
     *
     * @param string $notifyStudent
     * @return Referrals
     */
    public function setNotifyStudent($notifyStudent)
    {
        $this->notifyStudent = $notifyStudent;

        return $this;
    }

    /**
     * Get notifyStudent
     *
     * @return string
     */
    public function getNotifyStudent()
    {
        return $this->notifyStudent;
    }

    /**
     * Sets the value of accessPrivate.
     *
     * @param string $accessPrivate the access private
     *
     * @return self
     */
    public function setAccessPrivate($accessPrivate)
    {
        $this->accessPrivate = $accessPrivate;

        return $this;
    }

    /**
     * Gets the value of accessPrivate.
     *
     * @return string
     */
    public function getAccessPrivate()
    {
        return $this->accessPrivate;
    }

    /**
     * Sets the value of accessPublic.
     *
     * @param string $accessPublic the access public
     *
     * @return self
     */
    public function setAccessPublic($accessPublic)
    {
        $this->accessPublic = $accessPublic;

        return $this;
    }

    /**
     * Gets the value of accessPublic.
     *
     * @return string
     */
    public function getAccessPublic()
    {
        return $this->accessPublic;
    }

    /**
     * Sets the value of accessTeam.
     *
     * @param string $accessTeam the access team
     *
     * @return self
     */
    public function setAccessTeam($accessTeam)
    {
        $this->accessTeam = $accessTeam;

        return $this;
    }

    /**
     * Gets the value of accessTeam.
     *
     * @return string
     */
    public function getAccessTeam()
    {
        return $this->accessTeam;
    }

    /**
     * Sets the value of referralDate.
     *
     * @param string $referralDate the referral date
     *
     * @return self
     */
    public function setReferralDate($referralDate)
    {
        $this->referralDate = $referralDate;

        return $this;
    }

    /**
     * Gets the value of referralDate.
     *
     * @return string
     */
    public function getReferralDate()
    {
        return $this->referralDate;
    }

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
     * Set personFaculty
     *
     * @param \Synapse\CoreBundle\Entity\Person $personFaculty
     * @return Referrals
     */
    public function setPersonFaculty(\Synapse\CoreBundle\Entity\Person $personFaculty = null)
    {
        $this->personFaculty = $personFaculty;

        return $this;
    }

    /**
     * Get personFaculty
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonFaculty()
    {
        return $this->personFaculty;
    }
    //Defined for permission set because get/set method name was different
    /**
     * Set personIdFaculty
     *
     * @param \Synapse\CoreBundle\Entity\Person $personFaculty
     * @return Referrals
     */
    public function setPersonIdFaculty(\Synapse\CoreBundle\Entity\Person $personFaculty = null)
    {
        $this->personFaculty = $personFaculty;
    
        return $this;
    }
    /**
     * Get personIdFaculty
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdFaculty()
    {
        return $this->personFaculty;
    }

    /**
     * Set personStudent
     *
     * @param \Synapse\CoreBundle\Entity\Person $personStudent
     * @return Referrals
     */
    public function setPersonStudent(\Synapse\CoreBundle\Entity\Person $personStudent = null)
    {
        $this->personStudent = $personStudent;

        return $this;
    }

    /**
     * Get personStudent
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonStudent()
    {
        return $this->personStudent;
    }
    
    //Defined for permission set because get/set method name was different 
    /**
     * Set personIdStudent
     *
     * @param \Synapse\CoreBundle\Entity\Person $personStudent
     * @return Referrals
     */
    public function setPersonIdStudent(\Synapse\CoreBundle\Entity\Person $personStudent = null)
    {
        $this->personStudent = $personStudent;
    
        return $this;
    }
    
    /**
     * Get personIdStudent
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdStudent()
    {
        return $this->personStudent;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return Referrals
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
     * Set activityCategory
     *
     * @param \Synapse\CoreBundle\Entity\ActivityCategory $activityCategory
     * @return Referrals
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

    public function getPersonAssignedTo()
    {
        return $this->personAssignedTo;
    }

    public function setPersonAssignedTo($personAssignedTo)
    {
        $this->personAssignedTo = $personAssignedTo;
        return $this;
    }
           
    /**
     * @param boolean $isReasonRouted
     */
    public function setIsReasonRouted($isReasonRouted)
    {
        $this->isReasonRouted = $isReasonRouted;
    }
    
    /**
     * @return boolean
     */
    public function getIsReasonRouted()
    {
        return $this->isReasonRouted;
    }
    

    /**
     * Set userKey
     *
     * @param string $userKey
     * @return Referrals
     */
    public function setUserKey($userKey)
    {
    	$this->userKey = $userKey;
    
    	return $this;
    }
    
    /**
     * Get userKey
     *
     * @return string
     */
    public function getUserKey()
    {
    	return $this->userKey;
    }

}