<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\RiskBundle\Validator\Constraints as RiskAssert;

/**
 * @RiskAssert\SourceId
 * @RiskAssert\BucketDetail
 */
class RiskVariableDto
{

    /**
     * risk variable id
     *
     * @var string
     *
     *      @JMS\Type("integer")
     */
    private $id;

    /**
     * risk variable's name
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank(message = "Risk Variable Name should not be blank")
     */
    private $riskVariableName;

    /**
     * risk variable's source type.
     *
     * @var SourceIdDto
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank(message = "Source Type should not be blank")
     */
    private $sourceType;

    /**
     * id of a risk variable's source
     *
     * @var string
     *
     *      @JMS\Type("Synapse\RiskBundle\EntityDto\SourceIdDto")
     *      @Assert\NotBlank(message = "Source Id should not be blank")
     */
    private $sourceId;

    /**
     * whether a risk variable is continuous or not
     *
     * @var boolean
     *
     *      @JMS\Type("boolean")
     */
    private $isContinuous;

    /**
     * whether a risk variable is calculated or not
     *
     * @var boolean
     *
     *      @JMS\Type("boolean")
     */
    private $isCalculated;

    /**
     * array of bucket details objects
     *
     * @var BucketDetailsDto[]
     *
     *      @JMS\Type("array<Synapse\RiskBundle\EntityDto\BucketDetailsDto>")
     *      @Assert\NotBlank(message = "Bucket Details should not be blank")
     */
    private $bucketDetails;

    /**
     * object representation of the calculated data within a risk variable
     *
     * @var CalculatedDataDto
     *
     *      @JMS\Type("Synapse\RiskBundle\EntityDto\CalculatedDataDto")
     */
    private $calculatedData;

    /**
     *
     * @return SourceIdDto
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @param SourceIdDto $sourceType
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
    }

    /**
     *
     * @return string
     */
    public function getSourceId()
    {
        return $this->sourceId;
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
     * @return string
     */
    public function getRiskVariableName()
    {
        return $this->riskVariableName;
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
     * @return boolean
     */
    public function getIsContinuous()
    {
        return $this->isContinuous;
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
     * @return boolean
     */
    public function getIsCalculated()
    {
        return $this->isCalculated;
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
     * @return CalculatedDataDto
     */
    public function getCalculatedData()
    {
        return $this->calculatedData;
    }

    /**
     *
     * @param CalculatedDataDto $calculatedData
     */
    public function setCalculatedData($calculatedData)
    {
        $this->calculatedData = $calculatedData;
    }

    /**
     *
     * @return BucketDetailsDto[]
     */
    public function getBucketDetails()
    {
        return $this->bucketDetails;
    }

    /**
     *
     * @param BucketDetailsDto[] $bucketDetails
     */
    public function setBucketDetails($bucketDetails)
    {
        $this->bucketDetails = $bucketDetails;
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}