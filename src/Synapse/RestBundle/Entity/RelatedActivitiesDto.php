<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for RelatedActivities
 *
 * @package Synapse\RestBundle\Entity
 */
class RelatedActivitiesDto
{

    /**
     * id
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * organization
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organization;

    /**
     * activityDate
     * 
     * @var integer @JMS\Type("datetime")
     *     
     */
    private $createdOn;

    /**
     * activityLog
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $activityLog;

    /**
     * note
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $note;

    /**
     * contacts
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $contacts;
    
    /**
     * email
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $email;


    /**
     * appointment
     *
     * @var integer @JMS\Type("integer")
     */
    private $appointment;
    
    /**
     * referral
     *
     * @var integer @JMS\Type("integer")
     */
    private $referral;    


    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param int $organization            
     */
    public function setOrganization($organization = null)
    {
        $this->organization = $organization;
    }

    /**
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     *
     * @param int $activityLog            
     */
    public function setActivityLog($activityLog = null)
    {
        $this->activityLog = $activityLog;
    }

    /**
     *
     * @return int
     */
    public function getActivityLog()
    {
        return $this->activityLog;
    }

    /**
     *
     * @param mixed $createdOn            
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
        
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @param integer $note            
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     *
     * @return integer
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     *
     * @param int $contacts            
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     *
     * @return int
     */
    public function getContacts()
    {
        return $this->contacts;
    }
    
    /**
     *
     * @param int $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    /**
     *
     * @return int
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * @param int $appointment
     */
    public function setAppointment($appointment)
    {
        $this->appointment = $appointment;
    }
    /**
     * @return integer
     */
    public function getAppointment()
    {
        return $this->appointment;
    }
    
    
    /**
     * @param int $referral 
     */
    public function setReferral($referral)
    {
        $this->referral = $referral;
    }
    /**
     * @return integer
     */
    public function getReferrals()
    {
        return $this->referral;
    }
    

}