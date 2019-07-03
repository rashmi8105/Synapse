<?php

namespace Synapse\MapworksToolBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Mapworks Tool Analysis
 *
 *
 * @package Synapse\ToolsBundle\EntityDto
 */
class ToolAnalysisDTO
{
    /**
     * Name of the Tool
     *
     * @var string
     * @JMS\Type("string")
     */
    private $toolName;

    /**
     * Short Code that identifies the Tool
     *
     * @var string
     * @JMS\Type("string")
     */
    private $shortCode;

    /**
     * Whether new data has been generated in the background and the UI needs to display a visual indicator.
     *
     * @JMS\Type("boolean")
     */
    private $hasNewDataSinceLastRunDate;

    /**
     * The last run date of the selected tool, by the currently logged-in user.
     *
     * @var \Datetime
     * @JMS\Type("DateTime")
     */
    private $lastRunDate;

    /**
     * The order of the tool appearing in the module.
     *
     * @JMS\Type("integer")
     */
    private $toolOrder;


    /**
     * @return string
     */
    public function getToolName()
    {
        return $this->toolName;
    }

    /**
     * Sets the value of toolName.
     *
     * @param string $toolName the toolName
     */
    public function setToolName($toolName)
    {
        $this->toolName = $toolName;
    }

    /**
     * @return string
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }

    /**
     * Sets the value of shortCode.
     *
     * @param string $shortCode the shortCode
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
    }

    /**
     * @return boolean
     */
    public function getHasNewDataSinceLastRunDate()
    {
        return $this->hasNewDataSinceLastRunDate;
    }

    /**
     * @param boolean $hasNewDataSinceLastRunDate
     */
    public function setHasNewDataSinceLastRunDate($hasNewDataSinceLastRunDate)
    {
        $this->hasNewDataSinceLastRunDate = $hasNewDataSinceLastRunDate;
    }

    /**
     * @return \DateTime
     */
    public function getLastRunDate()
    {
        return $this->lastRunDate;
    }

    /**
     * @param \DateTime $lastRunDate
     */
    public function setLastRunDate($lastRunDate)
    {
        $this->lastRunDate = $lastRunDate;
    }

    /**
     * @return integer
     */
    public function getToolOrder()
    {
        return $this->toolOrder;
    }

    /**
     * Sets the value of toolOrder.
     *
     * @param integer $toolOrder the toolOrder
     */
    public function setToolOrder($toolOrder)
    {
        $this->toolOrder = $toolOrder;
    }

}