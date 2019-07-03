<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Data Transfer Object for Notes
 *
 * @package Synapse\RestBundle\Entity
 */
class NotesDto
{

    /**
     * Id of a single note.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $notesId;

    /**
     * Date that a note was updated.
     *
     * @var DateTime @JMS\Type("DateTime")
     */
    private $notesUpdatedOn;

    /**
     * Organization of a note.
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * Id of the Activity Category that a note applies to.
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $reasonCategorySubitemId;

    /**
     * Name of the Activity Category that is included in a note.
     * 
     * @var string @JMS\Type("string")
     */
    private $reasonCategorySubitem;

    /**
     * Id of the student that a note references.
     * 
     * @var string @JMS\Type("string")
     */
    private $notesStudentId;

    /**
     * Id of the staff member that creates a note.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $staffId;

    /**
     * Comment attached to a note.
     * 
     * @var string @JMS\Type("string")
     */
    private $comment;

    /**
     * Array object containing the ids of the members of a team that a note has been shared with.
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\TeamIdsDto>")
     */
    private $teamshare;

    /**
     * Id of the activity log that a note is referenced in.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $activityLogId;

    /**
     * Object containing the sharing options and constraints for a note.
     * 
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\ShareOptionsDto>")
     */
    private $shareOptions;

    /**
     * Sets the id for a note.
     *
     * @param int $notesId            
     */
    public function setNotesId($notesId)
    {
        $this->notesId = $notesId;
    }

    /**
     * Returns the id for a note.
     *
     * @return int
     */
    public function getNotesId()
    {
        return $this->notesId;
    }

    /**
     * Sets the date that a note is updated.
     *
     * @param DateTime $notesUpdatedOn
     */
    public function setNotesUpdatedOn($notesUpdatedOn)
    {
        $this->notesUpdatedOn = $notesUpdatedOn;
    }

    /**
     * Returns the date that a note is updated.
     *
     * @return Datetime
     */
    public function getNotesUpdatedOn()
    {
        return $this->notesUpdatedOn;
    }

    /**
     * Sets the comment for a note.
     *
     * @param string $comment            
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Returns the comment for a note.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the staff id for a note.
     *
     * @param int $staffId            
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;
    }

    /**
     * Returns the staff id for a note.
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->staffId;
    }

    /**
     * Sets the share options for a note.
     *
     * @param Object $shareOptions            
     */
    public function setShareOptions($shareOptions)
    {
        $this->shareOptions = $shareOptions;
    }

    /**
     * Gets the share options for a note.
     *
     * @return Object
     */
    public function getShareOptions()
    {
        return $this->shareOptions;
    }

    /**
     * Sets the Activity Category id for a note.
     *
     * @param int $reasonCategorySubitemId            
     */
    public function setReasonCategorySubitemId($reasonCategorySubitemId)
    {
        $this->reasonCategorySubitemId = $reasonCategorySubitemId;
    }

    /**
     * Returns the Activity Category id for a note.
     *
     * @return int
     */
    public function getReasonCategorySubitemId()
    {
        return $this->reasonCategorySubitemId;
    }

    /**
     * Sets the name of the Activity Category for a note.
     *
     * @param string $reasonCategorySubitem            
     */
    public function setReasonCategorySubitem($reasonCategorySubitem)
    {
        $this->reasonCategorySubitem = $reasonCategorySubitem;
    }

    /**
     * Returns the name of the Activity Category for a note.
     *
     * @return string
     */
    public function getReasonCategorySubitem()
    {
        return $this->reasonCategorySubitem;
    }

    /**
     * Sets a note's student id.
     *
     * @param string $notesStudentId            
     */
    public function setNotesStudentId($notesStudentId)
    {
        $this->notesStudentId = $notesStudentId;
    }

    /**
     * Returns a note's student id.
     *
     * @return string
     */
    public function getNotesStudentId()
    {
        return $this->notesStudentId;
    }

    /**
     * Sets a note's organization id.
     *
     * @param int $organizationId            
     */
    public function setOrganization($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * Returns a note's organization id.
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organizationId;
    }

    /**
     * Sets the team that a note has been shared with.
     *
     * @param Object $teamshare            
     */
    public function setTeamshare($teamshare)
    {
        $this->teamshare = $teamshare;
    }

    /**
     * Sets the id of the activity log that a note is referenced in.
     *
     * @param int $activityLogId            
     */
    public function setActivityLogId($activityLogId)
    {
        $this->activityLogId = $activityLogId;
    }

    /**
     * Returns the id of the activity log that a note is referenced in.
     *
     * @return int
     */
    public function getActivityLogId()
    {
        return $this->activityLogId;
    }
}