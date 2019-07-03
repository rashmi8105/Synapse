<?php
namespace Synapse\CampusConnectionBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CampusConnectionsArrayDto
{

    /**
     * personId
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * personFirstname
     *
     * @var string @JMS\Type("string")
     */
    private $personFirstname;

    /**
     * personLastname
     *
     * @var string @JMS\Type("string")
     */
    private $personLastname;

    /**
     * personTitle
     *
     * @var string @JMS\Type("string")
     */
    private $personTitle;

    /**
     * primaryConnection
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $primaryConnection;

    /**
     * phone
     *
     * @var string @JMS\Type("string")
     */
    private $phone;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * groups
     *
     * @var array @JMS\Type("array")
     */
    private $groups;

    /**
     * courses
     *
     * @var array @JMS\Type("array")
     */
    private $courses;
    
    /**
     * isInvisible
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isInvisible;

    /**
     *
     * @param array $courses            
     */
    public function setCourses($courses)
    {
        $this->courses = $courses;
    }

    /**
     *
     * @return array
     */
    public function getCourses()
    {
        return $this->courses;
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
     * @param array $groups            
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     *
     * @param string $personFirstname            
     */
    public function setPersonFirstname($personFirstname)
    {
        $this->personFirstname = $personFirstname;
    }

    /**
     *
     * @return string
     */
    public function getPersonFirstname()
    {
        return $this->personFirstname;
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
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param string $personLastname            
     */
    public function setPersonLastname($personLastname)
    {
        $this->personLastname = $personLastname;
    }

    /**
     *
     * @return string
     */
    public function getPersonLastname()
    {
        return $this->personLastname;
    }

    /**
     *
     * @param string $personTitle            
     */
    public function setPersonTitle($personTitle)
    {
        $this->personTitle = $personTitle;
    }

    /**
     *
     * @return string
     */
    public function getPersonTitle()
    {
        return $this->personTitle;
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
     * @param boolean $primaryConnection            
     */
    public function setPrimaryConnection($primaryConnection)
    {
        $this->primaryConnection = $primaryConnection;
    }

    /**
     *
     * @return boolean
     */
    public function getPrimaryConnection()
    {
        return $this->primaryConnection;
    }
    
    /**
     *
     * @param boolean $isInvisible
     */
    public function setIsInvisible($isInvisible)
    {
        $this->isInvisible = $isInvisible;
    }
    
    /**
     *
     * @return boolean
     */
    public function getIsInvisible()
    {
        return $this->isInvisible;
    }
}