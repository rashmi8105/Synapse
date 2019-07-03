<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * DatablockMasterLang
 *
 * @ORM\Table(name="datablock_master_lang", indexes={@ORM\Index(name="fk_datablocklang_datablockid_idx", columns={"datablock_id"}), @ORM\Index(name="fk_datablocklang_langid_idx", columns={"lang_id"})})
 * @ORM\Entity (repositoryClass="Synapse\CoreBundle\Repository\DatablockMasterLangRepository")
 * @UniqueEntity(fields={"datablockDesc","lang"},message="Datablock Name already exists.")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class DatablockMasterLang extends BaseEntity
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
     * @var string @ORM\Column(name="datablock_desc", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     *      @Assert\NotBlank()
     *      @Assert\Length(min = 1,
     *      max = 50,
     *      minMessage = "datablockDesc must be at least {{ limit }} characters long",
     *      maxMessage = "datablockDesc cannot be longer than {{ limit }} characters long"
     *      )
     */
    private $datablockDesc;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\DatablockMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\DatablockMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="datablock_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $datablock;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $lang;

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
     * Set datablockDesc
     *
     * @param string $datablockDesc            
     * @return DatablockMasterLang
     */
    public function setDatablockDesc($datablockDesc)
    {
        $this->datablockDesc = $datablockDesc;
        
        return $this;
    }

    /**
     * Get datablockDesc
     *
     * @return string
     */
    public function getDatablockDesc()
    {
        return $this->datablockDesc;
    }

    /**
     * Set datablock
     *
     * @param \Synapse\CoreBundle\Entity\DatablockMaster $datablock            
     * @return DatablockMasterLang
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
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang            
     * @return DatablockMasterLang
     */
    public function setLang(\Synapse\CoreBundle\Entity\LanguageMaster $lang = null)
    {
        $this->lang = $lang;
        
        return $this;
    }

    /**
     * Get lang
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLang()
    {
        return $this->lang;
    }
}
