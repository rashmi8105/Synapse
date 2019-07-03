<?php
namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcademicUpdateRequestGroup
 *
 * @ORM\Table(name="academic_update_request_group", indexes={@ORM\Index(name="fk_academic_update_request_group_org_group1_idx", columns={"org_group_id"}), @ORM\Index(name="fk_academic_update_request_group_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_academic_update_request_group_academic_update_request1_idx", columns={"academic_update_request_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestGroupRepository")
 */
class AcademicUpdateRequestGroup
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
     * @var \OrgGroup @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\OrgGroup")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_group_id", referencedColumnName="id")
     *      })
     */
    private $orgGroup;

    /**
     *
     * @var \Organization @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     */
    private $org;

    /**
     *
     * @var \AcademicUpdateRequest @ORM\OneToOne(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="academic_update_request_id", referencedColumnName="id")
     *      })
     */
    private $academicUpdateRequest;

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
     * Set orgGroup
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\OrgGroup $orgGroup            
     * @return AcademicUpdateRequestGroup
     */
    public function setOrgGroup(\Synapse\CoreBundle\Entity\OrgGroup $orgGroup)
    {
        $this->orgGroup = $orgGroup;
        
        return $this;
    }

    /**
     * Get orgGroup
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\OrgGroup
     */
    public function getOrgGroup()
    {
        return $this->orgGroup;
    }

    /**
     * Set org
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\Organization $org            
     * @return AcademicUpdateRequestGroup
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org)
    {
        $this->org = $org;
        
        return $this;
    }

    /**
     * Get org
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\Organization
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * Set academicUpdateRequest
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest            
     * @return AcademicUpdateRequestGroup
     */
    public function setAcademicUpdateRequest(\Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest)
    {
        $this->academicUpdateRequest = $academicUpdateRequest;
        
        return $this;
    }

    /**
     * Get academicUpdateRequest
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest
     */
    public function getAcademicUpdateRequest()
    {
        return $this->academicUpdateRequest;
    }
}
