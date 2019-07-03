<?php

namespace Synapse\JobBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * JobType
 *
 * @ORM\Table(name="job_type_blocked_mapping")
 * @ORM\Entity(repositoryClass="Synapse\JobBundle\Repository\JobTypeBlockedMappingRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class JobTypeBlockedMapping extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var JobType
     * @ORM\ManyToOne(targetEntity="Synapse\JobBundle\Entity\JobType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="job_type_id", referencedColumnName="id")
     *      })
     */
    private $jobType;

    /**
     * @var JobType
     * @ORM\ManyToOne(targetEntity="Synapse\JobBundle\Entity\JobType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="blocked_by_job_type_id", referencedColumnName="id")
     *      })
     */
    private $blockedByJobType;

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
     * @return JobType
     */
    public function getBlockedByJobType()
    {
        return $this->blockedByJobType;
    }

    /**
     * Set job type
     *
     * @param JobType $blockedByJobType
     * @return JobType
     */
    public function setBlockedByJobType(JobType $blockedByJobType = null)
    {
        $this->blockedByJobType = $blockedByJobType;
    }
}