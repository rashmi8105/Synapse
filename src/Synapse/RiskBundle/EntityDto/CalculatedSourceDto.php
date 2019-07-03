<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CalculatedSourceDto
{

    /**
     * $personId
     *
     * @var integer @JMS\Type("integer")
     *     
     *     
     */
    private $riskVariableId;

    /**
     * $personId
     *
     * @var integer @JMS\Type("integer")
     *     
     *     
     */
    private $calcBucketValue;

    /**
     * $personId
     *
     * @var integer @JMS\Type("double")
     *     
     *     
     */
    private $calcWeight;

    /**
     * $personId
     *
     * @var integer @JMS\Type("double")
     *     
     *     
     */
    private $riskSourceValue;
    
    /**
     * $createdAt
     *
     * @var DateTime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     *
     *
     */
    private $createdAt;

    /**
     * @param int $calcBucketValue
     */
    public function setCalcBucketValue($calcBucketValue)
    {
        $this->calcBucketValue = $calcBucketValue;
    }

    /**
     * @return int
     */
    public function getCalcBucketValue()
    {
        return $this->calcBucketValue;
    }

    /**
     * @param int $calcWeight
     */
    public function setCalcWeight($calcWeight)
    {
        $this->calcWeight = $calcWeight;
    }

    /**
     * @return int
     */
    public function getCalcWeight()
    {
        return $this->calcWeight;
    }

    /**
     * @param int $riskSourceValue
     */
    public function setRiskSourceValue($riskSourceValue)
    {
        $this->riskSourceValue = $riskSourceValue;
    }

    /**
     * @return int
     */
    public function getRiskSourceValue()
    {
        return $this->riskSourceValue;
    }

    /**
     * @param int $riskVariableId
     */
    public function setRiskVariableId($riskVariableId)
    {
        $this->riskVariableId = $riskVariableId;
    }

    /**
     * @return int
     */
    public function getRiskVariableId()
    {
        return $this->riskVariableId;
    }

    
    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}