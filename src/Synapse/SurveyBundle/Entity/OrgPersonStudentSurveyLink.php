<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgPersonStudentSurveyLink
 *
 * @ORM\Table(name="org_person_student_survey_link", indexes={@ORM\Index(name="fk_org_person_student_survey_link_survey1_idx", columns={"survey_id"}),
 * @ORM\Index(name="fk_org_person_student_survey_link_survey1_idx", columns={"survey_id"}),
 * @ORM\Index(name="fk_org_person_student_survey_link_org_academic_year1_idx", columns={"org_academic_year_id"}),
 * @ORM\Index(name="fk_org_person_student_survey_link_organization1_idx", columns={"org_id"}),
 * @ORM\Index(name="fk_org_person_student_survey_link_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyLinkRepository")
 */
class OrgPersonStudentSurveyLink extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicyear @ORM\ManyToOne(targetEntity="\Synapse\AcademicBundle\Entity\OrgAcademicyear")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id")
     *      })
     */
    private $orgAcademicYear;

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
     * @var string @ORM\Column(name="cohort", type="string", length=20, nullable=true)
     */
    private $cohort;

    /**
     *
     * @var string @ORM\Column(name="survey_link", type="string", length=500, nullable=true)
     */
    private $surveyLink;
    
    /**
     * @ORM\Column(name="survey_assigned_date", type="datetime", nullable=true)
     */
    private $surveyAssignedDate;
    
    /**
     * @ORM\Column(name="survey_completion_date", type="datetime", nullable=true)
     */
    private $surveyCompletionDate;
    
    /**
     *
     * @var string
     * @ORM\Column(name="survey_completion_status", type="string", nullable=true, columnDefinition="enum('Assigned', 'InProgress', 'CompletedMandatory', 'CompletedAll')")
     *     
     */
    private $surveyCompletionStatus;
    
    /**
     *
     * @var string
     * @ORM\Column(name="survey_opt_out_status", type="string", nullable=true, columnDefinition="enum('Yes', 'No')")
     *     
     */
    private $surveyOptOutStatus;

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
     *
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;
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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return SurveyResponse
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set orgAcademicYear
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear            
     *
     */
    public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear = null)
    {
        $this->orgAcademicYear = $orgAcademicYear;
    }

    /**
     * Get orgAcademicYear
     *
     * @return \Synapse\AcademicBundle\Entity\OrgAcademicYear
     */
    public function getOrgAcademicYear()
    {
        return $this->orgAcademicYear;
    }

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return Factor
     */
    public function setSurvey(\Synapse\CoreBundle\Entity\Survey $survey = null)
    {
        $this->survey = $survey;
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
     * Set cohort
     *
     * @param string $cohort            
     *
     */
    public function setCohort($cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * Get cohort
     *
     * @return string
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * Set surveyLink
     *
     * @param string $surveyLink            
     *
     */
    public function setSurveyLink($surveyLink)
    {
        $this->surveyLink = $surveyLink;
    }

    /**
     * Get cohort
     *
     * @return string
     */
    public function getSurveyLink()
    {
        return $this->surveyLink;
    }
    
    /**
     * Set surveyAssignedDate
     *
     * @param \DateTime $surveyAssignedDate                 
     */
    public function setSurveyAssignedDate($surveyAssignedDate)
    {
        $this->surveyAssignedDate = $surveyAssignedDate;
        return $this;
    }

    /**
     * Get surveyAssignedDate
     *
     * @return \Datetime
     */
    public function getSurveyAssignedDate()
    {
        return $this->surveyAssignedDate;
    }
    
    /**
     * Set surveyCompletionDate
     *
     * @param \DateTime $surveyCompletionDate                 
     */
    public function setSurveyCompletionDate($surveyCompletionDate)
    {
        $this->surveyCompletionDate = $surveyCompletionDate;
        return $this;
    }

    /**
     * Get surveyCompletionDate
     *
     * @return \Datetime
     */
    public function getSurveyCompletionDate()
    {
        return $this->surveyCompletionDate;
    }
    
    /**
     * Set surveyCompletionStatus
     *
     * @param string $surveyCompletionStatus                 
     */
    public function setSurveyCompletionStatus($surveyCompletionStatus)
    {
        $this->surveyCompletionStatus = $surveyCompletionStatus;
        
        return $this;
    }

    /**
     * Get surveyCompletionStatus
     *
     * @return string
     */
    public function getSurveyCompletionStatus()
    {
        return $this->surveyCompletionStatus;
    }
    
    /**
     * Set surveyOptOutStatus
     *
     * @param string $surveyOptOutStatus                 
     */
    public function setSurveyOptOutStatus($surveyOptOutStatus)
    {
        $this->surveyOptOutStatus = $surveyOptOutStatus;
        
        return $this;
    }

    /**
     * Get surveyOptOutStatus
     *
     * @return string
     */
    public function getSurveyOptOutStatus()
    {
        return $this->surveyOptOutStatus;
    }
}
