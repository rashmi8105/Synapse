<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class FeatureBlockDto implements DtoInterface
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
    private $publicShare;

    /**
     *
     * @var Object @JMS\Type("Synapse\RestBundle\Entity\PermissionValueDto")
     *     
     *     
     */
    private $privateShare;

    /**
     *
     * @var Object @JMS\Type("Synapse\RestBundle\Entity\PermissionValueDto")
     *     
     *     
     */
    private $teamsShare;

    /**
     * receiveReferrals
     *
     * @JMS\Type("boolean")
     */
    private $receiveReferrals;

    /**
     * lastUpdated
     *
     * @JMS\Type("DateTime")
     */
    private $lastUpdated;
    
 
    /**
     *
     * @var Object @JMS\Type("Synapse\RestBundle\Entity\FeatureBlockDto")
     *
     *
     */
    private $directReferral;
    
    
    /**
     *
     * @var Object @JMS\Type("Synapse\RestBundle\Entity\FeatureBlockDto")
     *
     *
     */
    private $reasonRoutedReferral;
    
    /**
     *
     * @param mixed $directReferral
     */
    public function setDirectReferral($directReferral)
    {
        $this->directReferral = $directReferral;
    }
    
    /**
     * @return mixed
     */
    public function getDirectReferral()
    {
        return $this->directReferral;
    }
    
    /**
     *
     * @param mixed $reasonRoutedReferral
     */
    public function setReasonRoutedReferral($reasonRoutedReferral)
    {
        $this->reasonRoutedReferral = $reasonRoutedReferral;
    }
    
    /**
     * @return mixed
     */
    public function getReasonRoutedReferral()
    {
        return $this->reasonRoutedReferral;
    }
    
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
     * @param Object $privateShare            
     */
    public function setPrivateShare($privateShare)
    {
        $this->privateShare = $privateShare;
    }

    /**
     *
     * @return Object
     */
    public function getPrivateShare()
    {
        return $this->privateShare;
    }

    /**
     *
     * @param Object $publicShare            
     */
    public function setPublicShare($publicShare)
    {
        $this->publicShare = $publicShare;
    }

    /**
     *
     * @return Object
     */
    public function getPublicShare()
    {
        return $this->publicShare;
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
     * @param mixed $lastUpdated            
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     *
     * @return mixed
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     *
     * @param Object $teamsShare            
     */
    public function setTeamsShare($teamsShare)
    {
        $this->teamsShare = $teamsShare;
    }

    /**
     *
     * @return Object
     */
    public function getTeamsShare()
    {
        return $this->teamsShare;
    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {      
        $this->id = isset($attributes['id']) ? (int)$attributes['id'] : null;
        $this->name = isset($attributes['name']) ? $attributes['name'] : null;
        $this->publicShare = isset($attributes['publicShare']) ? $attributes['publicShare'] : null;
        $this->privateShare = isset($attributes['privateShare']) ? $attributes['privateShare'] : null;
        $this->teamsShare = isset($attributes['teamsShare']) ? $attributes['teamsShare'] : null;
        $this->receiveReferrals = isset($attributes['receiveReferrals']) ? $attributes['receiveReferrals'] : null;
        $this->lastUpdated = (!empty($attributes['lastUpdated'])) ? new \DateTime($attributes['lastUpdated']) : null;
         
    }
}
