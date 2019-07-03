<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class WessSurveyResponseDto
{

 
   /**
     *
     * @var object @JMS\Type("array<Synapse\RestBundle\Entity\WessResponseDto>")
     *     
     */
    private $surveyResponse;
    
    
    public function setSurveyResponse($surveyResponse)
    {
        $this->surveyResponse = $surveyResponse;
    }
    
    /**
     *
     * @return mixed
     */
    public function getSurveyResponse()
    {
        return $this->surveyResponse;
    }
}
