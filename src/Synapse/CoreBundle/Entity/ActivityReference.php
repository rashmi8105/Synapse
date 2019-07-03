<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ActivityReference
 *
 * @ORM\Table(name="activity_reference")
 * @ORM\Entity
 */
class ActivityReference
{
    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=45, nullable=true)
     * @JMS\Expose
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="is_active", type="blob", length=1, nullable=true)
     * @JMS\Expose
     */
    private $isActive;

    /**
     * @var integer
     *
     * @ORM\Column(name="display_seq", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $displaySeq;

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
     * Set shortName
     *
     * @param string $shortName
     * @return ActivityReference
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string 
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set isActive
     *
     * @param string $isActive
     * @return ActivityReference
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return string 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set displaySeq
     *
     * @param integer $displaySeq
     * @return ActivityReference
     */
    public function setDisplaySeq($displaySeq)
    {
        $this->displaySeq = $displaySeq;

        return $this;
    }

    /**
     * Get displaySeq
     *
     * @return integer 
     */
    public function getDisplaySeq()
    {
        return $this->displaySeq;
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
}
