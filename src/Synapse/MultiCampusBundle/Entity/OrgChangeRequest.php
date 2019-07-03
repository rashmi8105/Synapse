<?php
namespace Synapse\MultiCampusBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgChangeRequest
 *
 * @ORM\Table(name="org_change_request", indexes={@ORM\Index(name="fk_org_change_request_person1_idx", columns={"person_id_requested_by"}), @ORM\Index(name="fk_org_change_request_person2_idx", columns={"person_id_student"}),@ORM\Index(name="fk_org_change_request_organization1_idx", columns={"org_id_source"}),@ORM\Index(name="fk_org_change_request_organization2_idx", columns={"org_id_destination"}),@ORM\Index(name="fk_org_change_request_person3_idx", columns={"person_id_approved_by"})})
 * @ORM\Entity(repositoryClass="Synapse\MultiCampusBundle\Repository\OrgChangeRequestRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgChangeRequest extends BaseEntity
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
     *      @ORM\JoinColumn(name="org_id_source", referencedColumnName="id")
     *      })
     */
    private $orgSource;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id_destination", referencedColumnName="id")
     *      })
     */
    private $orgDestination;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_requested_by", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $personRequestedBy;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $personStudent;

    /**
     *
     * @var \DateTime @ORM\Column(name="date_submitted", type="datetime", nullable=true)
     *      @JMS\Expose
     */
    private $dateSubmitted;

    /**
     *
     * @var \DateTime @ORM\Column(name="date_approved", type="datetime", nullable=true)
     *      @JMS\Expose
     */
    private $dateApproved;

    /**
     *
     * @var string @ORM\Column(name="approval_status", type="string", columnDefinition="enum('yes', 'no')")
     */
    private $approvalStatus;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_approved_by", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $personApprovedBy;

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
    public function setOrgSource(\Synapse\CoreBundle\Entity\Organization $orgSource = null)
    {
        $this->orgSource = $orgSource;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrgSource()
    {
        return $this->orgSource;
    }

    /**
     * Set orgDestination
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return OrganizationDestination
     */
    public function setOrgDestination(\Synapse\CoreBundle\Entity\Organization $orgDestination = null)
    {
        $this->orgDestination = $orgDestination;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrgDestination()
    {
        return $this->orgDestination;
    }

    /**
     * Set personRequestedBy
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return PersonRequestedBy
     */
    public function setPersonRequestedBy(\Synapse\CoreBundle\Entity\Person $personRequestedBy = null)
    {
        $this->personRequestedBy = $personRequestedBy;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonRequestedBy()
    {
        return $this->personRequestedBy;
    }

    /**
     * Set personApprovedBy
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return PersonApprovedBy
     */
    public function setPersonApprovedBy(\Synapse\CoreBundle\Entity\Person $personApprovedBy = null)
    {
        $this->personApprovedBy = $personApprovedBy;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonApprovedBy()
    {
        return $this->personApprovedBy;
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
     * @param \Date $dateSubmitted            
     */
    public function setDateSubmitted($dateSubmitted)
    {
        $this->dateSubmitted = $dateSubmitted;
    }

    /**
     *
     * @return \Date
     */
    public function getDateSubmitted()
    {
        return $this->dateSubmitted;
    }

    /**
     *
     * @param \Date $dateApproved            
     */
    public function setDateApproved($dateApproved)
    {
        $this->dateApproved = $dateApproved;
    }

    /**
     *
     * @return \Date
     */
    public function getDateApproved()
    {
        return $this->dateApproved;
    }

    /**
     *
     * @param string $approvalStatus            
     */
    public function setApprovalStatus($approvalStatus)
    {
        $this->approvalStatus = $approvalStatus;
    }

    /**
     *
     * @return string
     */
    public function getApprovalStatus()
    {
        return $this->approvalStatus;
    }
}