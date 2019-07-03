<?php

namespace Synapse\CoreBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OAuth2\OAuth2;

/**
 * @ORM\Table(name="Client")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ClientRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Client extends BaseClient
{

    public function __construct()
    {
        parent::__construct();

        $this->allowedGrantTypes[] = OAuth2::GRANT_TYPE_REFRESH_TOKEN;
        $this->allowedGrantTypes[] = OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $person;

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
     * Set person
     *
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * Get Person
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
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