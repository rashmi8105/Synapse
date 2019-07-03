<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Notes
 *
 * @package Synapse\RestBundle\Entity
 */
class ShareOptionsDto
{

    /**
     * Private permission. If TRUE, only the creator and persons related to the activity can view this.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $privateShare;

    /**
     * Public permission. If TRUE, any person within the organization can view this.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $publicShare;

    /**
     * Team permission. If TRUE, only a person within the same team as the creator can view this.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $teamsShare;

    /**
     * Array of teams. If $teamsShare is TRUE, this represents the teams that have access to the activity.
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\TeamIdsDto>")
     */
    private $teamIds;

    /**
     * Sets whether or not an activity has private permissions.
     *
     * @param boolean $privateShare            
     */
    public function setPrivateShare($privateShare)
    {
        $this->privateShare = $privateShare;
    }

    /**
     * Gets whether or not an activity has private permissions.
     *
     * @return boolean
     */
    public function getPrivateShare()
    {
        return $this->privateShare;
    }

    /**
     * Sets whether or not an activity has public permissions.
     *
     * @param boolean $publicShare            
     */
    public function setPublicShare($publicShare)
    {
        $this->publicShare = $publicShare;
    }

    /**
     * Gets whether or not an activity has public permissions.
     *
     * @param boolean $teamsShare            
     */
    public function setTeamsShare($teamsShare)
    {
        $this->teamsShare = $teamsShare;
    }

    /**
     * Sets whether or not an activity has team permissions.
     *
     * @return boolean
     */
    public function getTeamsShare()
    {
        return $this->teamsShare;
    }

    /**
     * Gets whether or not an activity has team permissions.
     *
     * @return boolean
     */
    public function getPublicShare()
    {
        return $this->publicShare;
    }

    /**
     * Sets the teams that are a part of a teams permission.
     *
     * @param Object $teamIds            
     */
    public function setTeamIds($teamIds)
    {
        $this->teamIds = $teamIds;
    }

    /**
     * Gets the teams that are a part of a teams permission.
     *
     * @return Object
     */
    public function getTeamIds()
    {
        return $this->teamIds;
    }
}