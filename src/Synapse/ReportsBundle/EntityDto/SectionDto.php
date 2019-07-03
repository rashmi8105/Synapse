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
class SectionDto
{
	/**
     * id of a section
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * id of the report that a section applies to
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * number of elements within a section
     * 
     * @var integer @JMS\Type("integer")
     */
    private $sectionElementsCount;
    
    /**
     * number of tips within a section
     * 
     * @var integer @JMS\Type("integer")
     */
    private $sectionTipsCount;
	
	/**
     * number of tips
     * 
     * @var integer @JMS\Type("integer")
     */
    private $tipsCount;
    
    /**
     * id of a section
     * 
     * @var integer @JMS\Type("integer")
     */
    private $sectionId;
    
    /**
     * name of a section
     *
     * @var string @JMS\Type("string")     
     *
     */
    private $sectionName; 
	
	/**
     * integer value representing the order of a section within a report
     * 
     * @var integer @JMS\Type("integer")
     */
    private $sectionOrder;
	
	/**
     * when editing a section, which direction to move the element to plaec it in its new correct order
     *
     * @var string @JMS\Type("string")     
     */
    private $reorderDirection; 
	
	/**
     * object representation of the elements within a section
     *
     * @var ElementDto[] @JMS\Type("array<Synapse\ReportsBundle\EntityDto\ElementDto>")
     */
    private $sectionElements;
	
	/**
     * object representation of the tips within a section
     *
     * @var TipsDto[] @JMS\Type("array<Synapse\ReportsBundle\EntityDto\TipsDto>")
     */
    private $tips;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return int
     */
    public function getSectionOrder()
    {
        return $this->sectionOrder;
    }

    /**
     * @param int $sectionOrder
     */
    public function setSectionOrder($sectionOrder)
    {
        $this->sectionOrder = $sectionOrder;
    }

    /**
     * @return string
     */
    public function getReorderDirection()
    {
        return $this->reorderDirection;
    }

    /**
     * @param string $reorderDirection
     */
    public function setReorderDirection($reorderDirection)
    {
        $this->reorderDirection = $reorderDirection;
    }

    /**
     * @return int
     */
    public function getSectionElementsCount()
    {
        return $this->sectionElementsCount;
    }

    /**
     * @param int $sectionElementsCount
     */
    public function setSectionElementsCount($sectionElementsCount)
    {
        $this->sectionElementsCount = $sectionElementsCount;
    }

    /**
     * @return int
     */
    public function getTipsCount()
    {
        return $this->tipsCount;
    }

    /**
     * @param int $tipsCount
     */
    public function setTipsCount($tipsCount)
    {
        $this->tipsCount = $tipsCount;
    }

    /**
     * @return ElementDto[]
     */
    public function getSectionElements()
    {
        return $this->sectionElements;
    }

    /**
     * @param array $sectionElements
     */
    public function setSectionElements($sectionElements)
    {
        $this->sectionElements = $sectionElements;
    }

    /**
     * @return TipsDto[]
     */
    public function getTips()
    {
        return $this->tips;
    }

    /**
     * @param array $tips
     */
    public function setTips($tips)
    {
        $this->tips = $tips;
    }

    /**
     * @return int
     */
    public function getSectionTipsCount()
    {
        return $this->sectionTipsCount;
    }

    /**
     * @param int $sectionTipsCount
     */
    public function setSectionTipsCount($sectionTipsCount)
    {
        $this->sectionTipsCount = $sectionTipsCount;
    }
}