<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EbiMetadata
 *
 * @ORM\Table(name="ebi_metadata")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiMetadataRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"key"},message="Another item exists with this name. Please choose another name")
 */
class EbiMetadata extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="meta_key", type="string", length=50, nullable=true)
     * @Assert\Length(max=50,maxMessage = "Column header cannot be longer than {{ limit }} characters")
     */
    private $key;

    /**
     *
     * @var string @ORM\Column(name="definition_type", type="string", length=1, nullable=true)
     */
    private $definitionType;

    /**
     *
     * @var string @ORM\Column(name="metadata_type", type="string", length=1, nullable=true)
     */
    private $metadataType;

    /**
     *
     * @var integer @ORM\Column(name="no_of_decimals", type="integer", nullable=true)
     */
    private $noOfDecimals;

    /**
     *
     * @var boolean @ORM\Column(name="is_required", type="boolean", length=1, nullable=true)
     *     
     */
    private $isRequired;

    /**
     *
     * @var integer @ORM\Column(name="min_range", type="decimal", precision=15, scale=4, nullable=true)
     *      @JMS\Expose
     */
    private $minRange;

    /**
     *
     * @var integer @ORM\Column(name="max_range", type="decimal", precision=15, scale=4, nullable=true)
     *      @JMS\Expose
     */
    private $maxRange;

    /**
     *
     * @var string @ORM\Column(name="entity", type="string", length=10, nullable=true)
     */
    private $entity;

    /**
     *
     * @var integer @ORM\Column(name="sequence", type="integer", nullable=true)
     *      @JMS\Expose
     */
    private $sequence;

    /**
     *
     * @var string @ORM\Column(name="meta_group", type="string", length=2, nullable=true)
     */
    private $metaGroup;

    /**
     *
     * @var string @ORM\Column(name="scope", type="string", length=1, nullable=true)
     */
    private $scope;
    
    /**
     *
     * @var string @ORM\Column(name="status", type="string", columnDefinition="ENUM('active','archived')", nullable=true)
     */
    private $status;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set key
     *
     * @param string $key            
     * @return EbiMetadata
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
     * @return EbiMetadata
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
     * @return EbiMetadata
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
     * @return EbiMetadata
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
     * @return EbiMetadata
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
     * @return EbiMetadata
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
     * @return EbiMetadata
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
     * Set entity
     *
     * @param string $entity            
     * @return EbiMetadata
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        
        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence            
     * @return EbiMetadata
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
     * Set metaGroup
     *
     * @param string $metaGroup            
     * @return EbiMetadata
     */
    public function setMetaGroup($metaGroup)
    {
        $this->metaGroup = $metaGroup;
        
        return $this;
    }

    /**
     * Get metaGroup
     *
     * @return string
     */
    public function getMetaGroup()
    {
        return $this->metaGroup;
    }

    /**
     * Set scope
     *
     * @param string $scope            
     * @return EbiMetadata
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        
        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
    
    /**
     * Set status
     *
     * @param string $status
     * @return EbiMetadata
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