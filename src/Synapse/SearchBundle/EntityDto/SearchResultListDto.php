<?php
namespace Synapse\SearchBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\SearchBundle\EntityDto
 */
class SearchResultListDto
{

    /**
     * id
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;
    
    
    /**
     * $referralId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $referralId;  
      
    
   
    /**
     * studentId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $studentId;

    /**
     * studentFirstName
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $studentFirstName;

    /**
     * studentLastName
     * 
     * @var string @JMS\Type("string")
     */
    private $studentLastName;

    /**
     * $riskLevel
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $riskLevel;

    /**
     * $riskModelId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $riskModelId;

    /**
     * imageName
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $imageName;

    /**
     * studentLogins
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $studentLogins;

    /**
     * studentCohorts
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $studentCohorts;

    /**
     * lastActivity
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $lastActivity;
    
    /**
     * lastActivityDate
     *
     * @var string @JMS\Type("string")
     *
     */
    private $lastActivityDate;
    
    
    /**
     * studentRiskText
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentRiskStatus;
    
    /**
     * studentRiskImageName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentRiskImageName;
    
    /**
     * studentIntentToLeaveText
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentIntentToLeave;
    
    /**
     * studentIntentToLeaveImageName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentIntentToLeaveImageName;
    
    /**
     * studentStatus
     *
     * @var string @JMS\Type("string")
     */
    private $studentStatus;

    /**
     * studentIsActive
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $studentIsActive;

    /**
     * studentPrimaryEmail
     *
     * @var string @JMS\Type("string")
     */
    private $studentPrimaryEmail;
    
    /**
     * studentClasslevel
     *
     * @var string @JMS\Type("string")
     *
     */
    private $studentClasslevel;
	
	/**
     * response
     *
     * @var string @JMS\Type("string")
     *
     */
    private $response;
    
    /**
     * externalId
     *
     * @var string @JMS\Type("string")
     *
     */
    private $externalId;

    /**
     *
     * @param integer $studentId            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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

    public function setStudentFirstName($studentFirstName)
    {
        $this->studentFirstName = $studentFirstName;
    }

    public function getStudentFirstName()
    {
        return $this->studentFirstName;
    }

    public function setStudentLastName($studentLastName)
    {
        $this->studentLastName = $studentLastName;
    }

    public function getStudentLastName()
    {
        return $this->studentLastName;
    }

    public function setRiskLevel($riskLevel)
    {
        $this->riskLevel = $riskLevel;
    }

    public function getRiskLevel()
    {
        return $this->riskLevel;
    }

    public function setRiskModelId($riskModelId)
    {
        $this->riskModelId = $riskModelId;
    }

    public function getRiskModelId()
    {
        return $this->riskModelId;
    }

    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function setStudentLogins($studentLogins)
    {
        $this->studentLogins = $studentLogins;
    }

    public function getStudentLogins()
    {
        return $this->studentLogins;
    }

    public function setStudentCohorts($studentCohorts)
    {
        $this->studentCohorts = $studentCohorts;
    }

    public function getStudentCohorts()
    {
        return $this->studentCohorts;
    }

    /**
     *
     * @param integer $lastActivity            
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

    /**
     *
     * @return integer
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }
    
    
    public function setLastActivityDate($lastActivityDate)
    {
        $this->lastActivityDate = $lastActivityDate;
    }
    
    public function getLastActivityDate()
    {
        return $this->lastActivityDate;
    }
    
    
    public function setStudentRiskStatus($studentRiskStatus)
    {
        $this->studentRiskStatus = $studentRiskStatus;
    }
    
    public function getStudentRiskStatus()
    {
        return $this->studentRiskStatus;
    }
    
    public function setStudentRiskImageName($studentRiskImageName)
    {
        $this->studentRiskImageName = $studentRiskImageName;
    }
    
    public function getStudentRiskImageName()
    {
        return $this->studentRiskImageName;
    }
    
    public function setStudentIntentToLeave($studentIntentToLeave)
    {
        $this->studentIntentToLeave = $studentIntentToLeave;
    }
    
    public function getStudentIntentToLeave()
    {
        return $this->studentIntentToLeave;
    }
    
    public function setStudentIntentToLeaveImageName($studentIntentToLeaveImageName)
    {
        $this->studentIntentToLeaveImageName = $studentIntentToLeaveImageName;
    }
    
    public function getStudentIntentToLeaveImageName()
    {
        return $this->studentIntentToLeaveImageName;
    }
    
    public function setStudentStatus($studentStatus)
    {
        $this->studentStatus = $studentStatus;
    }
    
    public function getStudentStatus()
    {
        return $this->studentStatus;
    }

    /**
     *
     * @param string $studentPrimaryEmail
     */
    public function setStudentPrimaryEmail($studentPrimaryEmail)
    {
        $this->studentPrimaryEmail = $studentPrimaryEmail;
    }

    /**
     *
     * @return string
     */
    public function getStudentPrimaryEmail()
    {
        return $this->studentPrimaryEmail;
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
	
	 /**
     *
     * @param string $response
     */
    public function setResponse($response)
    {
    	$this->response = $response;
    }
    
    /**
     *
     * @return string
     */
    public function getResponse()
    {
    	return $this->response;
    }
    
    /**
     *
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
    	$this->externalId = $externalId;
    }
    
    /**
     *
     * @return string
     */
    public function getExternalId()
    {
    	return $this->externalId;
    }

    /**
     * @return boolean
     */
    public function getStudentIsActive()
    {
        return $this->studentIsActive;
    }

    /**
     * @param boolean $studentIsActive
     */
    public function setStudentIsActive($studentIsActive)
    {
        $this->studentIsActive = $studentIsActive;
    }
    
}