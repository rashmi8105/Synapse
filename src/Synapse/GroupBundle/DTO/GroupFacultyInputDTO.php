<?php

namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GroupFacultyDTO
 *
 * @package Synapse\GroupBundle\DTO
 */
class GroupFacultyInputDTO
{
    /**
     * permissionset name
     *
     * @var string @JMS\Type("string")
     */
    private $permissionsetName;

    /**
     * Faculty's external ID
     *
     * @var string @JMS\Type("string")
     *
     */
    private $facultyId;

    /**
     * Groups external ID
     *
     * @var string @JMS\Type("string")
     *
     */
    private $groupId;

    /**
     * Groups visible status for the faculty
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $isInvisible;

    /**
     * @return string
     */
    public function getPermissionsetName()
    {
        return $this->permissionsetName;
    }

    /**
     * @param string $permissionsetName
     */
    public function setPermissionsetName($permissionsetName)
    {
        $this->permissionsetName = $permissionsetName;
    }

    /**
     * @return string
     */
    public function getFacultyId()
    {
        return $this->facultyId;
    }

    /**
     * @param string $facultyId
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
    }

    /**
     * @return boolean
     */
    public function getIsInvisible()
    {
        return $this->isInvisible;
    }

    /**
     * @param boolean $isInvisible
     */
    public function setIsInvisible($isInvisible)
    {
        $this->isInvisible = $isInvisible;
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param string $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }
}