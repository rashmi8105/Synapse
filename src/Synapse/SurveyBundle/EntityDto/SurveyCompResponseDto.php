<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Survey Comparision Response
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class SurveyCompResponseDto
{
    /**
     * questionText
     *
     * @var string @JMS\Type("string")
     *
     */
    private $questionText;
    
    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\StudentSurveyResArrayDto>")
     *
     */
    private $response;
    
    /**
     *
     * @param string $questionText
     */
    public function setQuestionText($questionText)
    {
    	$this->questionText = $questionText;
    }
    
    /**
     *
     * @return string
     */
    public function getQuestionText()
    {
    	return $this->questionText;
    }
    
    /**
     *
     * @param Object $response
     */
    public function setResponse($response)
    {
    	$this->response = $response;
    }
    
    /**
     *
     * @return Object
     */
    public function getResponse()
    {
    	return $this->response;
    }
    
    
}