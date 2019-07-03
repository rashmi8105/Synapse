<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EmailTemplate
 *
 * @ORM\Table(name="email_template")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EmailTemplateRepository")
 */
class EmailTemplate extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email_key", type="string", length=100, nullable=true)
     */
    private $emailKey;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_active", type="integer", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="from_email_address", type="string", length=255, nullable=true)
     */
    private $fromEmailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="bcc_recipient_list", type="string", length=500, nullable=true)
     */
    private $bccRecipientList;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set emailKey
     *
     * @param string $emailKey
     * @return EmailTemplate
     */
    public function setEmailKey($emailKey)
    {
        $this->emailKey = $emailKey;

        return $this;
    }

    /**
     * Get emailKey
     *
     * @return string 
     */
    public function getEmailKey()
    {
        return $this->emailKey;
    }

    /**
     * Set isActive
     *
     * @param string $isActive
     * @return EmailTemplate
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return string 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set fromEmailAddress
     *
     * @param string $fromEmailAddress
     * @return EmailTemplate
     */
    public function setFromEmailAddress($fromEmailAddress)
    {
        $this->fromEmailAddress = $fromEmailAddress;

        return $this;
    }

    /**
     * Get fromEmailAddress
     *
     * @return string 
     */
    public function getFromEmailAddress()
    {
        return $this->fromEmailAddress;
    }

    /**
     * Set bccRecipientList
     *
     * @param string $bccRecipientList
     * @return EmailTemplate
     */
    public function setBccRecipientList($bccRecipientList)
    {
        $this->bccRecipientList = $bccRecipientList;

        return $this;
    }

    /**
     * Get bccRecipientList
     *
     * @return string 
     */
    public function getBccRecipientList()
    {
        return $this->bccRecipientList;
    }
}
