<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;


/**
 * SuccessMarkerColor
 *
 * @ORM\Table(name="success_marker_color")
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\SuccessMarkerColorRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SuccessMarkerColor extends BaseEntity
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
     * @ORM\Column(name="color", type="string", nullable=false)
     * @JMS\Expose
     */
    private $color;

    /**
     * @var integer
     * @ORM\Column(name="base_value", type="smallint", nullable=false)
     * @JMS\Expose
     */
    private $baseValue;

    /**
     * @var string
     * @ORM\Column(name="min_value", type="decimal", precision=6, scale=3, nullable=false)
     * @JMS\Expose
     */
    private $minValue;

    /**
     * @var string
     * @ORM\Column(name="max_value", type="decimal", precision=6, scale=3, nullable=false)
     * @JMS\Expose
     */
    private $maxValue;

    
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
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * @param int $baseValue
     */
    public function setBaseValue($baseValue)
    {
        $this->baseValue = $baseValue;
    }

    /**
     * @return string
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @param string $minValue
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @return string
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @param string $maxValue
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
    }

}