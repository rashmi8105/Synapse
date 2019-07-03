<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Organization
 * It includes Organization,Orgnizationlang,Coordinator info
 *
 * @package Synapse\RestBundle\Entity
 */
class OrganizationDTO
{

    /**
     * Id of an organization.
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Id of an organization's parent organization.
     *
     * @var string @JMS\Type("integer")
     */
    private $parentorganizationid;

    /**
     * Name of an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $name;

    /**
     * Nick-name of an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $nickName;

    /**
     * Sub-domain of an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $subdomain;

    /**
     * Id of a campus within an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $campusId;

    /**
     * Timezone that an organization is in.
     *
     * @var string @JMS\Type("string")
     */
    private $timezone;

    /**
     * Website url of an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $website;

    /**
     * Organization's Current Status.
     *
     * @var string @JMS\Type("string")
     */
    private $status;

    /**
     * Language Organization Uses.
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     */
    private $langid;

    /**
     * Check used to link emails to the organization's coordinator.
     *
     * @var integer @JMS\Type("integer")
     */
    private $isSendLink;

    /**
     * Check used for a determining if a school is Ldap authenticated login or Saml authenticated login.
     *
     * @var string @JMS\Type("string")
     */
    private $isLdapSamlEnabled;

    /**
     * Boolean for whether an organization allows calendar syncing.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $calendarSync;

    /**
     * Point of contact service check, used to turn off calendar integration in mapworks.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $pcsRemove;

    /**
     * Returns an organization's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets an organization's id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the name of an organization.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of an organization.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns an organization's nickname.
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * Sets an organization's nickname.
     *
     * @param string $nickName
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    /**
     * Returns the id of an organization's parent organization.
     *
     * @return int
     */
    public function getParentorganizationid()
    {
        return $this->parentorganizationid;
    }

    /**
     * Sets the id of an organization's parent organization.
     *
     * @param int $parentorganizationid
     */
    public function setParentorganizationid($parentorganizationid)
    {
        $this->parentorganizationid = $parentorganizationid;
    }

    /**
     * Returns the subdomain of an organization.
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Sets the subdomain of an organization.
     *
     * @param string $subdomain
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;
    }

    /**
     * Returns the id of a campus.
     *
     * @return string
     */
    public function getCampusId()
    {
        return $this->campusId;
    }

    /**
     * Sets the id of a campus.
     *
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
        $this->campus = $campusId;
    }

    /**
     * Returns the timezone of an organization.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Sets the timezone of an organization.
     *
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Returns an organization's website URL.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Sets an organization's website URL.
     *
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Return the status of an organization.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status of an organization.
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the language id of an organization.
     *
     * @return int
     */
    public function getLangid()
    {
        return $this->langid;
    }

    /**
     * Sets the language id of an organization.
     *
     * @param int $langid
     */
    public function setLangid($langid)
    {
        $this->langid = $langid;
    }

    /**
     * Returns the 'is send' link.
     *
     * @return int
     */
    public function getIsSendLink()
    {
        return $this->isSendLink;
    }

    /**
     * Set the 'is send' link.
     *
     * @param int $isSendLink
     */
    public function setIsSendLink($isSendLink)
    {
        $this->isSendLink = $isSendLink;
    }

    /**
     * Returns the check for is a school Ldap authenticated or Saml authenticated.
     *
     * @return boolean
     */
    public function getIsLdapSamlEnabled()
    {
        return $this->isLdapSamlEnabled;
    }

    /**
     * Sets the check for is a school Ldap authenticated or Saml authenticated.
     *
     * @param boolean $isLdapSamlEnabled
     */
    public function setIsLdapSamlEnabled($isLdapSamlEnabled)
    {
        $this->isLdapSamlEnabled = $isLdapSamlEnabled;
    }

    /**
     * Returns calendar sync check.
     *
     * @return boolean
     */
    public function getCalendarSync()
    {
        return $this->calendarSync;
    }

    /**
     * Sets calendar sync check.
     *
     * @param boolean $calendarSync
     */
    public function setCalendarSync($calendarSync)
    {
        $this->calendarSync = $calendarSync;
    }

    /**
     * Returns point of contact service boolean check.
     *
     * @return boolean
     */
    public function getPcsRemove()
    {
        return $this->pcsRemove;
    }

    /**
     * Sets point of contact services boolean check.
     *
     * @param boolean $pcsRemove
     */
    public function setPcsRemove($pcsRemove)
    {
        $this->pcsRemove = $pcsRemove;
    }
}
