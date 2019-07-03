<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Response error
 *
 * @JMS\ExclusionPolicy("all")
 */
class Error
{

    /**
     * Error code
     *
     * @var string @JMS\Expose
     */
    private $code;

    /**
     * User message
     *
     * @var string @JMS\Expose
     */
    private $userMessage;

    /**
     * Event ID
     *
     * @var string @JMS\Expose
     */
    private $eventId;

    /**
     * Extra information about the error.
     * i.e property_path
     *
     * @var mixed @JMS\Expose
     */
    private $info;

    function __construct($code, $userMessage, $eventId, $info = array())
    {
        $this->code = $code;
        $this->userMessage = $userMessage;
        $this->eventId = $eventId;
        $this->info = $info;
    }

    /**
     *
     * @param string $code            
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     * @param string $userMessage            
     */
    public function setUserMessage($userMessage)
    {
        $this->userMessage = $userMessage;
    }

    /**
     *
     * @return string
     */
    public function getUserMessage()
    {
        return $this->userMessage;
    }

    /**
     * Sets the Event ID.
     *
     * @param string $eventId the event id
     *
     * @return self
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Gets the Event ID.
     *
     * @return string
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     *
     * @param mixed $info            
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }
}