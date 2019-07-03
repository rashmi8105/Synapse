<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class GroupsResponseDto
{

    /**
     * total_group_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalGroupCount;

    /**
     * organization_id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SearchBundle\EntityDto\GroupsDto>")
     *     
     *     
     */
    private $groupDetails;

    /**
     *
     * @param int $totalGroupCount            
     */
    public function setTotalGroupCount($totalGroupCount)
    {
        $this->totalGroupCount = $totalGroupCount;
    }

    /**
     *
     * @return int
     */
    public function getTotalGroupCount()
    {
        return $this->totalGroupCount;
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
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param mixed $groupDetails
     */
    public function setGroupDetails($groupDetails)
    {
        $this->groupDetails = $groupDetails;
    }
    
    /**
     *
     * @return string
     */
    public function getGroupDetails()
    {
        return $this->groupDetails;
    }


}