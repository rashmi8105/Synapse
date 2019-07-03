<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgQuestion
 *
 * @ORM\Table(name="org_question", indexes={@ORM\Index(name="fk_org_question_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_org_question_question_type1_idx", columns={"question_type_id"}), @ORM\Index(name="fk_org_question_question_category1_idx", columns={"question_category_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgQuestionRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgQuestion extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\QuestionType
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\QuestionType")
     * @ORM\JoinColumn(name="question_type_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $questionType;

    /**
     * @var Survey
     * @ORM\ManyToOne(targetEntity="Survey")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $survey;

    /**
     * @var int
     * @ORM\Column(name="cohort", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $cohort;

    /**
     * @var string
     *
     * @ORM\Column(name="question_text", type="text", precision=0, scale=0, nullable=true, unique=false)
     * @JMS\Expose
     */
    private $questionText;

    /**
     * @var string
     *
     * @ORM\Column(name="external_id", type="string", nullable=true)
     * @JMS\Expose
     */
    private $externalId;

    /**
     * @var \Synapse\CoreBundle\Entity\QuestionCategory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\QuestionCategory")
     * @ORM\JoinColumn(name="question_category_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $questionCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="question_key", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     * @JMS\Expose
     */
    private $questionKey;


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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgQuestion
     */
    public function setOrganization($organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set questionType
     *
     * @param \Synapse\CoreBundle\Entity\QuestionType $questionType
     * @return OrgQuestion
     */
    public function setQuestionType($questionType = null)
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
     * @param Survey $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param int $cohort
     */
    public function setCohort($cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return int
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * Set questionText
     *
     * @param string $questionText
     * @return OrgQuestion
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
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set questionCategory
     *
     * @param \Synapse\CoreBundle\Entity\QuestionCategory $questionCategory
     * @return OrgQuestion
     */
    public function setQuestionCategory($questionCategory = null)
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
     * Set questionKey
     *
     * @param string $questionKey
     * @return OrgQuestion
     */
    public function setQuestionKey($questionKey)
    {
        $this->questionKey = $questionKey;

        return $this;
    }

    /**
     * Get questionKey
     *
     * @return string 
     */
    public function getQuestionKey()
    {
        return $this->questionKey;
    }
    
}
