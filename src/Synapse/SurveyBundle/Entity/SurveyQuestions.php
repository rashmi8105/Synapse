<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * SurveyQuestions
 *
 * @ORM\Table(name="survey_questions", indexes={@ORM\Index(name="fk_survey_questions_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_survey_questions_ebi_question1_idx", columns={"ebi_question_id"}), @ORM\Index(name="fk_survey_questions_org_question1_idx", columns={"org_question_id"}), @ORM\Index(name="fk_survey_questions_survey_sections1_idx", columns={"survey_sections_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\SurveyQuestionsRepository")
 */
class SurveyQuestions extends BaseEntity
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
     * @var string
     * @ORM\Column(name="type", type="string", nullable=true, columnDefinition="enum('bank', 'isq')")
     *     
     */
    private $type;

    /**
     *
     * @var integer @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    private $sequence;

    /**
     *
     * @var string @ORM\Column(name="qnbr", type="string", length=10, nullable=true)
     */
    private $qnbr;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     */
    private $survey;
    
    /**
     *
     * @var integer @ORM\Column(name="cohort_code", type="integer", nullable=true)
     */
    private $cohortCode;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiQuestion @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\EbiQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_question_id", referencedColumnName="id")
     *      })
     */
    private $ebiQuestion;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\OrgQuestion @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\OrgQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_question_id", referencedColumnName="id")
     *      })
     */
    private $orgQuestion;
    
    
    /**
     *
     * @var \Synapse\SurveyBundle\Entity\IndQuestion @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\IndQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ind_question_id", referencedColumnName="id")
     *      })
     */
    private $indQuestion;

    /**
     *
     * @var \Synapse\SurveyBundle\Entity\SurveySections @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\SurveySections")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_sections_id", referencedColumnName="id")
     *      })
     */
    private $surveySections;
    
    

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
     * Set type
     *
     * @param string $type            
     * @return SurveyQuestions
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
     * Set sequence
     *
     * @param integer $sequence            
     * @return SurveyQuestions
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
     * Set qnbr
     *
     * @param string $qnbr            
     * @return SurveyQuestions
     */
    public function setQnbr($qnbr)
    {
        $this->qnbr = $qnbr;
        
        return $this;
    }

    /**
     * Get qnbr
     *
     * @return string
     */
    public function getQnbr()
    {
        return $this->qnbr;
    }

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return SurveyQuestions
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

    /**
     * Set cohortCode
     *
     * @param integer $cohortCode
     * @return SurveyQuestions
     */
    public function setCohortCode($cohortCode)
    {
        $this->cohortCode = $cohortCode;
    
        return $this;
    }
    
    /**
     * Get cohortCode
     *
     * @return integer
     */
    public function getCohortCode()
    {
        return $this->cohortCode;
    }
    
    /**
     * Set ebiQuestion
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion            
     * @return SurveyQuestions
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
     * Set orgQuestion
     *
     * @param \Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion            
     * @return SurveyQuestions
     */
    public function setOrgQuestion(\Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion = null)
    {
        $this->orgQuestion = $orgQuestion;
        
        return $this;
    }

    /**
     * Get orgQuestion
     *
     * @return \Synapse\CoreBundle\Entity\OrgQuestion
     */
    public function getOrgQuestion()
    {
        return $this->orgQuestion;
    }

    /**
     * Set surveySections
     *
     * @param \Synapse\SurveyBundle\Entity\SurveySections $surveySections            
     * @return SurveyQuestions
     */
    public function setSurveySections(\Synapse\SurveyBundle\Entity\SurveySections $surveySections = null)
    {
        $this->surveySections = $surveySections;
        
        return $this;
    }

    /**
     * Get surveySections
     *
     * @return \Synapse\SurveyBundle\Entity\SurveySections
     */
    public function getSurveySections()
    {
        return $this->surveySections;
    }
    
    /**
     * Set indQuestion
     *
     * @param \Synapse\SurveyBundle\Entity\IndQuestion $indQuestion
     * @return SurveyQuestions
     */
    public function setIndQuestion(\Synapse\SurveyBundle\Entity\IndQuestion $indQuestion = null)
    {
        $this->indQuestion = $indQuestion;
    
        return $this;
    }
    
    /**
     * Get indQuestion
     *
     * @return \Synapse\SurveyBundle\Entity\IndQuestion
     */
    public function getIndQuestion()
    {
        return $this->indQuestion;
    }
}
