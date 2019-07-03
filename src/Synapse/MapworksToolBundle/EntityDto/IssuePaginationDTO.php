<?php
namespace Synapse\MapworksToolBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\DTO\PaginatedSearchResultDTO;

/**
 * Class IssuePaginationDTO
 *
 * @package Synapse\MapworksToolBundle\EntityDto
 */
class IssuePaginationDTO extends PaginatedSearchResultDTO
{
    /**
     * Determines if a given issue of the set is returned/displayed
     *
     * @var bool
     * @JMS\Type("boolean")
     */
    private $displayStudents;

    /**
     * tracks the issue from the previous API call
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $issueId;

    /**
     * Column to sort by (student_last_name|student_risk_status|student_intent_to_leave|student_classlevel|student_logins|last_activity) and direction to sort (desc is indicated by '-' preceding column name, asc is indicated by '+' or just the column name)
     *
     * @var string
     * @JMS\Type("string")
     */
    private $sortBy;

    /**
     * Top Issue Number - related to Issue Primacy in Calculation
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $topIssue;

    /**
     * Tracks total participants from previous API call
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $participantCountWithIssue;

    /**
     * @return bool
     */
    public function getDisplayStudents()
    {
        return $this->displayStudents;
    }

    /**
     * @param bool $displayStudents
     */
    public function setDisplayStudents($displayStudents)
    {
        $this->displayStudents = $displayStudents;
    }

    /**
     * @return int
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * @param int $issueId
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param string $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return int
     */
    public function getTopIssue()
    {
        return $this->topIssue;
    }

    /**
     * @param int $topIssue
     */
    public function setTopIssue($topIssue)
    {
        $this->topIssue = $topIssue;
    }

    /**
     * @return int
     */
    public function getParticipantCountWithIssue()
    {
        return $this->participantCountWithIssue;
    }

    /**
     * @param int $participantCountWithIssue
     */
    public function setParticipantCountWithIssue($participantCountWithIssue)
    {
        $this->participantCountWithIssue = $participantCountWithIssue;
    }

}
