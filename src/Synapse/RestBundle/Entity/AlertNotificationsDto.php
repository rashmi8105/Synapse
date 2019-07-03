<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for AlertNotifications
 *
 * @package Synapse\RestBundle\Entity
 */
class AlertNotificationsDto
{

    /**
     * person_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $personId;

    /**
     * Count of unseen notifications
     *
     * @var integer @JMS\Type("integer")
     */
    private $unseenNotificationCount;

    /**
     * Count of campus announcements
     *
     * @var integer @JMS\Type("integer")
     */
    private $orgAnnouncementCount;

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $alertId;

    /**
     * alert_reason
     *
     * @var string @JMS\Type("string")
     */
    private $alertReason;

    /**
     * activity_type
     *
     * @var string @JMS\Type("string")
     */
    private $activityType;

    /**
     * activity_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $activityId;

    /**
     * activity_date
     *
     * @var \Datetime @JMS\Type("DateTime")
     */
    private $activityDate;

    /**
     * reason
     *
     * @var string @JMS\Type("string")
     */
    private $reason;

    /**
     * student
     *
     * @var array @JMS\Type("array<Synapse\RestBundle\Entity\StudentsDto>")
     */
    private $students;
    
    /**
     * faculty
     *
     * @var array @JMS\Type("array<Synapse\RestBundle\Entity\FacultyDto>")
     */
    private $faculties;

    /**
     * alerts
     *
     * @var array @JMS\Type("array<Synapse\RestBundle\Entity\AlertNotificationsDto>")
     */
    private $alerts;

    /**
     * is read
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isRead;

    /**
     * is seen
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $isSeen;

    /**
     * shared_by
     *
     * @var string @JMS\Type("string")
     */
    private $sharedBy;

    /**
     * org_course_upload_file
     *
     * @var string @JMS\Type("string")
     */
    private $orgCourseUploadFile;
    
    /**
     *
     * @var Object @JMS\Type("array<Synapse\CampusResourceBundle\EntityDto\SystemMessage>")
     *
     */
    private $systemMessage;
    

    /**
     * user_name
     *
     * @var string @JMS\Type("string")
     */
    private $userName;
    
    
    private $reportRunningStatusId;

    /**
     * Text of the notification after the user clicks on the alert bell 
     *
     * @var string @JMS\Type("string")
     */
    private $notificationBodyText;

    /**
     * Text of the notification when the user hovers over the alert bell 
     *
     * @var string @JMS\Type("string")
     */
    private $notificationHoverText;
    
    
    /**
     *
     * @param int $reportRunningStatusId
     */
    public function setReportRunningStatusId($reportRunningStatusId)
    {
        $this->reportRunningStatusId = $reportRunningStatusId;
    }
    
    /**
     *
     * @return int
     */
    public function getReportRunningStatusId()
    {
        return $this->reportRunningStatusId;
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
     * @param int $unseenNotificationCount
     */
    public function setUnseenNotificationCount($unseenNotificationCount)
    {
        $this->unseenNotificationCount = $unseenNotificationCount;
    }

    /**
     *
     * @return int
     */
    public function getUnseenNotificationCount()
    {
        return $this->unseenNotificationCount;
    }

    /**
     * @return int
     */
    public function getOrgAnnouncementCount()
    {
        return $this->orgAnnouncementCount;
    }

    /**
     * @param int $orgAnnouncementCount
     */
    public function setOrgAnnouncementCount($orgAnnouncementCount)
    {
        $this->orgAnnouncementCount = $orgAnnouncementCount;
    }


    /**
     *
     * @param int $alertId            
     */
    public function setAlertId($alertId)
    {
        $this->alertId = $alertId;
    }

    /**
     *
     * @return int
     */
    public function getAlertId()
    {
        return $this->alertId;
    }

    /**
     *
     * @param string $alertReason            
     */
    public function setAlertReason($alertReason)
    {
        $this->alertReason = $alertReason;
    }

    /**
     *
     * @return string
     */
    public function getAlertReason()
    {
        return $this->alertReason;
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
     * @param int $activityId            
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
     * @param string $reason            
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     *
     * @param \DateTime $activityDate
     */
    public function setActivityDate($activityDate)
    {
        $this->activityDate = $activityDate;
    }

    /**
     *
     * @return mixed
     */
    public function getActivityDate()
    {
        return $this->activityDate;
    }

    /**
     *
     * @param array $students
     */
    public function setStudents($students)
    {
        $this->students = $students;
    }

    /**
     *
     * @param array $alerts
     */
    public function setAlerts($alerts)
    {
        $this->alerts = $alerts;
    }

    /**
     *
     * @param boolean $isRead
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }

    /**
     *
     * @return boolean
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * @return bool
     */
    public function getIsSeen()
    {
        return $this->isSeen;
    }

    /**
     * @param bool $isSeen
     */
    public function setIsSeen($isSeen)
    {
        $this->isSeen = $isSeen;
    }

    /**
     *
     * @param string $sharedBy            
     */
    public function setSharedBy($sharedBy)
    {
        $this->sharedBy = $sharedBy;
    }

    /**
     *
     * @return string
     */
    public function getSharedBy()
    {
        return $this->sharedBy;
    }

    /**
     *
     * @param string $orgCourseUploadFile            
     */
    public function setOrgCourseUploadFile($orgCourseUploadFile)
    {
        $this->orgCourseUploadFile = $orgCourseUploadFile;
    }

    /**
     *
     * @return string
     */
    public function getOrgCourseUploadFile()
    {
        return $this->orgCourseUploadFile;
    }

    /**
     */
    public function setSystemMessage($systemMessage)
    {
        $this->systemMessage = $systemMessage;
    }

    /**
     */
    public function getSystemMessage()
    {
    return $this->systemMessage;
    }
    
    /**
     *
     * @param array $faculties
     */
    public function setFaculties($faculties)
    {
        $this->faculties = $faculties;
    }
    
    /**
     * 
     * @param array $faculties
     */
    public function getFaculties($faculties)
    {
        $this->faculties = $faculties;
    }
    
    /**
     *
     * @param string $userName
     */
    public function setUserName($userName)
    {
    	$this->userName = $userName;
    }

    /**
     *
     * @return string
     */
    public function getUserName()
    {
    	return $this->userName;
    }


    /**
     * @return string
     */
    public function getNotificationBodyText()
    {
        return $this->notificationBodyText;
    }

    /**
     * @param string $notificationBodyText
     */
    public function setNotificationBodyText($notificationBodyText)
    {
        $this->notificationBodyText = $notificationBodyText;
    }

    /**
     * @return string
     */
    public function getNotificationHoverText()
    {
        return $this->notificationHoverText;
    }

    /**
     * @param string $notificationHoverText
     */
    public function setNotificationHoverText($notificationHoverText)
    {
        $this->notificationHoverText = $notificationHoverText;
    }
}
