<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class MyAccountDto
{

    /**
     * Id of a person, i.e. faculty, student.
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     */
    private $personId;

    /**
     * Mobile phone number of the person.
     *
     * @var string @JMS\Type("string")
     */
    private $personMobile;

    /**
     * Boolean for whether a person's mobile phone number is being changed in an updateAccount action or not.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isMobileChanged;

    /**
     * Password for a person's account.
     *
     * @var string @JMS\Type("string")
     * @Assert\Regex("/^(?=(?:.*[!@#$%^&*()\-_=+{};:,<.>]))(?=.*[0-9]).{6,}$/")
     */
    private $password;

    /**
     * Sets the password for a person's account.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the password for a person's account.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the mobile phone number for a person.
     *
     * @param string $personMobile
     */
    public function setPersonMobile($personMobile)
    {
        $this->personMobile = $personMobile;
    }

    /**
     * Returns the mobile phone number for a person.
     *
     * @return string
     */
    public function getPersonMobile()
    {
        return $this->personMobile;
    }

    /**
     * Sets the id for a person.
     *
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * Returns the id for a person.
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Sets whether a person is changing their mobile phone number.
     *
     * @param boolean $isMobileChanged
     */
    public function setIsMobileChanged($isMobileChanged)
    {
        $this->isMobileChanged = $isMobileChanged;
    }

    /**
     * Returns whether a person is changing their mobile phone number.
     *
     * @return boolean
     */
    public function getIsMobileChanged()
    {
        return $this->isMobileChanged;
    }
}