<?php
namespace Synapse\PersonBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Person DTO
 */
class PersonDTO
{

    // array of fields that are allowed to be cleared
    private $fieldsAllowedToBeCleared = [

        'title',
        'auth_username',
        'photo_link',
        'primary_campus_connection_id'
    ];

    /**
     * Id of the person at the organization - Required field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $externalId;

    /**
     * Id of the person within Mapworks - This field is used for output only
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $mapworksInternalId;

    /**
     * First name of the person - Required field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $firstname;

    /**
     * Last name of the person - Required field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $lastname;

    /**
     * Primary email of the person - Required field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $primaryEmail;

    /**
     * Title of the person - Optional field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $title;

    /**
     * Authentication username of the person - Optional field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $authUsername;

    /**
     * Indicates whether the user is a faculty - Optional field.
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $isFaculty;

    /**
     * Indicates whether the user is a student - Optional field.
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $isStudent;

    /**
     * Id of the person at the organization that is the primary campus connection for the student - Optional field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $primaryCampusConnectionId;

    /**
     * Risk group id of the person within Mapworks - Optional field.
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $riskGroupId;

    /**
     * Photo URL of the person - Optional field.
     *
     * @var string
     * @JMS\Type("string")
     */
    private $photoLink;

    /**
     * List of person fields to clear
     *
     * @var array
     * @JMS\Type("array")
     */
    private $fieldsToClear;

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return integer
     */
    public function getMapworksInternalId()
    {
        return $this->mapworksInternalId;
    }

    /**
     * @param integer $mapworksInternalId
     */
    public function setMapworksInternalId($mapworksInternalId)
    {
        $this->mapworksInternalId = $mapworksInternalId;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     * @param string $primaryEmail
     */
    public function setPrimaryEmail($primaryEmail)
    {
        $this->primaryEmail = $primaryEmail;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAuthUsername()
    {
        return $this->authUsername;
    }

    /**
     * @param string $authUsername
     */
    public function setAuthUsername($authUsername)
    {
        $this->authUsername = $authUsername;
    }

    /**
     * @return boolean
     */
    public function getIsFaculty()
    {
        return $this->isFaculty;
    }

    /**
     * @param boolean $isFaculty
     */
    public function setIsFaculty($isFaculty)
    {
        $this->isFaculty = $isFaculty;
    }

    /**
     * @return boolean
     */
    public function getIsStudent()
    {
        return $this->isStudent;
    }

    /**
     * @param boolean $isStudent
     */
    public function setIsStudent($isStudent)
    {
        $this->isStudent = $isStudent;
    }

    /**
     * @return string
     */
    public function getPrimaryCampusConnectionId()
    {
        return $this->primaryCampusConnectionId;
    }

    /**
     * @param string $primaryCampusConnectionId
     */
    public function setPrimaryCampusConnectionId($primaryCampusConnectionId)
    {
        $this->primaryCampusConnectionId = $primaryCampusConnectionId;
    }

    /**
     * @return integer
     */
    public function getRiskGroupId()
    {
        return $this->riskGroupId;
    }

    /**
     * @param integer $riskGroupId
     */
    public function setRiskGroupId($riskGroupId)
    {
        $this->riskGroupId = $riskGroupId;
    }

    /**
     * @return string
     */
    public function getPhotoLink()
    {
        return $this->photoLink;
    }

    /**
     * @param string $photoLink
     */
    public function setPhotoLink($photoLink)
    {
        $this->photoLink = $photoLink;
    }

    /**
     * @return array
     */
    public function getFieldsToClear()
    {
        $validFieldsToBeCleared = array_intersect($this->fieldsToClear, $this->fieldsAllowedToBeCleared);
        return $validFieldsToBeCleared;
    }

    /**
     * @param array $fieldsToClear
     */
    public function setFieldsToClear($fieldsToClear)
    {
        $this->fieldsToClear = $fieldsToClear;
    }

}