<?php
namespace Synapse\StaticListBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgStaticList
 *
 * @ORM\Table(name="org_static_list", indexes={@ORM\Index(name="fk_org_staticlist_organization1_idx", columns={"organization_id"}),@ORM\Index(name="fk_staticlist_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\StaticListBundle\Repository\OrgStaticListRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgStaticList extends BaseEntity
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
     * @var string @ORM\Column(name="name", type="string", length=300, nullable=false)
     */
    private $name;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=2000, nullable=true)
     */
    private $description;

    /**
     *
     * @var integer @ORM\Column(name="person_id_shared_by", type="integer", length=150, nullable=true)
     */
    private $personIdSharedBy;

    /**
     *
     * @var date @ORM\Column(name="shared_on", type="date", nullable=true)
     *      @JMS\Expose
     */
    private $sharedOn;

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
     * @return OrgStaticList
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
     * @return OrgStaticList
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
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param int personIdSharedBy
     */
    public function setPersonIdSharedBy($personIdSharedBy)
    {
        $this->personIdSharedBy = $personIdSharedBy;
    }

    /**
     *
     * @return int
     */
    public function getPersonIdSharedBy()
    {
        return $this->personIdSharedBy;
    }

    /**
     *
     * @param date $sharedOn            
     */
    public function setSharedOn($sharedOn)
    {
        $this->sharedOn = $sharedOn;
    }

    /**
     *
     * @return date
     */
    public function getSharedOn()
    {
        return $this->sharedOn;
    }
}   