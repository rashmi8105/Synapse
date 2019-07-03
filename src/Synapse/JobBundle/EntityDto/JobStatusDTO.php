<?php

namespace Synapse\JobBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for JobStatusDTO
 *
 * @package Synapse\JobBundle\Entity
 */
class JobStatusDTO
{
    /**
     * Faculty Id
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $personId;

    /**
     * Organization id
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * detail
     *
     * @var string
     * @JMS\Type("string")
     */
    private $jobStatus;

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return string
     */
    public function getJobStatus()
    {
        return $this->jobStatus;
    }

    /**
     * @param string $jobStatus
     */
    public function setJobStatus($jobStatus)
    {
        $this->jobStatus = $jobStatus;
    }
}