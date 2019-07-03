<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\EbiQuestion;
use Synapse\CoreBundle\Entity\Survey;

/**
 * QuestionBankMap
 *
 * @ORM\Table(name="question_bank_map", uniqueConstraints = {@ORM\UniqueConstraint(name="qbm_unique", columns={"survey_id", "question_bank_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\QuestionBankMapRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class QuestionBankMap extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var Survey
     * @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $survey;

    /**
     * @var QuestionBank
     * @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\QuestionBank")
     * @ORM\JoinColumn(name="question_bank_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $questionBank;

    /**
     * @var EbiQuestion
     * @ORM\OneToOne(targetEntity="\Synapse\CoreBundle\Entity\EbiQuestion")
     * @ORM\JoinColumn(name="ebi_question_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $ebiQuestion;

    /**
     * @var SurveyQuestions
     * @ORM\OneToOne(targetEntity="\Synapse\SurveyBundle\Entity\SurveyQuestions")
     * @ORM\JoinColumn(name="survey_question_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $surveyQuestion;

    /**
     * @var integer
     * @ORM\Column(name="external_id", type="integer", nullable=false)
     * @JMS\Expose
     */
    private $externalId;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return QuestionBank
     */
    public function getQuestionBank()
    {
        return $this->questionBank;
    }

    /**
     * @param QuestionBank $questionBank
     */
    public function setQuestionBank($questionBank)
    {
        $this->questionBank = $questionBank;
    }

    /**
     * @return EbiQuestion
     */
    public function getEbiQuestion()
    {
        return $this->ebiQuestion;
    }

    /**
     * @param EbiQuestion $ebiQuestion
     */
    public function setEbiQuestion($ebiQuestion)
    {
        $this->ebiQuestion = $ebiQuestion;
    }

    /**
     * @return SurveyQuestions
     */
    public function getSurveyQuestion()
    {
        return $this->surveyQuestion;
    }

    /**
     * @param SurveyQuestions $surveyQuestion
     */
    public function setSurveyQuestion($surveyQuestion)
    {
        $this->surveyQuestion = $surveyQuestion;
    }

    /**
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }
}