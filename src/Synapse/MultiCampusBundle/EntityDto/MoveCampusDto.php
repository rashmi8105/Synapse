<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Tier
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class MoveCampusDto
{

    /**
     * campusId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $campusId;

    /**
     * primaryTierId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $primaryTierId;

    /**
     * secondaryTierId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $secondaryTierId;

    /**
     *
     * @param int $campusId            
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     *
     * @return int
     */
    public function getCampusId()
    {
        return $this->campusId;
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
}