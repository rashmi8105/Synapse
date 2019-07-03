<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\RiskBundle\Validator\Constraints as RiskAssert;

class RiskModelDto
{

    /**
     * $id
     *
     * @var string @JMS\Type("integer")
     *     
     *     
     */
    private $id;

    /**
     * $riskModelName
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank(message = "Risk Model Name should not be blank")
     *     
     */
    private $riskModelName;

    /**
     * $calculationStartDate
     *
     * @var integer @JMS\Type("DateTime<'m/d/Y'>")
     *     
     *     
     */
    private $calculationStartDate;

    /**
     * $calculationStopDate
     *
     * @var integer @JMS\Type("DateTime<'m/d/Y'>")
     *     
     *     
     */
    private $calculationStopDate;

    /**
     * $enrollmentEndDate
     *
     * @var integer @JMS\Type("DateTime<'m/d/Y'>")
     *     
     *     
     */
    private $enrollmentEndDate;

    /**
     * $modelState
     *
     * @var string @JMS\Type("string")
     *     
     *     
     */
    private $modelState;

    /**
     * $bucketDetails
     *
     * @var string @JMS\Type("array<Synapse\RiskBundle\EntityDto\RiskIndicatorsDto>")
     *     
     * @RiskAssert\RiskIndicator    
     */
    private $riskIndicators;

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
     * @param string $riskIndicators
     */
    public function setRiskIndicators($riskIndicators)
    {
        $this->riskIndicators = $riskIndicators;
    }

    /**
     * @return string
     */
    public function getRiskIndicators()
    {
        return $this->riskIndicators;
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
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $enrollmentEndDate
     */
    public function setEnrollmentEndDate($enrollmentEndDate)
    {
        $this->enrollmentEndDate = $enrollmentEndDate;
    }

    /**
     * @return int
     */
    public function getEnrollmentEndDate()
    {
        return $this->enrollmentEndDate;
    }

    /**
     * @param int $calculationStopDate
     */
    public function setCalculationStopDate($calculationStopDate)
    {
        $this->calculationStopDate = $calculationStopDate;
    }

    /**
     * @return int
     */
    public function getCalculationStopDate()
    {
        return $this->calculationStopDate;
    }

    /**
     * @param int $calculationStartDate
     */
    public function setCalculationStartDate($calculationStartDate)
    {
        $this->calculationStartDate = $calculationStartDate;
    }

    /**
     * @return int
     */
    public function getCalculationStartDate()
    {
        return $this->calculationStartDate;
    }


}