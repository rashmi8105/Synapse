<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * QuestionTypeLang
 *
 * @ORM\Table(name="question_type_lang", indexes={@ORM\Index(name="fk_question_types_lang_question_types1_idx", columns={"question_type_id"}), @ORM\Index(name="fk_question_type_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity
 */
class QuestionTypeLang extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     */
    private $description;

    /**
     * @var \Synapse\CoreBundle\Entity\QuestionType
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\QuestionType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_type_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $questionType;

    /**
     * @var \Synapse\CoreBundle\Entity\LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $lang;



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
     * Set description
     *
     * @param string $description
     * @return QuestionTypeLang
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set questionType
     *
     * @param \Synapse\CoreBundle\Entity\QuestionType $questionType
     * @return QuestionTypeLang
     */
    public function setQuestionType(\Synapse\CoreBundle\Entity\QuestionType $questionType = null)
    {
        $this->questionType = $questionType;

        return $this;
    }

    /**
     * Get questionType
     *
     * @return \Synapse\CoreBundle\Entity\QuestionType 
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return QuestionTypeLang
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
}
