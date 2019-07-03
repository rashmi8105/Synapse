<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class RiskGroupModelAssignmentsDto
{

    /**
     * $riskGroupId
     *
     * @var integer @JMS\Type("integer")
     */
    private $riskGroupId;

    /**
     * $riskGroupName
     *
     * @var string @JMS\Type("string")
     */
    private $riskGroupName;

    /**
     * $riskGroupDescription
     *
     * @var string @JMS\Type("string")
     */
    private $riskGroupDescription;

    /**
     * $riskModelId
     *
     * @var integer @JMS\Type("integer")
     */
    private $riskModelId;

    /**
     * $riskModelName
     *
     * @var string @JMS\Type("string")
     */
    private $riskModelName;

    /**
     * $modelState
     *
     * @var string @JMS\Type("string")
     */
    private $modelState;

    /**
     * $calculationStartDate
     *
     * @var date @JMS\Type("DateTime")
     *     
     */
    private $calculationStartDate;

    /**
     * $calculationStopDate
     *
     * @var date @JMS\Type("DateTime")
     *     
     */
    private $calculationStopDate;

    /**
     * $enrollmentEndDate
     *
     * @var date @JMS\Type("DateTime")
     *     
     */
    private $enrollmentEndDate;

    /**
     * $studentsCount
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentsCount;

    /**
     * @param \Synapse\RiskBundle\EntityDto\date $calculationStartDate
     */
    public function setCalculationStartDate($calculationStartDate)
    {
        $this->calculationStartDate = $calculationStartDate;
    }

    /**
     * @return \Synapse\RiskBundle\EntityDto\date
     */
    public function getCalculationStartDate()
    {
        return $this->calculationStartDate;
    }

    /**
     * @param int $studentsCount
     */
    public function setStudentsCount($studentsCount)
    {
        $this->studentsCount = $studentsCount;
    }

    /**
     * @return int
     */
    public function getStudentsCount()
    {
        return $this->studentsCount;
    }

    /**
     * @param string $riskModelName
     */
    public function setRiskModelName($riskModelName)
    {
        $this->riskModelName = $riskModelName;
    }

    /**
     * @return string
     */
    public function getRiskModelName()
    {
        return $this->riskModelName;
    }

    /**
     * @param int $riskModelId
     */
    public function setRiskModelId($riskModelId)
    {
        $this->riskModelId = $riskModelId;
    }

    /**
     * @return int
     */
    public function getRiskModelId()
    {
        return $this->riskModelId;
    }

    /**
     * @param string $riskGroupName
     */
    public function setRiskGroupName($riskGroupName)
    {
        $this->riskGroupName = $riskGroupName;
    }

    /**
     * @return string
     */
    public function getRiskGroupName()
    {
        return $this->riskGroupName;
    }

    /**
     * @param int $riskGroupId
     */
    public function setRiskGroupId($riskGroupId)
    {
        $this->riskGroupId = $riskGroupId;
    }

    /**
     * @return int
     */
    public function getRiskGroupId()
    {
        return $this->riskGroupId;
    }

    /**
     * @param string $riskGroupDescription
     */
    public function setRiskGroupDescription($riskGroupDescription)
    {
        $this->riskGroupDescription = $riskGroupDescription;
    }

    /**
     * @return string
     */
    public function getRiskGroupDescription()
    {
        return $this->riskGroupDescription;
    }

    /**
     * @param string $modelState
     */
    public function setModelState($modelState)
    {
        $this->modelState = $modelState;
    }

    /**
     * @return string
     */
    public function getModelState()
    {
        return $this->modelState;
    }

    /**
     * @param \Synapse\RiskBundle\EntityDto\date $enrollmentEndDate
     */
    public function setEnrollmentEndDate($enrollmentEndDate)
    {
        $this->enrollmentEndDate = $enrollmentEndDate;
    }

    /**
     * @return \Synapse\RiskBundle\EntityDto\date
     */
    public function getEnrollmentEndDate()
    {
        return $this->enrollmentEndDate;
    }

    /**
     * @param \Synapse\RiskBundle\EntityDto\date $calculationStopDate
     */
    public function setCalculationStopDate($calculationStopDate)
    {
        $this->calculationStopDate = $calculationStopDate;
    }

    /**
     * @return \Synapse\RiskBundle\EntityDto\date
     */
    public function getCalculationStopDate()
    {
        return $this->calculationStopDate;
    }


}