<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AlertNotificationReferral
 *
 * @ORM\Table(name="alert_notification_referral")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AlertNotificationReferralRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class AlertNotificationReferral extends BaseEntity
{
    /**
     * @var AlertNotifications
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\AlertNotifications")
     * @ORM\JoinColumn(name="alert_notification_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $alertNotification;

    /**
     * @var ReferralHistory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ReferralHistory")
     * @ORM\JoinColumn(name="referral_history_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $referralHistory;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_hover_text", type="string", length=300, nullable=true)
     * @JMS\Expose
     *
     */
    private $notificationHoverText;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_body_text", type="string", length=300, nullable=true)
     * @JMS\Expose
     *
     */
    private $notificationBodyText;

    /**
     * @param AlertNotifications $alertNotification
     */
    public function setAlertNotification($alertNotification)
    {
        $this->alertNotification = $alertNotification;

    }

    /**
     * @return AlertNotifications
     */
    public function getAlertNotification()
    {
        return $this->alertNotification;
    }


    /**
     * @param ReferralHistory $referralHistory
     */
    public function setReferralHistory($referralHistory)
    {
        $this->referralHistory = $referralHistory;

    }

    /**
     * @return ReferralHistory
     */
    public function getReferralHistory()
    {
        return $this->referralHistory;
    }

    /**
     * @param string $notificationHoverText
     */
    public function setNotificationHoverText($notificationHoverText)
    {
        $this->notificationHoverText = $notificationHoverText;

    }

    /**
     * @return string
     */
    public function getNotificationHoverText()
    {
        return $this->notificationHoverText;

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
    public function getNotificationBodyText()
    {
        return $this->notificationBodyText;

    }
}
