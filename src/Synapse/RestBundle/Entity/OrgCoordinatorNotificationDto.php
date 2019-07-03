<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OrgCoordinatorNotificationDto
{

     /**
     * Organization id
     *
     * @var integer @JMS\Type("integer")
     */
    private $organizationId;

    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    public function getOrganizationId()
    {
        
        return $this->organizationId;
    }
    
}
