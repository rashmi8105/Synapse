<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Risk Variables
 *
 * @package Synapse\RiskBundle\Entity
 */
class RiskVariablesResponseDto
{

    /**
     * total_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalCount;

    /**
     * total_archived_count
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalArchivedCount;
    
    /**
     * risk_variables
     *
     * @var Object @JMS\Type("array<Synapse\RiskBundle\EntityDto\RiskVariablesListDto>")
     *
     *
     */
    private $riskVariables;

    /**
     *
     * @param integer $totalCount
     */
    public function setTotalCount($totalCount)
    {
    	$this->totalCount = $totalCount;
    }

    /**
     *
     * @param integer $totalArchivedCount
     */
    public function setTotalArchivedCount($totalArchivedCount)
    {
    	$this->totalArchivedCount = $totalArchivedCount;
    }
    
    /**
     *
     * @param Object $riskVariables
     */
    public function setRiskVariables($riskVariables)
    {
    	$this->riskVariables = $riskVariables;
    }

    /**
     * @return Object
     */
    public function getRiskVariables()
    {
        return $this->riskVariables;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

}