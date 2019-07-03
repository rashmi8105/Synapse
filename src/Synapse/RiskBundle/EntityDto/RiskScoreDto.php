<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class RiskScoreDto
{
    /**
     * $createdAt
     *
     * @var integer @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     *
     *
     */
    private $createdAt;
    
    /**
     * $riskScoreValue
     *
     * @var integer @JMS\Type("double")
     *     
     *     
     */
    private $riskScoreValue;
    
    /** $riskScoreValue
    *
    * @var integer @JMS\Type("double")
    *
    *
    */
    private $riskLevel;

    /**
     * @param int $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param int $riskScoreValue
     */
    public function setRiskScoreValue($riskScoreValue)
    {
        $this->riskScoreValue = $riskScoreValue;
    }

    /**
     * @return int
     */
    public function getRiskScoreValue()
    {
        return $this->riskScoreValue;
    }

    /**
     * @param int $riskLevel
     */
    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    /**
     * @return int
     */
    public function getRiskLevel()
    {
        return $this->riskLevel;
    }


}