<?php

namespace Synapse\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MetadataMaster
 *
 * @ORM\Table(name="metadata_master")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\MetadataMasterRepository")
 * 
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"key","organization","definitionType"},message="Item Label already exists", ignoreNull=false) 
 */
class MetadataMaster extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="meta_key", type="string", length=30, nullable=true)
     * @JMS\Expose
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="definition_type", type="string", length=1, nullable=true)
     * @JMS\Expose
     */
    private $definitionType;

    /**
     * @var string
     *
     * @ORM\Column(name="metadata_type", type="string", length=1, nullable=true)
     * @JMS\Expose
     */
    private $metadataType;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_of_decimals", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $noOfDecimals;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_required", type="boolean", nullable=true)
     */
    private $isRequired;

    /**
     * @var integer
     *
     * @ORM\Column(name="min_range", type="decimal", precision=15, scale=4, nullable=true)
     * @JMS\Expose
     */
    private $minRange;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_range", type="decimal", precision=15, scale=4, nullable=true)
     * @JMS\Expose
     */
    private $maxRange;

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
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\Entity
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Entity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $entity;



    /**
     * Set key
     *
     * @param string $key
     * @return MetadataMaster
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set definitionType
     *
     * @param string $definitionType
     * @return MetadataMaster
     */
    public function setDefinitionType($definitionType)
    {
        $this->definitionType = $definitionType;

        return $this;
    }

    /**
     * Get definitionType
     *
     * @return string
     */
    public function getDefinitionType()
    {
        return $this->definitionType;
    }

    /**
     * Set metadataType
     *
     * @param string $metadataType
     * @return MetadataMaster
     */
    public function setMetadataType($metadataType)
    {
        $this->metadataType = $metadataType;

        return $this;
    }

    /**
     * Get metadataType
     *
     * @return string
     */
    public function getMetadataType()
    {
        return $this->metadataType;
    }

    /**
     * Set noOfDecimals
     *
     * @param integer $noOfDecimals
     * @return MetadataMaster
     */
    public function setNoOfDecimals($noOfDecimals)
    {
        $this->noOfDecimals = $noOfDecimals;

        return $this;
    }

    /**
     * Get noOfDecimals
     *
     * @return integer
     */
    public function getNoOfDecimals()
    {
        return $this->noOfDecimals;
    }

    /**
     * Set isRequired
     *
     * @param boolean $isRequired
     * @return MetadataMaster
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired
     *
     * @return boolean
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Set minRange
     *
     * @param integer $minRange
     * @return MetadataMaster
     */
    public function setMinRange($minRange)
    {
        $this->minRange = $minRange;

        return $this;
    }

    /**
     * Get minRange
     *
     * @return integer
     */
    public function getMinRange()
    {
        return $this->minRange;
    }

    /**
     * Set maxRange
     *
     * @param integer $maxRange
     * @return MetadataMaster
     */
    public function setMaxRange($maxRange)
    {
        $this->maxRange = $maxRange;

        return $this;
    }

    /**
     * Get maxRange
     *
     * @return integer
     */
    public function getMaxRange()
    {
        return $this->maxRange;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return MetadataMaster
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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return MetadataMaster
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

    /**
     * Set entity
     *
     * @param \Synapse\CoreBundle\Entity\Entity $entity
     * @return MetadataMaster
     */
    public function setEntity(\Synapse\CoreBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \Synapse\CoreBundle\Entity\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }
}