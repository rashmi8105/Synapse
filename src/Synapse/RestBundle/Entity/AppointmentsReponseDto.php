<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class AppointmentsReponseDto
{

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $personId;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $firstName;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $lastName;
    
    /**
     * personTitle
     *
     * @var string @JMS\Type("string")
     *
     */
    private $personTitle;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $personIdProxy;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $organizationId;

    /**
     * attendees student id
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\CalendarTimeSlotsReponseDto>")
     *     
     *     
     */
    private $calendarTimeSlots;
    
    /**
     *
     * @param array $calendarTimeSlots
     */
    public function setCalendarTimeSlots($calendarTimeSlots)
    {
        $this->calendarTimeSlots = $calendarTimeSlots;
    }

    /**
     *
     * @return Object
     */
    public function getCalendarTimeSlots()
    {
        return $this->calendarTimeSlots;
    }

    /**
     *
     * @param int $personIdProxy            
     */
    public function setPersonIdProxy($personIdProxy)
    {
        $this->personIdProxy = $personIdProxy;
    }

    /**
     *
     * @return int
     */
    public function getPersonIdProxy()
    {
        return $this->personIdProxy;
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
}