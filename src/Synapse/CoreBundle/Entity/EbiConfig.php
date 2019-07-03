<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EbiConfig
 *
 * @ORM\Table(name="ebi_config")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiConfigRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EbiConfig extends BaseEntity
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
     * @ORM\Column(name="key", type="string", length=100, nullable=true)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=2000, nullable=true)
     */
    private $value;



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
     * Set key
     *
     * @param string $key
     * @return EbiConfig
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
     * Set value
     *
     * @param string $value
     * @return EbiConfig
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
}
