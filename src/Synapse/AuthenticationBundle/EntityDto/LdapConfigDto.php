<?php
namespace Synapse\AuthenticationBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for LDAP authentication settings
 * *
 *
 * @package Synapse\AuthenticationBundle\EntityDto
 *
 */
class LdapConfigDto
{

    /**
     * type
     *
     * @var string @JMS\Type("string")
     *
     * @Assert\Choice(choices = {"LDAP", "AD"}, message = "Choose a server type.")
     *
     */
    private $type;

    /**
     * staff hostname
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "staff hostname cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $staffHostname;

    /**
     * student hostname
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "student hostname cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $studentHostname;

    /**
     * initial_user
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "initial user cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $staffInitialUser;

    /**
     * initial_password
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "initial password cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $staffInitialPassword;

    /**
     * user_base_domain
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "user base domain cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $staffUserBaseDomain;

    /**
     * username_attribute
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "username attribute cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $staffUsernameAttribute;

    /**
     * initial_user
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "initial user cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $studentInitialUser;

    /**
     * initial_password
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "initial password cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $studentInitialPassword;

    /**
     * user_base_domain
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "user base domain cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $studentUserBaseDomain;

    /**
     * username_attribute
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(max = 255,
     *      maxMessage = "username attribute cannot be longer than {{ limit }} characters long"
     * )
     *
     */
    private $studentUsernameAttribute;

    /**
     * Sets the type.
     *
     * @param string @JMS\Type("string") $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the type.
     *
     * @return string @JMS\Type("string")
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the initial_user
     *
     * @param string @JMS\Type("string") $staffInitialUser the staff initial user
     *
     * @return self
     */
    public function setStaffInitialUser($staffInitialUser)
    {
        $this->staffInitialUser = $staffInitialUser;

        return $this;
    }

    /**
     * Gets the initial_user
     *
     * @return string @JMS\Type("string")
     */
    public function getStaffInitialUser()
    {
        return $this->staffInitialUser;
    }

    /**
     * Sets the initial_password
     *
     * @param string @JMS\Type("string") $staffInitialPassword the staff initial password
     *
     * @return self
     */
    public function setStaffInitialPassword($staffInitialPassword)
    {
        $this->staffInitialPassword = $staffInitialPassword;

        return $this;
    }

    /**
     * Gets the initial_password
     *
     * @return string @JMS\Type("string")
     */
    public function getStaffInitialPassword()
    {
        return $this->staffInitialPassword;
    }

    /**
     * Sets the user_base_domain
     *
     * @param string @JMS\Type("string") $staffUserBaseDomain the staff user base domain
     *
     * @return self
     */
    public function setStaffUserBaseDomain($staffUserBaseDomain)
    {
        $this->staffUserBaseDomain = $staffUserBaseDomain;

        return $this;
    }

    /**
     * Gets the user_base_domain
     *
     * @return string @JMS\Type("string")
     */
    public function getStaffUserBaseDomain()
    {
        return $this->staffUserBaseDomain;
    }

    /**
     * Sets the username_attribute
     *
     * @param string @JMS\Type("string") $staffUsernameAttribute the staff username attribute
     *
     * @return self
     */
    public function setStaffUsernameAttribute($staffUsernameAttribute)
    {
        $this->staffUsernameAttribute = $staffUsernameAttribute;

        return $this;
    }

    /**
     * Gets the username_attribute
     *
     * @return string @JMS\Type("string")
     */
    public function getStaffUsernameAttribute()
    {
        return $this->staffUsernameAttribute;
    }

    /**
     * Sets the initial_user
     *
     * @param string @JMS\Type("string") $studentInitialUser the student initial user
     *
     * @return self
     */
    public function setStudentInitialUser($studentInitialUser)
    {
        $this->studentInitialUser = $studentInitialUser;

        return $this;
    }

    /**
     * Gets the initial_user
     *
     * @return string @JMS\Type("string")
     */
    public function getStudentInitialUser()
    {
        return $this->studentInitialUser;
    }

    /**
     * Sets the initial_password
     *
     * @param string @JMS\Type("string") $studentInitialPassword the student initial password
     *
     * @return self
     */
    public function setStudentInitialPassword($studentInitialPassword)
    {
        $this->studentInitialPassword = $studentInitialPassword;

        return $this;
    }

    /**
     * Gets the initial_password
     *
     * @return string @JMS\Type("string")
     */
    public function getStudentInitialPassword()
    {
        return $this->studentInitialPassword;
    }

    /**
     * Sets the user_base_domain
     *
     * @param string @JMS\Type("string") $studentUserBaseDomain the student user base domain
     *
     * @return self
     */
    public function setStudentUserBaseDomain($studentUserBaseDomain)
    {
        $this->studentUserBaseDomain = $studentUserBaseDomain;

        return $this;
    }

    /**
     * Gets the user_base_domain
     *
     * @return string @JMS\Type("string")
     */
    public function getStudentUserBaseDomain()
    {
        return $this->studentUserBaseDomain;
    }

    /**
     * Sets the username_attribute
     *
     * @param string @JMS\Type("string") $studentUsernameAttribute the student username attribute
     *
     * @return self
     */
    public function setStudentUsernameAttribute($studentUsernameAttribute)
    {
        $this->studentUsernameAttribute = $studentUsernameAttribute;

        return $this;
    }

    /**
     * Gets the username_attribute
     *
     * @return string @JMS\Type("string")
     */
    public function getStudentUsernameAttribute()
    {
        return $this->studentUsernameAttribute;
    }

    /**
     * Sets the staff hostname
     *
     * @param string @JMS\Type("string") $staffHostname the staff hostname
     *
     * @return self
     */
    public function setStaffHostname($staffHostname)
    {
        $this->staffHostname = $staffHostname;

        return $this;
    }

    /**
     * Gets the staff hostname
     *
     * @return string @JMS\Type("string")
     */
    public function getStaffHostname()
    {
        return $this->staffHostname;
    }

    /**
     * Sets the student hostname
     *
     * @param string @JMS\Type("string") $studentHostname the student hostname
     *
     * @return self
     */
    public function setStudentHostname($studentHostname)
    {
        $this->studentHostname = $studentHostname;

        return $this;
    }

    /**
     * Gets the student hostname
     *
     * @return string @JMS\Type("string")
     */
    public function getStudentHostname()
    {
        return $this->studentHostname;
    }
}