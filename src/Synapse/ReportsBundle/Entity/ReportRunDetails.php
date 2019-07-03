<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * report_run_details
 *
 * @ORM\Table(name="report_run_details")
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportRunDetailsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportRunDetails extends BaseEntity
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
     * @var \Synapse\ReportsBundle\Entity\ReportsRunningStatus @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportsRunningStatus")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="report_instance_id", referencedColumnName="id", nullable=true)
     *      })
     *      @JMS\Expose
     */
    private $reportInstance;
	

	/**
     *
     * @var \Synapse\ReportsBundle\Entity\ReportSections @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportSections")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $section;
	

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\SurveyQuestions @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\SurveyQuestions")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=true)
     *      })
     *      @JMS\Expose
     */
    private $question;
	
	/**
     *
     * @var integer @ORM\Column(name="survey_qnbr", type="integer", nullable=true)
     */
    private $surveyQnbr;
	
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
     * @var string @ORM\Column(name="response_json", type="text", nullable=false)
     * @JMS\Expose
     */
    private $responseJson;
    
        
    /**
     *
     * @var string @ORM\Column(name="type", type="string", length=100, nullable=true)
     */
    private $type;
   

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
     * Set reportInstance
     *
     * @param \Synapse\ReportsBundle\Entity\ReportsRunningStatus $reportInstance            
     * @return reportInstance
     */
    public function setReportInstance(\Synapse\ReportsBundle\Entity\ReportsRunningStatus $reportInstance = null)
    {
        $this->reportInstance = $reportInstance;
        
        return $this;
    }

    /**
     * Get reportInstance
     *
     * @return \Synapse\ReportsBundle\Entity\ReportsRunningStatus
     */
    public function getReportInstance()
    {
        return $this->reportInstance;
    }
	
	/**
     * Set section
     *
     * @param \Synapse\ReportsBundle\Entity\ReportSections $section            
     * @return section
     */
    public function setSection(\Synapse\ReportsBundle\Entity\ReportSections $section = null)
    {
        $this->section = $section;
        
        return $this;
    }

    /**
     * Get section
     *
     * @return \Synapse\ReportsBundle\Entity\ReportSections
     */
    public function getSection()
    {
        return $this->section;
    }
	
	/**
     * Set question
     *
     * @param \Synapse\SurveyBundle\Entity\SurveyQuestions $question            
     * @return question
     */
    public function setQuestion(\Synapse\SurveyBundle\Entity\SurveyQuestions $question = null)
    {
        $this->question = $question;
        
        return $this;
    }

    /**
     * Get question
     *
     * @return \Synapse\SurveyBundle\Entity\SurveyQuestions
     */
    public function getQuestion()
    {
        return $this->question;
    }
	
	/**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return AcademicUpdate
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
	
	/**
     * Set type
     *
     * @param string $type            
     * @return type
     */
    public function setType($type)
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}