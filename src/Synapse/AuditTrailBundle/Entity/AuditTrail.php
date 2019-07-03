<?php
namespace Synapse\AuditTrailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * Year
 *
 * @ORM\Table(name="audit_trail")
 * @ORM\Entity(repositoryClass="Synapse\AuditTrailBundle\Repository\AuditTrailRepository")
 */
class AuditTrail extends BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="audited_at", type="datetime")
     */
    private $auditedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $person;

    /**
     * @ORM\Column(type="string")
     */
    private $route;

    /**
     * @ORM\Column(type="string")
     */
    private $class;

    /**
     * @ORM\Column(type="string")
     */
    private $method;

    /**
     * @ORM\Column(type="json_array")
     */
    private $request;

    /**
     * @ORM\Column(name="unit_of_work", type="json_array")
     */
    private $unitOfWork;

    /**
     * @ORM\Column(type="string", nullable=true, columnDefinition="enum('SUCCESS', 'FAIL')")
     */
    private $status;
    
   /**
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="proxy_by", referencedColumnName="id")
     */
    private $proxyBy;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getAuditedAt()
    {
        return $this->auditedAt;
    }

    public function setAuditedAt($auditedAt)
    {
        $this->auditedAt = $auditedAt;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function setPerson($person)
    {
        $this->person = $person;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setUnitOfWork($unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
    
    public function getProxyBy()
    {
        return $this->proxyBy;
    }
    
    public function setProxyBy($proxyBy)
    {
        $this->proxyBy = $proxyBy;
    }

}
