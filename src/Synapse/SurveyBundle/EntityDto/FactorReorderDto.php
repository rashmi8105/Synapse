<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * @package Synapse\SurveyBundle\FactorReorderDto
 */
class FactorReorderDto
{

    /**
     * Id of a factor that locates it within a sequence.
     *
     * @var integer @JMS\Type("integer")
     */
    private $factorId;

    /**
     * Total number of related factors that have been created.
     *
     * @var integer @JMS\Type("integer")
     */
    private $sequence;

    /**
     * Sets the id of a factor.
     *
     * @param int $factorId            
     */
    public function setFactorId($factorId)
    {
        $this->factorId = $factorId;
    }

    /**
     * Returns the id of a factor.
     *
     * @return int
     */
    public function getFactorId()
    {
        return $this->factorId;
    }

    /**
     * Sets the sequence of a factor.
     *
     * @param string $sequence            
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * Returns the sequence of a factor.
     *
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }
}