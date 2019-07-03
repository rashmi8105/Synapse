<?php
namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\DTO\PaginatedSearchResultDTO;


/**
 * Class GroupListDto
 *
 * @package Synapse\GroupBundle\DTO
 */
Class GroupListDto extends PaginatedSearchResultDTO
{


    /**
     * List of groups for that organization
     *
     * @var array
     * @JMS\Type("array")
     */
    private $groupList;

    /**
     * @return mixed
     */
    public function getGroupList()
    {
        return $this->groupList;
    }

    /**
     * @param mixed $groupList
     */
    public function setGroupList($groupList)
    {
        $this->groupList = $groupList;
    }
}