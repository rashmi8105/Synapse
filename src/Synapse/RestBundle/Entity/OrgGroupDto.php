<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class OrgGroupDto
{

    /**
     * Id of a group.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $groupId;
    
    /**
     * Id assigned by an organization using mapworks.
     *
     * @var integer @JMS\Type("string")
     */
    private $externalId;

    /**
     * Id of an organization using mapworks.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Id of the group that is the parent of the current group.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $parentGroupId;

    /**
     * Name of a group.
     * 
     * @var string @JMS\Type("string")
     */
    private $groupName;

    /**
     * List of staff members within a group.
     * 
     * @var array @JMS\Type("array")
     */
    private $staffList;

    /**
     * Sets the name of a group.
     *
     * @param string $groupName            
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * Returns the name of a group.
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Sets the id of an organization.
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the id of an organization.
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets a group's parent group id.
     *
     * @param int $parentGroupId            
     */
    public function setParentGroupId($parentGroupId)
    {
        $this->parentGroupId = $parentGroupId;
    }

    /**
     * Returns a group's parent group id.
     *
     * @return int
     */
    public function getParentGroupId()
    {
        return $this->parentGroupId;
    }

    /**
     * Sets the list of staff for a group.
     *
     * @param array $staffList            
     */
    public function setStaffList($staffList)
    {
        $this->staffList = $staffList;
    }

    /**
     * Returns the list of staff for a group.
     *
     * @return array
     */
    public function getStaffList()
    {
        return $this->staffList;
    }

    /**
     * Sets the id of a group.
     *
     * @param int $groupId            
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Returns the id of a group.
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Sets the external id of a group.
     *
     * @param int $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * Returns the external id of a group.
     *
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

}