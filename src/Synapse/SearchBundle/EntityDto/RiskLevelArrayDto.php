<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class RiskLevelArrayDto
{

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SearchBundle\EntityDto\RisklevelsDto>")
     *     
     *     
     */
    private $riskLevels;

    /**
     *
     * @return string
     */
    public function getRiskLevels()
    {
        return $this->riskLevels;
    }

    /**
     *
     * @param mixed $riskLevels            
     */
    public function setRiskLevels($riskLevels)
    {
        $this->riskLevels = $riskLevels;
    }
}