<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class UsersDto
{

    /**
     * userId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $userId;

    /**
     * campusId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $campusId;

    /**
     * requestId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $requestId;

    /**
     * id
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * firstName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $firstName;

    /**
     * lastName
     *
     * @var string @JMS\Type("string")
     */
    private $lastName;

    /**
     * title
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $title;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $email;

    /**
     * phone
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $phone;

    /**
     * contactNumber
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $contactNumber;

    /**
     * primaryTierName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $primaryTierName;

    /**
     * secondaryTierName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $secondaryTierName;

    /**
     * campusName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $campusName;

    /**
     * userType
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $userType;

    /**
     * externalId
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $externalId;

    /**
     * role
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $role;

    /**
     * permissions
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\PermissionDto>")
     */
    private $permissions;

    /**
     * institutions
     *
     * @var Object @JMS\Type("Synapse\MultiCampusBundle\EntityDto\CampusDto")
     */
    private $institutions;

    /**
     * request_date
     *
     * @JMS\Type("DateTime")
     *     
     */
    private $requestDate;

    /**
     * requested_date
     *
     * @JMS\Type("DateTime")
     */
    private $requestedDate;

    /**
     * requestedBy
     *
     * @var Object @JMS\Type("Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto")
     */
    private $requestedBy;

    /**
     * requestedFrom
     *
     * @var Object @JMS\Type("Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto")
     */
    private $requestedFrom;
    
    /**
     * lastLogin
     *
     * @var string @JMS\Type("string")
     *
     */
    private $lastLogin;

    /**
     *
     * @param integer $userId            
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @param integer $requestId            
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     *
     * @return integer
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     *
     * @param integer $campusId            
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     *
     * @return integer
     */
    public function getCampusId()
    {
        return $this->campusId;
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
     * @param string $firstName            
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
     * @param string $title            
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @param string $phone            
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     *
     * @param string $contactNumber            
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;
    }

    /**
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     *
     * @param string $externalId            
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $userType            
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    /**
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     *
     * @param string $role            
     */
    public function setRole($role)
    {
        $this->role = $role;
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
     * @param Object $permissions            
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

    /**
     *
     * @param Object $institutions            
     */
    public function setInstitutions($institutions)
    {
        $this->institutions = $institutions;
    }

    /**
     *
     * @return Object
     */
    public function getInstitutions()
    {
        return $this->institutions;
    }

    /**
     *
     * @param mixed $requestDate            
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;
    }

    /**
     *
     * @return mixed
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     *
     * @param Object $requestedBy            
     */
    public function setRequestedBy($requestedBy)
    {
        $this->requestedBy = $requestedBy;
    }

    /**
     *
     * @return Object
     */
    public function getRequestedBy()
    {
        return $this->requestedBy;
    }

    /**
     *
     * @param mixed $requestedDate            
     */
    public function setRequestedDate($requestedDate)
    {
        $this->requestedDate = $requestedDate;
    }

    /**
     *
     * @param mixed $requestedDate            
     */
    public function getRequestedDate()
    {
        return $this->requestedDate;
    }

    /**
     *
     * @param Object $requestedFrom            
     */
    public function setRequestedFrom($requestedFrom)
    {
        $this->requestedFrom = $requestedFrom;
    }

    /**
     *
     * @return Object
     */
    public function getRequestedFrom()
    {
        return $this->requestedFrom;
    }

    /**
     *
     * @param string $primaryTierName            
     */
    public function setPrimaryTierName($primaryTierName)
    {
        $this->primaryTierName = $primaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getPrimaryTierName()
    {
        return $this->primaryTierName;
    }

    /**
     *
     * @param string $secondaryTierName            
     */
    public function setSecondaryTierName($secondaryTierName)
    {
        $this->secondaryTierName = $secondaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getSecondaryTierName()
    {
        return $this->secondaryTierName;
    }
    
    /**
     *
     * @param string $campusName            
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }

    /**
     *
     * @return string
     */
    public function getCampusName()
    {
        return $this->campusName;
    }
    
    /**
     *
     * @param string $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
    	$this->lastLogin = $lastLogin?$lastLogin:" ";
    }
    
    /**
     *
     * @return string
     */
    public function getLastLogin()
    {
    	return $this->lastLogin;
    }
}