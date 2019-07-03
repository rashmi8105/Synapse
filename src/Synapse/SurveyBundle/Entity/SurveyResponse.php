<?php
namespace Synapse\SurveyBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SurveyResponse
 *
 * @ORM\Table(name="survey_response", indexes={@ORM\Index(name="fk_survey_response_organization1", columns={"org_id"}), @ORM\Index(name="fk_survey_response_person1", columns={"person_id"}), @ORM\Index(name="fk_survey_response_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_survey_response_org_academic_year1_idx", columns={"org_academic_year_id"}), @ORM\Index(name="fk_survey_response_org_academic_terms1_idx", columns={"org_academic_terms_id"}), @ORM\Index(name="fk_survey_response_survey_questions1_idx", columns={"survey_questions_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\SurveyResponseRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SurveyResponse extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="bigint", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     */
    private $survey;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAcademicYear;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicTerms")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_terms_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $orgAcademicTerms;

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
     * @var string @ORM\Column(name="response_type", type="string", columnDefinition="ENUM('decimal','char','charmax')", nullable=true)
     */
    private $responseType;

    /**
     *
     * @var string @ORM\Column(name="decimal_value", type="decimal", precision=9, scale=2, nullable=true)
     */
    private $decimalValue;

    /**
     *
     * @var string @ORM\Column(name="char_value", type="string", length=500, nullable=true, unique=false)
     */
    private $charValue;

    /**
     *
     * @var string @ORM\Column(name="charmax_value", type="string", length=5000, nullable=true, unique=false)
     */
    private $charmaxValue;

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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return SurveyResponse
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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return SurveyResponse
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
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return SurveyResponse
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
     * @return SurveyResponse
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
     * @return SurveyResponse
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
     * Set surveyQuestions
     *
     * @param \Synapse\SurveyBundle\Entity\SurveyQuestions $surveyQuestions            
     * @return SurveyResponse
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
     * Set responseType
     *
     * @param string $responseType            
     * @return SurveyResponse
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
     * @return SurveyResponse
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
     * @return SurveyResponse
     */
    public function setCharValue($charValue)
    {
        $this->charValue = $charValue;
        
        return $this;
    }

    /**
     * Get metaKey
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
     * @return SurveyResponse
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
}