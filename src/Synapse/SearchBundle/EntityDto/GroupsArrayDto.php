<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class GroupsArrayDto
{

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SearchBundle\EntityDto\GroupsDto>")
     *     
     *     
     */
    private $groups;

    /**
     *
     * @return string
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     *
     * @param mixed $groups            
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }
}