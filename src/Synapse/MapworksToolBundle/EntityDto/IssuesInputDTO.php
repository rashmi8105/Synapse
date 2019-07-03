<?php

namespace Synapse\MapworksToolBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class IssuesInputDTO
 *
 * @package Synapse\MapworksToolBundle\EntityDto
 */
class IssuesInputDTO
{

    /**
     * Cohort id of given topIssue
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $cohort;

    /**
     * number of top issues to generate
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $numberOfTopIssues;

    /**
     * Org Academic Year Id
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $orgAcademicYearId;

    /**
     * Format to generate results in - either 'json' or 'csv'
     *
     * @var string
     * @JMS\Type("string")
     */
    private $outputFormat;

    /**
     * Tracks whether this is the first call to this API in a given session
     * True if this is first time in current session this API is called, used to indicate to backend whether to run checks against totalStudents and issueId
     *
     * @var bool
     * @JMS\Type("boolean")
     */
    private $rootCall;

    /**
     * survey id
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $surveyId;

    /**
     * number of top issues to generate
     *
     * @var IssuePaginationDTO[]
     * @JMS\Type("array<Synapse\MapworksToolBundle\EntityDto\IssuePaginationDTO>")
     */
    private $topIssuesPagination;

    /**
     * Tracks the total student population from the previous API call
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $totalStudentPopulation;

    /**
     * @return int
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * @param int $cohort
     */
    public function setCohort($cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return int
     */
    public function getNumberOfTopIssues()
    {
        return $this->numberOfTopIssues;
    }

    /**
     * @param int $numberOfTopIssues
     */
    public function setNumberOfTopIssues($numberOfTopIssues)
    {
        $this->numberOfTopIssues = $numberOfTopIssues;
    }

    /**
     * @return int
     */
    public function getOrgAcademicYearId()
    {
        return $this->orgAcademicYearId;
    }

    /**
     * @param int $orgAcademicYearId
     */
    public function setOrgAcademicYearId($orgAcademicYearId)
    {
        $this->orgAcademicYearId = $orgAcademicYearId;
    }

    /**
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * @param string $outputFormat
     */
    public function setOutputFormat($outputFormat)
    {
        $this->outputFormat = $outputFormat;
    }

    /**
     * @return bool
     */
    public function getRootCall()
    {
        return $this->rootCall;
    }

    /**
     * @param bool $rootCall
     */
    public function setRootCall($rootCall)
    {
        $this->rootCall = $rootCall;
    }

    /**
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * @param int $surveyId
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     * @return IssuePaginationDTO[]
     */
    public function getTopIssuesPagination()
    {
        return $this->topIssuesPagination;
    }

    /**
     * @param IssuePaginationDTO[] $topIssuesPagination
     */
    public function setTopIssuesPagination($topIssuesPagination)
    {
        $this->topIssuesPagination = $topIssuesPagination;
    }

    /**
     * @return int
     */
    public function getTotalStudentPopulation()
    {
        return $this->totalStudentPopulation;
    }

    /**
     * @param int $totalStudentPopulation
     */
    public function setTotalStudentPopulation($totalStudentPopulation)
    {
        $this->totalStudentPopulation = $totalStudentPopulation;
    }

}
