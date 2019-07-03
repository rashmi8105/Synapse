<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Risk Variables
 *
 * @package Synapse\RiskBundle\Entity
 */
class RiskVariableResponseDto
{
    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;
    
    /**
     * risk_b_variable
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
     * is_continuous
     *
     * @var string @JMS\Type("boolean")
     *
     *
     */
    private $isContinuous;
    
    /**
     * is_calculated
     *
     * @var string @JMS\Type("boolean")
     *
     *
     */
    private $isCalculated;
    
    /**
     * source_id
     *
     * @var Object @JMS\Type("Synapse\RiskBundle\EntityDto\SourceIdDto")
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
     * bucket_details
     *
     * @var Object @JMS\Type("array<Synapse\RiskBundle\EntityDto\BucketDetailsListDto>")
     *
     *
     */
    private $bucketDetails;
    
    /**
     * calculated_data
     *
     * @var Object @JMS\Type("Synapse\RiskBundle\EntityDto\CalculatedDataDto")
     *
     *
     */
    private $calculatedData;
    
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
     * @param string $sourceId
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
    	$this->campusId = $campusId;
    }
    
    /**
     *
     * @param boolean $isContinuous
     */
    public function setIsContinuous($isContinuous)
    {
    	$this->isContinuous = $isContinuous;
    }
    
    /**
     *
     * @param boolean $isCalculated
     */
    public function setIsCalculated($isCalculated)
    {
    	$this->isCalculated = $isCalculated;
    }
    
    /**
     *
     * @param Object $bucketDetails
     */
    public function setBucketDetails($bucketDetails)
    {
    	$this->bucketDetails = $bucketDetails;
    }
    
    /**
     *
     * @param string $calculatedData
     */
    public function setCalculatedData($calculatedData)
    {
    	$this->calculatedData = $calculatedData;
    }

    /**
     * @return Object
     */
    public function getBucketDetails()
    {
        return $this->bucketDetails;
    }

    /**
     * @return Object
     */
    public function getCalculatedData()
    {
        return $this->calculatedData;
    }

    /**
     * @return string
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIsCalculated()
    {
        return $this->isCalculated;
    }

    /**
     * @return string
     */
    public function getIsContinuous()
    {
        return $this->isContinuous;
    }

    /**
     * @return string
     */
    public function getRiskVariableName()
    {
        return $this->riskVariableName;
    }

    /**
     * @return Object
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @return string
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

}