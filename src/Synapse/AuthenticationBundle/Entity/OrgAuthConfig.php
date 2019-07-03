<?php
namespace Synapse\AuthenticationBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgAuthConfig
 *
 * @ORM\Table(name="org_auth_config", indexes={@ORM\Index(name="fk_org_auth_config_organization1_idx", columns={"org_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AuthenticationBundle\Repository\OrgAuthConfigRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgAuthConfig extends BaseEntity
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="org_id", nullable=false, referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var string
     * @ORM\Column(name="campus_portal_login_url", type="string", nullable=true)
     */
    private $campusPortalLoginUrl;

    /**
     * @var string
     * @ORM\Column(name="campus_portal_logout_url", type="string", nullable=true)
     */
    private $campusPortalLogoutUrl;

    /**
     * @var bool
     * @ORM\Column(name="campus_portal_student_enabled", type="boolean", nullable=true)
     */
    private $campusPortalStudentEnabled;

    /**
     * @var string
     * @ORM\Column(name="campus_portal_student_key", type="string", length=64, nullable=true)
     */
    private $campusPortalStudentKey;

    /**
     * @var bool
     * @ORM\Column(name="campus_portal_staff_enabled", type="boolean", nullable=true)
     */
    private $campusPortalStaffEnabled;

    /**
     * @var string
     * @ORM\Column(name="campus_portal_staff_key", type="string", length=64, nullable=true)
     */
    private $campusPortalStaffKey;

    /**
     * @var bool
     * @ORM\Column(name="ldap_student_enabled", type="boolean", nullable=true)
     */
    private $ldapStudentEnabled;

    /**
     * @var bool
     * @ORM\Column(name="ldap_staff_enabled", type="boolean", nullable=true)
     */
    private $ldapStaffEnabled;

    /**
     * @var bool
     * @ORM\Column(name="saml_student_enabled", type="boolean", nullable=true)
     */
    private $samlStudentEnabled;

    /**
     * @var bool
     * @ORM\Column(name="saml_staff_enabled", type="boolean", nullable=true)
     */
    private $samlStaffEnabled;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Synapse\CoreBundle\Entity\Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param bool $campusPortalStudentEnabled
     */
    public function setCampusPortalStudentEnabled($campusPortalStudentEnabled)
    {
        $this->campusPortalStudentEnabled = $campusPortalStudentEnabled;
    }

    /**
     * @return bool
     */
    public function getCampusPortalStudentEnabled()
    {
        return $this->campusPortalStudentEnabled;
    }

    /**
     * @param bool $campusPortalStudentKey
     */
    public function setCampusPortalStudentKey($campusPortalStudentKey)
    {
        $this->campusPortalStudentKey = $campusPortalStudentKey;
    }

    /**
     * @return bool
     */
    public function getCampusPortalStudentKey()
    {
        return $this->campusPortalStudentKey;
    }

    /**
     * @param bool $campusPortalStaffEnabled
     */
    public function setCampusPortalStaffEnabled($campusPortalStaffEnabled)
    {
        $this->campusPortalStaffEnabled = $campusPortalStaffEnabled;
    }

    /**
     * @return bool
     */
    public function getCampusPortalStaffEnabled()
    {
        return $this->campusPortalStaffEnabled;
    }

    /**
     * @param bool $campusPortalStaffKey
     */
    public function setCampusPortalStaffKey($campusPortalStaffKey)
    {
        $this->campusPortalStaffKey = $campusPortalStaffKey;
    }

    /**
     * @return bool
     */
    public function getCampusPortalStaffKey()
    {
        return $this->campusPortalStaffKey;
    }

    /**
     * @param bool $ldapStudentEnabled
     */
    public function setLdapStudentEnabled($ldapStudentEnabled)
    {
        $this->ldapStudentEnabled = $ldapStudentEnabled;
    }

    /**
     * @return bool
     */
    public function getLdapStudentEnabled()
    {
        return $this->ldapStudentEnabled;
    }

    /**
     * @param bool $ldapStaffEnabled
     */
    public function setLdapStaffEnabled($ldapStaffEnabled)
    {
        $this->ldapStaffEnabled = $ldapStaffEnabled;
    }

    /**
     * @return bool
     */
    public function getLdapStaffEnabled()
    {
        return $this->ldapStaffEnabled;
    }

    /**
     * @param bool $samlStudentEnabled
     */
    public function setSamlStudentEnabled($samlStudentEnabled)
    {
        $this->samlStudentEnabled = $samlStudentEnabled;
    }

    /**
     * @return bool
     */
    public function getSamlStudentEnabled()
    {
        return $this->samlStudentEnabled;
    }

    /**
     * @param bool $samlStaffEnabled
     */
    public function setSamlStaffEnabled($samlStaffEnabled)
    {
        $this->samlStaffEnabled = $samlStaffEnabled;
    }

    /**
     * @return bool
     */
    public function getSamlStaffEnabled()
    {
        return $this->samlStaffEnabled;
    }


    /**
     * Sets the value of campusPortalLoginUrl.
     *
     * @param string $campusPortalLoginUrl the campus portal login url
     *
     * @return self
     */
    public function setCampusPortalLoginUrl($campusPortalLoginUrl)
    {
        $this->campusPortalLoginUrl = $campusPortalLoginUrl;

        return $this;
    }

    /**
     * Gets the value of campusPortalLoginUrl.
     *
     * @return string
     */
    public function getCampusPortalLoginUrl()
    {
        return $this->campusPortalLoginUrl;
    }

    /**
     * Sets the value of campusPortalLogoutUrl.
     *
     * @param string $campusPortalLogoutUrl the campus portal logout url
     *
     * @return self
     */
    public function setCampusPortalLogoutUrl($campusPortalLogoutUrl)
    {
        $this->campusPortalLogoutUrl = $campusPortalLogoutUrl;

        return $this;
    }

    /**
     * Gets the value of campusPortalLogoutUrl.
     *
     * @return string
     */
    public function getCampusPortalLogoutUrl()
    {
        return $this->campusPortalLogoutUrl;
    }
}