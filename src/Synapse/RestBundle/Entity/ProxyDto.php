<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Proxy
 *
 * @package Synapse\RestBundle\Entity
 */
class ProxyDto
{
	/**
     * Id of a proxy.
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * If of a campus.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $campusId;

    /**
     * Id of the user that is being proxied.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $userId;

    /**
     * Id of the user that is proxying as another user.
     * 
     * @var integer @JMS\Type("integer")
     */
    private $proxyUserId;
	
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
     * @param int $campusId            
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    /**
     *
     * @return int
     */
    public function getCampusId()
    {
        return $this->campusId;
    }
	
	/**
     *
     * @param int $userId            
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
	
	/**
     *
     * @param int $proxyUserId            
     */
    public function setProxyUserId($proxyUserId)
    {
        $this->proxyUserId = $proxyUserId;
    }

    /**
     *
     * @return int
     */
    public function getProxyUserId()
    {
        return $this->proxyUserId;
    }	
}