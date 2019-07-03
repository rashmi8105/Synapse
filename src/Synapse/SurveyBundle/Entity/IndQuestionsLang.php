<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * IndQuestion
 *
 * @ORM\Table(name="ind_questions_lang", indexes={@ORM\Index(name="fk_survey_questions_lang_language_master1_idx", columns={"lang_id"}), @ORM\Index(name="fk_survey_questions_lang_ind_question1_idx", columns={"ind_question_id"})})
 * @ORM\Entity(repositoryClass="")
 */
class IndQuestionsLang extends BaseEntity
{

    /**
     *
     * @var \IndQuestion @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\SurveyBundle\Entity\IndQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ind_question_id", referencedColumnName="id")
     *      })
     */
    private $indQuestion;

    /**
     *
     * @var \LanguageMaster @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="\Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     *      })
     */
    private $languageMaster;

    /**
     *
     * @var string @ORM\Column(name="question_text", type="string", length=3000, nullable=true)
     */
    private $questionText;

    /**
     *
     * @var string @ORM\Column(name="question_rpt", type="string", length=3000, nullable=true)
     */
    private $questionRpt;

    /**
     * Set indQuestion
     *
     * @param \Synapse\SurveyBundle\Entity\IndQuestion $indQuestion            
     * @return IndQuestionsLang
     */
    public function setIndQuestion(\Synapse\SurveyBundle\Entity\IndQuestion $indQuestion = null)
    {
        $this->questionType = $questionType;
        
        return $this;
    }

    /**
     * Get indQuestion
     *
     * @return \Synapse\SurveyBundle\Entity\IndQuestion
     */
    public function getIndQuestion()
    {
        return $this->questionType;
    }

    /**
     * Set LanguageMaster
     *
     * @param
     *            \Synapse\CoreBundle\Entity\LanguageMaster
     * @return IndQuestionsLang
     */
    public function setLanguageMaster(\Synapse\CoreBundle\Entity\LanguageMaster $languageMaster = null)
    {
        $this->languageMaster = $languageMaster;
        
        return $this;
    }

    /**
     * Get LanguageMaster
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLanguageMaster()
    {
        return $this->languageMaster;
    }

    /**
     * Set questionText
     *
     * @param string $questionText            
     * @return IndQuestionsLang
     */
    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;
        
        return $this;
    }

    /**
     * Get questionText
     *
     * @return string
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }

    /**
     * Set questionRpt
     *
     * @param string $questionRpt            
     * @return IndQuestionsLang
     */
    public function setQuestionRpt($questionRpt)
    {
        $this->questionRpt = $questionRpt;
        
        return $this;
    }

    /**
     * Get questionRpt
     *
     * @return string
     */
    public function getQuestionRpt()
    {
        return $this->questionRpt;
    }

    public function getId()
    {
        return $this;
    }
}
