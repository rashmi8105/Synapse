<?php
namespace Synapse\CampusResourceBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\CampusResourceBundle\EntityDto
 *         
 */
class CampusAnnouncementDto
{

    /**
     * Id of a specific campus announcement
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * Id of the organization that a campus announcement belongs to
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * Id of the person accessing mapworks
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $personId;

    /**
     * Message included with a campus announcement. Maximum length is 140 characters
     *
     * @var string @JMS\Type("string")
     * @Assert\NotBlank(message = "Message should not be blank")
     * @Assert\Length(
     *     
     *      max = 140,
     *      maxMessage = "Message cannot be longer than {{ limit }} characters"
     *      )
     *     
     */
    private $message;

    /**
     * JMS type of the campus announcement message. Should not be blank and defaults as a string
     *
     * @var string @JMS\Type("string")
     * @Assert\NotBlank(message = "Message type should not be blank")
     */
    private $messageType;

    /**
     * Start date of a campus announcement. Uses a dateTime object
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $startDateTime;

    /**
     * End date of a campus announcement. Uses a dateTime object
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $endDateTime;

    /**
     * Id corresponding to the language used by the organization. 1 = English
     *
     * @var string @JMS\Type("integer")
     * 
     */
    private $langId;

    /**
     * Stores whether or not an announcement has been viewed or not. Stored to be converted to a boolean value. 0=False, 1=True
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $status;

    /**
     * Id for a notification corresponding to a specific campus announcement
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $notificationId;

    /**
     * Determines the way the alert is displayed, i.e. bell or announcement-banner
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $displayType;
    
    /**
     * Date object that determines how long an alert applies for
     *
     * @var string @JMS\Type("string")
     *
     */
    private $messageDuration;

    /**
     * Sets the id of a campus announcement
     *
     * @param mixed $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the id of a campus announcement
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id of the organization that the campus announcement applies to
     *
     * @param mixed $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the id of the organization that the campus announcement applies to
     *
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the id of the person creating an announcement
     *
     * @param mixed $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * Returns the id of the person creating an announcement
     *
     * @return mixed
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Sets the message included with a campus announcement
     *
     * @param string $message            
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the message included with a campus announcement
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the type of message that the campus announcement will display as
     *
     * @param string $messageType            
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * Returns the type of message that the campus announcement will display as
     *
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * Sets the start date of a campus announcement
     *
     * @param mixed $startDateTime            
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * Returns the start date of a campus announcement
     *
     * @return mixed
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * Sets the end date of a campus announcement
     *
     * @param mixed $endDateTime            
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     * Returns the end date of a campus announcement
     *
     * @return mixed
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     * Sets the language id for a campus announcement
     *
     * @param mixed $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     * Returns the language id for a campus announcement
     *
     * @return mixed
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Sets the status of a campus announcement
     *
     * @param mixed $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the status of a campus announcement
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the notification id for a campus announcement
     *
     * @param mixed $notificationId            
     */
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * Returns the notification id for a campus announcement
     *
     * @return mixed
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * Sets the display type of a campus announcement
     *
     * @param string $displayType            
     */
    public function setDisplayType($displayType)
    {
        $this->displayType = $displayType;
    }

    /**
     * Returns the display type of a campus announcement
     *
     * @return string
     */
    public function getDisplayType()
    {
        return $this->displayType;
    }
    
    /**
     * Sets the message duration of a campus announcement
     *
     * @param string $messageDuration
     */
    public function setMessageDuration($messageDuration)
    {
    	$this->messageDuration = $messageDuration;
    }
    
    /**
     * Returns the message duration of a campus announcement
     *
     * @return string
     */
    public function getMessageDuration()
    {
    	return $this->messageDuration;
    }
}