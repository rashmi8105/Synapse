<?php

namespace Synapse\CoreBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="AuthCode")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AuthCodeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    private $organization;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="modified_by", referencedColumnName="id")
     */
    private $modifiedBy;

    /**
     * @ORM\Column(type="datetime", name="modified_at", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $modifiedAt;

    /**
     * @Gedmo\Blameable(on="update", field={"deletedAt"})
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
     */
    private $deletedBy;

    /**
     * @ORM\Column(type="datetime", name="deleted_at", nullable=true)
     */
    private $deletedAt;


    /**
     * Set id
     *
     * @param $id
     */
    public function setId($id){
        $this->id = $id;
    }

    /**
     * Set Organization
     *
     * @param Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * Get Organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }



    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param Person $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * Get createdBy
     *
     * @return null|Person
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set modifiedBy
     *
     * @param Person $modifiedBy
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * Get modifiedBy
     *
     * @return Person
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set deletedBy
     *
     * @param Person $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }

    /**
     * Get deletedBy
     *
     * @return null | Person
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }
}