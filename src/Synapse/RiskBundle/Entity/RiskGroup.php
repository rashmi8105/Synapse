<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * RiskGroup
 *
 * @ORM\Table(name="risk_group")
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskGroupRepository")
 */
class RiskGroup extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="risk_group_key", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $riskGroupKey;

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
     * Set riskGroupKey
     *
     * @param string $riskGroupKey            
     * @return RiskGroup
     */
    public function setRiskGroupKey($riskGroupKey)
    {
        $this->riskGroupKey = $riskGroupKey;
        
        return $this;
    }

    /**
     * Get riskGroupKey
     *
     * @return string
     */
    public function getRiskGroupKey()
    {
        return $this->riskGroupKey;
    }
}
