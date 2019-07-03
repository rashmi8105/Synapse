<?php
namespace Synapse\CampusResourceBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\CampusResourceBundle\EntityDto
 *         
 */
class CampusResourceDto
{

    /**
     * Id of a specific campus resource
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * Id of the organization that a campus resource belongs to
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * Name of a specific campus resource
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Length(min = 1,
     *      max = 100,
     *      minMessage = "Resource Name must be at least {{ limit }} characters long",
     *      maxMessage = "Resource Name cannot be longer than {{ limit }} characters long"
     *      )
     *
     */
    private $resourceName;

    /**
     * Id of the staff member that is a campus resource
     *
     * @var string @JMS\Type("integer")
     *      @Assert\NotBlank()
     *     
     */
    private $staffId;

    /**
     * Name of the campus resource staff member
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $staffName;

    /**
     * Phone number of a specific campus resource
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank()
     *     
     */
    private $resourcePhoneNumber;

    /**
     * Email of a specific campus resource
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Length(
     *      max = 120,
     *      maxMessage = "Resource Email cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $resourceEmail;

    /**
     * Location of a specific campus resource
     *
     * @var string @JMS\Type("string")
     *      @Assert\Length(
     *      max = 100,
     *      maxMessage = "Resource Location cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $resourceLocation;

    /**
     * Url to reach or contact a certain campus resource
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $resourceUrl;

    /**
     * Description of a specific campus resource
     *
     * @var string @JMS\Type("string")
     *      @Assert\Length(min = 0,
     *      max = 300,
     *      minMessage = "Resource Description must be at least {{ limit }} characters long",
     *      maxMessage = "Resource Description cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $resourceDescription;

    /**
     * Boolean to determine whether a specific campus resource can receive referrals or not
     *
     * @var string @JMS\Type("boolean")
     * @Assert\Type(type="boolean")
     *     
     */
    private $receiveReferals;

    /**
     * Boolean to determine whether students can view a specific campus resource or not
     *
     * @var string @JMS\Type("boolean")
     * @Assert\Type(type="boolean")
     */
    private $visibleToStudents;

    /**
     * Sets the id of a specific campus resource
     *
     * @param mixed $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the id of a specific campus resource
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id of the organization that a specific campus resource is within
     *
     * @param mixed $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the id of the organization that a specific campus resource is within
     *
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the name of a specific campus resource
     *
     * @param string $resourceName            
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;
    }

    /**
     * Returns the name of a specific campus resource
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Sets the id of a specific staff member that is a campus resource
     *
     * @param mixed $staffId            
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;
    }

    /**
     * Returns the id of a specific staff member that is a campus resource
     *
     * @return mixed
     */
    public function getStaffId()
    {
        return $this->staffId;
    }

    /**
     * Sets the name of a specific staff member that is a campus resource
     *
     * @param string $staffName            
     */
    public function setStaffName($staffName)
    {
        $this->staffName = $staffName;
    }

    /**
     * Returns the name of a specific staff member that is a campus resource
     *
     * @return string
     */
    public function getStaffName()
    {
        return $this->staffName;
    }

    /**
     * Sets the phone number of a specific campus resource
     *
     * @param string $resourcePhoneNumber            
     */
    public function setResourcePhoneNumber($resourcePhoneNumber)
    {
        $this->resourcePhoneNumber = $resourcePhoneNumber;
    }

    /**
     * Returns the phone number of a specific campus resource
     *
     * @return string
     */
    public function getResourcePhoneNumber()
    {
        return $this->resourcePhoneNumber;
    }

    /**
     * Sets the email of a specific campus resource
     *
     * @param string $resourceEmail            
     */
    public function setResourceEmail($resourceEmail)
    {
        $this->resourceEmail = $resourceEmail;
    }

    /**
     * Returns the email of a specific campus resource
     *
     * @return string
     */
    public function getResourceEmail()
    {
        return $this->resourceEmail;
    }

    /**
     * Sets the location of a specific campus resource
     *
     * @param string $resourceLocation            
     */
    public function setResourceLocation($resourceLocation)
    {
        $this->resourceLocation = $resourceLocation;
    }

    /**
     * Returns the location of a specific campus resource
     *
     * @return string
     */
    public function getResourceLocation()
    {
        return $this->resourceLocation;
    }

    /**
     * Sets the url for a specific campus resource
     *
     * @param string $resourceUrl            
     */
    public function setResourceUrl($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;
    }

    /**
     * Returns the url for a specific campus resource
     *
     * @return string
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    /**
     * Sets the description for a specific campus resource
     *
     * @param string $resourceDescription            
     */
    public function setResourceDescription($resourceDescription)
    {
        $this->resourceDescription = $resourceDescription;
    }

    /**
     * Returns the description for a specific campus resource
     *
     * @return string
     */
    public function getResourceDescription()
    {
        return $this->resourceDescription;
    }

    /**
     * Sets whether or not a campus resource can receive referrals
     *
     * @param mixed $receiveReferals
     */
    public function setReceiveReferals($receiveReferals)
    {
        $this->receiveReferals = $receiveReferals;
    }

    /**
     * Returns whether or not a campus resource can receive referrals
     *
     * @return mixed
     */
    public function getReceiveReferals()
    {
        return $this->receiveReferals;
    }

    /**
     * Sets whether a campus resource is visible to students or not
     *
     * @param mixed $visibleToStudents            
     */
    public function setVisibleToStudents($visibleToStudents)
    {
        $this->visibleToStudents = $visibleToStudents;
    }

    /**
     * Returns whether or not a campus resource is visible to students
     *
     * @return mixed
     */
    public function getVisibleToStudents()
    {
        return $this->visibleToStudents;
    }
}