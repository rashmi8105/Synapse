<?php
namespace Synapse\SearchBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrgSearch
 *
 * @ORM\Table(name="org_search", indexes={@ORM\Index(name="fk_org_search_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_org_search_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SearchBundle\Repository\OrgSearchRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"name","person","organization"},message="You already have a Saved Search with this name. Please use a different one.", ignoreNull=false)
 */
class OrgSearch extends BaseEntity
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
     * @var string @ORM\Column(name="name", type="string", length=120, nullable=true)
     * @var string @Assert\Length(max="120", maxMessage = "Search Name cannot be longer than {{ limit }} characters")
     */
    private $name;

    /**
     * 
     * @var string @ORM\Column(name="query", type="string", length=5000, nullable=true)
     */
    private $query;

    /**
     *
     * @var string @ORM\Column(name="json", type="string", length=3000, nullable=true)
     */
    private $json;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $person;

    /**
     *
     * @var \Date @ORM\Column(name="shared_on", type="datetime", nullable=true)
     */
    private $sharedOn;

    /**
     *
     * @var string @ORM\Column(name="edited_by_me", type="integer", length=1, nullable=true)
     */
    private $editedByMe;

    /**
     *
     * @var string @ORM\Column(name="from_sharedtab", type="integer", length=1, nullable=true)
     */
    private $fromSharedtab;

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
     * @return OrgSearch
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
     * Set name
     *
     * @param string $name            
     * @return OrgSearch
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * Set query
     *
     * @param string $query            
     * @return OrgSearch
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set json
     *
     * @param string $json            
     * @return OrgSearch
     */
    public function setJson($json)
    {
        $this->json = $json;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return OrgSearch
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

    /**
     *
     * @param \DateTime $sharedOn            
     */
    public function setSharedOn($sharedOn)
    {
        $this->sharedOn = $sharedOn;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getSharedOn()
    {
        return $this->sharedOn;
    }

    /**
     * Set editedByMe
     *
     * @param integer $editedByMe           
     * @return OrgSearch
     */
    public function setEditedByMe($editedByMe)
    {
        $this->editedByMe = $editedByMe;
        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getEditedByMe()
    {
        return $this->editedByMe;
    }

    /**
     * Set fromSharedtab
     *
     * @param integer $fromSharedtab            
     * @return OrgSearch
     */
    public function setFromSharedtab($fromSharedtab)
    {
        $this->fromSharedtab = $fromSharedtab;
        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getFromSharedtab()
    {
        return $this->fromSharedtab;
    }
}