<?php
/**
 * Created by PhpStorm.
 * User: subash
 * Date: 20/7/14
 * Time: 10:57 PM
 */
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class CoordinatorDTO
{

    /**
     * Id of a coordinator.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Coordinator's first name.
     * 
     * @var string @JMS\Type("string")
     */
    private $firstname;

    /**
     * Coordinator's last name.
     * 
     * @var string @JMS\Type("string")
     */
    private $lastname;

    /**
     * Coordinator's title.
     * 
     * @var string @JMS\Type("string")
     */
    private $title;

    /**
     * Id of a coordinator's role.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $roleid;

    /**
     * Coordinator's email address.
     * 
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * Check for if phone number is mobile or not.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $ismobile;

    /**
     * Coordinator's phone number.
     * 
     * @var string @JMS\Type("string")
     */
    private $phone;

    /**
     * Coordinator's organization id.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organizationid;

    /**
     * Returns the coordinator's email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the coordinator's email address.
     *
     * @param string $email            
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the coordinator's first name
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Sets the coordinator's first name
     *
     * @param string $firstname            
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Returns the coordinator's last name
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Sets the coordinator's last name
     *
     * @param string $lastname            
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Returns boolean check for if phone number is mobile or not
     *
     * @return boolean
     */
    public function getIsmobile()
    {
        return $this->ismobile;
    }

    /**
     * Sets boolean check for if phone number is mobile or not
     *
     * @param boolean $ismobile            
     */
    public function setIsmobile($ismobile)
    {
        $this->ismobile = $ismobile;
    }

    /**
     * Returns organization Id
     *
     * @return int
     */
    public function getOrganizationid()
    {
        return $this->organizationid;
    }

    /**
     * Sets organization Id
     *
     * @param int $organizationid            
     */
    public function setOrganizationid($organizationid)
    {
        $this->organizationid = $organizationid;
    }

    /**
     * Returns role Id
     *
     * @return int
     */
    public function getRoleid()
    {
        return $this->roleid;
    }

    /**
     * Sets role Id
     *
     * @param int $roleid            
     */
    public function setRoleid($roleid)
    {
        $this->roleid = $roleid;
    }

    /**
     * Returns coordinator's phone number
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets coordinator's phone number
     *
     * @param string $phone            
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Returns coordinator's title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets coordinator's title
     *
     * @param string $title            
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns coordinator's Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets coordinator's Id
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}