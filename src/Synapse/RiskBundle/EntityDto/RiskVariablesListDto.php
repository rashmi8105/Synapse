<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Risk Variables
 *
 * @package Synapse\RiskBundle\EntityDto
 */
class RiskVariablesListDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * risk_variable_name
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $riskVariableName;

    /**
     * source_type
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $sourceType;

    /**
     * source_id
     *
     * @var integer @JMS\Type("integer")
     *     
     *     
     */
    private $sourceId;

    /**
     * survey_id
     *
     * @var string @JMS\Type("integer")
     *     
     *     
     */
    private $surveyId;

    /**
     * campus_id
     *
     * @var string @JMS\Type("string")
     *     
     *     
     */
    private $campusId;

    /**
     * is_assigned
     *
     * @var string @JMS\Type("boolean")
     *     
     *     
     */
    private $isAssigned;
    
    /**
     *
     * @param integer $id
     */
    public function setId($id)
    {
    	$this->id = $id;
    }
    
    /**
     *
     * @param string $riskVariableName
     */
    public function setRiskVariableName($riskVariableName)
    {
    	$this->riskVariableName = $riskVariableName;
    }
    
    /**
     *
     * @param string $sourceType
     */
    public function setSourceType($sourceType)
    {
    	$this->sourceType = $sourceType;
    }
    
    /**
     *
     * @param int $sourceId
     */
    public function setSourceId($sourceId)
    {
    	$this->sourceId = $sourceId;
    }
    
    /**
     *
     * @param int $surveyId
     */
    public function setSurveyId($surveyId)
    {
    	$this->surveyId = $surveyId;
    }
    
    /**
     *
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
    	$this->campusId = ($campusId) ? $campusId: '';
    }
    
    /**
     *
     * @param boolean $isAssigned
     */
    public function setIsAssigned($isAssigned)
    {
    	$this->isAssigned = $isAssigned;
    }
}