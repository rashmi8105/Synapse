<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class BucketDetailsDto
{

    /**
     * bucket's point value
     *
     * @var integer
     *
     *      @JMS\Type("integer")
     *      @Assert\NotBlank(message = "Bucket Value should not be blank")
     */
    private $bucketValue;

    /**
     * minimum value of a bucket
     *
     * @var integer
     *
     *      @JMS\Type("double")
     *      @Assert\Range(
     *      min = -999999999999,
     *      max = 999999999999,
     *      minMessage = "min value not less than {{ limit }} to enter",
     *      maxMessage = "min value not grater than {{ limit }} to enter"
     *      )
     */
    private $min;

    /**
     * maximum value of a bucket
     *
     * @var integer
     *
     *      @JMS\Type("double")
     *      @Assert\Range(
     *      min = -999999999999,
     *      max = 999999999999,
     *      minMessage = "max value not less than {{ limit }} to enter",
     *      maxMessage = "max value not grater than {{ limit }} to enter"
     *      )
     */
    private $max;

    /**
     * array of options set for a bucket
     *
     * @var array
     *
     *      @JMS\Type("array")
     */
    private $optionValue;

    /**
     *
     * @return string
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

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
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     *
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     *
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     *
     * @return int
     */
    public function getBucketValue()
    {
        return $this->bucketValue;
    }

    /**
     *
     * @param int $bucketValue
     */
    public function setBucketValue($bucketValue)
    {
        $this->bucketValue = $bucketValue;
    }
}