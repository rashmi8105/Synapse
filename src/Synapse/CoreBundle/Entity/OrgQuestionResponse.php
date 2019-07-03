<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgQuestionResponse
 *
 * 
 * @ORM\Table(name="org_question_response", indexes={@ORM\Index(name="fk_org_question_response_org_question1_idx", columns={"org_question_id"}), @ORM\Index(name="fk_org_question_response_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_org_question_response_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_org_question_response_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_org_question_response_org_academic_year1_idx", columns={"org_academic_year_id"}), @ORM\Index(name="fk_org_question_response_org_academic_terms1_idx", columns={"org_academic_terms_id"})})
 * 
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgQuestionResponseRepository")
 */
class OrgQuestionResponse extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $org;
	
	/**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgQuestionOptions")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_question_options_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgQuestionOptions;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $person;

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
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAcademicYear;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicTerms @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicTerms")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_terms_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAcademicTerms;

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
     * @var string @ORM\Column(name="multi_response_id", type="string", nullable=true, unique=false)
     */
    private $multiResponseId;

    /**
     *
     * @var string @ORM\Column(name="response_type", type="string", precision=0, scale=0, nullable=true, unique=false,columnDefinition="ENUM('decimal','char','charmax')")
     */
    private $responseType;

    /**
     *
     * @var string @ORM\Column(name="decimal_value", type="decimal", precision=9, scale=2, nullable=true, unique=false)
     */
    private $decimalValue;

    /**
     *
     * @var string @ORM\Column(name="char_value", type="string", length=500, precision=0, scale=0, nullable=true, unique=false)
     */
    private $charValue;

    /**
     *
     * @var string @ORM\Column(name="charmax_value", type="string", length=5000, precision=0, scale=0, nullable=true, unique=false)
     */
    private $charmaxValue;

    /**
     *
     * @var integer @ORM\Column(name="org_question_options_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $orgQuestionOptionsId;

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
     * Set Person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return OrgQuestionResponse
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     * Get Person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return OrgQuestionResponse
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
     * Set $orgAcademicYear
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear            
     * @return OrgQuestionResponse
     */
    public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear = null)
    {
        $this->orgAcademicYear = $orgAcademicYear;
        
        return $this;
    }

    /**
     * Get orgAcademicYear
     *
     * @return \Synapse\AcademicBundle\Entity\OrgAcademicYear
     */
    public function getOrgAcademicYear()
    {
        return $this->orgAcademicYear;
    }

    /**
     * Set orgAcademicTerms
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms            
     * @return OrgQuestionResponse
     */
    public function setOrgAcademicTerms(\Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms = null)
    {
        $this->orgAcademicTerms = $orgAcademicTerms;
        
        return $this;
    }

    /**
     * Get orgAcademicTerms
     *
     * @return \Synapse\CoreBundle\Entity\OrgAcademicTerms
     */
    public function getOrgAcademicTerms()
    {
        return $this->orgAcademicTerms;
    }

    /**
     * Set orgQuestion
     *
     * @param \Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion            
     * @return OrgQuestionResponse
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
     * Set responseType
     *
     * @param string $responseType            
     * @return OrgQuestionResponse
     */
    public function setResponseType($responseType)
    {
        $this->responseType = $responseType;
        
        return $this;
    }

    /**
     * Get responseType
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Set decimalValue
     *
     * @param string $decimalValue            
     * @return OrgQuestionResponse
     */
    public function setDecimalValue($decimalValue)
    {
        $this->decimalValue = $decimalValue;
        
        return $this;
    }

    /**
     * Get decimalValue
     *
     * @return string
     */
    public function getDecimalValue()
    {
        return $this->decimalValue;
    }

    /**
     * Set charValue
     *
     * @param string $charValue            
     * @return OrgQuestionResponse
     */
    public function setCharValue($charValue)
    {
        $this->charValue = $charValue;
        
        return $this;
    }

    /**
     * Get charValue
     *
     * @return string
     */
    public function getCharValue()
    {
        return $this->charValue;
    }

    /**
     * Set charmaxValue
     *
     * @param string $charmaxValue            
     * @return OrgQuestionResponse
     */
    public function setCharmaxValue($charmaxValue)
    {
        $this->charmaxValue = $charmaxValue;
        
        return $this;
    }

    /**
     * Get charmaxValue
     *
     * @return string
     */
    public function getCharmaxValue()
    {
        return $this->charmaxValue;
    }

      /**
     * Get orgQuestionOptionsId
     *
     * @return integer
     */
    public function getOrgQuestionOptionsId()
    {
        return $this->orgQuestionOptionsId;
    }
	
	/**
     * Set charmaxValue
     *
     * @param string $charmaxValue            
     * @return OrgQuestionResponse
     */
    public function setMultiResponseId()
    {
        $this->multiResponseId = $multiResponseId;
        
        return $this;
    }

     /**
     * Get charmaxValue
     *
     * @return string
     */
    public function getMultiResponseId()
    {
        return $this->multiResponseId;
    }
	
	/**
     * Set orgQuestionOptions
     *
     * @param \Synapse\CoreBundle\Entity\OrgQuestionOptions $orgQuestionOptions            
     * @return RiskVariable
     */
    public function setOrgQuestionOptions(\Synapse\CoreBundle\Entity\OrgQuestionOptions $orgQuestionOptions = null)
    {
        $this->orgQuestionOptions = $orgQuestionOptions;
        
        return $this;
    }

    /**
     * Get orgQuestionOptions
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrgQuestionOptions()
    {
        return $this->orgQuestionOptions;
    }
    
}
