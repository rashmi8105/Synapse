<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Teams
 *
 * @package Synapse\RestBundle\Entity
 */
class TeamsDto
{

    /**
     * Id of a person within a team.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $personId;

    /**
     * Name of the team that a person is in.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $teamName;

    /**
     * Description of a team.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $teamDescription;

    /**
     * Unique id of a team.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $teamId;

    /**
     * Id of a team's organization.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $organization;

    /**
     * Same as teamId; possibly deprecated.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $team;

    /**
     * If True, then the person is the leader of the team.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $isTeamLeader;

    /**
     * Team-specific id given to a person.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $teamMembersId;

    /**
     * Ids of a person's team members.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $teamMemberIds;

    /**
     * Person id.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $person;

    /**
     * Array of staff members.
     *
     * @var array
     * @JMS\Type("array")
     */
    private $staff;

    /**
     * Action that a person is doing.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $action;

    /**
     * Object representing the teams that a person is in.
     *
     * @var Object
     * @JMS\Type("array<Synapse\RestBundle\Entity\TeamIdsDto>")
     */
    private $teamIds;

    /**
     * Object representing a person's team members.
     *
     * @var Object
     * @JMS\Type("array<Synapse\RestBundle\Entity\TeamMembersDto>")
     */
    private $teamMembers;

    /**
     * Array of recent team activities.
     *
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\TeamActivityCountDto>")
     */
    private $recentActivities;

    /**
     * Id of a person's team member.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $teamMemberId;

    /**
     * Type of activity a person is doing.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $activityType;

    /**
     * Filters team member or team results.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $filter;

    /**
     * Number of total results after search.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalRecords;

    /**
     * Total pages after records per page and filters have been set.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalPages;

    /**
     * Number of records allowed per page.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $recordsPerPage;

    /**
     * Number of the current page of results.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $currentPage;

    /**
     * Array representing a person's team member activities.
     *
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\TeamMembersActivitiesDto>")
     */
    private $teamMembersActivities;


    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }


    /**
     *
     * @return array
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     *
     * @param array $staff
     */
    public function setStaff($staff)
    {
        $this->staff = $staff;
    }


    /**
     *
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }


    /**
     *
     * @return string
     */
    public function getIsTeamLeader()
    {
        return $this->isTeamLeader;
    }

    /**
     *
     * @param string $isTeamLeader
     */
    public function setIsTeamLeader($isTeamLeader)
    {
        $this->isTeamLeader = $isTeamLeader;
    }


    /**
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     *
     * @param int $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }


    /**
     *
     * @return int
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     *
     * @param int $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }


    /**
     *
     * @return int
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     *
     * @param int $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }


    /**
     *
     * @return string
     */
    public function getTeamDescription()
    {
        return $this->teamDescription;
    }

    /**
     *
     * @param string $teamDescription
     */
    public function setTeamDescription($teamDescription)
    {
        $this->teamDescription = $teamDescription;
    }


    /**
     *
     * @return int
     */
    public function getTeamMembersId()
    {
        return $this->teamMembersId;
    }

    /**
     *
     * @param int $teamMembersId
     */
    public function setTeamMembersId($teamMembersId)
    {
        $this->teamMembersId = $teamMembersId;
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
    public function getAction()
    {
        return $this->action;
    }

    /**
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }


    /**
     *
     * @return object
     */
    public function getTeamIds()
    {
        return $this->teamIds;
    }

    /**
     *
     * @param object $teamIds
     */
    public function setTeamIds($teamIds)
    {
        $this->teamIds = $teamIds;
    }


    /**
     *
     * @return object
     */
    public function getTeamMembers()
    {
        return $this->teamMembers;
    }

    /**
     *
     * @param object $teamMembers
     */
    public function setTeamMembers($teamMembers)
    {
        $this->teamMembers = $teamMembers;
    }


    /**
     *
     * @return array
     */
    public function getRecentActivities()
    {
        return $this->recentActivities;
    }

    /**
     *
     * @param array $recentActivities
     */
    public function setRecentActivities($recentActivities)
    {
        $this->recentActivities = $recentActivities;
    }


    /**
     *
     * @return int
     */
    public function getTeamMemberId()
    {
        return $this->teamMemberId;
    }

    /**
     *
     * @param int $teamMemberId
     */
    public function setTeamMemberId($teamMemberId)
    {
        $this->teamMemberId = $teamMemberId;
    }


    /**
     *
     * @return string
     */
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     *
     * @param string $activityType
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
    }


    /**
     *
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     *
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }


    /**
     *
     * @return array
     */
    public function getTeamMembersActivities()
    {
        return $this->teamMembersActivities;
    }

    /**
     *
     * @param array $teamMembersActivities
     */
    public function setTeamMembersActivities($teamMembersActivities)
    {
        $this->teamMembersActivities = $teamMembersActivities;
    }


    /**
     *
     * @return string
     */
    public function getTeamMemberIds()
    {
        return $this->teamMemberIds;
    }

    /**
     *
     * @param string $teamMemberIds
     */
    public function setTeamMemberIds($teamMemberIds)
    {
        $this->teamMemberIds = $teamMemberIds;
    }


    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }


    public function setTotalPages($totalpages)
    {
        $this->totalPages = $totalpages;
    }


    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
    }


    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }
}