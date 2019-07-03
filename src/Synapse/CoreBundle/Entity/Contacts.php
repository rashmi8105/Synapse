<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;

/**
 * Contacts 
 * @ORM\Table(name="contacts", indexes={@ORM\Index(name="fk_contacts_contact_types1_idx",columns={"contact_types_id"}),@ORM\Index(name="fk_contacts_organization1_idx",columns={"organization_id"}),@ORM\Index(name="fk_contacts_person1_idx",columns={"person_id_student"}),@ORM\Index(name="fk_contacts_person2_idx",columns={"person_id_faculty"}),@ORM\Index(name="fk_contacts_activity_category1_idx",columns={"activity_category_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ContactsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Contacts extends BaseEntity implements OwnableAssetEntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * 
     * @JMS\Expose
     */
    private $id;

   /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;
    
     /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $personIdStudent;
    
    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id_faculty", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $personIdFaculty;
    /**
     * @var \Synapse\CoreBundle\Entity\ContactTypes
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ContactTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_types_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $contactTypesId;
    /**
     * @var \Synapse\CoreBundle\Entity\ActivityCategory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_category_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $activityCategory;
    /**
     * @var datetime
     *
     * @ORM\Column(name="contact_date", type="datetime", nullable=false)
     * @JMS\Expose
     */
    private $contactDate;
    
    /**
     * @var text
     *
     * @ORM\Column(name="note", type="text", nullable=false)
     * @JMS\Expose
     */
    private $note;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_discussed", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $isDiscussed;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_high_priority", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $isHighPriority;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reveal", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $isReveal;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_leaving", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $isLeaving;
     
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param \Synapse\CoreBundle\Entity\ContactTypes $contactTypesId
     */
    public function setContactTypesId($contactTypesId)
    {
        $this->contactTypesId = $contactTypesId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\ContactTypes
     */
    public function getContactTypesId()
    {
        return $this->contactTypesId;
    }

    /**
     * @param boolean $isReveal
     */
    public function setIsReveal($isReveal)
    {
        $this->isReveal = $isReveal;
    }

    /**
     * @return boolean
     */
    public function getIsReveal()
    {
        return $this->isReveal;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Person $personIdFaculty
     */
    public function setPersonIdFaculty(Person $personIdFaculty = null)
    {
        $this->personIdFaculty = $personIdFaculty;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdFaculty()
    {
        return $this->personIdFaculty;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Person $personIdStudent
     */
    public function setPersonIdStudent(Person $personIdStudent = null)
    {
        $this->personIdStudent = $personIdStudent;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdStudent()
    {
        return $this->personIdStudent;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\ActivityCategory $activityCategory
     */
    public function setActivityCategory($activityCategory)
    {
        $this->activityCategory = $activityCategory;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\ActivityCategory
     */
    public function getActivityCategory()
    {
        return $this->activityCategory;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\datetime $contactDate
     */
    public function setContactDate($contactDate)
    {
        $this->contactDate = $contactDate;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\datetime
     */
    public function getContactDate()
    {
        return $this->contactDate;
    }

    /**
     * @param boolean $isDiscussed
     */
    public function setIsDiscussed($isDiscussed)
    {
        $this->isDiscussed = $isDiscussed;
    }

    /**
     * @return boolean
     */
    public function getIsDiscussed()
    {
        return $this->isDiscussed;
    }

    /**
     * @param boolean $isHighPriority
     */
    public function setIsHighPriority($isHighPriority)
    {
        $this->isHighPriority = $isHighPriority;
    }

    /**
     * @return boolean
     */
    public function getIsHighPriority()
    {
        return $this->isHighPriority;
    }

    /**
     * @param boolean $isLeaving
     */
    public function setIsLeaving($isLeaving)
    {
        $this->isLeaving = $isLeaving;
    }

    /**
     * @return boolean
     */
    public function getIsLeaving()
    {
        return $this->isLeaving;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\text $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\text
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
