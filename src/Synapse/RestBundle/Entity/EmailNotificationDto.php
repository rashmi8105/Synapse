<?php
namespace Synapse\RestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class EmailNotificationDto
{

    /**
     * Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * organization
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * sentDate
     * 
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $sentDate;

    /**
     * emailKey
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $emailKey;

    /**
     * recipientList
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $recipientList;

    /**
     * fromAddress
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $fromAddress;

    /**
     * ccList
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $ccList;

    /**
     * bccList
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $bccList;

    /**
     * subject
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $subject;

    /**
     * body
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $body;

    /**
     * status
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $status;

    /**
     * noOfRetries
     * 
     * @var integer @JMS\Type("integer")
     */
    private $noOfRetries;

    /**
     * serverResponse
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $serverResponse;
    
    /**
     * subject
     *
     * @var string @JMS\Type("string")
     *
     */
    private $replyTo;

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
     * @param string $fromAddress            
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;
    }

    /**
     *
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     *
     * @param \Synapse\RestBundle\Entity\datetime $sentDate            
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;
    }

    /**
     *
     * @return \Synapse\RestBundle\Entity\datetime
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
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param string $replyTo
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }


    
}