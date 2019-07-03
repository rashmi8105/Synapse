<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class RoleDto
{

    /**
     * Id of a person.
     *
     * @var integer @JMS\Type("integer")
     */
    private $userId;

    /**
     * Id of a person's campus.
     *
     * @var integer @JMS\Type("integer")
     */
    private $campusId;

    /**
     * Id of a person's role within their campus.
     *
     * @var integer @JMS\Type("integer")
     */
    private $roleId;

    /**
     * Person's role.
     *
     * @var string @JMS\Type("string")
     */
    private $role;

    /**
     *
     * @param int $userId            
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @param int $campusId            
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     *
     * @return int
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     *
     * @return integer
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     *
     * @param integer $roleId            
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     *
     * @param string $role            
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}