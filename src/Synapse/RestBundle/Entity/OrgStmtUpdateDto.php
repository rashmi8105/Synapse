<?php
namespace Synapse\RestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

class OrgStmtUpdateDto
{

    /**
     * Id of the organization.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Organization's custom confidentiality statement.
     * 
     * @var string @JMS\Type("string")
     */
    private $customConfidentialityStatement;

    /**
     * Sets an organization's custom confidentiality statement.
     *
     * @param string $customConfidentialityStatement            
     */
    public function setCustomConfidentialityStatement($customConfidentialityStatement)
    {
        $this->customConfidentialityStatement = $customConfidentialityStatement;
    }

    /**
     * Returns an organization's custom confidentiality statement.
     *
     * @return string
     */
    public function getCustomConfidentialityStatement()
    {
        return $this->customConfidentialityStatement;
    }

    /**
     * Sets the id of an organization.
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Gets the id of an organization.
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }
}