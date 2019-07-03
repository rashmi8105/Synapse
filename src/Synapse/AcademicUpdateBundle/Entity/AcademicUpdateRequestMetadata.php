<?php

namespace Synapse\AcademicUpdateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcademicUpdateRequestMetadata
 *
 * @ORM\Table(name="academic_update_request_metadata", indexes={@ORM\Index(name="fk_academic_update_request_metadata_ebi_metadata1_idx", columns={"ebi_metadata_id"}), @ORM\Index(name="fk_academic_update_request_metadata_org_metadata1_idx", columns={"org_metadata_id"}), @ORM\Index(name="fk_academic_update_request_metadata_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_academic_update_request_metadata_academic_update_request_idx", columns={"academic_update_request_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestMetadataRepository")
 */
class AcademicUpdateRequestMetadata
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="search_value", type="string", length=2000, nullable=true)
     */
    private $searchValue;

    /**
     * @var \EbiMetadata
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiMetadata")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ebi_metadata_id", referencedColumnName="id")
     * })
     */
    private $ebiMetadata;

    /**
     * @var \OrgMetadata
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgMetadata")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_metadata_id", referencedColumnName="id")
     * })
     */
    private $orgMetadata;

    /**
     * @var \Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     * })
     */
    private $org;

    /**
     * @var \AcademicUpdateRequest
     *
     * @ORM\ManyToOne(targetEntity="Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="academic_update_request_id", referencedColumnName="id")
     * })
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
     * Set searchValue
     *
     * @param string $searchValue
     * @return AcademicUpdateRequestMetadata
     */
    public function setSearchValue($searchValue)
    {
        $this->searchValue = $searchValue;

        return $this;
    }

    /**
     * Get searchValue
     *
     * @return string 
     */
    public function getSearchValue()
    {
        return $this->searchValue;
    }

    /**
     * Set ebiMetadata
     *
     * @param \Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata
     * @return AcademicUpdateRequestMetadata
     */
    public function setEbiMetadata(\Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata = null)
    {
        $this->ebiMetadata = $ebiMetadata;

        return $this;
    }

    /**
     * Get ebiMetadata
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\EbiMetadata 
     */
    public function getEbiMetadata()
    {
        return $this->ebiMetadata;
    }

    /**
     * Set orgMetadata
     *
     * @param \Synapse\AcademicUpdateBundle\Entity\OrgMetadata $orgMetadata
     * @return AcademicUpdateRequestMetadata
     */
    public function setOrgMetadata(\Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata = null)
    {
        $this->orgMetadata = $orgMetadata;

        return $this;
    }

    /**
     * Get orgMetadata
     *
     * @return \Synapse\AcademicUpdateBundle\Entity\OrgMetadata 
     */
    public function getOrgMetadata()
    {
        return $this->orgMetadata;
    }

    /**
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org
     * @return AcademicUpdateRequestMetadata
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org = null)
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
     * @return AcademicUpdateRequestMetadata
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
}
