<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class PermissionDto
{

    /**
     * permissionId
     *
     * @var integer @JMS\Type("integer")
     */
    private $permissionId;

    /**
     * permissionName
     *
     * @var string @JMS\Type("string")
     */
    private $permissionName;

    /**
     *
     * @param int $permissionId            
     */
    public function setPermissionId($permissionId)
    {
        $this->permissionId = $permissionId;
    }

    /**
     *
     * @return int
     */
    public function getPermissionId()
    {
        return $this->permissionId;
    }

    /**
     *
     * @return string
     */
    public function getPermissionName()
    {
        return $this->permissionName;
    }

    /**
     *
     * @param string $permissionName            
     */
    public function setPermissionName($permissionName)
    {
        $this->permissionName = $permissionName;
    }
}