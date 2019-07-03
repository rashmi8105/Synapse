<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgPermissionsetQuestion
 *
 * @ORM\Table(name="org_permissionset_question", indexes={@ORM\Index(name="fk_org_permissionset_question_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_org_permissionset_question_org_permissionset1_idx", columns={"org_permissionset_id"}), @ORM\Index(name="fk_org_permissionset_question_org_question1_idx", columns={"org_question_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPermissionsetQuestionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPermissionsetQuestion extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgPermissionset
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgPermissionset;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgQuestion
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgQuestion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_question_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgQuestion;
    
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
     * @return OrgPermissionsetQuestion
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
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
     * Set orgPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset
     * @return OrgPermissionsetQuestion
     */
    public function setOrgPermissionset(\Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset = null)
    {
        $this->orgPermissionset = $orgPermissionset;

        return $this;
    }

    /**
     * Get orgPermissionset
     *
     * @return \Synapse\CoreBundle\Entity\OrgPermissionset 
     */
    public function getOrgPermissionset()
    {
        return $this->orgPermissionset;
    }

    /**
     * Set orgQuestion
     *
     * @param \Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion
     * @return OrgPermissionsetQuestion
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
}
