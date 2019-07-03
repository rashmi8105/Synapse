<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class StudentListArrayResponseDto
{

    /**
     * activity_id
     *
     * @var integer @JMS\Type("integer")
     */
    private $activityId;

     /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityDate;

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
    private $activityCreatedById;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityCreatedByFirstName;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityCreatedByLastName;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $activityReasonId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityReasonText;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityDescription;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityReferralStatus;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $activityContactTypeId;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityContactTypeText;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $activityLogId;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\StudentListArrayResponseDto>")
     *     
     *     
     */
    private $relatedActivities;

    /**
     * @JMS\Type("integer")
     *
     * @var integer
     */
    private $relatedActivityId;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityEmailSubject;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $activityEmailBody;
    
    /**
     *
     * @param string $activityId            
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
    }

    /**
     *
     * @return string
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     *
     * @param mixed $activityDate            
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
     * @param int $activityType            
     */
    public function setActivityCreatedById($activityCreatedById)
    {
        $this->activityCreatedById = $activityCreatedById;
    }

    /**
     *
     * @return int
     */
    public function getActivityCreatedById()
    {
        return $this->activityCreatedById;
    }

    /**
     *
     * @param string $activityCreatedByFirstName            
     */
    public function setActivityCreatedByFirstName($activityCreatedByFirstName)
    {
        $this->activityCreatedByFirstName = $activityCreatedByFirstName;
    }

    /**
     *
     * @return string
     */
    public function getActivityCreatedByFirstName()
    {
        return $this->activityCreatedByFirstName;
    }

    /**
     *
     * @param string $activityCreatedByLastName            
     */
    public function setActivityCreatedByLastName($activityCreatedByLastName)
    {
        $this->activityCreatedByLastName = $activityCreatedByLastName;
    }

    /**
     *
     * @return string
     */
    public function getActivityCreatedByLastName()
    {
        return $this->activityCreatedByLastName;
    }

    /**
     *
     * @param int $activityReasonId            
     */
    public function setActivityReasonId($activityReasonId)
    {
        $this->activityReasonId = $activityReasonId;
    }

    /**
     *
     * @return int
     */
    public function getActivityReasonId()
    {
        return $this->activityReasonId;
    }

    /**
     *
     * @param string $activityReasonId            
     */
    public function setActivityReasonText($activityReasonText)
    {
        $this->activityReasonText = $activityReasonText;
    }

    /**
     *
     * @return string
     */
    public function getActivityReasonText()
    {
        return $this->activityReasonText;
    }

    /**
     *
     * @param string $activityDescription            
     */
    public function setActivityDescription($activityDescription)
    {
        $this->activityDescription = $activityDescription;
    }

    /**
     *
     * @return string
     */
    public function getActivityDescription()
    {
        return $this->activityDescription;
    }

    /**
     *
     * @param string $activityReferralStatus            
     */
    public function setActivityReferralStatus($activityReferralStatus)
    {
        $this->activityReferralStatus = $activityReferralStatus;
    }

    /**
     *
     * @return string
     */
    public function getActivityReferralStatus()
    {
        return $this->activityReferralStatus;
    }

    /**
     *
     * @param integer $activityContactTypeId            
     */
    public function setActivityContactTypeId($activityContactTypeId)
    {
        $this->activityContactTypeId = $activityContactTypeId;
    }

    /**
     *
     * @return integer
     */
    public function getActivityContactTypeId()
    {
        return $this->activityContactTypeId;
    }

    /**
     *
     * @param string $activityContactTypeText            
     */
    public function setActivityContactTypeText($activityContactTypeText)
    {
        $this->activityContactTypeText = $activityContactTypeText;
    }

    /**
     *
     * @return string
     */
    public function getActivityContactTypeText()
    {
        return $this->activityContactTypeText;
    }

    /**
     *
     * @param string $activityLogId            
     */
    public function setActivityLogId($activityLogId)
    {
        $this->activityLogId = $activityLogId;
    }

    /**
     *
     * @return integer
     */
    public function getActivityLogId()
    {
        return $this->activityLogId;
    }

    /**
     *
     * @param Object $relatedActivities            
     */
    public function setRelatedActivities($relatedActivities)
    {
        $this->relatedActivities = $relatedActivities;
    }

    /**
     *
     * @return Object
     */
    public function getRelatedActivities()
    {
        return $this->relatedActivities;
    }

    /**
     *
     * @param string $relatedActivityId            
     */
    public function setRelatedActivityId($relatedActivityId)
    {
        $this->relatedActivityId = $relatedActivityId;
    }

    /**
     *
     * @return string
     */
    public function getRelatedActivityId()
    {
        return $this->relatedActivityId;
    }

    /**
     * @param string $activityEmailBody
     */
    public function setActivityEmailBody($activityEmailBody)
    {
        $this->activityEmailBody = $activityEmailBody;
    }

    /**
     * @return string
     */
    public function getActivityEmailBody()
    {
        return $this->activityEmailBody;
    }

    /**
     * @param string $activityEmailSubject
     */
    public function setActivityEmailSubject($activityEmailSubject)
    {
        $this->activityEmailSubject = $activityEmailSubject;
    }

    /**
     * @return string
     */
    public function getActivityEmailSubject()
    {
        return $this->activityEmailSubject;
    }

}