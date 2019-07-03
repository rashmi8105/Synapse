<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * FactorQuestions
 *
 * @ORM\Table(name="factor_questions", indexes={@ORM\Index(name="fk_factor_questions_factor1", columns={"factor_id"}), @ORM\Index(name="fk_factor_questions_ebi_question1_idx", columns={"ebi_question_id"}), @ORM\Index(name="fk_factor_questions_survey_questions1_idx", columns={"survey_questions_id"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\FactorQuestionsRepository")
 */
class FactorQuestions extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\Factor @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\Factor")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="factor_id", referencedColumnName="id")
     *      })
     */
    private $factor;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiQuestion @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\EbiQuestion")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_question_id", referencedColumnName="id")
     *      })
     */
    private $ebiQuestion;

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\SurveyQuestions @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\SurveyQuestions")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_questions_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $surveyQuestions;

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
     * Set factor
     *
     * @param \Synapse\SurveyBundle\Entity\Factor $factor
     * @return FactorQuestions
     */
    public function setFactor(\Synapse\SurveyBundle\Entity\Factor $factor = null)
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

    /**
     * Set ebiQuestion
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion
     * @return FactorQuestions
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
     * Set surveyQuestions
     *
     * @param \Synapse\SurveyBundle\Entity\SurveyQuestions $surveyQuestions
     * @return SurveyQuestions
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
}
