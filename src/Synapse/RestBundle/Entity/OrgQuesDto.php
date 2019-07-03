<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OrgQuesDto
{

    /**
     * Object representing the details of an isq.
     *
     * @var object
     * @JMS\Type("array<Synapse\RestBundle\Entity\OrgQuesDetailsDto>")
     */
    private $orgQuestion;

    public function setOrgQuestion($orgQuestion)
    {
        $this->orgQuestion = $orgQuestion;
    }

    public function getOrgQuestion()
    {
        
        return $this->orgQuestion;
    }
    
}
