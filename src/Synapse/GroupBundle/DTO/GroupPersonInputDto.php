<?php

namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;

class GroupPersonInputDto {

    /**
     * Array Of associative array with group_external_id and  person_external_id
     *
     * @var array
     * @JMS\Type("array")
     */
    private $groupPersonList;

    /**
     * @return array
     */
    public function getGroupPersonList()
    {
        return $this->groupPersonList;
    }

    /**
     * @param array $groupPersonList
     */
    public function setGroupPersonList($groupPersonList)
    {
        $this->groupPersonList = $groupPersonList;
    }

}