<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\RestBundle\Entity
 */
class UsersDto
{

    /**
     * userId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $userId;

    /**
     * firstName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $firstName;

    /**
     * lastName
     *
     * @var string @JMS\Type("string")
     */
    private $lastName;

    /**
     * title
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $title;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $email;

    /**
     * contactNumber
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $contactNumber;

    /**
     *
     * @param integer $userId            
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @param string $firstName            
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     *
     * @param string $lastName            
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
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
     * @param string $contactNumber            
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;
    }

    /**
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }
}