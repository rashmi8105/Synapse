<?php
namespace Synapse\CalendarBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgCronofyCalendar
 *
 * @ORM\Table(name="org_cronofy_calendar", indexes={@ORM\Index(name="fk_org_cronofy_calendar_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_org_person_faculty_person1", columns={"person_id"})})))
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Synapse\CalendarBundle\Repository\OrgCronofyCalendarRepository")
 */
class OrgCronofyCalendar extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
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
     * @ORM\Column(name="status", type="string", length=1, nullable=true)
     */
    private $status;

    /**
     *
     * @var string @ORM\Column(name="cronofy_one_time_token", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $cronofyOneTimeToken;

    /**
     *
     * @var string @ORM\Column(name="cronofy_cal_access_token", type="string", length=1000, nullable=true)
     * @JMS\Expose
     */
    private $cronofyCalAccessToken;

    /**
     *
     * @var string @ORM\Column(name="cronofy_cal_refresh_token", type="string", length=300, nullable=true)
     * @JMS\Expose
     */
    private $cronofyCalRefreshToken;

    /**
     *
     * @var string @ORM\Column(name="cronofy_profile_name", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $cronofyProfile;

    /**
     *
     * @var string @ORM\Column(name="cronofy_calendar_id", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $cronofyCalendar;

    /**
     *
     * @var string @ORM\Column(name="cronofy_provider_name", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $cronofyProvider;

    /**
     * @var string @ORM\Column(name="cronofy_channel_id", type="string", length=45, nullable=true)
     * @JMS\Expose
     */
    private $cronofyChannel;

    /**
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return Organization
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
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
     * @return Person
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Referrals
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $cronofyOneTimeToken
     */
    public function setCronofyOneTimeToken($cronofyOneTimeToken)
    {
        $this->cronofyOneTimeToken = $cronofyOneTimeToken;
    }

    /**
     *
     * @return string
     */
    public function getCronofyOneTimeToken()
    {
        return $this->cronofyOneTimeToken;
    }

    /**
     *
     * @param string $cronofyCalAccessToken
     */
    public function setCronofyCalAccessToken($cronofyCalAccessToken)
    {
        $this->cronofyCalAccessToken = $cronofyCalAccessToken;
    }

    /**
     *
     * @return string
     */
    public function getCronofyCalAccessToken()
    {
        return $this->cronofyCalAccessToken;
    }

    /**
     *
     * @param string $cronofyCalRefreshToken
     */
    public function setCronofyCalRefreshToken($cronofyCalRefreshToken)
    {
        $this->cronofyCalRefreshToken = $cronofyCalRefreshToken;
    }

    /**
     *
     * @return string
     */
    public function getCronofyCalRefreshToken()
    {
        return $this->cronofyCalRefreshToken;
    }

    /**
     *
     * @param string $cronofyProfile
     */
    public function setCronofyProfile($cronofyProfile)
    {
        $this->cronofyProfile = $cronofyProfile;
    }

    /**
     *
     * @return string
     */
    public function getCronofyProfile()
    {
        return $this->cronofyProfile;
    }

    /**
     *
     * @param string $cronofyCalendar
     */
    public function setCronofyCalendar($cronofyCalendar)
    {
        $this->cronofyCalendar = $cronofyCalendar;
    }

    /**
     *
     * @return string
     */
    public function getCronofyCalendar()
    {
        return $this->cronofyCalendar;
    }

    /**
     *
     * @param string $cronofyProvider
     */
    public function setCronofyProvider($cronofyProvider)
    {
        $this->cronofyProvider = $cronofyProvider;
    }

    /**
     *
     * @return string
     */
    public function getCronofyProvider()
    {
        return $this->cronofyProvider;
    }

    /**
     * @param string $cronofyChannel
     */
    public function setCronofyChannel($cronofyChannel)
    {
        $this->cronofyChannel = $cronofyChannel;
    }

    /**
     * @return string
     */
    public function getCronofyChannel()
    {
        return $this->cronofyChannel;
    }
}