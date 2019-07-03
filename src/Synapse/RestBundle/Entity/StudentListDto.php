<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class StudentListDto
{
    
    /**
     * synapseId
     *
     * @var integer @JMS\Type("integer")
     */
    private $synapseId;

    /**
     * externalId
     *
     * @var string @JMS\Type("string")
     */
    private $externalId;

    /**
     * cohortId
     *
     * @var string @JMS\Type("string")
     */
    private $cohortId;

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * receive_survey
     *
     * @var string @JMS\Type("string")
     */
    private $receiveSurvey;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     */
    private $firstName;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     */
    private $lastName;

    /**
     * url
     *
     * @var string @JMS\Type("string")
     */
    private $url;

    /**
     * studentAuthMode
     *
     * @var string @JMS\Type("string")
     */
    private $studentAuthMode;

    /**
     *
     * @return int
     */
    public function getSynapseId()
    {
        return $this->synapseId;
    }

    /**
     *
     * @param string $synapseId            
     */
    public function setSynapseId($synapseId)
    {
        $this->synapseId = $synapseId;
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
     * @param string $externalId            
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     *
     * @return int
     */
    public function getCohortId()
    {
        return $this->cohortId;
    }

    /**
     *
     * @param string $cohortId            
     */
    public function setCohortId($cohortId = "")
    {
        $this->cohortId = $cohortId;
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
     * @param string $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getReceiveSurvey()
    {
        return $this->receiveSurvey;
    }

    /**
     *
     * @param string $receiveSurvey            
     */
    public function setReceiveSurvey($receiveSurvey = "")
    {
        $this->receiveSurvey = $receiveSurvey;
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
     * @param string $email
     */

    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @param string $firstName            
     */
    public function setFirstName($firstName = "")
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
    public function setLastName($lastName = "")
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
     * @param string $url            
     */
    public function setUrl($url = "")
    {
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param string $studentAuthMode            
     */
    public function setStudentAuthMode($studentAuthMode = "")
    {
        $this->studentAuthMode = $studentAuthMode;
    }

    /**
     *
     * @return string
     */
    public function getStudentAuthMode()
    {
        return $this->studentAuthMode;
    }
}