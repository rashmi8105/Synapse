<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * IndQuestion
 *
 * @ORM\Table(name="ind_question", indexes={@ORM\Index(name="fk_ind_question_question_type1_idx", columns={"question_type_id"}), @ORM\Index(name="fk_ind_question_question_category1_idx", columns={"question_category_id"}), @ORM\Index(name="fk_ind_question_survey1_idx", columns={"survey_id"})})
 * @ORM\Entity(repositoryClass="")
 */
class IndQuestion extends BaseEntity
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
     * @var boolean @ORM\Column(name="has_other", type="boolean", nullable=true)
     */
    private $hasOther;

    /**
     *
     * @var string @ORM\Column(name="external_id", type="string", length=45, nullable=true)
     */
    private $externalId;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\QuestionType @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\QuestionType")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="question_type_id", referencedColumnName="id")
     *      })
     */
    private $questionType;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\QuestionCategory @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\QuestionCategory")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="question_category_id", referencedColumnName="id")
     *      })
     */
    private $questionCategory;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     */
    private $survey;

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
     * Set hasOther
     *
     * @param boolean $hasOther            
     * @return IndQuestion
     */
    public function setHasOther($hasOther)
    {
        $this->hasOther = $hasOther;
        
        return $this;
    }

    /**
     * Get hasOther
     *
     * @return boolean
     */
    public function getHasOther()
    {
        return $this->hasOther;
    }

    /**
     * Set externalId
     *
     * @param string $externalId            
     * @return IndQuestion
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        
        return $this;
    }

    /**
     * Get externalId
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set questionType
     *
     * @param \Synapse\CoreBundle\Entity\QuestionType $questionType            
     * @return IndQuestion
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
     * Set questionCategory
     *
     * @param \Synapse\CoreBundle\Entity\QuestionCategory $questionCategory            
     * @return IndQuestion
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
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return IndQuestion
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
}
