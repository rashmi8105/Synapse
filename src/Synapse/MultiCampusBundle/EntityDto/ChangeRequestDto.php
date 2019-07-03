<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use Faker\Provider\cs_CZ\DateTime;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Tier
 *
 * @package Synapse\MultiCampusBundle\EntityDto
 */
class ChangeRequestDto
{

    /**
     * Data transfers starting campus location
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     *     
     */
    private $sourceCampus;

    /**
     * Data transfer campus destination
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     *     
     */
    private $destinationCampus;

    /**
     * Requested By
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     *     
     */
    private $requestedBy;

    /**
     * Requested For
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     *     
     */
    private $requestedFor;

    /**
     * Request Date
     *
     * @var datetime @JMS\Type("DateTime<'Y-m-d H:i:s'>")     
     *     
     */
    private $requestDate;
    
    /**
     * First Name
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $firstName;

    /**
     * Last Name
     *
     * @var string @JMS\Type("string")
     */
    private $lastName;
    
    
    /**
     * Role
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $role;
    
    /**
     * Campus
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $campus;

    /**
     * Returns source campus
     *
     * @return int
     */
    public function getSourceCampus()
    {
        return $this->sourceCampus;
    }

    /**
     * Sets source campus
     *
     * @param int $sourceCampus            
     */
    public function setSourceCampus($sourceCampus)
    {
        $this->sourceCampus = $sourceCampus;
    }

    /**
     * Return destination campus
     *
     * @return int
     */
    public function getDestinationCampus()
    {
        return $this->destinationCampus;
    }

    /**
     * Sets destination campus
     *
     * @param int $destinationCampus            
     */
    public function setDestinationCampus($destinationCampus)
    {
        $this->destinationCampus = $destinationCampus;
    }

    /**
     * Return requested by
     *
     * @return int
     */
    public function getRequestedBy()
    {
        return $this->requestedBy;
    }

    /**
     * Sets requested by
     *
     * @param int $requestedBy            
     */
    public function setRequestedBy($requestedBy)
    {
        $this->requestedBy = $requestedBy;
    }

    /**
     * Return requested for
     *
     * @return int
     */
    public function getRequestedFor()
    {
        return $this->requestedFor;
    }

    /**
     * Set requested for
     *
     * @param int $requestedFor            
     */
    public function setRequestedFor($requestedFor)
    {
        $this->requestedFor = $requestedFor;
    }

    /**
     * Return requested date
     *
     * @return DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Sets requested date
     *
     * @param DateTime $requestDate
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;
    }

    /**
     * Return first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets first name
     *
     * @param string $firstName            
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Return last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets last name
     *
     * @param string $firstName            
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Returns role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Sets role
     *
     * @param string $role            
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Returns campus name
     *
     * @return string
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * Sets campus name
     *
     * @param string $campus            
     */
    public function setCampus($campus)
    {
        $this->campus = $campus;
    }
}