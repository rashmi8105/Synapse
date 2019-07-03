<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Bucket Details
 *
 * @package Synapse\RiskBundle\EntityDto
 */
class BucketDetailsListDto
{

    /**
     * bucket_value
     *
     * @var integer @JMS\Type("integer")
     */
    private $bucketValue;

    /**
     * min
     *
     * @var string @JMS\Type("string")
     */
    private $min;
    
    /**
     * max
     *
     * @var string @JMS\Type("string")
     */
    private $max;
    
    /**
     * $optionValue
     *
     * @var array @JMS\Type("array")
     *     
     *     
     */
    private $optionValue;
    
    /**
     *
     * @param string $optionValue
     */
    public function setOptionValue($optionValue)
    {
    	$this->optionValue = $optionValue;
    }
    
    /**
     *
     * @param integer $bucketValue
     */
    public function setBucketValue($bucketValue)
    {
    	$this->bucketValue = $bucketValue;
    }
    
    /**
     *
     * @param string $min
     */
    public function setMin($min)
    {
    	$this->min = ($min)?$min:'';
    }
    
    /**
     *
     * @param string $max
     */
    public function setMax($max)
    {
    	$this->max = ($max)?$max:'';
    }
}