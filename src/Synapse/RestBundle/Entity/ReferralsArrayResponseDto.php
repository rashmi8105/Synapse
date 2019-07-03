<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class ReferralsArrayResponseDto
{

    /**
     * [$referralId]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $referralId;

    /**
     * [$studentId]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $studentId;

    /**
     * [$studentFirstName]
     *
     * @var string @JMS\Type("string")
     */
    private $studentFirstName;

    /**
     * [$studentLastName]
     *
     * @var string @JMS\Type("string")
     */
    private $studentLastName;

    /**
     * [$reasonId]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $reasonId;

    /**
     * [$reasonText]
     *
     * @var string @JMS\Type("string")
     */
    private $reasonText;

    /**
     * [$assignedToFirstName]
     *
     * @var string @JMS\Type("string")
     */
    private $assignedToFirstName;

    /**
     * [$assignedToLastName]
     *
     * @var string @JMS\Type("string")
     */
    private $assignedToLastName;

    /**
     *
     * @var datetime @JMS\Type("DateTime")
     */
    private $referralDate;
    
    /**
     * [$createdByFirstName]
     *
     * @var string @JMS\Type("string")
     */
    private $createdByFirstName;

    /**
     * [$assignedToLastName]
     *
     * @var string @JMS\Type("string")
     */
    private $createdByLastName;

    /**
     * [$status status]
     *
     * @var [type] @JMS\Type("string")
     */
    private $status;

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     *
     * @param string $assignedToFirstName            
     */
    public function setAssignedToFirstName($assignedToFirstName)
    {
        $this->assignedToFirstName = $assignedToFirstName;
    }

    /**
     *
     * @return string
     */
    public function getAssignedToFirstName()
    {
        return $this->assignedToFirstName;
    }

    /**
     *
     * @param string $assignedToLastName            
     */
    public function setAssignedToLastName($assignedToLastName)
    {
        $this->assignedToLastName = $assignedToLastName;
    }

    /**
     *
     * @return string
     */
    public function getAssignedToLastName()
    {
        return $this->assignedToLastName;
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
     * @return integer
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
     * @param datetime $referralDate            
     */
    public function setReferralDate($referralDate)
    {
        $this->referralDate = $referralDate;
    }

    /**
     *
     * @return datetime
     */
    public function getReferralDate()
    {
        return $this->referralDate;
    }

    /**
     *
     * @param integer $referralId            
     */
    public function setReferralId($referralId)
    {
        $this->referralId = $referralId;
    }

    /**
     *
     * @return integer
     */
    public function getReferralId()
    {
        return $this->referralId;
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
     * @param integer $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return integer
     */
    public function getStudentId()
    {
        return $this->studentId;
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
     * @return string
     */
    public function getCreatedByFirstName() {
        
        return $this->createdByFirstName;
    }

    /**
     *
     * @return string
     */
    public function getCreatedByLastName() {
        
        return $this->createdByLastName;
    }

    /**
     *
     * @param string $createdByFirstName            
     */
    public function setCreatedByFirstName($createdByFirstName) {
        
        $this->createdByFirstName = $createdByFirstName;
    }

    /**
     *
     * @param string $createdByLastName            
     */
    public function setCreatedByLastName($createdByLastName) {
        
        $this->createdByLastName = $createdByLastName;
    }


}