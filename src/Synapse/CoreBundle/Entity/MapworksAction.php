<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping\UniqueConstraint;


/**
 * MapworksAction
 *
 * @ORM\Table(name="mapworks_action",uniqueConstraints={@UniqueConstraint(name="Unique_action_recipient_type_event_type", columns={"action", "recipient_type", "event_type"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\MapworksActionRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class MapworksAction extends BaseEntity
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
     *
     * @var string
     * @ORM\Column(name="event_key", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $eventKey;

    /**
     * @var string
     * @ORM\Column(name="action", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $action;

    /**
     * @var string
     * @ORM\Column(name="recipient_type", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $recipientType;

    /**
     * @var string
     * @ORM\Column(name="event_type", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $eventType;

    /**
     * @var EmailTemplate
     *
     * @ORM\ManyToOne(targetEntity="EmailTemplate")
     * @ORM\JoinColumn(name="email_template_id", referencedColumnName="id")
     * @JMS\Expose
     */
    private $emailTemplate;

    /**
     * @var string
     * @ORM\Column(name="notification_body_text", type="string", length=300, nullable=true)
     * @JMS\Expose
     */
    private $notificationBodyText;

    /**
     * @var string
     * @ORM\Column(name="notification_hover_text", type="string", length=300, nullable=true)
     * @JMS\Expose
     */
    private $notificationHoverText;

    /**
     * @var bool
     * @ORM\Column(name="receives_email", type="boolean")
     * @JMS\Expose
     */
    private $receivesEmail;

    /**
     * @var bool
     * @ORM\Column(name="receives_notification", type="boolean")
     * @JMS\Expose
     */
    private $receivesNotification;

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
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getRecipientType()
    {
        return $this->recipientType;
    }

    /**
     * @param string $recipientType
     */
    public function setRecipientType($recipientType)
    {
        $this->recipientType = $recipientType;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return EmailTemplate
     */
    public function getEmailTemplate()
    {
        return $this->emailTemplate;
    }

    /**
     * @param EmailTemplate $emailTemplate
     */
    public function setEmailTemplate($emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @return string
     */
    public function getNotificationBodyText()
    {
        return $this->notificationBodyText;
    }

    /**
     * @param string $notificationBodyText
     */
    public function setNotificationBodyText($notificationBodyText)
    {
        $this->notificationBodyText = $notificationBodyText;
    }

    /**
     * @return string
     */
    public function getNotificationHoverText()
    {
        return $this->notificationHoverText;
    }

    /**
     * @param string $notificationHoverText
     */
    public function setNotificationHoverText($notificationHoverText)
    {
        $this->notificationHoverText = $notificationHoverText;
    }

    /**
     * @return boolean
     */
    public function getReceivesEmail()
    {
        return $this->receivesEmail;
    }

    /**
     * @param boolean $receivesEmail
     */
    public function setReceivesEmail($receivesEmail)
    {
        $this->receivesEmail = $receivesEmail;
    }


    /**
     * @return boolean
     */
    public function getReceivesNotification()
    {
        return $this->receivesNotification;
    }

    /**
     * @param boolean $receivesNotification
     */
    public function setReceivesNotification($receivesNotification)
    {
        $this->receivesNotification = $receivesNotification;
    }
}