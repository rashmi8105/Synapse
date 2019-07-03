<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\EbiQuestionOptions;
use Synapse\CoreBundle\Entity\Survey;


/**
 * SurveyBranch
 *
 * @ORM\Table(name="survey_branch")
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\SurveyBranchRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SurveyBranch extends BaseEntity
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
     * @var SurveyQuestions
     * @ORM\ManyToOne(targetEntity="SurveyQuestions")
     * @ORM\JoinColumn(name="survey_question_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $surveyQuestion;

    /**
     * @var EbiQuestionOptions
     * @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\EbiQuestionOptions")
     * @ORM\JoinColumn(name="ebi_question_options_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $ebiQuestionOptions;

    /**
     * @var SurveyPages
     * @ORM\ManyToOne(targetEntity="SurveyPages")
     * @ORM\JoinColumn(name="survey_pages_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $surveyPages;

    /**
     * @var SurveyPages
     * @ORM\ManyToOne(targetEntity="SurveyPages")
     * @ORM\JoinColumn(name="branch_to_survey_pages_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $branchToSurveyPages;


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
     * @return EbiQuestionOptions
     */
    public function getEbiQuestionOptions()
    {
        return $this->ebiQuestionOptions;
    }

    /**
     * @param EbiQuestionOptions $ebiQuestionOptions
     */
    public function setEbiQuestionOptions($ebiQuestionOptions)
    {
        $this->ebiQuestionOptions = $ebiQuestionOptions;
    }

    /**
     * @return SurveyPages
     */
    public function getSurveyPages()
    {
        return $this->surveyPages;
    }

    /**
     * @param SurveyPages $surveyPages
     */
    public function setSurveyPages($surveyPages)
    {
        $this->surveyPages = $surveyPages;
    }

    /**
     * @return SurveyPages
     */
    public function getBranchToSurveyPages()
    {
        return $this->branchToSurveyPages;
    }

    /**
     * @param SurveyPages $branchToSurveyPages
     */
    public function setBranchToSurveyPages($branchToSurveyPages)
    {
        $this->branchToSurveyPages = $branchToSurveyPages;
    }

}