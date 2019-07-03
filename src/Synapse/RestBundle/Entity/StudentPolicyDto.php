<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentPolicyDto
{
	/**
     * Student ID
     * 
     * @var integer @JMS\Type("integer")
     */
    private $studentId;
	
	/**
     * Organization ID
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;
	
	/**
     * Privacy policy
     *
     * @var string @JMS\Type("string")
     */
    private $privacyPolicy;
	
	/**
     * Privacy policy accepted can hold either a y or n value.
     *
     * @var string @JMS\Type("string")
     */
    private $isPrivacyPolicyAccepted;
		
	/**
     * Returns the student ID
     *
     * @return integer
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Sets the student ID
     *
     * @param integer $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }
	
	/**
     * Returns the organization ID
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the organization ID
     *
     * @param integer $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the privacy policy
     *
     * @return string
     */
    public function getPrivacyPolicy()
    {
        return $this->privacyPolicy;
    }

	/**
     * Sets the privacy policy
     *
     * @param string $privacyPolicy            
     */
    public function setPrivacyPolicy($privacyPolicy)
    {
        $this->privacyPolicy = $privacyPolicy;
    }

    /**
     * Returns the privacy policy y or n value.
     *
     * @return string
     */
    public function getIsPrivacyPolicyAccepted()
    {
        return $this->isPrivacyPolicyAccepted;
    }
	/**
     * Sets the privacy policy y or n value.
     *
     * @param string $isPrivacyPolicyAccepted            
     */
    public function setIsPrivacyPolicyAccepted($isPrivacyPolicyAccepted)
    {
        $this->isPrivacyPolicyAccepted = $isPrivacyPolicyAccepted;
    }
}