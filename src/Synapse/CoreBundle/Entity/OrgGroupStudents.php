<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrgGroupStudents
 *
 * @ORM\Table(name="org_group_students", indexes={@ORM\Index(name="org_group_students_orgid", columns={"organization_id"}), @ORM\Index(name="org_group_students_org_group_id", columns={"org_group_id"}), @ORM\Index(name="org_group_students_person_id", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgGroupStudentsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"orgGroup", "person"}, groups={"required"}, message="Student is already a member of the group")
 */
class OrgGroupStudents extends BaseEntity
{
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
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $person;

    /**
     * @var OrgGroup
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_group_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $orgGroup;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $organization;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return OrgGroup
     */
    public function getOrgGroup()
    {
        return $this->orgGroup;
    }

    /**
     * @param OrgGroup $orgGroup
     */
    public function setOrgGroup($orgGroup)
    {
        $this->orgGroup = $orgGroup;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }


}
