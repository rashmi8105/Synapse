<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Risk Variables
 *
 * @package Synapse\RiskBundle\EntityDto
 */
class RiskModelAssignmentsDto
{

    /**
     * campus_id
     *
     * @var string @JMS\Type("string")
     */
    private $campusId;

    /**
     * campus_name
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $campusName;

    /**
     * risk_group_id
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $riskGroupId;

    /**
     * risk_group_name
     *
     * @var string @JMS\Type("string")
     *     
     *     
     */
    private $riskGroupName;
    
    /**
     * risk_model_id
     *
     * @var string @JMS\Type("string")
     *
     */
    private $riskModelId;
    
    /**
     * risk_model_name
     *
     * @var string @JMS\Type("string")
     *
     *
     */
    private $riskModelName;
    
    /**
     * calculation_start_date
     * @var date @JMS\Type("DateTime<'m/d/Y'>")
     * 
     */
    private $calculationStartDate;
    
    /**
     * calculation_stop_date
     * @var date @JMS\Type("DateTime<'m/d/Y'>")
     *
     */
    private $calculationStopDate;

    /**
     * enrollment_end_date
     * @var date @JMS\Type("DateTime<'m/d/Y'>")
     *
     */
    private $enrollmentEndDate;

    /**
     * @param \Synapse\RiskBundle\EntityDto\date $calculationStartDate
     */
    public function setCalculationStartDate($calculationStartDate)
    {
        $this->calculationStartDate = $calculationStartDate;
    }

    /**
     * @param \Synapse\RiskBundle\EntityDto\date $calculationStopDate
     */
    public function setCalculationStopDate($calculationStopDate)
    {
        $this->calculationStopDate = $calculationStopDate;
    }

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     * @param string $campusName
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }

    /**
     * @param \Synapse\RiskBundle\EntityDto\date $enrollmentEndDate
     */
    public function setEnrollmentEndDate($enrollmentEndDate)
    {           
        $this->enrollmentEndDate = $enrollmentEndDate;        
    }

    /**
     * @param string $riskGroupId
     */
    public function setRiskGroupId($riskGroupId)
    {
        $this->riskGroupId = ($riskGroupId) ? $riskGroupId : '';
    }

    /**
     * @param string $riskGroupName
     */
    public function setRiskGroupName($riskGroupName)
    {
        $this->riskGroupName = ($riskGroupName) ? $riskGroupName : '';
    }

    /**
     * @param string $riskModelId
     */
    public function setRiskModelId($riskModelId)
    {
        $this->riskModelId = ($riskModelId) ? $riskModelId : '';
    }

    /**
     * @param string $riskModelName
     */
    public function setRiskModelName($riskModelName)
    {           
        $this->riskModelName = ($riskModelName) ? $riskModelName : '';        
    }

}