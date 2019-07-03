<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class ReferralListResponseDto
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
     * @var [type] @JMS\Type("string")
     */
    private $studentFirstName;

    /**
     * [$studentlastName]
     * 
     * @var [type] @JMS\Type("string")
     */
    private $studentLastName;

    /**
     * [$studentlastName]
     * 
     * @var [type] @JMS\Type("string")
     */
    private $riskLevel;

    /**
     * [$riskModelId]
     * 
     * @var [type] @JMS\Type("integer")
     */
    private $riskModelId;

    /**
     * [$studentIntentToLeave]
     * 
     * @var [type] @JMS\Type("boolean")
     */
    private $studentIntentToLeave;

    /**
     * [$imageName]
     * 
     * @var [type] @JMS\Type("string")
     */
    private $imageName;

    /**
     * [$riskText]
     * 
     * @var [type] @JMS\Type("string")
     */
    private $riskText;

    /**
     * [$studentLogins]
     * 
     * @var [type] @JMS\Type("integer")
     */
    private $studentLogins;

    /**
     * [$studentCohorts]
     * 
     * @var [type] @JMS\Type("string")
     */
    private $studentCohorts;

    /**
     * [$lastActivity]
     * 
     * @var [type] @JMS\Type("string")
     */
    private $lastActivity;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;
    

    /**
     * studentClasslevel
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentClasslevel;
    
    /**
     *
     * @param mixed $imageName            
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     *
     * @return mixed
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     *
     * @param mixed $lastActivity            
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

    /**
     *
     * @return mixed
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     *
     * @param mixed $referralId            
     */
    public function setReferralId($referralId)
    {
        $this->referralId = $referralId;
    }

    /**
     *
     * @return mixed
     */
    public function getReferralId()
    {
        return $this->referralId;
    }

    /**
     *
     * @param mixed $riskLevel            
     */
    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    /**
     *
     * @return mixed
     */
    public function getRiskLevel()
    {
        return $this->riskLevel;
    }

    /**
     *
     * @param mixed $riskModelId            
     */
    public function setRiskModelId($riskModelId)
    {
        $this->riskModelId = $riskModelId;
    }

    /**
     *
     * @return mixed
     */
    public function getRiskModelId()
    {
        return $this->riskModelId;
    }

    /**
     *
     * @param mixed $riskText            
     */
    public function setRiskText($riskText)
    {
        $this->riskText = $riskText;
    }

    /**
     *
     * @return mixed
     */
    public function getRiskText()
    {
        return $this->riskText;
    }

    /**
     *
     * @param mixed $studentCohorts            
     */
    public function setStudentCohorts($studentCohorts)
    {
        $this->studentCohorts = $studentCohorts;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentCohorts()
    {
        return $this->studentCohorts;
    }

    /**
     *
     * @param mixed $studentFirstName            
     */
    public function setStudentFirstName($studentFirstName)
    {
        $this->studentFirstName = $studentFirstName;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentFirstName()
    {
        return $this->studentFirstName;
    }

    /**
     *
     * @param mixed $studentId            
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     *
     * @param mixed $studentIntentToLeave            
     */
    public function setStudentIntentToLeave($studentIntentToLeave)
    {
        $this->studentIntentToLeave = $studentIntentToLeave;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentIntentToLeave()
    {
        return $this->studentIntentToLeave;
    }

    /**
     *
     * @param mixed $studentLogins            
     */
    public function setStudentLogins($studentLogins)
    {
        $this->studentLogins = $studentLogins;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentLogins()
    {
        return $this->studentLogins;
    }

    /**
     *
     * @param mixed $studentlastName            
     */
    public function setStudentLastName($studentLastName)
    {
        $this->studentLastName = $studentLastName;
    }

    /**
     *
     * @return mixed
     */
    public function getStudentLastName()
    {
        return $this->studentLastName;
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
     * @param string $studentClasslevel            
     */
    public function setStudentClasslevel($studentClasslevel)
    {
        $this->studentClasslevel = $studentClasslevel;
    }

    /**
     *
     * @return string
     */
    public function getStudentClasslevel()
    {
        return $this->studentClasslevel;
    }
}