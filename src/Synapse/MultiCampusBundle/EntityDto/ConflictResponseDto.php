<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class ConflictResponseDto
{

    /**
     * conflictId
     *
     * @var integer @JMS\Type("integer")
     */
    private $countConflicts;

    /**
     * conflictsDate
     *
     * @JMS\Type("DateTime")
     */
    private $conflictsDate;

    /**
     * Campus Id
     *
     * @var string @JMS\Type("string")
     */
    private $campusId;

    /**
     *
     * @var array @JMS\Type("array")
     *     
     *     
     */
    private $sourceOrg;

    /**
     *
     * @var array @JMS\Type("array")
     *     
     *     
     */
    private $destinationOrg;

    /**
     * campusName
     *
     * @var string @JMS\Type("string")
     */
    private $campusName;

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
     * createdOn
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $createdOn;

    /**
     * external Id
     *
     * @var string @JMS\Type("string")
     */
    private $externalId;

    /**
     * resolve_type
     *
     * @var string @JMS\Type("string")
     */
    private $resolveType;

    /**
     * is_by_passed
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isHierarchy;

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
     * @var Object @JMS\Type("array<Synapse\MultiCampusBundle\EntityDto\StudentsDto>")
     *     
     *     
     */
    private $hybrid;

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
    public function setSourceOrg($sourceOrg)
    {
        $this->sourceOrg = $sourceOrg;
    }

    /**
     *
     * @return int
     */
    public function getSourceOrg()
    {
        return $this->sourceOrg;
    }

    /**
     *
     * @return string
     */
    public function setDestinationOrg($destinationOrg)
    {
        $this->destinationOrg = $destinationOrg;
    }

    /**
     *
     * @return object
     */
    public function getDestinationOrg()
    {
        return $this->destinationOrg;
    }

    /**
     *
     * @param mixed $createdOn            
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $createdOn            
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @param mixed $conflictsDate            
     */
    public function setConflictsDate($conflictsDate)
    {
        $this->conflictsDate = $conflictsDate;
    }

    /**
     *
     * @return mixed
     */
    public function getConflictsDate()
    {
        return $this->conflictsDate;
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
     * @param string $countConflicts            
     */
    public function setCountConflicts($countConflicts)
    {
        $this->countConflicts = $countConflicts;
    }

    /**
     *
     * @return string
     */
    public function getCountConflicts()
    {
        return $this->countConflicts;
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
     * @param array $hybrid            
     */
    public function setHybrid($hybrid)
    {
        $this->hybrid = $hybrid;
    }

    /**
     *
     * @return array
     */
    public function getHybrid()
    {
        return $this->hybrid;
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
} 