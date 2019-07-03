<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CalculatedDataDto
{
    /**
     * date that data for calculation begins
     *
     * @var \DateTime
     *
     *      @JMS\Type("DateTime<'m/d/Y'>")
     */
    private $calculationStartDate;

    /**
     * date that data for calculation ends
     *
     * @var \DateTime @JMS\Type("DateTime<'m/d/Y'>")
     */
    private $calculationStopDate;

    /**
     * type of calculation being made
     *
     * @var string @JMS\Type("string")
     */
    private $calculationType;

    /**
     * @return \DateTime
     */
    public function getCalculationStartDate()
    {
        return $this->calculationStartDate;
    }

    /**
     * @param \DateTime $calculationStartDate
     */
    public function setCalculationStartDate($calculationStartDate)
    {
        $this->calculationStartDate = $calculationStartDate;
    }

    /**
     * @return string
     */
    public function getCalculationType()
    {
        return $this->calculationType;
    }

    /**
     * @param string $calculationType
     */
    public function setCalculationType($calculationType)
    {
        $this->calculationType = $calculationType;
    }

    /**
     * @return \DateTime
     */
    public function getCalculationStopDate()
    {
        return $this->calculationStopDate;
    }

    /**
     * @param \DateTime $calculationStopDate
     */
    public function setCalculationStopDate($calculationStopDate)
    {
        $this->calculationStopDate = $calculationStopDate;
    }

    
}