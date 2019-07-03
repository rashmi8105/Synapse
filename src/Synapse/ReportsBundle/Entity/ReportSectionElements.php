<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\SurveyBundle\Entity\QuestionBank;

/**
 * ReportSectionElements
 *
 * @ORM\Table(name="report_section_elements", indexes={@ORM\Index(name="fk_report_elements_report_sections1_idx", columns={"section_id"}), @ORM\Index(name="fk_report_elements_factor1_idx", columns={"factor_id"}),@ORM\Index(name="fk_report_elements_survey_questions1_idx", columns={"survey_question_id"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportSectionElementsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportSectionElements extends BaseEntity
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
     * @var \Synapse\ReportsBundle\Entity\ReportSections @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportSections")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $sectionId;

    /**
     *
     * @var string @ORM\Column(name="title", type="string", length=100, nullable=true)
     *      @JMS\Expose
     */
    private $title;

    /**
     *
     * @var string @ORM\Column(name="description", type="text", nullable=true)
     *      @JMS\Expose
     */
    private $description;

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\Factor @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\Factor")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="factor_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $factorId;

    /**
     * @var QuestionBank
     * @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\QuestionBank")
     * @ORM\JoinColumn(name="question_bank_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $questionBank;

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\SurveyQuestions @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\SurveyQuestions")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_question_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $surveyQuestionId;

    /**
     * @var string
     * @ORM\Column(name="source_type", type="string", columnDefinition="enum('F', 'Q', 'E')", nullable=true)
     */
    private $sourceType;

    /**
     *
     * @var string @ORM\Column(name="icon_file_name", type="string", length=100, nullable=true)
     *      @JMS\Expose
     */
    private $iconFileName;
    
    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $survey;
    
    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiQuestion @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\EbiQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_question_id", referencedColumnName="id")
     *      })
     */
    private $ebiQuestionId;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sectionId
     *
     * @param \Synapse\ReportsBundle\Entity\ReportSections $sectionId            
     * @return ReportSectionElements
     */
    public function setSectionId($sectionId = null)
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
     * Set name
     *
     * @param
     *            string title
     * @return ReportSectionElements
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
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
     * Set name
     *
     * @param
     *            string description
     * @return ReportSectionElements
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set factorId
     *
     * @param \Synapse\SurveyBundle\Entity\Factor $factorId            
     * @return ReportSectionElements
     */
    public function setFactorId($factorId = null)
    {
        $this->factorId = $factorId;
        
        return $this;
    }

    /**
     * Get factorId
     *
     * @return \Synapse\SurveyBundle\Entity\Factor
     */
    public function getFactorId()
    {
        return $this->factorId;
    }

    /**
     * @param QuestionBank $questionBank
     */
    public function setQuestionBank($questionBank)
    {
        $this->questionBank = $questionBank;
    }

    /**
     * @return QuestionBank
     */
    public function getQuestionBank()
    {
        return $this->questionBank;
    }

    /**
     * Set surveyQuestionId
     *
     * @param \Synapse\SurveyBundle\Entity\SurveyQuestions $surveyQuestionId            
     * @return ReportSectionElements
     */
    public function setSurveyQuestionId($surveyQuestionId = null)
    {
        $this->surveyQuestionId = $surveyQuestionId;
        
        return $this;
    }

    /**
     * Get surveyQuestionId
     *
     * @return \Synapse\SurveyBundle\Entity\SurveyQuestions
     */
    public function getSurveyQuestionId()
    {
        return $this->surveyQuestionId;
    }

    /**
     * Set sourceType
     *
     * @param string $sourceType            
     * @return ReportSectionElements
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
        
        return $this;
    }

    /**
     * Get sourceType
     *
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * Set iconFileName
     *
     * @param string $iconFileName            
     * @return ReportSectionElements
     */
    public function setIconFileName($iconFileName)
    {
        $this->iconFileName = $iconFileName;
        
        return $this;
    }

    /**
     * Get iconFileName
     *
     * @return string
     */
    public function getIconFileName()
    {
        return $this->iconFileName;
    }
    
    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey
     * @return ReportSectionElements
     */
    public function setSurvey($survey = null)
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
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestionId
     */
    public function setEbiQuestionId($ebiQuestionId)
    {
        $this->ebiQuestionId = $ebiQuestionId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\EbiQuestion
     */
    public function getEbiQuestionId()
    {
        return $this->ebiQuestionId;
    }
    
}