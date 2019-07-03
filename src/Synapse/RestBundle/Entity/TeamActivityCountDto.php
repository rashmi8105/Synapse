<?php

namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Recent Activities
 *
 * @package Synapse\RestBundle\Entity
 */
class TeamActivityCountDto
{

    /**
     * team_id
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $teamId;

    /**
     * team_name
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $teamName;

    /**
     * team_open_referrals
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $teamOpenReferrals;

    /**
     * team_activities
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $teamActivities;

    /**
     * team_logins
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $teamLogins;

    /**
     *
     * @param integer $teamId            
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     *
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     *
     * @param string $teamName            
     */
    public function setTeamName($teamName)
    {
        $this->teamName = $teamName;
    }

    /**
     *
     * @return string
     */
    public function getTeamName()
    {
        return $this->teamName;
    }

    /**
     *
     * @param integer $teamOpenReferrals            
     */
    public function setTeamOpenReferrals($teamOpenReferrals)
    {
        $this->teamOpenReferrals = $teamOpenReferrals;
    }

    /**
     *
     * @return int
     */
    public function getTeamOpenReferrals()
    {
        return $this->teamOpenReferrals;
    }

    /**
     *
     * @param integer $teamActivities            
     */
    public function setTeamActivities($teamActivities)
    {
        $this->teamActivities = $teamActivities;
    }

    /**
     *
     * @return int
     */
    public function getTeamActivities()
    {
        return $this->teamActivities;
    }

    /**
     *
     * @param integer $teamLogins            
     */
    public function setTeamLogins($teamLogins)
    {
        $this->teamLogins = $teamLogins;
    }

    /**
     *
     * @return int
     */
    public function getTeamLogins()
    {
        return $this->teamLogins;
    }
}