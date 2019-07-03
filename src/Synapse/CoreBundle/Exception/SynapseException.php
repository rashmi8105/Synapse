<?php

namespace Synapse\CoreBundle\Exception;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * Base Synapse exception
 * 
 * @codeCoverageIgnore
 */
abstract class SynapseException extends Exception
{   
    /**
     * @var String exception user message
     */
    protected $userMessage;

    /**
     * @var int exception http code
     */
    protected $httpCode;

    /**
     * @var string exception event id
     */
    protected $eventId;

    /**
     * @var array extra information for the exception
     */
    protected $info;

    /**
     * @var string developer message
     */
    protected $message;

    /**
     * @var string Unique string for locating error location
     */
    protected $code;

    function __construct($message, $userMessage, $code = 'generic_error', $httpCode = 500)
    {
        $this->message = $message;
        $this->userMessage = $userMessage;
        $this->code = $code;
        $this->httpCode = $httpCode;
        $this->eventId = uniqid('event-');
        $this->info = [];
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Sets the value of userMessage.
     *
     * @param String exception user message $userMessage the user message
     *
     * @return self
     */
    public function setUserMessage($userMessage)
    {
        $this->userMessage = $userMessage;

        return $this;
    }

    /**
     * Gets the value of userMessage.
     *
     * @return String exception user message
     */
    public function getUserMessage()
    {
        return $this->userMessage;
    }

    /**
     * Sets the value of httpCode.
     *
     * @param int exception http code $httpCode the http code
     *
     * @return self
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    /**
     * Gets the value of httpCode.
     *
     * @return int exception http code
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Sets the value of eventId.
     *
     * @param string exception event id $eventId the event id
     *
     * @return self
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Gets the value of eventId.
     *
     * @return string exception event id
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param array $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $key
     * @param mixed $obj
     */
    public function addInfo($key, $obj)
    {
        $this->info[$key] = $obj;
    }
}