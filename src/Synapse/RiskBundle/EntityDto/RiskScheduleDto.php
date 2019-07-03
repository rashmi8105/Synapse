<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class RiskScheduleDto
{

    /**
     * dateTime that a risk job was scheduled.
     *
     * @var \DateTime
     *
     *      @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $scheduleTime;

    /**
     * @return \DateTime
     */
    public function getScheduleTime()
    {
        return $this->scheduleTime;
    }

    /**
     * @param \DateTime $scheduleTime
     */
    public function setScheduleTime($scheduleTime)
    {
        $this->scheduleTime = $scheduleTime;
    }


    
    
}