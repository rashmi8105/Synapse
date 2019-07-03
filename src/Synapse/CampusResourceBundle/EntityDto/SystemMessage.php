<?php
namespace Synapse\CampusResourceBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class SystemMessage
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $startDateTime;

    /**
     * @var \DateTime
     */
    private $endDateTime;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $messageType;

    /**
     * @var string
     */
    private $messageDuration;

    /**
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     *
     * @param string $messageDuration            
     */
    public function setMessageDuration($messageDuration)
    {
        $this->messageDuration = $messageDuration;
    }

    /**
     *
     * @return string
     */
    public function getMessageDuration()
    {
    	return $this->messageDuration;
    }
}

