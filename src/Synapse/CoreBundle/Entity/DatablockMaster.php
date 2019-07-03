<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * DatablockMaster
 *
 * @ORM\Table(name="datablock_master", indexes={@ORM\Index(name="datablockr_datablockuiid_idx", columns={"datablock_ui_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\DatablockMasterRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class DatablockMaster extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="block_type", type="string", length=10, precision=0, scale=0, nullable=true, unique=false)
     */
    private $blockType;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\DatablockUi @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\DatablockUi")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="datablock_ui_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $datablockUi;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection @ORM\OneToMany(targetEntity="DatablockMetadata", mappedBy="datablock")
     *      @JMS\Expose
     */
    private $datablockMetadata;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", length=1, precision=0, scale=0, nullable=true, unique=false)
     */
    private $status;

    public function __construct()
    {
        $this->datablockMetadata = new ArrayCollection();
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
     * Set blockType
     *
     * @param string $blockType            
     * @return DatablockMaster
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
     * Set datablockUi
     *
     * @param \Synapse\CoreBundle\Entity\DatablockUi $datablockUi            
     * @return DatablockMaster
     */
    public function setDatablockUi(\Synapse\CoreBundle\Entity\DatablockUi $datablockUi = null)
    {
        $this->datablockUi = $datablockUi;
        
        return $this;
    }

    /**
     * Get datablockUi
     *
     * @return \Synapse\CoreBundle\Entity\DatablockUi
     */
    public function getDatablockUi()
    {
        return $this->datablockUi;
    }

    public function getDatablockMetadata()
    {
        return $this->datablockMetadata;
    }

    public function addDatablockMetadata($datablockMetadata)
    {
        $this->datablockMetadata[] = $datablockMetadata;
    }

    /**
     * Set status
     *
     * @param string $status            
     * @return DatablockMaster
     */
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
