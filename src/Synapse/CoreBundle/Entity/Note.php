<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;

/**
 * Note 
 * @ORM\Table(name="note", indexes={@ORM\Index(name="fk_note_organization1_idx",columns={"organization_id"}),@ORM\Index(name="fk_note_person1_idx",columns={"person_id_student"}),@ORM\Index(name="fk_note_person2_idx",columns={"person_id_faculty"}),@ORM\Index(name="fk_note_activity_category1_idx",columns={"activity_category_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\NoteRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Note extends BaseEntity implements OwnableAssetEntityInterface
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
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
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
     * @var text
     *
     * @ORM\Column(name="note", type="text", nullable=false)
     * @JMS\Expose
     */
    private $note;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="note_date", type="datetime", nullable=false)
     * @JMS\Expose
     */
    private $noteDate;
    
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
     * @param text $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return text
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param  $noteDate
     */
    public function setNoteDate($noteDate)
    {
        $this->noteDate = $noteDate;
    }

    /**
     * @return datetime
     */
    public function getNoteDate()
    {
        return $this->noteDate;
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
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
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
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
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
}
