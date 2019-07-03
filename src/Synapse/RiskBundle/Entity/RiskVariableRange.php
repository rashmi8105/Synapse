<?php

namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RiskVariableRange
 *
 * @ORM\Table(name="risk_variable_range", indexes={@ORM\Index(name="fk_risk_model_bucket_range_risk_variable1_idx", columns={"risk_variable_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskVariableRangeRepository")
 */
class RiskVariableRange
{
    /**
     * @var integer
     *
     * @ORM\Column(name="bucket_value", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $bucketValue;

    /**
     * @var string
     *
     * @ORM\Column(name="min", type="decimal", precision=6, scale=4, nullable=true, unique=false)
     */
    private $min;

    /**
     * @var string
     *
     * @ORM\Column(name="max", type="decimal", precision=6, scale=4, nullable=true, unique=false)
     */
    private $max;

    /**
     * @var \Synapse\RiskBundle\Entity\RiskVariable
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Synapse\RiskBundle\Entity\RiskVariable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risk_variable_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $riskVariable;



    /**
     * Set bucketValue
     *
     * @param integer $bucketValue
     * @return RiskVariableRange
     */
    public function setBucketValue($bucketValue)
    {
        $this->bucketValue = $bucketValue;

        return $this;
    }

    /**
     * Get bucketValue
     *
     * @return integer 
     */
    public function getBucketValue()
    {
        return $this->bucketValue;
    }

    /**
     * Set min
     *
     * @param string $min
     * @return RiskVariableRange
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return string 
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param string $max
     * @return RiskVariableRange
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return string 
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set riskVariable
     *
     * @param \Synapse\RiskBundle\Entity\RiskVariable $riskVariable
     * @return RiskVariableRange
     */
    public function setRiskVariable(\Synapse\RiskBundle\Entity\RiskVariable $riskVariable)
    {
        $this->riskVariable = $riskVariable;

        return $this;
    }

    /**
     * Get riskVariable
     *
     * @return \Synapse\RiskBundle\Entity\RiskVariable 
     */
    public function getRiskVariable()
    {
        return $this->riskVariable;
    }
}
