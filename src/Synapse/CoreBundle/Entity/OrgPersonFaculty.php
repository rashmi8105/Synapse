<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrgPersonFaculty
 *
 * @ORM\Table(name="org_person_faculty",indexes={@ORM\Index(name="fk_org_person_faculty_organization1", columns={"organization_id"}), @ORM\Index(name="fk_org_person_faculty_person1", columns={"person_id"})}))
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPersonFacultyRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPersonFaculty extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     *
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     * @JMS\Expose
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     * @JMS\Expose
     */
    private $person;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", length=1, nullable=true)
     *
     */
    private $status;

    /**
     *
     * @var string @ORM\Column(name="auth_key", type="string", length=200, nullable=true)
     *
     */
    private $authKey;

    /**
     *
     * @var string @ORM\Column(name="maf_to_pcs_is_active", type="string", columnDefinition="enum('y','n','i')", options={"default":"n"}, nullable=false)
     */
    private $mafToPcsIsActive;

    /**
     *
     * @var string @ORM\Column(name="pcs_to_maf_is_active", type="string", columnDefinition="enum('y','n')", options={"default":"n"}, nullable=false)
     */
    private $pcsToMafIsActive;

    /**
     *
     * @var string @ORM\Column(name="google_client_id", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $googleClientId;

    /**
     *
     * @var string @ORM\Column(name="google_email_id", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $googleEmailId;

    /**
     *
     * @var string @ORM\Column(name="google_p12_filename", type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    private $googleP12Filename;

    /**
     *
     * @var string @ORM\Column(name="google_page_token", type="string", length=130, nullable=true)
     * @JMS\Expose
     */
    private $googlePageToken;

    /**
     *
     * @var string @ORM\Column(name="google_sync_token", type="string", length=130, nullable=true)
     * @JMS\Expose
     */
    private $googleSyncToken;

    /**
     *
     * @var string @ORM\Column(name="msexchange_sync_state", type="string", length=130, nullable=true)
     * @JMS\Expose
     */
    private $msexchangeSyncState;

    /**
     *
     * @var string @ORM\Column(name="google_sync_status", type="string", columnDefinition="enum('0','1')", options={"default":"0"}, nullable=false)
     */
    private $googleSyncStatus;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="google_sync_disabled_time", type="datetime", nullable=true)
     */
    private $googleSyncDisabledTime;

    /**
     *
     * @var string @ORM\Column(name="oauth_one_time_token", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $oauthOneTimeToken;

    /**
     *
     * @var string @ORM\Column(name="oauth_cal_access_token", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $oauthCalAccessToken;

    /**
     *
     * @var string @ORM\Column(name="oauth_cal_refresh_token", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $oauthCalRefreshToken;

    /**
     *
     * @var string @ORM\Column(name="is_privacy_policy_accepted", type="string", columnDefinition="enum('y','n')", options={"default":"y"}, precision=0, scale=0, nullable=false, unique=false)
     */
    private $isPrivacyPolicyAccepted;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="privacy_policy_accepted_date", type="datetime", nullable=true)
     */
    private $privacyPolicyAcceptedDate;

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
     * Sets the value of authKey.
     *
     * @param string
     *
     * @return self
     */
    public function setAuthKey($authKey)
    {
        $this->authKey = $authKey;

        return $this;
    }

    /**
     * Gets the value of authKey.
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     *
     * @param string $mafToPcsIsActive
     */
    public function setMafToPcsIsActive($mafToPcsIsActive)
    {
        $this->mafToPcsIsActive = $mafToPcsIsActive;
    }

    /**
     *
     * @return string
     */
    public function getMafToPcsIsActive()
    {
        return $this->mafToPcsIsActive;
    }

    /**
     *
     * @param string $pcsToMafIsActive
     */
    public function setPcsToMafIsActive($pcsToMafIsActive)
    {
        $this->pcsToMafIsActive = $pcsToMafIsActive;
    }

    /**
     *
     * @return string
     */
    public function getPcsToMafIsActive()
    {
        return $this->pcsToMafIsActive;
    }

    /**
     *
     * @param string $googleClientId
     */
    public function setGoogleClientId($googleClientId)
    {
        $this->googleClientId = $googleClientId;
    }

    /**
     *
     * @return string
     */
    public function getGoogleClientId()
    {
        return $this->googleClientId;
    }

    /**
     *
     * @param string $googleEmailId
     */
    public function setGoogleEmailId($googleEmailId)
    {
        $this->googleEmailId = $googleEmailId;
    }

    /**
     *
     * @return string
     */
    public function getGoogleEmailId()
    {
        return $this->googleEmailId;
    }

    /**
     *
     * @param string $googleP12Filename
     */
    public function setGoogleP12Filename($googleP12Filename)
    {
        $this->googleP12Filename = $googleP12Filename;
    }

    /**
     *
     * @return string
     */
    public function getGoogleP12Filename()
    {
        return $this->googleP12Filename;
    }

    /**
     *
     * @param string $googlePageToken
     */
    public function setGooglePageToken($googlePageToken)
    {
        $this->googlePageToken = $googlePageToken;
    }

    /**
     *
     * @return string
     */
    public function getGooglePageToken()
    {
        return $this->googlePageToken;
    }

    /**
     *
     * @param string $googleSyncToken
     */
    public function setGoogleSyncToken($googleSyncToken)
    {
        $this->googleSyncToken = $googleSyncToken;
    }

    /**
     *
     * @return string
     */
    public function getGoogleSyncToken()
    {
        return $this->googleSyncToken;
    }

    /**
     *
     * @param string $msexchangeSyncState
     */
    public function setMsexchangeSyncState($msexchangeSyncState)
    {
        $this->msexchangeSyncState = $msexchangeSyncState;
    }

    /**
     *
     * @return string
     */
    public function getMsexchangeSyncState()
    {
        return $this->msexchangeSyncState;
    }

    /**
     *
     * @param string $googleSyncStatus
     */
    public function setGoogleSyncStatus($googleSyncStatus)
    {
        $this->googleSyncStatus = $googleSyncStatus;
    }

    /**
     *
     * @return string
     */
    public function getGoogleSyncStatus()
    {
        return $this->googleSyncStatus;
    }

    /**
     * Set googleSyncDisabledTime
     *
     * @param \DateTime $googleSyncDisabledTime
     */
    public function setGoogleSyncDisabledTime($googleSyncDisabledTime)
    {
        $this->googleSyncDisabledTime = $googleSyncDisabledTime;
        return $this;
    }

    /**
     * Get googleSyncDisabledTime
     *
     * @return \DateTime
     */
    public function getGoogleSyncDisabledTime()
    {
        return $this->googleSyncDisabledTime;
    }

    /**
     *
     * @param string $oauthOneTimeToken
     */
    public function setOauthOneTimeToken($oauthOneTimeToken)
    {
        $this->oauthOneTimeToken = $oauthOneTimeToken;
    }

    /**
     *
     * @return string
     */
    public function getOauthOneTimeToken()
    {
        return $this->oauthOneTimeToken;
    }

    /**
     *
     * @param string $oauthCalAccessToken
     */
    public function setOauthCalAccessToken($oauthCalAccessToken)
    {
        $this->oauthCalAccessToken = $oauthCalAccessToken;
    }

    /**
     *
     * @return string
     */
    public function getOauthCalAccessToken()
    {
        return $this->oauthCalAccessToken;
    }

    /**
     *
     * @param string $oauthCalRefreshToken
     */
    public function setOauthCalRefreshToken($oauthCalRefreshToken)
    {
        $this->oauthCalRefreshToken = $oauthCalRefreshToken;
    }

    /**
     *
     * @return string
     */
    public function getOauthCalRefreshToken()
    {
        return $this->oauthCalRefreshToken;
    }

    /**
     * @param string $isPrivacyPolicyAccepted
     */
    public function setIsPrivacyPolicyAccepted($isPrivacyPolicyAccepted)
    {
        $this->isPrivacyPolicyAccepted = $isPrivacyPolicyAccepted;

    }

    /**
     * @return string
     */
    public function getIsPrivacyPolicyAccepted()
    {
        return $this->isPrivacyPolicyAccepted;
    }

    /**
     * @param \DateTime $privacyPolicyAcceptedDate
     */
    public function setPrivacyPolicyAcceptedDate($privacyPolicyAcceptedDate)
    {
        $this->privacyPolicyAcceptedDate = $privacyPolicyAcceptedDate;
    }

    /**
     * @return \DateTime
     */
    public function getPrivacyPolicyAcceptedDate()
    {
        return $this->privacyPolicyAcceptedDate;
    }
}