<?php
namespace Synapse\CampusResourceBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgAnnouncements
 *
 * @ORM\Table(name="org_announcements", indexes={@ORM\Index(name="fk_org_announcements_organization1_idx", columns={"org_id"}), @ORM\Index(name="fk_org_announcements_person1_idx", columns={"creator_person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgAnnouncements extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $orgId;

    /**
     * @var string @ORM\Column(name="display_type", type="string", precision=0, scale=0, nullable=false, unique=false, columnDefinition="ENUM('banner', 'notification bell')")
     */
    private $displayType;

    /**
     *
     * @var \DateTime @ORM\Column(name="start_datetime", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $startDatetime;

    /**
     *
     * @var \DateTime @ORM\Column(name="stop_datetime", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $stopDatetime;

    /**
     *
     * @var \Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="creator_person_id", nullable=false, referencedColumnName="id")
     *      })
     */
    private $creatorPersonId;
    
    /**
     *
     * @var string @ORM\Column(name="message_duration", type="string", length=25, nullable=true)
     */
    private $messageDuration;

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
     * Set orgId
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgAnnouncements
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization)
    {
        $this->orgId = $organization;
        
        return $this;
    }

    /**
     * Get orgId
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->orgId;
    }

    /**
     * Set displayType
     *
     * @param string $displayType            
     * @return OrgAnnouncements
     */
    public function setDisplayType($displayType)
    {
        $this->displayType = $displayType;
        
        return $this;
    }

    /**
     * Get displayType
     *
     * @return string
     */
    public function getDisplayType()
    {
        return $this->displayType;
    }

    /**
     * Set startDatetime
     *
     * @param \DateTime $startDatetime            
     * @return OrgAnnouncements
     */
    public function setStartDatetime($startDatetime)
    {
        $this->startDatetime = $startDatetime;
        
        return $this;
    }

    /**
     * Get startDatetime
     *
     * @return \DateTime
     */
    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    /**
     * Set stopDatetime
     *
     * @param \DateTime $stopDatetime            
     * @return OrgAnnouncements
     */
    public function setStopDatetime($stopDatetime)
    {
        $this->stopDatetime = $stopDatetime;
        
        return $this;
    }

    /**
     * Get stopDatetime
     *
     * @return \DateTime
     */
    public function getStopDatetime()
    {
        return $this->stopDatetime;
    }

    /**
     * Set creatorPersonId
     *
     * @param \Synapse\CoreBundle\Entity\Person $personId            
     * @return Person
     */
    public function setCreatorPersonId(\Synapse\CoreBundle\Entity\Person $creatorPersonId)
    {
        $this->creatorPersonId = $creatorPersonId;
        
        return $this;
    }

    /**
     * Get creatorPersonId
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getCreatorPersonId()
    {
        return $this->creatorPersonId;
    }
    
    /**
     * Set message duration
     *
     * @param string $messageDuration
     * @return OrgAnnouncements
     */
    public function setMessageDuration($messageDuration)
    {
    	$this->messageDuration = $messageDuration;
    
    	return $this;
    }
    
    /**
     * Get message duration
     *
     * @return string
     */
    public function getMessageDuration()
    {
    	return $this->messageDuration;
    }
}