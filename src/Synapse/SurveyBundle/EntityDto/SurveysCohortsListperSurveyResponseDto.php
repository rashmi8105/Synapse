<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object Cohorts list per Survey
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveysCohortsListperSurveyResponseDto
{
    /**
     * cohortId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $cohortId;
    
    /**
     * cohortName
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $cohortName;

    /**
     * totalStudents
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalStudents;

    /**
     * totalStudentsResponded
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalStudentsResponded;

    /**
     * totalStudentsPercentageResponded
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $totalStudentsPercentageResponded;
  
    /**
     *
     * @param int $cohortId
     */
    public function setCohortId($cohortId)
    {
        $this->cohortId = $cohortId;
    }
    
    /**
     *
     * @return int
     */
    public function getCohortId()
    {
        return $this->cohortId;
    }

    /**
     *
     * @param string $cohortName            
     */
    public function setCohortName($cohortName)
    {
        $this->cohortName = $cohortName;
    }

    /**
     *
    * @return string
     */
    public function getCohortName()
    {
        return $this->cohortName;
    }
    
    /**
     *
     * @param int $totalStudents
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }
    
    /**
     *
     * @return int
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
    }
    
    /**
     *
     * @param int $totalStudentsResponded
     */
    public function setTotalStudentsResponded($totalStudentsResponded)
    {
        $this->totalStudentsResponded = $totalStudentsResponded;
    }
    
    /**
     *
     * @return int
     */
    public function getTotalStudentsResponded()
    {
        return $this->totalStudentsResponded;
    }
    
    /**
     *
     * @param int $totalStudentsPercentageResponded
     */
    public function setTotalStudentsPercentageResponded($totalStudentsPercentageResponded)
    {
        $this->totalStudentsPercentageResponded = $totalStudentsPercentageResponded;
    }
    
    /**
     *
     * @return int
     */
    public function getTotalStudentsPercentageResponded()
    {
        return $this->totalStudentsPercentageResponded;
    }  

}