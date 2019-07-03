<?php
namespace Synapse\CampusResourceBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgAnnouncementsLang
 *
 * @ORM\Table(name="org_announcements_lang", indexes={@ORM\Index(name="fk_org_announcements_lang_org_announcements1_idx", columns={"org_announcements_id"}), @ORM\Index(name="fk_org_announcements_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementLangRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgAnnouncementsLang extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Organization @ORM\ManyToOne(targetEntity="Synapse\CampusResourceBundle\Entity\OrgAnnouncements")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_announcements_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $orgAnnouncements;

    /**
     *
     * @var \Organization @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\LanguageMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="lang_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $lang;

    /**
     *
     * @var string @ORM\Column(name="message", type="string", length=300, nullable=true)
     */
    private $message;

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
     * Set orgAnnouncements
     *
     * @param \Synapse\CampusResourceBundle\Entity\OrgAnnouncements $orgAnnouncements            
     * @return OrgAnnouncementsLang
     */
    public function setOrgAnnouncements(\Synapse\CampusResourceBundle\Entity\OrgAnnouncements $orgAnnouncements)
    {
        $this->orgAnnouncements = $orgAnnouncements;
        
        return $this;
    }

    /**
     * Get orgAnnouncements
     *
     * @return \Synapse\CampusResourceBundle\Entity\OrgAnnouncements
     */
    public function getOrgAnnouncements()
    {
        return $this->orgAnnouncements;
    }

    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang            
     * @return OrgAnnouncementsLang
     */
    public function setLang(\Synapse\CoreBundle\Entity\LanguageMaster $lang)
    {
        $this->lang = $lang;
        
        return $this;
    }

    /**
     * Get lang
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set message
     *
     * @param string $message            
     * @return OrgAnnouncementsLang
     */
    public function setMessage($message)
    {
        $this->message = $message;
        
        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}