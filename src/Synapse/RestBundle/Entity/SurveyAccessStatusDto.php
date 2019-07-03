<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class SurveyAccessStatusDto
{

    /**
     * Student's ID
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $student;

    /**
     * Survey number
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $survey;

    /**
     * Year
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $year;

    /**
     * Cohort number
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $cohort;

    /**
     * Returns the cohort number
     *
     * @return integer
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * Sets the cohorts number
     *
     * @param integer $cohort
     */
    public function setCohort($cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * Returns the student ID
     *
     * @return integer
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Sets the student ID
     *
     * @param integer $student
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * Returns the survey number
     *
     * @return integer
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Sets the survey number
     *
     * @param integer $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * Returns the year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Sets the year
     *
     * @param integer $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }
}