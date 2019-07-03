<?php
namespace Synapse\SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\Person;

/**
 * OrgSearchSharedBy
 *
 * @ORM\Table(name="org_search_shared_by", indexes={@ORM\Index(name="fk_org_search_shared_by_org_search1_idx", columns={"org_search_id"}), @ORM\Index(name="fk_org_search_shared_by_org_search2_idx", columns={"org_search_id_source"}), @ORM\Index(name="fk_org_search_shared_by_person1_idx", columns={"person_id_shared_by"})})
 * @ORM\Entity(repositoryClass="Synapse\SearchBundle\Repository\OrgSearchSharedByRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgSearchSharedBy extends BaseEntity
{

    /**
     *
     * @var \DateTime @ORM\Column(name="shared_on", type="datetime", nullable=true)
     */
    private $sharedOn;

    /**
     *
     * @var OrgSearch
     * @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\SearchBundle\Entity\OrgSearch")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_search_id", referencedColumnName="id")
     *      })
     */
    private $orgSearch;

    /**
     *
     * @var OrgSearch
     * @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\SearchBundle\Entity\OrgSearch")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_search_id_source", referencedColumnName="id")
     *      })
     */
    private $orgSearchSource;

    /**
     *
     * @var Person
     * @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_shared_by", referencedColumnName="id")
     *      })
     */
    private $personSharedBy;

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
     * @param \Synapse\SearchBundle\Entity\OrgSearch $orgSearchSource            
     * @return OrgSearchSharedBy
     */
    public function setOrgSearchSource(\Synapse\SearchBundle\Entity\OrgSearch $orgSearchSource)
    {
        $this->orgSearchSource = $orgSearchSource;
        
        return $this;
    }

    /**
     * Get orgSearchSource
     *
     * @return \Synapse\SearchBundle\Entity\OrgSearch
     */
    public function getOrgSearchSource()
    {
        return $this->orgSearchSource;
    }

    /**
     * Set personSharedBy
     *
     * @param \Synapse\CoreBundle\Entity\Person $personSharedBy            
     * @return OrgSearchSharedBy
     */
    public function setPersonSharedBy(\Synapse\CoreBundle\Entity\Person $personSharedBy)
    {
        $this->personSharedBy = $personSharedBy;
        
        return $this;
    }

    /**
     * Get personSharedBy
     *
     * @return Person
     */
    public function getPersonSharedBy()
    {
        return $this->personSharedBy;
    }

    public function getId()
    {
        return $this;
    }
}
