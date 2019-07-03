<?php
namespace Synapse\AuthenticationBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for authentication settings
 * *
 *
 * @package Synapse\AuthenticationBundle\EntityDto
 *
 */
class AuthConfigDto
{

    /**
     * organization
     *
     * @var string @JMS\Type("integer")
     *
     */
    private $organization;

    /**
     * campusportalloginurl
     *
     * @var string @JMS\Type("string")
     *
     */
    private $campusPortalLoginUrl;

    /**
     * campusportallogouturl
     *
     * @var string @JMS\Type("string")
     *
     */
    private $campusPortalLogoutUrl;

    /**
     * campusportalstudentenabled
     *
     * @var string @JMS\Type("boolean")
     *
     */
    private $campusPortalStudentEnabled;

    /**
     * campusportalstudentkey
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(min = 64,
     *      max = 64,
     *      minMessage = "description must be at least {{ limit }} characters long",
     *      maxMessage = "description cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $campusPortalStudentKey;

    /**
     * campusportalstaffenabled
     *
     * @var string @JMS\Type("boolean")
     *
     */
    private $campusPortalStaffEnabled;

    /**
     * campusportalstaffkey
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(min = 64,
     *      max = 64,
     *      minMessage = "description must be at least {{ limit }} characters long",
     *      maxMessage = "description cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $campusPortalStaffKey;

    /**
     * ldapstudentenabled
     *
     * @var string @JMS\Type("boolean")
     *
     */
    private $ldapStudentEnabled;

    /**
     * ldapstaffenabled
     *
     * @var string @JMS\Type("boolean")
     *
     */
    private $ldapStaffEnabled;

    /**
     * samlstudentenabled
     *
     * @var string @JMS\Type("boolean")
     *
     */
    private $samlStudentEnabled;

    /**
     * samlstaffenabled
     *
     * @var string @JMS\Type("boolean")
     *
     */
    private $samlStaffEnabled;

    /**
     * ldapauthconfig
     *
     * @var string @JMS\Type("Synapse\AuthenticationBundle\EntityDto\LdapConfigDto")
     *
     */
    private $ldapConfig;

    /**
     * samlauthconfig
     *
     * @var string @JMS\Type("Synapse\AuthenticationBundle\EntityDto\SamlConfigDto")
     *
     */
    private $samlConfig;


    /**
     * Gets the organization.
     *
     * @return string @JMS\Type("integer")
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Sets the organization.
     *
     * @param string @JMS\Type("integer") $organization the organization
     *
     * @return self
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Gets the campusportalstudentenabled.
     *
     * @return string @JMS\Type("boolean")
     */
    public function getCampusPortalStudentEnabled()
    {
        return $this->campusPortalStudentEnabled;
    }

    /**
     * Sets the campusportalstudentenabled.
     *
     * @param string @JMS\Type("boolean") $campusPortalStudentEnabled the campus portal student enabled
     *
     * @return self
     */
    public function setCampusPortalStudentEnabled($campusPortalStudentEnabled)
    {
        $this->campusPortalStudentEnabled = $campusPortalStudentEnabled;

        return $this;
    }

    /**
     * Gets the campusportalstudentkey
     *
     * @return string @JMS\Type("string")
     */
    public function getCampusPortalStudentKey()
    {
        return $this->campusPortalStudentKey;
    }

    /**
     * Sets the campusportalstudentkey
     *
     * @param string @JMS\Type("string") $campusPortalStudentKey the campus portal student key
     *
     * @return self
     */
    public function setCampusPortalStudentKey($campusPortalStudentKey)
    {
        $this->campusPortalStudentKey = $campusPortalStudentKey;

        return $this;
    }

    /**
     * Gets the campusportalstaffenabled.
     *
     * @return string @JMS\Type("boolean")
     */
    public function getCampusPortalStaffEnabled()
    {
        return $this->campusPortalStaffEnabled;
    }

    /**
     * Sets the campusportalstaffenabled.
     *
     * @param string @JMS\Type("boolean") $campusPortalStaffEnabled the campus portal staff enabled
     *
     * @return self
     */
    public function setCampusPortalStaffEnabled($campusPortalStaffEnabled)
    {
        $this->campusPortalStaffEnabled = $campusPortalStaffEnabled;

        return $this;
    }

    /**
     * Gets the campusportalstaffkey
     *
     * @return string @JMS\Type("string")
     */
    public function getCampusPortalStaffKey()
    {
        return $this->campusPortalStaffKey;
    }

    /**
     * Sets the campusportalstaffkey
     *
     * @param string @JMS\Type("string") $campusPortalStaffKey the campus portal staff key
     *
     * @return self
     */
    public function setCampusPortalStaffKey($campusPortalStaffKey)
    {
        $this->campusPortalStaffKey = $campusPortalStaffKey;

        return $this;
    }

    /**
     * Gets the ldapstudentenabled.
     *
     * @return string @JMS\Type("boolean")
     */
    public function getLdapStudentEnabled()
    {
        return $this->ldapStudentEnabled;
    }

    /**
     * Sets the ldapstudentenabled.
     *
     * @param string @JMS\Type("boolean") $ldapStudentEnabled the ldap student enabled
     *
     * @return self
     */
    public function setLdapStudentEnabled($ldapStudentEnabled)
    {
        $this->ldapStudentEnabled = $ldapStudentEnabled;

        return $this;
    }

    /**
     * Gets the ldapstaffenabled.
     *
     * @return string @JMS\Type("boolean")
     */
    public function getLdapStaffEnabled()
    {
        return $this->ldapStaffEnabled;
    }

    /**
     * Sets the ldapstaffenabled.
     *
     * @param string @JMS\Type("boolean") $ldapStaffEnabled the ldap staff enabled
     *
     * @return self
     */
    public function setLdapStaffEnabled($ldapStaffEnabled)
    {
        $this->ldapStaffEnabled = $ldapStaffEnabled;

        return $this;
    }

    /**
     * Gets the samlstudentenabled.
     *
     * @return string @JMS\Type("boolean")
     */
    public function getSamlStudentEnabled()
    {
        return $this->samlStudentEnabled;
    }

    /**
     * Sets the samlstudentenabled.
     *
     * @param string @JMS\Type("boolean") $samlStudentEnabled the saml student enabled
     *
     * @return self
     */
    public function setSamlStudentEnabled($samlStudentEnabled)
    {
        $this->samlStudentEnabled = $samlStudentEnabled;

        return $this;
    }

    /**
     * Gets the samlstaffenabled.
     *
     * @return string @JMS\Type("boolean")
     */
    public function getSamlStaffEnabled()
    {
        return $this->samlStaffEnabled;
    }

    /**
     * Sets the samlstaffenabled.
     *
     * @param string @JMS\Type("boolean") $samlStaffEnabled the saml staff enabled
     *
     * @return self
     */
    public function setSamlStaffEnabled($samlStaffEnabled)
    {
        $this->samlStaffEnabled = $samlStaffEnabled;

        return $this;
    }

    /**
     * Gets the ldapauthconfig.
     *
     * @return string @JMS\Type("Synapse\AuthenticationBundle\EntityDto\LdapConfigDto")
     */
    public function getLdapConfig()
    {
        return $this->ldapConfig;
    }

    /**
     * Sets the ldapauthconfig.
     *
     * @param string @JMS\Type("Synapse\AuthenticationBundle\EntityDto\LdapConfigDto") $ldapConfig the ldap config
     *
     * @return self
     */
    public function setLdapConfig($ldapConfig)
    {
        $this->ldapConfig = $ldapConfig;

        return $this;
    }

    /**
     * Gets the samlauthconfig.
     *
     * @return string @JMS\Type("Synapse\AuthenticationBundle\EntityDto\SamlConfigDto")
     */
    public function getSamlConfig()
    {
        return $this->samlConfig;
    }

    /**
     * Sets the samlauthconfig.
     *
     * @param string @JMS\Type("Synapse\AuthenticationBundle\EntityDto\SamlConfigDto") $samlConfig the saml config
     *
     * @return self
     */
    public function setSamlConfig($samlConfig)
    {
        $this->samlConfig = $samlConfig;

        return $this;
    }

    /**
     * Sets the campusportalloginurl.
     *
     * @param string @JMS\Type("string") $campusPortalLoginUrl the campus portal login url
     *
     * @return self
     */
    public function setCampusPortalLoginUrl($campusPortalLoginUrl)
    {
        $this->campusPortalLoginUrl = $campusPortalLoginUrl;

        return $this;
    }

    /**
     * Gets the campusportalloginurl.
     *
     * @return string @JMS\Type("string")
     */
    public function getCampusPortalLoginUrl()
    {
        return $this->campusPortalLoginUrl;
    }

    /**
     * Sets the campusportallogouturl.
     *
     * @param string @JMS\Type("string") $campusPortalLogoutUrl the campus portal logout url
     *
     * @return self
     */
    public function setCampusPortalLogoutUrl($campusPortalLogoutUrl)
    {
        $this->campusPortalLogoutUrl = $campusPortalLogoutUrl;

        return $this;
    }

    /**
     * Gets the campusportallogouturl.
     *
     * @return string @JMS\Type("string")
     */
    public function getCampusPortalLogoutUrl()
    {
        return $this->campusPortalLogoutUrl;
    }
}