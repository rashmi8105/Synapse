<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgSurveyReportAccessHistory
 *
 * @ORM\Table(name="org_survey_report_access_history")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgSurveyReportAccessHistoryRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgSurveyReportAccessHistory extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     *     
     */
    private $orgId;

    /**
     *
     * @var integer @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $person;
    
    
    /**
     * 
     * @var integer @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $student;

    /**
     *
     * @var integer @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $survey;

    /**
     *
     * @var integer @ORM\Id
     *      @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\Year")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="year_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $year;

    /**
     *
     * @var integer @ORM\Id
     *      @ORM\Column(name="cohort_code", type="integer", nullable=true)
     */
    private $cohortCode;

    /**
     *
     * @var \Date @ORM\Column(name="last_accessed_on", type="datetime", nullable=true)
     */
    private $lastAccessedOn;

    public function getId()
    {
        return $this;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     *
     */
    public function setOrganization($orgId)
    {
        $this->orgId = $orgId;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->orgId;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     *
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * Get person
     *
     * @return Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     *
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * Get Survey
     *
     * @return Synapse\CoreBundle\Entity\Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set survey
     *
     * @param Synapse\AcademicBundle\Entity\Year $year            
     *
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Get Year
     *
     * @return Synapse\AcademicBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     *
     * @param int $id            
     */
    public function setCohortCode($cohortCode)
    {
        $this->cohortCode = $cohortCode;
    }

    /**
     *
     * @return int
     */
    public function getCohortCode()
    {
        return $this->cohortCode;
    }

    /**
     * Set lastAccessedOn
     *
     * @param \DateTime $lastAccessedOn            
     *
     */
    public function setLastAccessedOn($lastAccessedOn)
    {
        $this->lastAccessedOn = $lastAccessedOn;
    }

    /**
     * Get lastAccessedOn
     *
     * @return \Datetime
     */
    public function getLastAccessedOn()
    {
        return $this->lastAccessedOn;
    }

    
    /**
     * @param int $student
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * @return int
     */
    public function getStudent()
    {
        return $this->student;
    }


}