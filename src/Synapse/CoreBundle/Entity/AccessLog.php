<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
/**
 * AccessLog
 *
 * @ORM\Table(name="access_log", indexes={@ORM\Index(name="fk_access_log_organization1", columns={"organization_id"}), @ORM\Index(name="fk_access_log_person1", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AccessLogRepository")
 */

class AccessLog extends BaseEntity
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
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     */
    private $person;

    /**
     * @var string
     *
     * @ORM\Column(name="event", type="string", length=45, nullable=true)
     * @JMS\Expose
     */
    private $event;

    /**
     * @var integer
     *
     * @ORM\Column(name="event_id", type="integer", length=12, nullable=true)
     * @JMS\Expose
     */
    private $eventId;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time", type="datetime", nullable=true)
     * @JMS\Expose
     */
    private $dateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="source_ip", type="string", length=20, nullable=true)
     * @JMS\Expose
     */
    private $sourceIP;

    /**
     * @var string
     *
     * @ORM\Column(name="browser", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $browser;

    /**
     * @var string
     *
     * @ORM\Column(name="user_token", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $userToken;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $apiToken;

    /**
     * @param string $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param string $browser
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param string $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $sourceIP
     */
    public function setSourceIP($sourceIP)
    {
        $this->sourceIP = $sourceIP;
    }

    /**
     * @return string
     */
    public function getSourceIP()
    {
        return $this->sourceIP;
    }

    /**
     * @param string $userToken
     */
    public function setUserToken($userToken)
    {
        $this->userToken = $userToken;
    }

    /**
     * @return string
     */
    public function getUserToken()
    {
        return $this->userToken;
    }


    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return Person
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }



    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return TeamMembers
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param int $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

}
