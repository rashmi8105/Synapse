<?php
namespace Synapse\MapworksToolBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TopIssuesDTO
 *
 * @package Synapse\MapworksToolBundle\EntityDto
 */
class TopIssuesDTO
{

    /**
     * Academic year's name for the given academic year
     *
     * @var string
     * @JMS\Type("string")
     */
    private $academicYearName;

    /**
     * CohortId
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $cohort;

    /**
     * current datetime of the Top Issues Run
     *
     * @var \DateTime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $currentDatetime;

    /**
     * Faculty Firstname
     *
     * @var string
     * @JMS\Type("string")
     */
    private $facultyFirstname;

    /**
     * Mapworks internal person id for faculty
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $facultyId;

    /**
     * Faculty Lastname
     *
     * @var string
     * @JMS\Type("string")
     */
    private $facultyLastname;

    /**
     * Issue Count
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $issueCount;

    /**
     * Tracks whether the student population or issues have changed since the last API call
     *
     * @var bool
     * @JMS\Type("boolean")
     */
    private $studentPopulationChange;

    /**
     * Mapworks internal survey id
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $surveyId;

    /**
     * Survey Name
     *
     * @var string
     * @JMS\Type("string")
     */
    private $surveyName;

    /**
     * Top issues with top level information including percentages and counts for given number of issues Optionally includes a student list
     *
     * @var array
     * @JMS\Type("array")
     */
    private $topIssues;

    /**
     * Count of students who have the given top issues
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $totalStudents;

    /**
     * Mapworks internal year id
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $year;

    /**
     * Non participant count with all top 5 issues
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $totalNonParticipantCount;


    /**
     * @return string
     */
    public function getAcademicYearName()
    {
        return $this->academicYearName;
    }

    /**
     * @param string $academicYearName
     */
    public function setAcademicYearName($academicYearName)
    {
        $this->academicYearName = $academicYearName;
    }

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
     * @return \DateTime
     */
    public function getCurrentDatetime()
    {
        return $this->currentDatetime;
    }

    /**
     * @param \Date $currentDatetime
     */
    public function setCurrentDatetime($currentDatetime)
    {
        $this->currentDatetime = $currentDatetime;
    }

    /**
     * @return string
     */
    public function getFacultyFirstname()
    {
        return $this->facultyFirstname;
    }

    /**
     * @param string $facultyFirstname
     */
    public function setFacultyFirstname($facultyFirstname)
    {
        $this->facultyFirstname = $facultyFirstname;
    }

    /**
     * @return int
     */
    public function getFacultyId()
    {
        return $this->facultyId;
    }

    /**
     * @param int $facultyId
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
    }

    /**
     * @return string
     */
    public function getFacultyLastname()
    {
        return $this->facultyLastname;
    }

    /**
     * @param string $facultyLastname
     */
    public function setFacultyLastname($facultyLastname)
    {
        $this->facultyLastname = $facultyLastname;
    }

    /**
     * @return int
     */
    public function getIssueCount()
    {
        return $this->issueCount;
    }

    /**
     * @param int $issueCount
     */
    public function setIssueCount($issueCount)
    {
        $this->issueCount = $issueCount;
    }

    /**
     * @return bool
     */
    public function getStudentPopulationChange()
    {
        return $this->studentPopulationChange;
    }

    /**
     * @param bool $studentPopulationChange
     */
    public function setStudentPopulationChange($studentPopulationChange)
    {
        $this->studentPopulationChange = $studentPopulationChange;
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
     * @return string
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     * @param string $surveyName
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = $surveyName;
    }

    /**
     * @return array
     */
    public function getTopIssues()
    {
        return $this->topIssues;
    }

    /**
     * @param array $topIssues
     */
    public function setTopIssues($topIssues)
    {
        $this->topIssues = $topIssues;
    }

    /**
     * @return int
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
    }

    /**
     * @param int $totalStudents
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getTotalNonParticipantCount()
    {
        return $this->totalNonParticipantCount;
    }

    /**
     * @param int $totalNonParticipantCount
     */
    public function setTotalNonParticipantCount($totalNonParticipantCount)
    {
        $this->totalNonParticipantCount = $totalNonParticipantCount;
    }

}
