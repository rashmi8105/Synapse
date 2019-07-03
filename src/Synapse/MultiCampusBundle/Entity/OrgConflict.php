<?php
namespace Synapse\MultiCampusBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgConflict
 *
 * @ORM\Table(name="org_conflict", indexes={@ORM\Index(name="fk_table1_org_person_faculty1_idx", columns={"faculty_id"}), @ORM\Index(name="fk_table1_org_person_student1_idx", columns={"student_id"}),@ORM\Index(name="fk_table1_organization1_idx", columns={"src_org_id"}),@ORM\Index(name="fk_table1_organization2_idx", columns={"dst_org_id"})})
 * @ORM\Entity(repositoryClass="Synapse\MultiCampusBundle\Repository\OrgConflictRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgConflict extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="src_org_id", referencedColumnName="id")
     *      })
     */
    private $srcOrgId;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="dst_org_id", referencedColumnName="id")
     *      })
     */
    private $dstOrgId;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="faculty_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $facultyId;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $studentId;

    /**
     *
     * @var string @ORM\Column(name="record_type", type="string", columnDefinition="enum('master', 'home','other')")
     */
    private $recordType;

    /**
     *
     * @var string @ORM\Column(name="owning_org_tier_code", type="integer", columnDefinition="enum('0','3')")
     */
    private $owningOrgTierCode;

    /**
     *
     * @var string @ORM\Column(name="merge_type", type="string", columnDefinition="enum('O','N','S','H', 'M')")
     */
    private $mergeType;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", columnDefinition="enum('conflict', 'merged')")
     */
    private $status;

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set orgSource
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return OrganizationSource
     */
    public function setSrcOrgId(\Synapse\CoreBundle\Entity\Organization $srcOrgId = null)
    {
        $this->srcOrgId = $srcOrgId;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getSrcOrgId()
    {
        return $this->srcOrgId;
    }

    /**
     * Set dstOrgId
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return OrganizationDestination
     */
    public function setDstOrgId(\Synapse\CoreBundle\Entity\Organization $dstOrgId = null)
    {
        $this->dstOrgId = $dstOrgId;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getDstOrgId()
    {
        return $this->dstOrgId;
    }

    /**
     * Set personRequestedBy
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return facultyId
     */
    public function setFacultyId(\Synapse\CoreBundle\Entity\Person $facultyId = null)
    {
        $this->facultyId = $facultyId;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getFacultyId()
    {
        return $this->facultyId;
    }

    /**
     * Set studentId
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return studentId
     */
    public function setStudentId(\Synapse\CoreBundle\Entity\Person $studentId = null)
    {
        $this->studentId = $studentId;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Set personStudent
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return PersonStudent
     */
    public function setPersonStudent(\Synapse\CoreBundle\Entity\Person $personStudent = null)
    {
        $this->personStudent = $personStudent;
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonStudent()
    {
        return $this->personStudent;
    }

    /**
     *
     * @param string $recordType            
     */
    public function setRecordType($recordType)
    {
        $this->recordType = $recordType;
    }

    /**
     *
     * @return string
     */
    public function getRecordType()
    {
        return $this->recordType;
    }

    /**
     *
     * @param string $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param integer $owningOrgTierCode            
     */
    public function setOwningOrgTierCode($owningOrgTierCode)
    {
        $this->owningOrgTierCode = $owningOrgTierCode;
    }

    /**
     *
     * @return integer
     */
    public function getOwningOrgTierCode()
    {
        return $this->owningOrgTierCode;
    }

    /**
     *
     * @param string $mergeType            
     */
    public function setMergeType($mergeType)
    {
        $this->mergeType = $mergeType;
    }

    /**
     *
     * @return string
     */
    public function getMergeType()
    {
        return $this->mergeType;
    }
}