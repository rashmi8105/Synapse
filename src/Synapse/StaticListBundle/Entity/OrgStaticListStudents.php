<?php
namespace Synapse\StaticListBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgStaticListStudents
 *
 * @ORM\Table(name="org_static_list_students", indexes={@ORM\Index(name="fk_org_staticlist_organization1_idx", columns={"organization_id"}),@ORM\Index(name="fk_staticlist_person1_idx", columns={"person_id"}),@ORM\Index(name="fk_staticlist_org_static_list_id1_idx", columns={"org_static_list_id"})})
 * @ORM\Entity(repositoryClass="Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgStaticListStudents extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $person;

    /**
     *
     * @var \Synapse\StaticListBundle\Entity\OrgStaticList @ORM\ManyToOne(targetEntity="Synapse\StaticListBundle\Entity\OrgStaticList")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_static_list_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $orgStaticList;

    /**
     * staticListDetails
     *
     * @var array @JMS\Type("array")
     */
    private $staticListErrors;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return OrgStaticListStudents
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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return OrgStaticListStudents
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
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

    /**
     * Set orgStaticList
     *
     * @param \Synapse\StaticListBundle\Entity\OrgStaticList $orgStaticList            
     * @return OrgStaticListStudents
     */
    public function setOrgStaticList(\Synapse\StaticListBundle\Entity\OrgStaticList $orgStaticList = null)
    {
        return $this->orgStaticList = $orgStaticList;
    }

    /**
     * Get orgStaticList
     *
     * @return \Synapse\StaticListBundle\Entity\OrgStaticList
     */
    public function getOrgStaticList()
    {
        return $this->orgStaticList;
    }

    /**
     *
     * @param arrray $staticListDetails            
     */
    public function setStaticListErrors($staticListErrors)
    {
        $this->staticListErrors = $staticListErrors;
    }

    /**
     *
     * @return array
     */
    public function getStaticListErrors()
    {
        return $this->staticListErrors;
    }
}   