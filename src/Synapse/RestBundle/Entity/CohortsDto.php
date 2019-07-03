<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Cohorts
 *
 * @package Synapse\RestBundle\Entity
 */
class CohortsDto
{

    /**
     * @JMS\Type("DateTime")
     * 
     * @var DateTime
     */
    private $startDate;

    /**
     * @JMS\Type("DateTime")
     * 
     * @var DateTime
     */
    private $endDate;

    /**
     * cohort_name
     * 
     * @var string @JMS\Type("string")
     */
    private $cohortName;

    /**
     * total_students
     * 
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;

    /**
     * responded
     * 
     * @var integer @JMS\Type("integer")
     */
    private $responded;

    /**
     * not_responded
     * 
     * @var integer @JMS\Type("integer")
     */
    private $notResponded;

    /**
     * percentage
     * 
     * @var integer @JMS\Type("integer")
     */
    private $percentage;

    /**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $date            
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     *
     * @return \Synapse\RestBundle\Entity\datetime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $date            
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     *
     * @return \Synapse\RestBundle\Entity\datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
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
     * @param int $responded            
     */
    public function setResponded($responded)
    {
        $this->responded = $responded;
    }

    /**
     *
     * @return int
     */
    public function getResponded()
    {
        return $this->responded;
    }

    /**
     *
     * @param int $notResponded            
     */
    public function setNotResponded($notResponded)
    {
        $this->notResponded = $notResponded;
    }

    /**
     *
     * @return int
     */
    public function getNotResponded()
    {
        return $this->notResponded;
    }

    /**
     *
     * @param int $percentage            
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     *
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }
}