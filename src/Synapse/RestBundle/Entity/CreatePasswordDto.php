<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePasswordDto
{

    /**
     * Access token created with a password.
     * 
     * @var string @JMS\Type("string")
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * User's password.
     * 
     * @var string @JMS\Type("string")
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * Id of the client.
     * 
     * @var string @JMS\Type("string")
     * @Assert\NotBlank()
     */
    private $clientId;

    /**
     * If true, then the user has accepted the confidentiality statement.
     * 
     * @var boolean @JMS\Type("boolean")
     * @Assert\NotBlank()
     */
    private $is_confidentiality_accepted;

    /**
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     *
     * @return boolean
     */
    public function getIsConfidentialityAccepted()
    {
        return $this->is_confidentiality_accepted;
    }

    /**
     *
     * @param boolean $is_confidentiality_accepted
     */
    public function setIsConfidentialityAccepted($is_confidentiality_accepted)
    {
        $this->is_confidentiality_accepted = $is_confidentiality_accepted;
    }

    /**
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     *
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }
}