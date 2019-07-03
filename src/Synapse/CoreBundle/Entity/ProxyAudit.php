<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * ProxyAudit
 *
 * @ORM\Table(name="proxy_audit", indexes={@ORM\Index(name="fk_proxy_audit_proxy_log1_idx", columns={"proxy_log_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ProxyAuditRepository")
 */
class ProxyAudit extends BaseEntity
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
     * @var \ProxyLog @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ProxyLog")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="proxy_log_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $proxyLogId;

    /**
     *
     * @var enum @ORM\Column(name="action", type="string", columnDefinition="enum('insert', 'update', 'delete')")
     */
    private $action;

    /**
     *
     * @var string @ORM\Column(name="resource", type="string", length=45, nullable=true)
     */
    private $resource;

    /**
     *
     * @var string @ORM\Column(name="json_text_old", type="string", length=4000, nullable=true)
     */
    private $jsonTextOld;

    /**
     *
     * @var string @ORM\Column(name="json_text_new", type="string", length=4000, nullable=true)
     */
    private $jsonTextNew;

    /**
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

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
     * Set proxyLogId
     *
     * @param \Synapse\CoreBundle\Entity\ProxyLog $proxyLogId            
     * @return ProxyLog
     */
    public function setProxyLogId(\Synapse\CoreBundle\Entity\ProxyLog $proxyLogId)
    {
        $this->proxyLogId = $proxyLogId;
        
        return $this;
    }

    /**
     * Get proxyLogId
     *
     * @return \Synapse\CoreBundle\Entity\ProxyLog
     */
    public function getProxyLogId()
    {
        return $this->proxyLogId;
    }

    /**
     * Set action
     *
     * @param string $action            
     * @return ProxyAudit
     */
    public function setAction($action)
    {
        $this->action = $action;
        
        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set resource
     *
     * @param string $resource            
     * @return ProxyAudit
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        
        return $this;
    }

    /**
     * Get resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set jsonTextOld
     *
     * @param string $jsonTextOld            
     * @return ProxyAudit
     */
    public function setJsonTextOld($jsonTextOld)
    {
        $this->jsonTextOld = $jsonTextOld;
        
        return $this;
    }

    /**
     * Get jsonTextOld
     *
     * @return string
     */
    public function getJsonTextOld()
    {
        return $this->jsonTextOld;
    }

    /**
     * Set jsonTextNew
     *
     * @param string $jsonTextNew            
     * @return ProxyAudit
     */
    public function setJsonTextNew($jsonTextNew)
    {
        $this->jsonTextNew = $jsonTextNew;
        
        return $this;
    }

    /**
     * Get jsonTextNew
     *
     * @return string
     */
    public function getJsonTextNew()
    {
        return $this->jsonTextNew;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn            
     * @return ProxyAudit
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;
        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \Datetime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }
}