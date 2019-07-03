<?php

namespace Synapse\MapworksToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\MapworksToolBundle\Entity\MapworksTool;

/**
 * OrgPermissionsetTool
 *
 * @ORM\Table(name="org_permissionset_tool", indexes={@ORM\Index(name="fk_permissiontool_permissionsetid", columns={"org_permissionset_id"}), @ORM\Index(name="fk_permissiontool_toolid", columns={"tool_id"}), @ORM\Index(name="fk_permissiontool_organizationid", columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\MapworksToolBundle\Repository\OrgPermissionsetToolRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPermissionsetTool extends BaseEntity
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
     * @var MapworksTool
     *
     * @ORM\ManyToOne(targetEntity="MapworksTool")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="mapworks_tool_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $mapworksToolId;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $organization;

    /**
     * @var OrgPermissionset
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgPermissionset;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param MapworksTool $mapworksToolId
     */
    public function setMapworksToolId($mapworksToolId)
    {
        $this->mapworksToolId = $mapworksToolId;
    }

    /**
     * @return MapworksTool
     */
    public function getMapworksToolId()
    {
        $this->mapworksToolId;
    }

    /**
     * @param OrgPermissionset $orgPermissionset
     */
    public function setOrgPermissionset($orgPermissionset)
    {
        $this->orgPermissionset = $orgPermissionset;
    }

    /**
     * @return OrgPermissionset
     */
    public function getOrgPermissionset()
    {
        return $this->orgPermissionset;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

}
