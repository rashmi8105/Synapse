<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\ReportsBundle\Entity\Reports;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * reports template
 *
 * @ORM\Table(name="reports_template",indexes={@ORM\Index(name="fk_report_templates_reports1_idx", columns={"report_id"}), @ORM\Index(name="fk_report_templates_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportsTemplateRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"templateName","organization"},message="Template Name already exists.")
 */
class ReportsTemplate extends BaseEntity
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
     * @var string @ORM\Column(name="filter_criteria", type="text", nullable=false)
     * @JMS\Expose
     */
    private $filterCriteria;
	
	 /**
     *
     * @var string @ORM\Column(name="template_name", type="string", length=255, nullable=false)
     */
    private $templateName;
	
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
     * Set templateName
     *
     * @param string $templateName            
     * @return templateName
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
        
        return $this;
    }

    /**
     * Get templateName
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
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
}