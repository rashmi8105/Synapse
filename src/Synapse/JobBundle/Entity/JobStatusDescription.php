<?php

namespace Synapse\JobBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * JobStatusDescription
 *
 * @ORM\Table(name="job_status_description")
 * @ORM\Entity(repositoryClass="Synapse\JobBundle\Repository\JobStatusDescriptionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class JobStatusDescription extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="job_status_description", type="string", length=50, nullable=true)
     */
    private $jobStatusDescription;

    /**
     * Get the job status description id
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
     * Get Job status description
     *
     * @return string
     */
    public function getJobStatusDescription()
    {
        return $this->jobStatusDescription;
    }

    /**
     * Set Job status description
     *
     * @param string $jobStatusDescription
     */
    public function setJobStatusDescription($jobStatusDescription)
    {
        $this->jobStatusDescription = $jobStatusDescription;
    }
}