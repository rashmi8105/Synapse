<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Request From
 *
 * @package Synapse\RestBundle\Entity
 */
class RequestFromResponseDto
{
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $firstname;
    
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $lastname;
    
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
}