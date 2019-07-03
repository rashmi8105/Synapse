<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class FeaturePermissionResponseDto
{

    /**
     * Id
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * Question label
     *
     * @JMS\Type("string")
     */
    private $name;

    /**
     *
     * @var Object @JMS\Type("Synapse\RestBundle\Entity\PermissionValueDto")
     *     
     *     
     */
    private $publicAccess;

    /**
     *
     * @var Object @JMS\Type("Synapse\RestBundle\Entity\PermissionValueDto")
     *     
     *     
     */
    private $privateAccess;

    /**
     *
     * @var Object @JMS\Type("Synapse\RestBundle\Entity\PermissionValueDto")
     *     
     *     
     */
    private $teamsAccess;

    /**
     * receiveReferrals
     *
     * @JMS\Type("boolean")
     */
    private $receiveReferrals;

    /**
     *
     * @param mixed $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param mixed $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param Object $privateAccess            
     */
    public function setPrivateAccess($privateAccess)
    {
        $this->privateAccess = $privateAccess;
    }

    /**
     *
     * @return Object
     */
    public function getPrivateAccess()
    {
        return $this->privateAccess;
    }

    /**
     *
     * @param Object $publicAccess            
     */
    public function setPublicAccess($publicAccess)
    {
        $this->publicAccess = $publicAccess;
    }

    /**
     *
     * @return Object
     */
    public function getPublicAccess()
    {
        return $this->publicAccess;
    }

    /**
     *
     * @param mixed $receiveReferrals            
     */
    public function setReceiveReferrals($receiveReferrals)
    {
        $this->receiveReferrals = $receiveReferrals;
    }

    /**
     *
     * @return mixed
     */
    public function getReceiveReferrals()
    {
        return $this->receiveReferrals;
    }

    /**
     *
     * @param Object $teamsAccess            
     */
    public function setTeamsAccess($teamsAccess)
    {
        $this->teamsAccess = $teamsAccess;
    }

    /**
     *
     * @return Object
     */
    public function getTeamsAccess()
    {
        return $this->teamsAccess;
    }
}