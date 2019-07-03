<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 * 
 * @package Synapse\SearchBundle\EntityDto
 */
class SurveyStatusReportDto
{

    /**
     * $studentId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $studentId;
    
    
    /**
     * $firstName
     *
     * @var string @JMS\Type("string")
     *
     */
    private $firstName;  
      
    
   
    /**
     * $lastName
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $lastName;

    /**
     * $email
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $email;

    /**
     * $phoneNumber
     * 
     * @var string @JMS\Type("string")
     */
    private $phoneNumber;

    /**
     * $optedOut
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $optedOut;

    /**
     * $responded
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $responded;

    /**
     * $respondedAt
     *
     * @var datetime @JMS\Type("string")
     *     
     */
    private $respondedAt;

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $optedOut
     */
    public function setOptedOut($optedOut)
    {
        $this->optedOut = $optedOut;
    }

    /**
     * @return string
     */
    public function getOptedOut()
    {
        return $this->optedOut;
    }

    /**
     * @param int $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $responded
     */
    public function setResponded($responded)
    {
        $this->responded = $responded;
    }

    /**
     * @return string
     */
    public function getResponded()
    {
        return $this->responded;
    }

    /**
     * @param \Synapse\SearchBundle\EntityDto\datetime $respondedAt
     */
    public function setRespondedAt($respondedAt)
    {
        $this->respondedAt = $respondedAt;
    }

    /**
     * @return \Synapse\SearchBundle\EntityDto\datetime
     */
    public function getRespondedAt()
    {
        return $this->respondedAt;
    }

    
    
}