<?php

namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GroupFacultyDTO
 *
 * @package Synapse\GroupBundle\DTO
 */
class GroupFacultyListInputDTO
{
    /**
     * List of faculty within the group.
     *
     * @var GroupFacultyInputDTO[]
     * @JMS\Type("array<Synapse\GroupBundle\DTO\GroupFacultyInputDTO>")
     */
    private $groupFacultyList;

    /**
     * @return GroupFacultyInputDTO[]
     */
    public function getGroupFacultyList()
    {
        return $this->groupFacultyList;
    }

    /**
     * @param GroupFacultyInputDTO[] $groupFacultyList
     */
    public function setGroupFacultyList($groupFacultyList)
    {
        $this->groupFacultyList = $groupFacultyList;
    }
}