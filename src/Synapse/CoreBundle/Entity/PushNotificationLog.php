<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * PushNotificationLogs
 *
 * @ORM\Table(name="push_notification_log")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\PushNotificationLogRepository")
 * @JMS\ExclusionPolicy("all")
 */
class PushNotificationLog extends BaseEntity
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;


    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;


    /**
     * @var \Synapse\CoreBundle\Entity\Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $person;

    /**
     * @var string
     * @ORM\Column(name="channel_name", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $channelName;

    /**
     * @var string
     * @ORM\Column(name="event_key", type="string", columnDefinition="enum('channel_created', 'push_notification_to_channels','channel_deleted')")
     * @JMS\Expose
     */
    private $eventKey;

    /**
     * @var string
     * @ORM\Column(name="data_posted_to_push_server", type="text", nullable=true)
     * @JMS\Expose
     */
    private $dataPostedToPushServer;


    /**
     * @var string
     * @ORM\Column(name="response_from_push_server", type="text", nullable=true)
     * @JMS\Expose
     */
    private $responseFromPushServer;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * @param string $channelName
     */
    public function setChannelName($channelName)
    {
        $this->channelName = $channelName;
    }

    /**
     * @return string
     */
    public function getEventKey()
    {
        return $this->eventKey;
    }

    /**
     * @param string $eventKey
     */
    public function setEventKey($eventKey)
    {
        $this->eventKey = $eventKey;
    }

    /**
     * @return string
     */
    public function getDataPostedToPushServer()
    {
        return $this->dataPostedToPushServer;
    }

    /**
     * @param string $dataPostedToPushServer
     */
    public function setDataPostedToPushServer($dataPostedToPushServer)
    {
        $this->dataPostedToPushServer = $dataPostedToPushServer;
    }

    /**
     * @return string
     */
    public function getResponseFromPushServer()
    {
        return $this->responseFromPushServer;
    }

    /**
     * @param string $responseFromPushServer
     */
    public function setResponseFromPushServer($responseFromPushServer)
    {
        $this->responseFromPushServer = $responseFromPushServer;
    }

}