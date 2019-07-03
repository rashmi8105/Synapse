<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for AuthCodeDto
 *
 * @package Synapse\RestBundle\Entity
 */
class AuthCodeDto
{
    /**
     * Person id for the service Account
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $personId;

    /**
     * Organization id for which the service account Auth code is being generated
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * Client Id for the service account to be used along with client secret and auth code to generate access token
     *
     * @var string
     * @JMS\Type("string")
     */
    private $clientId;

    /**
     * Client Secret for the service account used along with client id and auth code to generate access token
     *
     * @var string
     * @JMS\Type("string")
     */
    private $clientSecret;

    /**
     * Authorization Code for the service account used along with client id and client token to generate access token
     *
     * @var string
     * @JMS\Type("string")
     */
    private $authCode;

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     */
    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
    }
}