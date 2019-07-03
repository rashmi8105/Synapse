<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 *
 * 
 * @package Synapse\ReportsBundle\EntityDto
 */
class ReportsTemplatesDto
{
	
	/**
     * generated id of a reports template
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;
	
	/**
     * Id of the person that created the reports template
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;
	
	/**
     * id of the organization that stores the reports template
     *
     * @var integer @JMS\Type("integer")
     */
    private $orgId;
	
	/**
     * id of the report that a reports template references
     *
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * name of a reports template
     *
     * @var string @JMS\Type("string")
     */
    private $templateName;
	
	/**
     * date that a reports template was created
     *
     * @var \Datetime @JMS\Type("DateTime")
     */
    private $templateDate;
	
	/**
     * predefined filter set for this reports template
     *
     * @var array @JMS\Type("array")
     */
    private $searchFilter;

    /**
     * request json that contains a template's information
     *
     * @var array @JMS\Type("array")
     */
    private  $requestJson;
	
	/**
     * information about a reports template
     *
     * @var array @JMS\Type("array")
     */
    private $reportInfo;

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
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->orgId;
    }

    /**
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->orgId = $organizationId;
    }

    /**
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     *
     * @param string $templateName
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     *
     * @return array
     */
    public function getRequestJson()
    {
        return $this->requestJson;
    }

    /**
     *
     * @param array $searchFilter
     */
    public function setSearchFilter($searchFilter)
    {
        $this->searchFilter = $searchFilter;
    }

    /**
     *
     * @return array
     */
    public function getSearchFilter()
    {
        return $this->searchFilter;
    }

    /**
     *
     * @param array
     */
    public function setRequestJson($requestJson)
    {
        $this->requestJson = $requestJson;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param int $reportId
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     *
     * @return array
     */
    public function getReportInfo()
    {
        return $this->reportInfo;
    }

    /**
     *
     * @param array $reportInfo
     */
    public function setReportInfo($reportInfo)
    {
        $this->reportInfo = $reportInfo;
    }

    /**
     *
     * @return \DateTime
     */
    public function getTemplateDate()
    {
        return $this->templateDate;
    }

    /**
     *
     * @param \DateTime $templateDate
     */
    public function setTemplateDate($templateDate)
    {
        $this->templateDate = $templateDate;
    }
}