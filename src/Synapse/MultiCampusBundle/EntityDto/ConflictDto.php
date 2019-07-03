<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 * This table is empty and there are no documented uses
 * @deprecated
 */
class ConflictDto
{

    /**
     * Conflict Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $conflictId;

    /**
     * Person Firstname
     *
     * @var string @JMS\Type("string")
     */
    private $firstname;

    /**
     * Person Lastname
     *
     * @var string @JMS\Type("string")
     */
    private $lastname;

    /**
     * Campus Id
     *
     * @var string @JMS\Type("string")
     */
    private $campusId;

    /**
     * resolve_type
     *
     * @var string @JMS\Type("string")
     */
    private $resolveType;

    /**
     * Email id
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * Source Organization Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $sourceOrgId;

    /**
     * Destination Organization Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $destinationOrgId;

    /**
     * Source Organization Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $orgId;

    /**
     * Source Person Id
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * createdOn
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $createdOn;

    /**
     * createdDate
     *
     * @JMS\Type("DateTime")
     */
    private $createdDate;

    /**
     * external Id
     *
     * @var string @JMS\Type("string")
     */
    private $externalId;

    /**
     * merge_type
     *
     * @var string @JMS\Type("string")
     */
    private $mergeType;

    /**
     * autoResolveId
     *
     * @var integer @JMS\Type("string")
     */
    private $autoResolveId;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\UserConflictsDto>")
     *     
     *     
     */
    private $userConflicts;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\StudentsDto>")
     *     
     *     
     */
    private $students;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\StaffDto>")
     *     
     *     
     */
    private $staff;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\OthersDto>")
     *     
     *     
     */
    private $others;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\ConflictResponseDto>")
     *     
     *     
     */
    private $conflicts;

    /**
     * type
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * isHome
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $isHome;

    /**
     * isHierarchy
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $isHierarchy;

    /**
     * isMaster
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $isMaster;

    /**
     * role
     *
     * @var string @JMS\Type("string")
     *     
     *     
     */
    private $role;

    /**
     * multicampusUser
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $multicampusUser;

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
     * @param string $firstname            
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     *
     * @param string $lastname            
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
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
     * @param int $autoResolveId            
     */
    public function setAutoResolveId($autoResolveId)
    {
        $this->autoResolveId = $autoResolveId;
    }

    /**
     *
     * @return int
     */
    public function getAutoResolveId()
    {
        return $this->autoResolveId;
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
     * @param int $sourceOrgId            
     */
    public function setSourceOrgId($sourceOrgId)
    {
        $this->sourceOrgId = $sourceOrgId;
    }

    /**
     *
     * @return int
     */
    public function getSourceOrgId()
    {
        return $this->sourceOrgId;
    }

    /**
     *
     * @param int $destinationOrgId            
     */
    public function setDestinationOrgId($destinationOrgId)
    {
        $this->destinationOrgId = $destinationOrgId;
    }

    /**
     *
     * @return int
     */
    public function getDestinationOrgId()
    {
        return $this->destinationOrgId;
    }

    /**
     *
     * @param date $createdOn            
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @return date
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @param int $conflictId            
     */
    public function setConflictId($conflictId)
    {
        $this->conflictId = $conflictId;
    }

    /**
     *
     * @return int
     */
    public function getConflictId()
    {
        return $this->conflictId;
    }

    /**
     *
     * @param string $resolveType            
     */
    public function setResolveType($resolveType)
    {
        $this->resolveType = $resolveType;
    }

    /**
     *
     * @return string
     */
    public function getResolveType()
    {
        return $this->resolveType;
    }

    /**
     *
     * @param int $conflicts            
     */
    public function setConflicts($conflicts)
    {
        $this->conflicts = $conflicts;
    }

    /**
     *
     * @return int
     */
    public function getConflicts()
    {
        return $this->conflicts;
    }

    /**
     *
     * @param int $orgId            
     */
    public function setOrgId($orgId)
    {
        $this->orgId = $orgId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->orgId;
    }

    /**
     *
     * @param int $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param boolean $isHome            
     */
    public function setIsHome($isHome)
    {
        $this->isHome = $isHome;
    }

    /**
     *
     * @return boolean
     */
    public function getIsHome()
    {
        return $this->isHome;
    }

    /**
     *
     * @param boolean $isHierarchy            
     */
    public function setIsHierarchy($isHierarchy)
    {
        $this->isHierarchy = $isHierarchy;
    }

    /**
     *
     * @return boolean
     */
    public function getIsHierarchy()
    {
        return $this->isHierarchy;
    }

    /**
     *
     * @param boolean $isMaster            
     */
    public function setIsMaster($isMaster)
    {
        $this->isMaster = $isMaster;
    }

    /**
     *
     * @return boolean
     */
    public function getIsMaster()
    {
        return $this->isMaster;
    }

    /**
     *
     * @param mixed $createdDate            
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     *
     * @param array $students            
     */
    public function setStudents($students)
    {
        $this->students = $students;
    }

    /**
     *
     * @return array
     */
    public function getStudents()
    {
        return $this->students;
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
     * @return array
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     *
     * @param array $others            
     */
    public function setOthers($others)
    {
        $this->others = $others;
    }

    /**
     *
     * @return array
     */
    public function getOthers()
    {
        return $this->$others;
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
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param int $type            
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @param array $userConflicts            
     */
    public function setUserConflicts($userConflicts)
    {
        $this->userConflicts = $userConflicts;
    }

    /**
     *
     * @return array
     */
    public function getUserConflicts()
    {
        return $this->userConflicts;
    }

    /**
     *
     * @param boolean $multicampusUser            
     */
    public function setMulticampusUser($multicampusUser)
    {
        $this->multicampusUser = $multicampusUser;
    }

    /**
     *
     * @return boolean
     */
    public function getMulticampusUser()
    {
        return $this->multicampusUser;
    }

    /**
     *
     * @param string $mergeType            
     */
    public function setMergeType($mergeType)
    {
        $this->mergeType = $mergeType;
    }

    /**
     *
     * @return string
     */
    public function getMergeType()
    {
        return $this->mergeType;
    }
} 