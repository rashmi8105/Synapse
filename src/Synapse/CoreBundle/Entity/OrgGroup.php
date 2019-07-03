<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * OrgGroup
 *
 * @ORM\Table(name="org_group", indexes={@ORM\Index(name="org_group_orgid", columns={"organization_id"}), @ORM\Index(name="org_group_groupid1", columns={"parent_group_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgGroupRepository")
 * @ORM\EntityListeners({ "Synapse\CoreBundle\Listener\OrgGroupListener" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 * @UniqueEntity(fields={"externalId", "organization"},message="Group Id already exists.")
 * @UniqueEntity(fields={"groupName", "organization"},message="Group Name already exists.")
 *
 */
class OrgGroup extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=100, nullable=true)
     * @JMS\Expose
     * @Assert\NotBlank(message = "Group Name can not be empty")
     * @Assert\Length(max=100,maxMessage = "Group Name can not exceed {{ limit }} characters ")
     * 
     */
    private $groupName;

    /**
     * @var string
     *
     * @ORM\Column(name="external_id", type="string", length=100, nullable=true)
     * @JMS\Expose
     * @Assert\NotBlank(message = "ID can not be empty")
     * @Assert\Length(max=100,maxMessage = "ID can not exceed {{ limit }} characters ");
     */
    private $externalId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @var \Synapse\CoreBundle\Entity\OrgGroup
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_group_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $parentGroup;


    
    

    /**
     * Set groupName
     *
     * @param string $groupName
     * @return OrgGroup
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set externalId
     *
     * @param string $externalId
     * @return OrgGroup
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * Get externalId
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgGroup
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
     * Set parentGroup
     *
     * @param \Synapse\CoreBundle\Entity\OrgGroup $parentGroup
     * @return OrgGroup
     */
    public function setParentGroup(\Synapse\CoreBundle\Entity\OrgGroup $parentGroup = null)
    {
        $this->parentGroup = $parentGroup;

        return $this;
    }

    /**
     * Get parentGroup
     *
     * @return \Synapse\CoreBundle\Entity\OrgGroup
     */
    public function getParentGroup()
    {
        return $this->parentGroup;
    }
    
    
}
