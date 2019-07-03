<?php
namespace Synapse\PdfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EbiTemplate
 *
 * @ORM\Table(name="ebi_template")
 * @ORM\Entity(repositoryClass="")
 */
class EbiTemplate
{

    /**
     *
     * @var string 
     * @ORM\Id
     * @ORM\Column(name="key", type="string", length=45, nullable=false)
     */
    private $key;

    /**
     *
     * @var string @ORM\Column(name="is_active", type="string", columnDefinition="ENUM('y','n')", nullable=true)
     */
    private $isActive;

    /**
     *
     * @param string $isActive            
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @param string $key            
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}