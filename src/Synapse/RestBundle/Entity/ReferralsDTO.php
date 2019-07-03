<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Referrals
 * *
 * 
 * @package Synapse\RestBundle\Entity
 */
class ReferralsDTO
{

    /**
     * Id of the organization.
     * 
     * @var int
     * @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Language id. Always 1(English).
     * 
     * @var int
     * @JMS\Type("integer")
     */
    private $langId;

    /**
     * Id of a referral.
     * 
     * @var int
     * @JMS\Type("integer")
     */
    private $referralId;

    /**
     * Id of the student that is being referred.
     * 
     * @var int
     * @JMS\Type("string")
     */
    private $personStudentId;

    /**
     * Id of the staff member that a student is referred to.
     * 
     * @var int
     * @JMS\Type("integer")
     */
    private $personStaffId;

    /**
     * Id of the reason category that has been selected as the reason for a referral.
     * 
     * @var int
     * @JMS\Type("integer")
     */
    private $reasonCategorySubitemId;

    /**
     * Description of the reason category set for a referral.
     * 
     * @var string
     * @JMS\Type("string")
     */
    private $reasonCategorySubitem;

    /**
     * Id of the person that a referral has been assigned to.
     * 
     * @var int
     * @JMS\Type("integer")
     */
    private $assignedToUserId;

    /**
     * Array of people that have expressed interest in a referral.
     * 
     * @var array
     * @JMS\Type("array")
     */
    private $interestedParties;

    /**
     * Comment attached to a referral.
     * 
     * @var string
     * @JMS\Type("string")
     */
    private $comment;

    /**
     * If True, the issue has been discussed with the referred student.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $issueDiscussedWithStudent;

    /**
     * If True, the referral has been indicated to be a high priority issue.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $highPriorityConcern;

    /**
     * If True, the issue has been revealed to the student.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $issueRevealedToStudent;

    /**
     * If True, the referred student has indicated that they were going to leave.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $studentIndicatedToLeave;
    
    /**
     * If True, then the student will be notified.
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $notifyStudent;

    /**
     * Array of share option objects.
     * 
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\ShareOptionsDto>")
     */
    private $shareOptions;

    /**
     * Status of a referral.
     * 
     * @var string
     * @JMS\Type("string")
     */
    private $status;

    /**
     * Object describing who has been assigned the referral.
     * 
     * @var object
     * @JMS\Type("Synapse\RestBundle\Entity\AssignToResponseDto")
     */
    private $assignedTo;
    
    /**
     * Status of the student that is being referred.
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;
    
    /**
     * Id of the activity log that records the referral.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $activityLogId;
    
    /**
     * First name of the person that creates a referral.
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $referredByFirstName;
    
    /**
     * Last name of the person that creates a referral.
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $referredByLastName;
    
    /**
     * Assigned to user key.
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $assignedToUserKey;
    

    /**
     * Gets the value of organizationId.
     *
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Sets the value of organizationId.
     *
     * @param mixed $organizationId
     *            the organization id
     *            
     * @return self
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
        
        return $this;
    }

    /**
     * Gets the value of langId.
     *
     * @return mixed
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Sets the value of langId.
     *
     * @param mixed $langId
     *            the lang id
     *            
     * @return self
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
        
        return $this;
    }

    /**
     * Gets the value of referralId.
     *
     * @return mixed
     */
    public function getReferralId()
    {
        return $this->referralId;
    }

    /**
     * Sets the value of referralId.
     *
     * @param mixed $referralId
     *            the referral id
     *            
     * @return self
     */
    public function setReferralId($referralId)
    {
        $this->referralId = $referralId;
        
        return $this;
    }

    /**
     * Gets the value of personStudentId.
     *
     * @return mixed
     */
    public function getPersonStudentId()
    {
        return $this->personStudentId;
    }

    /**
     * Sets the value of personStudentId.
     *
     * @param mixed $personStudentId
     *            the person student id
     *            
     * @return self
     */
    public function setPersonStudentId($personStudentId)
    {
        $this->personStudentId = $personStudentId;
        
        return $this;
    }

    /**
     * Gets the value of personStaffId.
     *
     * @return mixed
     */
    public function getPersonStaffId()
    {
        return $this->personStaffId;
    }

    /**
     * Sets the value of personStaffId.
     *
     * @param mixed $personStaffId
     *            the person staff id
     *            
     * @return self
     */
    public function setPersonStaffId($personStaffId)
    {
        $this->personStaffId = $personStaffId;
        
        return $this;
    }

    /**
     * Gets the value of reasonCategorySubitemId.
     *
     * @return mixed
     */
    public function getReasonCategorySubitemId()
    {
        return $this->reasonCategorySubitemId;
    }

    /**
     * Sets the value of reasonCategorySubitemId.
     *
     * @param mixed $reasonCategorySubitemId
     *            the reason category subitem id
     *            
     * @return self
     */
    public function setReasonCategorySubitemId($reasonCategorySubitemId)
    {
        $this->reasonCategorySubitemId = $reasonCategorySubitemId;
        
        return $this;
    }

    /**
     * Gets the value of reasonCategorySubitem.
     *
     * @return mixed
     */
    public function getReasonCategorySubitem()
    {
        return $this->reasonCategorySubitem;
    }

    /**
     * Sets the value of reasonCategorySubitem.
     *
     * @param mixed $reasonCategorySubitem
     *            the reason category subitem
     *            
     * @return self
     */
    public function setReasonCategorySubitem($reasonCategorySubitem)
    {
        $this->reasonCategorySubitem = $reasonCategorySubitem;
        
        return $this;
    }

    /**
     * Gets the value of assignedToUserId.
     *
     * @return mixed
     */
    public function getAssignedToUserId()
    {
        return $this->assignedToUserId;
    }

    /**
     * Sets the value of assignedToUserId.
     *
     * @param mixed $assignedToUserId
     *            the assigned to user id
     *            
     * @return self
     */
    public function setAssignedToUserId($assignedToUserId)
    {
        $this->assignedToUserId = $assignedToUserId;
        
        return $this;
    }

    /**
     * Gets the value of interestedParties.
     *
     * @return mixed
     */
    public function getInterestedParties()
    {
        return $this->interestedParties;
    }

    /**
     * Sets the value of interestedParties.
     *
     * @param mixed $interestedParties
     *            the interested parties
     *            
     * @return self
     */
    public function setInterestedParties($interestedParties)
    {
        $this->interestedParties = $interestedParties;
        
        return $this;
    }

    /**
     * Gets the value of comment.
     *
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the value of comment.
     *
     * @param mixed $comment
     *            the comment
     *            
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        
        return $this;
    }

    /**
     * Gets the value of issueDiscussedWithStudent.
     *
     * @return mixed
     */
    public function getIssueDiscussedWithStudent()
    {
        return $this->issueDiscussedWithStudent;
    }

    /**
     * Sets the value of issueDiscussedWithStudent.
     *
     * @param mixed $issueDiscussedWithStudent
     *            the issue discussed with student
     *            
     * @return self
     */
    public function setIssueDiscussedWithStudent($issueDiscussedWithStudent)
    {
        $this->issueDiscussedWithStudent = $issueDiscussedWithStudent;
        
        return $this;
    }

    /**
     * Gets the value of highPriorityConcern.
     *
     * @return mixed
     */
    public function getHighPriorityConcern()
    {
        return $this->highPriorityConcern;
    }

    /**
     * Sets the value of highPriorityConcern.
     *
     * @param mixed $highPriorityConcern
     *            the high priority concern
     *            
     * @return self
     */
    public function setHighPriorityConcern($highPriorityConcern)
    {
        $this->highPriorityConcern = $highPriorityConcern;
        
        return $this;
    }

    /**
     * Gets the value of issueRevealedToStudent.
     *
     * @return mixed
     */
    public function getIssueRevealedToStudent()
    {
        return $this->issueRevealedToStudent;
    }

    /**
     * Sets the value of issueRevealedToStudent.
     *
     * @param mixed $issueRevealedToStudent
     *            the issue revealed to student
     *            
     * @return self
     */
    public function setIssueRevealedToStudent($issueRevealedToStudent)
    {
        $this->issueRevealedToStudent = $issueRevealedToStudent;
        
        return $this;
    }

    /**
     * Gets the value of studentIndicatedToLeave.
     *
     * @return mixed
     */
    public function getStudentIndicatedToLeave()
    {
        return $this->studentIndicatedToLeave;
    }

    /**
     * Sets the value of studentIndicatedToLeave.
     *
     * @param mixed $studentIndicatedToLeave
     *            the student indicated to leave
     *            
     * @return self
     */
    public function setStudentIndicatedToLeave($studentIndicatedToLeave)
    {
        $this->studentIndicatedToLeave = $studentIndicatedToLeave;
        
        return $this;
    }

    /**
     * @param mixed $notifyStudent
     */
    public function setNotifyStudent($notifyStudent)
    {
        $this->notifyStudent = $notifyStudent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotifyStudent()
    {
        return $this->notifyStudent;
    }


    /**
     * Gets the value of shareOptions.
     *
     * @return mixed
     */
    public function getShareOptions()
    {
        return $this->shareOptions;
    }

    /**
     * Sets the value of shareOptions.
     *
     * @param mixed $shareOptions
     *            the share options
     *            
     * @return self
     */
    public function setShareOptions($shareOptions)
    {
        $this->shareOptions = $shareOptions;
        
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }
    
    /**
     *
     * @param string $studentStatus
     */
    public function setStudentStatus($studentStatus)
    {
        $this->studentStatus = $studentStatus;
    }
    
    /**
     *
     * @return string
     */
    public function getStudentStatus()
    {
        return $this->studentStatus;
    }
    
    /**
     *
     * @return int
     */
    public function getActivityLogId()
    {
        return $this->activityLogId;
    }
    

    /**
     *
     * @param string $referredByFirstName
     */
    public function setReferredByFirstName($referredByFirstName)
    {
    	$this->referredByFirstName = $referredByFirstName;
    }
    
    /**
     *
     * @return string
     */
    public function getReferredByFirstName()
    {
    	return $this->referredByFirstName;
    }
    
    /**
     *
     * @param string $referredByLastName
     */
    public function setReferredByLastName($referredByLastName)
    {
    	$this->referredByLastName = $referredByLastName;
    }
    
    /**
     *
     * @return string
     */
    public function getReferredByLastName()
    {
    	return $this->referredByLastName;
    }
    
    /**
     *
     * @param string $assignedToUserKey
     */
    public function setAssignedToUserKey($assignedToUserKey)
    {
    	$this->assignedToUserKey = $assignedToUserKey;
    }
    
    /**
     *
     * @return string
     */
    public function getAssignedToUserKey()
    {
    	return $this->assignedToUserKey;
    }
    
}