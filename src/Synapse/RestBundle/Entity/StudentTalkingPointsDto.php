<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Student talking points
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentTalkingPointsDto
{

    /**
     * personStudentId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $personStudentId;

    /**
     * personStaffId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $personStaffId;

    /**
     * organizationId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * talkingPointsWeaknessCount
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $talkingPointsWeaknessCount;

    /**
     * talkingPointsStrengthsCount
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $talkingPointsStrengthsCount;

    /**
     * weakness
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\TalkingPointsDto>")
     */
    private $weakness;

    /**
     * strength
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\TalkingPointsDto>")
     */
    private $strength;

    /**
     *
     * @param integer $personStudentId            
     */
    public function setPersonStudentId($personStudentId)
    {
        $this->personStudentId = $personStudentId;
    }

    /**
     *
     * @return int
     */
    public function getPersonStudentId()
    {
        return $this->personStudentId;
    }

    /**
     *
     * @param integer $personStaffId            
     */
    public function setPersonStaffId($personStaffId)
    {
        $this->personStaffId = $personStaffId;
    }

    /**
     *
     * @return int
     */
    public function getPersonStaffId()
    {
        return $this->personStaffId;
    }

    /**
     *
     * @param integer $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param integer $talkingPointsWeaknessCount            
     */
    public function setTalkingPointsWeaknessCount($talkingPointsWeaknessCount)
    {
        $this->talkingPointsWeaknessCount = $talkingPointsWeaknessCount;
    }

    /**
     *
     * @return int
     */
    public function getTalkingPointsWeaknessCount()
    {
        return $this->talkingPointsWeaknessCount;
    }

    /**
     *
     * @param integer $talkingPointsStrengthsCount            
     */
    public function setTalkingPointsStrengthsCount($talkingPointsStrengthsCount)
    {
        $this->talkingPointsStrengthsCount = $talkingPointsStrengthsCount;
    }

    /**
     *
     * @return int
     */
    public function getTalkingPointsStrengthsCount()
    {
        return $this->talkingPointsStrengthsCount;
    }

    /**
     *
     * @param object $weakness            
     */
    public function setWeakness($weakness)
    {
        $this->weakness = $weakness;
    }

    /**
     *
     * @return object
     */
    public function getWeakness()
    {
        return $this->weakness;
    }

    /**
     *
     * @param object $strength            
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
    }

    /**
     *
     * @return object
     */
    public function getStrength()
    {
        return $this->strength;
    }
}