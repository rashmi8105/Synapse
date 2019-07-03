<?php

namespace Synapse\JobBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * JobType
 *
 * @ORM\Table(name="job_type")
 * @ORM\Entity(repositoryClass="Synapse\JobBundle\Repository\JobTypeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class JobType extends BaseEntity
{

    /**
     * @var integer @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="job_type", type="string", length=300, nullable=true)
     */
    private $jobType;

    /**
     * Get job type id
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
     * Get Job Type
     *
     * @return string
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * Set Job Type
     *
     * @param string $jobType
     */
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }
}