<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EbiMetadataListValues
 *
 * @ORM\Table(name="ebi_metadata_list_values", indexes={@ORM\Index(name="metadataid_idx", columns={"ebi_metadata_id"}), @ORM\Index(name="metadatalistvalues_langid_idx", columns={"lang_id"}), @ORM\Index(name="metadata_listname_idx", columns={"list_name"})})
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiMetadataListValuesRepository")
 */
class EbiMetadataListValues extends BaseEntity
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
     * @ORM\Column(name="list_name", type="string", length=1000, nullable=true)
     */
    private $listName;

    /**
     * @var string
     *
     * @ORM\Column(name="list_value", type="string", length=255, nullable=true)
     */
    private $listValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    private $sequence;

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
     * Set listName
     *
     * @param string $listName
     * @return EbiMetadataListValues
     */
    public function setListName($listName)
    {
        $this->listName = $listName;

        return $this;
    }

    /**
     * Get listName
     *
     * @return string 
     */
    public function getListName()
    {
        return $this->listName;
    }

    /**
     * Set listValue
     *
     * @param string $listValue
     * @return EbiMetadataListValues
     */
    public function setListValue($listValue)
    {
        $this->listValue = $listValue;

        return $this;
    }

    /**
     * Get listValue
     *
     * @return string 
     */
    public function getListValue()
    {
        return $this->listValue;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return EbiMetadataListValues
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return EbiMetadataListValues
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
     * @return EbiMetadataListValues
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
