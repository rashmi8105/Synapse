<?php
namespace Synapse\SearchBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrgSearchShared
 *
 * @ORM\Table(name="org_search_shared", uniqueConstraints={@ORM\UniqueConstraint(name="fk_org_search_shared_unique", columns={"org_search_id_source", "person_id_sharedby", "person_id_sharedwith", "org_search_id_dest"})}, indexes={@ORM\Index(name="fk_org_search_shared_org_search1_idx", columns={"org_search_id_source"}), @ORM\Index(name="fk_org_search_shared_org_search2_idx", columns={"org_search_id_dest"}), @ORM\Index(name="fk_org_search_shared_person1_idx", columns={"person_id_sharedby"}), @ORM\Index(name="fk_org_search_shared_person2_idx", columns={"person_id_sharedwith"})})
 * @ORM\Entity(repositoryClass="Synapse\SearchBundle\Repository\OrgSearchSharedRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgSearchShared extends BaseEntity
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
     * @var \Synapse\SearchBundle\Entity\OrgSearch @ORM\ManyToOne(targetEntity="Synapse\SearchBundle\Entity\OrgSearch")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_search_id_source", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgSearchIdSource;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_sharedby", referencedColumnName="id", nullable=true)
     *      })
     */
    private $personIdSharedby;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_sharedwith", referencedColumnName="id", nullable=true)
     *      })
     */
    private $personIdSharedwith;

    /**
     *
     * @var \Synapse\SearchBundle\Entity\OrgSearch @ORM\ManyToOne(targetEntity="Synapse\SearchBundle\Entity\OrgSearch")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_search_id_dest", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgSearchIdDest;

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
     * Set orgSearchIdSource
     *
     * @param \Synapse\SearcBundle\Entity\OrgSearch $orgSearchIdSources            
     * @return OrgSearchShared
     */
    public function setOrgSearchIdSource(\Synapse\SearchBundle\Entity\OrgSearch $orgSearchIdSource = null)
    {
        $this->orgSearchIdSource = $orgSearchIdSource;
        
        return $this;
    }

    /**
     * Get orgSearchIdSource
     *
     * @return \Synapse\SearcBundle\Entity\OrgSearch
     */
    public function getOrgSearchIdSource()
    {
        return $this->orgSearchIdSource;
    }

    /**
     * Set orgSearchIdDest
     *
     * @param \Synapse\SearcBundle\Entity\OrgSearch $orgSearchIdDest            
     * @return OrgSearchShared
     */
    public function setOrgSearchIdDest(\Synapse\SearchBundle\Entity\OrgSearch $orgSearchIdDest = null)
    {
        $this->orgSearchIdDest = $orgSearchIdDest;
        
        return $this;
    }

    /**
     * Get orgSearchIdDest
     *
     * @return \Synapse\SearcBundle\Entity\OrgSearch
     */
    public function getOrgSearchIdDest()
    {
        return $this->orgSearchIdDest;
    }

    /**
     * Set personIdSharedby
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdSharedby            
     * @return OrgSearchShared
     */
    public function setPersonIdSharedby(\Synapse\CoreBundle\Entity\Person $personIdSharedby = null)
    {
        $this->personIdSharedby = $personIdSharedby;
        
        return $this;
    }

    /**
     * Get personIdSharedby
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdSharedby()
    {
        return $this->personIdSharedby;
    }

    /**
     * Set personIdSharedwith
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdSharedwith            
     * @return OrgSearchShared
     */
    public function setPersonIdSharedwith(\Synapse\CoreBundle\Entity\Person $personIdSharedwith = null)
    {
        $this->personIdSharedwith = $personIdSharedwith;
        
        return $this;
    }

    /**
     * Get personIdSharedwith
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdSharedwith()
    {
        return $this->personIdSharedwith;
    }
}