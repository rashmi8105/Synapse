<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Response Dto for LoggedInUserDetails
 *
 * @package Synapse\RestBundle\Entity
 */
class LoggedInUserDetailsResponseDto
{

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $id;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $type;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $firstname;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $lastname;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $email;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $mobile;
    
    /**
     * externalId
     *
     * @var string @JMS\Type("string")
     *
     */
    private $externalId;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $organizationId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $organizationName;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $langId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $langCode;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $canActAsProxy;

    /**
     * @JMS\Type("array")
     *
     * @var array
     */
    private $orgFeatures;

    /**
     * @JMS\Type("array")
     *
     * @var array
     */
    private $userFeaturePermissions;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $riskIndicator;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $intentToLeave;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $isMulticampusUser;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $tierLevel;

    /**
     * @JMS\Type("array")
     *
     * @var array
     */
    private $proxy;

    /**
     * @JMS\Type("array")
     *
     * @var array
     */
    private $permissions;
    
    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $courseTabEnable;
        
    /**
     * @JMS\Type("array")
     *
     * @var array
     */
    private $accessLevel;
    
    /**
     * @JMS\Type("array")
     *
     * @var array
     */
    private $coursesAccess;
    
    /**
     * academicUpdateNotification
     *
     * @JMS\Type("boolean")
     */
    private $academicUpdateNotification;
    
    /**
     * referForAcademicAssistance
     *
     * @JMS\Type("boolean")
     */
    private $referForAcademicAssistance;
    
    /**
     * sendToStudent
     *
     * @JMS\Type("boolean")
     */
    private $sendToStudent;
    
    /**
     * isSurveyClose
     *
     * @JMS\Type("boolean")
     */
    private $isSurveyClose;
    
    /**
     * isSurveyAllowed
     *
     * @JMS\Type("boolean")
     */
    private $isSurveyAllowed;
	
	/**
     * privacyPolicyAcceptedDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $privacyPolicyAcceptedDate ;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $googleEmailId;


    /**
     * retentionCompletion -  Used in My accounts api for determining the retention and completion permission for faculty
     *
     * @JMS\Type("boolean")
     *
     * @var bool
     */
    private $retentionCompletion;


    /**
     * @param $retentionCompletion
     */
    public function setRetentionCompletion($retentionCompletion)
    {

        $this->retentionCompletion = $retentionCompletion;
    }

    /**
     * @return bool
     */
    public function getRetentionCompletion()
    {

        return $this->retentionCompletion;
    }

    
    /**
     *
     * @param boolean $intentToLeave            
     */
    public function setIntentToLeave($intentToLeave)
    {
        $this->intentToLeave = $intentToLeave;
    }
	
	/**
     * isPrivacyPolicyAccepted
     *
     * @JMS\Type("boolean")
     */
    private $isPrivacyPolicyAccepted;

    /**
     *
     * @return boolean
     */
    public function getIntentToLeave()
    {
        return $this->intentToLeave;
    }

    /**
     *
     * @param boolean $riskIndicator            
     */
    public function setRiskIndicator($riskIndicator)
    {
        $this->riskIndicator = $riskIndicator;
    }

    /**
     *
     * @return boolean
     */
    public function getRiskIndicator()
    {
        return $this->riskIndicator;
    }

    /**
     *
     * @param boolean $canActAsProxy            
     */
    public function setCanActAsProxy($canActAsProxy)
    {
        $this->canActAsProxy = $canActAsProxy;
    }

    /**
     *
     * @return boolean
     */
    public function getCanActAsProxy()
    {
        return $this->canActAsProxy;
    }

    /**
     *
     * @param string $email            
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @param string $firstname            
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     *
     * @param string $langCode            
     */
    public function setLangCode($langCode)
    {
        $this->langCode = $langCode;
    }

    /**
     *
     * @return string
     */
    public function getLangCode()
    {
        return $this->langCode;
    }

    /**
     *
     * @param int $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     *
     * @param string $lastname            
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     *
     * @param int $mobile            
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     *
     * @return int
     */
    public function getMobile()
    {
        return $this->mobile;
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
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param string $organizationName            
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;
    }

    /**
     *
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     *
     * @param string $type            
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

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
     *
     * @param array $orgFeatures            
     */
    public function setOrgFeatures($orgFeatures)
    {
        $this->orgFeatures = $orgFeatures;
    }

    /**
     *
     * @return array
     */
    public function getOrgFeatures()
    {
        return $this->orgFeatures;
    }

    /**
     *
     * @param array $userFeaturePermissions            
     */
    public function setUserFeaturePermissions($userFeaturePermissions)
    {
        $this->userFeaturePermissions = $userFeaturePermissions;
    }

    /**
     *
     * @return array
     */
    public function getUserFeaturePermissions()
    {
        return $this->userFeaturePermissions;
    }

    /**
     *
     * @param boolean $isMulticampusUser            
     */
    public function setIsMulticampusUser($isMulticampusUser)
    {
        $this->isMulticampusUser = $isMulticampusUser;
    }

    /**
     *
     * @return boolean
     */
    public function getIsMulticampusUser()
    {
        return $this->isMulticampusUser;
    }

    /**
     *
     * @param string $tierLevel            
     */
    public function setTierLevel($tierLevel)
    {
        $this->tierLevel = $tierLevel;
    }

    /**
     *
     * @return boolean
     */
    public function getTierLevel()
    {
        return $this->tierLevel;
    }

    /**
     *
     * @param array $proxy            
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     *
     * @return array
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     *
     * @param array $permissions            
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
    
    /**
     *
     * @param boolean $courseTabEnable
     */
    public function setCourseTabEnable($courseTabEnable)
    {
        $this->courseTabEnable = $courseTabEnable;
    }
    
    /**
     *
     * @return boolean
     */
    public function getCourseTabEnable()
    {
        return $this->courseTabEnable;
    }
    
    /**
     *
     * @param array $accessLevel
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }
    
    /**
     *
     * @return array
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }
    
    /**
     *
     * @param array $coursesAccess
     */
    public function setCoursesAccess($coursesAccess)
    {
        $this->coursesAccess = $coursesAccess;
    }
    
    /**
     *
     * @return array
     */
    public function getCoursesAccess()
    {
        return $this->coursesAccess;
    }
    
    /**
     *
     * @return string $externalId
     */
    public function setExternalId($externalId)
    {
    	$this->externalId = $externalId;
    }
    
    /**
     *
     * @return boolean $academicUpdateNotification
     */
    public function setAcademicUpdateNotification($academicUpdateNotification)
    {
    	$this->academicUpdateNotification = $academicUpdateNotification;
    }
    
    /**
     *
     * @return boolean $referForAcademicAssistance
     */
    public function setReferForAcademicAssistance($referForAcademicAssistance)
    {
    	$this->referForAcademicAssistance = $referForAcademicAssistance;
    }
    
    /**
     *
     * @return boolean $sendToStudent
     */
    public function setSendToStudent($sendToStudent)
    {
    	$this->sendToStudent = $sendToStudent;
    }
    
    /**
     *
     * @return boolean $isSurveyClose
     */
    public function setIsSurveyClose($isSurveyClose)
    {
    	$this->isSurveyClose = $isSurveyClose;
    }
    
    /**
     *
     * @return boolean $isSurveyAllowed
     */
    public function setIsSurveyAllowed($isSurveyAllowed)
    {
        $this->isSurveyAllowed = $isSurveyAllowed;
    }
	
	/**
     *
     * @return boolean $isPrivacyPolicyAccepted
     */
    public function setIsPrivacyPolicyAccepted($isPrivacyPolicyAccepted)
    {
    	$this->isPrivacyPolicyAccepted = $isPrivacyPolicyAccepted;
    }   
    
	/**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $date            
     */
    public function setPrivacyPolicyAcceptedDate($privacyPolicyAcceptedDate)
    {
        $this->privacyPolicyAcceptedDate = $privacyPolicyAcceptedDate;
    }

    /**
     *
     * @return mixed
     */
    public function getPrivacyPolicyAcceptedDate()
    {
        return $this->privacyPolicyAcceptedDate;
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
}