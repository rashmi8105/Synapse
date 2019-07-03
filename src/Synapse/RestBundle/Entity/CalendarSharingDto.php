<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for CalendarSharing
 *
 * @package Synapse\RestBundle\Entity
 */
class CalendarSharingDto
{

    /**
     * Person Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * Organization Id
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * Delegated Users
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\DelegatedUsersDto>")
     */
    private $delegatedUsers;

    /**
     * Returns delegated users
     *
     * @return Object
     */
    public function getDelegatedUsers()
    {
        return $this->delegatedUsers;
    }

    /**
     * Sets delegated users
     *
     * @param Object $delegatedUsers            
     */
    public function setDelegatedUsers($delegatedUsers)
    {
        $this->delegatedUsers = $delegatedUsers;
    }

    /**
     * Returns the organization Id
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the organization Id
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the person Id
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Sets the person Id
     *
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }
}