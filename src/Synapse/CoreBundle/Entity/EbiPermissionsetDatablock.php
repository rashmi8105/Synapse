<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EbiPermissionsetDatablock
 *
 * @ORM\Table(name="ebi_permissionset_datablock", indexes={@ORM\Index(name="fk_ebi_permissionset_datablock_datablock_master1_idx", columns={"datablock_id"}), @ORM\Index(name="fk_ebi_permissionset_datablock_ebi_permissionset1_idx", columns={"ebi_permissionset_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiPermissionsetDatablockRepository")
 */
class EbiPermissionsetDatablock
{

    /**
     *
     * @var boolean @ORM\Column(name="timeframe_all", type="boolean", nullable=true)
     */
    private $timeframeAll;

    /**
     *
     * @var boolean @ORM\Column(name="current_calendar", type="boolean", nullable=true)
     */
    private $currentCalendar;

    /**
     *
     * @var boolean @ORM\Column(name="previous_period", type="boolean", nullable=true)
     */
    private $previousPeriod;

    /**
     *
     * @var string @ORM\Column(name="block_type", type="string", nullable=true)
     */
    private $blockType;

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Permissionset @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\PermissionSet")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_permissionset_id", referencedColumnName="id")
     *      })
     */
    private $ebiPermissionset;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\DatablockMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\DatablockMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="datablock_id", referencedColumnName="id")
     *      })
     */
    private $dataBlock;

    /**
     * Set blockType
     *
     * @param string $blockType            
     * @return EbiPermissionsetDatablock
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
     * @return EbiPermissionsetDatablock
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
     * @return EbiPermissionsetDatablock
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
     * @return EbiPermissionsetDatablock
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
     * @return EbiPermissionsetDatablock
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ebiPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\Permissionset $ebiPermissionset            
     * @return EbiPermissionsetDatablock
     */
    public function setPermissionset(\Synapse\CoreBundle\Entity\Permissionset $ebiPermissionset = null)
    {
        $this->ebiPermissionset = $ebiPermissionset;
        
        return $this;
    }

    /**
     * Get ebiPermissionset
     *
     * @return \Synapse\CoreBundle\Entity\Permissionset
     */
    public function getPermissionset()
    {
        return $this->ebiPermissionset;
    }

    /**
     * Set dataBlock
     *
     * @param \Synapse\CoreBundle\Entity\DatablockMaster $dataBlock            
     * @return EbiPermissionsetDatablock
     */
    public function setDataBlock(\Synapse\CoreBundle\Entity\DatablockMaster $dataBlock = null)
    {
        $this->dataBlock = $dataBlock;
        
        return $this;
    }

    /**
     * Get dataBlock
     *
     * @return \Synapse\CoreBundle\Entity\DatablockMaster
     */
    public function getDataBlock()
    {
        return $this->dataBlock;
    }
}