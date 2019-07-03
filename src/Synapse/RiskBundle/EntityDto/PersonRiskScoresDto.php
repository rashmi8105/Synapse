<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
class PersonRiskScoresDto
{
    
    /**
     * $riskModelName
     *
     * @var integer @JMS\Type("string")
     *
     *
     */
    private $riskModelName;
    
    /**
     * $personId
     *
     * @var integer @JMS\Type("integer")
     *
     *
     */
    private $personId;
    

    /**
     * $riskScore
     *
     * @JMS\Type("array<Synapse\RiskBundle\EntityDto\RiskScoreDto>")
     *
     *
     */
    private $riskScore;

    /**
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param int $riskModelName
     */
    public function setRiskModelName($riskModelName)
    {
        $this->riskModelName = $riskModelName;
    }

    /**
     * @return int
     */
    public function getRiskModelName()
    {
        return $this->riskModelName;
    }

    /**
     * @param mixed $riskScore
     */
    public function setRiskScore($riskScore)
    {
        $this->riskScore = $riskScore;
    }

    /**
     * @return mixed
     */
    public function getRiskScore()
    {
        return $this->riskScore;
    }


    
    
}