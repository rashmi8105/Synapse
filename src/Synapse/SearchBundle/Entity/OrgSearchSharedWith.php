<?php
namespace Synapse\SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrgSearchSharedWith
 *
 * @ORM\Table(name="org_search_shared_with", indexes={@ORM\Index(name="fk_org_search_shared_org_search1_idx", columns={"org_search_id"}), @ORM\Index(name="fk_org_search_shared_org_search2", columns={"org_search_id_dest"}), @ORM\Index(name="fk_org_search_shared_person2", columns={"person_id_sharedwith"})})
 * @ORM\Entity(repositoryClass="Synapse\SearchBundle\Repository\OrgSearchSharedWithRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgSearchSharedWith extends BaseEntity
{

    /**
     *
     * @var \DateTime @ORM\Column(name="shared_on", type="datetime", nullable=true)
     */
    private $sharedOn;

    /**
     *
     * @var \OrgSearch @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\SearchBundle\Entity\OrgSearch")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_search_id", referencedColumnName="id")
     *      })
     */
    private $orgSearch;

    /**
     *
     * @var \OrgSearch @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\SearchBundle\Entity\OrgSearch", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_search_id_dest", referencedColumnName="id")
     *      })
     */
    private $orgSearchDest;

    /**
     *
     * @var \Person @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_sharedwith", referencedColumnName="id")
     *      })
     */
    private $personSharedwith;

    /**
     * Set sharedOn
     *
     * @param \DateTime $sharedOn            
     * @return OrgSearchSharedBy
     */
    public function setSharedOn($sharedOn)
    {
        $this->sharedOn = $sharedOn;
        
        return $this;
    }

    /**
     * Get sharedOn
     *
     * @return \DateTime
     */
    public function getSharedOn()
    {
        return $this->sharedOn;
    }

    /**
     * Set orgSearch
     *
     * @param \Synapse\SearchBundle\Entity\OrgSearch $orgSearch            
     * @return OrgSearchSharedBy
     */
    public function setOrgSearch(\Synapse\SearchBundle\Entity\OrgSearch $orgSearch)
    {
        $this->orgSearch = $orgSearch;
        
        return $this;
    }

    /**
     * Get orgSearch
     *
     * @return \Synapse\SearchBundle\Entity\OrgSearch
     */
    public function getOrgSearch()
    {
        return $this->orgSearch;
    }

    /**
     * Set orgSearchSource
     *
     * @param \Synapse\SearchBundle\Entity\OrgSearch $orgSearchDest            
     * @return OrgSearchSharedwith
     */
    public function setOrgSearchDest(\Synapse\SearchBundle\Entity\OrgSearch $orgSearchDest)
    {
        $this->orgSearchDest = $orgSearchDest;
        
        return $this;
    }

    /**
     * Get orgSearchDest
     *
     * @return \Synapse\SearchBundle\Entity\OrgSearch
     */
    public function getOrgSearchDest()
    {
        return $this->orgSearchDest;
    }

    /**
     * Set personSharedwith
     *
     * @param \Synapse\CoreBundle\Entity\Person $personSharedwith            
     * @return OrgSearchSharedBy
     */
    public function setPersonSharedwith(\Synapse\CoreBundle\Entity\Person $personSharedwith)
    {
        $this->personSharedwith = $personSharedwith;
        
        return $this;
    }

    /**
     * Get personSharedwith
     *
     * @return \Synapse\SearchBundle\Entity\Person
     */
    public function getPersonSharedwith()
    {
        return $this->personSharedwith;
    }

    public function getId()
    {
        return $this;
    }
}
