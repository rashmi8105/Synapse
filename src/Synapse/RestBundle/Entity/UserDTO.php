<?php
namespace Synapse\RestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class UserDTO
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Tier id
     *
     * @var integer @JMS\Type("integer")
     */
    private $tierId;

    /**
     * Tier Level
     *
     * @var string @JMS\Type("string")
     */
    private $tierLevel;

    /**
     * Person Firstname
     *
     * @var string @JMS\Type("string")
     */
    private $firstname;

    /**
     * Person Lastname
     *
     * @var string @JMS\Type("string")
     */
    private $lastname;

    /**
     * Title
     *
     * @var string @JMS\Type("string")
     */
    private $title;

    /**
     * User Type Coordinator | Faculty | Student
     *
     * @var string @JMS\Type("string")
     */
    private $userType;

    /**
     * Type
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * Role id
     *
     * @var integer @JMS\Type("integer")
     */
    private $roleid;

    /**
     * Email id
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * External id
     *
     * @var string @JMS\Type("string")
     */
    private $externalid;

    /**
     * Phone number is mobile or not
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $ismobile;

    /**
     * Is active
     *
     * @var string @JMS\Type("string")
     */
    private $isActive;

    /**
     * Phone number
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(min = 0,
     *      max = 32,
     *      minMessage = "Phone Number must be at least {{ limit }} characters long",
     *      maxMessage = "Phone Number cannot be longer than {{ limit }} characters long"
     *      )
     *
     */
    private $phone;

    /**
     * Campus id
     *
     * @var integer @JMS\Type("integer")
     */
    private $campusid;

    /**
     * Faculty Id
     *
     * @var string @JMS\Type("string")
     */
    private $facultyId;

    /**
     * Secondary Tier Name
     *
     * @var string @JMS\Type("string")
     */
    private $secondaryTierName;

    /**
     * Campus Name
     *
     * @var string @JMS\Type("string")
     */
    private $campusName;

    /**
     * @var string @JMS\Type("string")
     */
    private $ldapUsername;

    /**
     * sendinvite
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $sendinvite;

    /**
     * Person googleEmailId
     *
     * @var string @JMS\Type("string")
     */
    private $googleEmailId;

    /**
     * isParticipating
     *
     * @var integer @JMS\Type("integer")
     */
    private $isParticipating;

    /**
     * Client Id for the service account to be used along with client secret and auth code to generate Access Token
     *
     * @var string
     * @JMS\Type("string")
     */
    private $clientId;

    /**
     * Client Secret for the service account used along with client Id and auth code to generate Access Token
     *
     * @var string
     * @JMS\Type("string")
     */
    private $clientSecret;


    /**
     * Authorization Code for the service account used along with Client Id and Client Token to generate Access Token
     *
     * @var string
     * @JMS\Type("string")
     */
    private $authCode;

    /**
     * Date the welcome email was sent to the user.
     *
     * @var string @JMS\Type("string")
     *
     */
    private $welcomeEmailSentDate;

    /**
     * Student's Mapworks participation status.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $participantStatus;

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
     * @param string $externalid
     */
    public function setExternalid($externalid)
    {
        $this->externalid = $externalid;
    }

    /**
     *
     * @return string
     */
    public function getExternalid()
    {
        return $this->externalid;
    }

    /**
     *
     * @param string $firstName
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
     * @param boolean $ismobile
     */
    public function setIsmobile($ismobile)
    {
        $this->ismobile = $ismobile;
    }

    /**
     *
     * @return boolean
     */
    public function getIsmobile()
    {
        return $this->ismobile;
    }

    /**
     *
     * @param string $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @param integer $participating
     */
    public function setParticipating($participating)
    {
        $this->isParticipating = $participating;
    }

    /**
     *
     * @return integer
     */
    public function getParticipating()
    {
        return $this->isParticipating;
    }

    /**
     *
     * @param int $campusid
     */
    public function setCampusid($campusid)
    {
        $this->campusid = $campusid;
    }

    /**
     *
     * @return int
     */
    public function getCampusId()
    {
        return $this->campusid;
    }

    /**
     *
     * @param int $roleid
     */
    public function setRoleid($roleid)
    {
        $this->roleid = $roleid;
    }

    /**
     *
     * @return int
     */
    public function getRoleid()
    {
        return $this->roleid;
    }

    /**
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    /**
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
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
     * @param int $tierId
     */
    public function setTierId($tierId)
    {
        $this->tierId = $tierId;
    }

    /**
     *
     * @return int
     */
    public function getTierId()
    {
        return $this->tierId;
    }

    /**
     *
     * @param string $facultyId
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
    }

    /**
     *
     * @return string
     */
    public function getFacultyId()
    {
        return $this->facultyId;
    }

    /**
     *
     * @param
     *            string TierLevel
     */
    public function setTierLevel($tierLevel)
    {
        $this->tierLevel = $tierLevel;
    }

    /**
     *
     * @return string
     */
    public function getTierLevel()
    {
        return $this->tierLevel;
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
     * @param
     *            string secondary tier Name
     */
    public function setSecondaryTierName($secondaryTierName)
    {
        $this->secondaryTierName = $secondaryTierName;
    }

    /**
     *
     * @return string
     */
    public function getSecondaryTierName()
    {
        return $this->secondaryTierName;
    }

    /**
     *
     * @param
     *            string campusName
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }

    /**
     *
     * @return string
     */
    public function getCampusName()
    {
        return $this->campusName;
    }

    /**
     * @param $ldapUsername
     */
    public function setLdapUsername($ldapUsername)
    {
        $this->ldapUsername = $ldapUsername;
    }

    /**
     * @return string
     */
    public function getLdapUsername()
    {
        return $this->ldapUsername;
    }

    /**
     *
     * @param boolean $sendinvite
     */
    public function setSendinvite($sendinvite)
    {
        $this->sendinvite = $sendinvite;
    }

    /**
     *
     * @return boolean
     */
    public function getSendinvite()
    {
        return $this->sendinvite;
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
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string $authCode
     */
    public function setAuthCode($authCode)
    {

        $this->authCode = $authCode;
    }

    /**
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {

        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getWelcomeEmailSentDate()
    {
        return $this->welcomeEmailSentDate;
    }

    /**
     * @param string $welcomeEmailSentDate
     */
    public function setWelcomeEmailSentDate($welcomeEmailSentDate)
    {
        $this->welcomeEmailSentDate = $welcomeEmailSentDate;
    }

    /**
     * @return boolean
     */
    public function getParticipantStatus()
    {
        return $this->participantStatus;
    }

    /**
     * @param boolean $participantStatus
     */
    public function setParticipantStatus($participantStatus)
    {
        $this->participantStatus = $participantStatus;
    }

} 