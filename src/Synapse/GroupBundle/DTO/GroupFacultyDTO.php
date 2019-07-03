<?php

namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GroupFacultyDTO
 *
 * @package Synapse\GroupBundle\DTO
 */
class GroupFacultyDTO
{
    /**
     * ID of the group at the organization
     *
     * @var string
     * @JMS\Type("string")
     */
    private $groupExternalId;

    /**
     * Name of the group
     *
     * @var string
     * @JMS\Type("string")
    */
    private $groupName;

    /**
     * Faculty within the group
     *
     * @var array
     * @JMS\Type("array")
     */
    private $facultyList;

    /**
     * @return string
     */
    public function getGroupExternalId()
    {
        return $this->groupExternalId;
    }

    /**
     * @param string $groupExternalId
     */
    public function setGroupExternalId($groupExternalId)
    {
        $this->groupExternalId = $groupExternalId;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * @return array
     */
    public function getFacultyList()
    {
        return $this->facultyList;
    }

    /**
     * @param array $facultyList
     */
    public function setFacultyList($facultyList)
    {
        $this->facultyList = $facultyList;
    }


}