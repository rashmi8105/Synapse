<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for ActivityLog
 *
 * @package Synapse\RestBundle\Entity
 */
class ActivityLogDto
{

    /**
     * Unique id of an activity.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * Id of the organization that an activity applies to.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organization;

    /**
     * Id of the faculty that is creating an activity with the student.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personIdFaculty;

    /**
     * Id of the student that the activity log is for.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personIdStudent;

    /**
     * Type of activity that occurred, i.e. Contact, Referral, Appointment, etc.
     * 
     * @var string @JMS\Type("string")
     */
    private $activityType;

    /**
     * Date that an activity occurred.
     * 
     * @var integer @JMS\Type("DateTime")
     */
    private $activityDate;

    /**
     * Number of times a student has been referred to a faculty.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $referrals;

    /**
     * Number of appointments a student has had.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $appointments;

    /**
     * Reason for an activity.
     * 
     * @var string @JMS\Type("string")
     */
    private $reason;

    /**
     * Extra details about an activity.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $note;

    /**
     * Number of contacts a student has.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $contacts;       

    /**
     * email
     *
     * @var integer @JMS\Type("integer")
     */
    private $email;

    /**
     * Sets the id of an activity.
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the id of an activity.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id for the organization an activity applies to.
     *
     * @param int $organization            
     */
    public function setOrganization($organization = null)
    {
        $this->organization = $organization;
    }

    /**
     * Returns the id for the organization an activity applies to.
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Sets the id of the faculty associated with an activity.
     *
     * @param int $personIdFaculty            
     */
    public function setPersonIdFaculty($personIdFaculty = null)
    {
        $this->personIdFaculty = $personIdFaculty;
    }

    /**
     * Returns the id of the faculty associated with an activity.
     *
     * @return int
     */
    public function getPersonIdFaculty()
    {
        return $this->personIdFaculty;
    }

    /**
     * Sets the id of the student associated with an activity.
     *
     * @param int $personIdStudent
     */
    public function setPersonIdStudent($personIdStudent = null)
    {
        $this->personIdStudent = $personIdStudent;
    }

    /**
     * Returns the id of the student associated with an activity.
     *
     * @return int
     */
    public function getPersonIdStudent()
    {
        return $this->personIdStudent;
    }

    /**
     * Sets an activity's type.
     *
     * @param string $activityType            
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
    }

    /**
     * Returns an activity's type.
     *
     * @return string
     */
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     * Sets the date an activity occurred.
     *
     * @param mixed $activityDate
     *
     * @return mixed
     */
    public function setActivityDate($activityDate)
    {
        $this->activityDate = $activityDate;
        
        return $this;
    }

    /**
     * Returns the date an activity occurred.
     *
     * @return mixed
     */
    public function getActivityDate()
    {
        return $this->activityDate;
    }

    /**
     * Sets the number of referrals made.
     *
     * @param int $referrals            
     */
    public function setReferrals($referrals = null)
    {
        $this->referrals = $referrals;
    }

    /**
     * Returns the number of referrals made.
     *
     * @return int
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * Sets the number of appointments made.
     *
     *
     * @param int $appointments            
     */
    public function setAppointments($appointments = null)
    {
        $this->appointments = $appointments;
    }

    /**
     * Returns the number of appointments made.
     *
     * @return int
     */
    public function getAppointments()
    {
        return $this->appointments;
    }

    /**
     * Sets the reason for an activity.
     *
     * @param string $reason            
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * Returns the reason for an activity.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Sets the note for an activity.
     *
     * @param string $note            
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Returns the note for an activity.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Sets the number of contacts.
     *
     * @param int $contacts            
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * Returns the number of contacts.
     *
     * @return int
     */
    public function getContacts()
    {
        return $this->contacts;
    }
    
    /**
     * Sets the email for an activity log.
     *
     * @param int $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    /**
     * Returns the email for an activity log.
     *
     * @return int
     */
    public function getEmail()
    {
        return $this->email;
    }
}