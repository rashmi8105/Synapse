<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgPermissionsetMetadata
 *
 * @ORM\Table(name="org_permissionset_metadata", indexes={@ORM\Index(name="fk_org_permissionset_metadata_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_org_permissionset_metadata_org_permissionset1_idx", columns={"org_permissionset_id"}), @ORM\Index(name="fk_org_permissionset_metadata_org_metadata1_idx", columns={"org_metadata_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPermissionsetMetadata extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgPermissionset
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgPermissionset;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgMetadata
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgMetadata")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_metadata_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgMetadata;



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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgPermissionsetMetadata
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
     * Set orgPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset
     * @return OrgPermissionsetMetadata
     */
    public function setOrgPermissionset(\Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset = null)
    {
        $this->orgPermissionset = $orgPermissionset;

        return $this;
    }

    /**
     * Get orgPermissionset
     *
     * @return \Synapse\CoreBundle\Entity\OrgPermissionset 
     */
    public function getOrgPermissionset()
    {
        return $this->orgPermissionset;
    }

    /**
     * Set orgMetadata
     *
     * @param \Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata
     * @return OrgPermissionsetMetadata
     */
    public function setOrgMetadata(\Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata = null)
    {
        $this->orgMetadata = $orgMetadata;

        return $this;
    }

    /**
     * Get orgMetadata
     *
     * @return \Synapse\CoreBundle\Entity\OrgMetadata 
     */
    public function getOrgMetadata()
    {
        return $this->orgMetadata;
    }
}
