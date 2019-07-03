<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\ReportsBundle\Entity\Reports;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * reports
 *
 * @ORM\Table(name="org_calc_flags_student_reports")
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class OrgCalcFlagsStudentReports extends BaseEntity
{


    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     * @JMS\Expose
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     * @JMS\Expose
     */
    private $person;
    /**
     *
     * @var \Synapse\ReportsBundle\Entity\Reports @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\Reports")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $reports;


    /**
     * @ORM\Column(name="calculated_at", type="datetime", nullable=true)
     * @JMS\Expose
     */
    private $calculatedAt;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     * @JMS\Expose
     */
    private $survey;

    /**
     *
     * @var string @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $fileName;

    /**
     *
     * @var boolean @ORM\Column(name="in_progress_email_sent", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $inProgressEmailSent;

    /**
     *
     * @var boolean @ORM\Column(name="completion_email_sent", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $completionEmailSent;


    /**
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgCalcFlagsStudentReports
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }


    /**
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
     * @return OrgCalcFlagsStudentReports
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }


    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }


    /**
     * Set reports
     *
     * @param \Synapse\ReportsBundle\Entity\reports $reports
     * @return OrgCalcFlagsStudentReports
     */
    public function setReports(Reports $reports = null)
    {
        $this->reports = $reports;

        return $this;
    }


    /**
     * Get reports
     *
     * @return \Synapse\ReportsBundle\Entity\reports
     */
    public function getReports()
    {
        return $this->reports;
    }


    /**
     * Set calculatedAt
     *
     * @param \DateTime $calculatedAt
     * @return OrgCalcFlagsStudentReports
     */
    public function setCalculatedAt($calculatedAt)
    {
        $this->calculatedAt = $calculatedAt;
        return $this;
    }


    /**
     * Get calculatedAt
     *
     * @return \Datetime
     */
    public function getCalculatedAt()
    {
        return $this->calculatedAt;
    }


    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey
     * @return OrgCalcFlagsStudentReports
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
     * Set fileName
     * @param string $fileName
     * @return OrgCalcFlagsStudentReports
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }


    /**
     * Get fileName
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }


    /**
     * @return boolean
     */
    public function isInProgressEmailSent()
    {
        return $this->inProgressEmailSent;

    }


    /**
     * @param boolean $inProgressEmailSent
     */
    public function setInProgressEmailSent($inProgressEmailSent)
    {
        $this->inProgressEmailSent = $inProgressEmailSent;
    }


    /**
     * @return boolean
     */
    public function isCompletionEmailSent()
    {
        return $this->completionEmailSent;
    }


    /**
     * @param boolean $completionEmailSent
     */
    public function setCompletionEmailSent($completionEmailSent)
    {
        $this->completionEmailSent = $completionEmailSent;
    }

}