<?php
namespace Synapse\StudentViewBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentOpenReferralsDto
{

    /**
     * referralId
     *
     * @var integer @JMS\Type("integer")
     */
    private $referralId;

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * campusName
     *
     * @var string @JMS\Type("string")
     */
    private $campusName;

    /**
     * referralDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $referralDate;

    /**
     * createdBy
     *
     * @var string @JMS\Type("string")
     */
    private $createdBy;

    /**
     * createdByRole
     *
     * @var string @JMS\Type("string")
     */
    private $createdByRole;

    /**
     * createdByEmail
     *
     * @var string @JMS\Type("string")
     */
    private $createdByEmail;

    /**
     * assignedTo
     *
     * @var string @JMS\Type("string")
     */
    private $assignedTo;

    /**
     * assignedToRole
     *
     * @var string @JMS\Type("string")
     */
    private $assignedToRole;

    /**
     * assignedToEmail
     *
     * @var string @JMS\Type("string")
     */
    private $assignedToEmail;

    /**
     * description
     *
     * @var string @JMS\Type("string")
     */
    private $description;
    
    /**
     * reason
     *
     * @var string @JMS\Type("string")
     */
    private $reason;

    /**
     *
     * @param string $assignedTo            
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;
    }

    /**
     *
     * @return string
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     *
     * @param string $assignedToEmail            
     */
    public function setAssignedToEmail($assignedToEmail)
    {
        $this->assignedToEmail = $assignedToEmail;
    }

    /**
     *
     * @return string
     */
    public function getAssignedToEmail()
    {
        return $this->assignedToEmail;
    }

    /**
     *
     * @param string $assignedToRole            
     */
    public function setAssignedToRole($assignedToRole)
    {
        $this->assignedToRole = $assignedToRole;
    }

    /**
     *
     * @return string
     */
    public function getAssignedToRole()
    {
        return $this->assignedToRole;
    }

    /**
     *
     * @param string $campusName            
     */
    public function setCampusName($campusName)
    {
        $this->campusName = $campusName;
    }

    /**
     *
     * @return string
     */
    public function getCampusName()
    {
        return $this->campusName;
    }

    /**
     *
     * @param string $createdBy            
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @param string $createdByEmail            
     */
    public function setCreatedByEmail($createdByEmail)
    {
        $this->createdByEmail = $createdByEmail;
    }

    /**
     *
     * @return string
     */
    public function getCreatedByEmail()
    {
        return $this->createdByEmail;
    }

    /**
     *
     * @param string $createdByRole            
     */
    public function setCreatedByRole($createdByRole)
    {
        $this->createdByRole = $createdByRole;
    }

    /**
     *
     * @return string
     */
    public function getCreatedByRole()
    {
        return $this->createdByRole;
    }

    /**
     *
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param \Synapse\StudentViewBundle\EntityDto\datetime $referralDate            
     */
    public function setReferralDate($referralDate)
    {
        $this->referralDate = $referralDate;
    }

    /**
     *
     * @return \Synapse\StudentViewBundle\EntityDto\datetime
     */
    public function getReferralDate()
    {
        return $this->referralDate;
    }

    /**
     *
     * @param int $referralId            
     */
    public function setReferralId($referralId)
    {
        $this->referralId = $referralId;
    }

    /**
     *
     * @return int
     */
    public function getReferralId()
    {
        return $this->referralId;
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
}