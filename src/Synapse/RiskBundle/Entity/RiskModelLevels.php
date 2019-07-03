<?php
namespace Synapse\RiskBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * RiskModelLevels
 *
 * @ORM\Table(name="risk_model_levels")
 * @ORM\Table(name="risk_model_levels", indexes={@ORM\Index(name="fk_risk_model_levels_risk_model_master1_idx",columns={"risk_model_id"}),@ORM\Index(name="fk_risk_model_levels_risk_level1_idx",columns={"risk_level"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskModelLevelsRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class RiskModelLevels extends BaseEntity
{
    /**
     *
     * @var \RiskModelMaster @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\RiskBundle\Entity\RiskModelMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_model_id", referencedColumnName="id")
     *      })
     */
    private $riskModel;
    
    /**
     *
     * @var \RiskLevel @ORM\Id
     *      @ORM\GeneratedValue(strategy="NONE")
     *      @ORM\OneToOne(targetEntity="Synapse\RiskBundle\Entity\RiskLevels")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risk_level", referencedColumnName="id")
     *      })
     */
    private $riskLevel;

    /**
     *
     * @var string @ORM\Column(name="min", type="decimal", precision=6, scale=4, nullable=true)
     */
    private $min;

    /**
     *
     * @var string @ORM\Column(name="max", type="decimal", precision=6, scale=4, nullable=true)
     */
    private $max;

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
     * @param integer $riskModel            
     */
    public function setRiskModel($riskModel)
    {
        $this->riskModel = $riskModel;
    }

    /**
     *
     * @return integer
     */
    public function getRiskModel()
    {
        return $this->riskModel;
    }

    /**
     *
     * @param integer $riskLevel            
     */
    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    /**
     *
     * @return riskLevel
     */
    public function getRiskLevel()
    {
        return $this->riskLevel;
    }

    /**
     * Set min
     */
    public function setMin($min)
    {
        $this->min = $min;
        
        return $this;
    }

    /**
     * Get min
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     */
    public function setMax($max)
    {
        $this->max = $max;
        
        return $this;
    }

    /**
     * Get max
     */
    public function getMax()
    {
        return $this->max;
    }
}
