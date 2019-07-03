<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
/**
 * Data Transfer Object for Survey Questions
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveyQuestionsResponseDto
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
     * cohortId
     *
     * @var integer @JMS\Type("integer")
     *
     */
    private $cohortId;
    
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
    private $surveyQuestions;

    /**
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param int $surveyId
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }
    
    /**
     * @param int $cohortId
     */
    public function setCohortId($cohortId)
    {
        $this->cohortId = $cohortId;
    }
    
    /**
     * @return int
     */
    public function getCohortId()
    {
        return $this->cohortId;
    }

    /**
     * @param string $surveyName
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = $surveyName;
    }

    /**
     * @return string
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     * @param Object $surveyQuestions
     */
    public function setSurveyQuestions($surveyQuestions)
    {
        $this->surveyQuestions = $surveyQuestions;
    }

    /**
     * @return Object
     */
    public function getSurveyQuestions()
    {
        return $this->surveyQuestions;
    }
    
    /**
     * @param int $langId
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }
    
    /**
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }
}