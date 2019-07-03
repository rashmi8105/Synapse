<?php
namespace Synapse\AcademicUpdateBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;

/**
 * AcademicUpdateRequest
 *
 * @ORM\Table(name="academic_update_request", indexes={@ORM\Index(name="fk_academic_update_request_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_academic_update_request_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"name", "org"},message="Academic Update Request Name already exists.")
 */
class AcademicUpdateRequest extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="update_type", type="string", nullable=true)
     */
    private $updateType;

    /**
     *
     * @var \DateTime @ORM\Column(name="request_date", type="datetime", nullable=true)
     */
    private $requestDate;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=4000, nullable=true)
     */
    private $description;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", nullable=true, columnDefinition="enum('open','closed','cancelled')")
     */
    private $status;

    /**
     *
     * @var \DateTime @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     *
     * @var \DateTime @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     *
     * @var \DateTime @ORM\Column(name="due_date", type="datetime", nullable=true)
     */
    private $dueDate;

    /**
     *
     * @var string @ORM\Column(name="subject", type="string", length=400, nullable=true)
     */
    private $subject;

    /**
     *
     * @var string @ORM\Column(name="email_optional_msg", type="string", length=65536, nullable=true)
     */
    private $emailOptionalMsg;

    /**
     *
     * @var Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     */
    private $org;

    /**
     *
     * @var Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;
    
    
    /**
     *
     * @var string @ORM\Column(name="select_course", type="string", nullable=true, columnDefinition="enum('all,','individual','none')")
     */
    private $selectCourse;
    
    /**
     *
     * @var string @ORM\Column(name="select_faculty", type="string", nullable=true, columnDefinition="enum('all,','individual','none')")
     */
    private $selectFaculty;
    
    /**
     *
     * @var string @ORM\Column(name="select_student", type="string", nullable=true, columnDefinition="enum('all,','individual','none')")
     */
    private $selectStudent;
    
    /**
     *
     * @var string @ORM\Column(name="select_group", type="string", nullable=true, columnDefinition="enum('all,','individual','none')")
     */
    private $selectGroup;
    
    
    /**
     *
     * @var string @ORM\Column(name="select_metadata", type="string", nullable=true, columnDefinition="enum('all,','individual','none')")
     */
    private $selectMetadata;
    
    /**
     *
     * @var string @ORM\Column(name="select_static_list", type="string", nullable=true, columnDefinition="enum('all,','individual','none')")
     */
    private $selectStaticList;

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
     * Set updateType
     *
     * @param string $updateType            
     * @return AcademicUpdateRequest
     */
    public function setUpdateType($updateType)
    {
        $this->updateType = $updateType;
        
        return $this;
    }

    /**
     * Get updateType
     *
     * @return string
     */
    public function getUpdateType()
    {
        return $this->updateType;
    }

    /**
     * Set requestDate
     *
     * @param \DateTime $requestDate            
     * @return AcademicUpdateRequest
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;
        
        return $this;
    }

    /**
     * Get requestDate
     *
     * @return \DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set name
     *
     * @param string $name            
     * @return AcademicUpdateRequest
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description            
     * @return AcademicUpdateRequest
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
     * Set status
     *
     * @param string $status            
     * @return AcademicUpdateRequest
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
     * Set startDate
     *
     * @param \DateTime $startDate            
     * @return AcademicUpdateRequest
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate            
     * @return AcademicUpdateRequest
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate            
     * @return AcademicUpdateRequest
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
        
        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set subject
     *
     * @param string $subject            
     * @return AcademicUpdateRequest
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        
        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set emailOptionalMsg
     *
     * @param string $emailOptionalMsg            
     * @return AcademicUpdateRequest
     */
    public function setEmailOptionalMsg($emailOptionalMsg)
    {
        $this->emailOptionalMsg = $emailOptionalMsg;
        
        return $this;
    }

    /**
     * Get emailOptionalMsg
     *
     * @return string
     */
    public function getEmailOptionalMsg()
    {
        return $this->emailOptionalMsg;
    }

    /**
     * Set org
     *
     * @param Organization $org
     * @return AcademicUpdateRequest
     */
    public function setOrg($org = null)
    {
        $this->org = $org;
        
        return $this;
    }

    /**
     * Get org
     *
     * @return Organization
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * Set person
     *
     * @param Person $person
     * @return AcademicUpdateRequest
     */
    public function setPerson($person = null)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     * Get person
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param string $selectCourse
     */
    public function setSelectCourse($selectCourse)
    {
        $this->selectCourse = $selectCourse;
    }

    /**
     * @return string
     */
    public function getSelectCourse()
    {
        return $this->selectCourse;
    }

    /**
     * @param string $selectStudent
     */
    public function setSelectStudent($selectStudent)
    {
        $this->selectStudent = $selectStudent;
    }

    /**
     * @return string
     */
    public function getSelectStudent()
    {
        return $this->selectStudent;
    }

    /**
     * @param string $selectMetadata
     */
    public function setSelectMetadata($selectMetadata)
    {
        $this->selectMetadata = $selectMetadata;
    }

    /**
     * @return string
     */
    public function getSelectMetadata()
    {
        return $this->selectMetadata;
    }

    /**
     * @param string $selectGroup
     */
    public function setSelectGroup($selectGroup)
    {
        $this->selectGroup = $selectGroup;
    }

    /**
     * @return string
     */
    public function getSelectGroup()
    {
        return $this->selectGroup;
    }

    /**
     * @param string $selectFaculty
     */
    public function setSelectFaculty($selectFaculty)
    {
        $this->selectFaculty = $selectFaculty;
    }

    /**
     * @return string
     */
    public function getSelectFaculty()
    {
        return $this->selectFaculty;
    }

    /**
     * @param string $selectStaticList
     */
    public function setSelectStaticList($selectStaticList)
    {
        $this->selectStaticList = $selectStaticList;
    }

    /**
     * @return string
     */
    public function getSelectStaticList()
    {
        return $this->selectStaticList;
    }




}
