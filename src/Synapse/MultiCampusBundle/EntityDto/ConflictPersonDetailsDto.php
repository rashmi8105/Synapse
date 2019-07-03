<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class ConflictPersonDetailsDto
{

    /**
     * person_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * merge_type
     *
     * @var string @JMS\Type("string")
     */
    private $mergeType;

    /**
     * conflictId
     *
     * @var integer @JMS\Type("integer")
     */
    private $conflictId;

    /**
     * isMaster
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $isMaster;

    /**
     * isHome
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $isHome;

    /**
     * multicampusUser
     *
     * @var boolean @JMS\Type("boolean")
     *     
     *     
     */
    private $multicampusUser;

    /**
     * orgId
     *
     * @var integer @JMS\Type("integer")
     */
    private $orgId;

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
     * @param boolean $resolveType            
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
     * @param boolean $resolveType            
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
     * @param boolean $resolveType            
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