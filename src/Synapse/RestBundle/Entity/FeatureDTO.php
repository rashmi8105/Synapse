<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Synapse\CoreBundle\Entity\OrgFeatures
 *
 * 
 * @package Synapse\RestBundle\Entity
 */
class FeatureDTO
{

    /**
     * Boolean that determines whether an organization has booking enabled or not.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isBookingEnabled;

    /**
     * Sets the id of the specific email feature an organization has.
     *
     * @param int $emailFeatureId
     */
    public function setEmailFeatureId($emailFeatureId)
    {
        $this->emailFeatureId = $emailFeatureId;
    }

    /**
     * Returns the id of the specific email feature an organization has.
     *
     * @return int
     */
    public function getEmailFeatureId()
    {
        return $this->emailFeatureId;
    }

    /**
     *
     *
     * @param int $emailOrgId
     */
    public function setEmailOrgId($emailOrgId)
    {
        $this->emailOrgId = $emailOrgId;
    }

    /**
     *
     *
     * @return int
     */
    public function getEmailOrgId()
    {
        return $this->emailOrgId;
    }

    /**
     * Sets whether an organization has emails enabled or not.
     *
     * @param boolean $isEmailEnabled
     */
    public function setIsEmailEnabled($isEmailEnabled)
    {
        $this->isEmailEnabled = $isEmailEnabled;
    }

    /**
     * Returns whether an organization has emails enabled or not.
     *
     * @return boolean
     */
    public function getIsEmailEnabled()
    {
        return $this->isEmailEnabled;
    }

    /**
     *
     * 
     * @var integer @JMS\Type("integer")
     */
    private $referralOrgId;

    /**
     * notesOrgId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $notesOrgId;

    /**
     * logContactsOrgId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $logContactsOrgId;

    /**
     * bookingOrgId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $bookingOrgId;
    
    /**
     * Id of the specific email feature an organization has.
     *
     * @var integer; @JMS\Type("integer")
     */
    private $emailFeatureId;
    
    /**
     * bookingOrgId
     *
     * @var integer @JMS\Type("integer")
     */
    private $emailOrgId;

    /**
     * Boolean determining whether an organization has emails enabled or not.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isEmailEnabled;

    /**
     * Boolean determining whether notifications for student referrals are enabled or not.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isStudentReferralNotificationEnabled;

    /**
     * Boolean determining whether or not reason routing is enabled.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isReasonRoutingEnabled;

    /**
     * Id of the specific referral notification feature an organization has.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $studentReferralNotificationFeatureId;

    /**
     * Id of the specific reason routing feature an organization has.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reasonRoutingFeatureId;

    /**
     * studentReferralNotificationOrgId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $studentReferralNotificationOrgId;

    /**
     * $reasonRoutingOrgId
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reasonRoutingOrgId;

    /**
     * Id of an organization using mapworks.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Id of the notes feature that an organization has.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $notesFeatureId;

    /**
     * Id of the specific referrals feature an organization has.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $referralFeatureId;

    /**
     * Id of the specific contact logging feature an organization has.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $logContactsFeatureId;

    /**
     * Id of the specific booking feature that an organization has.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $bookingFeatureId;

    /**
     * Boolean determining whether notes are enabled for an organization or not.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isNotesEnabled;

    /**
     * Boolean determining whether contact logs enabled for an organization or not.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isLogContactsEnabled;

    /**
     * referralsConnected;
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isReferralEnabled;

    /**
     * reasonRoutingList;
     * 
     * @var array @JMS\Type("array")
     */
    private $reasonRoutingList;

    /**
     * Deprecated attribute of an organization. All organization's langId's are 1(ENGLISH).
     * 
     * @var integer @JMS\Type("integer")
     */
    private $langId;

    /**
     * Boolean determining whether a person is a primary coordinator or not.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $isPrimaryCoordinator;
    
    /**
     * Boolean determining whether routing referrals to an organization's primary campus connection is enabled or not.
     *
     * @var boolean; @JMS\Type("boolean")
     */
    private $isPrimaryCampusConnectionReferralRoutingEnabled;
    
    /**
     * Id of an organization that has enabled routing referrals to an organization's primary campus connection.
     *
     * @var integer; @JMS\Type("integer")
     */
    private $primaryCampusConnectionReferralRoutingOrgId;
    
    /**
     * Id of the feature that enables routing referrals to an organization's primary campus connection.
     *
     * @var integer; @JMS\Type("integer")
     */
    private $primaryCampusConnectionReferralRoutingFeatureId;
    
    /**
     *
     *
     * @param int $primaryCampusConnectionReferralRoutingFeatureId
     */
    public function setPrimaryCampusConnectionReferralRoutingFeatureId($primaryCampusConnectionReferralRoutingFeatureId)
    {
    	$this->primaryCampusConnectionReferralRoutingFeatureId = $primaryCampusConnectionReferralRoutingFeatureId;
    }
    
    /**
     *
     *
     * @return int
     */
    public function getPrimaryCampusConnectionReferralRoutingFeatureId()
    {
    	return $this->primaryCampusConnectionReferralRoutingFeatureId;
    }
    
    /**
     *
     *
     * @param int $primaryCampusConnectionReferralRoutingOrgId
     */
    public function setPrimaryCampusConnectionReferralRoutingOrgId($primaryCampusConnectionReferralRoutingOrgId)
    {
    	$this->primaryCampusConnectionReferralRoutingOrgId = $primaryCampusConnectionReferralRoutingOrgId;
    }
    
    /**
     *
     *
     * @return int
     */
    public function getPrimaryCampusConnectionReferralRoutingOrgId()
    {
    	return $this->primaryCampusConnectionReferralRoutingOrgId;
    }
    
    /**
     *
     *
     * @param boolean $isPrimaryCampusConnectionReferralRoutingEnabled
     */
    public function setIsPrimaryCampusConnectionReferralRoutingEnabled($isPrimaryCampusConnectionReferralRoutingEnabled)
    {
    	$this->isPrimaryCampusConnectionReferralRoutingEnabled = $isPrimaryCampusConnectionReferralRoutingEnabled;
    }
    
    /**
     *
     *
     * @return boolean
     */
    public function getIsPrimaryCampusConnectionReferralRoutingEnabled()
    {
    	return $this->isPrimaryCampusConnectionReferralRoutingEnabled;
    }

    /**
     *
     *
     * @param boolean $isReasonRoutingEnabled            
     */
    public function setIsReasonRoutingEnabled($isReasonRoutingEnabled)
    {
        $this->isReasonRoutingEnabled = $isReasonRoutingEnabled;
    }

    /**
     *
     *
     * @return boolean
     */
    public function getIsReasonRoutingEnabled()
    {
        return $this->isReasonRoutingEnabled;
    }

    /**
     *
     *
     * @param boolean $isStudentReferralNotificationEnabled            
     */
    public function setIsStudentReferralNotificationEnabled($isStudentReferralNotificationEnabled)
    {
        $this->isStudentReferralNotificationEnabled = $isStudentReferralNotificationEnabled;
    }

    /**
     *
     *
     * @return int
     */
    public function getIsStudentReferralNotificationEnabled()
    {
        return $this->isStudentReferralNotificationEnabled;
    }

    /**
     *
     *
     * @param int $bookingOrgId            
     */
    public function setBookingOrgId($bookingOrgId)
    {
        $this->bookingOrgId = $bookingOrgId;
    }

    /**
     *
     *
     * @return int
     */
    public function getBookingOrgId()
    {
        return $this->bookingOrgId;
    }

    /**
     *
     *
     * @param int $logContactsOrgId            
     */
    public function setLogContactsOrgId($logContactsOrgId)
    {
        $this->logContactsOrgId = $logContactsOrgId;
    }

    /**
     *
     *
     * @return int
     */
    public function getLogContactsOrgId()
    {
        return $this->logContactsOrgId;
    }

    /**
     *
     *
     * @param int $reasonRoutingFeatureId            
     */
    public function setReasonRoutingFeatureId($reasonRoutingFeatureId)
    {
        $this->reasonRoutingFeatureId = $reasonRoutingFeatureId;
    }

    /**
     *
     *
     * @return int
     */
    public function getReasonRoutingFeatureId()
    {
        return $this->reasonRoutingFeatureId;
    }

    /**
     *
     *
     * @param int $reasonRoutingOrgId            
     */
    public function setReasonRoutingOrgId($reasonRoutingOrgId)
    {
        $this->reasonRoutingOrgId = $reasonRoutingOrgId;
    }

    /**
     *
     *
     * @return int
     */
    public function getReasonRoutingOrgId()
    {
        return $this->reasonRoutingOrgId;
    }

    /**
     *
     *
     * @param int $studentReferralNotificationFeatureId            
     */
    public function setStudentReferralNotificationFeatureId($studentReferralNotificationFeatureId)
    {
        $this->studentReferralNotificationFeatureId = $studentReferralNotificationFeatureId;
    }

    /**
     *
     *
     * @return int
     */
    public function getStudentReferralNotificationFeatureId()
    {
        return $this->studentReferralNotificationFeatureId;
    }

    /**
     *
     *
     * @param int $studentReferralNotificationOrgId            
     */
    public function setStudentReferralNotificationOrgId($studentReferralNotificationOrgId)
    {
        $this->studentReferralNotificationOrgId = $studentReferralNotificationOrgId;
    }

    /**
     *
     *
     * @return int
     */
    public function getStudentReferralNotificationOrgId()
    {
        return $this->studentReferralNotificationOrgId;
    }

    /**
     *
     *
     * @param int $studentNotificationReferralOrgId            
     */
    public function setStudentNotificationReferralOrgId($studentNotificationReferralOrgId)
    {
        $this->studentNotificationReferralOrgId = $studentNotificationReferralOrgId;
    }

    /**
     *
     *
     * @param boolean $isBookingEnabled            
     */
    public function setIsBookingEnabled($isBookingEnabled)
    {
        $this->isBookingEnabled = $isBookingEnabled;
    }

    /**
     *
     *
     * @return boolean
     */
    public function getIsBookingEnabled()
    {
        return $this->isBookingEnabled;
    }

    /**
     * Sets the organization id.
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the organization id.
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets whether an organization has logging contacts enabled or not.
     *
     * @param boolean $isLogContactsEnabled            
     */
    public function setIsLogContactsEnabled($isLogContactsEnabled)
    {
        $this->isLogContactsEnabled = $isLogContactsEnabled;
    }

    /**
     * Returns whether an organization has logging contacts enabled or not.
     *
     * @return boolean
     */
    public function getIsLogContactsEnabled()
    {
        return $this->isLogContactsEnabled;
    }

    /**
     * Sets whether an organization has notes enabled or not.
     *
     * @param boolean $isNotesEnabled            
     */
    public function setIsNotesEnabled($isNotesEnabled)
    {
        $this->isNotesEnabled = $isNotesEnabled;
    }

    /**
     * Returns whether an organization has notes enabled or not.
     *
     * @return int
     */
    public function getIsNotesEnabled()
    {
        return $this->isNotesEnabled;
    }

    /**
     * Sets whether an organization has referrals enabled or not.
     *
     * @param boolean $isReferralEnabled            
     */
    public function setIsReferralEnabled($isReferralEnabled)
    {
        $this->isReferralEnabled = $isReferralEnabled;
    }

    /**
     * Returns whether an organization has referrals enabled or not.
     *
     * @return boolean
     */
    public function getIsReferralEnabled()
    {
        return $this->isReferralEnabled;
    }

    /**
     *
     *
     * @param int $referralFeatureId            
     */
    public function setReferralFeatureId($referralFeatureId)
    {
        $this->referralFeatureId = $referralFeatureId;
    }

    /**
     *
     *
     * @return int
     */
    public function getReferralFeatureId()
    {
        return $this->referralFeatureId;
    }

    /**
     *
     *
     * @param int $referralOrgId            
     */
    public function setReferralOrgId($referralOrgId)
    {
        $this->referralOrgId = $referralOrgId;
    }

    /**
     *
     *
     * @return int
     */
    public function getReferralOrgId()
    {
        return $this->referralOrgId;
    }

    /**
     *
     *
     * @param int $notesOrgId            
     */
    public function setNotesOrgId($notesOrgId)
    {
        $this->notesOrgId = $notesOrgId;
    }

    /**
     *
     *
     * @return int
     */
    public function getNotesOrgId()
    {
        return $this->notesOrgId;
    }
    
    /**
     * Sets the id of the booking feature an organization is using.
     *
     * @param int $bookingFeatureId            
     */
    public function setBookingFeatureId($bookingFeatureId)
    {
        $this->bookingFeatureId = $bookingFeatureId;
    }

    /**
     * Returns the id of the booking feature an organization is using.
     *
     * @return int
     */
    public function getBookingFeatureId()
    {
        return $this->bookingFeatureId;
    }

    /**
     * Sets the id of the contact logging feature an organization is using.
     *
     * @param int $logContactsFeatureId            
     */
    public function setLogContactsFeatureId($logContactsFeatureId)
    {
        $this->logContactsFeatureId = $logContactsFeatureId;
    }

    /**
     * Returns the id of the contact logging feature an organization is using.
     *
     * @return int
     */
    public function getLogContactsFeatureId()
    {
        return $this->logContactsFeatureId;
    }

    /**
     * Sets the id of the notes feature an organization is using.
     *
     * @param int $notesFeatureId            
     */
    public function setNotesFeatureId($notesFeatureId)
    {
        $this->notesFeatureId = $notesFeatureId;
    }

    /**
     * Returns the id of the notes feature an organization is using.
     *
     * @return int
     */
    public function getNotesFeatureId()
    {
        return $this->notesFeatureId;
    }

    /**
     * Sets the language id for an organization. (ALWAYS[1]-ENGLISH)
     *
     * @param integer $langId
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     * Returns the language id for an organization. (ALWAYS[1]-ENGLISH)
     *
     * @return integer
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     *
     *
     * @param mixed $reasonRoutingList            
     */
    public function setReasonRoutingList($reasonRoutingList)
    {
        $this->reasonRoutingList = $reasonRoutingList;
    }

    /**
     *
     *
     * @return mixed
     */
    public function getReasonRoutingList()
    {
        return $this->reasonRoutingList;
    }

    /**
     *
     *
     * @param boolean $isPrimaryCoordinator            
     */
    public function setIsPrimaryCoordinator($isPrimaryCoordinator)
    {
        $this->isPrimaryCoordinator = $isPrimaryCoordinator;
    }

    /**
     *
     *
     * @return boolean
     */
    public function getIsPrimaryCoordinator()
    {
        return $this->isPrimaryCoordinator;
    }
}