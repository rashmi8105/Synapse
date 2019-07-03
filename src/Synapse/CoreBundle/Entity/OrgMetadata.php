<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgMetadata
 *
 * @ORM\Table(name="org_metadata", uniqueConstraints={@ORM\UniqueConstraint(name="unique_index_org_key", columns={"organization_id", "meta_key"})}, indexes={@ORM\Index(name="fk_org_metadata_organization1_idx", columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgMetadataRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"metaKey", "organization"},message="Another item exists with this name. Please choose another name")
 * @UniqueEntity(fields={"metaName", "organization"},message="Another item exists with this name. Please choose another name")
 */
class OrgMetadata extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_key", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     * @Assert\Length(max=50,maxMessage = "Column header cannot be longer than {{ limit }} characters")
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_name", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $metaName;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="definition_type", type="string", length=1, precision=0, scale=0, nullable=true, unique=false)
     */
    private $definitionType;

    /**
     * @var string
     *
     * @ORM\Column(name="metadata_type", type="string", length=1, precision=0, scale=0, nullable=true, unique=false)
     */
    private $metadataType;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_of_decimals", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $noOfDecimals;

    /**
     * @var string
     *
     * @ORM\Column(name="is_required", type="boolean", length=1, precision=0, scale=0, nullable=true, unique=false)
     */
    private $isRequired;

    /**
     * @var string
     *
     * @ORM\Column(name="min_range", type="decimal", precision=15, scale=4, nullable=true, unique=false)
     */
    private $minRange;

    /**
     * @var string
     *
     * @ORM\Column(name="max_range", type="decimal", precision=15, scale=4, nullable=true, unique=false)
     */
    private $maxRange;

    /**
     * @var string
     *
     * @ORM\Column(name="entity", type="string", length=10, precision=0, scale=0, nullable=true, unique=false)
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $sequence;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_group", type="string", length=2, precision=0, scale=0, nullable=true, unique=false)
     */
    private $metaGroup;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $organization;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set metaKey
     *
     * @param string $metaKey
     * @return OrgMetadata
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * Get metaKey
     *
     * @return string 
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * Set metaName
     *
     * @param string $metaName
     * @return OrgMetadata
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
     * @return OrgMetadata
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
     * Set definitionType
     *
     * @param string $definitionType
     * @return OrgMetadata
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
     * @return OrgMetadata
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
     * @return OrgMetadata
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
     * @param string $isRequired
     * @return OrgMetadata
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired
     *
     * @return string 
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Set minRange
     *
     * @param string $minRange
     * @return OrgMetadata
     */
    public function setMinRange($minRange)
    {
        $this->minRange = $minRange;

        return $this;
    }

    /**
     * Get minRange
     *
     * @return string 
     */
    public function getMinRange()
    {
        return $this->minRange;
    }

    /**
     * Set maxRange
     *
     * @param string $maxRange
     * @return OrgMetadata
     */
    public function setMaxRange($maxRange)
    {
        $this->maxRange = $maxRange;

        return $this;
    }

    /**
     * Get maxRange
     *
     * @return string 
     */
    public function getMaxRange()
    {
        return $this->maxRange;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return OrgMetadata
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
     * @return OrgMetadata
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
     * @return OrgMetadata
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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgMetadata
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
     * @return OrgMetadata
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
