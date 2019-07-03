<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student Isq Questions
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class StudentIsqQuesResponseDto
{

    /**
     * surveyId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $surveyId;

    /**
     * surveyName
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $surveyName;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\IsqDataArrayDto>")
     *     
     */
    private $isqData;

    /**
     *
     * @param int $surveyId            
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     *
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     *
     * @param string $surveyName            
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = $surveyName;
    }

    /**
     *
     * @return string
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     *
     * @param Object $isqData            
     */
    public function setIsqData($isqData)
    {
        $this->isqData = $isqData;
    }

    /**
     *
     * @return Object
     */
    public function getIsqData()
    {
        return $this->isqData;
    }
}