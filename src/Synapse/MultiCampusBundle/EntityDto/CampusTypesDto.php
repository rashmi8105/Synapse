<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for List Campus
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class CampusTypesDto
{

    /**
     *
     * @var Object @JMS\Type("Synapse\MultiCampusBundle\EntityDto\TierDto")
     *     
     *     
     */
    private $campusList;

    /**
     *
     * @param Object $campusList            
     */
    public function setCampusList($campusList)
    {
        $this->campusList = $campusList;
    }

    /**
     *
     * @return Object
     */
    public function getCampusList()
    {
        return $this->campusList;
    }
}