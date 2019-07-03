<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Contacts
 *
 * @package Synapse\RestBundle\Entity
 */
class EmailDto
{

    /**
     * Id of the organization that a faculty and student belong to.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Id of an email that has been sent between a faculty and student.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $emailId;

    /**
     * Id of the Student that an email has been sent to.
     * 
     * @var string @JMS\Type("string")
     */
    private $personStudentId;

    /**
     * Id of the faculty that is sending/viewing emails to students.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personStaffId;
    
    /**
     * Email address that is used to populate the 'reply to' field.
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * Id of the Activity Category supplied as the reason for the email being sent.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reasonCategorySubitemId;
    
    /**
     * Activity Category supllied as the reason for the email being sent.
     *
     * @var string @JMS\Type("string")
     */
    private $reasonCategorySubitem;

    /**
     * List of email addresses that have been BCC's(Blind Carbon Copied) to an email.
     * 
     * @var string @JMS\Type("string")
     */
    private $emailBccList;

    /**
     * String that is supplied as the email's subject.
     * 
     * @var string @JMS\Type("string")
     */
    private $emailSubject;
    
    /**
     * Body text of the email.
     * 
     * @var string @JMS\Type("string")
     */
    private $emailBody;

    /**
     * Determines the scope that an email can be viewed by other user's or teams.
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\ShareOptionsDto>")
     */
    private $shareOptions;

    /**
     * Id of the activity log that is associated with a user's email address.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $activityLogId;

    /**
     * Sets the id of the activity log that tracks the history of an email.
     *
     * @param int $activityLogId
     */
    public function setActivityLogId($activityLogId)
    {
        $this->activityLogId = $activityLogId;
    }

    /**
     * Returns the id of the activity log that tracks the history of an email.
     *
     * @return int
     */
    public function getActivityLogId()
    {
        return $this->activityLogId;
    }

    /**
     * Sets the list of emails that have been Bcc'd to an email.
     *
     * @param string $emailBccList
     */
    public function setEmailBccList($emailBccList)
    {
        $this->emailBccList = $emailBccList;
    }

    /**
     * Returns the list of emails that have been Bcc'd to an email.
     *
     * @return string
     */
    public function getEmailBccList()
    {
        return $this->emailBccList;
    }

    /**
     * Sets the body text of the email being sent.
     *
     * @param string $emailBody
     */
    public function setEmailBody($emailBody)
    {
        $this->emailBody = $emailBody;
    }

    /**
     * Returns the body text of the email being sent.
     *
     * @return string
     */
    public function getEmailBody()
    {
        return $this->emailBody;
    }

    /**
     * Sets the id of the email being sent.
     *
     * @param int $emailId
     */
    public function setEmailId($emailId)
    {
        $this->emailId = $emailId;
    }

    /**
     * Returns the id of the email being sent.
     *
     * @return int
     */
    public function getEmailId()
    {
        return $this->emailId;
    }

    /**
     * Sets the subject text of the email being sent.
     *
     * @param string $emailSubject
     */
    public function setEmailSubject($emailSubject)
    {
        $this->emailSubject = $emailSubject;
    }

    /**
     * Returns the subject text of the email being sent.
     *
     * @return string
     */
    public function getEmailSubject()
    {
        return $this->emailSubject;
    }

    /**
     * Sets the id of the organization that email belongs to.
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns the id of the organization that email belongs to.
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the id of the faculty member that is sending an email.
     *
     * @param int $personStaffId
     */
    public function setPersonStaffId($personStaffId)
    {
        $this->personStaffId = $personStaffId;
    }

    /**
     * Returns the id of the faculty member that is sending an email.
     *
     * @return int
     */
    public function getPersonStaffId()
    {
        return $this->personStaffId;
    }

    /**
     * Sets the id of the student that an email is being sent to.
     *
     * @param string $personStudentId
     */
    public function setPersonStudentId($personStudentId)
    {
        $this->personStudentId = $personStudentId;
    }

    /**
     * Returns the id of the student that an email is being sent to.
     *
     * @return string
     */
    public function getPersonStudentId()
    {
        return $this->personStudentId;
    }

    /**
     * Sets the id of the Activity Category that is used as the reason the email is being sent.
     *
     * @param int $reasonCategorySubitemId
     */
    public function setReasonCategorySubitemId($reasonCategorySubitemId)
    {
        $this->reasonCategorySubitemId = $reasonCategorySubitemId;
    }

    /**
     * Returns the id of the Activity Category that is used as the reason the email is being sent.
     *
     * @return int
     */
    public function getReasonCategorySubitemId()
    {
        return $this->reasonCategorySubitemId;
    }

    /**
     * Sets the users and teams that can view the email.
     *
     * @param Object $shareOptions
     */
    public function setShareOptions($shareOptions)
    {
        $this->shareOptions = $shareOptions;
    }

    /**
     * Returns the users and teams that can view the email.
     *
     * @return Object
     */
    public function getShareOptions()
    {
        return $this->shareOptions;
    }

    /**
     * Sets the email that will be supplied in the 'Reply to' field of an email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the email that will be supplied in the 'Reply to' field of an email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the Activity Category that is the reason for the email being sent.
     *
     * @param string $reasonCategorySubitem
     */
    public function setReasonCategorySubitem($reasonCategorySubitem)
    {
        $this->reasonCategorySubitem = $reasonCategorySubitem;
    }

    /**
     * Returns the Activity Category that is the reason for the email being sent.
     *
     * @return string
     */
    public function getReasonCategorySubitem()
    {
        return $this->reasonCategorySubitem;
    }

}