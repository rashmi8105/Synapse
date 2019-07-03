<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Survey Comparision Response Array
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class StudentSurveyResArrayDto
{

    /**
     * responseText
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $responseText;

    /**
     * responseDecimal
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $responseDecimal;

    /**
     * surveyId
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $surveyId;

    /**
     * surveyDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $surveyDate;

    /**
     *
     * @param string $responseText            
     */
    public function setResponseText($responseText)
    {
        $this->responseText = $responseText;
    }

    /**
     *
     * @return string
     */
    public function getResponseText()
    {
        return $this->responseText;
    }

    /**
     *
     * @param string $responseDecimal
     */
    public function setResponseDecimal($responseDecimal)
    {
        $this->responseDecimal = $responseDecimal;
    }

    /**
     *
     * @return string
     */
    public function getResponseDecimal()
    {
        return $this->responseDecimal;
    }

    /**
     *
     * @param string $surveyId            
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    /**
     *
     * @return string
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     *
     * @param mixed $surveyDate            
     */
    public function setSurveyDate($surveyDate)
    {
        $this->surveyDate = $surveyDate;
    }

    /**
     *
     * @return mixed
     */
    public function getSurveyDate()
    {
        return $this->surveyDate;
    }
}