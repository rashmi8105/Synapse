<?php
namespace Synapse\StudentViewBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for List Campus Connections
 *
 * @package Synapse\StudentViewBundle\EntityDto
 */
class ListCampusConnectionDto
{

    /**
     * $campusConnection
     *
     * @var Object @JMS\Type("array<Synapse\StudentViewBundle\EntityDto\CampusConnectionDto>")
     */
    private $campusConnection;

    /**
     *
     * @param Object $campusConnection            
     */
    public function setCampusConnection($campusConnection)
    {
        $this->campusConnection = $campusConnection;
    }

    /**
     *
     * @return Object
     */
    public function getCampusConnection()
    {
        return $this->campusConnection;
    }
}
