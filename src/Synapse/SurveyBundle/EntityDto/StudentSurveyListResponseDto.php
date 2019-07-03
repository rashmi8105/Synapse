<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student Survey List
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class StudentSurveyListResponseDto
{

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * studentId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $studentId;
    
    /**
     * studentId
     *
     * @var integer @JMS\Type("string")
     *
     */
    private $cohort;
    
    /**
     * studentId
     *
     * @var integer @JMS\Type("string")
     *
     */
    private $year;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\SurveysDetailsDto>")
     *     
     */
    private $surveys;

    /**
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param int $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     *
     * @param Object $surveys            
     */
    public function setSurveys($surveys)
    {
        $this->surveys = $surveys;
    }

    /**
     *
     * @return Object
     */
    public function getSurveys()
    {
        return $this->surveys;
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
    public function getCohort()
    {
        return $this->cohort;
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
    public function getYear()
    {
        return $this->year;
    }

    
}