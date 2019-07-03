<?php

namespace Synapse\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * MetadataMasterLang
 *
 * @ORM\Table(name="metadata_master_lang")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\MetadataMasterLangRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class MetadataMasterLang extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="meta_name", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $metaName;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $metaDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\MetadataMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\MetadataMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="metadata_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $metadata;

    /**
     * @var \Synapse\CoreBundle\Entity\LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $lang;



    /**
     * Set metaName
     *
     * @param string $metaName
     * @return MetadataMasterLang
     */
    public function setMetaName($metaName)
    {
        $this->metaName = $metaName;

        return $this;
    }

    /**
     * Get metaName
     *
     * @return string 
     */
    public function getMetaName()
    {
        return $this->metaName;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return MetadataMasterLang
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string 
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
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
     * Set metadata
     *
     * @param \Synapse\CoreBundle\Entity\MetadataMaster $metadata
     * @return MetadataMasterLang
     */
    public function setMetadata(\Synapse\CoreBundle\Entity\MetadataMaster $metadata = null)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get metadata
     *
     * @return \Synapse\CoreBundle\Entity\MetadataMaster 
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return MetadataMasterLang
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