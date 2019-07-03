<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EmailTemplateLang
 *
 * @ORM\Table(name="email_template_lang", indexes={@ORM\Index(name="fk_email_notification_lang_email_notification1_idx", columns={"email_template_id"}), @ORM\Index(name="fk_email_notification_lang_language_master1_idx", columns={"language_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EmailTemplateLangRepository")
 */
class EmailTemplateLang extends BaseEntity
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
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=500, nullable=true)
     */
    private $subject;

    /**
     * @var EmailTemplate
     *
     * @ORM\ManyToOne(targetEntity="EmailTemplate")
     * @ORM\JoinColumn(name="email_template_id", referencedColumnName="id")
     */
    private $emailTemplate;

    /**
     * @var LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="LanguageMaster")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $language;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
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
     * @return LanguageMaster
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param LanguageMaster $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }


}
