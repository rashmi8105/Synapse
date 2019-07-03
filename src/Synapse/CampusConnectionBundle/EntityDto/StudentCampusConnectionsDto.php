<?php
namespace Synapse\CampusConnectionBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentCampusConnectionsDto
{

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * campusId
     *
     * @var string @JMS\Type("string")
     */
    private $campusId;

    /**
     * campusName
     *
     * @var string @JMS\Type("string")
     */
    private $campusName;

    /**
     * @var CampusConnectionsArrayDto[] @JMS\Type("array<Synapse\CampusConnectionBundle\EntityDto\CampusConnectionsArrayDto>")
     */
    private $campusConnections;

    /**
     * @param CampusConnectionsArrayDto[] $campusConnections
     */
    public function setCampusConnections($campusConnections)
    {
        $this->campusConnections = $campusConnections;
    }

    /**
     * @return CampusConnectionsArrayDto[]
     */
    public function getCampusConnections()
    {
        return $this->campusConnections;
    }

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     * @return string
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     * @param string $campusName
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }

    /**
     * @return string
     */
    public function getCampusName()
    {
        return $this->campusName;
    }

    /**
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }
}