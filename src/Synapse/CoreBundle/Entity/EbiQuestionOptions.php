<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EbiQuestionOptions
 *
 * @ORM\Table(name="ebi_question_options", indexes={@ORM\Index(name="fk_ebi_question_options_ebi_question1_idx", columns={"ebi_question_id"}), @ORM\Index(name="fk_ebi_question_options_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiQuestionOptionsRepository")
 */
class EbiQuestionOptions extends BaseEntity
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
     * @var string @ORM\Column(name="option_text", type="string", length=150, precision=0, scale=0, nullable=true, unique=false)
     */
    private $optionText;

    /**
     *
     * @var string @ORM\Column(name="option_value", type="string", length=5000, precision=0, scale=0, nullable=true, unique=false)
     */
    private $optionValue;

    /**
     *
     * @var integer @ORM\Column(name="sequence", type="smallint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $sequence;

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
     * @var string @ORM\Column(name="extended_option_text", type="string", length=150, precision=0, scale=0, nullable=true, unique=false)
     */
    private $extendedOptionText;

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
     * Set optionText
     *
     * @param string $optionText
     * @return EbiQuestionOptions
     */
    public function setOptionText($optionText)
    {
        $this->optionText = $optionText;

        return $this;
    }

    /**
     * Get optionText
     *
     * @return string
     */
    public function getOptionText()
    {
        return $this->optionText;
    }

    /**
     * Set optionValue
     *
     * @param string $optionValue
     * @return EbiQuestionOptions
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    /**
     * Get optionValue
     *
     * @return string
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return EbiQuestionOptions
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
     * Set ebiQuestion
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion
     * @return EbiQuestionOptions
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
     * @return EbiQuestionOptions
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
     * @return string
     */
    public function getExtendedOptionText()
    {
        return $this->extendedOptionText;
    }

    /**
     * @param string $extendedOptionText
     */
    public function setExtendedOptionText($extendedOptionText)
    {
        $this->extendedOptionText = $extendedOptionText;
    }
}
