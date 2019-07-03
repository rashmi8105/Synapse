<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ShareOptionsBlockDto implements DtoInterface
{


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
        $this->publicShare = isset($attributes['publicShare']) ? $attributes['publicShare'] : null;
        $this->privateShare = isset($attributes['privateShare']) ? $attributes['privateShare'] : null;
        $this->teamsShare = isset($attributes['teamsShare']) ? $attributes['teamsShare'] : null;     
    }
}
