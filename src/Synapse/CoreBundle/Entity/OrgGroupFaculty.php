<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * OrgGroupFaculty
 *
 * @ORM\Table(name="org_group_faculty", indexes={@ORM\Index(name="org_group_faculty_group_id", columns={"org_group_id"}), @ORM\Index(name="org_group_faculty_organization_id", columns={"organization_id"}), @ORM\Index(name="org_group_faculty_org_permissionset_id", columns={"org_permissionset_id"}), @ORM\Index(name="org_group_faculty_person_id", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgGroupFacultyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"orgGroup", "person"}, errorPath="person", message="Faculty already exists in group.", groups={"required"}))
 */
class OrgGroupFaculty extends BaseEntity
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_invisible", type="boolean", length=1, nullable=true)
     * @Assert\NotBlank()
     */
    private $isInvisible;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgPermissionset
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $orgPermissionset;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgGroup
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_group_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $orgGroup;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $person;



    /**
     * Set isInvisible
     *
     * @param boolean $isInvisible
     * @return OrgGroupFaculty
     */
    public function setIsInvisible($isInvisible)
    {
        $this->isInvisible = $isInvisible;

        return $this;
    }

    /**
     * Get isInvisible
     *
     * @return boolean
     */
    public function getIsInvisible()
    {
        return $this->isInvisible;
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
     * Set orgPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset
     * @return OrgGroupFaculty
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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgGroupFaculty
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
     * Set orgGroup
     *
     * @param \Synapse\CoreBundle\Entity\OrgGroup $orgGroup
     * @return OrgGroupFaculty
     */
    public function setOrgGroup(\Synapse\CoreBundle\Entity\OrgGroup $orgGroup = null)
    {
        $this->orgGroup = $orgGroup;

        return $this;
    }

    /**
     * Get orgGroup
     *
     * @return \Synapse\CoreBundle\Entity\OrgGroup 
     */
    public function getOrgGroup()
    {
        return $this->orgGroup;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return OrgGroupFaculty
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }
}
