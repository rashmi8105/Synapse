<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * QuestionCategoryLang
 *
 * @ORM\Table(name="question_category_lang", indexes={@ORM\Index(name="fk_question_category_lang_question_category1_idx", columns={"question_category_id"}), @ORM\Index(name="fk_question_category_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity
 */
class QuestionCategoryLang extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\QuestionCategory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\QuestionCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_category_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $questionCategory;

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
     * @return QuestionCategoryLang
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
     * Set questionCategory
     *
     * @param \Synapse\CoreBundle\Entity\QuestionCategory $questionCategory
     * @return QuestionCategoryLang
     */
    public function setQuestionCategory(\Synapse\CoreBundle\Entity\QuestionCategory $questionCategory = null)
    {
        $this->questionCategory = $questionCategory;

        return $this;
    }

    /**
     * Get questionCategory
     *
     * @return \Synapse\CoreBundle\Entity\QuestionCategory 
     */
    public function getQuestionCategory()
    {
        return $this->questionCategory;
    }

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return QuestionCategoryLang
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
