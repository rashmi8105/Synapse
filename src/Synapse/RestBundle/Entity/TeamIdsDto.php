<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Notes
 *
 * @package Synapse\RestBundle\Entity
 */
class TeamIdsDto
{

    /**
     * Id of a team.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Name of the team.
     * 
     * @var string @JMS\Type("string")
     */
    private $teamName;

    /**
     * Boolean determining whether the team applies to a team permission or not.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isTeamSelected;

    /**
     * Sets the id of a team.
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the id of a team.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets whether a team is a part of a team permission or not.
     *
     * @param boolean $isTeamSelected            
     */
    public function setIsTeamSelected($isTeamSelected)
    {
        $this->isTeamSelected = $isTeamSelected;
    }

    /**
     * Gets whether a team is a part of a team permission or not.
     *
     * @return boolean
     */
    public function getIsTeamSelected()
    {
        return $this->isTeamSelected;
    }

    /**
     * Sets the name of a team.
     *
     * @param string $teamName            
     */
    public function setTeamName($teamName)
    {
        $this->teamName = $teamName;
    }

    /**
     * Gets the name of a team.
     *
     * @return string
     */
    public function getTeamName()
    {
        return $this->teamName;
    }
}