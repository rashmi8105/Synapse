<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 *
 *
 * @package Synapse\RestBundle\Entity
 */
class TotalStudentsListDto
{

    /**
     * Unique id of a student.
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentId;
    
    /**
     * Id for a person determined by an organization.
     *
     * @var string @JMS\Type("string")
     */
    private $externalId;

    /**
     * First name of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentFirstName;

    /**
     * Last name of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentLastName;

    /**
     * Id of the person that referred the student.
     *
     * @var integer @JMS\Type("integer")
     */
    private $referredById;

    /**
     * First name of the person that referred the student.
     *
     * @var string @JMS\Type("string")
     */
    private $referredByFirstName;

    /**
     * Last name of the person that referred the student.
     *
     * @var string @JMS\Type("string")
     */
    private $referredByLastName;

    /**
     * Risk status of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentRiskStatus;

    /**
     * Risk level of a student.
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentRiskLevel;

    /**
     * The integer equivalent of a student's intent to leave.
     *
     * @var integer @JMS\Type("string")
     */
    private $studentIntentToLeave;

    /**
     * The cohorts that are assigned to a student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentCohorts;

    /**
     * Class level of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentClasslevel;

    /**
     * Number of times a student has logged-in.
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentLogins;

    /**
     * Last activity of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $lastActivity;

    /**
     * Current Status of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentStatus;

    /**
     * Name of the image that represents a student's intent to leave.
     *
     * @var string @JMS\Type("string")
     */
    private $studentIntentToLeaveImageName;

    /**
     * Name of the image that represents a students risk level.
     *
     * @var string @JMS\Type("string")
     */
    private $studentRiskImageName;
    
    /**
     * Primary email of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $primaryEmail;
    
    /**
     * Risk color of a student.
     *
     * @var string @JMS\Type("string")
     */
    private $studentRiskColor;
    
    /**
     * Color of a student's intent to leave.
     *
     * @var string @JMS\Type("string")
     */
    private $studentIntentColor;
    
    /**
     * Boolean That determines whether a student has a primary connection or not.
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $hasPrimaryConnection;

    /**
     * Text describing a student's intent to leave.
     *
     * @var string @JMS\Type("string")
     */
    private $studentIntentToLeaveText;

    /**
     * Sets the id of a student.
     *
     * @param integer $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * Gets the id of a student.
     *
     * @return integer
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Sets the external id of a student.
     *
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * Gets the external id of a student.
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Sets the first name of a student.
     *
     * @param string $studentFirstName
     */
    public function setStudentFirstName($studentFirstName)
    {
        $this->studentFirstName = $studentFirstName;
    }

    /**
     * Gets the first name of a student.
     *
     * @return string
     */
    public function getStudentFirstName()
    {
        return $this->studentFirstName;
    }

    /**
     * Sets the last name of a student.
     *
     * @param string $studentLastName
     */
    public function setStudentLastName($studentLastName)
    {
        $this->studentLastName = $studentLastName;
    }

    /**
     * Gets the last name of a student.
     *
     * @return string
     */
    public function getStudentLastName()
    {
        return $this->studentLastName;
    }

    /**
     * Sets the id of the person that refers a student.
     *
     * @param integer $referredById            
     */
    public function setReferredById($referredById)
    {
        $this->referredById = $referredById;
    }

    /**
     * Gets the id of the person that refers a student.
     *
     * @return integer
     */
    public function getReferredById()
    {
        return $this->referredById;
    }

    /**
     * Sets the first name of the person that refers a student.
     *
     * @param string $referredByFirstName
     */
    public function setReferredByFirstName($referredByFirstName)
    {
        $this->referredByFirstName = $referredByFirstName;
    }

    /**
     * Gets the first name of the person that refers a student.
     *
     * @return integer
     */
    public function getReferredByFirstName()
    {
        return $this->referredByFirstName;
    }

    /**
     * Sets the last name of the person that refers a student.
     *
     * @param string $referredByLastName
     */
    public function setReferredByLastName($referredByLastName)
    {
        $this->referredByLastName = $referredByLastName;
    }

    /**
     * Gets the last name of the person that refers a student.
     *
     * @return integer
     */
    public function getReferredByLastName()
    {
        return $this->referredByLastName;
    }

    /**
     * Sets the risk status of a student.
     *
     * @param string $studentRiskStatus
     */
    public function setStudentRiskStatus($studentRiskStatus)
    {
        $this->studentRiskStatus = $studentRiskStatus;
    }

    /**
     * Gets the risk status of a student.
     *
     * @return integer
     */
    public function getStudentRiskStatus()
    {
        return $this->studentRiskStatus;
    }

    /**
     * Sets the risk level of a student.
     *
     * @param integer $studentRiskLevel
     */
    public function setStudentRiskLevel($studentRiskLevel)
    {
        $this->studentRiskLevel = $studentRiskLevel;
    }

    /**
     * Gets the risk level of a student.
     *
     * @return integer
     */
    public function getStudentRiskLevel()
    {
        return $this->studentRiskLevel;
    }

    /**
     * Sets a students intent to leave.
     *
     * @param string $studentIntentToLeave
     */
    public function setStudentIntentToLeave($studentIntentToLeave)
    {
        $this->studentIntentToLeave = $studentIntentToLeave;
    }

    /**
     * Gets a students intent to leave.
     *
     * @return integer
     */
    public function getStudentIntentToLeave()
    {
        return $this->studentIntentToLeave;
    }

    /**
     * Sets the last activity of a person.
     *
     * @param string $lastActivity            
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

    /**
     * Gets the last activity of a person.
     *
     * @return string
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Sets the cohorts assigned to a student.
     *
     * @param string $studentCohorts            
     */
    public function setStudentCohorts($studentCohorts)
    {
         $this->studentCohorts = $studentCohorts;
    }

    /**
     * Gets the cohorts assigned to a student.
     *
     * @return string
     */
    public function getStudentCohorts()
    {
        return $this->studentCohorts;
    }


    /**
     * Sets the class level of a student.
     *
     * @param string $studentClasslevel            
     */
    public function setStudentClasslevel($studentClasslevel)
    {
        $this->studentClasslevel = $studentClasslevel;
    }

    /**
     * Gets the class level of a student.
     *
     * @return string
     */
    public function getStudentClasslevel()
    {
        return $this->studentClasslevel;
    }

    /**
     * Sets the number of times that a student has logged-in.
     *
     * @param integer $studentLogins
     */
    public function setStudentLogins($studentLogins)
    {
        $this->studentLogins = $studentLogins;
    }

    /**
     * Gets the number of times that a student has logged-in.
     *
     * @return integer
     */
    public function getStudentLogins()
    {
        return $this->studentLogins;
    }

    /**
     * Sets the status of a student.
     *
     * @param string $studentStatus            
     */
    public function setStudentStatus($studentStatus)
    {
        $this->studentStatus = $studentStatus;
    }

    /**
     * Gets the status of a student.
     *
     * @return string
     */
    public function getStudentStatus()
    {
        return $this->studentStatus;
    }

    /**
     * Sets a student's risk image name.
     *
     * @param string $studentRiskImageName
     */
    public function setStudentRiskImageName($studentRiskImageName)
    {
        $this->studentRiskImageName = $studentRiskImageName;
    }

    /**
     * Gets a student's risk image name.
     *
     * @return integer
     */
    public function getStudentRiskImageName()
    {
        return $this->studentRiskImageName;
    }

    /**
     * Sets a student's intent to leave image name.
     *
     * @param string $studentIntentToLeaveImageName
     */
    public function setStudentIntentToLeaveImageName($studentIntentToLeaveImageName)
    {
        $this->studentIntentToLeaveImageName = $studentIntentToLeaveImageName;
    }

    /**
     * Gets a student's intent to leave image name.
     *
     * @return integer
     */
    public function getStudentIntentToLeaveImageName()
    {
        return $this->studentIntentToLeaveImageName;
    }

    /**
     * Sets the text for a student's intent to leave.
     *
     * @param string $studentIntentToLeaveText
     */
    public function setStudentIntentToLeaveText($studentIntentToLeaveText)
    {
        $this->studentIntentToLeaveText = $studentIntentToLeaveText;
    }

    /**
     * Gets the text for a student's intent to leave.
     *
     * @return integer
     */
    public function getStudentIntentToLeaveText()
    {
        return $this->studentIntentToLeaveText;
    }

    /**
     * Sets the primary email of a student.
     *
     * @param string $primaryEmail
     */
    public function setPrimaryEmail($primaryEmail)
    {
    	$this->primaryEmail = $primaryEmail;
    }

    /**
     * Gets the primary email of a student.
     *
     * @return integer
     */
    public function getPrimaryEmail()
    {
    	return $this->primaryEmail;
    }

    /**
     * Sets the risk color of a student's risk.
     *
     * @param string $studentRiskColor
     */
    public function setStudentRiskColor($studentRiskColor)
    {
    	$this->studentRiskColor = $studentRiskColor;
    }
    
    /**
     * Gets the risk color of a student's risk.
     *
     * @return string
     */
    public function getStudentRiskColor()
    {
    	return $this->studentRiskColor;
    }
    
    /**
     * Sets the color of a student's intent level.
     *
     * @param string $studentIntentColor
     */
    public function setStudentIntentColor($studentIntentColor)
    {
    	$this->studentIntentColor = $studentIntentColor;
    }
    
    /**
     * Gets the color of a student's intent level.
     *
     * @return string
     */
    public function getStudentIntentColor()
    {
    	return $this->studentIntentColor;
    }
    
    /**
     * Sets whether a student has a primary campus connection or not.
     *
     * @param boolean $hasPrimaryConnection
     */
    public function setHasPrimaryConnection($hasPrimaryConnection)
    {
        $this->hasPrimaryConnection = $hasPrimaryConnection;
    }
    
    /**
     * Gets whether a student has a primary campus connection or not.
     *
     * @return boolean
     */
    public function getHasPrimaryConnection()
    {
        return $this->hasPrimaryConnection;
    }
}