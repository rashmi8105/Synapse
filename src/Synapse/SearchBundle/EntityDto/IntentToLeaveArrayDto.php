<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class IntentToLeaveArrayDto
{

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SearchBundle\EntityDto\IntentToLeaveDto>")
     *     
     *     
     */
    private $intentToLeaveTypes;

    /**
     *
     * @return string
     */
    public function getIntentToLeaveTypes()
    {
        return $this->intentToLeaveTypes;
    }

    /**
     *
     * @param mixed $intentToLeaveTypes            
     */
    public function setIntentToLeaveTypes($intentToLeaveTypes)
    {
        $this->intentToLeaveTypes = $intentToLeaveTypes;
    }
}