<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrgMetadataListValues
 *
 * @ORM\Table(name="org_metadata_list_values", indexes={@ORM\Index(name="fk_org_metadata_list_values_org_metadata1_idx", columns={"org_metadata_id"}), @ORM\Index(name="list_name_idx", columns={"list_name"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgMetadataListValuesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgMetadataListValues extends BaseEntity
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
     * @ORM\Column(name="list_name", type="string", length=255, nullable=true)
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
     * @var \OrgMetadata
     *
     * @ORM\ManyToOne(targetEntity="OrgMetadata")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_metadata_id", referencedColumnName="id")
     * })
     */
    private $orgMetadata;



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
     * @return OrgMetadataListValues
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
     * @return OrgMetadataListValues
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
     * @return OrgMetadataListValues
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
     * Set orgMetadata
     *
     * @param \Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata
     * @return OrgMetadataListValues
     */
    public function setOrgMetadata(\Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata = null)
    {
        $this->orgMetadata = $orgMetadata;

        return $this;
    }

    /**
     * Get orgMetadata
     *
     * @return \Synapse\CoreBundle\Entity\OrgMetadata 
     */
    public function getOrgMetadata()
    {
        return $this->orgMetadata;
    }
}
