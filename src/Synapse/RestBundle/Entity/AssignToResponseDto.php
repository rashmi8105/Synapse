<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class AssignToResponseDto
{

    /**
     * id
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $userId;

    /**
     * id
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $firstName;

    /**
     * id
     * 
     * @var string @JMS\Type("string")
     *     
     */
    private $lastName;

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }
}