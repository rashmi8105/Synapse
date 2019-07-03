<?php

namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * RiskVariableCategory
 *
 * @ORM\Table(name="risk_variable_category", indexes={@ORM\Index(name="fk_risk_model_bucket_category_risk_variable1_idx", columns={"risk_variable_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskVariableCategoryRepository")
 */
class RiskVariableCategory extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="bucket_value", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $bucketValue;

    /**
     * @var string
     *
     * @ORM\Column(name="option_value", type="string", length=200, precision=0, scale=0, nullable=true, unique=false)
     */
    private $optionValue;

    /**
     * @var \Synapse\RiskBundle\Entity\RiskVariable
     *
     * @ORM\ManyToOne(targetEntity="Synapse\RiskBundle\Entity\RiskVariable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risk_variable_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $riskVariable;



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
     * Set bucketValue
     *
     * @param integer $bucketValue
     * @return RiskVariableCategory
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
     * Set optionValue
     *
     * @param string $optionValue
     * @return RiskVariableCategory
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    /**
     * Get optionValue
     *
     * @return string 
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set riskVariable
     *
     * @param \Synapse\RiskBundle\Entity\RiskVariable $riskVariable
     * @return RiskVariableCategory
     */
    public function setRiskVariable(\Synapse\RiskBundle\Entity\RiskVariable $riskVariable = null)
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
