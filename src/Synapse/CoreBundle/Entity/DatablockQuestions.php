<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\SurveyBundle\Entity\QuestionBank;

/**
 * DatablockQuestions
 *
 * @ORM\Table(name="datablock_questions", indexes={@ORM\Index(name="fk_datablock_questions_datablock_master1_idx", columns={"datablock_id"}), @ORM\Index(name="fk_datablock_questions_ebi_question1_idx", columns={"ebi_question_id"}), @ORM\Index(name="fk_datablock_questions_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_datablock_questions_survey_questions1_idx", columns={"survey_questions_id"}), @ORM\Index(name="fk_datablock_questions_factor1_idx", columns={"factor_id"})})
 * @ORM\Entity (repositoryClass="Synapse\CoreBundle\Repository\DatablockQuestionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class DatablockQuestions extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\DatablockMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\DatablockMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="datablock_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $datablock;

    /**
     * @var QuestionBank
     * @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\QuestionBank")
     * @ORM\JoinColumn(name="question_bank_id", referencedColumnName="id", nullable=true)
     */
    private $questionBank;

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
     * @var string @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;

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
     * @var \Synapse\SurveyBundle\Entity\SurveyQuestions @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\SurveyQuestions")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_questions_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $surveyQuestions;

    /**
     *
     * @var string @ORM\Column(name="red_low", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $redLow;

    /**
     *
     * @var string @ORM\Column(name="red_high", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $redHigh;

    /**
     *
     * @var string @ORM\Column(name="yellow_low", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $yellowLow;

    /**
     *
     * @var string @ORM\Column(name="yellow_high", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $yellowHigh;

    /**
     *
     * @var string @ORM\Column(name="green_low", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $greenLow;

    /**
     *
     * @var string @ORM\Column(name="green_high", type="decimal", precision=6, scale=3, nullable=true)
     */
    private $greenHigh;

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
     * Set datablock
     *
     * @param \Synapse\CoreBundle\Entity\DatablockMaster $datablock            
     * @return DatablockQuestions
     */
    public function setDatablock($datablock = null)
    {
        $this->datablock = $datablock;
        
        return $this;
    }

    /**
     * Get datablock
     *
     * @return \Synapse\CoreBundle\Entity\DatablockMaster
     */
    public function getDatablock()
    {
        return $this->datablock;
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
     * Set ebiQuestion
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion            
     * @return DatablockQuestions
     */
    public function setEbiQuestion($ebiQuestion = null)
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
     * Set type
     *
     * @param string $type            
     * @return DatablockQuestions
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

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return DatablockQuestions
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
     * Set surveyQuestions
     *
     * @param \Synapse\SurveyBundle\Entity\SurveyQuestions $surveyQuestions            
     * @return DatablockQuestions
     */
    public function setSurveyQuestions($surveyQuestions = null)
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
     * Set redLow
     *
     * @param string $redLow            
     * @return DatablockQuestions
     */
    public function setRedLow($redLow)
    {
        $this->redLow = $redLow;
        
        return $this;
    }

    /**
     * Get redLow
     *
     * @return string
     */
    public function getRedLow()
    {
        return $this->redLow;
    }

    /**
     * Set redHigh
     *
     * @param string $redHigh            
     * @return DatablockQuestions
     */
    public function setRedHigh($redHigh)
    {
        $this->redHigh = $redHigh;
        
        return $this;
    }

    /**
     * Get redHigh
     *
     * @return string
     */
    public function getRedHigh()
    {
        return $this->redHigh;
    }

    /**
     * Set yellowLow
     *
     * @param string $yellowLow            
     * @return DatablockQuestions
     */
    public function setYellowLow($yellowLow)
    {
        $this->yellowLow = $yellowLow;
        
        return $this;
    }

    /**
     * Get yellowLow
     *
     * @return string
     */
    public function getYellowLow()
    {
        return $this->yellowLow;
    }

    /**
     * Set yellowHigh
     *
     * @param string $yellowHigh            
     * @return DatablockQuestions
     */
    public function setYellowHigh($yellowHigh)
    {
        $this->yellowHigh = $yellowHigh;
        
        return $this;
    }

    /**
     * Get yellowHigh
     *
     * @return string
     */
    public function getYellowHigh()
    {
        return $this->yellowHigh;
    }

    /**
     * Set greenLow
     *
     * @param string $greenLow            
     * @return DatablockQuestions
     */
    public function setGreenLow($greenLow)
    {
        $this->greenLow = $greenLow;
        
        return $this;
    }

    /**
     * Get greenLow
     *
     * @return string
     */
    public function getGreenLow()
    {
        return $this->greenLow;
    }

    /**
     * Set greenHigh
     *
     * @param string $greenHigh            
     * @return DatablockQuestions
     */
    public function setGreenHigh($greenHigh)
    {
        $this->greenHigh = $greenHigh;
        
        return $this;
    }

    /**
     * Get greenHigh
     *
     * @return string
     */
    public function getGreenHigh()
    {
        return $this->greenHigh;
    }

    /**
     * Set factor
     *
     * @param \Synapse\SurveyBundle\Entity\Factor $factor            
     * @return DatablockQuestions
     */
    public function setFactor($factor = null)
    {
        $this->factor = $factor;
        
        return $this;
    }

    /**
     * Get factor
     *
     * @return \Synapse\SurveyBundle\Entity\Factor
     */
    public function getFactor()
    {
        return $this->factor;
    }
}
