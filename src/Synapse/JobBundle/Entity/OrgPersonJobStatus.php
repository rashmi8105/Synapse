<?php
namespace Synapse\JobBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;

/**
 * OrgPersonJobStatus
 *
 * @ORM\Table(name="org_person_job_status",indexes={@ORM\Index(name="fk_org_person_job_status_organization1", columns={"organization_id"}), @ORM\Index(name="fk_org_person_job_status_person1", columns={"person_id"})}))
 * @ORM\Entity(repositoryClass="Synapse\JobBundle\Repository\OrgPersonJobStatusRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPersonJobStatus extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     */
    private $person;

    /**
     * @var Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     */
    private $organization;

    /**
     * @var JobStatusDescription
     * @ORM\ManyToOne(targetEntity="Synapse\JobBundle\Entity\JobStatusDescription")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="job_status_id", referencedColumnName="id")
     *      })
     */
    private $jobStatus;

    /**
     * @var JobType
     * @ORM\ManyToOne(targetEntity="Synapse\JobBundle\Entity\JobType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="job_type_id", referencedColumnName="id")
     *      })
     */
    private $jobType;

    /**
     * @var string
     * @ORM\Column(name="job_id", type="string", length=50, nullable=true)
     */
    private $jobId;

    /**
     * @var string
     * @ORM\Column(name="failure_description", type="text", nullable=true)
     */
    private $failureDescription;

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set person
     *
     * @param Person $person
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;
    }

    /**
     * @return JobStatusDescription
     */
    public function getJobStatus()
    {
        return $this->jobStatus;
    }

    /**
     * Set job status description
     *
     * @param JobStatusDescription $jobStatus
     * @return JobStatusDescription
     */
    public function setJobStatus(JobStatusDescription $jobStatus = null)
    {
        $this->jobStatus = $jobStatus;
    }

    /**
     * @return JobType
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * Set job type
     *
     * @param JobType $jobType
     * @return JobType
     */
    public function setJobType(JobType $jobType = null)
    {
        $this->jobType = $jobType;
    }

    /**
     * Get Job id
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * @param string $jobId
     */
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * @return string
     */
    public function getFailureDescription()
    {
        return $this->failureDescription;
    }

    /**
     * @param string $failureDescription
     */
    public function setFailureDescription($failureDescription)
    {
        $this->failureDescription = $failureDescription;
    }
}