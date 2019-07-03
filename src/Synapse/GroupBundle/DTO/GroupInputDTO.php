<?php
namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;


/**
 * Class GroupInputDto
 *
 * @package Synapse\GroupBundle\DTO
 */
Class GroupInputDTO
{

    /**
     * List of Groups
     *
     * @var OrgGroupDTO[]
     * @JMS\Type("array<Synapse\GroupBundle\DTO\OrgGroupDTO>")
     */
    private $orgGroups;

    /**
     * @return OrgGroupDTO[]
     */
    public function getGroupList()
    {
        return $this->orgGroups;
    }

    /**
     * @param OrgGroupDTO[] $orgGroups
     */
    public function setGroupList($orgGroups)
    {
        $this->orgGroups = $orgGroups;
    }



}