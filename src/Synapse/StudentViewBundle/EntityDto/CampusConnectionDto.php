<?php
namespace Synapse\StudentViewBundle\EntityDto;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Campus Connection
 *
 * @package Synapse\StudentViewBundle\EntityDto
 */
class CampusConnectionDto
{

    /**
     * personId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $personId;

    /**
     * personFirstname
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $personFirstname;

    /**
     * personLastname
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $personLastname;

    /**
     * personTitle
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $personTitle;

    /**
     *
     * @param int $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param string $personFirstname            
     */
    public function setPersonFirstname($personFirstname)
    {
        $this->personFirstname = $personFirstname;
    }

    /**
     *
     * @return string
     */
    public function getPersonFirstname()
    {
        return $this->personFirstname;
    }

    /**
     *
     * @param string $personLastname            
     */
    public function setPersonLastname($personLastname)
    {
        $this->personLastname = $personLastname;
    }

    /**
     *
     * @return string
     */
    public function getPersonLastname()
    {
        return $this->personLastname;
    }
    
    /**
     *
     * @param string $personTitle
     */
    public function setPersonTitle($personTitle)
    {
        $this->personTitle = $personTitle;
    }
    
    /**
     *
     * @return string
     */
    public function getPersonTitle()
    {
        return $this->personTitle;
    }
}