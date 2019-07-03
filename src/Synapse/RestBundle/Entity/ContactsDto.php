<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Contacts
 *
 * @package Synapse\RestBundle\Entity
 */
class ContactsDto
{

    /**
     * Organization Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Language Id that the contact is using
     * 
     * @var integer @JMS\Type("integer")
     */
    private $langId;

    /**
     * Contact Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $contactId;

    /**
     * Id of student
     * 
     * @var string @JMS\Type("string")
     */
    private $personStudentId;

    /**
     * Id of staff/faculty
     * 
     * @var integer @JMS\Type("integer")
     */
    private $personStaffId;

    /**
     * Reason category of submitted item Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reasonCategorySubitemId;

    /**
     * Reason category of submitted item
     * 
     * @var string @JMS\Type("string")
     */
    private $reasonCategorySubitem;

    /**
     * Contact type Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $contactTypeId;

    /**
     * Contact type
     * 
     * @var string @JMS\Type("string")
     */
    private $contactTypeText;

    /**
     * Date of contact
     * 
     * @var integer @JMS\Type("DateTime<'m/d/Y'>")
     */
    private $dateOfContact;

    /**
     * Comment
     * 
     * @var string @JMS\Type("string")
     */
    private $comment;

    /**
     * Issue discussed with student check
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $issueDiscussedWithStudent;

    /**
     * High priority concern check
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $highPriorityConcern;

    /**
     * Issue revealed to student check
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $issueRevealedToStudent;

    /**
     * Student indicated to leave check
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $studentIndicatedToLeave;

    /**
     * Share option
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\ShareOptionsDto>")
     */
    private $shareOptions;

    /**
     * Activity log Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $activityLogId;

    /**
     * Return comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment            
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Return contact Id
     *
     * @return int
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * Set contact Id
     *
     * @param int $contactId            
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * Return contact type Id
     *
     * @return int
     */
    public function getContactTypeId()
    {
        return $this->contactTypeId;
    }

    /**
     * Set contact type Id
     *
     * @param int $contactTypeId            
     */
    public function setContactTypeId($contactTypeId)
    {
        $this->contactTypeId = $contactTypeId;
    }

    /**
     * Return date of contact
     *
     * @return mixed
     */
    public function getDateOfContact()
    {
        return $this->dateOfContact;
    }

    /**
     * Set date of contact
     *
     * @param mixed $dateOfContact            
     */
    public function setDateOfContact($dateOfContact)
    {
        $this->dateOfContact = $dateOfContact;
    }

    /**
     * Return high priority concern
     *
     * @return boolean
     */
    public function getHighPriorityConcern()
    {
        return $this->highPriorityConcern;
    }

    /**
     * Set high priority concern
     *
     * @param boolean $highPriorityConcern            
     */
    public function setHighPriorityConcern($highPriorityConcern)
    {
        $this->highPriorityConcern = $highPriorityConcern;
    }

    /**
     * Return issue discussed with student boolean value
     *
     * @return boolean
     */
    public function getIssueDiscussedWithStudent()
    {
        return $this->issueDiscussedWithStudent;
    }

    /**
     * Set issue discussed with student boolean value
     *
     * @param boolean $issueDiscussedWithStudent            
     */
    public function setIssueDiscussedWithStudent($issueDiscussedWithStudent)
    {
        $this->issueDiscussedWithStudent = $issueDiscussedWithStudent;
    }

    /**
     * Return issue revealed to student boolean value
     *
     * @return boolean
     */
    public function getIssueRevealedToStudent()
    {
        return $this->issueRevealedToStudent;
    }

    /**
     * Set issue revealed to student boolean value
     *
     * @param boolean $issueRevealedToStudent            
     */
    public function setIssueRevealedToStudent($issueRevealedToStudent)
    {
        $this->issueRevealedToStudent = $issueRevealedToStudent;
    }

    /**
     * Return language Id
     *
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Set language Id
     *
     * @param int $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     * Return organization Id
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set organization Id
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Return person staff Id
     *
     * @return int
     */
    public function getPersonStaffId()
    {
        return $this->personStaffId;
    }

    /**
     * Set person staff Id
     *
     * @param int $personStaffId            
     */
    public function setPersonStaffId($personStaffId)
    {
        $this->personStaffId = $personStaffId;
    }

    /**
     * Return person student Id
     *
     * @return string
     */
    public function getPersonStudentId()
    {
        return $this->personStudentId;
    }

    /**
     * Set person student Id
     *
     * @param string $personStudentId            
     */
    public function setPersonStudentId($personStudentId)
    {
        $this->personStudentId = $personStudentId;
    }

    /**
     * Return reason category submitted item Id
     *
     * @return int
     */
    public function getReasonCategorySubitemId()
    {
        return $this->reasonCategorySubitemId;
    }

    /**
     * Set reason category submitted item Id
     *
     * @param int $reasonCategorySubitemId            
     */
    public function setReasonCategorySubitemId($reasonCategorySubitemId)
    {
        $this->reasonCategorySubitemId = $reasonCategorySubitemId;
    }

    /**
     * Return share option
     *
     * @return Object
     */
    public function getShareOptions()
    {
        return $this->shareOptions;
    }

    /**
     * Set share option
     *
     * @param Object $shareOptions            
     */
    public function setShareOptions($shareOptions)
    {
        $this->shareOptions = $shareOptions;
    }

    /**
     * Return student indicated to leave boolean value
     *
     * @return boolean
     */
    public function getStudentIndicatedToLeave()
    {
        return $this->studentIndicatedToLeave;
    }

    /**
     * Set student indicated to leave boolean value
     *
     * @param boolean $studentIndicatedToLeave            
     */
    public function setStudentIndicatedToLeave($studentIndicatedToLeave)
    {
        $this->studentIndicatedToLeave = $studentIndicatedToLeave;
    }

    /**
     * Return contact type text
     *
     * @return string
     */
    public function getContactTypeText()
    {
        return $this->contactTypeText;
    }

    /**
     * Set contact type text
     *
     * @param string $contactTypeText            
     */
    public function setContactTypeText($contactTypeText)
    {
        $this->contactTypeText = $contactTypeText;
    }

    /**
     * Return reason category of the submitted item
     *
     * @return string
     */
    public function getReasonCategorySubitem()
    {
        return $this->reasonCategorySubitem;
    }

    /**
     * Set reason category of the submitted item
     *
     * @param string $reasonCategorySubitem            
     */
    public function setReasonCategorySubitem($reasonCategorySubitem)
    {
        $this->reasonCategorySubitem = $reasonCategorySubitem;
    }

    /**
     * Return activity log Id
     *
     * @return int
     */
    public function getActivityLogId()
    {
        return $this->activityLogId;
    }

    /**
     * Set activity log Id
     *
     * @param int $activityLogId            
     */
    public function setActivityLogId($activityLogId)
    {
        $this->activityLogId = $activityLogId;
    }
}