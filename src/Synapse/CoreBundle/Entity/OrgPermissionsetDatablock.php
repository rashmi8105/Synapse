<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgPermissionsetDatablock
 *
 * @ORM\Table(name="org_permissionset_datablock", indexes={@ORM\Index(name="fk_orgdashboarditem_organizationid", columns={"organization_id"}), @ORM\Index(name="fk_orgdashboarditem_datablockid", columns={"datablock_id"}), @ORM\Index(name="fk_orgdashboarditems_permissionset", columns={"org_permissionset_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPermissionsetDatablock extends BaseEntity
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
     * @var string
     *
     * @ORM\Column(name="block_type", type="string", length=10, precision=0, scale=0, nullable=true, unique=false)
     */
    private $blockType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="timeframe_all", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $timeframeAll;

    /**
     * @var boolean
     *
     * @ORM\Column(name="current_calendar", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $currentCalendar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="previous_period", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $previousPeriod;

    /**
     * @var boolean
     *
     * @ORM\Column(name="next_period", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $nextPeriod;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgPermissionset
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgPermissionset;

    /**
     * @var \Synapse\CoreBundle\Entity\DatablockMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\DatablockMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="datablock_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $datablock;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $organization;



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
     * Set blockType
     *
     * @param string $blockType
     * @return OrgPermissionsetDatablock
     */
    public function setBlockType($blockType)
    {
        $this->blockType = $blockType;

        return $this;
    }

    /**
     * Get blockType
     *
     * @return string 
     */
    public function getBlockType()
    {
        return $this->blockType;
    }

    /**
     * Set timeframeAll
     *
     * @param boolean $timeframeAll
     * @return OrgPermissionsetDatablock
     */
    public function setTimeframeAll($timeframeAll)
    {
        $this->timeframeAll = $timeframeAll;

        return $this;
    }

    /**
     * Get timeframeAll
     *
     * @return boolean 
     */
    public function getTimeframeAll()
    {
        return $this->timeframeAll;
    }

    /**
     * Set currentCalendar
     *
     * @param boolean $currentCalendar
     * @return OrgPermissionsetDatablock
     */
    public function setCurrentCalendar($currentCalendar)
    {
        $this->currentCalendar = $currentCalendar;

        return $this;
    }

    /**
     * Get currentCalendar
     *
     * @return boolean 
     */
    public function getCurrentCalendar()
    {
        return $this->currentCalendar;
    }

    /**
     * Set previousPeriod
     *
     * @param boolean $previousPeriod
     * @return OrgPermissionsetDatablock
     */
    public function setPreviousPeriod($previousPeriod)
    {
        $this->previousPeriod = $previousPeriod;

        return $this;
    }

    /**
     * Get previousPeriod
     *
     * @return boolean 
     */
    public function getPreviousPeriod()
    {
        return $this->previousPeriod;
    }

    /**
     * Set nextPeriod
     *
     * @param boolean $nextPeriod
     * @return OrgPermissionsetDatablock
     */
    public function setNextPeriod($nextPeriod)
    {
        $this->nextPeriod = $nextPeriod;

        return $this;
    }

    /**
     * Get nextPeriod
     *
     * @return boolean 
     */
    public function getNextPeriod()
    {
        return $this->nextPeriod;
    }

    /**
     * Set orgPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset
     * @return OrgPermissionsetDatablock
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
     * Set datablock
     *
     * @param \Synapse\CoreBundle\Entity\DatablockMaster $datablock
     * @return OrgPermissionsetDatablock
     */
    public function setDatablock(\Synapse\CoreBundle\Entity\DatablockMaster $datablock = null)
    {
        $this->datablock = $datablock;

        return $this;
    }

    /**
     * Get datablock
     *
     * @return \Synapse\CoreBundle\Entity\DatablockMaster 
     */
    public function getDatablock()
    {
        return $this->datablock;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgPermissionsetDatablock
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
}
