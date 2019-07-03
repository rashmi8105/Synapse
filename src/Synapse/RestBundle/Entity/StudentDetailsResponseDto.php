<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class StudentDetailsResponseDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
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
     * @JMS\Type("string")
     *
     * @var string
     */
    private $primaryEmail;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $phoneNumber;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $mobileNumber;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentRiskStatus;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentIntentToLeave;

    /**
     *
     * @var \DateTime @JMS\Type("DateTime")
     *     
     */
    private $riskUpdatedDate;

    /**
     *
     * @var \DateTime @JMS\Type("DateTime")
     *     
     */
    private $lastViewedDate;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $photoUrl;


    /**
     * assessmentBanner
     *
     * @var array @JMS\Type("array")
     */
    private $assessmentBanner;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $authUsername;

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param string $primaryEmail            
     */
    public function setPrimaryEmail($primaryEmail)
    {
        $this->primaryEmail = $primaryEmail;
    }

    /**
     *
     * @return string
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     *
     * @param string $primaryEmail            
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     *
     * @param string $mobileNumber            
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     *
     * @param string $studentRiskStatus            
     */
    public function setStudentRiskStatus($studentRiskStatus)
    {
        $this->studentRiskStatus = $studentRiskStatus;
    }

    /**
     *
     * @return string
     */
    public function getStudentRiskStatus()
    {
        return $this->studentRiskStatus;
    }

    /**
     *
     * @param string $studentRiskStatus            
     */
    public function setStudentIntentToLeave($studentIntentToLeave)
    {
        $this->studentIntentToLeave = $studentIntentToLeave;
    }

    /**
     *
     * @return string
     */
    public function getStudentIntentToLeave()
    {
        return $this->studentIntentToLeave;
    }

    /**
     *
     * @param mixed $riskUpdatedDate            
     */
    public function setRiskUpdatedDate($riskUpdatedDate)
    {
        $this->riskUpdatedDate = $riskUpdatedDate;
    }

    /**
     *
     * @return string
     */
    public function getRiskUpdatedDate()
    {
        return $this->riskUpdatedDate;
    }

    /**
     *
     * @param mixed $lastViewedDate            
     */
    public function setLastViewedDate($lastViewedDate)
    {
        $this->lastViewedDate = $lastViewedDate;
    }

    /**
     *
     * @return mixed
     */
    public function getLastViewedDate()
    {
        return $this->lastViewedDate;
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
     * @param string $photoUrl            
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;
    }

    /**
     *
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }
    
    /**
     *
     * @param array $assessmentBanner
     */
    public function setAssessmentBanner($assessmentBanner)
    {
        $this->assessmentBanner = $assessmentBanner;
    }
    
    /**
     *
     * @return array
     */
    public function getAssessmentBanner()
    {
        return $this->assessmentBanner;
    }


    /**
     * @return string
     */
    public function getAuthUsername()
    {
        return $this->authUsername;
    }

    /**
     * @param string $authUsername
     */
    public function setAuthUsername($authUsername)
    {
        $this->authUsername = $authUsername;
    }
}