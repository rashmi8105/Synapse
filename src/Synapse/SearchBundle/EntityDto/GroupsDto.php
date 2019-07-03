<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for IntentToLeave
 *
 * @package Synapse\SearchBundle\EntityDto
 */
class GroupsDto
{

    /**
     * groupId
     *
     * @var integer @JMS\Type("integer")
     */
    private $groupId;

    /**
     * groupName
     *
     * @var string @JMS\Type("string")
     */
    private $groupName;

    /**
     *
     * @param integer $groupId            
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     *
     * @param string $groupName            
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }
}