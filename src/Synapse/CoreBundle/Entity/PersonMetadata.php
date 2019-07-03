<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonMetadata
 *
 * @ORM\Table(name="person_metadata")
 *  @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\PersonMetadataRepository")
 */
class PersonMetadata extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person", inversedBy="metadata")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     */
    private $person;

    /**
     * @var \Synapse\CoreBundle\Entity\MetadataMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\MetadataMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="metadata_id", referencedColumnName="id")
     * })
     */
    private $metadata;

    /**
     * @var string
     *
     * @ORM\Column(name="metadata_value", type="string", length=2000, nullable=true)
     */
    private $value;



    /**
     * Set value
     *
     * @param string $value
     * @return PersonMetadata
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
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
     * Set id
     *
     * @return PersonMetadata
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return PersonMetadata
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set metadata
     *
     * @param \Synapse\CoreBundle\Entity\MetadataMaster $metadata
     * @return PersonMetadata
     */
    public function setMetadata(\Synapse\CoreBundle\Entity\MetadataMaster $metadata)
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
}
