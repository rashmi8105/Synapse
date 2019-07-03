<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Tier
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class PrimaryTierDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * primaryTierId
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $primaryTierId;

    /**
     * primaryTierName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $primaryTierName;

    /**
     * countSecondaryTiers
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $countSecondaryTiers;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\TierDto>")
     *     
     *     
     */
    private $primaryTiers;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\SecondaryTiersDto>")
     *     
     *     
     */
    private $secondaryTiers;

    /**
     * countCampus
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $countCampus;

    /**
     * countUsers
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $countUsers;

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
     * @param int $countSecondaryTiers            
     */
    public function setCountSecondaryTiers($countSecondaryTiers)
    {
        $this->countSecondaryTiers = $countSecondaryTiers;
    }

    /**
     *
     * @return int
     */
    public function getCountSecondaryTiers()
    {
        return $this->countSecondaryTiers;
    }

    /**
     *
     * @param int $countCampus            
     */
    public function setCountCampus($countCampus)
    {
        $this->countCampus = $countCampus;
    }

    /**
     *
     * @return int
     */
    public function getCountCampus()
    {
        return $this->countCampus;
    }

    /**
     *
     * @return int $countUsers
     */
    public function setCountUsers()
    {
        return $this->countUsers;
    }

    /**
     *
     * @return int
     */
    public function getCountUsers()
    {
        return $this->countUsers;
    }

    /**
     *
     * @param array $primaryTiers            
     */
    public function setPrimaryTiers($primaryTiers)
    {
        $this->primaryTiers = $primaryTiers;
    }

    /**
     *
     * @return array
     */
    public function getPrimaryTiers()
    {
        return $this->primaryTiers;
    }

    /**
     *
     * @param array $secondaryTiers            
     */
    public function setSecondaryTiers($secondaryTiers)
    {
        $this->secondaryTiers = $secondaryTiers;
    }

    /**
     *
     * @return array
     */
    public function getSecondaryTiers()
    {
        return $this->secondaryTiers;
    }
}