<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\ReportsBundle\Entity\Reports;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * reports_running_status
 *
 * @ORM\Table(name="reports_running_status")
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportsRunningStatus extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
	
	/**
     *
     * @var \Synapse\ReportsBundle\Entity\Reports @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\Reports")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $reports;
	
	/**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $organization;
	
	
	/**
     *
     * @var string @ORM\Column(name="is_viewed", type="string", columnDefinition="enum('Y','N')")
     */
    private $isViewed;
	
	
	/**
     *
     * @var string @ORM\Column(name="filtered_student_ids", type="text", nullable=true)
     * @JMS\Expose
     */
    private $filteredStudentIds;
	
	
	/**
     *
     * @var string @ORM\Column(name="filter_criteria", type="text", nullable=true)
     * @JMS\Expose
     */
    private $filterCriteria;
	
	
	/**
     *
     * @var string @ORM\Column(name="response_json ", type="text", nullable=true)
     * @JMS\Expose
     */
    private $responseJson;
	

    /**
     *
     * @var string @ORM\Column(name="report_custom_title", type="string", length=255, nullable=true)
     */
    private $reportCustomTitle;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", nullable=true, columnDefinition="enum('Q','IP','C','F')")
     *     
     */
    private $status;

      /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     *      })
     *      @JMS\Expose
     */
    private $person;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * Set reports
     *
     * @param \Synapse\ReportsBundle\Entity\reports $reports            
     * @return reportSections
     */
    public function setReports(Reports $reports = null)
    {
        $this->reports = $reports;
        
        return $this;
    }

    /**
     * Get reports
     *
     * @return \Synapse\ReportsBundle\Entity\reports
     */
    public function getReports()
    {
        return $this->reports;
    }
	
	/**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return organization
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;
        
        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
    /**
     *
     * @param string $isViewed
     */
    public function setIsViewed($isViewed)
    {
        $this->isViewed = $isViewed;
    }

    /**
     *
     * @return string
     */
    public function getIsViewed()
    {
        return $this->isViewed;
    }
	
	/**
     * Set filteredStudentIds
     *
     * @param string $filteredStudentIds            
     * @return filteredStudentIds
     */
    public function setFilteredStudentIds($filteredStudentIds)
    {
        $this->filteredStudentIds = $filteredStudentIds;
        
        return $this;
    }

    /**
     * Get filteredStudentIds
     *
     * @return string
     */
    public function getFilteredStudentIds()
    {
        return $this->filteredStudentIds;
    }
	
	/**
     * Set filterCriteria
     *
     * @param string $filterCriteria            
     * @return filterCriteria
     */
    public function setFilterCriteria($filterCriteria)
    {
        $this->filterCriteria = $filterCriteria;
        
        return $this;
    }

    /**
     * Get filterCriteria
     *
     * @return string
     */
    public function getFilterCriteria()
    {
        return $this->filterCriteria;
    }
	
	/**
     * Set reportCustomTitle
     *
     * @param string $reportCustomTitle            
     * @return reportCustomTitle
     */
    public function setReportCustomTitle($reportCustomTitle)
    {
        $this->reportCustomTitle = $reportCustomTitle;
        
        return $this;
    }

    /**
     * Get reportCustomTitle
     *
     * @return string
     */
    public function getReportCustomTitle()
    {
        return $this->reportCustomTitle;
    }
	
	/**
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
	
	/**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return person
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
	
	/**
     * Set responseJson
     *
     * @param string $responseJson            
     * @return responseJson
     */
    public function setResponseJson($responseJson)
    {
        $this->responseJson = $responseJson;
        
        return $this;
    }

    /**
     * Get responseJson
     *
     * @return string
     */
    public function getResponseJson()
    {
        return $this->responseJson;
    }
}