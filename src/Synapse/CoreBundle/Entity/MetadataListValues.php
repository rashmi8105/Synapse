<?php

namespace Synapse\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * MetadataListValues
 *
 * @ORM\Table(name="metadata_list_values")
*  @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\MetadataListValuesRepository")
*  @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
*  @UniqueEntity(fields={"metadata","listValue"},errorPath="listValue",message="List Value already exists.")
 */
class MetadataListValues extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="list_name", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $listName;

    /**
     * @var string
     *
     * @ORM\Column(name="list_value", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $listValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $sequence;

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
     * Set listName
     *
     * @param string $listName
     * @return MetadataListValues
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
     * @return MetadataListValues
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
     * @return MetadataListValues
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
     * @return MetadataListValues
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
     * @return MetadataListValues
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