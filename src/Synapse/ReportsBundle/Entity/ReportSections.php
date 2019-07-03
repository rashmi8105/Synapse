<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\ReportsBundle\Entity\Reports;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * reports
 *
 * @ORM\Table(name="report_sections",indexes={@ORM\Index(name="fk_sections_reports1_idx", columns={"report_id"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportSectionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportSections extends BaseEntity
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
     * @var \Synapse\ReportsBundle\Entity\reports
     *      @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\Reports")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $reports;

    /**
     *
     * @var string @ORM\Column(name="title", type="string", length=100, nullable=true)
     *      @JMS\Expose
     */
    private $title;

    /**
     *
     * @var integer @ORM\Column(name="sequence", type="smallint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $sequence;
	
	/**
     *
     * @var string @ORM\Column(name="section_query", type="text", nullable=true)
     * @JMS\Expose
     */
    private $sectionQuery;

    /**
     * @var string @ORM\Column(name="retention_tracking_type", type="string", nullable=true, unique=false)
     */
    private $retentionTrackingType;

    /**
     * @var boolean @ORM\Column(name="survey_contingent", type="boolean", nullable=true, unique=false)
     */
    private $surveyContingent;

    /**
     * @var boolean @ORM\Column(name="academic_term_contingent", type="boolean", nullable=true, unique=false)
     */
    private $academicTermContingent;

    /**
     * @var boolean @ORM\Column(name="risk_contingent", type="boolean", nullable=true, unique=false)
     */
    private $riskContingent;


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
     * Set name
     *
     * @param
     *            string title
     * @return ReportSections
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence            
     * @return ReportSections
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
        
        return $this;
    }
    
    /**
     * Get sequence
     *
     * @return integer
     */
    public function getSequence()
    {
    	return $this->sequence;
    }
	
	/**
     * Get sectionQuery
     *
     * @return string
     */
    public function getSectionQuery()
    {
        return $this->sectionQuery;
    }
	
	/**
     * Set type
     *
     * @param string $sectionQuery            
     * @return type
     */
    public function setSectionQuery($sectionQuery)
    {
        $this->sectionQuery = $sectionQuery;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getRetentionTrackingType()
    {
        return $this->retentionTrackingType;
    }

    /**
     * @param string $retentionTrackingType
     */
    public function setRetentionTrackingType($retentionTrackingType)
    {
        $this->retentionTrackingType = $retentionTrackingType;
    }

    /**
     * @return boolean
     */
    public function isSurveyContingent()
    {
        return $this->surveyContingent;
    }

    /**
     * @param boolean $surveyContingent
     */
    public function setSurveyContingent($surveyContingent)
    {
        $this->surveyContingent = $surveyContingent;
    }

    /**
     * @return boolean
     */
    public function isAcademicTermContingent()
    {
        return $this->academicTermContingent;
    }

    /**
     * @param boolean $academicTermContingent
     */
    public function setAcademicTermContingent($academicTermContingent)
    {
        $this->academicTermContingent = $academicTermContingent;
    }

    /**
     * @return boolean
     */
    public function isRiskContingent()
    {
        return $this->riskContingent;
    }

    /**
     * @param boolean $riskContingent
     */
    public function setRiskContingent($riskContingent)
    {
        $this->riskContingent = $riskContingent;
    }


}