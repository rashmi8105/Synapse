<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Synapse\SearchBundle\EntityDto\FilteredStudentListDto;

/**
 * Survey Factors Report Drilldown DTO.
 *
 * @package Synapse\ReportsBundle\EntityDto
 */
class SurveyFactorReportDrilldownDto extends FilteredStudentListDto
{
    /**
     * The text of the factor that the user drilled down on.
     *
     * @var string @JMS\Type("string")
     */
    private $question;

    /**
     * Count of non-participants to which the faculty has individual and aggregate access
     *
     * @var integer @JMS\Type("integer")
     */
    private $individualNonParticipantCount;

    /**
     * Count of non-participants to which the faculty has aggregate only access
     *
     * @var integer @JMS\Type("integer")
     */
    private $aggregateOnlyNonParticipantCount;

    /**
     * Count of participants to which the faculty only has aggregate access
     *
     * @var integer @JMS\Type("integer")
     */
    private $aggregateOnlyParticipantCount;


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


    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }
}