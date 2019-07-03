<?php
namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class OrgGroupDTO
 *
 * @package Synapse\GroupBundle\DTO
 */
Class OrgGroupDTO
{

    /**
     * Id of the Group at the organization
     *
     * @var string
     * @JMS\Type("string")
     */
    private $externalId;


    /**
     * Mapworks internal id for the group
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $mapworksInternalId;


    /**
     * Name of the Group
     *
     * @var string
     * @JMS\Type("string")
     */
    private $groupName;

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return integer
     */
    public function getMapworksInternalId()
    {
        return $this->mapworksInternalId;
    }

    /**
     * @param integer $mapworksInternalId
     */
    public function setMapworksInternalId($mapworksInternalId)
    {
        $this->mapworksInternalId = $mapworksInternalId;
    }


    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }


}