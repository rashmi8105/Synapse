<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Team members activities
 *
 * @package Synapse\RestBundle\Entity
 */
class TeamMembersActivitiesDto
{

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $date;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $teamMemberId;
    
    /**
     * teamMemberExternalId
     *
     * @var string @JMS\Type("string")
     *
     */
    private $teamMemberExternalId;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $teamMemberFirstName;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $teamMemberLastName;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $teamMemberEmailId;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $studentId;
    
    /**
     * studentExternalId
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentExternalId;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $studentFirstName;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $studentLastName;
    
    /**
     * studentEmail
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentEmail;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $activityType;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $activityId;

    /**
     * @JMS\Type("integer")
     * 
     * @var integer
     */
    private $reasonId;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $reasonText;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;
    /**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $date            
     */
    
    /**
     * activityFlagPrivate
     *
     * @var datetime @JMS\Type("boolean")
     *
     */
    private $activityFlagPrivate;
    
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     *
     * @param integer $teamMemberId            
     */
    public function setTeamMemberId($teamMemberId)
    {
        $this->teamMemberId = $teamMemberId;
    }

    /**
     *
     * @return int
     */
    public function getTeamMemberId()
    {
        return $this->teamMemberId;
    }
    
    
    /**
     *
     * @param string $teamMemberExternalId
     */
    public function setTeamMemberExternalId($teamMemberExternalId)
    {
        $this->teamMemberExternalId = $teamMemberExternalId;
    }
    
    /**
     *
     * @return string
     */
    public function getTeamMemberExternalId()
    {
        return $this->teamMemberExternalId;
    }

    /**
     *
     * @param string $teamMemberFirstName            
     */
    public function setTeamMemberFirstName($teamMemberFirstName)
    {
        $this->teamMemberFirstName = $teamMemberFirstName;
    }

    /**
     *
     * @return string
     */
    public function getTeamMemberFirstName()
    {
        return $this->teamMemberFirstName;
    }

    /**
     *
     * @param string $teamMemberLastName            
     */
    public function setTeamMemberLastName($teamMemberLastName)
    {
        $this->teamMemberLastName = $teamMemberLastName;
    }

    /**
     *
     * @return string
     */
    public function getTeamMemberLastName()
    {
        return $this->teamMemberLastName;
    }

    /**
     *
     * @param string $teamMemberEmailId            
     */
    public function setTeamMemberEmailId($teamMemberEmailId)
    {
        $this->teamMemberEmailId = $teamMemberEmailId;
    }

    /**
     *
     * @return string
     */
    public function getTeamMemberEmailId()
    {
        return $this->teamMemberEmailId;
    }

    /**
     *
     * @param integer $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }
    
    /**
     *
     * @param string $studentExternalId
     */
    public function setStudentExternalId($studentExternalId)
    {
        $this->studentExternalId = $studentExternalId;
    }
    
    /**
     *
     * @return string
     */
    public function getStudentExternalId()
    {
        return $this->studentExternalId;
    }
    
    

    /**
     *
     * @param string $studentFirstName            
     */
    public function setStudentFirstName($studentFirstName)
    {
        $this->studentFirstName = $studentFirstName;
    }

    /**
     *
     * @return string
     */
    public function getStudentFirstName()
    {
        return $this->studentFirstName;
    }

    /**
     *
     * @param string $studentLastName            
     */
    public function setStudentLastName($studentLastName)
    {
        $this->studentLastName = $studentLastName;
    }

    /**
     *
     * @return string
     */
    public function getStudentLastName()
    {
        return $this->studentLastName;
    }
    
    /**
     *
     * @param string $studentEmail
     */
    public function setStudentEmail($studentEmail)
    {
        $this->studentEmail = $studentEmail;
    }
    
    /**
     *
     * @return string
     */
    public function getStudentEmail()
    {
        return $this->studentEmail;
    }

    /**
     *
     * @param string $activityType            
     */
    public function setActivityType($activityType)
    {
        $this->activityType = $activityType;
    }

    /**
     *
     * @return string
     */
    public function getActivityType()
    {
        return $this->activityType;
    }

    /**
     *
     * @param integer $activityId            
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
    }

    /**
     *
     * @return int
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     *
     * @param integer $reasonId            
     */
    public function setReasonId($reasonId)
    {
        $this->reasonId = $reasonId;
    }

    /**
     *
     * @return int
     */
    public function getReasonId()
    {
        return $this->reasonId;
    }

    /**
     *
     * @param string $reasonText            
     */
    public function setReasonText($reasonText)
    {
        $this->reasonText = $reasonText;
    }

    /**
     *
     * @return string
     */
    public function getReasonText()
    {
        return $this->reasonText;
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
     * @param boolean $activityFlagPrivate
     */
    public function setActivityFlagPrivate($activityFlagPrivate)
    {
    	$this->activityFlagPrivate = $activityFlagPrivate;
    }
    
    /**
     *
     * @return boolean
     */
    public function getActivityFlagPrivate()
    {
    	return $this->activityFlagPrivate;
    }
}