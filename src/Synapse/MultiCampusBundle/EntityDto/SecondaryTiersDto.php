<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Tier
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class SecondaryTiersDto
{

    /**
     * primaryTierId
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $primaryTierId;

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * primaryTierName
     *
     * @var string @JMS\Type("string")
     *     
     *     
     */
    private $primaryTierName;

    /**
     * secondaryTierId
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $secondaryTierId;

    /**
     * secondaryTierName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $secondaryTierName;

    /**
     * totalSecondaryTiers
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $totalSecondaryTiers;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\TierDto>")
     *     
     *     
     */
    private $secondaryTiers;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\CampusDto>")
     *     
     *     
     */
    private $campuses;

    /**
     *
     * @param string $primaryTierName            
     */
    public function setPrimaryTierName($primaryTierName)
    {
        $this->primaryTierName = $primaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getPrimaryTierName()
    {
        return $this->primaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getCampuses()
    {
        return $this->campuses;
    }

    /**
     *
     * @param mixed $campuses            
     */
    public function setCampuses($campuses)
    {
        $this->campuses = $campuses;
    }

    /**
     *
     * @return string
     */
    public function getSecondaryTiers()
    {
        return $this->secondaryTiers;
    }

    /**
     *
     * @param mixed $secondaryTiers            
     */
    public function setSecondaryTiers($secondaryTiers)
    {
        $this->secondaryTiers = $secondaryTiers;
    }

    /**
     *
     * @param int $primaryTierId            
     */
    public function setPrimaryTierId($primaryTierId)
    {
        $this->primaryTierId = $primaryTierId;
    }

    /**
     *
     * @return int
     */
    public function getPrimaryTierId()
    {
        return $this->primaryTierId;
    }

    /**
     *
     * @param string $totalSecondaryTiers            
     */
    public function setTotalSecondaryTiers($totalSecondaryTiers)
    {
        $this->totalSecondaryTiers = $totalSecondaryTiers;
    }

    /**
     *
     * @return string
     */
    public function getTotalSecondaryTiers()
    {
        return $this->totalSecondaryTiers;
    }

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $secondaryTierId            
     */
    public function setSecondaryTierId($secondaryTierId)
    {
        $this->secondaryTierId = $secondaryTierId;
    }

    /**
     *
     * @return int
     */
    public function getSecondaryTierId()
    {
        return $this->secondaryTierId;
    }

    /**
     *
     * @param string $secondaryTierName            
     */
    public function setSecondaryTierName($secondaryTierName)
    {
        $this->secondaryTierName = $secondaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getSecondaryTierName()
    {
        return $this->secondaryTierName;
    }
}