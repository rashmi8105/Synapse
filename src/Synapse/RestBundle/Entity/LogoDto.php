<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class LogoDto
{

    /**
     * Id of the organization that has a logo.
     *
     * @var string @JMS\Type("integer")
     *      @Assert\NotBlank()
     */
    private $organizationId;

    /**
     * Id of the person changing an organization's logo.
     *
     * @var string @JMS\Type("integer")
     */
    private $personId;

    /**
     * Primary color for an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $primaryColor;

    /**
     * Secondary color for an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $secondaryColor;

    /**
     * File name of the logo for an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $logoFileName;

    /**
     * String containing EBI's confidentiality statement.
     *
     * @var string @JMS\Type("string")
     */
    private $ebiConfidentialityStatement;

    /**
     * Amount of time before a person can be logged-out for inactivity.
     *
     * @var int @JMS\Type("string")
     *      @Assert\Range(
     *      min = 1,
     *      max = 500,
     *      minMessage = "Inactivity timeout must be at least 1 min",
     *      maxMessage = "Inactivity timeout cannot exceed 500 min"
     *      )
     */
    private $inactivityTimeout;

    /**
     * Boolean allowing academic update notifications.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $academicUpdateNotification;

    /**
     * Boolean to allow students to be referred for academic assistance.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $referForAcademicAssistance;

    /**
     * Boolean that allows notifications to be sent to students.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $sendToStudent;
    
    /**
     * Boolean allowing users to view in-progress grades.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $canViewInProgressGrade;
    
    /**
     * Boolean allowing users to view absences.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $canViewAbsences;
    
    /**
     * Boolean allowing users to view comments.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $canViewComments;

    /**
     * Type of calendar that an organization uses.
     *
     * @var string @JMS\Type("string")
     */
    private $calendarType;

    /**
     * Type of exchange server that an organization uses.
     *
     * @var string @JMS\Type("string")
     */
    private $exchangeServerType;

    /**
     * Exchange URL for an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $exchangeUrl;

    /**
     * Username for the service account of an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $serviceAccountUsername;

    /**
     * Password for an organization's service account.
     *
     * @var string @JMS\Type("string")
     */
    private $serviceAccountPassword;

    /**
     * Boolean allowing syncing.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $syncOption;
    
    /**
     * Boolean allowing corporate access.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $corporateAccess ;

    /**
     * Boolean allowing calendar sync.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $calendarSync;

    /**
     * Boolean to remove external calendar.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $pcsRemove;

    /**
     * Users that have access to calendar syncing.
     *
     * @var string @JMS\Type("integer")
     */
    private $calendarSyncUsers;


    /**
     * Sets the logo for an organization's logo.
     *
     * @param string $logoFileName            
     */
    public function setLogoFileName($logoFileName)
    {
        $this->logoFileName = $logoFileName;
    }

    /**
     * Returns the logo for an organization's logo.
     *
     * @return string
     */
    public function getLogoFileName()
    {
        return $this->logoFileName;
    }

    /**
     * Sets the id for an organization.
     *
     * @param string $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the id for an organization.
     *
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the primary color for an organization.
     *
     * @param string $primaryColor            
     */
    public function setPrimaryColor($primaryColor)
    {
        $this->primaryColor = $primaryColor;
    }

    /**
     * Returns the primary color for an organization.
     *
     * @return string
     */
    public function getPrimaryColor()
    {
        return $this->primaryColor;
    }

    /**
     * Sets the secondary color for an organization.
     *
     * @param string $secondaryColor            
     */
    public function setSecondaryColor($secondaryColor)
    {
        $this->secondaryColor = $secondaryColor;
    }

    /**
     * Returns the secondary color for an organization.
     *
     * @return string
     */
    public function getSecondaryColor()
    {
        return $this->secondaryColor;
    }

    /**
     * Sets the confidentiality statement for an organization.
     *
     * @param string $ebiConfidentialityStatement            
     */
    public function setEbiConfidentialityStatement($ebiConfidentialityStatement)
    {
        $this->ebiConfidentialityStatement = $ebiConfidentialityStatement;
    }

    /**
     * Returns the confidentiality statement for an organization.
     *
     * @return string
     */
    public function getEbiConfidentialityStatement()
    {
        return $this->ebiConfidentialityStatement;
    }

    /**
     * Returns the inactivity timeout for an organization.
     *
     * @return int
     */
    public function getInactivityTimeout()
    {
        return $this->inactivityTimeout;
    }

    /**
     * Sets the inactivity timeout for an organization.
     *
     * @param int $inactivityTimeout            
     */
    public function setInactivityTimeout($inactivityTimeout)
    {
        $this->inactivityTimeout = $inactivityTimeout;
    }

    /**
     * Sets whether an organization allows academic update notifications.
     *
     * @param boolean $academicUpdateNotification            
     */
    public function setAcademicUpdateNotification($academicUpdateNotification)
    {
        $this->academicUpdateNotification = $academicUpdateNotification;
    }

    /**
     * Returns whether an organization allows academic update notifications.
     *
     * @return boolean
     */
    public function getAcademicUpdateNotification()
    {
        return $this->academicUpdateNotification;
    }

    /**
     * Sets whether an organization allows academic assistance referrals.
     *
     * @param boolean $referForAcademicAssistance            
     */
    public function setReferForAcademicAssistance($referForAcademicAssistance)
    {
        $this->referForAcademicAssistance = $referForAcademicAssistance;
    }

    /**
     * Returns whether an organization allows academic assistance referrals.
     *
     * @return boolean
     */
    public function getReferForAcademicAssistance()
    {
        return $this->referForAcademicAssistance;
    }

    /**
     * Sets whether notifications can be sent to students.
     *
     * @param boolean $sendToStudent            
     */
    public function setSendToStudent($sendToStudent)
    {
        $this->sendToStudent = $sendToStudent;
    }

    /**
     * Returns whether students can receive notifications.
     *
     * @return boolean
     */
    public function getSendToStudent()
    {
        return $this->sendToStudent;
    }

    /**
     * Sets the person's id.
     *
     * @param int $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * Returns the person's id.
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Sets the type of exchange server.
     *
     * @param string $exchangeServerType            
     */
    public function setExchangeServerType($exchangeServerType)
    {
        $this->exchangeServerType = $exchangeServerType;
    }

    /**
     * Returns the type of exchange server.
     *
     * @return string
     */
    public function getExchangeServerType()
    {
        return $this->exchangeServerType;
    }

    /**
     * Sets the exchange url for an organization.
     *
     * @param string $exchangeUrl            
     */
    public function setExchangeUrl($exchangeUrl)
    {
        $this->exchangeUrl = $exchangeUrl;
    }

    /**
     * Returns the exchange url for an organization.
     *
     * @return string
     */
    public function getExchangeUrl()
    {
        return $this->exchangeUrl;
    }

    /**
     * Sets the calendar type.
     *
     * @param string $calendarType            
     */
    public function setCalendarType($calendarType)
    {
        $this->calendarType = $calendarType;
    }

    /**
     * Returns the calendar type being used.
     *
     * @return string
     */
    public function getCalendarType()
    {
        return $this->calendarType;
    }

    /**
     * Sets the username for a service account.
     *
     * @param string $serviceAccountUsername            
     */
    public function setServiceAccountUsername($serviceAccountUsername)
    {
        $this->serviceAccountUsername = $serviceAccountUsername;
    }

    /**
     * Returns the user name for a service account.
     *
     * @return string
     */
    public function getServiceAccountUsername()
    {
        return $this->serviceAccountUsername;
    }

    /**
     * Sets the password for a service account.
     *
     * @param string $serviceAccountPassword            
     */
    public function setServiceAccountPassword($serviceAccountPassword)
    {
        $this->serviceAccountPassword = $serviceAccountPassword;
    }

    /**
     * Returns the password for a service account.
     *
     * @return string
     */
    public function getServiceAccountPassword()
    {
        return $this->serviceAccountPassword;
    }

    /**
     * Sets whether sync options are available or not.
     *
     * @param boolean $syncOption            
     */
    public function setSyncOption($syncOption)
    {
        $this->syncOption = $syncOption;
    }

    /**
     * Returns whether sync options are available or not.
     *
     * @return boolean
     */
    public function getSyncOption()
    {
        return $this->syncOption;
    }

    /**
     * @param boolean $canViewAbsences
     */
    public function setCanViewAbsences($canViewAbsences)
    {
        $this->canViewAbsences = $canViewAbsences;
    }

    /**
     * Returns whether an organization allows viewing absences.
     *
     * @return boolean
     */
    public function getCanViewAbsences()
    {
        return $this->canViewAbsences;
    }

    /**
     * Sets whether an organization allows in-progress grades to be viewed.
     *
     * @param boolean $canViewInProgressGrade
     */
    public function setCanViewInProgressGrade($canViewInProgressGrade)
    {
        $this->canViewInProgressGrade = $canViewInProgressGrade;
    }

    /**
     * Returns whether an organization allows in-progress grades to be viewed.
     *
     * @return boolean
     */
    public function getCanViewInProgressGrade()
    {
        return $this->canViewInProgressGrade;
    }

    /**
     * Sets whether an organization allows comments to be viewed.
     *
     * @param boolean $canViewComments
     */
    public function setCanViewComments($canViewComments)
    {
        $this->canViewComments = $canViewComments;
    }

    /**
     * Returns whether an organization allows comments to be viewed.
     *
     * @return boolean
     */
    public function getCanViewComments()
    {
        return $this->canViewComments;
    }
    
    /**
     * Sets whether an organization allows corporate access.
     *
     * @param boolean $corporateAccess            
     */
    public function setCorporateAccess($corporateAccess)
    {
        $this->corporateAccess = $corporateAccess;
    }

    /**
     * Returns whether an organization allows corporate access.
     *
     * @return boolean
     */
    public function getCorporateAccess()
    {
        return $this->corporateAccess;
    }

    /**
     * Sets whether an organization allows calendar syncing.
     *
     * @param boolean $calendarSync
     */
    public function setCalendarSync($calendarSync)
    {
        $this->calendarSync = $calendarSync;
    }

    /**
     * Returns whether an organization allows calendar syncing.
     *
     * @return boolean
     */
    public function getCalendarSync()
    {
        return $this->calendarSync;
    }

    /**
     * Sets whether an external calendar is being removed.
     *
     * @param boolean $pcsRemove
     */
    public function setPcsRemove($pcsRemove)
    {
        $this->pcsRemove = $pcsRemove;
    }

    /**
     * Returns whether an external calendar is being removed.
     *
     * @return boolean
     */
    public function getPcsRemove()
    {
        return $this->pcsRemove;
    }

    /**
     * Sets how many users have calendar sync.
     *
     * @param int $calendarSyncUsers
     */
    public function setCalendarSyncUsers($calendarSyncUsers)
    {
        $this->calendarSyncUsers = $calendarSyncUsers;
    }

    /**
     * Returns how many users have calendar sync.
     *
     * @return int
     */
    public function getCalendarSyncUsers()
    {
        return $this->calendarSyncUsers;
    }
}