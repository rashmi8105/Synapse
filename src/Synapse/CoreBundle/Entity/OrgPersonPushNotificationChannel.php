<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgPersonPushNotificationChannel
 *
 * @ORM\Table(name="org_person_push_notification_channel")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPersonPushNotificationChannelRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPersonPushNotificationChannel extends BaseEntity
{
    /**
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;

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
     * @var \Synapse\CoreBundle\Entity\Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $person;

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
     * @var string
     * @ORM\Column(name="channel_name", type="string", length=100, nullable=false)
     * @JMS\Expose
     */
    private $channelName;

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

}
