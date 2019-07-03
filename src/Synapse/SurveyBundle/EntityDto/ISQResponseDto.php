<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for ISQ
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class ISQResponseDto
{

    /**
     * organizationId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $organizationId;

    /**
     * langId
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $langId;
    
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
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\SurveyQuestionsArrayDto>")
     *     
     */
    private $isqs;

    /**
     *
     * @param array $isqs
     */
    public function setIsqs($isqs)
    {
        $this->isqs = $isqs;
    }

    /**
     *
     * @return array
     */
    public function getIsqs()
    {
        return $this->isqs;
    }

    /**
     *
     * @param int $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     *
     * @param int $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }
    
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
}