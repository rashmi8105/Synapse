<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\Person;

/**
 * @ORM\MappedSuperclass
 *
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class BaseEntity
{

    /**
     * Person object that created a BaseEntity
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * DateTime that a BaseEntity was created
     *
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * Person object that modified a BaseEntity
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="modified_by", referencedColumnName="id")
     */
    private $modifiedBy;

    /**
     * DateTime that a BaseEntity was modified at
     *
     * @ORM\Column(type="datetime", name="modified_at", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $modifiedAt;

    /**
     * Person object that deleted a BaseEntity
     *
     * @Gedmo\Blameable(on="update", field={"deletedAt"})
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
     */
    private $deletedBy;

    /**
     * DateTime that a BaseEntity was deleted at
     *
     * @ORM\Column(type="datetime", name="deleted_at", nullable=true)
     */
    private $deletedAt;

    /**
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdBy
     *
     * @return Person
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param string $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return Person
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param Person $modifiedBy
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return Person
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param Person $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }


}
