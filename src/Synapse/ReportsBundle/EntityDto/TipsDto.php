<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\ReportsBundle\EntityDto
 */
class TipsDto
{

    /**
     * id of the report that a tip belongs to
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * id of the section that a tip belongs to
     * 
     * @var integer @JMS\Type("integer")
     */
    private $sectionId;
	
	/**
     * name of a tips section
     *
     * @var string @JMS\Type("string")     
     */
    private $sectionName;
    
    /**
     * id of a tip
     * 
     * @var integer @JMS\Type("integer")
     */
    private $tipId;     
    
    /**
     * name of a tip
     *
     * @var string @JMS\Type("string")
     */
    private $tipsName; 
	
	/**
     * name of a tip
     *
     * @var string @JMS\Type("string")    
     */
    private $tipName;
    
    /**
     * text describing a tip
     *
     * @var string @JMS\Type("string")
     */
    private $tipsDescription; 
	
	/**
     * text describing a tip
     *
     * @var string @JMS\Type("string")
     */
    private $tipText; 
    
    /**
     * integer value representing the order that a tip resides in a group of tips, i.e. 4 = 4th in line
     * 
     * @var integer @JMS\Type("integer")
     */
    private $tipOrder;

    /**
     * @return int
     */
    public function getReportId()
    {
        return $this->reportId;
    }

    /**
     * @param int $reportId
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * @return int
     */
    public function getTipId()
    {
        return $this->tipId;
    }

    /**
     * @param int $tipId
     */
    public function setTipId($tipId)
    {
        $this->tipId = $tipId;
    }

    /**
     * @return string
     */
    public function getTipsName()
    {
        return $this->tipsName;
    }

    /**
     * @param string $tipsName
     */
    public function setTipsName($tipsName)
    {
        $this->tipsName = $tipsName;
    }

    /**
     * @return string
     */
    public function getTipName()
    {
        return $this->tipName;
    }

    /**
     * @param string $tipName
     */
    public function setTipName($tipName)
    {
        $this->tipName = $tipName;
    }

    /**
     * @return string
     */
    public function getTipsDescription()
    {
        return $this->tipsDescription;
    }

    /**
     * @param string $tipsDescription
     */
    public function setTipsDescription($tipsDescription)
    {
        $this->tipsDescription = $tipsDescription;
    }

    /**
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * @param int $sectionId
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     * @return string
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }

    /**
     * @param string $sectionName
     */
    public function setSectionName($sectionName)
    {
        $this->sectionName = $sectionName;
    }

    /**
     * @return string
     */
    public function getTipText()
    {
        return $this->tipText;
    }

    /**
     * @param string $tipText
     */
    public function setTipText($tipText)
    {
        $this->tipText = $tipText;
    }

    /**
     * @return int
     */
    public function getTipOrder()
    {
        return $this->tipOrder;
    }

    /**
     * @param int $tipOrder
     */
    public function setTipOrder($tipOrder)
    {
        $this->tipOrder = $tipOrder;
    }
}