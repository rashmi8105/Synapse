<?php

namespace Synapse\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * DatablockMetadata
 *
 * @ORM\Table(name="datablock_metadata", indexes={@ORM\Index(name="fk_datablock_metadata_datablock_master1_idx", columns={"datablock_id"}), @ORM\Index(name="fk_datablock_metadata_ebi_metadata1_idx", columns={"ebi_metadata_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\DatablockMetadataRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"ebiMetadata"},message="Item already Mapped.")
 */

class DatablockMetadata extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DatablockMaster
     *
     * @ORM\ManyToOne(targetEntity="DatablockMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="datablock_id", referencedColumnName="id")
     * })
     */
    private $datablock;

    /**
     * @var \MetadataMaster
     *
     * @ORM\ManyToOne(targetEntity="EbiMetadata")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ebi_metadata_id", referencedColumnName="id")
     * })
     */
    private $ebiMetadata;



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
     * Set id
     */
    public function setId($id)
    {
        return $this->id =$id;
    }
    
    /**
     * Set datablock
     *
     * @param \Synapse\CoreBundle\Entity\DatablockMaster $datablock
     * @return DatablockMetadata
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
     * Set ebiMetadata
     *
     * @param \Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata
     * @return DatablockMetadata
     */
    public function setEbiMetadata(\Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata = null)
    {
        $this->ebiMetadata = $ebiMetadata;

        return $this;
    }

    /**
     * Get ebiMetadata
     *
     * @return \Synapse\CoreBundle\Entity\MetadataMaster 
     */
    public function getEbiMetadata()
    {
        return $this->ebiMetadata;
    }
}
