<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;


class GroupsDto
{
    /**
     * Determines whether all groups apply to a Request or not.
     *
     * @var boolean
     * @JMS\Type("boolean")
     *
     */
    private $isAll;
    
    /**
     * The string that contains the IDs of groups that apply to a Request.
     *
     * @var string
     * @JMS\Type("string")
     *
     */
    private $selectedGroupIds;

    /**
     * Set whether all groups apply to a Request or not.
     *
     * @param boolean $isAll
     */
    public function setIsAll($isAll)
    {
        $this->isAll = $isAll;
    }

    /**
     * Returns whether all groups apply to a Request or not.
     *
     * @return boolean
     */
    public function getIsAll()
    {
        return $this->isAll;
    }

    /**
     * Set the Ids of groups that apply to a Request.
     *
     * @param string $selectedGroupIds
     */
    public function setSelectedGroupIds($selectedGroupIds)
    {
        $this->selectedGroupIds = $selectedGroupIds;
    }

    /**
     * Returns the IDs of groups that apply to a Request.
     *
     * @return string
     */
    public function getSelectedGroupIds()
    {
        return $this->selectedGroupIds;
    }


}