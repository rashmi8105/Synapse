<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class RiskIndicatorsDto
{

    /**
     * $name
     *
     * @var integer @JMS\Type("string")
     *      @Assert\NotBlank(message = "RiskIndicator Name should not be blank")
     *     
     */
    private $name;

    /**
     * $min
     *
     * @var integer @JMS\Type("double")
     *      @Assert\NotBlank(message = "min should not be blank")
     *     
     */
    private $min;

    /**
     * $max
     * @Assert\NotBlank(message = "max should not be blank")
     * 
     * @var integer @JMS\Type("double")
     *
     */
    private $max;

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
    public function getMax()
    {
        return $this->max;
    }

    /**
     *
     * @param int $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return int
     */
    public function getName()
    {
        return $this->name;
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
    public function getMin()
    {
        return $this->min;
    }
}