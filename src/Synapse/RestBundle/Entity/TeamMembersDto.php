<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Team members
 *
 * @package Synapse\RestBundle\Entity
 */
class TeamMembersDto
{

    /**
     * id
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * first_name
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $firstName;

    /**
     * last_name
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $lastName;

    /**
     * team_member_email_id
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $teamMemberEmailId;

    /**
     *
     * @param integer $id            
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
     * @param string $firstName            
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     *
     * @param string $lastName            
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     *
     * @param string $teamMemberEmailId            
     */
    public function setTeamMemberEmailId($teamMemberEmailId)
    {
        $this->teamMemberEmailId = $teamMemberEmailId;
    }

    /**
     *
     * @return string
     */
    public function getTeamMemberEmailId()
    {
        return $this->teamMemberEmailId;
    }
}