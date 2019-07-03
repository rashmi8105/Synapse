<?php
namespace Synapse\CampusConnectionBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class StudentFacultyConnectionsListDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * externalId
     *
     * @var string @JMS\Type("string")
     */
    private $externalId;

    /**
     * firstname
     *
     * @var string @JMS\Type("string")
     */
    private $firstname;

    /**
     * lastname
     *
     * @var string @JMS\Type("string")
     */
    private $lastname;

    /**
     * email
     *
     * @var string @JMS\Type("string")
     */
    private $email;

    /**
     * title
     *
     * @var string @JMS\Type("string")
     */
    private $title;
    
    /**
     * students
     *
     * @var array @JMS\Type("array")
     */
    private $students;

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
     *
     * @param string $firstname            
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

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
     * @param string $lastname            
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     *
     * @param string $title            
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * @return array
     */
    public function getStudents()
    {
    	return $this->students;
    }
}