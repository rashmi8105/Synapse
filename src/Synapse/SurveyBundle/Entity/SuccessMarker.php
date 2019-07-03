<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;


/**
 * SuccessMarker
 *
 * @ORM\Table(name="success_marker")
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\SuccessMarkerRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SuccessMarker extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     * @JMS\Expose
     */
    private $name;

    /**
     * @var integer
     * @ORM\Column(name="sequence", type="smallint", nullable=false)
     * @JMS\Expose
     */
    private $sequence;

    /**
     * @var boolean
     * @ORM\Column(name="needs_color_calculated", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $needsColorCalculated;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return boolean
     */
    public function getNeedsColorCalculated()
    {
        return $this->needsColorCalculated;
    }

    /**
     * @param boolean $needsColorCalculated
     */
    public function setNeedsColorCalculated($needsColorCalculated)
    {
        $this->needsColorCalculated = $needsColorCalculated;
    }

}