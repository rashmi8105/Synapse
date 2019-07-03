<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Faculty
 *
 * @package Synapse\RestBundle\Entity
 */
class FacultyPolicyDto
{
	/**
     * Id of the faculty member that the policy applies to.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $facultyId;
	
	/**
     * Id of the policy's organization.
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;
	

	/**
     * Determines whether a faculty has accepted the organization's privacy policy or not.
     *
     * @var string @JMS\Type("string")
     */
    private $isPrivacyPolicyAccepted;
		
	/**
     * Returns the id of the faculty that a policy applies to.
     *
     * @return int
     */
    public function getFacultyId()
    {
        return $this->facultyId;
    }

    /**
     * Sets the id of the faculty that a policy applies to.
     *
     * @param integer $facultyId
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
    }
	
	/**
     * Returns the id of the faculty's organization.
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the id of the faculty's organization.
     *
     * @param integer $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

	/**
     * Sets whether the faculty has accepted the organizations privacy policy or not.
     *
     * @param string $isPrivacyPolicyAccepted            
     */
    public function setIsPrivacyPolicyAccepted($isPrivacyPolicyAccepted)
    {
        $this->isPrivacyPolicyAccepted = $isPrivacyPolicyAccepted;
    }

    /**
     * Returns whether the faculty has accepted the organizations privacy policy or not.
     *
     * @return string
     */
    public function getIsPrivacyPolicyAccepted()
    {
        return $this->isPrivacyPolicyAccepted;
    }
}