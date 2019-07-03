<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgCalcFlagsRisk
 *
 * @ORM\Table(name="org_calc_flags_risk", indexes={@ORM\Index(name="org_person_idx", columns={"org_id", "person_id"}), @ORM\Index(name="person_idx", columns={"person_id"}), @ORM\Index(name="created_at_idx", columns={"created_at"}), @ORM\Index(name="modified_at_idx", columns={"modified_at"}), @ORM\Index(name="calculated_at_idx", columns={"calculated_at"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\OrgCalcFlagsRiskRepository")
 */
class OrgCalcFlagsRisk extends BaseEntity
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
     * @var integer @ORM\Column(name="org_id", type="integer", nullable=true)
     */
    private $orgId;

    /**
     *
     * @var integer @ORM\Column(name="person_id", type="integer", nullable=true)
     */
    private $personId;

    /**
     *
     * @var \DateTime @ORM\Column(name="calculated_at", type="datetime", nullable=true)
     */
    private $calculatedAt;

    /**
     *
     * @param \DateTime $calculatedAt            
     */
    public function setCalculatedAt($calculatedAt)
    {
        $this->calculatedAt = $calculatedAt;
        
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCalculatedAt()
    {
        return $this->calculatedAt;
    }

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->orgId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->orgId;
    }

    /**
     *
     * @param int $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
        
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }
}
