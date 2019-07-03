<?php
namespace Synapse\JobBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;

/**
 * OrgPersonJobQueue
 *
 * @ORM\Table(name="org_person_job_queue",indexes={@ORM\Index(name="fk_org_person_job_queue_organization1", columns={"organization_id"}), @ORM\Index(name="fk_org_person_job_queue_person1", columns={"person_id"})}))
 * @ORM\Entity(repositoryClass="Synapse\JobBundle\Repository\OrgPersonJobQueueRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPersonJobQueue extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Assert\NotNull()
     */
    private $id;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @Assert\NotNull()
     */
    private $person;

    /**
     * @var Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     * @Assert\NotNull()
     */
    private $organization;


    /**
     * @var JobType
     * @ORM\ManyToOne(targetEntity="Synapse\JobBundle\Entity\JobType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="job_type_id", referencedColumnName="id")
     *      })
     * @Assert\NotNull()
     */
    private $jobType;

    /**
     * @var string
     * @ORM\Column(name="job_id", type="string", length=50, nullable=true)
     * @Assert\NotNull()
     */
    private $jobId;

    /**
     * @var string
     * @ORM\Column(name="job_queued_info", type="text", nullable=true)
     */
    private $jobQueuedInfo;

    /**
     * @var integer
     * @ORM\Column(name="queued_status", type="integer")
     */
    private $queuedStatus;

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
    public function getJobQueuedInfo()
    {
        return $this->jobQueuedInfo;
    }

    /**
     * @param string $jobQueuedInfo
     */
    public function setJobQueuedInfo($jobQueuedInfo)
    {
        $this->jobQueuedInfo = $jobQueuedInfo;
    }

    /**
     * Get queuedStatus
     *
     * @return integer
     */
    public function getQueuedStatus()
    {
        return $this->queuedStatus;
    }

    /**
     * @param int $queuedStatus
     */
    public function setQueuedStatus($queuedStatus)
    {
        $this->queuedStatus = $queuedStatus;
    }
}