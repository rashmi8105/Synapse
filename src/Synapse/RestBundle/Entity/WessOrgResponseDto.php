<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class WessOrgResponseDto
{

    /**
     *
     * @var object @JMS\Type("array<Synapse\RestBundle\Entity\WessOrgRespDto>")
     *     
     */
    private $orgResponse;

    public function setOrgResponse($orgResponse)
    {
        $this->orgResponse = $orgResponse;
    }

    /**
     *
     * @return mixed
     */
    public function getOrgResponse()
    {
        return $this->orgResponse;
    }
}
