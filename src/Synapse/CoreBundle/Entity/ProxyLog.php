<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * ProxyLog
 *
 * @ORM\Table(name="proxy_log", indexes={@ORM\Index(name="fk_proxy_log_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_proxy_log_person2_idx", columns={"person_id_proxied_for"}), @ORM\Index(name="fk_proxy_log_ebi_users1_idx", columns={"ebi_users_id"}), @ORM\Index(name="fk_proxy_log_person1_idx", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ProxyLogRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ProxyLog extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $organizationId;

    /**
     *
     * @var \EbiUsers @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiUsers")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_users_id", nullable=true, referencedColumnName="id")
     *      })
     */
    private $ebiUsersId;

    /**
     *
     * @var \Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_proxied_for", nullable=false, referencedColumnName="id")
     *      })
     */
    private $personIdProxiedFor;

    /**
     *
     * @var \Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $personId;

    /**
     * @ORM\Column(name="login_date_time", type="datetime", nullable=true)
     */
    private $loginDateTime;

    /**
     * @ORM\Column(name="logoff_date_time", type="datetime", nullable=true)
     */
    private $logoffDateTime;

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
     * Set organizationId
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organizationId            
     * @return Organization
     */
    public function setOrganizationId(\Synapse\CoreBundle\Entity\Organization $organizationId)
    {
        $this->organizationId = $organizationId;
        
        return $this;
    }

    /**
     * Get organizationId
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set ebiUsersId
     *
     * @param \Synapse\CoreBundle\Entity\EbiUsers $ebiUsersId            
     * @return EbiUsers
     */
    public function setEbiUsersId(\Synapse\CoreBundle\Entity\EbiUsers $ebiUsersId)
    {
        $this->ebiUsersId = $ebiUsersId;
        
        return $this;
    }

    /**
     * Get ebiUsersId
     *
     * @return \Synapse\CoreBundle\Entity\EbiUsers
     */
    public function getEbiUsersId()
    {
        return $this->ebiUsersId;
    }

    /**
     * Set personIdProxiedFor
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdProxiedFor            
     * @return Person
     */
    public function setPersonIdProxiedFor(\Synapse\CoreBundle\Entity\Person $personIdProxiedFor)
    {
        $this->personIdProxiedFor = $personIdProxiedFor;
        
        return $this;
    }

    /**
     * Get personIdProxiedFor
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdProxiedFor()
    {
        return $this->personIdProxiedFor;
    }

    /**
     * Set personId
     *
     * @param \Synapse\CoreBundle\Entity\Person $personId            
     * @return Person
     */
    public function setPersonId(\Synapse\CoreBundle\Entity\Person $personId)
    {
        $this->personId = $personId;
        
        return $this;
    }

    /**
     * Get personId
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set loginDateTime
     *
     * @param \DateTime $loginDateTime            
     * @return ProxyLog
     */
    public function setLoginDateTime($loginDateTime)
    {
        $this->loginDateTime = $loginDateTime;
        return $this;
    }

    /**
     * Get loginDateTime
     *
     * @return \Datetime
     */
    public function getLoginDateTime()
    {
        return $this->loginDateTime;
    }

    /**
     * Set logoffDateTime
     *
     * @param \DateTime $logoffDateTime            
     * @return ProxyLog
     */
    public function setLogoffDateTime($logoffDateTime)
    {
        $this->logoffDateTime = $logoffDateTime;
        return $this;
    }

    /**
     * Get logoffDateTime
     *
     * @return \Datetime
     */
    public function getLogoffDateTime()
    {
        return $this->logoffDateTime;
    }
}