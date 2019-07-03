<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * NotificationLog
 *
 * @ORM\Table(name="notification_log", indexes={@ORM\Index(name="fk_notification_log_organization1",columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\NotificationRepository")
 * @JMS\ExclusionPolicy("all")
 */
class NotificationLog extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     */
    private $organization;

    /**
     *
     * @var \Date @ORM\Column(name="sent_date", type="date", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $sentDate;

    /**
     *
     * @var string @ORM\Column(name="email_key", type="string", length=100, nullable=true)
     *     
     *      @JMS\Expose
     */
    private $emailKey;

    /**
     *
     * @var string @ORM\Column(name="recipient_list", type="string", length=500, nullable=true)
     *     
     *      @JMS\Expose
     */
    private $recipientList;

    /**
     *
     * @var string @ORM\Column(name="cc_list", type="string", length=500, nullable=true)
     *     
     *      @JMS\Expose
     */
    private $ccList;

    /**
     *
     * @var string @ORM\Column(name="bcc_list", type="string", length=500, nullable=true)
     *     
     *      @JMS\Expose
     */
    private $bccList;

    /**
     *
     * @var string @ORM\Column(name="subject", type="string", length=1000, nullable=true)
     *     
     *      @JMS\Expose
     */
    private $subject;

    /**
     *
     * @var string @ORM\Column(name="body", type="text", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $body;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", length=1, nullable=true)
     *     
     *      @JMS\Expose
     */
    private $status;

    /**
     *
     * @var integer @ORM\Column(name="no_of_retries", type="integer", nullable=true)
     *      @JMS\Expose
     */
    private $noOfRetries;

    /**
     *
     * @var string @ORM\Column(name="server_response", type="string", length=500, nullable=true)
     *     
     *      @JMS\Expose
     */
    private $serverResponse;

    /**
     *
     * @param string $bccList            
     */
    public function setBccList($bccList)
    {
        $this->bccList = $bccList;
    }

    /**
     *
     * @return string
     */
    public function getBccList()
    {
        return $this->bccList;
    }

    /**
     *
     * @param string $body            
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *
     * @param string $ccList            
     */
    public function setCcList($ccList)
    {
        $this->ccList = $ccList;
    }

    /**
     *
     * @return string
     */
    public function getCcList()
    {
        return $this->ccList;
    }

    /**
     *
     * @param string $emailKey            
     */
    public function setEmailKey($emailKey)
    {
        $this->emailKey = $emailKey;
    }

    /**
     *
     * @return string
     */
    public function getEmailKey()
    {
        return $this->emailKey;
    }

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $noOfRetries            
     */
    public function setNoOfRetries($noOfRetries)
    {
        $this->noOfRetries = $noOfRetries;
    }

    /**
     *
     * @return int
     */
    public function getNoOfRetries()
    {
        return $this->noOfRetries;
    }

    /**
     *
     * @param string $recipientList            
     */
    public function setRecipientList($recipientList)
    {
        $this->recipientList = $recipientList;
    }

    /**
     *
     * @return string
     */
    public function getRecipientList()
    {
        return $this->recipientList;
    }

    /**
     *
     * @param \Date $sentDate            
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;
    }

    /**
     *
     * @return \Date
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     *
     * @param string $serverResponse            
     */
    public function setServerResponse($serverResponse)
    {
        $this->serverResponse = $serverResponse;
    }

    /**
     *
     * @return string
     */
    public function getServerResponse()
    {
        return $this->serverResponse;
    }

    /**
     *
     * @param string $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $subject            
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
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
}