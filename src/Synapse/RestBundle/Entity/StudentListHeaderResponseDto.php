<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class StudentListHeaderResponseDto
{

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $personId;

    /**
     * @JMS\Type("integer")
     *
     * @var string
     */
    private $totalActivities;

    /**
     * @JMS\Type("integer")
     *
     * @var string
     */
    private $totalNotes;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $totalContacts;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $totalReferrals;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $totalAppointments;
    
    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $totalEmail;

    /**
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    private $showInteractionContactType;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\StudentListArrayResponseDto>")
     */
    private $activities;

    /**
     *
     * @param int $showInteractionContactType            
     */
    public function setShowInteractionContactType($showInteractionContactType)
    {
        $this->showInteractionContactType = $showInteractionContactType;
    }

    /**
     *
     * @return int
     */
    public function getShowInteractionContactType()
    {
        return $this->showInteractionContactType;
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
     * @param int $totalActivities            
     */
    public function setTotalActivities($totalActivities)
    {
        $this->totalActivities = $totalActivities;
    }

    /**
     *
     * @return int
     */
    public function getTotalActivities()
    {
        return $this->totalActivities;
    }

    /**
     *
     * @param int $totalNotes            
     */
    public function setTotalNotes($totalNotes)
    {
        $this->totalNotes = $totalNotes;
    }

    /**
     *
     * @return int
     */
    public function getTotalNotes()
    {
        return $this->totalNotes;
    }

    /**
     *
     * @param int $totalContacts            
     */
    public function setTotalContacts($totalContacts)
    {
        $this->totalContacts = $totalContacts;
    }

    /**
     *
     * @return int
     */
    public function getTotalContacts()
    {
        return $this->totalContacts;
    }

    /**
     *
     * @param int $totalReferrals            
     */
    public function setTotalReferrals($totalReferrals)
    {
        $this->totalReferrals = $totalReferrals;
    }

    /**
     *
     * @return int
     */
    public function getTotalReferrals()
    {
        return $this->totalReferrals;
    }

    /**
     *
     * @param int $totalAppointments            
     */
    public function setTotalAppointments($totalAppointments)
    {
        $this->totalAppointments = $totalAppointments;
    }

    /**
     *
     * @return int
     */
    public function getTotalAppointments()
    {
        return $this->totalAppointments;
    }
    
    /**
     *
     * @param int $totalEmail
     */
    public function setTotalEmail($totalEmail)
    {
        $this->totalEmail = $totalEmail;
    }
    
    /**
     *
     * @return int
     */
    public function getTotalEmail()
    {
        return $this->totalEmail;
    }

    /**
     *
     * @param Object $activities            
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;
    }

    /**
     *
     * @return Object
     */
    public function getActivities()
    {
        return $this->activities;
    }
}