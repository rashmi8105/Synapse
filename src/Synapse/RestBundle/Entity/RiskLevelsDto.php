<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for RiskLevels
 *
 * @package Synapse\RestBundle\Entity
 */
class RiskLevelsDto
{

    /**
     * Name of a risk level.
     * 
     * @var string @JMS\Type("string")
     */
    private $riskLevel;

    /**
     * Total number of students that have a certain risk level.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;

    /**
     * Percentage of total students that have a certain risk level.
     * 
     * @var string @JMS\Type("string")
     */
    private $riskPercentage;

    /**
     * Color value of a risk level.
     * 
     * @var string @JMS\Type("string")
     */
    private $colorValue;

    /**
     * Sets the name of a risk level.
     *
     * @param string $riskLevel            
     */
    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    /**
     * Gets the name of a risk level.
     *
     * @return string
     */
    public function getRiskLevel()
    {
        return $this->riskLevel;
    }

    /**
     * Sets the total number of students at a certain risk level.
     *
     * @param integer $totalStudents            
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }

    /**
     * Gets the total number of students at a certain risk level.
     *
     * @return integer
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
    }

    /**
     * Sets the percentage of total students that have a certain risk level.
     *
     * @param string $riskPercentage            
     */
    public function setRiskPercentage($riskPercentage)
    {
        $this->riskPercentage = $riskPercentage;
    }

    /**
     * Gets the percentage of total students that have a certain risk level.
     *
     * @return string
     */
    public function getRiskPercentage()
    {
        return $this->riskPercentage;
    }

    /**
     * Sets the color value of a risk level.
     *
     * @param string $colorValue            
     */
    public function setColorValue($colorValue)
    {
        $this->colorValue = ($colorValue)?$colorValue:'';
    }

    /**
     * Gets the color value of a risk level.
     *
     * @return string
     */
    public function getColorValue()
    {
        return $this->colorValue;
    }
}