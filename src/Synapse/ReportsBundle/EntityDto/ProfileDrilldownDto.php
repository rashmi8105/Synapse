<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Synapse\SearchBundle\EntityDto\FilteredStudentListDto;
use JMS\Serializer\Annotation\AccessorOrder;

/**
 * Class ProfileDrilldownDto
 * @package Synapse\ReportsBundle\EntityDto
 * @AccessorOrder("custom", custom = {"listTitle", "profileItemText", "personId", "totalRecords", "totalPages", "recordsPerPage", "currentPage", "individualNonParticipantCount", "aggregateOnlyNonParticipantCount", "aggregateOnlyParticipantCount", "searchResult", "searchAttributes"})
 */
class ProfileDrilldownDto extends FilteredStudentListDto
{
    /**
     * @var string @JMS\Type("string")
     */
    private $profileItemText;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $individualNonParticipantCount;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $aggregateOnlyNonParticipantCount;

    /**
     * @var integer @JMS\Type("integer")
     */
    private $aggregateOnlyParticipantCount;

    /**
     * @return string
     */
    public function getProfileItemText()
    {
        return $this->profileItemText;
    }

    /**
     * @param string $profileItemText
     */
    public function setProfileItemText($profileItemText)
    {
        $this->profileItemText = $profileItemText;
    }

    /**
     * @return int
     */
    public function getIndividualNonParticipantCount()
    {
        return $this->individualNonParticipantCount;
    }

    /**
     * @param int $individualNonParticipantCount
     */
    public function setIndividualNonParticipantCount($individualNonParticipantCount)
    {
        $this->individualNonParticipantCount = $individualNonParticipantCount;
    }

    /**
     * @return int
     */
    public function getAggregateOnlyNonParticipantCount()
    {
        return $this->aggregateOnlyNonParticipantCount;
    }

    /**
     * @param int $aggregateOnlyNonParticipantCount
     */
    public function setAggregateOnlyNonParticipantCount($aggregateOnlyNonParticipantCount)
    {
        $this->aggregateOnlyNonParticipantCount = $aggregateOnlyNonParticipantCount;
    }

    /**
     * @return int
     */
    public function getAggregateOnlyParticipantCount()
    {
        return $this->aggregateOnlyParticipantCount;
    }

    /**
     * @param int $aggregateOnlyParticipantCount
     */
    public function setAggregateOnlyParticipantCount($aggregateOnlyParticipantCount)
    {
        $this->aggregateOnlyParticipantCount = $aggregateOnlyParticipantCount;
    }

}