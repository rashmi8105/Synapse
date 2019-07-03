<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentDetailsDto
{

    /**
     * studentId
     *
     * @var integer @JMS\Type("integer")
     */
    private $studentId;

    /**
     * firstName
     *
     * @var string @JMS\Type("string")
     */
    private $firstName;

    /**
     * lastName
     *
     * @var string @JMS\Type("string")
     */
    private $lastName;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * id
     *
     * @var string @JMS\Type("string")
     */
    private $id;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $studentStatus;
    
    /**
     * $academicUpdates
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $academicUpdates;

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
     * @param string $firstName            
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     *
     * @param string $lastName            
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     *
     * @param string $email            
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @param string $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * @param boolean $academicUpdates
     */
    public function setAcademicUpdates($academicUpdates)
    {
        $this->academicUpdates = $academicUpdates;
    }

    /**
     * @return boolean
     */
    public function getAcademicUpdates()
    {
        return $this->academicUpdates;
    }
}