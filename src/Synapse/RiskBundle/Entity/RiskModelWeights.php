<?php

namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RiskModelWeights
 *
 * @ORM\Table(name="risk_model_weights", indexes={@ORM\Index(name="fk_risk_model_bucket_risk_model_master1_idx", columns={"risk_model_id"}), @ORM\Index(name="fk_risk_model_bucket_risk_variable1_idx", columns={"risk_variable_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskModelWeightsRepository")
 */
class RiskModelWeights
{
    
    /**
     * @var \Synapse\RiskBundle\Entity\RiskModelMaster
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Synapse\RiskBundle\Entity\RiskModelMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risk_model_id", referencedColumnName="id", nullable=false)
     * })
     */    
    private $riskModel;
    /**
     * @var \Synapse\RiskBundle\Entity\RiskVariable
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Synapse\RiskBundle\Entity\RiskVariable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risk_variable_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $riskVariable;
    
    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $weight;

    /**
     * @param string $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param \Synapse\RiskBundle\Entity\RiskModelMaster $riskModel
     */
    public function setRiskModel($riskModel)
    {
        $this->riskModel = $riskModel;
    }

    /**
     * @return \Synapse\RiskBundle\Entity\RiskModelMaster
     */
    public function getRiskModel()
    {
        return $this->riskModel;
    }

    /**
     * @param \Synapse\RiskBundle\Entity\RiskVariable $riskVariable
     */
    public function setRiskVariable($riskVariable)
    {
        $this->riskVariable = $riskVariable;
    }

    /**
     * @return \Synapse\RiskBundle\Entity\RiskVariable
     */
    public function getRiskVariable()
    {
        return $this->riskVariable;
    }


}
