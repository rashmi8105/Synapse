<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Issue
 *
 * @ORM\Table(name="issue", indexes={@ORM\Index(name="fk_issue_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_issue_survey_questions1_idx", columns={"survey_questions_id"}), @ORM\Index(name="fk_issue_factor1_idx", columns={"factor_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\IssueRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Issue extends BaseEntity
{
    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
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
     * @var string
     *
     * @ORM\Column(name="min", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $min;
    
    /**
     * @var string
     *
     * @ORM\Column(name="max", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $max;
    
    /**
     *
     * @var \DateTime @ORM\Column(name="start_date", type="date", precision=0, scale=0, nullable=true, unique=false)
     */
    private $startDate;
    
    /**
     *
     * @var \DateTime @ORM\Column(name="end_date", type="date", precision=0, scale=0, nullable=true, unique=false)
     */
    private $endDate;
    
    /**
     *
     * @var string @ORM\Column(name="icon", type="string", length=255, nullable=true, unique=false)
     */
    private $icon;
    
    /**
     *
     * @var string @ORM\Column(name="thumbnail", type="string", length=255, nullable=true, unique=false)
     */
    private $thumbnail;
    
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
    
    /**
     * Set min
     *
     * @param string $min
     * @return string
     */
    public function setMin($min)
    {
    	$this->min = $min;
    
    	return $this;
    }
    
    /**
     * Get min
     *
     * @return string
     */
    public function getMin()
    {
    	return $this->min;
    }
    
    /**
     * Set max
     *
     * @param string $max
     * @return RiskVariableRange
     */
    public function setMax($max)
    {
    	$this->max = $max;
    
    	return $this;
    }
    
    /**
     * Get max
     *
     * @return string
     */
    public function getMax()
    {
    	return $this->max;
    }
    
    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return startDate
     */
    public function setStartDate($startDate)
    {
    	$this->startDate = $startDate;
    
    	return $this;
    }
    
    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
    	return $this->startDate;
    }
    
    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return endDate
     */
    public function setEndDate($endDate)
    {
    	$this->endDate = $endDate;
    
    	return $this;
    }
    
    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
    	return $this->endDate;
    }
    
    /**
     * Set icon
     * @param string $icon
     * @return string
     */
    public function setIcon($icon)
    {
    	$this->icon = $icon;
    
    	return $this;
    }
    
    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
    	return $this->icon;
    }
    
    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return string
     */
    public function setThumbnail($thumbnail)
    {
    	$this->thumbnail = $thumbnail;
    
    	return $this;
    }
    
    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
    	return $this->thumbnail;
    }
    
}