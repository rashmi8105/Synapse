<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class ReportSectionsDto
{

    /**
     * sectionId
     *
     * @var integer @JMS\Type("integer")
     */
    private $sectionId;
	
	/**
     * reportId
     *
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * sections_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $sectionsCount;
	
	/**
     * sectionName
     *
     * @var string @JMS\Type("string")
     */
    private $sectionName;
	
	
	/**
     * sections
     *
     * @var Object @JMS\Type("array<Synapse\ReportsBundle\EntityDto\SectionDto>")
     */
    private $sections;
	
	
	/**
     * elements
     *
     * @var Object @JMS\Type("array<Synapse\ReportsBundle\EntityDto\ElementDto>")
     */
    private $elements;
	
	/**
     * tips
     *
     * @var Object @JMS\Type("array<Synapse\ReportsBundle\EntityDto\TipsDto>")
     */
    private $tips;

	/**
     *
     * @param int $reportId            
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }
	
	/**
     *
     * @param string $sectionName            
     */
    public function setSectionName($sectionName)
    {
        $this->sectionName = $sectionName;
    }

    /**
     *
     * @return string
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }
	
	/**
     *
     * @param Object $elements            
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     *
     * @return Object
     */
    public function getElements()
    {
        return $this->elements;
    }
	
	/**
     *
     * @param Object $tips            
     */
    public function setTips($tips)
    {
        $this->tips = $tips;
    }

    /**
     *
     * @return Object
     */
    public function getTips()
    {
        return $this->tips;
    }
	
	/**
     *
     * @param int $reportId            
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     *
     * @return int
     */
    public function getReportId()
    {
        return $this->reportId;
    }
	
	/**
     *
     * @param int $sectionsCount            
     */
    public function setSectionsCount($sectionsCount)
    {
        $this->sectionsCount = $sectionsCount;
    }

    /**
     *
     * @return int
     */
    public function getSectionsCount()
    {
        return $this->sectionsCount;
    }
	
	/**
     *
     * @param Object $sections            
     */
    public function setSections($sections)
    {
        $this->sections = $sections;
    }

    /**
     *
     * @return Object
     */
    public function getSections()
    {
        return $this->sections;
    }
	
}