<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Data Transfer Object for ReferralsInterestedParties
 * *
 * 
 * @package Synapse\RestBundle\Entity
 */
class ReferralsInterestedPartiesDTO
{

    /**
     * [$organizationId description]
     * 
     * @var [type] @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * [$studentId description]
     * 
     * @var [type] @JMS\Type("integer")
     */
    private $studentId;

    /**
     * [$staffId description]
     * 
     * @var [type] @JMS\Type("integer")
     */
    private $staffId;

    /**
     * [$interestedParties description]
     * 
     * @var [type] @JMS\Type("array")
     */
    private $interestedParties;

    public function __construct()
    {
        $this->interestedParties = new ArrayCollection();
    }

    /**
     * Sets the value of organizationId.
     *
     * @param mixed $organizationId
     *            the organization id
     *            
     * @return self
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
        
        return $this;
    }

    /**
     * Gets the value of organizationId.
     *
     *
     * @return $organizationId
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Gets the value of studentId.
     *
     * @return mixed
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Sets the value of studentId.
     *
     * @param mixed $personStudentId
     *            the person student id
     *            
     * @return self
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
        
        return $this;
    }

    /**
     * Gets the value of personStaffId.
     *
     * @return mixed
     */
    public function getStaffId()
    {
        return $this->staffId;
    }

    /**
     * Sets the value of staffId.
     *
     * @param mixed $staffId
     *            the person staff id
     *            
     * @return self
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;
        
        return $this;
    }

    /**
     * Gets the value of interestedParties.
     *
     * @return mixed
     */
    public function getInterestedParties()
    {
        return $this->interestedParties;
    }

    /**
     * Sets the value of interestedParties.
     *
     * @param mixed $interestedParties
     *            the interested parties
     *            
     * @return self
     */
    public function addInterestedParties($interestedParties,$key=null)
    {
        if($key){
           $this->interestedParties[$key] = $interestedParties;
        }
        else{
            $this->interestedParties[] = $interestedParties;
          }
        return $this;
    }
}