<?php
namespace Synapse\CalendarBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Calendar
 *
 * @package Synapse\CalendarBundle\EntityDto
 */
class SyncFacultySettingsDto
{

    /**
     * Specifies the type of calendar, i.e. 'google, outlook'
     *
     * @var string @JMS\Type("string")
     *
     */
    private $calendarType;

    /**
     * Token provided by external calendar(PCS) to sync appointments with Mapworks
     *
     * @var string @JMS\Type("string")
     *
     */
    private $clientId;

    /**
     * Email address that the user wants to sync their appointments to
     *
     * @var string @JMS\Type("string")
     *
     */
    private $emailId;

    /**
     * key DEPRECATED
     *
     * @var string @JMS\Type("string")
     */
    private $key;

    /**
     * Boolean for whether or not to push mapworks sync data to PCS(Point of Control System) i.e. outlook, gmail
     *
     * @var boolean @JMS\Type("boolean")
     *
     */
    private $mafToPcs;

    /**
     * Id of the organization that a faculty member belongs to
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $organizationId;

    /**
     * Boolean to remove sync between PCS(Point of Control System)
     *
     * @var boolean @JMS\Type("boolean")
     *
     */
    private $pcsRemove;

    /**
     * Boolean for whether or not to push PCS(Point of Control System) data to mapworks
     *
     * @var boolean @JMS\Type("boolean")
     *
     */
    private $pcsToMaf;

    /**
     * Id specifying which user the faculty sync settings apply to
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $personId;

    /**
     * Google, Outlook, Exchange, Office365 and iCloud. User will select one of these providers to sync mapworks data to
     *
     * @var string @JMS\Type("string")
     *
     */
    private $providerName;

    /**
     * Boolean to remove syncing from mapworks to PCS
     *
     * @var boolean @JMS\Type("boolean")
     *
     */
    private $removeMafToPcs;

    /**
     * Boolean to remove syncing from PCS to mapworks
     *
     * @var boolean @JMS\Type("boolean")
     *
     */
    private $removePcsToMaf;

    /**
     * serviceAccountEmail DEPRECATED
     *
     * @var string @JMS\Type("string")
     */
    private $serviceAccountEmail;

    /**
     * Boolean for whether sync is enabled or disabled by the user
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $syncOption;

    /**
     *
     * @return string
     */
    public function getCalendarType()
    {
        return $this->calendarType;
    }

    /**
     *
     * @param string $calendarType
     */
    public function setCalendarType($calendarType)
    {
        $this->calendarType = $calendarType;
    }

    /**
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }
    /**
     *
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     *
     * @return string
     */
    public function getEmailId()
    {
        return $this->emailId;
    }

    /**
     *
     * @param string $emailId
     */
    public function setEmailId($emailId)
    {
        $this->emailId = $emailId;
    }

    /**
     * DEPRECATED
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * DEPRECATED
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @return boolean
     */
    public function getMafToPcs()
    {
        return $this->mafToPcs;
    }

    /**
     *
     * @param boolean $mafToPcs
     */
    public function setMafToPcs($mafToPcs)
    {
        $this->mafToPcs = $mafToPcs;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return boolean
     */
    public function getPcsRemove()
    {
        return $this->pcsRemove;
    }
    /**
     *
     * @param boolean $pcsRemove
     */
    public function setPcsRemove($pcsRemove)
    {
        $this->pcsRemove = $pcsRemove;
    }

    /**
     *
     * @return boolean
     */
    public function getPcsToMaf()
    {
        return $this->pcsToMaf;
    }
    /**
     *
     * @param boolean $pcsToMaf
     */
    public function setPcsToMaf($pcsToMaf)
    {
        $this->pcsToMaf = $pcsToMaf;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }
    /**
     *
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }
    /**
     *
     * @param string $providerName
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     *
     * @return boolean
     */
    public function getRemoveMafToPcs()
    {
        return $this->removeMafToPcs;
    }
    /**
     *
     * @param boolean $removeMafToPcs
     */
    public function setRemoveMafToPcs($removeMafToPcs)
    {
        $this->removeMafToPcs = $removeMafToPcs;
    }

    /**
     *
     * @return boolean
     */
    public function getRemovePcsToMaf()
    {
        return $this->removePcsToMaf;
    }

    /**
     *
     * @param boolean $removePcsToMaf
     */
    public function setRemovePcsToMaf($removePcsToMaf)
    {
        $this->removePcsToMaf = $removePcsToMaf;
    }

    /**
     * DEPRECATED
     *
     * @return string
     */
    public function getServiceAccountEmail()
    {
        return $this->serviceAccountEmail;
    }

    /**
     * DEPRECATED
     *
     * @param string $serviceAccountEmail
     */
    public function setServiceAccountEmail($serviceAccountEmail)
    {
        $this->serviceAccountEmail = $serviceAccountEmail;
    }

    /**
     *
     * @return boolean
     */
    public function getSyncOption()
    {
        return $this->syncOption;
    }

    /**
     *
     * @param boolean $syncOption
     */
    public function setSyncOption($syncOption)
    {
        $this->syncOption = $syncOption;
    }

}