<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EbiQuestionsLang
 *
 * @ORM\Table(name="ebi_questions_lang", indexes={@ORM\Index(name="fk_ebi_questions_lang_ebi_question1_idx", columns={"ebi_question_id"}), @ORM\Index(name="fk_ebi_questions_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiQuestionLangRepository")
 */
class EbiQuestionsLang extends BaseEntity
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
     * @var string @ORM\Column(name="question_text", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $questionText;

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
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $lang;

    /**
     *
     * @var string @ORM\Column(name="question_rpt", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $questionRpt;

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
     * Set questionText
     *
     * @param string $questionText            
     * @return EbiQuestionsLang
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
     * Set ebiQuestion
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion            
     * @return EbiQuestionsLang
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
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang            
     * @return EbiQuestionsLang
     */
    public function setLang(\Synapse\CoreBundle\Entity\LanguageMaster $lang = null)
    {
        $this->lang = $lang;
        
        return $this;
    }

    /**
     * Get lang
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set questionRpt
     *
     * @param string $questionRpt            
     * @return EbiQuestionsLang
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
}
