<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for List Campus
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class ListCampusDto
{

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
     * totalCampus
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $totalCampus;

    /**
     * $campusDto
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\CampusDto>")
     */
    private $campus;

    /**
     *
     * @param Object $campusDto            
     */
    public function setCampus($campus)
    {
        $this->campus = $campus;
    }

    /**
     *
     * @return Object
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     *
     * @param int $totalCampus            
     */
    public function setTotalCampus($totalCampus)
    {
        $this->totalCampus = $totalCampus;
    }

    /**
     *
     * @return int
     */
    public function getTotalCampus()
    {
        return $this->totalCampus;
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
}