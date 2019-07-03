<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class RiskGroupModelDto
{

    /**
     * $riskGroupId
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalAssignedModelsCount;

    /**
     * $riskGroupId
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentsWithNoRisk;

    /**
     * $riskGroupId
     *
     * @var integer 
     * @JMS\Type("array<Synapse\RiskBundle\EntityDto\RiskGroupModelAssignmentsDto>")
     */
    private $riskModelAssignments;

    /**
     * @param int $riskModelAssignments
     */
    public function setRiskModelAssignments($riskModelAssignments)
    {
        $this->riskModelAssignments = $riskModelAssignments;
    }

    /**
     * @return int
     */
    public function getRiskModelAssignments()
    {
        return $this->riskModelAssignments;
    }

    /**
     * @param int $totalAssignedModelsCount
     */
    public function setTotalAssignedModelsCount($totalAssignedModelsCount)
    {
        $this->totalAssignedModelsCount = $totalAssignedModelsCount;
    }

    /**
     * @return int
     */
    public function getTotalAssignedModelsCount()
    {
        return $this->totalAssignedModelsCount;
    }

    /**
     * @param int $studentsWithNoRisk
     */
    public function setStudentsWithNoRisk($studentsWithNoRisk)
    {
        $this->studentsWithNoRisk = $studentsWithNoRisk;
    }

    /**
     * @return int
     */
    public function getStudentsWithNoRisk()
    {
        return $this->studentsWithNoRisk;
    }


}