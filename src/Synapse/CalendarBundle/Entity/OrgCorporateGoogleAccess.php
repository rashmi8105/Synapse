<?php
namespace Synapse\CalendarBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * OrgCorporateGoogleAccess
 *
 * @ORM\Table(name="org_corporate_google_access", indexes={@ORM\Index(name="fk_org_corporate_google_access_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_org_person_faculty_person1", columns={"person_id"})})))
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Synapse\CalendarBundle\Repository\OrgCorporateGoogleAccessRepository")
 */
class OrgCorporateGoogleAccess extends BaseEntity
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
}