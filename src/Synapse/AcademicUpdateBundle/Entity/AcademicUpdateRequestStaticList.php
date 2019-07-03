<?php

namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * AcademicUpdateRequestStaticList
 *
 * @ORM\Table(name="academic_update_request_static_list", indexes={@ORM\Index(name="fk_academic_update_request_static_list_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_academic_update_request_static_list_academic_update_requ_idx", columns={"academic_update_request_id"}), @ORM\Index(name="fk_academic_update_request_static_list_org_static_list1_idx", columns={"org_static_list_id"})})
 * @ORM\Entity
 */
class AcademicUpdateRequestStaticList extends BaseEntity
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
     * @var \Synapse\RiskBundle\Entity\OrgStaticList
     *
     * @ORM\ManyToOne(targetEntity="Synapse\StaticListBundle\Entity\OrgStaticList")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_static_list_id", referencedColumnName="id")
     * })
     */
    private $orgStaticList;

    /**
     * @var \Synapse\RiskBundle\Entity\AcademicUpdateRequest
     *
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="academic_update_request_id", referencedColumnName="id")
     * })
     */
    private $academicUpdateRequest;

    /**
     * @var \Synapse\RiskBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;



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
     * Set orgStaticList
     *
     * @param \Synapse\StaticListBundle\Entity\OrgStaticList $orgStaticList
     * @return AcademicUpdateRequestStaticList
     */
    public function setOrgStaticList(\Synapse\StaticListBundle\Entity\OrgStaticList $orgStaticList = null)
    {
        $this->orgStaticList = $orgStaticList;

        return $this;
    }

    /**
     * Get orgStaticList
     *
     * @return \Synapse\RiskBundle\Entity\OrgStaticList 
     */
    public function getOrgStaticList()
    {
        return $this->orgStaticList;
    }

    /**
     * Set academicUpdateRequest
     *
     * @param \Synapse\RiskBundle\Entity\AcademicUpdateRequest $academicUpdateRequest
     * @return AcademicUpdateRequestStaticList
     */
    public function setAcademicUpdateRequest(\Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest $academicUpdateRequest = null)
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

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return AcademicUpdateRequestStaticList
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\RiskBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
