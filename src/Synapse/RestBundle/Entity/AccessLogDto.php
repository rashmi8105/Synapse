<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for AccessLog
 *
 * @package Synapse\RestBundle\Entity
 */
class AccessLogDto
{

    /**
     * Unique id for an access log.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Id of the organization that an access log applies to.
     *
     * @var integer @JMS\Type("integer")
     */
    private $organization;

    /**
     * Id of the person that affect the access log.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $person;

    /**
     * Type of event being logged.
     * 
     * @var string @JMS\Type("string")
     */
    private $event;

    /**
     * Event Id that if used, would have stored the type of event being logged.
     * 
     * @var integer @JMS\Type("integer")
     * @deprecated
     */
    private $eventId;

    /**
     * Time event was logged.
     * 
     * @var integer @JMS\Type("datetime")
     */
    private $dateTime;

    /**
     * Source ip address
     * 
     * @var string @JMS\Type("string")
     */
    private $sourceip;

    /**
     * Browser being used
     * 
     * @var string @JMS\Type("string")
     */
    private $browser;

    /**
     * User token
     * 
     * @var string @JMS\Type("string")
     */
    private $userToken;

    /**
     * Api token
     * 
     * @var string @JMS\Type("string")
     */
    private $apiToken;

    /**
     * Returns the api token
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set Api token
     *
     * @param string $apiToken            
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Returns the current browser a person is using.
     *
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Set current browser
     *
     * @param string $browser            
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * Returns the date-time a log was created
     *
     * @return int
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set date-time the log was created
     *
     * @param int $dateTime            
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * Returns event name that triggered the access log
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Sets the event name
     *
     * @param string $event            
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * If used, would return the event Id
     *
     * @return integer
     * @deprecated
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * If used, would set the event Id
     *
     * @param integer $eventId
     * @deprecated
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Returns the access log Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the access log Id
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the organization Id
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Sets the organization Id
     *
     * @param int $organization            
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * Returns the person Id
     *
     * @return int
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Sets the person Id
     *
     * @param int $person            
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * Returns the user token
     *
     * @return string
     */
    public function getUserToken()
    {
        return $this->userToken;
    }

    /**
     * Sets the user token
     *
     * @param string $userToken            
     */
    public function setUserToken($userToken)
    {
        $this->userToken = $userToken;
    }

    /**
     * Returns the source ip
     *
     * @return string
     */
    public function getSourceip()
    {
        return $this->sourceip;
    }

    /**
     * Sets the source ip
     *
     * @param string $sourceip            
     */
    public function setSourceip($sourceip)
    {
        $this->sourceip = $sourceip;
    }
}