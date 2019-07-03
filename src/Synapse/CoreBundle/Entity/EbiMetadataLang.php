<?php

namespace Synapse\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EbiMetadataLang
 *
 * @ORM\Table(name="ebi_metadata_lang", indexes={@ORM\Index(name="metadataid_idx_lang", columns={"ebi_metadata_id"}), @ORM\Index(name="langid_idx", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiMetadataLangRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"metaName"},message="Another item exists with this name. Please choose another name")
 */
class EbiMetadataLang extends BaseEntity
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
     * @var string
     *
     * @ORM\Column(name="meta_name", type="string", length=255, nullable=true)
     */
    private $metaName;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @var \LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     */
    private $lang;

    /**
     * @var \EbiMetadata
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiMetadata")
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
     * Set metaName
     *
     * @param string $metaName
     * @return EbiMetadataLang
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
     * @return EbiMetadataLang
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
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return EbiMetadataLang
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

    /**
     * Set ebiMetadata
     *
     * @param \Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata
     * @return EbiMetadataLang
     */
    public function setEbiMetadata(\Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata = null)
    {
        $this->ebiMetadata = $ebiMetadata;

        return $this;
    }

    /**
     * Get ebiMetadata
     *
     * @return \Synapse\CoreBundle\Entity\EbiMetadata 
     */
    public function getEbiMetadata()
    {
        return $this->ebiMetadata;
    }
}
