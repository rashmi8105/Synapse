<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RiskVariable
 *
 * @ORM\Table(name="risk_variable", indexes={@ORM\Index(name="fk_risk_variable_ebi_metadata1_idx", columns={"ebi_metadata_id"}), @ORM\Index(name="fk_risk_variable_org_metadata1_idx", columns={"org_metadata_id"}), @ORM\Index(name="fk_risk_variable_ebi_question1_idx", columns={"ebi_question_id"}), @ORM\Index(name="fk_risk_variable_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_risk_variable_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_risk_variable_org_question1_idx", columns={"org_question_id"}), @ORM\Index(name="fk_risk_variable_survey_questions1_idx", columns={"survey_questions_id"}), @ORM\Index(name="fk_risk_variable_factor1_idx", columns={"factor_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskVariableRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"riskBVariable"},message="Choose a unique name.")
 */
class RiskVariable extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="risk_b_variable", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     *      @Assert\Regex("/^\S+$/", message="No spaces are allowed in the name")
     *      @Assert\Regex("/^[A-Za-z]+/", message="Variable names must start with a letter")
     *      @Assert\Length(max=100,maxMessage = "Risk Variable cannot be longer than {{ limit }} characters");
     */
    private $riskBVariable;

    /**
     *
     * @var string @ORM\Column(name="variable_type", type="string", precision=0, scale=0, nullable=true, unique=false, columnDefinition="ENUM('continuous', 'categorical')")
     */
    private $variableType;

    /**
     *
     * @var boolean @ORM\Column(name="is_calculated", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $isCalculated;

    /**
     *
     * @var string @ORM\Column(name="calc_type", type="string", precision=0, scale=0, nullable=true, unique=false,columnDefinition="ENUM('Most Recent', 'Sum', 'Average', 'Count', 'Academic Update')")
     */
    private $calcType;

    /**
     *
     * @var \DateTime @ORM\Column(name="calculation_start_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $calculationStartDate;

    /**
     *
     * @var \DateTime @ORM\Column(name="calculation_end_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $calculationEndDate;

    /**
     *
     * @var boolean @ORM\Column(name="is_archived", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $isArchived;

    /**
     *
     * @var string @ORM\Column(name="source", type="string", precision=0, scale=0, nullable=true, unique=false,columnDefinition="ENUM('profile','surveyquestion','surveyfactor','isp','isq','questionbank'')")
     */
    private $source;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiMetadata @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiMetadata")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_metadata_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $ebiMetadata;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\OrgMetadata @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgMetadata")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_metadata_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgMetadata;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiQuestion @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_question_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $ebiQuestion;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $survey;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $org;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\OrgQuestion @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_question_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgQuestion;

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\SurveyQuestions @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\SurveyQuestions")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_questions_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $surveyQuestions;

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\Factor @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\Factor")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="factor_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $factor;

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
     * Set riskBVariable
     *
     * @param string $riskBVariable            
     * @return RiskVariable
     */
    public function setRiskBVariable($riskBVariable)
    {
        $this->riskBVariable = $riskBVariable;
        
        return $this;
    }

    /**
     * Get riskBVariable
     *
     * @return string
     */
    public function getRiskBVariable()
    {
        return $this->riskBVariable;
    }

    /**
     * Set variableType
     *
     * @param string $variableType            
     * @return RiskVariable
     */
    public function setVariableType($variableType)
    {
        $this->variableType = $variableType;
        
        return $this;
    }

    /**
     * Get variableType
     *
     * @return string
     */
    public function getVariableType()
    {
        return $this->variableType;
    }

    /**
     * Set isCalculated
     *
     * @param boolean $isCalculated            
     * @return RiskVariable
     */
    public function setIsCalculated($isCalculated)
    {
        $this->isCalculated = $isCalculated;
        
        return $this;
    }

    /**
     * Get isCalculated
     *
     * @return boolean
     */
    public function getIsCalculated()
    {
        return $this->isCalculated;
    }

    /**
     * Set calcType
     *
     * @param string $calcType            
     * @return RiskVariable
     */
    public function setCalcType($calcType)
    {
        $this->calcType = $calcType;
        
        return $this;
    }

    /**
     * Get calcType
     *
     * @return string
     */
    public function getCalcType()
    {
        return $this->calcType;
    }

    /**
     * Set calculationStartDate
     *
     * @param \DateTime $calculationStartDate            
     * @return RiskVariable
     */
    public function setCalculationStartDate($calculationStartDate)
    {
        $this->calculationStartDate = $calculationStartDate;
        
        return $this;
    }

    /**
     * Get calculationStartDate
     *
     * @return \DateTime
     */
    public function getCalculationStartDate()
    {
        return $this->calculationStartDate;
    }

    /**
     * Set calculationEndDate
     *
     * @param \DateTime $calculationEndDate            
     * @return RiskVariable
     */
    public function setCalculationEndDate($calculationEndDate)
    {
        $this->calculationEndDate = $calculationEndDate;
        
        return $this;
    }

    /**
     * Get calculationEndDate
     *
     * @return \DateTime
     */
    public function getCalculationEndDate()
    {
        return $this->calculationEndDate;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived            
     * @return RiskVariable
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;
        
        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set source
     *
     * @param string $source            
     * @return RiskVariable
     */
    public function setSource($source)
    {
        $this->source = $source;
        
        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set ebiMetadata
     *
     * @param \Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata            
     * @return RiskVariable
     */
    public function setEbiMetadata(\Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata = null)
    {
        $this->ebiMetadata = $ebiMetadata;
        
        return $this;
    }

    /**
     * Get ebiMetadata
     *
     * @return \Synapse\CoreBundle\Entity\EbiMetadata
     */
    public function getEbiMetadata()
    {
        return $this->ebiMetadata;
    }

    /**
     * Set orgMetadata
     *
     * @param \Synapse\RiskBundle\Entity\OrgMetadata $orgMetadata            
     * @return RiskVariable
     */
    public function setOrgMetadata(\Synapse\CoreBundle\Entity\OrgMetadata $orgMetadata = null)
    {
        $this->orgMetadata = $orgMetadata;
        
        return $this;
    }

    /**
     * Get orgMetadata
     *
     * @return \Synapse\RiskBundle\Entity\OrgMetadata
     */
    public function getOrgMetadata()
    {
        return $this->orgMetadata;
    }

    /**
     * Set ebiQuestion
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion            
     * @return RiskVariable
     */
    public function setEbiQuestion(\Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion = null)
    {
        $this->ebiQuestion = $ebiQuestion;
        
        return $this;
    }

    /**
     * Get ebiQuestion
     *
     * @return \Synapse\CoreBundle\Entity\EbiQuestion
     */
    public function getEbiQuestion()
    {
        return $this->ebiQuestion;
    }

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return RiskVariable
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
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org            
     * @return RiskVariable
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org = null)
    {
        $this->org = $org;
        
        return $this;
    }

    /**
     * Get org
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * Set orgQuestion
     *
     * @param \Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion            
     * @return RiskVariable
     */
    public function setOrgQuestion(\Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion = null)
    {
        $this->orgQuestion = $orgQuestion;
        
        return $this;
    }

    /**
     * Get orgQuestion
     *
     * @return \Synapse\RiskBundle\Entity\OrgQuestion
     */
    public function getOrgQuestion()
    {
        return $this->orgQuestion;
    }

    /**
     * Set surveyQuestions
     *
     * @param \Synapse\SurveyBundle\Entity\SurveyQuestions $surveyQuestions            
     * @return RiskVariable
     */
    public function setSurveyQuestions(\Synapse\SurveyBundle\Entity\SurveyQuestions $surveyQuestions = null)
    {
        $this->surveyQuestions = $surveyQuestions;
        
        return $this;
    }

    /**
     * Get surveyQuestions
     *
     * @return \Synapse\SurveyBundle\Entity\SurveyQuestions
     */
    public function getSurveyQuestions()
    {
        return $this->surveyQuestions;
    }

    /**
     * Set factor
     *
     * @param \Synapse\SurveyBundle\Entity\Factor $factor            
     * @return RiskVariable
     */
    public function setFactor(\Synapse\SurveyBundle\Entity\Factor $factor = null)
    {
        $this->factor = $factor;
        
        return $this;
    }

    /**
     * Get factor
     *
     * @return \Synapse\RiskBundle\Entity\Factor
     */
    public function getFactor()
    {
        return $this->factor;
    }
}
