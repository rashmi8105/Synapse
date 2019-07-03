<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ReportCalculatedValues
 *
 * @ORM\Table(name="report_calculated_values", indexes={@ORM\Index(name="fk_report_calculated_values_reports1_idx", columns={"report_id"}), @ORM\Index(name="fk_report_calculated_values_report_sections1_idx", columns={"section_id"}),@ORM\Index(name="fk_report_calculated_values_report_element_buckets1_idx", columns={"element_bucket_id"}),@ORM\Index(name="fk_report_calculated_values_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportCalculatedValuesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportCalculatedValues extends BaseEntity
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
     * @var \Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $orgId;

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
     * @var \Synapse\ReportsBundle\Entity\Reports @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\Reports")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $reportId;

    /**
     *
     * @var \Synapse\ReportsBundle\Entity\ReportSections @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportSections")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $sectionId;
    
    /**
     *
     * @var \Synapse\ReportsBundle\Entity\ReportSectionElements @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportSectionElements")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="element_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $elementId;
	
	/**
     *
     * @var \Synapse\ReportsBundle\Entity\ReportElementBuckets @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportElementBuckets")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="element_bucket_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $elementBucketId;
    
    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     */
    private $survey;
	
	/**
     *
     * @var string @ORM\Column(name="calculated_value", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $calculatedValue;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orgId
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return ReportCalculatedValues
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization)
    {
        $this->orgId = $organization;
        
        return $this;
    }

    /**
     * Get orgId
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->orgId;
    }
	
	/**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return Person
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    

    /**
     * Set reportId
     *
     * @param \Synapse\ReportsBundle\Entity\Reports $reportId            
     * @return ReportCalculatedValues
     */
    public function setReportId(\Synapse\ReportsBundle\Entity\Reports $reportId = null)
    {
        $this->reportId = $reportId;
        
        return $this;
    }

    /**
     * Get reportId
     *
     * @return \Synapse\ReportsBundle\Entity\Reports
     */
    public function getReportId()
    {
        return $this->reportId;
    }

    /**
     * Set sectionId
     *
     * @param \Synapse\ReportsBundle\Entity\ReportSections $sectionId            
     * @return ReportSectionElements
     */
    public function setSectionId(\Synapse\ReportsBundle\Entity\ReportSections $sectionId = null)
    {
        $this->sectionId = $sectionId;
        
        return $this;
    }

    /**
     * Get sectionId
     *
     * @return \Synapse\ReportsBundle\Entity\ReportSections
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }
	
	/**
     * Set elementBucketId
     *
     * @param \Synapse\ReportsBundle\Entity\ReportElementBuckets $elementBucketId            
     * @return ReportSectionElements
     */
    public function setElementBucketId(\Synapse\ReportsBundle\Entity\ReportElementBuckets $elementBucketId = null)
    {
        $this->elementBucketId = $elementBucketId;
        
        return $this;
    }

    /**
     * Get elementBucketId
     *
     * @return \Synapse\ReportsBundle\Entity\ReportElementBuckets
     */
    public function getElementBucketId()
    {
        return $this->elementBucketId;
    }
    
    /**
     * Set elementId
     *
     * @param \Synapse\ReportsBundle\Entity\ReportSectionElements $elementId
     * @return ReportSectionElements
     */
    public function setElementId(\Synapse\ReportsBundle\Entity\ReportSections $elementId = null)
    {
    	$this->elementId = $elementId;
    
    	return $this;
    }
    
    /**
     * Get elementId
     *
     * @return \Synapse\ReportsBundle\Entity\ReportSectionElements
     */
    public function getElementId()
    {
    	return $this->elementId;
    }
    
    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey
     * @return survey
     */
    public function setSurvey(\Synapse\CoreBundle\Entity\Survey $survey = null)
    {
    	$this->survey = $survey;
    
    	return $this;
    }
    
    /**
     * Get survey
     *
     * @return \Synapse\CoreBundle\Entity\Survey
     */
    public function getSurvey()
    {
    	return $this->survey;
    }
	/**
     * Set calculatedValue
     */
    public function setCalculatedValue($calculatedValue)
    {
        $this->calculatedValue = $calculatedValue;
        
        return $this;
    }

    /**
     * Get calculatedValue
     */
    public function getCalculatedValue()
    {
        return $this->calculatedValue;
    }
}