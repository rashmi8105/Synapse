<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Savesearch
 *
 * @package Synapse\SearchBundle\EntityDto
 *         
 */
class SaveSearchDto
{

    /**
     * id of the organization that contains the saved search
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * id of a saved search
     *
     * @var integer @JMS\Type("integer")
     */
    private $savedSearchId;

    /**
     * name of a saved search
     *
     * @var string @JMS\Type("string")
     */
    private $savedSearchName;

    /**
     * datetime that a saved search was created
     *
     * @var \DateTime @JMS\Type("DateTime")
     */
    private $dateCreated;

    /**
     * id of the person that created a saved search
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * search filters that apply to a saved search
     *
     * @var array @JMS\Type("array")
     */
    private $searchAttributes;

    /**
     * array of SavedSearchDtos
     *
     * @var SavedSearchesDto[] @JMS\Type("array<Synapse\SearchBundle\EntityDto\SavedSearchesDto>")
     */
    private $savedSearches;

    /**
     * list of past activity upon a saved search
     *
     * @var array @JMS\Type("array")
     */
    private $activityReport;
    
    /**
     * list of sections within a saved report
     *
     * @var array @JMS\Type("array")
     */
    private $reportSections;
    
    /**
     * id of the report that a saved search applies to
     *
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
    
    /**
     * comma separated value containing selected search attributes
     *
     * @var string @JMS\Type("string")
     */
    private $selectedAttributesCsv;

    /**
     * type of search
     *
     * @var string @JMS\Type("string")
     */
    private $searchType;

    /**
     * array of mandatory compare report / search filters.
     *
     * @var array @JMS\Type("array")
     */
    private $mandatoryFilters;

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
     * @param integer $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getSavedSearchId()
    {
        return $this->savedSearchId;
    }

    /**
     *
     * @param integer $savedSearchId
     */
    public function setSavedSearchId($savedSearchId)
    {
        $this->savedSearchId = $savedSearchId;
    }

    /**
     *
     * @return string
     */
    public function getSavedSearchName()
    {
        return $this->savedSearchName;
    }

    /**
     *
     * @param string $savedSearchName
     */
    public function setSavedSearchName($savedSearchName)
    {
        $this->savedSearchName = $savedSearchName;
    }

    /**
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     *
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param integer $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return array
     */
    public function getSearchAttributes()
    {
        return $this->searchAttributes;
    }

    /**
     *
     * @param array $searchAttributes
     */
    public function setSearchAttributes($searchAttributes)
    {
        $this->searchAttributes = $searchAttributes;
    }

    /**
     *
     * @return SavedSearchesDto[]
     */
    public function getSavedSearches()
    {
        return $this->savedSearches;
    }

    /**
     *
     * @param SavedSearchesDto[] $savedSearches
     */
    public function setSavedSearches($savedSearches)
    {
        $this->savedSearches = $savedSearches;
    }


    /**
     *
     * @return array
     */
    public function getActivityReport()
    {
        return $this->activityReport;
    }

    /**
     *
     * @param Object $activityReport
     */
    public function setActivityReport($activityReport)
    {
        $this->activityReport = $activityReport;
    }

    /**
     *
     * @return array
     */
    public function getReportSections()
    {
        return $this->reportSections;
    }

    /**
     *
     * @param array $reportSections
     */
    public function setReportSections($reportSections)
    {
        $this->reportSections = $reportSections;
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
     * @param integer $reportId
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     *
     * @return string
     */
    public function getSelectedAttributesCsv()
    {
        return $this->selectedAttributesCsv;
    }

    /**
     *
     * @param string $selectedAttributesCsv
     */
    public function setSelectedAttributesCsv($selectedAttributesCsv)
    {
    	$this->selectedAttributesCsv = $selectedAttributesCsv;
    }

    /**
     *
     * @return string
     */
    public function getSearchType()
    {
        return $this->searchType;
    }

    /**
     *
     * @param string $searchType
     */
    public function setSearchType($searchType)
    {
        $this->searchType = $searchType;
    }

    /**
     *
     * @return array
     */
    public function getMandatoryFilters()
    {
        return $this->mandatoryFilters;
    }

    /**
     *
     * @param array $mandatoryFilters
     */
    public function setMandatoryFilters($mandatoryFilters)
    {
        $this->mandatoryFilters = $mandatoryFilters;
    }
}