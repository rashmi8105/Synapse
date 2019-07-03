<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class FacultyDetailsDto
{

    /**
     * facultyId
     *
     * @var integer @JMS\Type("integer")
     */
    private $facultyId;

    /**
     * firstName
     *
     * @var string @JMS\Type("string")
     */
    private $firstName;

    /**
     * lastName
     *
     * @var string @JMS\Type("string")
     */
    private $lastName;

    /**
     * facultyName
     *
     * @var string @JMS\Type("string")
     */
    private $facultyName;

    /**
     * permissionsetId
     *
     * @var integer @JMS\Type("integer")
     */
    private $permissionsetId;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * id
     *
     * @var string @JMS\Type("string")
     */
    private $id;

    /**
     * permissions
     *
     * @var Object @JMS\Type("array<Synapse\AcademicBundle\EntityDto\FacultyPermissionSetDto>")
     */
    private $permissions;

    /**
     *
     * @param integer $facultyId            
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
    }

    /**
     *
     * @return integer
     */
    public function getFacultyId()
    {
        return $this->facultyId;
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
     * @param string $facultyName            
     */
    public function setFacultyName($facultyName)
    {
        $this->facultyName = $facultyName;
    }

    /**
     *
     * @return string
     */
    public function getFacultyName()
    {
        return $this->facultyName;
    }

    /**
     *
     * @param integer $permissionsetId            
     */
    public function setPermissionsetId($permissionsetId)
    {
        $this->permissionsetId = $permissionsetId;
    }

    /**
     *
     * @return integer
     */
    public function getPermissionsetId()
    {
        return $this->permissionsetId;
    }

    /**
     *
     * @param string $email            
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @param string $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param Object $courseListTable            
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     *
     * @return Object
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}